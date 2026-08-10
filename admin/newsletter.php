<?php
/**
 * Vortexsoft Innovations — Admin: Newsletter Subscribers Manager
 * /admin/newsletter.php
 */

session_start();
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
admin_check();

$db = getDB();
$msg = '';

if ($db) {
    // CSV Export
    if (isset($_GET['export']) && $_GET['export'] === 'csv') {
        try {
            $stmt = $db->query("SELECT id, email, name, ip_address, is_active, subscribed_at FROM newsletter_subscribers ORDER BY subscribed_at DESC");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (ob_get_level()) ob_end_clean();
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="newsletter_subscribers_' . date('Y-m-d') . '.csv"');

            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'Email', 'Name', 'IP Address', 'Status', 'Subscribed At']);
            foreach ($rows as $row) {
                fputcsv($output, [
                    $row['id'],
                    $row['email'],
                    $row['name'] ?? '—',
                    $row['ip_address'] ?? '—',
                    $row['is_active'] ? 'Active' : 'Unsubscribed',
                    $row['subscribed_at']
                ]);
            }
            fclose($output);
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    try {
        // Toggle Active Status
        if (isset($_GET['toggle'])) {
            $sid = (int)$_GET['toggle'];
            $db->prepare("UPDATE newsletter_subscribers SET is_active = NOT is_active WHERE id = :id")->execute([':id' => $sid]);
            header('Location: newsletter.php?msg=updated');
            exit;
        }

        // Delete Subscriber
        if (isset($_GET['delete'])) {
            $sid = (int)$_GET['delete'];
            $db->prepare("DELETE FROM newsletter_subscribers WHERE id = :id")->execute([':id' => $sid]);
            header('Location: newsletter.php?msg=deleted');
            exit;
        }

        $filter = sanitize($_GET['filter'] ?? '');
        $search = sanitize($_GET['q']      ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));

        $where  = "WHERE 1=1";
        $params = [];
        if ($filter === 'active')       { $where .= " AND is_active = 1"; }
        if ($filter === 'unsubscribed') { $where .= " AND is_active = 0"; }
        if ($search)                    { $where .= " AND (email LIKE :q OR name LIKE :q2)"; $params[':q'] = $params[':q2'] = '%' . $search . '%'; }

        $total_count = (int)$db->query("SELECT COUNT(*) FROM newsletter_subscribers $where")->fetchColumn();
        $pg          = paginate($total_count, ITEMS_PER_PAGE, $page);

        $stmt = $db->prepare("SELECT id, email, name, ip_address, is_active, subscribed_at FROM newsletter_subscribers $where ORDER BY subscribed_at DESC LIMIT :l OFFSET :o");
        foreach ($params as $k => &$v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':l', $pg['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':o', $pg['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $subscribers = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $subscribers = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Newsletter Subscribers — Vortexsoft Admin Panel</title>
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
.table-card{background:#fff;border-radius:16px;border:1px solid #e8ecff;overflow:hidden}
.table-card-header{padding:18px 24px;border-bottom:1px solid #f0f4ff;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
.table-card-header h5{font-family:'Poppins',sans-serif;font-weight:700;font-size:15px;color:#1e293b;margin:0}
table{width:100%;border-collapse:collapse}
th{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;padding:12px 20px;background:#f8faff;border-bottom:1px solid #f0f4ff;white-space:nowrap}
td{font-size:13.5px;padding:13px 20px;border-bottom:1px solid #f8f9ff;vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:#fafbff}
.status-badge{font-size:11px;font-weight:700;padding:4px 10px;border-radius:100px}
.badge-active{background:#f0fdf4;color:#10b981}
.badge-unsub{background:#fff0f0;color:#CC2228}
.action-btn{font-size:12px;font-weight:600;padding:5px 10px;border-radius:7px;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-right:4px}
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
    <a href="newsletter.php" class="sidebar-link active"><span class="icon"><i class="fas fa-paper-plane"></i></span> Newsletter</a>
    <div class="nav-section">System</div>
    <a href="settings.php" class="sidebar-link"><span class="icon"><i class="fas fa-cog"></i></span> Settings</a>
    <a href="/index.php" target="_blank" class="sidebar-link"><span class="icon"><i class="fas fa-external-link-alt"></i></span> View Website</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt me-2"></i> Sign Out</a>
  </div>
</aside>

<main class="admin-main">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
      <h1><i class="fas fa-paper-plane me-2" style="color:#CC2228;"></i> Newsletter Subscribers</h1>
      <div style="font-size:13px;color:#64748b;">Manage newsletter email subscriptions collected from website footer.</div>
    </div>
    <a href="newsletter.php?export=csv" class="btn" style="background:#10b981;color:#fff;border-radius:10px;font-weight:700;font-size:13px;padding:10px 20px;">
      <i class="fas fa-file-csv me-1"></i> Export CSV List
    </a>
  </div>

  <?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success mb-4" style="border-radius:12px;">
    <i class="fas fa-check-circle me-2"></i> Subscriber <?= $_GET['msg'] === 'deleted' ? 'deleted' : 'updated' ?> successfully.
  </div>
  <?php endif; ?>

  <div class="table-card">
    <div class="table-card-header">
      <h5>Subscribers List (<?= $total_count ?? 0 ?>)</h5>
      <form action="newsletter.php" method="GET" class="d-flex gap-2">
        <input type="text" name="q" class="form-control form-control-sm" placeholder="Search email..." value="<?= htmlspecialchars($search) ?>" style="border-radius:8px;min-width:200px;">
        <select name="filter" class="form-select form-select-sm" style="border-radius:8px;width:auto;">
          <option value="">All Statuses</option>
          <option value="active" <?= $filter==='active'?'selected':'' ?>>Active Only</option>
          <option value="unsubscribed" <?= $filter==='unsubscribed'?'selected':'' ?>>Unsubscribed</option>
        </select>
        <button type="submit" class="btn btn-sm btn-primary" style="border-radius:8px;">Filter</button>
      </form>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>#</th><th>Email Address</th><th>Name</th><th>Subscribed At</th><th>IP Address</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if(empty($subscribers)): ?>
          <tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;"><i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:12px;opacity:.3;"></i> No subscribers found.</td></tr>
          <?php else: ?>
          <?php foreach($subscribers as $s): ?>
          <tr>
            <td style="color:#94a3b8;font-size:12px;">#<?= $s['id'] ?></td>
            <td style="font-weight:600;"><a href="mailto:<?= htmlspecialchars($s['email']) ?>" style="color:#1C2280;"><?= htmlspecialchars($s['email']) ?></a></td>
            <td><?= htmlspecialchars($s['name'] ?? '—') ?></td>
            <td style="font-size:12px;color:#94a3b8;white-space:nowrap;"><?= date('d M Y, h:i A', strtotime($s['subscribed_at'])) ?></td>
            <td style="font-size:12px;color:#94a3b8;"><?= htmlspecialchars($s['ip_address'] ?? '—') ?></td>
            <td><span class="status-badge <?= $s['is_active']?'badge-active':'badge-unsub' ?>"><?= $s['is_active']?'Active':'Unsubscribed' ?></span></td>
            <td>
              <a href="newsletter.php?toggle=<?= $s['id'] ?>" class="action-btn" style="background:rgba(28,34,128,.1);color:#1C2280;" title="Toggle Status"><i class="fas fa-sync-alt"></i> Toggle</a>
              <a href="newsletter.php?delete=<?= $s['id'] ?>" class="action-btn" style="background:rgba(204,34,40,.1);color:#CC2228;" onclick="return confirm('Delete this subscriber?');"><i class="fas fa-trash"></i> Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if (isset($pg) && $pg['total_pages'] > 1): ?>
    <div style="padding:16px 24px;">
      <nav><ul class="pagination mb-0 gap-1">
        <?php if($pg['has_prev']): ?><li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$pg['prev_page']])) ?>" style="border-radius:6px;font-size:13px;"><i class="fas fa-chevron-left"></i></a></li><?php endif; ?>
        <?php for($n=1;$n<=$pg['total_pages'];$n++): ?><li class="page-item <?= $n==$pg['current_page']?'active':'' ?>"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$n])) ?>" style="border-radius:6px;font-size:13px;<?= $n==$pg['current_page']?'background:#1C2280;border-color:#1C2280;':'' ?>"><?= $n ?></a></li><?php endfor; ?>
        <?php if($pg['has_next']): ?><li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$pg['next_page']])) ?>" style="border-radius:6px;font-size:13px;"><i class="fas fa-chevron-right"></i></a></li><?php endif; ?>
      </ul></nav>
    </div>
    <?php endif; ?>
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
