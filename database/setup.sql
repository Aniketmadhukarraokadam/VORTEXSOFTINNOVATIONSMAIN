-- ============================================================
--  VORTEXSOFT INNOVATIONS — MySQL Database Setup Script
--  Database: u696371114_vortexsoftcom
--  Run this on Hostinger via phpMyAdmin or MySQL CLI
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ────────────────────────────────────────────────────────────
-- 1. CONTACT INQUIRIES
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `contact_inquiries` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 2. JOB APPLICATIONS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `job_applications` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 3. BLOG POSTS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `blog_posts` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 4. NEWSLETTER SUBSCRIBERS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 5. ADMIN USERS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admin_users` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 6. SEED ADMIN ACCOUNTS
-- ────────────────────────────────────────────────────────────

-- 1. Super Admin (Username/Email: admin@vortexsoftinnovations.in | Password: Mrunal@9996)
REPLACE INTO `admin_users` (`id`, `username`, `password_hash`, `email`, `full_name`, `role`, `is_active`) VALUES
(1, 'admin@vortexsoftinnovations.in', '$2y$12$8IpMP6IJeshSPurTe5.baubMZF5rGtkdX4KDIAWiwN6tSSGiwR5SW', 'admin@vortexsoftinnovations.in', 'Super Admin', 'super_admin', 1);

-- 2. Admin (Username/Email: Aniket@vortexsoftinnovations.in | Password: ShivaG@1437)
REPLACE INTO `admin_users` (`id`, `username`, `password_hash`, `email`, `full_name`, `role`, `is_active`) VALUES
(2, 'Aniket@vortexsoftinnovations.in', '$2y$12$AmQgvWVp/eQKMCnDzD3TK.a.0GwTVdoRcE4rS6i0VnA9cAWdP5Xta', 'Aniket@vortexsoftinnovations.in', 'Aniket Kadam', 'admin', 1);

-- Sample blog post
REPLACE INTO `blog_posts` (`id`, `title`, `slug`, `excerpt`, `content`, `author`, `category`, `is_published`, `is_featured`, `published_at`) VALUES
(1, 'Vortexsoft Group: Your Trusted Global IT & BPO Partner', 'vortexsoft-group-trusted-global-it-bpo-partner', 'Discover how Vortexsoft Group delivers world-class IT, AI, Healthcare, and BPO services to 150+ clients across the globe.', '<p>Vortexsoft Innovations Pvt. Ltd., a proud member of the Vortexsoft Group, has been delivering exceptional outsourcing solutions since 2020.</p><p>With ISO 27001:2013 certification, we ensure the highest standards of information security while providing 75+ specialized services.</p>', 'Vortexsoft Team', 'Company News', 1, 1, NOW());

SET FOREIGN_KEY_CHECKS = 1;
