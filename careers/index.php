<?php
/**
 * Vortexsoft Innovations — careers/ redirect
 * Redirects permanently to canonical /careers.php with query string preserved
 */
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: /careers.php" . $qs, true, 301);
exit;
