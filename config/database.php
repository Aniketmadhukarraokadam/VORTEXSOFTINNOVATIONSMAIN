<?php
/**
 * Vortexsoft Innovations — MySQL Database Connection (PDO)
 * Hostinger: u696371114_vortexsoftcom
 *
 * Credentials can be loaded from .env or default to production values.
 */

// ── Robust .env loader ──────────────────────────────────────
$_env = [];
$_possible_env_files = [
    __DIR__ . '/.env',
    dirname(__DIR__) . '/.env',
    dirname(__DIR__) . '/config/.env'
];

foreach ($_possible_env_files as $_env_file) {
    if (file_exists($_env_file)) {
        // First try parse_ini_file
        $parsed = @parse_ini_file($_env_file, false, INI_SCANNER_RAW);
        if (is_array($parsed) && !empty($parsed)) {
            $_env = array_merge($_env, $parsed);
        } else {
            // Line by line fallback
            $lines = @file($_env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines) {
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || $line[0] === '#') continue;
                    if (strpos($line, '=') !== false) {
                        list($k, $v) = explode('=', $line, 2);
                        $_env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
                    }
                }
            }
        }
    }
}
unset($_possible_env_files, $_env_file);

define('DB_HOST',    !empty($_env['DB_HOST']) ? $_env['DB_HOST'] : 'localhost');
define('DB_NAME',    !empty($_env['DB_NAME']) ? $_env['DB_NAME'] : 'u696371114_vortexsoftcom');
define('DB_USER',    !empty($_env['DB_USER']) ? $_env['DB_USER'] : 'u696371114_vortexsoftcom');
define('DB_PASS',    !empty($_env['DB_PASS']) ? $_env['DB_PASS'] : 'Madhukarrao@121');
define('DB_CHARSET', 'utf8mb4');
unset($_env);

function getDB(): ?PDO {
    static $pdo = null;
    static $tables_checked = false;

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

    if ($pdo && !$tables_checked) {
        $tables_checked = true;
        // Check if main table exists; if not, auto-create tables
        try {
            $check = $pdo->query("SELECT 1 FROM contact_inquiries LIMIT 1");
        } catch (Throwable $t) {
            ensure_core_database_tables($pdo);
        }
    }

    return $pdo;
}

