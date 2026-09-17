<?php
/**
 * Vortexsoft Innovations — Admin Settings & Security
 * /admin/settings.php
 */

session_start();
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
admin_check();

$db = getDB();
$admin_id = $_SESSION[ADMIN_USER_KEY] ?? 1;

$error   = '';
$success = '';

if ($db && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Update Profile
    if ($action === 'update_profile') {
        $full_name = sanitize($_POST['full_name'] ?? '');
        $email     = sanitize_email($_POST['email'] ?? '');

        if (empty($full_name)) {
            $error = 'Please enter your full name.';
        } elseif (!is_valid_email($email)) {
            $error = 'Please enter a valid email address.';
        } else {
            try {
                $stmt = $db->prepare("UPDATE admin_users SET full_name = :n, email = :e WHERE id = :id");
                $stmt->execute([':n' => $full_name, ':e' => $email, ':id' => $admin_id]);
                $_SESSION['admin_name'] = $full_name;
                $success = 'Profile details updated successfully.';
            } catch (PDOException $e) {
                $error = 'Failed to update profile: ' . $e->getMessage();
            }
        }
    }

    // Change Password
    if ($action === 'change_password') {
        $current_pass = $_POST['current_password'] ?? '';
        $new_pass     = $_POST['new_password'] ?? '';
        $confirm_pass = $_POST['confirm_password'] ?? '';

        if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
            $error = 'Please fill in all password fields.';
        } elseif ($new_pass !== $confirm_pass) {
            $error = 'New password and confirmation password do not match.';
        } elseif (strlen($new_pass) < 6) {
            $error = 'New password must be at least 6 characters long.';
        } else {
            try {
                $stmt = $db->prepare("SELECT password_hash FROM admin_users WHERE id = :id");
                $stmt->execute([':id' => $admin_id]);
                $user = $stmt->fetch();

                if ($user && password_verify($current_pass, $user['password_hash'])) {
                    $new_hash = password_hash($new_pass, PASSWORD_BCRYPT, ['cost' => 10]);
                    $stmt = $db->prepare("UPDATE admin_users SET password_hash = :p WHERE id = :id");
                    $stmt->execute([':p' => $new_hash, ':id' => $admin_id]);
                    $success = 'Password changed successfully.';
                } else {
                    $error = 'Current password is incorrect.';
                }
            } catch (PDOException $e) {
                $error = 'Failed to update password: ' . $e->getMessage();
            }
        }
    }

    // Update Global Site Settings
    if ($action === 'update_settings') {
        $site_name     = sanitize($_POST['site_name'] ?? '');
        $contact_email = sanitize_email($_POST['contact_email'] ?? '');
        $careers_email = sanitize_email($_POST['careers_email'] ?? '');
        $contact_phone = sanitize($_POST['contact_phone'] ?? '');
        $address       = sanitize($_POST['office_address'] ?? '');

        try {
            $db->exec("CREATE TABLE IF NOT EXISTS `system_settings` (`setting_key` VARCHAR(100) PRIMARY KEY, `setting_value` TEXT, `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            
            $settings = [
                'site_name'      => $site_name,
                'contact_email'  => $contact_email,
                'careers_email'  => $careers_email,
                'contact_phone'  => $contact_phone,
                'office_address' => $address,
            ];
            $stmt = $db->prepare("REPLACE INTO system_settings (setting_key, setting_value) VALUES (:k, :v)");
            foreach ($settings as $k => $v) {
                $stmt->execute([':k' => $k, ':v' => $v]);
            }
            $success = 'Global website settings updated successfully.';
        } catch (PDOException $e) {
            $error = 'Failed to save website settings: ' . $e->getMessage();
        }
    }

    // Update AI Blog & Image Generator Settings
    if ($action === 'update_ai_settings') {
        $gemini_api_key   = trim($_POST['gemini_api_key'] ?? '');
        $gemini_model     = trim($_POST['gemini_model'] ?? 'gemini-2.0-flash-exp');
        $groq_api_key     = trim($_POST['groq_api_key'] ?? '');
        $groq_model       = trim($_POST['groq_model'] ?? 'llama-3.3-70b-versatile');
        $openrouter_api_k = trim($_POST['openrouter_api_key'] ?? '');

        try {
            $db->exec("CREATE TABLE IF NOT EXISTS `system_settings` (`setting_key` VARCHAR(100) PRIMARY KEY, `setting_value` TEXT, `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            
            $ai_settings = [
                'gemini_api_key'     => $gemini_api_key,
                'gemini_model'       => $gemini_model,
                'groq_api_key'       => $groq_api_key,
                'groq_model'         => $groq_model,
                'openrouter_api_key' => $openrouter_api_k,
            ];
            $stmt = $db->prepare("REPLACE INTO system_settings (setting_key, setting_value) VALUES (:k, :v)");
            foreach ($ai_settings as $k => $v) {
                $stmt->execute([':k' => $k, ':v' => $v]);
            }
            $success = 'AI API configuration updated successfully.';
        } catch (PDOException $e) {
            $error = 'Failed to save AI settings: ' . $e->getMessage();
        }
    }
}

// Fetch current user details
$user_info = ['username' => 'admin', 'full_name' => 'Admin User', 'email' => 'careers@vortexsoftinnovations.in', 'role' => 'super_admin'];
if ($db) {
    try {
        $stmt = $db->prepare("SELECT username, full_name, email, role, last_login, created_at FROM admin_users WHERE id = :id");
        $stmt->execute([':id' => $admin_id]);
        $row = $stmt->fetch();
        if ($row) $user_info = $row;
    } catch (PDOException $e) {}
}

// Fetch global site settings
$site_settings = [
    'site_name'          => 'Vortexsoft Innovations Pvt. Ltd.',
    'contact_email'      => 'info@vortexsoftinnovations.in',
    'careers_email'      => 'careers@vortexsoftinnovations.in',
    'contact_phone'      => '+91 8308906690',
    'office_address'     => '125 Ranganath Complex, Madiwala, Bengaluru, Karnataka 560068',
    'gemini_api_key'     => '',  // Set your key at https://aistudio.google.com/apikey
    'gemini_model'       => defined('DEFAULT_GEMINI_MODEL') ? DEFAULT_GEMINI_MODEL : 'gemini-2.0-flash-exp',
    'groq_api_key'       => defined('DEFAULT_GROQ_API_KEY') ? DEFAULT_GROQ_API_KEY : '',
    'groq_model'         => defined('DEFAULT_GROQ_MODEL') ? DEFAULT_GROQ_MODEL : 'llama-3.3-70b-versatile',
    'openrouter_api_key' => '',
];
if ($db) {
    try {
        $rows = $db->query("SELECT setting_key, setting_value FROM system_settings")->fetchAll();
        foreach ($rows as $r) {
            $site_settings[$r['setting_key']] = $r['setting_value'];
        }
    } catch (PDOException $e) {}
}

// System stats
$db_connected     = ($db !== null);
$uploads_writable = is_writable(UPLOADS_PATH . '/resumes/') || is_writable(UPLOADS_PATH);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings — Vortexsoft Admin Panel</title>
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="/assets/vendor/bootstrap.min.css">
<link rel="stylesheet" href="/assets/vendor/fontawesome/all.min.css">
<link rel="stylesheet" href="/assets/vendor/fonts.css">
<link rel="icon" type="image/x-icon" href="/favicon.ico?v=20260912">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=20260912">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=20260912">
<link rel="icon" type="image/jpeg" sizes="1024x1024" href="/icon.jpg?v=20260912">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--dark:#080B1A;--primary:#1C2280;--accent:#CC2228;--sidebar-w:260px}
body{font-family:'Inter',sans-serif;background:#f0f2ff;color:#1e293b;min-height:100vh;display:flex}
.admin-sidebar{width:var(--sidebar-w);background:var(--dark);min-height:100vh;position:fixed;top:0;left:0;z-index:1000;display:flex;flex-direction:column;transition:.3s}
.sidebar-logo{padding:24px 20px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;justify-content:space-between}
.sidebar-logo img{height:44px;object-fit:contain}
.sidebar-logo .sub{font-size:11px;color:rgba(255,255,255,.4);letter-spacing:1px;text-transform:uppercase;margin-top:6px}
.sidebar-nav{flex:1;padding:16px 0;overflow-y:auto}
.nav-section{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:rgba(255,255,255,.3);padding:12px 20px 6px}
.sidebar-link{display:flex;align-items:center;gap:12px;padding:11px 20px;color:rgba(255,255,255,.6);font-size:13.5px;font-weight:500;text-decoration:none;transition:.2s;position:relative}
.sidebar-link:hover,.sidebar-link.active{color:#fff;background:rgba(255,255,255,.07)}
.sidebar-link.active::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--accent);border-radius:0 3px 3px 0}
.sidebar-link .icon{width:20px;text-align:center;font-size:14px;color:rgba(255,255,255,.4)}
.sidebar-link:hover .icon,.sidebar-link.active .icon{color:var(--accent)}
.sidebar-footer{padding:16px 20px;border-top:1px solid rgba(255,255,255,.06)}
.btn-logout{background:rgba(204,34,40,.15);border:1px solid rgba(204,34,40,.3);color:#CC2228;width:100%;padding:9px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-align:center;text-decoration:none;display:block;transition:.2s}
.btn-logout:hover{background:#CC2228;color:#fff}
.admin-main{margin-left:var(--sidebar-w);flex:1;padding:28px;transition:.3s}
.mobile-header{display:none;background:var(--dark);padding:14px 20px;align-items:center;justify-content:space-between;color:#fff}
.card-box{background:#fff;border-radius:16px;border:1px solid #e8ecff;padding:28px;margin-bottom:24px}
.card-box h5{font-family:'Poppins',sans-serif;font-weight:700;font-size:16px;color:#1e293b;margin-bottom:16px;display:flex;align-items:center;gap:10px}
@media(max-width:1024px){
  body{flex-direction:column}
  .admin-sidebar{transform:translateX(-100%)}
  .admin-sidebar.show{transform:translateX(0)}
  .admin-main{margin-left:0;padding:20px}
  .mobile-header{display:flex}
}
</style>
</head>
<body>

<div class="mobile-header">
  <img src="/logo-header.png" alt="Vortexsoft" style="height:32px;">
  <button class="btn text-white p-0" id="sidebarToggleBtn" style="font-size:20px;"><i class="fas fa-bars"></i></button>
</div>

<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-logo">
    <div>
      <img src="/logo-header.png?v=20260912" alt="Vortexsoft Innovations">
      <div class="sub">Admin Panel</div>
    </div>
    <button class="btn text-white p-0 d-lg-none" id="sidebarCloseBtn"><i class="fas fa-times"></i></button>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">Main</div>
    <a href="dashboard.php" class="sidebar-link"><span class="icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard</a>
    <a href="contacts.php" class="sidebar-link"><span class="icon"><i class="fas fa-envelope"></i></span> Inquiries</a>
    <a href="applications.php" class="sidebar-link"><span class="icon"><i class="fas fa-briefcase"></i></span> Applications</a>
    <div class="nav-section">Content</div>
    <a href="blog-posts.php" class="sidebar-link"><span class="icon"><i class="fas fa-pen-alt"></i></span> Blog Posts</a>
    <a href="blog/generate.php" class="sidebar-link"><span class="icon"><i class="fas fa-robot"></i></span> AI Blog Generator</a>
    <a href="newsletter.php" class="sidebar-link"><span class="icon"><i class="fas fa-paper-plane"></i></span> Newsletter</a>
    <div class="nav-section">System</div>
    <a href="settings.php" class="sidebar-link active"><span class="icon"><i class="fas fa-cog"></i></span> Settings</a>
    <a href="/index.php" target="_blank" class="sidebar-link"><span class="icon"><i class="fas fa-external-link-alt"></i></span> View Website</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt me-2"></i> Sign Out</a>
  </div>
</aside>

<main class="admin-main">
  <div class="mb-4">
    <h1><i class="fas fa-cog me-2" style="color:#CC2228;"></i> Admin Settings</h1>
    <div style="font-size:13px;color:#64748b;">Manage profile details, security credentials, global website settings, AI engine configuration, and system diagnostics.</div>
  </div>

  <?php if ($error): ?>
  <div class="alert alert-danger mb-4" style="border-radius:12px;"><i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
  <div class="alert alert-success mb-4" style="border-radius:12px;"><i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <div class="row">
    <!-- Profile & Password -->
    <div class="col-lg-7">
      <!-- Edit Profile -->
      <div class="card-box">
        <h5><i class="fas fa-user-edit text-primary"></i> Edit Admin Profile</h5>
        <form method="POST" action="settings.php">
          <input type="hidden" name="action" value="update_profile">
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Username / Email</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user_info['username']) ?>" disabled readonly style="background:#f8fafc;">
          </div>
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user_info['full_name'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Contact Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_info['email'] ?? '') ?>" required>
          </div>
          <button type="submit" class="btn" style="background:#1C2280;color:#fff;border-radius:8px;font-weight:700;padding:10px 24px;">Save Profile</button>
        </form>
      </div>

      <!-- AI Blog & Image Generator Settings -->
      <div class="card-box">
        <h5><i class="fas fa-robot" style="color:#6366f1;"></i> AI Blog &amp; Image Generator Settings</h5>
        <div style="font-size:12.5px;color:#64748b;margin-bottom:15px;">
          Configure API credentials used by the AI Blog &amp; Image Generator (<a href="blog/generate.php" style="color:#1C2280;font-weight:600;">Open Generator</a>). Values are securely saved to the database.
        </div>
        <form method="POST" action="settings.php">
          <input type="hidden" name="action" value="update_ai_settings">
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Google Gemini API Key <span class="badge bg-primary ms-1">Default Engine</span></label>
            <input type="text" name="gemini_api_key" class="form-control font-monospace" style="font-size:13px;" value="<?= htmlspecialchars($site_settings['gemini_api_key'] ?? '') ?>" placeholder="AIza... or Gemini API Key">
            <div class="form-text" style="font-size:11.5px;">Powers automatic SEO, AEO &amp; GEO articles with Google Gemini 3.6 Flash.</div>
          </div>
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Gemini Model</label>
            <input type="text" name="gemini_model" class="form-control font-monospace" style="font-size:13px;" value="<?= htmlspecialchars($site_settings['gemini_model'] ?? 'gemini-3.6-flash') ?>" placeholder="gemini-3.6-flash">
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Groq API Key <span class="badge bg-secondary ms-1">Fallback</span></label>
              <input type="password" name="groq_api_key" class="form-control font-monospace" style="font-size:13px;" value="<?= htmlspecialchars($site_settings['groq_api_key'] ?? '') ?>" placeholder="gsk_...">
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Groq Model</label>
              <input type="text" name="groq_model" class="form-control font-monospace" style="font-size:13px;" value="<?= htmlspecialchars($site_settings['groq_model'] ?? 'llama-3.3-70b-versatile') ?>" placeholder="llama-3.3-70b-versatile">
            </div>
          </div>
          <button type="submit" class="btn" style="background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;border-radius:8px;font-weight:700;padding:10px 24px;">Save AI Configuration</button>
        </form>
      </div>

      <!-- Global Website Settings -->
      <div class="card-box">
        <h5><i class="fas fa-sliders-h text-success"></i> Global Website Settings</h5>
        <form method="POST" action="settings.php">
          <input type="hidden" name="action" value="update_settings">
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Company / Site Name</label>
            <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($site_settings['site_name'] ?? '') ?>" required>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">General Contact Email</label>
              <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($site_settings['contact_email'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">HR / Careers Email</label>
              <input type="email" name="careers_email" class="form-control" value="<?= htmlspecialchars($site_settings['careers_email'] ?? '') ?>" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Contact Phone Number</label>
            <input type="text" name="contact_phone" class="form-control" value="<?= htmlspecialchars($site_settings['contact_phone'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label font-weight-semibold">HQ Office Address</label>
            <textarea name="office_address" class="form-control" rows="2" required><?= htmlspecialchars($site_settings['office_address'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn" style="background:#10b981;color:#fff;border-radius:8px;font-weight:700;padding:10px 24px;">Save Website Settings</button>
        </form>
      </div>

      <!-- AI API Keys Configuration -->
      <div class="card-box" id="ai-keys-section">
        <h5><i class="fas fa-robot" style="color:#1C2280;"></i> AI Blog Generator — API Keys</h5>
        <div class="alert alert-warning d-flex gap-2 align-items-start mb-3" style="background:#fff8e1;border:1px solid #f59e0b;border-radius:10px;padding:14px 16px;">
          <i class="fas fa-triangle-exclamation mt-1" style="color:#f59e0b;flex-shrink:0;"></i>
          <div style="font-size:13px;">
            <strong>Gemini API key required.</strong> Get a <strong>FREE</strong> key at
            <a href="https://aistudio.google.com/apikey" target="_blank" rel="noopener" style="color:#1C2280;font-weight:700;">aistudio.google.com/apikey</a> →
            click <strong>Create API Key</strong> → paste it below.<br>
            Keys must start with <code>AIza</code>. <em>Vertex AI keys (starting with <code>AQ.</code>) are NOT compatible.</em>
          </div>
        </div>
        <form method="POST" action="settings.php">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="update_ai_settings">
          <!-- Gemini -->
          <div style="background:#f8faff;border:1px solid #e2e8f0;border-radius:10px;padding:18px;margin-bottom:16px;">
            <div class="d-flex align-items-center gap-2 mb-3">
              <span style="background:#1C2280;color:#fff;border-radius:8px;padding:4px 10px;font-size:11px;font-weight:700;letter-spacing:.5px;">GEMINI</span>
              <span style="font-size:12px;color:#64748b;">Google AI Studio — Default engine used by AI Blog Generator</span>
            </div>
            <div class="mb-3">
              <label class="form-label font-weight-semibold">Gemini API Key <span style="color:#CC2228;">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-key"></i></span>
                <input type="password" name="gemini_api_key" class="form-control" 
                  value="<?= htmlspecialchars($site_settings['gemini_api_key'] ?? '') ?>"
                  placeholder="AIzaSy... (get free key at aistudio.google.com/apikey)"
                  autocomplete="new-password" id="gemini-key-input">
                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('gemini-key-input',this)" title="Show/hide key">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
              <div class="form-text">Must start with <code>AIza</code>. Free tier supports ~1500 requests/day.</div>
            </div>
            <div class="mb-0">
              <label class="form-label font-weight-semibold">Gemini Model</label>
              <select name="gemini_model" class="form-select">
                <option value="gemini-2.0-flash-exp" <?= ($site_settings['gemini_model'] ?? '') === 'gemini-2.0-flash-exp' ? 'selected' : '' ?>>gemini-2.0-flash-exp (Recommended — Fast & Free)</option>
                <option value="gemini-1.5-flash" <?= ($site_settings['gemini_model'] ?? '') === 'gemini-1.5-flash' ? 'selected' : '' ?>>gemini-1.5-flash (Stable)</option>
                <option value="gemini-1.5-pro" <?= ($site_settings['gemini_model'] ?? '') === 'gemini-1.5-pro' ? 'selected' : '' ?>>gemini-1.5-pro (Higher quality, slower)</option>
                <option value="gemini-2.0-flash-thinking-exp" <?= ($site_settings['gemini_model'] ?? '') === 'gemini-2.0-flash-thinking-exp' ? 'selected' : '' ?>>gemini-2.0-flash-thinking-exp (Reasoning)</option>
              </select>
            </div>
          </div>
          <!-- Groq (backup) -->
          <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:18px;margin-bottom:16px;">
            <div class="d-flex align-items-center gap-2 mb-3">
              <span style="background:#f97316;color:#fff;border-radius:8px;padding:4px 10px;font-size:11px;font-weight:700;letter-spacing:.5px;">GROQ</span>
              <span style="font-size:12px;color:#64748b;">Backup engine — Free at <a href="https://console.groq.com" target="_blank" style="color:#f97316;">console.groq.com</a></span>
            </div>
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label font-weight-semibold">Groq API Key</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-key"></i></span>
                  <input type="password" name="groq_api_key" class="form-control"
                    value="<?= htmlspecialchars($site_settings['groq_api_key'] ?? '') ?>"
                    placeholder="gsk_..." autocomplete="new-password" id="groq-key-input">
                  <button type="button" class="btn btn-outline-secondary" onclick="togglePass('groq-key-input',this)"><i class="fas fa-eye"></i></button>
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label font-weight-semibold">Model</label>
                <select name="groq_model" class="form-select">
                  <option value="llama-3.3-70b-versatile" <?= ($site_settings['groq_model'] ?? '') === 'llama-3.3-70b-versatile' ? 'selected' : '' ?>>llama-3.3-70b-versatile</option>
                  <option value="llama-3.1-8b-instant" <?= ($site_settings['groq_model'] ?? '') === 'llama-3.1-8b-instant' ? 'selected' : '' ?>>llama-3.1-8b-instant</option>
                  <option value="mixtral-8x7b-32768" <?= ($site_settings['groq_model'] ?? '') === 'mixtral-8x7b-32768' ? 'selected' : '' ?>>mixtral-8x7b-32768</option>
                </select>
              </div>
            </div>
          </div>
          <!-- OpenRouter (backup) -->
          <div style="background:#f5f3ff;border:1px solid #ddd6fe;border-radius:10px;padding:18px;margin-bottom:20px;">
            <div class="d-flex align-items-center gap-2 mb-3">
              <span style="background:#8b5cf6;color:#fff;border-radius:8px;padding:4px 10px;font-size:11px;font-weight:700;letter-spacing:.5px;">OPENROUTER</span>
              <span style="font-size:12px;color:#64748b;">Additional backup — <a href="https://openrouter.ai/keys" target="_blank" style="color:#8b5cf6;">openrouter.ai/keys</a></span>
            </div>
            <label class="form-label font-weight-semibold">OpenRouter API Key</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-key"></i></span>
              <input type="password" name="openrouter_api_key" class="form-control"
                value="<?= htmlspecialchars($site_settings['openrouter_api_key'] ?? '') ?>"
                placeholder="sk-or-v1-..." autocomplete="new-password" id="openrouter-key-input">
              <button type="button" class="btn btn-outline-secondary" onclick="togglePass('openrouter-key-input',this)"><i class="fas fa-eye"></i></button>
            </div>
          </div>
          <button type="submit" class="btn" style="background:#1C2280;color:#fff;border-radius:8px;font-weight:700;padding:11px 26px;">
            <i class="fas fa-save me-2"></i>Save AI Keys
          </button>
          <a href="/admin/blog/generate.php" class="btn btn-outline-secondary ms-2" style="border-radius:8px;padding:11px 20px;">
            <i class="fas fa-robot me-1"></i>Test AI Generator
          </a>
        </form>
      </div>

      <!-- Change Password -->
      <div class="card-box">
        <h5><i class="fas fa-key text-danger"></i> Change Password</h5>
        <form method="POST" action="settings.php">
          <input type="hidden" name="action" value="change_password">
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Current Password</label>
            <input type="password" name="current_password" class="form-control" required placeholder="••••••••">
          </div>
          <div class="mb-3">
            <label class="form-label font-weight-semibold">New Password</label>
            <input type="password" name="new_password" class="form-control" required placeholder="At least 6 characters">
          </div>
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" required placeholder="Repeat new password">
          </div>
          <button type="submit" class="btn" style="background:#CC2228;color:#fff;border-radius:8px;font-weight:700;padding:10px 24px;">Update Password</button>
        </form>
      </div>
    </div>

    <!-- System Diagnostics Sidebar -->
    <div class="col-lg-5">
      <div class="card-box">
        <h5><i class="fas fa-server text-info"></i> System &amp; Health Diagnostics</h5>
        <div style="font-size:13.5px;">
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-secondary">MySQL Status:</span>
            <span><?= $db_connected ? '<span class="badge bg-success">Connected</span>' : '<span class="badge bg-danger">Disconnected</span>' ?></span>
          </div>
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-secondary">Database Name:</span>
            <span class="font-weight-bold"><code><?= DB_NAME ?></code></span>
          </div>
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-secondary">PHP Version:</span>
            <span class="font-weight-bold"><?= PHP_VERSION ?></span>
          </div>
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-secondary">Gemini AI Engine:</span>
            <span><?= !empty($site_settings['gemini_api_key']) ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Ready (' . htmlspecialchars($site_settings['gemini_model'] ?? 'gemini-3.6-flash') . ')</span>' : '<span class="badge bg-danger">Not Set</span>' ?></span>
          </div>
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-secondary">AI Image Engine:</span>
            <span><span class="badge bg-info"><i class="fas fa-image me-1"></i>Active (Flux 1200x630)</span></span>
          </div>
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-secondary">Resume Directory:</span>
            <span><?= $uploads_writable ? '<span class="badge bg-success">Writable</span>' : '<span class="badge bg-warning">Check Permissions</span>' ?></span>
          </div>
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-secondary">Server Time:</span>
            <span class="font-weight-bold"><?= date('H:i:s T') ?></span>
          </div>
          <div class="d-flex justify-content-between py-2">
            <span class="text-secondary">Admin Role:</span>
            <span class="badge bg-primary text-capitalize"><?= str_replace('_', ' ', $user_info['role'] ?? 'admin') ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<script src="/assets/vendor/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('sidebarToggleBtn')?.addEventListener('click', function(){
  document.getElementById('adminSidebar').classList.toggle('show');
});
document.getElementById('sidebarCloseBtn')?.addEventListener('click', function(){
  document.getElementById('adminSidebar').classList.remove('show');
});
function togglePass(inputId, btn) {
  const input = document.getElementById(inputId);
  if (!input) return;
  const isPass = input.type === 'password';
  input.type = isPass ? 'text' : 'password';
  btn.querySelector('i').className = isPass ? 'fas fa-eye-slash' : 'fas fa-eye';
}
</script>
</body>
</html>
