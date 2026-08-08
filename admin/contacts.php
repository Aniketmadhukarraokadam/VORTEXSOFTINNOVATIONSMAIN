<?php
/**
 * Vortexsoft Innovations — Admin: Inquiries Manager
 * /admin/contacts.php
 */

session_start();
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
admin_check();

$db = getDB();
$view_id  = (int)($_GET['view'] ?? 0);
$view     = null;
$contacts = [];

if ($db) {
    try {
        // Mark as read if viewing
        if ($view_id) {
            $db->prepare("UPDATE contact_inquiries SET is_read=1 WHERE id=:id")->execute([':id' => $view_id]);
            $view = $db->prepare("SELECT * FROM contact_inquiries WHERE id=:id")->execute([':id'=>$view_id]) ? $db->query("SELECT * FROM contact_inquiries WHERE id=$view_id")->fetch() : null;
        }

        // Save note
        if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['note'])) {
            $note = sanitize($_POST['note']);
            $db->prepare("UPDATE contact_inquiries SET notes=:n,is_replied=:r WHERE id=:id")
               ->execute([':n'=>$note, ':r'=>isset($_POST['is_replied'])?1:0, ':id'=>$view_id]);
            header("Location: contacts.php?view=$view_id&saved=1"); exit;
        }

        $filter = sanitize($_GET['filter'] ?? '');
        $search = sanitize($_GET['q']      ?? '');
        $page   = max(1,(int)($_GET['page']??1));

        $where  = "WHERE 1=1";
        $params = [];
        if ($filter==='new')   { $where.=" AND is_read=0"; }
        if ($filter==='unread'){ $where.=" AND is_read=0"; }
        if ($search)           { $where.=" AND (name LIKE :q OR email LIKE :q2 OR service LIKE :q3 OR message LIKE :q4)"; $params[':q']=$params[':q2']=$params[':q3']=$params[':q4']='%'.$search.'%'; }

        $total_count = (int)$db->query("SELECT COUNT(*) FROM contact_inquiries $where")->fetchColumn();
        $pg = paginate($total_count, ITEMS_PER_PAGE, $page);

        $stmt = $db->prepare("SELECT id,name,email,phone,service,created_at,is_read,is_replied FROM contact_inquiries $where ORDER BY created_at DESC LIMIT :l OFFSET :o");
        foreach($params as $k=>&$v) $stmt->bindValue($k,$v);
        $stmt->bindValue(':l',$pg['per_page'],PDO::PARAM_INT);
        $stmt->bindValue(':o',$pg['offset'],PDO::PARAM_INT);
        $stmt->execute();
        $contacts = $stmt->fetchAll();
    } catch (PDOException $e) { error_log($e->getMessage()); }
}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inquiries — Vortexsoft Admin</title>
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="/assets/vendor/bootstrap.min.css">
<link rel="stylesheet" href="/assets/vendor/fontawesome/all.min.css">
<link rel="stylesheet" href="/assets/vendor/fonts.css">
<link rel="icon" href="/icon.jpg">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--dark:#080B1A;--primary:#1C2280;--accent:#CC2228;--sidebar-w:260px}
body{font-family:'Inter',sans-serif;background:#f0f2ff;color:#1e293b;min-height:100vh;display:flex}
.admin-sidebar{width:var(--sidebar-w);background:var(--dark);min-height:100vh;position:fixed;top:0;left:0;z-index:1000;display:flex;flex-direction:column}
.sidebar-logo{padding:24px 20px;border-bottom:1px solid rgba(255,255,255,.06)}
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
.admin-avatar{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#1C2280,#CC2228);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px;flex-shrink:0}
.btn-logout{background:rgba(204,34,40,.15);border:1px solid rgba(204,34,40,.3);color:#CC2228;width:100%;padding:9px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-align:center;text-decoration:none;display:block;transition:.2s}
.btn-logout:hover{background:#CC2228;color:#fff}
.admin-main{margin-left:var(--sidebar-w);flex:1;padding:28px}
.admin-header h1{font-family:'Poppins',sans-serif;font-size:22px;font-weight:700;color:#1e293b;margin-bottom:4px}
.table-card{background:#fff;border-radius:16px;border:1px solid #e8ecff;overflow:hidden}
.table-card-header{padding:18px 24px;border-bottom:1px solid #f0f4ff;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
.table-card-header h5{font-family:'Poppins',sans-serif;font-weight:700;font-size:15px;color:#1e293b;margin:0}
table{width:100%;border-collapse:collapse}
th{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;padding:12px 20px;background:#f8faff;border-bottom:1px solid #f0f4ff;white-space:nowrap}
td{font-size:13.5px;padding:13px 20px;border-bottom:1px solid #f8f9ff;vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:#fafbff}
.status-badge{font-size:11px;font-weight:700;padding:4px 10px;border-radius:100px}
.badge-new{background:#fff0f0;color:#CC2228}
.badge-read{background:#f0fdf4;color:#10b981}
.badge-replied{background:#f0f9ff;color:#0ea5e9}
.action-btn{font-size:12px;font-weight:600;padding:5px 12px;border-radius:7px;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:5px;background:rgba(28,34,128,.08);color:#1C2280;transition:.2s}
.action-btn:hover{background:#1C2280;color:#fff}
.detail-card{background:#fff;border-radius:16px;border:1px solid #e8ecff;padding:32px}
</style>
</head>
<body>
<aside class="admin-sidebar">
  <div class="sidebar-logo"><img src="/logo-header.png" alt="Vortexsoft"><div class="sub">Admin Panel</div></div>
  <nav class="sidebar-nav">
    <div class="nav-section">Main</div>
    <a href="dashboard.php" class="sidebar-link"><span class="icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard</a>
    <a href="contacts.php" class="sidebar-link active"><span class="icon"><i class="fas fa-envelope"></i></span> Inquiries</a>
    <a href="applications.php" class="sidebar-link"><span class="icon"><i class="fas fa-briefcase"></i></span> Applications</a>
    <div class="nav-section">Content</div>
    <a href="blog-posts.php" class="sidebar-link"><span class="icon"><i class="fas fa-pen-alt"></i></span> Blog Posts</a>
    <a href="newsletter.php" class="sidebar-link"><span class="icon"><i class="fas fa-paper-plane"></i></span> Newsletter</a>
    <div class="nav-section">System</div>
    <a href="/index.php" target="_blank" class="sidebar-link"><span class="icon"><i class="fas fa-external-link-alt"></i></span> View Website</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt me-2"></i> Sign Out</a>
  </div>
</aside>

<main class="admin-main">
  <div class="mb-4">
    <h1><i class="fas fa-envelope me-2" style="color:#CC2228;"></i> Contact Inquiries</h1>
    <div style="font-size:13px;color:#64748b;">Manage all website contact form submissions.</div>
  </div>

  <?php if (!empty($_GET['saved'])): ?>
  <div class="alert alert-success mb-4" style="border-radius:12px;"><i class="fas fa-check-circle me-2"></i> Note saved successfully.</div>
  <?php endif; ?>

  <?php if ($view): ?>
  <!-- Detail View -->
  <div class="mb-3"><a href="contacts.php" style="color:#1C2280;font-size:14px;font-weight:600;text-decoration:none;"><i class="fas fa-arrow-left me-1"></i> Back to Inquiries</a></div>
  <div class="detail-card">
    <div class="row gy-3">
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Name</div><div style="font-size:16px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($view['name']) ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Email</div><div><a href="mailto:<?= htmlspecialchars($view['email']) ?>" style="font-size:15px;color:#1C2280;font-weight:600;"><?= htmlspecialchars($view['email']) ?></a></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Phone</div><div style="font-size:14px;"><?= htmlspecialchars($view['phone'] ?? 'Not provided') ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Service Needed</div><div style="font-size:14px;"><?= htmlspecialchars($view['service'] ?? 'General') ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Submitted</div><div style="font-size:14px;"><?= date('d M Y, h:i A', strtotime($view['created_at'])) ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">IP Address</div><div style="font-size:14px;color:#94a3b8;"><?= htmlspecialchars($view['ip_address'] ?? '-') ?></div></div>
      <div class="col-12"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:8px;">Message</div><div style="background:#f8f9ff;border-radius:12px;padding:20px;font-size:15px;color:#374151;line-height:1.7;white-space:pre-wrap;"><?= htmlspecialchars($view['message']) ?></div></div>
      <!-- Admin Notes -->
      <div class="col-12 mt-2">
        <form method="POST" action="contacts.php?view=<?= $view['id'] ?>">
          <label style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;display:block;margin-bottom:6px;">Admin Notes</label>
          <textarea name="note" class="form-control" rows="3" placeholder="Internal notes about this inquiry..." style="border-radius:10px;border:1.5px solid #e2e8f0;font-size:14px;"><?= htmlspecialchars($view['notes'] ?? '') ?></textarea>
          <div class="mt-3 d-flex gap-3 align-items-center flex-wrap">
            <button type="submit" class="btn" style="background:#1C2280;color:#fff;border-radius:8px;font-size:13px;font-weight:700;padding:10px 22px;"><i class="fas fa-save me-1"></i> Save Notes</button>
            <label style="font-size:13px;color:#64748b;font-weight:600;display:flex;align-items:center;gap:6px;">
              <input type="checkbox" name="is_replied" value="1" <?= $view['is_replied']?'checked':'' ?>> Mark as Replied
            </label>
            <a href="mailto:<?= htmlspecialchars($view['email']) ?>" class="btn" style="background:rgba(28,34,128,.08);color:#1C2280;border-radius:8px;font-size:13px;font-weight:700;padding:10px 22px;"><i class="fas fa-reply me-1"></i> Reply via Email</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php else: ?>
  <!-- List View -->
  <div class="table-card">
    <div class="table-card-header">
      <h5>All Inquiries (<?= $total_count ?? 0 ?>)</h5>
      <form action="contacts.php" method="GET" class="d-flex gap-2">
        <input type="text" name="q" class="form-control form-control-sm" placeholder="Search inquiries..." value="<?= htmlspecialchars($search) ?>" style="border-radius:8px;min-width:200px;">
        <select name="filter" class="form-select form-select-sm" style="border-radius:8px;width:auto;">
          <option value="">All</option>
          <option value="new" <?= $filter==='new'?'selected':'' ?>>Unread Only</option>
        </select>
        <button type="submit" class="btn btn-sm btn-primary" style="border-radius:8px;">Filter</button>
      </form>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Service</th><th>Phone</th><th>Received</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
          <?php if(empty($contacts)): ?>
          <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;"><i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:12px;opacity:.3;"></i> No inquiries found.</td></tr>
          <?php else: ?>
          <?php foreach($contacts as $c): ?>
          <tr style="<?= !$c['is_read'] ? 'background:#fffbf5;' : '' ?>">
            <td style="color:#94a3b8;font-size:12px;">#<?= $c['id'] ?></td>
            <td style="font-weight:<?= !$c['is_read']?'700':'500' ?>;"><?= htmlspecialchars($c['name']) ?></td>
            <td><a href="mailto:<?= htmlspecialchars($c['email']) ?>" style="color:#1C2280;font-size:13px;"><?= htmlspecialchars($c['email']) ?></a></td>
            <td style="font-size:12.5px;"><?= htmlspecialchars($c['service'] ?? 'General') ?></td>
            <td style="font-size:12.5px;color:#64748b;"><?= htmlspecialchars($c['phone'] ?? '—') ?></td>
            <td style="font-size:12px;color:#94a3b8;white-space:nowrap;"><?= time_ago($c['created_at']) ?></td>
            <td>
              <?php if (!$c['is_read']): ?><span class="status-badge badge-new">New</span>
              <?php elseif ($c['is_replied']): ?><span class="status-badge badge-replied">Replied</span>
              <?php else: ?><span class="status-badge badge-read">Read</span><?php endif; ?>
            </td>
            <td><a href="contacts.php?view=<?= $c['id'] ?>" class="action-btn"><i class="fas fa-eye"></i> View</a></td>
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
  <?php endif; ?>
</main>
<script src="/assets/vendor/bootstrap.bundle.min.js"></script>
</body></html>