function ensure_core_database_tables(PDO $db): void {
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    
    if ($driver === 'mysql') {
        $queries = [
            "SET NAMES utf8mb4;",
            "CREATE TABLE IF NOT EXISTS `contact_inquiries` (
              `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `name`         VARCHAR(120) NOT NULL,
              `email`        VARCHAR(180) NOT NULL,
              `phone`        VARCHAR(30)  DEFAULT NULL,
              `service`      VARCHAR(120) DEFAULT 'General Inquiry',
              `company`      VARCHAR(120) DEFAULT NULL,
              `message`      TEXT         NOT NULL,
              `ip_address`   VARCHAR(45)  DEFAULT NULL,
              `user_agent`   VARCHAR(255) DEFAULT NULL,
              `source_page`  VARCHAR(255) DEFAULT NULL,
              `is_read`      TINYINT(1)   NOT NULL DEFAULT 0,
              `is_replied`   TINYINT(1)   NOT NULL DEFAULT 0,
              `notes`        TEXT         DEFAULT NULL,
              `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              INDEX `idx_email`      (`email`),
              INDEX `idx_is_read`    (`is_read`),
              INDEX `idx_created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            
            "CREATE TABLE IF NOT EXISTS `job_applications` (
              `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
              `job_title`       VARCHAR(200)  NOT NULL,
              `department`      VARCHAR(100)  DEFAULT NULL,
              `applicant_name`  VARCHAR(120)  NOT NULL,
              `email`           VARCHAR(180)  NOT NULL,
              `phone`           VARCHAR(30)   DEFAULT NULL,
              `current_location`VARCHAR(120)  DEFAULT NULL,
              `experience_years`DECIMAL(4,1)  DEFAULT NULL,
              `current_company` VARCHAR(120)  DEFAULT NULL,
              `notice_period`   VARCHAR(60)   DEFAULT NULL,
              `expected_ctc`    VARCHAR(60)   DEFAULT NULL,
              `resume_filename` VARCHAR(255)  DEFAULT NULL,
              `resume_path`     VARCHAR(500)  DEFAULT NULL,
              `cover_letter`    TEXT          DEFAULT NULL,
              `linkedin_url`    VARCHAR(300)  DEFAULT NULL,
              `portfolio_url`   VARCHAR(300)  DEFAULT NULL,
              `ip_address`      VARCHAR(45)   DEFAULT NULL,
              `status`          ENUM('new','reviewed','shortlisted','interview','offered','rejected','withdrawn')
                                              NOT NULL DEFAULT 'new',
              `admin_notes`     TEXT          DEFAULT NULL,
              `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              INDEX `idx_email`      (`email`),
              INDEX `idx_status`     (`status`),
              INDEX `idx_job_title`  (`job_title`),
              INDEX `idx_created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            
            "CREATE TABLE IF NOT EXISTS `blog_posts` (
              `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `title`        VARCHAR(300) NOT NULL,
              `slug`         VARCHAR(320) NOT NULL,
              `excerpt`      VARCHAR(500) DEFAULT NULL,
              `content`      LONGTEXT     NOT NULL,
              `author`       VARCHAR(100) NOT NULL DEFAULT 'Vortexsoft Team',
              `author_role`  VARCHAR(100) DEFAULT NULL,
              `cover_image`  VARCHAR(500) DEFAULT NULL,
              `category`     VARCHAR(100) NOT NULL DEFAULT 'General',
              `tags`         VARCHAR(500) DEFAULT NULL,
              `meta_title`   VARCHAR(300) DEFAULT NULL,
              `meta_desc`    VARCHAR(500) DEFAULT NULL,
              `views`        INT UNSIGNED NOT NULL DEFAULT 0,
              `is_published` TINYINT(1)   NOT NULL DEFAULT 0,
              `is_featured`  TINYINT(1)   NOT NULL DEFAULT 0,
              `published_at` DATETIME     DEFAULT NULL,
              `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `uk_slug`         (`slug`),
              INDEX `idx_category`         (`category`),
              INDEX `idx_is_published`     (`is_published`),
              INDEX `idx_published_at`     (`published_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            
            "CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
              `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `email`          VARCHAR(180) NOT NULL,
              `name`           VARCHAR(120) DEFAULT NULL,
              `ip_address`     VARCHAR(45)  DEFAULT NULL,
              `is_active`      TINYINT(1)   NOT NULL DEFAULT 1,
              `unsubscribe_token` VARCHAR(64) DEFAULT NULL,
              `subscribed_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `unsubscribed_at`DATETIME     DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `uk_email` (`email`),
              INDEX `idx_is_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            
            "CREATE TABLE IF NOT EXISTS `admin_users` (
              `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `username`     VARCHAR(60)  NOT NULL,
              `password_hash`VARCHAR(255) NOT NULL,
              `email`        VARCHAR(180) NOT NULL,
              `full_name`    VARCHAR(120) DEFAULT NULL,
              `role`         ENUM('super_admin','admin','viewer') NOT NULL DEFAULT 'admin',
              `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
              `last_login`   DATETIME     DEFAULT NULL,
              `login_count`  INT UNSIGNED NOT NULL DEFAULT 0,
              `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `uk_username` (`username`),
              UNIQUE KEY `uk_email`    (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            
            "CREATE TABLE IF NOT EXISTS `ai_generation_logs` (
              `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `topic`          VARCHAR(255) NOT NULL,
              `target_keyword` VARCHAR(255) NOT NULL,
              `provider`       VARCHAR(50)  NOT NULL,
              `model`          VARCHAR(100) NOT NULL,
              `latency_ms`     INT UNSIGNED NOT NULL DEFAULT 0,
              `status`         VARCHAR(50)  NOT NULL DEFAULT 'success',
              `error_message`  TEXT         DEFAULT NULL,
              `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              INDEX `idx_provider`   (`provider`),
              INDEX `idx_status`     (`status`),
              INDEX `idx_created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        ];

        foreach ($queries as $q) {
            try { $db->exec($q); } catch (Throwable $t) {}
        }

        try {
            $p1 = password_hash('ShivaG@1437', PASSWORD_BCRYPT, ['cost' => 10]);
            $p2 = password_hash('Mrunal@9996', PASSWORD_BCRYPT, ['cost' => 10]);
            $stmt = $db->prepare("REPLACE INTO `admin_users` (`id`, `username`, `password_hash`, `email`, `full_name`, `role`, `is_active`) VALUES
                (1, 'admin@vortexsoftinnovations.in', :p1, 'admin@vortexsoftinnovations.in', 'Super Admin', 'super_admin', 1),
                (2, 'Aniket@vortexsoftinnovations.in', :p2, 'Aniket@vortexsoftinnovations.in', 'Aniket Kadam', 'admin', 1)");
            $stmt->execute([':p1' => $p1, ':p2' => $p2]);
        } catch (Throwable $t) {}
    }
}
