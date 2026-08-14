<?php
/**
 * Vortexsoft Innovations — Admin Panel: Login with OTP 2FA
 * /admin/login.php
 *
 * Flow:
 *   Step 1 — Enter username + password
 *   Step 2 — Enter 6-digit OTP sent to admin email
 *   Step 3 — Session granted, redirect to dashboard
 */

// ── Secure session settings ──────────────────────────────────
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');
session_start();

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// ── Brute-force lockout helper ─────────────────────────────
function get_bf_file(string $ip): string {
    return sys_get_temp_dir() . '/vx_bf_' . md5($ip) . '.json';
}
function is_locked_out(string $ip): bool {
    $file = get_bf_file($ip);
    if (!file_exists($file)) return false;
    $data = json_decode(file_get_contents($file), true);
    if (!$data) return false;
    if (($data['locked_until'] ?? 0) > time()) return true;
    if ($data['locked_until'] > 0 && $data['locked_until'] <= time()) @unlink($file);
    return false;
}
function record_failed_attempt(string $ip): void {
    $file = get_bf_file($ip);
    $data = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: ['count'=>0,'locked_until'=>0]) : ['count'=>0,'locked_until'=>0];
    $data['count'] = ($data['count'] ?? 0) + 1;
    $data['last_attempt'] = time();
    if ($data['count'] >= 5) { $data['locked_until'] = time() + 900; $data['count'] = 0; }
    file_put_contents($file, json_encode($data), LOCK_EX);
}
function reset_failed_attempts(string $ip): void { @unlink(get_bf_file($ip)); }

// ── Auto-install tables ────────────────────────────────────
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
          INDEX `idx_email` (`email`), INDEX `idx_is_read` (`is_read`), INDEX `idx_created_at` (`created_at`)
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
          `status`          ENUM('new','reviewed','shortlisted','interview','offered','rejected','withdrawn') NOT NULL DEFAULT 'new',
          `admin_notes`     TEXT          DEFAULT NULL,
          `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          INDEX `idx_email` (`email`), INDEX `idx_status` (`status`), INDEX `idx_created_at` (`created_at`)
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
          UNIQUE KEY `uk_slug` (`slug`),
          INDEX `idx_category` (`category`), INDEX `idx_is_published` (`is_published`)
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
          UNIQUE KEY `uk_email` (`email`)
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
        "CREATE TABLE IF NOT EXISTS `system_settings` (
          `setting_key`   VARCHAR(100) NOT NULL,
          `setting_value` TEXT         DEFAULT NULL,
          `updated_at`    DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`setting_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    ];
    foreach ($queries as $q) { try { $db->exec($q); } catch (Throwable $t) {} }
    try {
        $p1 = password_hash('Mrunal@9996',  PASSWORD_BCRYPT, ['cost' => 10]);
        $p2 = password_hash('ShivaG@1437', PASSWORD_BCRYPT, ['cost' => 10]);
        $stmt = $db->prepare("REPLACE INTO `admin_users` (`id`,`username`,`password_hash`,`email`,`full_name`,`role`,`is_active`) VALUES
            (1,'admin@vortexsoftinnovations.in',:p1,'admin@vortexsoftinnovations.in','Super Admin','super_admin',1),
            (2,'Aniket@vortexsoftinnovations.in',:p2,'Aniket@vortexsoftinnovations.in','Aniket Kadam','admin',1)");
        $stmt->execute([':p1' => $p1, ':p2' => $p2]);
    } catch (Throwable $t) {}
}

