<?php
/**
 * Vortexsoft Innovations — Admin Dashboard
 * /admin/dashboard.php
 */

session_start();
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
admin_check();

$db = getDB();

// Fetch dashboard stats
$stats = ['contacts'=>0,'new_contacts'=>0,'applications'=>0,'new_apps'=>0,'blog_posts'=>0,'subscribers'=>0,'jobs_active'=>0];
$recent_contacts = [];
$recent_apps     = [];

if ($db) {
    try {
        $stats['contacts']     = (int)$db->query("SELECT COUNT(*) FROM contact_inquiries")->fetchColumn();
        $stats['new_contacts'] = (int)$db->query("SELECT COUNT(*) FROM contact_inquiries WHERE is_read=0")->fetchColumn();
        $stats['applications'] = (int)$db->query("SELECT COUNT(*) FROM job_applications")->fetchColumn();
        $stats['new_apps']     = (int)$db->query("SELECT COUNT(*) FROM job_applications WHERE status='new'")->fetchColumn();
        $stats['blog_posts']   = (int)$db->query("SELECT COUNT(*) FROM blog_posts WHERE is_published=1")->fetchColumn();
        $stats['subscribers']  = (int)$db->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE is_active=1")->fetchColumn();
        try { $stats['jobs_active'] = (int)$db->query("SELECT COUNT(*) FROM jobs WHERE is_active=1")->fetchColumn(); } catch(Throwable $e) {}

        // Recent contacts
        $recent_contacts = $db->query("SELECT id,name,email,phone,service,created_at,is_read FROM contact_inquiries ORDER BY created_at DESC LIMIT 8")->fetchAll();
        // Recent applications
        $recent_apps     = $db->query("SELECT id,applicant_name,email,phone,job_title,status,created_at FROM job_applications ORDER BY created_at DESC LIMIT 8")->fetchAll();
// Pipeline Breakdown for Quick Bar
        $app_pipeline = ['new' => 0, 'shortlisted' => 0, 'interview' => 0, 'offered' => 0];
        try {
            $pRows = $db->query("SELECT status, COUNT(*) as c FROM job_applications GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
            foreach ($pRows as $st => $c) {
                $app_pipeline[$st] = (int)$c;
            }
        } catch (Throwable $e) {}
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_role = $_SESSION['admin_role'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Vortexsoft Admin Panel</title>
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="/assets/vendor/bootstrap.min.css">
<link rel="stylesheet" href="/assets/vendor/fontawesome/all.min.css">
<link rel="stylesheet" href="/assets/vendor/fonts.css">
<link rel="icon" type="image/x-icon" href="/favicon.ico?v=20260912">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=20260912">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=20260912">
<link rel="icon" type="image/jpeg" sizes="1024x1024" href="/icon.jpg?v=20260912">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--dark:#080B1A;--primary:#1C2280;--accent:#CC2228;--sidebar-w:260px}
body{font-family:'Inter',sans-serif;background:#f0f2ff;color:#1e293b;min-height:100vh;display:flex}
/* Sidebar */
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
.sidebar-link .badge{margin-left:auto;background:var(--accent);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:100px}
.sidebar-footer{padding:16px 20px;border-top:1px solid rgba(255,255,255,.06)}
.admin-user-info{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.admin-avatar{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px;flex-shrink:0}
.admin-name{font-size:13px;font-weight:600;color:#fff}
.admin-role{font-size:11px;color:rgba(255,255,255,.4);text-transform:capitalize}
.btn-logout{background:rgba(204,34,40,.15);border:1px solid rgba(204,34,40,.3);color:#CC2228;width:100%;padding:9px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:.2s;text-align:center;text-decoration:none;display:block}
.btn-logout:hover{background:#CC2228;color:#fff}
/* Main Content */
.admin-main{margin-left:var(--sidebar-w);flex:1;padding:28px;min-height:100vh;transition:.3s}
.mobile-header{display:none;background:var(--dark);padding:14px 20px;align-items:center;justify-content:space-between;color:#fff}
.admin-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}
.admin-header h1{font-family:'Poppins',sans-serif;font-size:22px;font-weight:700;color:#1e293b}
.admin-header .subtitle{font-size:13px;color:#64748b;margin-top:2px}
.stat-card{background:#fff;border-radius:16px;padding:24px;border:1px solid #e8ecff;transition:.3s;position:relative;overflow:hidden}
.stat-card:hover{transform:translateY(-3px);box-shadow:0 10px 30px rgba(28,34,128,.1)}
.stat-icon{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
.stat-value{font-family:'Poppins',sans-serif;font-size:2.2rem;font-weight:800;color:#1e293b;line-height:1;margin:12px 0 4px}
.stat-label{font-size:13px;color:#64748b;font-weight:500}
.stat-badge{position:absolute;top:16px;right:16px;font-size:11px;font-weight:700;padding:4px 10px;border-radius:100px}
.table-card{background:#fff;border-radius:16px;border:1px solid #e8ecff;overflow:hidden;margin-bottom:24px}
.table-card-header{padding:18px 24px;border-bottom:1px solid #f0f4ff;display:flex;justify-content:space-between;align-items:center}
.table-card-header h5{font-family:'Poppins',sans-serif;font-weight:700;font-size:15px;color:#1e293b;margin:0}
.table-card table{width:100%;border-collapse:collapse}
.table-card th{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;padding:12px 20px;background:#f8faff;border-bottom:1px solid #f0f4ff;white-space:nowrap}
.table-card td{font-size:13.5px;padding:13px 20px;border-bottom:1px solid #f8f9ff;vertical-align:middle}
.table-card tr:last-child td{border-bottom:none}
.table-card tr:hover td{background:#fafbff}
.status-badge{font-size:11px;font-weight:700;padding:4px 10px;border-radius:100px;border:1px solid}
.status-new{background:#fff0f0;color:#CC2228;border-color:rgba(204,34,40,.2)}
.status-read,.status-reviewed{background:#f0fdf4;color:#10b981;border-color:rgba(16,185,129,.2)}
.status-shortlisted,.status-interview{background:#fffbeb;color:#f59e0b;border-color:rgba(245,158,11,.2)}
.status-offered{background:#f0f9ff;color:#0ea5e9;border-color:rgba(14,165,233,.2)}
.status-rejected{background:#fef2f2;color:#ef4444;border-color:rgba(239,68,68,.2)}
.action-btn{font-size:12px;font-weight:600;padding:5px 12px;border-radius:7px;border:none;cursor:pointer;transition:.2s;text-decoration:none;display:inline-flex;align-items:center;gap:5px}
.action-btn-view{background:rgba(28,34,128,.08);color:#1C2280}
.action-btn-view:hover{background:#1C2280;color:#fff}
.action-btn-wa{background:rgba(37,211,102,.12);color:#128c7e;}
.action-btn-wa:hover{background:#25D366;color:#fff;}
.action-btn-call{background:rgba(14,165,233,.12);color:#0284c7;}
.action-btn-call:hover{background:#0284c7;color:#fff;}
.pipeline-pill{background:#fff;border-radius:12px;padding:12px 18px;border:1px solid #e8ecff;display:flex;align-items:center;justify-content:space-between;text-decoration:none;transition:.2s;color:inherit;}
.pipeline-pill:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(28,34,128,.06);color:inherit;}
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

<?php require __DIR__ . '/_sidebar.php'; ?>

<!-- Main Content -->
<main class="admin-main">
  <div class="admin-header">
    <div>
      <h1>Dashboard <span style="font-weight:400;font-size:18px;color:#64748b;">— Welcome, <?= htmlspecialchars($admin_name) ?></span></h1>
      <div class="subtitle">Vortexsoft Innovations Private Limited Admin Panel | <?= date('l, d F Y') ?></div>
    </div>
    <div class="d-flex gap-2">
      <a href="blog/generate.php" class="btn" style="background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;border-radius:10px;font-size:13px;font-weight:600;padding:10px 18px;text-decoration:none;"><i class="fas fa-robot me-2"></i> AI Blog Generator</a>
      <a href="/index.php" target="_blank" class="btn btn-outline-secondary" style="border-radius:10px;font-size:13px;font-weight:600;padding:10px 16px;"><i class="fas fa-external-link-alt me-1"></i> View Site</a>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="row g-4 mb-4">
    <?php $cards = [
      ['label'=>'Total Inquiries','value'=>$stats['contacts'],'new'=>$stats['new_contacts'],'icon'=>'fa-envelope','bg'=>'rgba(28,34,128,.08)','color'=>'#1C2280','link'=>'contacts.php'],
      ['label'=>'Job Applications','value'=>$stats['applications'],'new'=>$stats['new_apps'],'icon'=>'fa-briefcase','bg'=>'rgba(204,34,40,.08)','color'=>'#CC2228','link'=>'applications.php'],
      ['label'=>'Active Job Postings','value'=>$stats['jobs_active'],'new'=>0,'icon'=>'fa-clipboard-list','bg'=>'rgba(91,168,212,.08)','color'=>'#5BA8D4','link'=>'jobs.php'],
      ['label'=>'Newsletter Subs','value'=>$stats['subscribers'],'new'=>0,'icon'=>'fa-users','bg'=>'rgba(16,185,129,.08)','color'=>'#10b981','link'=>'newsletter.php'],
    ]; ?>
    <?php foreach($cards as $c): ?>
    <div class="col-lg-3 col-md-6">
      <a href="<?= $c['link'] ?>" style="text-decoration:none;">
        <div class="stat-card">
          <?php if($c['new']): ?><span class="stat-badge" style="background:rgba(204,34,40,.1);color:#CC2228;"><?= $c['new'] ?> new</span><?php endif; ?>
          <div class="stat-icon" style="background:<?= $c['bg'] ?>"><i class="fas <?= $c['icon'] ?>" style="color:<?= $c['color'] ?>;"></i></div>
          <div class="stat-value"><?= number_format($c['value']) ?></div>
          <div class="stat-label"><?= $c['label'] ?></div>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Quick Recruitment Pipeline Bar -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <a href="applications.php?filter=new" class="pipeline-pill">
        <div>
          <div style="font-size:11px;font-weight:700;color:#CC2228;text-transform:uppercase;">Unreviewed Apps</div>
          <div style="font-size:18px;font-weight:800;color:#CC2228;"><?= number_format($app_pipeline['new'] ?? 0) ?></div>
        </div>
        <i class="fas fa-bell text-danger" style="font-size:20px;opacity:.8;"></i>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="applications.php?filter=shortlisted" class="pipeline-pill">
        <div>
          <div style="font-size:11px;font-weight:700;color:#d97706;text-transform:uppercase;">Shortlisted</div>
          <div style="font-size:18px;font-weight:800;color:#d97706;"><?= number_format($app_pipeline['shortlisted'] ?? 0) ?></div>
        </div>
        <i class="fas fa-star" style="color:#f59e0b;font-size:20px;opacity:.8;"></i>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="applications.php?filter=interview" class="pipeline-pill">
        <div>
          <div style="font-size:11px;font-weight:700;color:#0284c7;text-transform:uppercase;">Interviewing</div>
          <div style="font-size:18px;font-weight:800;color:#0284c7;"><?= number_format($app_pipeline['interview'] ?? 0) ?></div>
        </div>
        <i class="fas fa-comments text-info" style="font-size:20px;opacity:.8;"></i>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="applications.php?filter=offered" class="pipeline-pill">
        <div>
          <div style="font-size:11px;font-weight:700;color:#15803d;text-transform:uppercase;">Hired / Offered</div>
          <div style="font-size:18px;font-weight:800;color:#15803d;"><?= number_format($app_pipeline['offered'] ?? 0) ?></div>
        </div>
        <i class="fas fa-award text-success" style="font-size:20px;opacity:.8;"></i>
      </a>
    </div>
  </div>

  <!-- Recent Inquiries Table -->
  <div class="table-card mb-4">
    <div class="table-card-header">
      <h5><i class="fas fa-envelope me-2" style="color:#CC2228;"></i> Recent Contact Inquiries</h5>
      <a href="contacts.php" style="font-size:13px;color:#1C2280;font-weight:600;text-decoration:none;">View All →</a>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>#</th><th>Name</th><th>Email</th><th>Service</th><th>Date</th><th>Status</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recent_contacts)): ?>
          <tr><td colspan="7" style="text-align:center;padding:30px;color:#94a3b8;"><i class="fas fa-inbox me-2"></i> No inquiries yet.</td></tr>
          <?php else: ?>
          <?php foreach($recent_contacts as $c): ?>
          <tr>
            <td style="color:#94a3b8;">#<?= $c['id'] ?></td>
            <td style="font-weight:600;"><?= htmlspecialchars($c['name']) ?></td>
            <td><a href="mailto:<?= htmlspecialchars($c['email']) ?>" style="color:#1C2280;"><?= htmlspecialchars($c['email']) ?></a></td>
            <td><?= htmlspecialchars($c['service'] ?? 'General') ?></td>
            <td style="color:#94a3b8;white-space:nowrap;"><?= time_ago($c['created_at']) ?></td>
            <td><span class="status-badge <?= $c['is_read'] ? 'status-read' : 'status-new' ?>"><?= $c['is_read'] ? 'Read' : 'New' ?></span></td>
            <td style="white-space:nowrap;text-align:right;">
              <div class="d-inline-flex gap-1 align-items-center">
                <?php if (!empty($c['phone'])): 
                  $inq_wa = format_whatsapp_url($c['phone'], "Hello {$c['name']}, thank you for contacting Vortexsoft Innovations regarding " . ($c['service'] ?? 'our services') . ". How can we assist you?");
                ?>
                  <a href="<?= $inq_wa ?>" target="_blank" class="action-btn action-btn-wa" title="WhatsApp Client"><i class="fab fa-whatsapp"></i></a>
                  <a href="tel:<?= preg_replace('/[^0-9+]/', '', $c['phone']) ?>" class="action-btn action-btn-call" title="Call Client"><i class="fas fa-phone-alt"></i></a>
                <?php endif; ?>
                <a href="mailto:<?= htmlspecialchars($c['email']) ?>?subject=Regarding your inquiry at Vortexsoft Innovations" class="action-btn" title="Email Client"><i class="fas fa-envelope"></i></a>
                <a href="contacts.php?view=<?= $c['id'] ?>" class="action-btn action-btn-view" title="View Inquiry"><i class="fas fa-eye"></i></a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Recent Applications Table -->
  <div class="table-card">
    <div class="table-card-header">
      <h5><i class="fas fa-briefcase me-2" style="color:#CC2228;"></i> Recent Job Applications</h5>
      <a href="applications.php" style="font-size:13px;color:#1C2280;font-weight:600;text-decoration:none;">View All →</a>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr><th>#</th><th>Applicant</th><th>Email</th><th>Position</th><th>Applied</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php if (empty($recent_apps)): ?>
          <tr><td colspan="7" style="text-align:center;padding:30px;color:#94a3b8;"><i class="fas fa-inbox me-2"></i> No applications yet.</td></tr>
          <?php else: ?>
          <?php foreach($recent_apps as $a): ?>
          <tr>
            <td style="color:#94a3b8;">#<?= $a['id'] ?></td>
            <td style="font-weight:600;"><?= htmlspecialchars($a['applicant_name']) ?></td>
            <td><a href="mailto:<?= htmlspecialchars($a['email']) ?>" style="color:#1C2280;"><?= htmlspecialchars($a['email']) ?></a></td>
            <td><?= htmlspecialchars($a['job_title']) ?></td>
            <td style="color:#94a3b8;white-space:nowrap;"><?= time_ago($a['created_at']) ?></td>
            <td><span class="status-badge status-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
            <td style="white-space:nowrap;text-align:right;">
              <div class="d-inline-flex gap-1 align-items-center">
                <?php if (!empty($a['phone'])): 
                  $app_wa = format_whatsapp_url($a['phone'], "Hello {$a['applicant_name']}, this is from Vortexsoft Innovations HR regarding your application for {$a['job_title']}. Are you available for a brief discussion?");
                ?>
                  <a href="<?= $app_wa ?>" target="_blank" class="action-btn action-btn-wa" title="WhatsApp Candidate"><i class="fab fa-whatsapp"></i></a>
                  <a href="tel:<?= preg_replace('/[^0-9+]/', '', $a['phone']) ?>" class="action-btn action-btn-call" title="Call Candidate"><i class="fas fa-phone-alt"></i></a>
                <?php endif; ?>
                <a href="mailto:<?= htmlspecialchars($a['email']) ?>?subject=Regarding your application for <?= rawurlencode($a['job_title']) ?> at Vortexsoft Innovations" class="action-btn" title="Email Candidate"><i class="fas fa-envelope"></i></a>
                <a href="applications.php?view=<?= $a['id'] ?>" class="action-btn action-btn-view" title="View Application"><i class="fas fa-eye"></i></a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
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
