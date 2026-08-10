<?php
/**
 * Vortexsoft Innovations — Admin Email Templates Manager
 * Requirement #5: Manage system email templates + protected variable replacement
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();
admin_require_role(['super_admin', 'admin']);

$db = getDB();
$message = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Security token mismatch.';
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'save_template') {
            $id        = (int)($_POST['template_id'] ?? 0);
            $subject   = sanitize($_POST['subject'] ?? '');
            $body_html = trim($_POST['body_html'] ?? '');

            if ($id > 0 && !empty($subject) && !empty($body_html)) {
                $stmt = $db->prepare("UPDATE email_templates SET subject=?, body_html=?, updated_at=NOW() WHERE id=?");
                $stmt->execute([$subject, $body_html, $id]);
                log_admin_activity('Email Template Updated', "Updated template ID: {$id}");
                $message = "Email template updated successfully.";
            } else {
                $error = 'Subject and Body cannot be empty.';
            }
        }
    }
}

$templates = $db->query("SELECT * FROM email_templates ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Email Templates — Vortexsoft Admin</title>
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
.card-header{padding:16px 22px;border-bottom:1px solid #f0f2ff;font-weight:700;color:#1C2280;display:flex;justify-content:space-between;align-items:center}
table{width:100%;border-collapse:collapse}
th,td{padding:12px 18px;text-align:left;font-size:13.5px;border-bottom:1px solid #f0f2ff;vertical-align:middle}
th{background:#f8fafc;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:.5px}
.var-pill{display:inline-block;background:#f0f2ff;color:#1C2280;font-family:monospace;font-size:11px;padding:2px 6px;border-radius:6px;margin:2px 0}
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
        <a href="email-accounts.php"><i class="fas fa-mail-bulk"></i> Mail Accounts</a>
        <a href="email-templates.php" class="active"><i class="fas fa-file-code"></i> Templates</a>
        <a href="activity-log.php"><i class="fas fa-history"></i> Activity Log</a>
        <a href="users.php"><i class="fas fa-users-cog"></i> Admin Users</a>
        <a href="backup_db.php"><i class="fas fa-database"></i> DB Backup</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>
<div class="main-content">
    <div class="topbar">
        <div>
            <h1><i class="fas fa-file-code" style="color:#CC2228;margin-right:10px;"></i>System Email Templates</h1>
            <div style="font-size:13px;color:#64748b;margin-top:2px;">Customize automated emails sent to candidates, visitors, and administrators.</div>
        </div>
    </div>

    <?php if($message): ?><div class="alert alert-success" style="padding:14px;border-radius:10px;margin-bottom:20px;background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;"><?= $message ?></div><?php endif; ?>
    <?php if($error): ?><div class="alert alert-danger" style="padding:14px;border-radius:10px;margin-bottom:20px;background:#fff0f0;color:#991b1b;border:1px solid #fecaca;"><?= $error ?></div><?php endif; ?>

    <div class="card">
        <div class="card-header">
            <span>Email Templates List (<?= count($templates) ?>)</span>
            <span style="font-size:12px;color:#94a3b8;">Protected variables: <code>{{candidate_name}}</code>, <code>{{job_title}}</code>, <code>{{company_name}}</code></span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Template Name</th>
                    <th>Template Key</th>
                    <th>Subject Line</th>
                    <th>Supported Variables</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($templates as $t): ?>
                <tr>
                    <td style="font-weight:700;color:#1C2280;"><?= htmlspecialchars($t['name']) ?></td>
                    <td style="font-family:monospace;font-size:12px;color:#64748b;"><?= htmlspecialchars($t['template_key']) ?></td>
                    <td style="font-size:13px;color:#334155;"><?= htmlspecialchars($t['subject']) ?></td>
                    <td>
                        <?php
                        $vars = json_decode($t['variables_json'] ?? '[]', true) ?: [];
                        foreach($vars as $v): ?>
                        <span class="var-pill">{{<?= htmlspecialchars($v) ?>}}</span>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick='openEditModal(<?= json_encode($t) ?>)'><i class="fas fa-edit"></i> Edit / Preview</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="tplModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;">
      <div class="modal-header" style="background:linear-gradient(135deg,#1C2280,#CC2228);color:#fff;">
        <h5 class="modal-title" id="m_name">Edit Email Template</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="action" value="save_template">
        <input type="hidden" name="template_id" id="t_id" value="0">
        <div class="modal-body p-4">
            <div class="mb-3">
                <label class="form-label fw-bold">Subject Line</label>
                <input type="text" name="subject" id="t_subject" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">HTML Body Content</label>
                <textarea name="body_html" id="t_body" class="form-control" rows="8" style="font-family:monospace;font-size:13px;" required></textarea>
            </div>
            <div style="background:#f8fafc;padding:12px;border-radius:8px;font-size:12px;color:#64748b;">
                <strong>Available Variables:</strong> <span id="t_vars"></span>
            </div>
        </div>
        <div class="modal-footer" style="padding:16px 24px;">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-primary-custom">Save Template</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="../assets/vendor/bootstrap.bundle.min.js"></script>
<script>
function openEditModal(t){
    document.getElementById('m_name').innerText = 'Edit: ' + t.name;
    document.getElementById('t_id').value = t.id;
    document.getElementById('t_subject').value = t.subject;
    document.getElementById('t_body').value = t.body_html;
    
    var vars = JSON.parse(t.variables_json || '[]');
    document.getElementById('t_vars').innerText = vars.map(function(v){ return '{{' + v + '}}'; }).join(', ');
    new bootstrap.Modal(document.getElementById('tplModal')).show();
}
</script>
</body>
</html>
