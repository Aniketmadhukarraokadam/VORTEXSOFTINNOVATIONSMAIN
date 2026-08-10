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

// System stats
$db_connected  = ($db !== null);
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
<link rel="icon" href="/icon.jpg">
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
      <img src="/logo-header.png" alt="Vortexsoft">
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
    <div style="font-size:13px;color:#64748b;">Manage profile details, security credentials, and system diagnostics.</div>
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
        <h5><i class="fas fa-user-edit text-primary"></i> Edit Profile Information</h5>
        <form method="POST" action="settings.php">
          <input type="hidden" name="action" value="update_profile">
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Username</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user_info['username']) ?>" disabled readonly style="background:#f8fafc;">
          </div>
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user_info['full_name'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Email Address</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_info['email'] ?? '') ?>" required>
          </div>
          <button type="submit" class="btn" style="background:#1C2280;color:#fff;border-radius:8px;font-weight:700;padding:10px 24px;">Save Profile</button>
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
        <h5><i class="fas fa-server text-info"></i> System & Health Diagnostics</h5>
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
</script>
</body>
</html>