// ── OTP helper ────────────────────────────────────────────────
function generate_otp(): string {
    return str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
}
function send_otp_email(string $to_email, string $to_name, string $otp): bool {
    $subject = 'Your Vortexsoft Admin OTP — ' . $otp;
    $body = "
<html><body style='font-family:Arial,sans-serif;background:#f5f5f5;padding:20px;'>
<div style='max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.1);'>
  <div style='background:linear-gradient(135deg,#1C2280,#CC2228);padding:28px;text-align:center;'>
    <h2 style='color:#fff;margin:0;font-size:22px;'>Admin Login OTP</h2>
    <p style='color:rgba(255,255,255,.8);margin:6px 0 0;font-size:13px;'>Vortexsoft Group — Secure Access</p>
  </div>
  <div style='padding:32px;text-align:center;'>
    <p style='color:#374151;font-size:15px;margin-bottom:20px;'>Hello <strong>" . htmlspecialchars($to_name) . "</strong>,</p>
    <p style='color:#374151;font-size:14px;margin-bottom:24px;'>Use the OTP below to complete your admin login. It is valid for <strong>5 minutes</strong> and can only be used once.</p>
    <div style='background:#f0f4ff;border:2px dashed #1C2280;border-radius:12px;padding:20px;display:inline-block;margin-bottom:24px;'>
      <span style='font-size:38px;font-weight:800;letter-spacing:12px;color:#1C2280;font-family:monospace;'>" . $otp . "</span>
    </div>
    <p style='color:#ef4444;font-size:13px;'>If you did not attempt to log in, please change your password immediately.</p>
  </div>
  <div style='background:#f8f9ff;padding:16px;text-align:center;'>
    <p style='color:#94a3b8;font-size:12px;margin:0;'>Vortexsoft Innovations Pvt. Ltd. | vortexsoftinnovations.com</p>
  </div>
</div>
</body></html>";
    return send_notification_email($to_email, $subject, $body, 'Vortexsoft Admin Security', 'noreply@vortexsoftinnovations.com');
}

// ── CSRF tokens ───────────────────────────────────────────────
if (empty($_SESSION['login_csrf'])) {
    $_SESSION['login_csrf'] = bin2hex(random_bytes(32));
}
if (empty($_SESSION['otp_csrf'])) {
    $_SESSION['otp_csrf'] = bin2hex(random_bytes(32));
}

$error    = '';
$success  = '';
$ip       = get_client_ip();
$step     = $_SESSION['otp_step'] ?? 1; // 1=credentials, 2=otp

// ── Handle OTP resend ─────────────────────────────────────────
if (isset($_GET['resend_otp']) && $step === 2) {
    $pending = $_SESSION['otp_pending_admin'] ?? [];
    if (!empty($pending)) {
        $new_otp  = generate_otp();
        $_SESSION['otp_code']       = $new_otp;
        $_SESSION['otp_expires_at'] = time() + 300;
        $_SESSION['otp_attempts']   = 0;
        send_otp_email($pending['email'], $pending['full_name'] ?? $pending['username'], $new_otp);
        $success = 'A new OTP has been sent to your registered email.';
    }
}

// ── Handle Cancel (go back to step 1) ────────────────────────
if (isset($_GET['cancel_otp'])) {
    unset($_SESSION['otp_step'], $_SESSION['otp_code'], $_SESSION['otp_expires_at'],
          $_SESSION['otp_pending_admin'], $_SESSION['otp_attempts']);
    $step = 1;
}

