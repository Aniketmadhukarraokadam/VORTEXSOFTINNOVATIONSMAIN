<?php
/**
 * Vortexsoft Innovations — Admin: Database Backup & Recovery Utility
 * Exports database to /backups/db_backup_[TIMESTAMP].sql
 * Protected by super_admin role check + .htaccess deny from all
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();
admin_require_role(['super_admin']);

$db = getDB();
$message = $error = '';

$backup_dir = __DIR__ . '/../backups';
if (!is_dir($backup_dir)) {
    @mkdir($backup_dir, 0755, true);
    // Protect backup directory
    @file_put_contents($backup_dir . '/.htaccess', "Order allow,deny\nDeny from all");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_backup') {
    if (!verify_csrf()) {
        $error = 'Security token mismatch.';
    } else {
        try {
            $filename = 'db_backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = $backup_dir . '/' . $filename;
            
            $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            $sql = "-- Vortexsoft Group Database Backup\n-- Generated: " . date('Y-m-d H:i:s') . "\n\nSET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                $create = $db->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sql .= $create['Create Table'] . ";\n\n";

                $rows = $db->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        $keys = array_map(fn($k) => "`{$k}`", array_keys($row));
                        $vals = array_map(function($v) use ($db) {
                            if ($v === null) return "NULL";
                            return $db->quote($v);
                        }, array_values($row));

                        $sql .= "INSERT INTO `{$table}` (" . implode(',', $keys) . ") VALUES (" . implode(',', $vals) . ");\n";
                    }
                    $sql .= "\n";
                }
            }
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            file_put_contents($filepath, $sql);
            log_admin_activity('Database Backup Created', "Created backup file: {$filename}");
            $message = "Database backup successfully generated: <strong>{$filename}</strong> (" . round(filesize($filepath) / 1024, 2) . " KB)";
        } catch (Throwable $e) {
            $error = 'Backup failed: ' . htmlspecialchars($e->getMessage());
        }
    }
}

// Fetch existing backups
$backups = [];
if (is_dir($backup_dir)) {
    $files = scandir($backup_dir);
    foreach ($files as $f) {
        if (str_ends_with($f, '.sql')) {
            $path = $backup_dir . '/' . $f;
            $backups[] = [
                'name' => $f,
                'size' => round(filesize($path) / 1024, 2) . ' KB',
                'time' => date('Y-m-d H:i:s', filemtime($path))
            ];
        }
    }
    usort($backups, fn($a, $b) => strcmp($b['name'], $a['name']));
}

$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Database Backup — Vortexsoft Admin</title>
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
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:28px}
.topbar h1{font-size:22px;font-weight:800;color:#1C2280;margin:0}
.card{background:#fff;border-radius:16px;border:1px solid #e8ecff;overflow:hidden;margin-bottom:24px}
.card-header{padding:18px 22px;border-bottom:1px solid #f0f2ff;font-weight:700;color:#1C2280;display:flex;justify-content:space-between;align-items:center}
table{width:100%;border-collapse:collapse}
th,td{padding:12px 18px;text-align:left;font-size:13.5px;border-bottom:1px solid #f0f2ff}
th{background:#f8fafc;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:.5px}
.btn-primary-custom{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;border:none;border-radius:10px;padding:10px 20px;font-size:13.5px;font-weight:700;cursor:pointer}
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
        <a href="activity-log.php"><i class="fas fa-history"></i> Activity Log</a>
        <a href="users.php"><i class="fas fa-users-cog"></i> Admin Users</a>
        <a href="backup_db.php" class="active"><i class="fas fa-database"></i> DB Backup</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>
<div class="main-content">
    <div class="topbar">
        <div>
            <h1><i class="fas fa-database" style="color:#CC2228;margin-right:10px;"></i>Database Backup & Rollback</h1>
            <div style="font-size:13px;color:#64748b;margin-top:2px;">Generate and manage secure additive database snapshots.</div>
        </div>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="action" value="create_backup">
            <button type="submit" class="btn-primary-custom"><i class="fas fa-download me-2"></i>Create New Backup</button>
        </form>
    </div>

    <?php if($message): ?><div class="alert alert-success" style="padding:14px;border-radius:10px;margin-bottom:20px;background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;"><?= $message ?></div><?php endif; ?>
    <?php if($error): ?><div class="alert alert-danger" style="padding:14px;border-radius:10px;margin-bottom:20px;background:#fff0f0;color:#991b1b;border:1px solid #fecaca;"><?= $error ?></div><?php endif; ?>

    <div class="card">
        <div class="card-header">
            <span>Existing SQL Backups (<?= count($backups) ?>)</span>
            <span style="font-size:12px;color:#64748b;font-weight:400;">Backups stored safely in <code>/backups/</code> with HTTP access restricted</span>
        </div>
        <?php if(empty($backups)): ?>
        <div style="padding:40px;text-align:center;color:#94a3b8;">No backups created yet. Click "Create New Backup" above.</div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Backup File Name</th>
                    <th>File Size</th>
                    <th>Created At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($backups as $b): ?>
                <tr>
                    <td style="font-family:monospace;font-weight:700;color:#1C2280;"><i class="fas fa-file-alt me-2" style="color:#CC2228;"></i><?= htmlspecialchars($b['name']) ?></td>
                    <td><?= $b['size'] ?></td>
                    <td><?= $b['time'] ?></td>
                    <td><span class="badge bg-success" style="font-size:11px;padding:4px 8px;">Protected</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
