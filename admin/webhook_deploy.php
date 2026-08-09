<?php
/**
 * Vortexsoft Innovations — GitHub Auto-Deploy Webhook
 * Called by GitHub Actions on every push to main branch.
 * This script downloads the latest ZIP from GitHub and extracts it to public_html.
 *
 * URL: https://vortexsoftinnovations.com/admin/webhook_deploy.php
 * Security: Protected by X-Deploy-Token header
 */

// ── Security token (must match DEPLOY_TOKEN in GitHub secrets) ──────────────
define('DEPLOY_TOKEN', 'VortexDeploy6498286f401141b8');

// ── GitHub repo details ──────────────────────────────────────────────────────
define('GITHUB_USER',  'Aniketmadhukarraokadam');
define('GITHUB_REPO',  'VORTEXSOFTINNOVATIONSMAIN');
define('GITHUB_BRANCH','main');
define('GITHUB_TOKEN', getenv('GITHUB_API_TOKEN') ?: ''); // Set via Hostinger ENV or .env file

// ── Paths ────────────────────────────────────────────────────────────────────
define('PUBLIC_HTML',  '/home/u696371114/domains/vortexsoftinnovations.com/public_html');
define('TEMP_DIR',     sys_get_temp_dir());

header('Content-Type: application/json');

// ── 1. Verify deploy token ───────────────────────────────────────────────────
$receivedToken = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? '';
if (!hash_equals(DEPLOY_TOKEN, $receivedToken)) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized']));
}

// ── 2. Download latest ZIP from GitHub ──────────────────────────────────────
$zipUrl  = "https://github.com/" . GITHUB_USER . "/" . GITHUB_REPO . "/archive/refs/heads/" . GITHUB_BRANCH . ".zip";
$zipFile = TEMP_DIR . '/vortex_deploy_' . time() . '.zip';

$context = stream_context_create([
    'http' => [
        'method'     => 'GET',
        'header'     => "Authorization: token " . GITHUB_TOKEN . "\r\nUser-Agent: VortexDeploy/1.0\r\n",
        'timeout'    => 120,
        'follow_location' => 1,
    ]
]);

$zipContent = file_get_contents($zipUrl, false, $context);
if ($zipContent === false) {
    http_response_code(500);
    die(json_encode(['error' => 'Failed to download repository ZIP from GitHub']));
}
file_put_contents($zipFile, $zipContent);

// ── 3. Extract ZIP ───────────────────────────────────────────────────────────
$extractDir = TEMP_DIR . '/vortex_extract_' . time();
$zip = new ZipArchive();
if ($zip->open($zipFile) !== true) {
    http_response_code(500);
    @unlink($zipFile);
    die(json_encode(['error' => 'Failed to open ZIP file']));
}
$zip->extractTo($extractDir);
$zip->close();

// The ZIP extracts as REPO-BRANCH/ subfolder
$sourceDir = $extractDir . '/' . GITHUB_REPO . '-' . GITHUB_BRANCH;
if (!is_dir($sourceDir)) {
    // Try to find extracted folder
    $dirs = glob($extractDir . '/*', GLOB_ONLYDIR);
    $sourceDir = $dirs[0] ?? null;
}

if (!$sourceDir || !is_dir($sourceDir)) {
    http_response_code(500);
    die(json_encode(['error' => 'Cannot find extracted source directory', 'tried' => $extractDir]));
}

// ── 4. Files to SKIP (never overwrite on server) ────────────────────────────
$skipFiles = [
    '.git', '.github', 'README.md', 'package.json',
    'vortexsoft_website_details.txt', 'website_info.txt',
];

// ── 5. Copy files to public_html ─────────────────────────────────────────────
function copyRecursive($src, $dst, $skip = []) {
    if (!is_dir($dst)) mkdir($dst, 0755, true);
    $items = scandir($src);
    $count = 0;
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        if (in_array($item, $skip)) continue;
        $srcPath = $src . '/' . $item;
        $dstPath = $dst . '/' . $item;
        if (is_dir($srcPath)) {
            $count += copyRecursive($srcPath, $dstPath);
        } else {
            copy($srcPath, $dstPath);
            $count++;
        }
    }
    return $count;
}

$filesCopied = copyRecursive($sourceDir, PUBLIC_HTML, $skipFiles);

// ── 6. Cleanup temp files ────────────────────────────────────────────────────
@unlink($zipFile);
array_map('unlink', glob($extractDir . '/*'));
@rmdir($extractDir);

// ── 7. Success response ──────────────────────────────────────────────────────
echo json_encode([
    'success'      => true,
    'message'      => 'Deployment completed successfully',
    'files_copied' => $filesCopied,
    'deployed_at'  => date('Y-m-d H:i:s'),
    'branch'       => GITHUB_BRANCH,
]);
