<?php
/**
 * Vortexsoft Innovations — GitHub Auto-Deploy Webhook
 * URL: https://vortexsoftinnovations.com/admin/webhook_deploy.php
 *
 * Strategy: Respond to GitHub Actions immediately (avoids 30s timeout),
 * then run the actual download + deploy in the background.
 */

// ── Security token ───────────────────────────────────────────────────────────
define('DEPLOY_TOKEN', 'VortexDeploy6498286f401141b8');

// ── GitHub repo details ──────────────────────────────────────────────────────
define('GITHUB_USER',   'Aniketmadhukarraokadam');
define('GITHUB_REPO',   'VORTEXSOFTINNOVATIONSMAIN');
define('GITHUB_BRANCH', 'main');
define('GITHUB_TOKEN',  getenv('GITHUB_API_TOKEN') ?: '');

// ── Server path ──────────────────────────────────────────────────────────────
define('PUBLIC_HTML', '/home/u696371114/domains/vortexsoftinnovations.com/public_html');

// ── 1. Verify deploy token ───────────────────────────────────────────────────
header('Content-Type: application/json');
$receivedToken = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? '';
if (!hash_equals(DEPLOY_TOKEN, $receivedToken)) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized']));
}

// ── 2. Respond to GitHub Actions IMMEDIATELY (avoid 30s timeout) ─────────────
$responseBody = json_encode([
    'success'    => true,
    'message'    => 'Deploy started in background',
    'started_at' => date('Y-m-d H:i:s'),
]);
header('Content-Encoding: none');
header('Content-Length: ' . strlen($responseBody));
header('Connection: close');
echo $responseBody;

// Flush all output buffers to close the HTTP connection
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
} else {
    ob_end_flush();
    flush();
}

// ── 3. Continue deploying in background (connection is already closed) ────────
ignore_user_abort(true);
set_time_limit(300);

// ── 4. Download latest ZIP from GitHub ───────────────────────────────────────
$zipUrl  = "https://github.com/" . GITHUB_USER . "/" . GITHUB_REPO . "/archive/refs/heads/" . GITHUB_BRANCH . ".zip";
$zipFile = sys_get_temp_dir() . '/vortex_deploy_' . time() . '.zip';

$ctx = stream_context_create([
    'http' => [
        'method'          => 'GET',
        'header'          => "Authorization: token " . GITHUB_TOKEN . "\r\nUser-Agent: VortexDeploy/1.0\r\n",
        'timeout'         => 180,
        'follow_location' => 1,
    ]
]);
$zipContent = @file_get_contents($zipUrl, false, $ctx);
if ($zipContent === false) {
    error_log('[VortexDeploy] Failed to download ZIP from GitHub');
    exit;
}
file_put_contents($zipFile, $zipContent);

// ── 5. Extract ZIP ───────────────────────────────────────────────────────────
$extractDir = sys_get_temp_dir() . '/vortex_extract_' . time();
$zip = new ZipArchive();
if ($zip->open($zipFile) !== true) {
    error_log('[VortexDeploy] Failed to open ZIP');
    @unlink($zipFile);
    exit;
}
$zip->extractTo($extractDir);
$zip->close();
@unlink($zipFile);

// ── 6. Locate extracted source folder ────────────────────────────────────────
$sourceDir = $extractDir . '/' . GITHUB_REPO . '-' . GITHUB_BRANCH;
if (!is_dir($sourceDir)) {
    $dirs = glob($extractDir . '/*', GLOB_ONLYDIR);
    $sourceDir = $dirs[0] ?? null;
}
if (!$sourceDir || !is_dir($sourceDir)) {
    error_log('[VortexDeploy] Cannot find extracted source dir in: ' . $extractDir);
    exit;
}

// ── 7. Files/dirs to NEVER overwrite ─────────────────────────────────────────
$skip = ['.git', '.github', 'README.md', 'package.json', 'uploads',
         'vortexsoft_website_details.txt', 'website_info.txt'];

// ── 8. Copy files recursively ────────────────────────────────────────────────
function vortex_copy($src, $dst, $skip = []) {
    if (!is_dir($dst)) @mkdir($dst, 0755, true);
    foreach (scandir($src) as $item) {
        if ($item === '.' || $item === '..' || in_array($item, $skip)) continue;
        $s = "$src/$item";
        $d = "$dst/$item";
        is_dir($s) ? vortex_copy($s, $d) : @copy($s, $d);
    }
}
vortex_copy($sourceDir, PUBLIC_HTML, $skip);

// ── 9. Cleanup ────────────────────────────────────────────────────────────────
function rmdir_recursive($dir) {
    if (!is_dir($dir)) return;
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $p = "$dir/$f";
        is_dir($p) ? rmdir_recursive($p) : @unlink($p);
    }
    @rmdir($dir);
}
rmdir_recursive($extractDir);

// ── 10. Write deploy log ──────────────────────────────────────────────────────
$logFile = PUBLIC_HTML . '/admin/deploy.log';
$logEntry = date('Y-m-d H:i:s') . " | Deploy completed successfully\n";
file_put_contents($logFile, $logEntry, FILE_APPEND);