// ── POST processing ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ── STEP 2: Verify OTP ─────────────────────────────────
    if (isset($_POST['otp_submit'])) {
        if (!hash_equals($_SESSION['otp_csrf'] ?? '', $_POST['otp_csrf'] ?? '')) {
            $error = 'Security validation failed. Please refresh and try again.';
        } elseif ($step !== 2) {
            $error = 'Invalid request. Please start again.';
        } elseif (($_SESSION['otp_expires_at'] ?? 0) < time()) {
            // OTP expired — reset
            unset($_SESSION['otp_step'], $_SESSION['otp_code'], $_SESSION['otp_expires_at'],
                  $_SESSION['otp_pending_admin'], $_SESSION['otp_attempts']);
            $step  = 1;
            $error = 'Your OTP has expired. Please log in again.';
        } else {
            $entered_otp = trim($_POST['otp'] ?? '');
            $attempts    = $_SESSION['otp_attempts'] = ($_SESSION['otp_attempts'] ?? 0) + 1;

            if ($attempts > 5) {
                unset($_SESSION['otp_step'], $_SESSION['otp_code'], $_SESSION['otp_expires_at'],
                      $_SESSION['otp_pending_admin'], $_SESSION['otp_attempts']);
                $step  = 1;
                $error = 'Too many incorrect OTP attempts. Please log in again.';
            } elseif (hash_equals($_SESSION['otp_code'] ?? '', $entered_otp)) {
                // ✅ OTP correct — grant access
                $admin = $_SESSION['otp_pending_admin'];
                unset($_SESSION['otp_step'], $_SESSION['otp_code'], $_SESSION['otp_expires_at'],
                      $_SESSION['otp_pending_admin'], $_SESSION['otp_attempts']);

                reset_failed_attempts($ip);
                session_regenerate_id(true);

                $_SESSION[ADMIN_SESSION]       = true;
                $_SESSION[ADMIN_USER_KEY]      = $admin['id'];
                $_SESSION['admin_id']          = $admin['id'];
                $_SESSION['admin_username']    = $admin['username'];
                $_SESSION['vortex_admin_id']   = $admin['id'];
                $_SESSION['vortex_admin_user'] = $admin['username'];
                $_SESSION['admin_name']        = $admin['full_name'] ?? $admin['username'];
                $_SESSION['admin_role']        = $admin['role'];
                $_SESSION['vortex_admin_role'] = $admin['role'];
                $_SESSION['admin_login_time']  = time();
                $_SESSION['admin_login_ip']    = $ip;

                $db = getDB();
                if ($db) {
                    try {
                        $db->prepare("UPDATE admin_users SET last_login=NOW(), login_count=login_count+1 WHERE id=:id")
                           ->execute([':id' => $admin['id']]);
                    } catch (Throwable $e3) {}
                }
                log_admin_activity('Admin Login', "User '{$admin['username']}' logged in successfully (OTP verified).");
                header('Location: /admin/dashboard.php');
                exit;
            } else {
                $error = 'Incorrect OTP. ' . (5 - $attempts) . ' attempt(s) remaining.';
            }
        }

    // ── STEP 1: Verify credentials ─────────────────────────
    } else {
        $csrf_ok = hash_equals($_SESSION['login_csrf'] ?? '', $_POST['login_csrf'] ?? '');
        if (!$csrf_ok) {
            $error = 'Security validation failed. Please refresh the page and try again.';
        } elseif (is_locked_out($ip)) {
            $error = 'Too many failed attempts. This IP is locked out for 15 minutes. Please try again later.';
            error_log("Admin login lockout for IP: $ip");
        } else {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            if (empty($username) || empty($password)) {
                $error = 'Please enter your username/email and password.';
            } else {
                $db = getDB();
                if ($db) {
                    try {
                        $stmt = $db->prepare("SELECT * FROM admin_users WHERE (LOWER(username)=LOWER(:u) OR LOWER(email)=LOWER(:e)) AND is_active=1 LIMIT 1");
                        $stmt->execute([':u' => $username, ':e' => $username]);
                        $admin = $stmt->fetch();
                    } catch (Throwable $e) {
                        auto_install_tables($db);
                        try {
                            $stmt = $db->prepare("SELECT * FROM admin_users WHERE (LOWER(username)=LOWER(:u) OR LOWER(email)=LOWER(:e)) AND is_active=1 LIMIT 1");
                            $stmt->execute([':u' => $username, ':e' => $username]);
                            $admin = $stmt->fetch();
                        } catch (Throwable $e2) { $admin = false; }
                    }

                    if ($admin && password_verify($password, $admin['password_hash'])) {
                        // Credentials correct — generate & send OTP
                        $otp = generate_otp();
                        $_SESSION['otp_step']         = 2;
                        $_SESSION['otp_code']         = $otp;
                        $_SESSION['otp_expires_at']   = time() + 300; // 5 minutes
                        $_SESSION['otp_attempts']     = 0;
                        $_SESSION['otp_pending_admin'] = [
                            'id'        => $admin['id'],
                            'username'  => $admin['username'],
                            'email'     => $admin['email'],
                            'full_name' => $admin['full_name'],
                            'role'      => $admin['role'],
                            'password_hash' => $admin['password_hash'],
                        ];
                        // Regenerate CSRF for OTP step
                        $_SESSION['otp_csrf'] = bin2hex(random_bytes(32));

                        send_otp_email($admin['email'], $admin['full_name'] ?? $admin['username'], $otp);

                        $step    = 2;
                        $success = 'OTP sent to your registered email. Enter it below to continue.';
                    } else {
                        record_failed_attempt($ip);
                        log_admin_activity('Failed Login', "Failed login attempt for: '{$username}'.");
                        $error = 'Invalid credentials or inactive account.';
                        error_log("Failed admin login attempt for user: $username from IP: $ip");
                        $_SESSION['login_csrf'] = bin2hex(random_bytes(32));
                    }
                } else {
                    $error = 'Database unavailable. Please try again later.';
                }
            }
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
.btn-link-style{background:none;border:none;color:#1C2280;font-size:13px;font-weight:600;cursor:pointer;padding:0;text-decoration:none;}
.btn-link-style:hover{text-decoration:underline;}
.alert-error{background:#fff5f5;border:1px solid rgba(204,34,40,.2);color:#CC2228;border-radius:10px;padding:12px 16px;font-size:14px;margin-bottom:20px;display:flex;align-items:center;gap:10px}
.alert-success{background:#f0fdf4;border:1px solid rgba(16,185,129,.25);color:#065f46;border-radius:10px;padding:12px 16px;font-size:14px;margin-bottom:20px;display:flex;align-items:center;gap:10px}
.back-link{text-align:center;margin-top:20px;font-size:13px;color:#64748b}
.back-link a{color:#1C2280;font-weight:600;text-decoration:none}
.secured-badge{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:24px;font-size:12px;color:#94a3b8;font-weight:500}
.secured-badge i{color:#10b981}
/* OTP Styles */
.otp-step-header{text-align:center;margin-bottom:24px}
.otp-step-header .otp-icon{width:64px;height:64px;background:linear-gradient(135deg,#1C2280,#2d35c4);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:26px;color:#fff}
.otp-step-header h5{font-family:'Poppins',sans-serif;font-weight:700;color:#1C2280;font-size:16px;margin:0 0 6px}
.otp-step-header p{font-size:13px;color:#64748b;margin:0}
.otp-input-wrap{display:flex;gap:10px;justify-content:center;margin:20px 0}
.otp-digit{width:52px;height:58px;text-align:center;font-size:24px;font-weight:700;font-family:monospace;border:2px solid #e2e8f0;border-radius:10px;background:#fff;color:#1C2280;transition:.3s;-moz-appearance:textfield}
.otp-digit:focus{border-color:#1C2280;box-shadow:0 0 0 3px rgba(28,34,128,.1);outline:none}
.otp-digit::-webkit-outer-spin-button,.otp-digit::-webkit-inner-spin-button{-webkit-appearance:none}
.otp-timer{text-align:center;font-size:13px;color:#64748b;margin-bottom:16px}
.otp-timer span{color:#CC2228;font-weight:700}
.otp-actions{display:flex;justify-content:center;gap:16px;margin-top:12px;font-size:13px}
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
  <?php if ($success): ?>
  <div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <?php if ($step === 1): ?>
  <!-- ── STEP 1: Credentials ── -->
  <form method="POST" action="" id="loginForm" autocomplete="off">
    <input type="hidden" name="login_csrf" value="<?= htmlspecialchars($_SESSION['login_csrf'] ?? '') ?>">
    <div class="mb-4">
      <label class="form-label" for="username">Username or Email</label>
      <div class="input-group-icon">
        <i class="fas fa-user icon"></i>
        <input type="text" class="form-control" id="username" name="username"
               placeholder="Enter your admin email" autocomplete="off" required
               value="">
      </div>
    </div>
    <div class="mb-4">
      <label class="form-label" for="password">Password</label>
      <div class="input-group-icon">
        <i class="fas fa-lock icon"></i>
        <input type="password" class="form-control" id="password" name="password"
               placeholder="••••••••" autocomplete="new-password" required>
      </div>
    </div>
    <button type="submit" class="btn-login" id="loginBtn">
      <i class="fas fa-shield-alt"></i> Continue to OTP Verification
    </button>
  </form>

  <?php else: // $step === 2 — OTP screen ?>
  <!-- ── STEP 2: OTP Entry ── -->
  <div class="otp-step-header">
    <div class="otp-icon"><i class="fas fa-mobile-alt"></i></div>
    <h5>Two-Factor Verification</h5>
    <p>A 6-digit OTP has been sent to your registered email address.<br>It expires in 5 minutes.</p>
  </div>
  <form method="POST" action="" id="otpForm" autocomplete="off">
    <input type="hidden" name="otp_csrf"   value="<?= htmlspecialchars($_SESSION['otp_csrf'] ?? '') ?>">
    <input type="hidden" name="otp_submit" value="1">
    <input type="hidden" name="otp" id="otpHidden">
    <label class="form-label" style="text-align:center;display:block;margin-bottom:4px;">Enter 6-Digit OTP</label>
    <div class="otp-input-wrap" id="otpBoxes">
      <input type="number" class="otp-digit" maxlength="1" min="0" max="9" id="d1" inputmode="numeric" autofocus>
      <input type="number" class="otp-digit" maxlength="1" min="0" max="9" id="d2" inputmode="numeric">
      <input type="number" class="otp-digit" maxlength="1" min="0" max="9" id="d3" inputmode="numeric">
      <input type="number" class="otp-digit" maxlength="1" min="0" max="9" id="d4" inputmode="numeric">
      <input type="number" class="otp-digit" maxlength="1" min="0" max="9" id="d5" inputmode="numeric">
      <input type="number" class="otp-digit" maxlength="1" min="0" max="9" id="d6" inputmode="numeric">
    </div>
    <div class="otp-timer">OTP expires in: <span id="timerDisplay">5:00</span></div>
    <button type="submit" class="btn-login" id="otpBtn">
      <i class="fas fa-check-circle"></i> Verify & Sign In
    </button>
    <div class="otp-actions">
      <a href="?resend_otp=1" class="btn-link-style"><i class="fas fa-redo-alt me-1"></i>Resend OTP</a>
      <span style="color:#cbd5e1;">|</span>
      <a href="?cancel_otp=1" class="btn-link-style" style="color:#64748b;"><i class="fas fa-times me-1"></i>Cancel</a>
    </div>
  </form>
  <?php endif; ?>

  <div class="back-link"><a href="/index.php"><i class="fas fa-arrow-left me-1"></i>Back to Website</a></div>
  <div class="secured-badge"><i class="fas fa-shield-alt"></i> Secure Admin Area — Authorized Personnel Only</div>
</div>

<script src="/assets/vendor/bootstrap.bundle.min.js"></script>
<script>
// ── Step 1: Spinner ──
var lf = document.getElementById('loginForm');
if (lf) {
  lf.addEventListener('submit', function() {
    var btn = document.getElementById('loginBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending OTP...';
    btn.disabled = true;
  });
}

// ── Step 2: OTP digit boxes ──
var digits = ['d1','d2','d3','d4','d5','d6'];
digits.forEach(function(id, i) {
  var el = document.getElementById(id);
  if (!el) return;
  el.addEventListener('input', function() {
    var val = this.value.replace(/\D/g,'').slice(-1);
    this.value = val;
    if (val && i < digits.length - 1) document.getElementById(digits[i+1]).focus();
    updateHidden();
  });
  el.addEventListener('keydown', function(e) {
    if (e.key === 'Backspace' && !this.value && i > 0) document.getElementById(digits[i-1]).focus();
    if (e.key === 'Enter') { updateHidden(); document.getElementById('otpForm') && document.getElementById('otpForm').requestSubmit(); }
  });
  el.addEventListener('paste', function(e) {
    e.preventDefault();
    var pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
    pasted.split('').forEach(function(ch, j) {
      var d = document.getElementById(digits[j]);
      if (d) d.value = ch;
    });
    updateHidden();
    if (pasted.length >= 6 && document.getElementById('otpForm')) {
      setTimeout(function() { document.getElementById('otpForm').requestSubmit(); }, 100);
    }
  });
});
function updateHidden() {
  var val = digits.map(function(id) { var el = document.getElementById(id); return el ? el.value : ''; }).join('');
  var h = document.getElementById('otpHidden');
  if (h) h.value = val;
}

// OTP form submit
var of = document.getElementById('otpForm');
if (of) {
  of.addEventListener('submit', function(e) {
    updateHidden();
    var otp = document.getElementById('otpHidden').value;
    if (otp.length < 6) { e.preventDefault(); alert('Please enter the full 6-digit OTP.'); return; }
    var btn = document.getElementById('otpBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
    btn.disabled = true;
  });
}

// ── Countdown timer (5 min) ──
<?php if ($step === 2): ?>
var expiresAt = <?= ($_SESSION['otp_expires_at'] ?? (time()+300)) * 1000 ?>;
function updateTimer() {
  var now = Date.now();
  var remaining = Math.max(0, Math.floor((expiresAt - now) / 1000));
  var m = Math.floor(remaining / 60);
  var s = remaining % 60;
  var el = document.getElementById('timerDisplay');
  if (el) el.textContent = m + ':' + (s < 10 ? '0' : '') + s;
  if (remaining <= 30 && el) el.style.color = '#CC2228';
  if (remaining > 0) setTimeout(updateTimer, 1000);
  else if (el) el.parentElement.innerHTML = '<span style="color:#CC2228;font-weight:700;">OTP expired. Please <a href="?cancel_otp=1">log in again</a>.</span>';
}
updateTimer();
<?php endif; ?>
</script>
</body>
</html>
