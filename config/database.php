<?php
/**
 * Vortexsoft Innovations — MySQL Database Connection (PDO)
 * Hostinger: u696371114_adminvortex
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'u696371114_adminvortex');
define('DB_USER', 'u696371114_adminvortex');
define('DB_PASS', 'ShivaG@1437'); // Hostinger DB Password
define('DB_CHARSET', 'utf8mb4');

function getDB(): ?PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci";
        }
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (Throwable $e) {
            // Log error but don't expose credentials
            error_log('Database connection failed: ' . $e->getMessage());
            return null;
        }
    }
    return $pdo;
}
