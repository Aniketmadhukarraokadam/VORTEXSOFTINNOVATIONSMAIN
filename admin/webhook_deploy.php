<?php
/**
 * Vortexsoft Innovations — GitHub Auto-Deploy Webhook
 * URL: https://vortexsoftinnovations.com/admin/webhook_deploy.php
 */

define('DEPLOY_TOKEN', 'VortexDeploy6498286f401141b8');
define('GITHUB_USER',   'Aniketmadhukarraokadam');
define('GITHUB_REPO',   'VORTEXSOFTINNOVATIONSMAIN');
define('GITHUB_BRANCH', 'main');
define('PUBLIC_HTML',   dirname(__DIR__));

header('Content-Type: application/json');

// 1. Check security token
$receivedToken = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? $_GET['token'] ?? '';
if (!hash_equals(DEPLOY_TOKEN, $receivedToken)) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized']));
}

// 2. Ensure temp directory is inside public_html (bypasses open_basedir restrictions)
$tempDir = PUBLIC_HTML . '/uploads/temp';
if (!is_dir($tempDir)) {
    @mkdir($tempDir, 0777, true);
}

// 3. Helper function to download with cURL or file_get_contents
function downloadFile($url, $dest) {
    // Try cURL first
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        $fp = fopen($dest, 'w+');
        if ($fp) {
            curl_setopt_array($ch, [
                CURLOPT_FILE           => $fp,
                CURLOPT_TIMEOUT        => 120,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 5,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) VortexDeploy/1.0',
            ]);
            $exec = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            fclose($fp);

            if ($exec && $httpCode >= 200 && $httpCode < 300 && filesize($dest) > 1000) {
                return true;
            }
        }
    }

    // Fallback: copy / file_get_contents
    $ctx = stream_context_create([
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
        'http' => ['header' => "User-Agent: VortexDeploy/1.0\r\n"]
    ]);
    $data = @file_get_contents($url, false, $ctx);
    if ($data !== false && strlen($data) > 1000) {
        return @file_put_contents($dest, $data) !== false;
    }

    return false;
}

// 4. Download latest repository ZIP from GitHub
$zipUrl  = "https://codeload.github.com/" . GITHUB_USER . "/" . GITHUB_REPO . "/zip/refs/heads/" . GITHUB_BRANCH;
$tempZip = $tempDir . '/deploy_' . time() . '.zip';

if (!downloadFile($zipUrl, $tempZip)) {
    // Fallback to github.com archive URL
    $zipUrl = "https://github.com/" . GITHUB_USER . "/" . GITHUB_REPO . "/archive/refs/heads/" . GITHUB_BRANCH . ".zip";
    if (!downloadFile($zipUrl, $tempZip)) {
        http_response_code(500);
        die(json_encode([
            'error'   => 'Failed to download repository ZIP from GitHub',
            'tempDir' => $tempDir,
            'url'     => $zipUrl
        ]));
    }
}

// 5. Extract ZIP archive
$extractDir = $tempDir . '/extract_' . time();
$zip = new ZipArchive();
if ($zip->open($tempZip) !== true) {
    @unlink($tempZip);
    http_response_code(500);
    die(json_encode(['error' => 'Failed to open ZIP archive']));
}

$zip->extractTo($extractDir);
$zip->close();
@unlink($tempZip);

// 6. Find extracted source folder
$sourceDir = $extractDir . '/' . GITHUB_REPO . '-' . GITHUB_BRANCH;
if (!is_dir($sourceDir)) {
    $dirs = glob($extractDir . '/*', GLOB_ONLYDIR);
    $sourceDir = $dirs[0] ?? null;
}

if (!$sourceDir || !is_dir($sourceDir)) {
    http_response_code(500);
    die(json_encode(['error' => 'Could not find extracted repository folder']));
}

// 7. Copy files recursively
$skip = ['.git', '.github', 'README.md', 'package.json', 'vortexsoft_website_details.txt', 'website_info.txt'];

function syncDir($src, $dst, $skip = []) {
    if (!is_dir($dst)) @mkdir($dst, 0755, true);
    $count = 0;
    foreach (scandir($src) as $item) {
        if ($item === '.' || $item === '..' || in_array($item, $skip)) continue;
        $s = "$src/$item";
        $d = "$dst/$item";
        if (is_dir($s)) {
            $count += syncDir($s, $d);
        } else {
            @copy($s, $d);
            $count++;
        }
    }
    return $count;
}

$filesCopied = syncDir($sourceDir, PUBLIC_HTML, $skip);

// 8. Cleanup temp extract folder
function cleanupDir($dir) {
    if (!is_dir($dir)) return;
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $p = "$dir/$f";
        is_dir($p) ? cleanupDir($p) : @unlink($p);
    }
    @rmdir($dir);
}
cleanupDir($extractDir);

// 9. Write deploy log
@file_put_contents(PUBLIC_HTML . '/admin/deploy.log', date('Y-m-d H:i:s') . " - Auto-deploy success ($filesCopied files)\n", FILE_APPEND);

// 10. Output success JSON
echo json_encode([
    'success'      => true,
    'message'      => 'Auto-deploy completed successfully',
    'files_copied' => $filesCopied,
    'timestamp'    => date('Y-m-d H:i:s')
]);
