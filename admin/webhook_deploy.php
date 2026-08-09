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

// Helper function to download with cURL (using direct codeload URL)
function downloadZip($url, $dest) {
    $ch = curl_init($url);
    $fp = fopen($dest, 'w+');
    if (!$fp) return false;

    curl_setopt_array($ch, [
        CURLOPT_FILE           => $fp,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    ]);

    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    return ($res && $code >= 200 && $code < 300);
}

// Check security token
$receivedToken = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? $_GET['token'] ?? '';
if (!hash_equals(DEPLOY_TOKEN, $receivedToken)) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized']));
}

$tempZip = sys_get_temp_dir() . '/vortex_' . time() . '.zip';
// Use direct codeload URL to avoid 302 redirects
$zipUrl  = "https://codeload.github.com/" . GITHUB_USER . "/" . GITHUB_REPO . "/zip/refs/heads/" . GITHUB_BRANCH;

if (!downloadZip($zipUrl, $tempZip) || !file_exists($tempZip) || filesize($tempZip) < 1000) {
    @unlink($tempZip);
    http_response_code(500);
    die(json_encode([
        'error' => 'Failed to download repository ZIP from GitHub',
        'url'   => $zipUrl
    ]));
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
        @file_put_contents(PUBLIC_HTML . '/admin/deploy.log', date('Y-m-d H:i:s') . " - Auto-deploy success ($filesCopied files)\n", FILE_APPEND);
        
        echo json_encode([
            'success'      => true,
            'message'      => 'Auto-deploy completed successfully',
            'files_copied' => $filesCopied,
            'timestamp'    => date('Y-m-d H:i:s')
        ]);
        exit;
    }
}

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

http_response_code(500);
echo json_encode(['error' => 'Failed to extract ZIP archive']);
