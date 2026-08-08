<?php
/**
 * Vortexsoft Innovations — Admin Panel: Login
 * /admin/login.php
 */

session_start();
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (!empty($_SESSION[ADMIN_SESSION])) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!check_rate_limit('admin_login_' . md5(get_client_ip()), 10, 300)) {
        $error = 'Too many login attempts. Please wait 5 minutes.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Please enter username and password.';
    } else {
        $db = getDB();
        if ($db) {
            $stmt = $db->prepare("SELECT * FROM admin_users WHERE (username = :u OR email = :u) AND is_active = 1 LIMIT 1");
            $stmt->execute([':u' => $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION[ADMIN_SESSION]  = true;
                $_SESSION[ADMIN_USER_KEY] = $admin['id'];
                $_SESSION['admin_name']   = $admin['full_name'] ?? $admin['username'];
                $_SESSION['admin_role']   = $admin['role'];

                $db->prepare("UPDATE admin_users SET last_login=NOW(), login_count=login_count+1 WHERE id=:id")
                   ->execute([':id' => $admin['id']]);

                header('Location: /admin/dashboard.php');
                exit;
            } else {
                $error = 'Invalid username or password.';
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
        <input type="text" class="form-control" id="username" name="username" placeholder="admin" autocomplete="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
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
