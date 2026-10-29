<?php
/**
 * Local Development Router for Vortexsoft Innovations
 * Used with PHP built-in server: php -S localhost:8000 router.php
 */

$rawUri = $_SERVER['REQUEST_URI'];
$uri = parse_url($rawUri, PHP_URL_PATH);
$file = __DIR__ . $uri;

// 1. Direct static file match
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    return false; // Let PHP built-in server serve the static file directly
}

// 1.5 Canonical aliases
$aliases = [
    '/privacy-policy' => '/privacy.php',
    '/privacy'        => '/privacy.php',
    '/terms-and-conditions' => '/terms.php',
    '/terms-conditions'     => '/terms.php',
    '/terms'                => '/terms.php',
    '/faq'                  => '/faq.php',
];
if (isset($aliases[$uri])) {
    require __DIR__ . $aliases[$uri];
    return true;
}

// 2. Directory match (e.g. /admin/ or /about/)
if (is_dir($file)) {
    $indexPhp = rtrim($file, DIRECTORY_SEPARATOR . '/') . '/index.php';
    $indexHtml = rtrim($file, DIRECTORY_SEPARATOR . '/') . '/index.html';
    if (file_exists($indexPhp)) {
        require $indexPhp;
        return true;
    }
    if (file_exists($indexHtml)) {
        return false;
    }
}

// 3. Clean URL matching .php file (e.g. /about -> /about.php)
if (file_exists($file . '.php')) {
    require $file . '.php';
    return true;
}

// 4. HTML extension rewrite to .php (e.g. /about.html -> /about.php)
if (preg_match('/\.html$/', $uri)) {
    $phpFile = __DIR__ . preg_replace('/\.html$/', '.php', $uri);
    if (file_exists($phpFile)) {
        require $phpFile;
        return true;
    }
}

// 5. Blog detail routing: /blog/slug -> /blog-detail.php?slug=slug
if (preg_match('#^/blog/([a-zA-Z0-9_\-]+)(?:\.php|/?)$#', $uri, $matches)) {
    $_GET['slug'] = $matches[1];
    require __DIR__ . '/blog-detail.php';
    return true;
}

// 6. Default root index
if ($uri === '/' || $uri === '') {
    require __DIR__ . '/index.php';
    return true;
}

// 7. 404 fallback
if (file_exists(__DIR__ . '/404.php')) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    return true;
}

return false;
