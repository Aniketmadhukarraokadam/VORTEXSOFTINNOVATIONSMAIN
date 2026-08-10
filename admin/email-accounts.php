<?php
/**
 * Vortexsoft Innovations — Admin Email Accounts & Connection Tester
 * Requirements #1 & #3: Mail account settings + SMTP/IMAP testing tools
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();
admin_require_role(['super_admin']);

$db = getDB();
$message = $error = '';

// Handle POST save or test actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Security token mismatch.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'save_account') {
            $id            = (int)($_POST['account_id'] ?? 0);
            $email_address = sanitize_email($_POST['email_address'] ?? '');
            $display_name  = sanitize($_POST['display_name'] ?? 'Vortexsoft Group');
            $provider      = sanitize($_POST['provider'] ?? 'Hostinger / Custom SMTP');
            $smtp_host     = sanitize($_POST['smtp_host'] ?? '');
            $smtp_port     = (int)($_POST['smtp_port'] ?? 587);
            $smtp_enc      = sanitize($_POST['smtp_encryption'] ?? 'tls');
            $smtp_user     = sanitize($_POST['smtp_username'] ?? '');
            $smtp_pass_raw = $_POST['smtp_password'] ?? '';
            $imap_host     = sanitize($_POST['imap_host'] ?? '');
            $imap_port     = (int)($_POST['imap_port'] ?? 993);
            $imap_enc      = sanitize($_POST['imap_encryption'] ?? 'ssl');
            $imap_user     = sanitize($_POST['imap_username'] ?? '');
            $imap_pass_raw = $_POST['imap_password'] ?? '';
            $is_active     = isset($_POST['is_active']) ? 1 : 0;
            $is_default    = isset($_POST['is_default']) ? 1 : 0;

            if (empty($email_address)) {
                $error = 'Email address is required.';
            } else {
                if ($is_default) {
                    $db->exec("UPDATE email_accounts SET is_default=0");
                }

                if ($id > 0) {
                    $existing = $db->prepare("SELECT smtp_password_enc, imap_password_enc FROM email_accounts WHERE id=?");
                    $existing->execute([$id]);
                    $old = $existing->fetch(PDO::FETCH_ASSOC);

                    $smtp_enc_val = !empty($smtp_pass_raw) ? encrypt_secret($smtp_pass_raw) : ($old['smtp_password_enc'] ?? '');
                    $imap_enc_val = !empty($imap_pass_raw) ? encrypt_secret($imap_pass_raw) : ($old['imap_password_enc'] ?? '');

                    $stmt = $db->prepare("UPDATE email_accounts SET email_address=?, display_name=?, provider=?, smtp_host=?, smtp_port=?, smtp_encryption=?, smtp_username=?, smtp_password_enc=?, imap_host=?, imap_port=?, imap_encryption=?, imap_username=?, imap_password_enc=?, is_active=?, is_default=? WHERE id=?");
                    $stmt->execute([$email_address, $display_name, $provider, $smtp_host, $smtp_port, $smtp_enc, $smtp_user, $smtp_enc_val, $imap_host, $imap_port, $imap_enc, $imap_user, $imap_enc_val, $is_active, $is_default, $id]);
                    log_admin_activity('Email Account Updated', "Updated email settings for: {$email_address}");
                    $message = "Email account configuration updated.";
                } else {
                    $smtp_enc_val = encrypt_secret($smtp_pass_raw);
                    $imap_enc_val = encrypt_secret($imap_pass_raw);

                    $stmt = $db->prepare("INSERT INTO email_accounts (email_address, display_name, provider, smtp_host, smtp_port, smtp_encryption, smtp_username, smtp_password_enc, imap_host, imap_port, imap_encryption, imap_username, imap_password_enc, is_active, is_default) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                    $stmt->execute([$email_address, $display_name, $provider, $smtp_host, $smtp_port, $smtp_enc, $smtp_user, $smtp_enc_val, $imap_host, $imap_port, $imap_enc, $imap_user, $imap_enc_val, $is_active, $is_default]);
                    log_admin_activity('Email Account Added', "Added new email account: {$email_address}");
                    $message = "New email account added successfully.";
                }
            }
        }

        if ($action === 'test_smtp') {
            $host = sanitize($_POST['test_smtp_host'] ?? '');
            $port = (int)($_POST['test_smtp_port'] ?? 587);
            if (empty($host)) {
                $error = 'SMTP Host is required for connection testing.';
            } else {
                $fp = @fsockopen($host, $port, $errno, $errstr, 5);
                if ($fp) {
                    fclose($fp);
                    log_admin_activity('SMTP Test Passed', "Tested connection to {$host}:{$port}");
                    $message = "<i class='fas fa-check-circle me-1'></i> SMTP Connection Successful! Server {$host}:{$port} is reachable.";
                } else {
                    log_admin_activity('SMTP Test Failed', "Failed connection to {$host}:{$port}");
                    $error = "<i class='fas fa-times-circle me-1'></i> SMTP Connection Failed: Unable to reach {$host}:{$port} (Error {$errno}: {$errstr})";
                }
            }
        }

        if ($action === 'test_imap') {
            $host = sanitize($_POST['test_imap_host'] ?? '');
            $port = (int)($_POST['test_imap_port'] ?? 993);
            if (empty($host)) {
                $error = 'IMAP Host is required for connection testing.';
            } else {
                $fp = @fsockopen(($port == 993 ? 'ssl://' : '') . $host, $port, $errno, $errstr, 5);
                if ($fp) {
                    fclose($fp);
                    log_admin_activity('IMAP Test Passed', "Tested IMAP connection to {$host}:{$port}");
                    $message = "<i class='fas fa-check-circle me-1'></i> IMAP Connection Successful! Mail server {$host}:{$port} responded.";
                } else {
                    log_admin_activity('IMAP Test Failed', "Failed IMAP connection to {$host}:{$port}");
                    $error = "<i class='fas fa-times-circle me-1'></i> IMAP Connection Failed: Unable to reach {$host}:{$port}";
                }
            }
        }

        if ($action === 'send_test_mail') {
            $target = sanitize_email($_POST['test_email_target'] ?? '');
            if (empty($target) || !is_valid_email($target)) {
                $error = 'Please enter a valid target email for testing.';
            } else {
                $subject = 'Vortexsoft Admin — Test Email Verification';
                $body = "<h2>Test Email Successful</h2><p>This is a test notification generated from the Vortexsoft Admin Panel.</p><p>Timestamp: " . date('Y-m-d H:i:s') . " IST</p>";
                $sent = send_notification_email($target, $subject, $body);
                if ($sent) {
                    log_admin_activity('Test Email Sent', "Sent test email to {$target}");
                    $message = "<i class='fas fa-check-circle me-1'></i> Test email sent successfully to <strong>{$target}</strong>!";
                } else {
                    log_admin_activity('Test Email Failed', "Failed test email to {$target}");
                    $error = "<i class='fas fa-times-circle me-1'></i> Failed to send test email to {$target}. Check server mail configuration.";
                }
            }
        }
    }
}

$accounts = $db->query("SELECT * FROM email_accounts ORDER BY is_default DESC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mail Account Settings — Vortexsoft Admin</title>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="../assets/vendor/bootstrap.min.css">
<link rel="stylesheet" href="../assets/vendor/fontawesome/all.min.css">
<style>
body{font-family:'Inter',sans-serif;background:#f0f2ff;margin:0;color:#1e293b}
.admin-sidebar{position:fixed;top:0;left:0;width:260px;height:100vh;background:linear-gradient(180deg,#080B1A 0%,#1C2280 100%);padding:0;color:#fff}
.sidebar-logo{padding:24px 20px;border-bottom:1px solid rgba(255,255,255,.08);font-size:20px;font-weight:800}.sidebar-logo span{color:#CC2228}
.sidebar-nav{padding:16px 12px}
.sidebar-nav a{display:flex;align-items:center;gap:12px;padding:10px 14px;border-radius:10px;color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px;font-weight:500;margin-bottom:2px}
.sidebar-nav a:hover,.sidebar-nav a.active{background:rgba(255,255,255,.1);color:#fff}
.sidebar-nav a i{width:18px;text-align:center;color:#CC2228}
.sidebar-section{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:rgba(255,255,255,.3);padding:14px 14px 6px}
.main-content{margin-left:260px;padding:28px;min-height:100vh}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
.topbar h1{font-size:22px;font-weight:800;color:#1C2280;margin:0}
.card{background:#fff;border-radius:16px;border:1px solid #e8ecff;overflow:hidden;margin-bottom:24px}
.card-header{padding:16px 22px;border-bottom:1px solid #f0f2ff;display:flex;justify-content:space-between;align-items:center;font-weight:700;color:#1C2280}
.btn-primary-custom{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;border:none;border-radius:10px;padding:9px 18px;font-size:13px;font-weight:700;cursor:pointer}
.provider-note{background:#f8fafc;border-left:4px solid #1C2280;padding:14px 18px;border-radius:0 10px 10px 0;font-size:13px;color:#475569;margin-bottom:24px}
</style>
</head>
<body>
<div class="admin-sidebar">
    <div class="sidebar-logo">VORTEX<span>SOFT</span></div>
    <nav class="sidebar-nav">
        <div class="sidebar-section">Main</div>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="contacts.php"><i class="fas fa-envelope"></i> Inquiries</a>
        <a href="applications.php"><i class="fas fa-briefcase"></i> Applications</a>
        <a href="jobs.php"><i class="fas fa-clipboard-list"></i> Jobs</a>
        <div class="sidebar-section">Email & Security</div>
        <a href="emails.php"><i class="fas fa-inbox"></i> Email Activity</a>
        <a href="email-accounts.php" class="active"><i class="fas fa-mail-bulk"></i> Mail Accounts</a>
        <a href="email-templates.php"><i class="fas fa-file-code"></i> Templates</a>
        <a href="activity-log.php"><i class="fas fa-history"></i> Activity Log</a>
        <a href="users.php"><i class="fas fa-users-cog"></i> Admin Users</a>
        <a href="backup_db.php"><i class="fas fa-database"></i> DB Backup</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>
<div class="main-content">
    <div class="topbar">
        <div>
            <h1><i class="fas fa-mail-bulk" style="color:#CC2228;margin-right:10px;"></i>Company Mail Accounts & Connection Tester</h1>
            <div style="font-size:13px;color:#64748b;margin-top:2px;">Configure company SMTP & IMAP mail servers securely.</div>
        </div>
        <button class="btn-primary-custom" onclick="openAddModal()"><i class="fas fa-plus me-2"></i>Configure New Mail Account</button>
    </div>

    <div class="provider-note">
        <i class="fas fa-info-circle me-1" style="color:#1C2280;"></i>
        <strong>Mailbox Notice:</strong> Creating an actual mailbox requires your email provider's hosting dashboard (e.g. Hostinger / Titan Mail / cPanel). This admin panel securely stores credentials and manages server connections. Passwords are encrypted with AES-256 and never shown in plain text.
    </div>

    <?php if($message): ?><div class="alert alert-success" style="padding:14px;border-radius:10px;margin-bottom:20px;background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;"><?= $message ?></div><?php endif; ?>
    <?php if($error): ?><div class="alert alert-danger" style="padding:14px;border-radius:10px;margin-bottom:20px;background:#fff0f0;color:#991b1b;border:1px solid #fecaca;"><?= $error ?></div><?php endif; ?>

    <!-- Mail Accounts List -->
    <div class="card">
        <div class="card-header">
            <span>Configured Mail Accounts (<?= count($accounts) ?>)</span>
            <span style="font-size:12px;color:#94a3b8;">Primary account: <strong>contact@vortexsoftinnovations.com</strong></span>
        </div>
        <?php if(empty($accounts)): ?>
        <div style="padding:40px;text-align:center;color:#94a3b8;">No mail accounts configured yet. Default server PHP mail() is currently active.</div>
        <?php else: ?>
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Email Address</th>
                    <th>Display Name</th>
                    <th>Provider</th>
                    <th>SMTP Server</th>
                    <th>IMAP Server</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($accounts as $acc): ?>
                <tr>
                    <td style="font-weight:700;color:#1C2280;"><?= htmlspecialchars($acc['email_address']) ?> <?php if($acc['is_default']): ?><span class="badge bg-primary" style="font-size:10px;">DEFAULT</span><?php endif; ?></td>
                    <td><?= htmlspecialchars($acc['display_name']) ?></td>
                    <td><?= htmlspecialchars($acc['provider']) ?></td>
                    <td style="font-size:12.5px;"><?= htmlspecialchars($acc['smtp_host'] ?: 'Default') ?>:<?= $acc['smtp_port'] ?></td>
                    <td style="font-size:12.5px;"><?= htmlspecialchars($acc['imap_host'] ?: 'Default') ?>:<?= $acc['imap_port'] ?></td>
                    <td><span class="badge bg-<?= $acc['is_active'] ? 'success' : 'secondary' ?>"><?= $acc['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick='openEditModal(<?= json_encode($acc) ?>)'><i class="fas fa-cog"></i> Edit / Test</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Quick Connection Testing Tools -->
    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="card p-4">
                <h6 style="font-weight:700;color:#1C2280;"><i class="fas fa-plug me-2" style="color:#CC2228;"></i>Test SMTP Connection</h6>
                <form method="POST" class="mt-3">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                    <input type="hidden" name="action" value="test_smtp">
                    <div class="mb-2"><input type="text" name="test_smtp_host" class="form-control form-control-sm" placeholder="smtp.hostinger.com" value="smtp.hostinger.com" required></div>
                    <div class="mb-3"><input type="number" name="test_smtp_port" class="form-control form-control-sm" value="587"></div>
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100"><i class="fas fa-network-wired me-1"></i>Test SMTP Host</button>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4">
                <h6 style="font-weight:700;color:#1C2280;"><i class="fas fa-inbox me-2" style="color:#CC2228;"></i>Test IMAP Connection</h6>
                <form method="POST" class="mt-3">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                    <input type="hidden" name="action" value="test_imap">
                    <div class="mb-2"><input type="text" name="test_imap_host" class="form-control form-control-sm" placeholder="imap.hostinger.com" value="imap.hostinger.com" required></div>
                    <div class="mb-3"><input type="number" name="test_imap_port" class="form-control form-control-sm" value="993"></div>
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100"><i class="fas fa-satellite me-1"></i>Test IMAP Host</button>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4">
                <h6 style="font-weight:700;color:#1C2280;"><i class="fas fa-paper-plane me-2" style="color:#CC2228;"></i>Send Test Email</h6>
                <form method="POST" class="mt-3">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                    <input type="hidden" name="action" value="send_test_mail">
                    <div class="mb-3"><input type="email" name="test_email_target" class="form-control form-control-sm" placeholder="your-email@domain.com" required></div>
                    <button type="submit" class="btn btn-sm btn-success w-100" style="background:#10b981;border:none;"><i class="fas fa-paper-plane me-1"></i>Send Test Email</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="accountModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;">
      <div class="modal-header" style="background:linear-gradient(135deg,#1C2280,#CC2228);color:#fff;">
        <h5 class="modal-title" id="modalTitle">Mail Account Configuration</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="action" value="save_account">
        <input type="hidden" name="account_id" id="accId" value="0">
        <div class="modal-body p-4">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Company Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email_address" id="acc_email" class="form-control" placeholder="contact@vortexsoftinnovations.com" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Display Name</label>
                    <input type="text" name="display_name" id="acc_name" class="form-control" value="Vortexsoft Group">
                </div>
            </div>
            <hr>
            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-paper-plane me-1"></i> Outgoing SMTP Settings</h6>
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label">SMTP Host</label><input type="text" name="smtp_host" id="acc_smtp_host" class="form-control" placeholder="smtp.hostinger.com"></div>
                <div class="col-md-3"><label class="form-label">SMTP Port</label><input type="number" name="smtp_port" id="acc_smtp_port" class="form-control" value="587"></div>
                <div class="col-md-3"><label class="form-label">Encryption</label><select name="smtp_encryption" id="acc_smtp_enc" class="form-select"><option value="tls">TLS</option><option value="ssl">SSL</option><option value="none">None</option></select></div>
                <div class="col-md-6"><label class="form-label">SMTP Username</label><input type="text" name="smtp_username" id="acc_smtp_user" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">SMTP Secret / Password</label><input type="password" name="smtp_password" class="form-control" placeholder="********"></div>
            </div>
            <hr>
            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-inbox me-1"></i> Incoming IMAP Settings</h6>
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label">IMAP Host</label><input type="text" name="imap_host" id="acc_imap_host" class="form-control" placeholder="imap.hostinger.com"></div>
                <div class="col-md-3"><label class="form-label">IMAP Port</label><input type="number" name="imap_port" id="acc_imap_port" class="form-control" value="993"></div>
                <div class="col-md-3"><label class="form-label">Encryption</label><select name="imap_encryption" id="acc_imap_enc" class="form-select"><option value="ssl">SSL</option><option value="tls">TLS</option><option value="none">None</option></select></div>
                <div class="col-md-6"><label class="form-label">IMAP Username</label><input type="text" name="imap_username" id="acc_imap_user" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">IMAP Secret / Password</label><input type="password" name="imap_password" class="form-control" placeholder="********"></div>
            </div>
            <div class="row g-3">
                <div class="col-6"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" id="acc_active" checked><label class="form-check-label" for="acc_active">Account Active</label></div></div>
                <div class="col-6"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_default" id="acc_default"><label class="form-check-label" for="acc_default">Default Sending Mailbox</label></div></div>
            </div>
        </div>
        <div class="modal-footer" style="padding:16px 24px;">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-primary-custom">Save Configuration</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="../assets/vendor/bootstrap.bundle.min.js"></script>
<script>
function openAddModal(){
    document.getElementById('accId').value = '0';
    document.getElementById('acc_email').value = '';
    document.getElementById('acc_name').value = 'Vortexsoft Group';
    document.getElementById('acc_smtp_host').value = 'smtp.hostinger.com';
    document.getElementById('acc_imap_host').value = 'imap.hostinger.com';
    new bootstrap.Modal(document.getElementById('accountModal')).show();
}
function openEditModal(acc){
    document.getElementById('accId').value = acc.id;
    document.getElementById('acc_email').value = acc.email_address;
    document.getElementById('acc_name').value = acc.display_name || '';
    document.getElementById('acc_smtp_host').value = acc.smtp_host || '';
    document.getElementById('acc_smtp_port').value = acc.smtp_port || 587;
    document.getElementById('acc_smtp_enc').value = acc.smtp_encryption || 'tls';
    document.getElementById('acc_smtp_user').value = acc.smtp_username || '';
    document.getElementById('acc_imap_host').value = acc.imap_host || '';
    document.getElementById('acc_imap_port').value = acc.imap_port || 993;
    document.getElementById('acc_imap_enc').value = acc.imap_encryption || 'ssl';
    document.getElementById('acc_imap_user').value = acc.imap_username || '';
    document.getElementById('acc_active').checked = acc.is_active == 1;
    document.getElementById('acc_default').checked = acc.is_default == 1;
    new bootstrap.Modal(document.getElementById('accountModal')).show();
}
</script>
</body>
</html>
