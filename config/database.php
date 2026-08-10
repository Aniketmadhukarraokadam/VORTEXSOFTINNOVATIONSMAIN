<?php
/**
 * Vortexsoft Innovations — MySQL Database Connection (PDO)
 * Hostinger: u696371114_vortexsoftcom
 *
 * Credentials are loaded from config/.env (never committed to Git).
 * See config/.env.example for the template.
 */

// ── Load .env if it exists ──────────────────────────────────
$_env_file = __DIR__ . '/.env';
$_env = [];
if (file_exists($_env_file)) {
    $_env = @parse_ini_file($_env_file, false, INI_SCANNER_RAW) ?: [];
}
unset($_env_file);

define('DB_HOST',    $_env['DB_HOST']    ?? 'localhost');
define('DB_NAME',    $_env['DB_NAME']    ?? 'u696371114_vortexsoftcom');
define('DB_USER',    $_env['DB_USER']    ?? 'u696371114_vortexsoftcom');
define('DB_PASS',    $_env['DB_PASS']    ?? '');   // Must be set in .env on production!
define('DB_CHARSET', 'utf8mb4');
unset($_env);

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
            // Local dev fallback to SQLite if MySQL server is not running locally
            try {
                $sqlitePath = __DIR__ . '/../sqlite_dev.db';
                $pdo = new PDO("sqlite:" . $sqlitePath);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (Throwable $e2) {
                error_log('Database connection failed: ' . $e->getMessage());
                return null;
            }
        }
    }
    return $pdo;
}
