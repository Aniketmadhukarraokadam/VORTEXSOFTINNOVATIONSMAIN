<?php
/**
 * Vortexsoft Innovations Private Limited — 301 Permanent Redirect
 * Eliminates intermediate redirect screens and consolidates SEO equity to canonical /service.php
 */
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("HTTP/1.1 301 Moved Permanently");
header("Location: https://www.vortexsoftinnovations.com/service.php" . $qs, true, 301);
exit;
