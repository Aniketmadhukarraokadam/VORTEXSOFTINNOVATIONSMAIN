<?php
/**
 * Vortexsoft Innovations — Admin Sent & Received Email Viewer + Failure Monitor
 * Requirements #2 & #4: Mailbox activity viewer, failure logs & retry actions
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();
admin_require_role(['super_admin', 'admin']);

$db = getDB();
$message = $error = '';

// Handle Retry Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'retry_email') {
    if (!verify_csrf()) {
        $error = 'Security token mismatch.';
    } else {
        $log_id = (int)($_POST['log_id'] ?? 0);
        if ($log_id > 0) {
            $stmt = $db->prepare("SELECT * FROM email_logs WHERE id=?");
            $stmt->execute([$log_id]);
            $log = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($log) {
                $sent = send_notification_email($log['recipient'], $log['subject'], $log['body_html']);
                if ($sent) {
                    $db->prepare("UPDATE email_logs SET status='sent', error_message=NULL, last_retry_at=NOW(), retry_count=retry_count+1 WHERE id=?")->execute([$log_id]);
                    log_admin_activity('Email Retried', "Successfully retried email ID: {$log_id} to {$log['recipient']}");
                    $message = "Email #{$log_id} retried and delivered successfully!";
                } else {
                    $db->prepare("UPDATE email_logs SET status='failed', error_message='Retry failed', last_retry_at=NOW(), retry_count=retry_count+1 WHERE id=?")->execute([$log_id]);
                    log_admin_activity('Email Retry Failed', "Retry failed for email ID: {$log_id}");
                    $error = "Retry failed for email #{$log_id}.";
                }
            }
        }
    }
}

$tab = sanitize($_GET['tab'] ?? 'sent');

$logs = [];
if ($tab === 'inbox') {
    // Received inquiries/emails
    $logs = $db->query("SELECT id, name AS sender, email AS recipient, service AS subject, message AS body_html, 'received' AS status, created_at FROM contact_inquiries ORDER BY created_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
} elseif ($tab === 'failed') {
    // Failed emails
    $logs = $db->query("SELECT * FROM email_logs WHERE status='failed' ORDER BY created_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Sent emails
    $logs = $db->query("SELECT * FROM email_logs WHERE status='sent' ORDER BY created_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
}

$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Email Activity Viewer — Vortexsoft Admin</title>
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
.card{background:#fff;border-radius:16px;border:1px solid #e8ecff;overflow:hidden}
.card-header{padding:14px 22px;border-bottom:1px solid #f0f2ff;display:flex;gap:12px;align-items:center;background:#fff}
.nav-tab{padding:8px 16px;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;color:#64748b;transition:.2s}
.nav-tab.active{background:#1C2280;color:#fff}
table{width:100%;border-collapse:collapse}
th,td{padding:12px 18px;text-align:left;font-size:13.5px;border-bottom:1px solid #f0f2ff;vertical-align:middle}
th{background:#f8fafc;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:.5px}
.status-sent{background:#f0fdf4;color:#16a34a;padding:3px 10px;border-radius:100px;font-size:11px;font-weight:700}
.status-failed{background:#fff0f0;color:#dc2626;padding:3px 10px;border-radius:100px;font-size:11px;font-weight:700}
.status-inbox{background:#eff6ff;color:#2563eb;padding:3px 10px;border-radius:100px;font-size:11px;font-weight:700}
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
        <a href="emails.php" class="active"><i class="fas fa-inbox"></i> Email Activity</a>
        <a href="email-accounts.php"><i class="fas fa-mail-bulk"></i> Mail Accounts</a>
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
            <h1><i class="fas fa-inbox" style="color:#CC2228;margin-right:10px;"></i>Sent & Received Email Activity</h1>
            <div style="font-size:13px;color:#64748b;margin-top:2px;">Live mailbox audit log, delivery status, and diagnostic failure monitoring.</div>
        </div>
    </div>

    <?php if($message): ?><div class="alert alert-success" style="padding:14px;border-radius:10px;margin-bottom:20px;background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;"><?= $message ?></div><?php endif; ?>
    <?php if($error): ?><div class="alert alert-danger" style="padding:14px;border-radius:10px;margin-bottom:20px;background:#fff0f0;color:#991b1b;border:1px solid #fecaca;"><?= $error ?></div><?php endif; ?>

    <div class="card">
        <div class="card-header">
            <a href="?tab=sent" class="nav-tab <?= $tab==='sent' ? 'active' : '' ?>"><i class="fas fa-paper-plane me-1"></i> Sent Emails</a>
            <a href="?tab=inbox" class="nav-tab <?= $tab==='inbox' ? 'active' : '' ?>"><i class="fas fa-inbox me-1"></i> Received / Inbox</a>
            <a href="?tab=failed" class="nav-tab <?= $tab==='failed' ? 'active' : '' ?>"><i class="fas fa-exclamation-triangle me-1"></i> Failed / Bounced</a>
        </div>
        <?php if(empty($logs)): ?>
        <div style="padding:50px;text-align:center;color:#94a3b8;">No email records found for this view.</div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Date & Time</th>
                    <th><?= $tab==='inbox' ? 'Sender' : 'Recipient' ?></th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($logs as $l): ?>
                <tr>
                    <td style="color:#94a3b8;font-size:12px;"><?= $l['id'] ?></td>
                    <td style="font-size:12.5px;white-space:nowrap;"><?= htmlspecialchars($l['created_at']) ?></td>
                    <td style="font-weight:700;color:#1C2280;"><?= htmlspecialchars($tab==='inbox' ? $l['sender'] : $l['recipient']) ?></td>
                    <td style="color:#334155;max-width:320px;"><?= htmlspecialchars($l['subject']) ?></td>
                    <td>
                        <?php if($tab==='inbox'): ?>
                        <span class="status-inbox">Received</span>
                        <?php elseif(($l['status'] ?? '') === 'sent'): ?>
                        <span class="status-sent">Delivered</span>
                        <?php else: ?>
                        <span class="status-failed">Failed</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick='viewMail(<?= json_encode($l) ?>)'><i class="fas fa-eye"></i> View</button>
                        <?php if($tab==='failed'): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <input type="hidden" name="action" value="retry_email">
                            <input type="hidden" name="log_id" value="<?= $l['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-success" style="background:#10b981;border:none;"><i class="fas fa-redo"></i> Retry</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="mailModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;">
      <div class="modal-header" style="background:linear-gradient(135deg,#1C2280,#CC2228);color:#fff;">
        <h5 class="modal-title" id="m_subject">Email Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div style="background:#f8fafc;padding:14px;border-radius:10px;margin-bottom:16px;font-size:13px;color:#475569;">
            <div><strong>Sender:</strong> <span id="m_sender"></span></div>
            <div><strong>Recipient:</strong> <span id="m_recipient"></span></div>
            <div><strong>Date:</strong> <span id="m_date"></span></div>
            <div id="m_error_row" style="display:none;color:#dc2626;margin-top:6px;"><strong>Failure Diagnostic:</strong> <span id="m_error"></span></div>
        </div>
        <hr>
        <div id="m_body" style="font-size:14px;line-height:1.6;color:#1e293b;max-height:400px;overflow-y:auto;padding:10px;"></div>
      </div>
    </div>
  </div>
</div>

<script src="../assets/vendor/bootstrap.bundle.min.js"></script>
<script>
function viewMail(l){
    document.getElementById('m_subject').innerText = l.subject || 'Email Details';
    document.getElementById('m_sender').innerText  = l.sender || 'contact@vortexsoftinnovations.com';
    document.getElementById('m_recipient').innerText = l.recipient || '';
    document.getElementById('m_date').innerText   = l.created_at || '';
    document.getElementById('m_body').innerHTML   = l.body_html || '';
    
    if (l.error_message) {
        document.getElementById('m_error_row').style.display = 'block';
        document.getElementById('m_error').innerText = l.error_message;
    } else {
        document.getElementById('m_error_row').style.display = 'none';
    }
    new bootstrap.Modal(document.getElementById('mailModal')).show();
}
</script>
</body>
</html>
