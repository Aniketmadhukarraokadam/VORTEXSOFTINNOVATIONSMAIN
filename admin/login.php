<?php
/**
 * Vortexsoft Innovations — Admin Panel: Login
 * /admin/login.php
 */

session_start();
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

function auto_install_tables(PDO $db): void {
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
        (1, 'admin@vortexsoftinnovations.in', '$2y$12$8IpMP6IJeshSPurTe5.baubMZF5rGtkdX4KDIAWiwN6tSSGiwR5SW', 'admin@vortexsoftinnovations.in', 'Super Admin', 'super_admin', 1);",
        "REPLACE INTO `admin_users` (`id`, `username`, `password_hash`, `email`, `full_name`, `role`, `is_active`) VALUES
        (2, 'Aniket@vortexsoftinnovations.in', '$2y$12$AmQgvWVp/eQKMCnDzD3TK.a.0GwTVdoRcE4rS6i0VnA9cAWdP5Xta', 'Aniket@vortexsoftinnovations.in', 'Aniket Kadam', 'admin', 1);"
    ];
    foreach ($queries as $q) {
        try { $db->exec($q); } catch (Throwable $t) {}
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!check_rate_limit('admin_login_' . md5(get_client_ip()), 10, 300)) {
        $error = 'Too many login attempts. Please wait 5 minutes.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Please enter username/email and password.';
    } else {
        $db = getDB();
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT * FROM admin_users WHERE (LOWER(username) = LOWER(:u) OR LOWER(email) = LOWER(:u)) AND is_active = 1 LIMIT 1");
                $stmt->execute([':u' => $username]);
                $admin = $stmt->fetch();
            } catch (Throwable $e) {
                // Auto-create missing tables on the fly
                auto_install_tables($db);
                try {
                    $stmt = $db->prepare("SELECT * FROM admin_users WHERE (LOWER(username) = LOWER(:u) OR LOWER(email) = LOWER(:u)) AND is_active = 1 LIMIT 1");
                    $stmt->execute([':u' => $username]);
                    $admin = $stmt->fetch();
                } catch (Throwable $e2) {
                    $admin = false;
                }
            }

            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION[ADMIN_SESSION]  = true;
                $_SESSION[ADMIN_USER_KEY] = $admin['id'];
                $_SESSION['admin_name']   = $admin['full_name'] ?? $admin['username'];
                $_SESSION['admin_role']   = $admin['role'];

                try {
                    $db->prepare("UPDATE admin_users SET last_login=NOW(), login_count=login_count+1 WHERE id=:id")
                       ->execute([':id' => $admin['id']]);
                } catch (Throwable $e3) {}

                header('Location: /admin/dashboard.php');
                exit;
            } else {
                $error = 'Invalid username/email or password.';
                error_log("Failed admin login attempt for user: $username from IP: " . get_client_ip());
            }
        } else {
            $error = 'Database unavailable. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — Vortexsoft Group</title>
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="/assets/vendor/bootstrap.min.css">
<link rel="stylesheet" href="/assets/vendor/fontawesome/all.min.css">
<link rel="stylesheet" href="/assets/vendor/fonts.css">
<link rel="icon" href="/icon.jpg">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#080B1A 0%,#1C2280 50%,#0D1035 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden}
body::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:50px 50px;pointer-events:none}
.login-card{background:rgba(255,255,255,.97);border-radius:24px;padding:48px 40px;width:100%;max-width:440px;box-shadow:0 30px 80px rgba(0,0,0,.4),0 0 0 1px rgba(255,255,255,.1);position:relative;z-index:2}
.brand{text-align:center;margin-bottom:36px}
.brand img{height:55px;margin-bottom:12px;display:block;margin-left:auto;margin-right:auto}
.brand h4{font-family:'Poppins',sans-serif;font-size:15px;font-weight:700;color:#1C2280;margin:0}
.brand p{font-size:12px;color:#94a3b8;margin-top:4px}
.form-label{font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;display:block}
.form-control{border:1.5px solid #e2e8f0;border-radius:10px;padding:12px 16px;font-size:14px;width:100%;font-family:'Inter',sans-serif;transition:.3s;background:#fff;color:#1e293b}
.form-control:focus{border-color:#1C2280;box-shadow:0 0 0 3px rgba(28,34,128,.1);outline:none;background:#fff}
.input-group-icon{position:relative}
.input-group-icon .form-control{padding-left:44px}
.input-group-icon .icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:15px;pointer-events:none}
.btn-login{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;font-family:'Poppins',sans-serif;font-size:15px;font-weight:600;padding:14px;border:none;border-radius:10px;width:100%;cursor:pointer;transition:.3s;display:flex;align-items:center;justify-content:center;gap:8px;margin-top:8px}
.btn-login:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(28,34,128,.35)}
.alert-error{background:#fff5f5;border:1px solid rgba(204,34,40,.2);color:#CC2228;border-radius:10px;padding:12px 16px;font-size:14px;margin-bottom:20px;display:flex;align-items:center;gap:10px}
.back-link{text-align:center;margin-top:20px;font-size:13px;color:#64748b}
.back-link a{color:#1C2280;font-weight:600;text-decoration:none}
.secured-badge{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:24px;font-size:12px;color:#94a3b8;font-weight:500}
.secured-badge i{color:#10b981}
</style>
</head>
<body>
<div class="login-card">
  <div class="brand">
    <img src="/logo-header.png" alt="Vortexsoft Group">
    <h4>Admin Panel</h4>
    <p>Vortexsoft Group — Internal Management</p>
  </div>

  <?php if ($error): ?>
  <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="" id="loginForm">
    <div class="mb-4">
      <label class="form-label" for="username">Username or Email</label>
      <div class="input-group-icon">
        <i class="fas fa-user icon"></i>
        <input type="text" class="form-control" id="username" name="username" placeholder="admin@vortexsoftinnovations.in" autocomplete="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>
    </div>
    <div class="mb-4">
      <label class="form-label" for="password">Password</label>
      <div class="input-group-icon">
        <i class="fas fa-lock icon"></i>
        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
      </div>
    </div>
    <button type="submit" class="btn-login" id="loginBtn">
      <i class="fas fa-sign-in-alt"></i> Sign In to Dashboard
    </button>
  </form>

  <div class="back-link"><a href="/index.php"><i class="fas fa-arrow-left me-1"></i> Back to Website</a></div>
  <div class="secured-badge"><i class="fas fa-shield-alt"></i> Secure Admin Area — Authorized Personnel Only</div>
</div>

<script src="/assets/vendor/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('loginForm').addEventListener('submit',function(){
  var btn = document.getElementById('loginBtn');
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
  btn.disabled = true;
});
</script>
</body>
</html>
