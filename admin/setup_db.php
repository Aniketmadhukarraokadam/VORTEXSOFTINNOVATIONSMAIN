<?php
/**
 * Vortexsoft Innovations — Automatic Web Database Installer
 * Access in browser: https://vortexsoftinnovations.com/admin/setup_db.php
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: text/html; charset=utf-8');

$db = getDB();
if (!$db) {
    die("
    <div style='font-family:sans-serif;max-width:600px;margin:50px auto;padding:30px;background:#fff5f5;border:1px solid #fed7d7;border-radius:12px;color:#9b2c2c;'>
        <h2>❌ Database Connection Failed</h2>
        <p>Could not connect to MySQL server. Please verify database credentials in <code>config/database.php</code>.</p>
    </div>");
}

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

    "REPLACE INTO `admin_users` (`id`, `username`, `password_hash`, `email`, `full_name`, `role`, `is_active`) VALUES
    (1, 'Aniket1800', '$2y$10$MUQgvXQBrRkaG.vKSDO5quIZQVYEA56nMXPs6ImwRMqEIClc/IYk.', 'careers@vortexsoftinnovations.in', 'Aniket Kadam', 'super_admin', 1);",

    "REPLACE INTO `blog_posts` (`id`, `title`, `slug`, `excerpt`, `content`, `author`, `category`, `is_published`, `is_featured`, `published_at`) VALUES
    (1, 'Vortexsoft Group: Your Trusted Global IT & BPO Partner', 'vortexsoft-group-trusted-global-it-bpo-partner', 'Discover how Vortexsoft Group delivers world-class IT, AI, Healthcare, and BPO services to 150+ clients across the globe.', '<p>Vortexsoft Innovations Pvt. Ltd., a proud member of the Vortexsoft Group, has been delivering exceptional outsourcing solutions since 2020.</p><p>With ISO 27001:2013 certification, we ensure the highest standards of information security while providing 75+ specialized services.</p>', 'Vortexsoft Team', 'Company News', 1, 1, NOW());"
];

$errors = [];
$success_count = 0;

foreach ($queries as $q) {
    try {
        $db->exec($q);
        $success_count++;
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

if (empty($errors)) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>Database Setup Completed — Vortexsoft Group</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #080B1A; color: #fff; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #ffffff; color: #0f172a; padding: 40px; border-radius: 20px; max-width: 520px; width: 100%; text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
        .icon { width: 70px; height: 70px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; margin: 0 auto 20px; }
        h1 { margin: 0 0 10px; font-size: 24px; color: #0f172a; }
        p { color: #64748b; font-size: 15px; margin-bottom: 24px; line-height: 1.6; }
        .info-box { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 20px; text-align: left; margin-bottom: 28px; font-size: 14px; }
        .info-box div { margin-bottom: 8px; color: #334155; }
        .info-box div:last-child { margin-bottom: 0; }
        .btn { display: block; width: 100%; background: linear-gradient(135deg, #1C2280, #2d35c4); color: #ffffff; text-decoration: none; padding: 14px; border-radius: 10px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(28,34,128,0.3); }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(28,34,128,0.4); }
    </style>
    </head>
    <body>
        <div class="card">
            <div class="icon">✓</div>
            <h1>Database Setup Completed!</h1>
            <p>All 5 database tables have been created and initialized on Hostinger.</p>

            <div class="info-box">
                <div><strong>Admin Username:</strong> <code>Aniket1800</code></div>
                <div><strong>Admin Password:</strong> <code>Aniket@1800</code></div>
                <div><strong>Database:</strong> <code>u696371114_adminvortex</code></div>
            </div>

            <a href="/admin/login.php" class="btn">Go to Admin Login →</a>
        </div>
    </body>
    </html>
    <?php
} else {
    echo "
    <div style='font-family:sans-serif;max-width:600px;margin:50px auto;padding:30px;background:#fff5f5;border:1px solid #fed7d7;border-radius:12px;color:#9b2c2c;'>
        <h2>❌ Database Setup Error</h2>
        <ul>";
        foreach ($errors as $err) {
            echo "<li>" . htmlspecialchars($err) . "</li>";
        }
        echo "</ul>
    </div>";
}
