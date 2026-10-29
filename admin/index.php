<?php
/**
 * Vortexsoft Innovations — Admin Entry Point
 * Redirects to dashboard (or login if unauthenticated)
 */
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/functions.php';
admin_check();
header('Location: /admin/dashboard.php');
exit;
