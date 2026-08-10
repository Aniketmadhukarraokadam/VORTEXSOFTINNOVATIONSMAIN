<?php
/**
 * Vortexsoft Innovations — Admin Activity Log
 * Requirement #8: Log all admin activity without exposing passwords/secrets.
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();
admin_require_role(['super_admin', 'admin']);

$db = getDB();
$search = sanitize($_GET['q'] ?? '');

$where = "";
$params = [];

if (!empty($search)) {
    $where = "WHERE action LIKE :q OR admin_username LIKE :q OR details LIKE :q OR ip_address LIKE :q";
    $params[':q'] = "%{$search}%";
}

$page = max(1, (int)($_GET['p'] ?? 1));
$limit = 25;
$offset = ($page - 1) * $limit;

$total = 0;
$logs  = [];

if ($db) {
    try {
        $stmtCnt = $db->prepare("SELECT COUNT(*) FROM admin_activity_logs {$where}");
        $stmtCnt->execute($params);
        $total = (int)$stmtCnt->fetchColumn();

        $stmtLogs = $db->prepare("SELECT * FROM admin_activity_logs {$where} ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}");
        $stmtLogs->execute($params);
        $logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {}
}

$totalPages = max(1, ceil($total / $limit));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Activity Log — Vortexsoft Admin</title>
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
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.topbar h1{font-size:22px;font-weight:800;color:#1C2280;margin:0}
.card{background:#fff;border-radius:16px;border:1px solid #e8ecff;overflow:hidden}
.card-header{padding:16px 22px;border-bottom:1px solid #f0f2ff;display:flex;justify-content:space-between;align-items:center}
table{width:100%;border-collapse:collapse}
th,td{padding:12px 18px;text-align:left;font-size:13.5px;border-bottom:1px solid #f0f2ff;vertical-align:middle}
th{background:#f8fafc;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:.5px}
.badge-action{background:rgba(28,34,128,.08);color:#1C2280;font-size:11px;font-weight:700;padding:3px 10px;border-radius:100px}
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
        <a href="email-templates.php"><i class="fas fa-file-code"></i> Templates</a>
        <a href="activity-log.php" class="active"><i class="fas fa-history"></i> Activity Log</a>
        <a href="users.php"><i class="fas fa-users-cog"></i> Admin Users</a>
        <a href="backup_db.php"><i class="fas fa-database"></i> DB Backup</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>
<div class="main-content">
    <div class="topbar">
        <div>
            <h1><i class="fas fa-history" style="color:#CC2228;margin-right:10px;"></i>Admin Activity Log</h1>
            <div style="font-size:13px;color:#64748b;margin-top:2px;">Complete audit trail of system events and admin operations.</div>
        </div>
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Search logs..." value="<?= htmlspecialchars($search) ?>" style="border-radius:8px;width:220px;">
            <button type="submit" class="btn btn-sm btn-primary" style="background:#1C2280;border-radius:8px;">Filter</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <span style="font-size:14px;font-weight:700;color:#1C2280;">Log Entries (<?= $total ?> total)</span>
            <span style="font-size:12px;color:#94a3b8;">Passwords & connection secrets are automatically excluded</span>
        </div>
        <?php if(empty($logs)): ?>
        <div style="padding:40px;text-align:center;color:#94a3b8;">No activity log records found.</div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Date & Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($logs as $l): ?>
                <tr>
                    <td style="color:#94a3b8;font-size:12px;"><?= $l['id'] ?></td>
                    <td style="font-size:13px;white-space:nowrap;"><?= htmlspecialchars($l['created_at']) ?></td>
                    <td style="font-weight:700;color:#1C2280;"><?= htmlspecialchars($l['admin_username']) ?></td>
                    <td><span class="badge-action"><?= htmlspecialchars($l['action']) ?></span></td>
                    <td style="color:#475569;max-width:380px;"><?= htmlspecialchars($l['details']) ?></td>
                    <td style="font-family:monospace;font-size:12px;color:#64748b;"><?= htmlspecialchars($l['ip_address']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php if($totalPages > 1): ?>
        <div style="padding:16px 22px;display:flex;gap:6px;justify-content:center;border-top:1px solid #f0f2ff;">
            <?php for($i=1;$i<=$totalPages;$i++): ?>
            <a href="?p=<?= $i ?>&q=<?= urlencode($search) ?>" class="btn btn-sm <?= $i===$page ? 'btn-primary' : 'btn-light' ?>" style="<?= $i===$page ? 'background:#1C2280;' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
