<?php
/**
 * Vortexsoft Innovations — GitHub Auto-Deploy Webhook
 * URL: https://vortexsoftinnovations.com/admin/webhook_deploy.php
 */

define('DEPLOY_TOKEN', 'VortexDeploy6498286f401141b8');
define('GITHUB_USER',   'Aniketmadhukarraokadam');
define('GITHUB_REPO',   'VORTEXSOFTINNOVATIONSMAIN');
define('GITHUB_BRANCH', 'main');
define('PUBLIC_HTML',   '/home/u696371114/domains/vortexsoftinnovations.com/public_html');

header('Content-Type: application/json');

// 1. Check security token
$receivedToken = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? $_GET['token'] ?? '';
if (!hash_equals(DEPLOY_TOKEN, $receivedToken)) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized']));
}

// 2. Helper function to download file using cURL
function downloadWithCurl($url, $destPath) {
    $ch = curl_init($url);
    $fp = fopen($destPath, 'w+');
    if (!$fp) return false;

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

    return ($exec && $httpCode >= 200 && $httpCode < 300);
}

// 3. Download latest repo ZIP
$zipUrl  = "https://github.com/" . GITHUB_USER . "/" . GITHUB_REPO . "/archive/refs/heads/" . GITHUB_BRANCH . ".zip";
$tempZip = sys_get_temp_dir() . '/vortex_deploy_' . time() . '.zip';

if (!downloadWithCurl($zipUrl, $tempZip) || !file_exists($tempZip) || filesize($tempZip) < 1000) {
    @unlink($tempZip);
    http_response_code(500);
    die(json_encode([
        'error' => 'Failed to download repository ZIP via cURL',
        'url'   => $zipUrl
    ]));
}

// 4. Extract ZIP file
$extractDir = sys_get_temp_dir() . '/vortex_extract_' . time();
$zip = new ZipArchive();
if ($zip->open($tempZip) !== true) {
    @unlink($tempZip);
    http_response_code(500);
    die(json_encode(['error' => 'Failed to open ZIP archive']));
}

$zip->extractTo($extractDir);
$zip->close();
@unlink($tempZip);

// 5. Find source directory
$sourceDir = $extractDir . '/' . GITHUB_REPO . '-' . GITHUB_BRANCH;
if (!is_dir($sourceDir)) {
    $dirs = glob($extractDir . '/*', GLOB_ONLYDIR);
    $sourceDir = $dirs[0] ?? null;
}

if (!$sourceDir || !is_dir($sourceDir)) {
    http_response_code(500);
    die(json_encode(['error' => 'Could not find extracted repository folder']));
}

// 6. Copy files recursively
$skip = ['.git', '.github', 'README.md', 'package.json', 'vortexsoft_website_details.txt', 'website_info.txt'];

function syncFiles($src, $dst, $skip = []) {
    if (!is_dir($dst)) @mkdir($dst, 0755, true);
    $copied = 0;
    foreach (scandir($src) as $item) {
        if ($item === '.' || $item === '..' || in_array($item, $skip)) continue;
        $s = "$src/$item";
        $d = "$dst/$item";
        if (is_dir($s)) {
            $copied += syncFiles($s, $d);
        } else {
            @copy($s, $d);
            $copied++;
        }
    }
    return $copied;
}

$count = syncFiles($sourceDir, PUBLIC_HTML, $skip);

// 7. Cleanup temp extract folder
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

// 8. Output success response
echo json_encode([
    'success'      => true,
    'message'      => 'Auto-deploy completed successfully via cURL',
    'files_copied' => $count,
    'timestamp'    => date('Y-m-d H:i:s')
]);
