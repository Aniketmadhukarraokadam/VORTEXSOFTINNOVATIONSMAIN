<?php
/**
 * Password Hash Generator Utility
 * /admin/generate_hash.php
 * 
 * ⚠️ DELETE THIS FILE AFTER USE
 * Usage: Open in browser, enter password, copy the hash to admin_users table
 */

// Only accessible from server (basic protection)
if (APP_ENV !== 'development' && !in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'])) {
    http_response_code(404);
    exit('Not found.');
}

define('APP_ENV', 'development');

$hash = '';
$msg  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (strlen($password) < 8) {
        $msg = 'Password must be at least 8 characters.';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $msg  = 'Hash generated successfully! Copy the SQL below and run it in Hostinger phpMyAdmin.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Password Hash Generator — Vortexsoft</title>
<meta name="robots" content="noindex, nofollow">
<style>
body{font-family:Arial,sans-serif;background:#f0f2ff;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px}
.card{background:#fff;border-radius:16px;padding:40px;max-width:560px;width:100%;box-shadow:0 8px 32px rgba(0,0,0,.08)}
h2{color:#1C2280;margin-bottom:8px}
p{color:#64748b;font-size:14px;margin-bottom:24px}
label{font-size:13px;font-weight:700;color:#374151;display:block;margin-bottom:6px}
input[type=text],input[type=password]{width:100%;padding:12px 16px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;margin-bottom:16px;font-family:Arial,sans-serif}
input[type=text]:focus,input[type=password]:focus{border-color:#1C2280;outline:none;box-shadow:0 0 0 3px rgba(28,34,128,.1)}
button{background:#1C2280;color:#fff;font-weight:700;padding:12px 28px;border:none;border-radius:10px;cursor:pointer;font-size:14px;width:100%}
button:hover{background:#2d35c4}
.msg{padding:12px 16px;border-radius:10px;font-size:14px;margin-bottom:16px}
.msg.ok{background:#f0fdf4;color:#10b981;border:1px solid rgba(16,185,129,.2)}
.msg.err{background:#fff0f0;color:#CC2228;border:1px solid rgba(204,34,40,.2)}
.hash-box{background:#1e293b;color:#10b981;padding:16px;border-radius:10px;font-family:monospace;font-size:12px;word-break:break-all;margin-bottom:16px;cursor:pointer}
.sql-box{background:#f8f9ff;border:1px solid #e8ecff;border-radius:10px;padding:16px;font-family:monospace;font-size:12px;word-break:break-all;color:#1e293b;white-space:pre-wrap;cursor:pointer}
.warning{background:#fffbeb;border:1px solid rgba(245,158,11,.2);border-radius:10px;padding:12px 16px;font-size:13px;color:#92400e;margin-top:20px}
</style>
</head>
<body>
<div class="card">
  <h2>🔐 Admin Password Setup</h2>
  <p>Enter your desired admin password to generate a secure bcrypt hash for the database. <strong>Delete this file after use!</strong></p>

  <?php if ($msg): ?>
  <div class="msg <?= $hash ? 'ok' : 'err' ?>"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <?php if ($hash): ?>
  <label>Generated Hash (click to copy):</label>
  <div class="hash-box" onclick="navigator.clipboard.writeText(this.textContent).then(()=>{this.style.background='#064e3b';setTimeout(()=>{this.style.background='#1e293b';},1000})"><?= htmlspecialchars($hash) ?></div>

  <label>SQL to run in Hostinger phpMyAdmin (click to copy):</label>
  <div class="sql-box" onclick="navigator.clipboard.writeText(this.textContent).then(()=>{this.style.background='#f0fdf4';setTimeout(()=>{this.style.background='#f8f9ff';},1000})"><?= "UPDATE admin_users SET password_hash = '" . addslashes($hash) . "' WHERE username = 'admin';" ?></div>
  <hr style="margin:20px 0;border-color:#e8ecff">
  <?php endif; ?>

  <form method="POST" action="">
    <label for="password">New Admin Password (min 8 chars):</label>
    <input type="password" id="password" name="password" placeholder="Enter strong password..." required>
    <button type="submit">Generate Secure Hash</button>
  </form>

  <div class="warning">
    <strong>⚠️ Security Notice:</strong> Delete <code>/admin/generate_hash.php</code> immediately after setting your password. Never leave this file accessible on production.
  </div>
</div>
</body>
</html>
