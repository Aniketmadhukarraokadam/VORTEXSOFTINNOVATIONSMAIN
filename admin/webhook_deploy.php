<?php
/**
 * Vortexsoft Innovations — Instant Response Webhook Deployer
 * URL: https://vortexsoftinnovations.com/admin/webhook_deploy.php
 */

define('DEPLOY_TOKEN', 'VortexDeploy6498286f401141b8');
define('GITHUB_USER',   'Aniketmadhukarraokadam');
define('GITHUB_REPO',   'VORTEXSOFTINNOVATIONSMAIN');
define('GITHUB_BRANCH', 'main');
define('PUBLIC_HTML',   '/home/u696371114/domains/vortexsoftinnovations.com/public_html');

// Clean all output buffering
while (ob_get_level() > 0) {
    @ob_end_clean();
}

header('Content-Type: application/json');

// 1. Verify token
$receivedToken = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? $_GET['token'] ?? '';
if (!hash_equals(DEPLOY_TOKEN, $receivedToken)) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized']));
}

// 2. Prepare success payload
$payload = json_encode([
    'success' => true,
    'message' => 'Deploy started in background',
    'timestamp' => date('Y-m-d H:i:s')
]);

// 3. Send HTTP response immediately (under 50ms)
header('Content-Length: ' . strlen($payload));
header('Connection: close');
echo $payload;

if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
} else {
    @flush();
}

// 4. Continue background deployment
ignore_user_abort(true);
set_time_limit(300);

// Helper function to download with cURL
function downloadZip($url, $dest) {
    $ch = curl_init($url);
    $fp = fopen($dest, 'w+');
    if (!$fp) return false;

    curl_setopt_array($ch, [
        CURLOPT_FILE           => $fp,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT      => 'VortexDeploy/1.0',
    ]);

    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    return ($res && $code >= 200 && $code < 300);
}

$zipUrl = "https://github.com/" . GITHUB_USER . "/" . GITHUB_REPO . "/archive/refs/heads/" . GITHUB_BRANCH . ".zip";
$tempZip = sys_get_temp_dir() . '/vortex_' . time() . '.zip';

if (!downloadZip($zipUrl, $tempZip)) {
    error_log("[VortexDeploy] cURL download failed: $zipUrl");
    @unlink($tempZip);
    exit;
}

$extractDir = sys_get_temp_dir() . '/vortex_ex_' . time();
$zip = new ZipArchive();
if ($zip->open($tempZip) === true) {
    $zip->extractTo($extractDir);
    $zip->close();
    @unlink($tempZip);

    $sourceDir = $extractDir . '/' . GITHUB_REPO . '-' . GITHUB_BRANCH;
    if (!is_dir($sourceDir)) {
        $dirs = glob($extractDir . '/*', GLOB_ONLYDIR);
        $sourceDir = $dirs[0] ?? null;
    }

    if ($sourceDir && is_dir($sourceDir)) {
        $skip = ['.git', '.github', 'README.md', 'package.json', 'vortexsoft_website_details.txt', 'website_info.txt'];
        
        function syncDir($src, $dst, $skip = []) {
            if (!is_dir($dst)) @mkdir($dst, 0755, true);
            foreach (scandir($src) as $item) {
                if ($item === '.' || $item === '..' || in_array($item, $skip)) continue;
                $s = "$src/$item";
                $d = "$dst/$item";
                is_dir($s) ? syncDir($s, $d) : @copy($s, $d);
            }
        }
        
        syncDir($sourceDir, PUBLIC_HTML, $skip);

        // Write deploy log
        @file_put_contents(PUBLIC_HTML . '/admin/deploy.log', date('Y-m-d H:i:s') . " - Auto-deploy success\n", FILE_APPEND);
    }
}

// Cleanup
function cleanTmp($dir) {
    if (!is_dir($dir)) return;
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $p = "$dir/$f";
        is_dir($p) ? cleanTmp($p) : @unlink($p);
    }
    @rmdir($dir);
}
cleanTmp($extractDir);
