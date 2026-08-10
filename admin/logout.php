<?php
/**
 * Admin Logout
 */
session_start();
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

log_admin_activity('Admin Logout', 'User logged out.');

session_unset();
session_destroy();
header('Location: /admin/login.php');
exit;

