<?php
/**
 * Vortexsoft Innovations — Admin: Job Applications Manager
 * /admin/applications.php
 */

session_start();
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
admin_check();

$db = getDB();
$view_id = (int)($_GET['view'] ?? 0);
$view    = null;
$apps    = [];

if ($db) {
    try {
        // Delete Application
        if (isset($_GET['delete'])) {
            $did = (int)$_GET['delete'];
            $db->prepare("DELETE FROM job_applications WHERE id = :id")->execute([':id' => $did]);
            header('Location: applications.php?msg=deleted');
            exit;
        }

        // Status update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_update'])) {
            $new_status = sanitize($_POST['status'] ?? 'new');
            $admin_note = sanitize($_POST['admin_notes'] ?? '');
            $db->prepare("UPDATE job_applications SET status=:s, admin_notes=:n WHERE id=:id")
               ->execute([':s' => $new_status, ':n' => $admin_note, ':id' => $view_id]);
            header("Location: applications.php?view=$view_id&updated=1");
            exit;
        }

        if ($view_id) {
            $stmt = $db->prepare("SELECT * FROM job_applications WHERE id=:id");
            $stmt->execute([':id' => $view_id]);
            $view = $stmt->fetch();
            if ($view && $view['status'] === 'new') {
                $db->prepare("UPDATE job_applications SET status='reviewed' WHERE id=:id")->execute([':id' => $view_id]);
                $view['status'] = 'reviewed';
            }
        }

        $filter = sanitize($_GET['filter'] ?? '');
        $search = sanitize($_GET['q']      ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));

        $where  = "WHERE 1=1";
        $params = [];
        if ($filter) { $where .= " AND status=:st"; $params[':st'] = $filter; }
        if ($search) {
            $where .= " AND (applicant_name LIKE :q OR email LIKE :q2 OR job_title LIKE :q3 OR current_location LIKE :q4)";
            $params[':q'] = $params[':q2'] = $params[':q3'] = $params[':q4'] = '%' . $search . '%';
        }

        $total_count = (int)$db->query("SELECT COUNT(*) FROM job_applications $where")->fetchColumn();
        $pg          = paginate($total_count, ITEMS_PER_PAGE, $page);

        $stmt = $db->prepare("SELECT id, applicant_name, email, phone, job_title, experience_years, status, created_at, resume_filename, resume_path FROM job_applications $where ORDER BY created_at DESC LIMIT :l OFFSET :o");
        foreach ($params as $k => &$v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':l', $pg['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':o', $pg['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $apps = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Job Applications — Vortexsoft Admin Panel</title>
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
.status-badge{font-size:11px;font-weight:700;padding:4px 10px;border-radius:100px;border:1px solid}
.status-new{background:#fff0f0;color:#CC2228;border-color:rgba(204,34,40,.2)}
.status-reviewed{background:#f0fdf4;color:#10b981;border-color:rgba(16,185,129,.2)}
.status-shortlisted,.status-interview{background:#fffbeb;color:#f59e0b;border-color:rgba(245,158,11,.2)}
.status-offered{background:#f0f9ff;color:#0ea5e9;border-color:rgba(14,165,233,.2)}
.status-rejected{background:#fef2f2;color:#ef4444;border-color:rgba(239,68,68,.2)}
.action-btn{font-size:12px;font-weight:600;padding:5px 12px;border-radius:7px;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:5px;background:rgba(28,34,128,.08);color:#1C2280;transition:.2s;margin-right:4px}
.action-btn:hover{background:#1C2280;color:#fff}
.action-btn-delete{background:rgba(204,34,40,.08);color:#CC2228}
.action-btn-delete:hover{background:#CC2228;color:#fff}
.detail-card{background:#fff;border-radius:16px;border:1px solid #e8ecff;padding:32px}
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
    <a href="applications.php" class="sidebar-link active"><span class="icon"><i class="fas fa-briefcase"></i></span> Applications</a>
    <div class="nav-section">Content</div>
    <a href="blog-posts.php" class="sidebar-link"><span class="icon"><i class="fas fa-pen-alt"></i></span> Blog Posts</a>
    <a href="newsletter.php" class="sidebar-link"><span class="icon"><i class="fas fa-paper-plane"></i></span> Newsletter</a>
    <div class="nav-section">System</div>
    <a href="settings.php" class="sidebar-link"><span class="icon"><i class="fas fa-cog"></i></span> Settings</a>
    <a href="/index.php" target="_blank" class="sidebar-link"><span class="icon"><i class="fas fa-external-link-alt"></i></span> View Website</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt me-2"></i> Sign Out</a>
  </div>
</aside>

<main class="admin-main">
  <div class="mb-4">
    <h1><i class="fas fa-briefcase me-2" style="color:#CC2228;"></i> Job Applications</h1>
    <div style="font-size:13px;color:#64748b;">Review and manage candidate applications submitted via careers page.</div>
  </div>

  <?php if (!empty($_GET['updated'])): ?>
  <div class="alert alert-success mb-4" style="border-radius:12px;"><i class="fas fa-check-circle me-2"></i> Application updated successfully.</div>
  <?php endif; ?>

  <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
  <div class="alert alert-success mb-4" style="border-radius:12px;"><i class="fas fa-check-circle me-2"></i> Application deleted successfully.</div>
  <?php endif; ?>

  <?php if ($view): ?>
  <!-- Detail View -->
  <div class="mb-3"><a href="applications.php" style="color:#1C2280;font-size:14px;font-weight:600;text-decoration:none;"><i class="fas fa-arrow-left me-1"></i> Back to Applications</a></div>
  <div class="detail-card">
    <div class="row gy-3">
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Position Applied For</div><div style="font-size:18px;font-weight:800;color:#1C2280;"><?= htmlspecialchars($view['job_title']) ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Applicant Name</div><div style="font-size:16px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($view['applicant_name']) ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Email</div><div><a href="mailto:<?= htmlspecialchars($view['email']) ?>" style="font-size:15px;color:#1C2280;font-weight:600;"><?= htmlspecialchars($view['email']) ?></a></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Phone</div><div style="font-size:14px;font-weight:600;"><?= htmlspecialchars($view['phone']) ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Location</div><div style="font-size:14px;"><?= htmlspecialchars($view['current_location'] ?? 'Not provided') ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Experience / Current Co.</div><div style="font-size:14px;"><?= htmlspecialchars($view['experience_years'] ? $view['experience_years'].' Yrs' : 'N/A') ?> — <?= htmlspecialchars($view['current_company'] ?? 'N/A') ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Notice Period &amp; Expected CTC</div><div style="font-size:14px;"><?= htmlspecialchars($view['notice_period'] ?? 'N/A') ?> | <?= htmlspecialchars($view['expected_ctc'] ?? 'N/A') ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Resume File</div>
        <?php if(!empty($view['resume_filename'])): ?>
        <a href="download.php?id=<?= $view['id'] ?>" class="btn btn-sm" style="background:#1C2280;color:#fff;border-radius:8px;font-weight:600;"><i class="fas fa-download me-1"></i> Download <?= htmlspecialchars($view['resume_filename']) ?></a>
        <?php else: ?><span style="color:#94a3b8;font-size:13px;">No resume file attached</span><?php endif; ?>
      </div>
      <?php if($view['cover_letter']): ?>
      <div class="col-12"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:6px;">Cover Letter</div><div style="background:#f8f9ff;border-radius:12px;padding:20px;font-size:14px;line-height:1.7;white-space:pre-wrap;"><?= htmlspecialchars($view['cover_letter']) ?></div></div>
      <?php endif; ?>

      <!-- Candidate Status Update -->
      <div class="col-12 mt-4 pt-3" style="border-top:1px solid #e8ecff;">
        <form method="POST" action="applications.php?view=<?= $view['id'] ?>">
          <input type="hidden" name="status_update" value="1">
          <div class="row g-3">
            <div class="col-md-4">
              <label style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;display:block;margin-bottom:6px;">Application Status</label>
              <select name="status" class="form-select" style="border-radius:10px;">
                <?php foreach(['new','reviewed','shortlisted','interview','offered','rejected','withdrawn'] as $st): ?>
                <option value="<?= $st ?>" <?= $view['status']===$st?'selected':'' ?>><?= ucfirst($st) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-8">
              <label style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;display:block;margin-bottom:6px;">Internal HR Notes</label>
              <input type="text" name="admin_notes" class="form-control" style="border-radius:10px;" placeholder="e.g. Scheduled technical interview for Tuesday 3 PM" value="<?= htmlspecialchars($view['admin_notes'] ?? '') ?>">
            </div>
            <div class="col-12 d-flex gap-3 mt-3 flex-wrap">
              <button type="submit" class="btn" style="background:#1C2280;color:#fff;border-radius:8px;font-size:13px;font-weight:700;padding:10px 24px;"><i class="fas fa-save me-1"></i> Update Candidate Status</button>
              <a href="mailto:<?= htmlspecialchars($view['email']) ?>?subject=Regarding your application for <?= urlencode($view['job_title']) ?>" class="btn" style="background:rgba(28,34,128,.08);color:#1C2280;border-radius:8px;font-size:13px;font-weight:700;padding:10px 24px;"><i class="fas fa-envelope me-1"></i> Email Candidate</a>
              <a href="applications.php?delete=<?= $view['id'] ?>" class="btn action-btn-delete" style="border-radius:8px;font-size:13px;font-weight:700;padding:10px 24px;" onclick="return confirm('Delete this application permanently?');"><i class="fas fa-trash me-1"></i> Delete</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php else: ?>
  <!-- List View -->
  <div class="table-card">
    <div class="table-card-header">
      <h5>All Job Applications (<?= $total_count ?? 0 ?>)</h5>
      <form action="applications.php" method="GET" class="d-flex gap-2">
        <input type="text" name="q" class="form-control form-control-sm" placeholder="Search applicant..." value="<?= htmlspecialchars($search) ?>" style="border-radius:8px;min-width:200px;">
        <select name="filter" class="form-select form-select-sm" style="border-radius:8px;width:auto;">
          <option value="">All Statuses</option>
          <option value="new" <?= $filter==='new'?'selected':'' ?>>New</option>
          <option value="shortlisted" <?= $filter==='shortlisted'?'selected':'' ?>>Shortlisted</option>
          <option value="interview" <?= $filter==='interview'?'selected':'' ?>>Interview</option>
          <option value="offered" <?= $filter==='offered'?'selected':'' ?>>Offered</option>
          <option value="rejected" <?= $filter==='rejected'?'selected':'' ?>>Rejected</option>
        </select>
        <button type="submit" class="btn btn-sm btn-primary" style="border-radius:8px;">Filter</button>
      </form>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>#</th><th>Applicant</th><th>Position</th><th>Experience</th><th>Resume</th><th>Applied</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if(empty($apps)): ?>
          <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;"><i class="fas fa-briefcase" style="font-size:32px;display:block;margin-bottom:12px;opacity:.3;"></i> No applications found.</td></tr>
          <?php else: ?>
          <?php foreach($apps as $a): ?>
          <tr>
            <td style="color:#94a3b8;font-size:12px;">#<?= $a['id'] ?></td>
            <td style="font-weight:600;"><?= htmlspecialchars($a['applicant_name']) ?><br><span style="font-size:12px;color:#94a3b8;"><?= htmlspecialchars($a['email']) ?></span></td>
            <td style="font-weight:600;color:#1C2280;"><?= htmlspecialchars($a['job_title']) ?></td>
            <td style="font-size:13px;"><?= htmlspecialchars($a['experience_years'] ? $a['experience_years'].' Yrs' : 'Fresh') ?></td>
            <td>
              <?php if(!empty($a['resume_filename'])): ?>
              <a href="download.php?id=<?= $a['id'] ?>" style="font-size:12px;color:#CC2228;font-weight:700;"><i class="fas fa-file-pdf me-1"></i> Resume</a>
              <?php else: ?><span style="color:#94a3b8;font-size:12px;">None</span><?php endif; ?>
            </td>
            <td style="font-size:12px;color:#94a3b8;white-space:nowrap;"><?= time_ago($a['created_at']) ?></td>
            <td><span class="status-badge status-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
            <td>
              <a href="applications.php?view=<?= $a['id'] ?>" class="action-btn"><i class="fas fa-eye"></i> View</a>
              <a href="applications.php?delete=<?= $a['id'] ?>" class="action-btn action-btn-delete" onclick="return confirm('Delete this application?');"><i class="fas fa-trash"></i></a>
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
  <?php endif; ?>
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
