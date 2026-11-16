<?php
/**
 * Vortexsoft Innovations — Admin: Inquiries Manager
 * /admin/contacts.php
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
admin_check();

$db = getDB();
$view_id  = (int)($_GET['view'] ?? 0);
$view     = null;
$contacts = [];

// Stats for Metric Cards
$contact_stats = ['total' => 0, 'unread' => 0, 'replied' => 0, 'today' => 0];

if ($db) {
    try {
        // Quick Toggle Read / Unread
        if (isset($_GET['toggle_read'])) {
            $token = $_GET['csrf'] ?? '';
            if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
                header('Location: contacts.php?err=csrf');
                exit;
            }
            $tid = (int)$_GET['toggle_read'];
            $db->prepare("UPDATE contact_inquiries SET is_read = IF(is_read=1, 0, 1) WHERE id = :id")->execute([':id' => $tid]);
            header('Location: contacts.php?msg=status_updated');
            exit;
        }

        // Quick Toggle Replied
        if (isset($_GET['toggle_replied'])) {
            $token = $_GET['csrf'] ?? '';
            if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
                header('Location: contacts.php?err=csrf');
                exit;
            }
            $tid = (int)$_GET['toggle_replied'];
            $db->prepare("UPDATE contact_inquiries SET is_replied = IF(is_replied=1, 0, 1), is_read = 1 WHERE id = :id")->execute([':id' => $tid]);
            header('Location: contacts.php?msg=status_updated');
            exit;
        }

        // Delete Inquiry (CSRF Protected)
        if (isset($_GET['delete'])) {
            $token = $_GET['csrf'] ?? $_POST['csrf_token'] ?? '';
            if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
                header('Location: contacts.php?err=csrf');
                exit;
            }
            $did = (int)$_GET['delete'];
            $db->prepare("DELETE FROM contact_inquiries WHERE id = :id")->execute([':id' => $did]);
            if (function_exists('log_admin_activity')) {
                log_admin_activity('DELETE_INQUIRY', "Deleted contact inquiry #{$did}");
            }
            header('Location: contacts.php?msg=deleted');
            exit;
        }

        // Mark as read if viewing
        if ($view_id) {
            $db->prepare("UPDATE contact_inquiries SET is_read=1 WHERE id=:id")->execute([':id' => $view_id]);
            $stmt = $db->prepare("SELECT * FROM contact_inquiries WHERE id=:id");
            $stmt->execute([':id' => $view_id]);
            $view = $stmt->fetch() ?: null;
        }

        // Save note (CSRF Protected)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note'])) {
            if (!verify_csrf()) {
                header('Location: contacts.php?view=' . $view_id . '&err=csrf');
                exit;
            }
            $note = sanitize($_POST['note']);
            $db->prepare("UPDATE contact_inquiries SET notes=:n, is_replied=:r WHERE id=:id")
               ->execute([':n' => $note, ':r' => isset($_POST['is_replied']) ? 1 : 0, ':id' => $view_id]);
            if (function_exists('log_admin_activity')) {
                log_admin_activity('UPDATE_INQUIRY_NOTE', "Updated notes on contact inquiry #{$view_id}");
            }
            header("Location: contacts.php?view=$view_id&saved=1");
            exit;
        }

        // Calculate Contact Stats
        $contact_stats['total']   = (int)$db->query("SELECT COUNT(*) FROM contact_inquiries")->fetchColumn();
        $contact_stats['unread']  = (int)$db->query("SELECT COUNT(*) FROM contact_inquiries WHERE is_read=0")->fetchColumn();
        $contact_stats['replied'] = (int)$db->query("SELECT COUNT(*) FROM contact_inquiries WHERE is_replied=1")->fetchColumn();
        $contact_stats['today']   = (int)$db->query("SELECT COUNT(*) FROM contact_inquiries WHERE DATE(created_at) = CURDATE()")->fetchColumn();

        $filter = sanitize($_GET['filter'] ?? '');
        $search = sanitize($_GET['q']      ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));

        $where  = "WHERE 1=1";
        $params = [];
        if ($filter === 'new' || $filter === 'unread') { $where .= " AND is_read=0"; }
        elseif ($filter === 'replied')                { $where .= " AND is_replied=1"; }
        elseif ($filter === 'read')                   { $where .= " AND is_read=1 AND is_replied=0"; }

        if ($search) {
            $where .= " AND (name LIKE :q OR email LIKE :q2 OR service LIKE :q3 OR message LIKE :q4 OR phone LIKE :q5)";
            $params[':q'] = $params[':q2'] = $params[':q3'] = $params[':q4'] = $params[':q5'] = '%' . $search . '%';
        }

        // Handle CSV & Excel Export
        if (isset($_GET['export']) && in_array($_GET['export'], ['excel', 'csv'], true)) {
            $export_format = $_GET['export'];
            $export_scope  = sanitize($_GET['scope'] ?? 'filtered');

            $exp_where  = ($export_scope === 'all') ? "WHERE 1=1" : $where;
            $exp_params = ($export_scope === 'all') ? [] : $params;

            $exp_stmt = $db->prepare("SELECT * FROM contact_inquiries {$exp_where} ORDER BY created_at DESC");
            foreach ($exp_params as $k => &$v) $exp_stmt->bindValue($k, $v);
            $exp_stmt->execute();
            $export_rows = $exp_stmt->fetchAll(PDO::FETCH_ASSOC);

            $date_slug = date('Y-m-d_His');
            if ($export_format === 'csv') {
                $filename = "vortexsoft_inquiries_{$date_slug}.csv";
                header('Content-Type: text/csv; charset=UTF-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                echo "\xEF\xBB\xBF"; // UTF-8 BOM
                $out = fopen('php://output', 'w');
                fputcsv($out, [
                    'Inquiry ID', 'Received Date', 'Client Name', 'Email Address', 'Phone Number',
                    'Service Requested', 'Status (Read)', 'Replied Status', 'IP Address',
                    'Internal Admin Notes', 'Message Content'
                ]);
                foreach ($export_rows as $r) {
                    fputcsv($out, [
                        $r['id'],
                        $r['created_at'],
                        $r['name'],
                        $r['email'],
                        $r['phone'] ?? '',
                        $r['service'] ?? 'General',
                        $r['is_read'] ? 'Read' : 'Unread',
                        $r['is_replied'] ? 'Replied' : 'Pending Reply',
                        $r['ip_address'] ?? '',
                        $r['notes'] ?? '',
                        $r['message'] ?? ''
                    ]);
                }
                fclose($out);
                exit;
            } elseif ($export_format === 'excel') {
                $filename = "vortexsoft_inquiries_{$date_slug}.xls";
                header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');
                echo "<!DOCTYPE html>\n<html><head><meta charset=\"UTF-8\">\n<style>\n";
                echo "body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }\n";
                echo "table { border-collapse: collapse; width: 100%; }\n";
                echo "th { background-color: #1C2280; color: #FFFFFF; font-weight: bold; border: 1px solid #000000; padding: 10px 8px; text-align: left; }\n";
                echo "td { border: 1px solid #D0D7DE; padding: 8px; vertical-align: top; }\n";
                echo ".row-even { background-color: #F8FAFC; }\n";
                echo ".badge-new { background-color: #FEE2E2; color: #991B1B; font-weight: bold; }\n";
                echo ".badge-read { background-color: #D1FAE5; color: #065F46; }\n";
                echo ".badge-replied { background-color: #DBEAFE; color: #1E40AF; font-weight: bold; }\n";
                echo "</style></head><body>\n";
                echo "<table>\n";
                echo "<tr><td colspan=\"10\" style=\"font-size:16pt;font-weight:bold;color:#1C2280;border:none;padding:12px 0 4px 0;\">VORTEXSOFT INNOVATIONS — CONTACT INQUIRIES EXPORT</td></tr>\n";
                echo "<tr><td colspan=\"10\" style=\"font-size:10pt;color:#64748B;border:none;padding-bottom:12px;\">Generated: " . date('d M Y, h:i A') . " IST &nbsp;|&nbsp; Scope: " . ($export_scope === 'all' ? 'All Records' : 'Filtered View') . " &nbsp;|&nbsp; Total Records: " . count($export_rows) . "</td></tr>\n";
                echo "<thead><tr>\n";
                echo "<th>ID</th><th>Received</th><th>Client Name</th><th>Email</th><th>Phone</th><th>Service</th><th>Read Status</th><th>Replied</th><th>Notes</th><th>Message</th>\n";
                echo "</tr></thead><tbody>\n";
                $i = 0;
                foreach ($export_rows as $r) {
                    $bg = ($i++ % 2 === 0) ? 'row-even' : '';
                    $st_badge = !$r['is_read'] ? 'badge-new' : ($r['is_replied'] ? 'badge-replied' : 'badge-read');
                    $st_label = !$r['is_read'] ? 'NEW / UNREAD' : ($r['is_replied'] ? 'REPLIED' : 'READ');
                    echo "<tr class=\"{$bg}\">\n";
                    echo "<td>#" . (int)$r['id'] . "</td>\n";
                    echo "<td>" . htmlspecialchars($r['created_at']) . "</td>\n";
                    echo "<td><strong>" . htmlspecialchars($r['name']) . "</strong></td>\n";
                    echo "<td><a href=\"mailto:" . htmlspecialchars($r['email']) . "\">" . htmlspecialchars($r['email']) . "</a></td>\n";
                    echo "<td style=\"mso-number-format:'\@';\">" . htmlspecialchars($r['phone'] ?? '') . "</td>\n";
                    echo "<td>" . htmlspecialchars($r['service'] ?? 'General') . "</td>\n";
                    echo "<td class=\"{$st_badge}\" style=\"text-align:center;\">" . $st_label . "</td>\n";
                    echo "<td>" . ($r['is_replied'] ? 'Yes' : 'No') . "</td>\n";
                    echo "<td>" . nl2br(htmlspecialchars($r['notes'] ?? '')) . "</td>\n";
                    echo "<td>" . nl2br(htmlspecialchars($r['message'] ?? '')) . "</td>\n";
                    echo "</tr>\n";
                }
                echo "</tbody></table></body></html>\n";
                exit;
            }
        }

        $total_count = (int)$db->query("SELECT COUNT(*) FROM contact_inquiries $where")->fetchColumn();
        $pg          = paginate($total_count, ITEMS_PER_PAGE, $page);

        $stmt = $db->prepare("SELECT id, name, email, phone, service, created_at, is_read, is_replied FROM contact_inquiries $where ORDER BY created_at DESC LIMIT :l OFFSET :o");
        foreach ($params as $k => &$v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':l', $pg['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':o', $pg['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $contacts = $stmt->fetchAll();
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
<title>Inquiries &amp; Leads — Vortexsoft Admin Panel</title>
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
.admin-main{margin-left:var(--sidebar-w);flex:1;padding:28px;transition:.3s;max-width:calc(100vw - var(--sidebar-w))}
.mobile-header{display:none;background:var(--dark);padding:14px 20px;align-items:center;justify-content:space-between;color:#fff}
.admin-header h1{font-family:'Poppins',sans-serif;font-size:22px;font-weight:700;color:#1e293b;margin-bottom:4px}

/* Metric Cards */
.stat-card{background:#fff;border-radius:16px;padding:20px;border:1px solid #e8ecff;transition:.25s;position:relative;overflow:hidden;cursor:pointer;text-decoration:none;display:block}
.stat-card:hover{transform:translateY(-3px);box-shadow:0 10px 25px rgba(28,34,128,.08)}
.stat-icon{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.stat-value{font-family:'Poppins',sans-serif;font-size:1.9rem;font-weight:800;color:#1e293b;line-height:1;margin:10px 0 2px}
.stat-label{font-size:12.5px;color:#64748b;font-weight:600}
.pulse-badge{display:inline-flex;align-items:center;gap:6px;background:#fee2e2;color:#b91c1c;font-size:10px;font-weight:800;padding:2px 8px;border-radius:100px;text-transform:uppercase}
.pulse-dot{width:6px;height:6px;border-radius:50%;background:#ef4444;box-shadow:0 0 0 0 rgba(239,68,68,.7);animation:pulseRed 1.6s infinite}
@keyframes pulseRed{0%{box-shadow:0 0 0 0 rgba(239,68,68,.7)}70%{box-shadow:0 0 0 8px rgba(239,68,68,0)}100%{box-shadow:0 0 0 0 rgba(239,68,68,0)}}

/* Table Card */
.table-card{background:#fff;border-radius:16px;border:1px solid #e8ecff;overflow:hidden}
.table-card-header{padding:18px 24px;border-bottom:1px solid #f0f4ff;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
.table-card-header h5{font-family:'Poppins',sans-serif;font-weight:700;font-size:15px;color:#1e293b;margin:0}
table{width:100%;border-collapse:collapse}
th{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;padding:12px 18px;background:#f8faff;border-bottom:1px solid #f0f4ff;white-space:nowrap}
td{font-size:13px;padding:13px 18px;border-bottom:1px solid #f8f9ff;vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:#fafbff}

.status-badge{font-size:11px;font-weight:700;padding:4px 10px;border-radius:100px;display:inline-flex;align-items:center;gap:4px}
.badge-new{background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5}
.badge-read{background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0}
.badge-replied{background:#dbeafe;color:#1e40af;border:1px solid #bfdbfe}

/* Action Buttons */
.action-btn{font-size:12px;font-weight:600;padding:5px 9px;border-radius:7px;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;background:rgba(28,34,128,.08);color:#1C2280;transition:.2s}
.action-btn:hover{background:#1C2280;color:#fff}
.action-btn-wa{background:rgba(37,211,102,.12);color:#128c7e}
.action-btn-wa:hover{background:#25D366;color:#fff}
.action-btn-call{background:rgba(14,165,233,.12);color:#0284c7}
.action-btn-call:hover{background:#0284c7;color:#fff}
.action-btn-delete{background:rgba(204,34,40,.08);color:#CC2228}
.action-btn-delete:hover{background:#CC2228;color:#fff}

.detail-card{background:#fff;border-radius:16px;border:1px solid #e8ecff;padding:32px}
@media(max-width:1024px){
  body{flex-direction:column}
  .admin-sidebar{transform:translateX(-100%)}
  .admin-sidebar.show{transform:translateX(0)}
  .admin-main{margin-left:0;padding:20px;max-width:100%}
  .mobile-header{display:flex}
}
</style>
</head>
<body>

<?php require __DIR__ . '/_sidebar.php'; ?>

<main class="admin-main">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
      <h1 class="admin-header"><i class="fas fa-envelope me-2" style="color:#CC2228;"></i> Contact Inquiries &amp; Leads</h1>
      <div style="font-size:13px;color:#64748b;">Review client inquiries, send 1-click WhatsApp/Email responses, and export records.</div>
    </div>
    <!-- Quick Export Dropdown -->
    <div class="dropdown">
      <button class="btn btn-sm btn-outline-primary dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown" style="border-radius:8px;padding:8px 14px;">
        <i class="fas fa-file-export me-1"></i> Export Inquiries
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:10px;">
        <li><h6 class="dropdown-header text-uppercase" style="font-size:10px;">Filtered Inquiries (<?= $total_count ?? 0 ?>)</h6></li>
        <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['export'=>'excel','scope'=>'filtered'])) ?>"><i class="fas fa-file-excel text-success me-2"></i> Export to Excel (.xls)</a></li>
        <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['export'=>'csv','scope'=>'filtered'])) ?>"><i class="fas fa-file-csv text-primary me-2"></i> Export to CSV (.csv)</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><h6 class="dropdown-header text-uppercase" style="font-size:10px;">All Database Records (<?= $contact_stats['total'] ?>)</h6></li>
        <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['export'=>'excel','scope'=>'all'])) ?>"><i class="fas fa-database text-warning me-2"></i> Export All to Excel</a></li>
        <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['export'=>'csv','scope'=>'all'])) ?>"><i class="fas fa-database text-secondary me-2"></i> Export All to CSV</a></li>
      </ul>
    </div>
  </div>

  <?php if (!empty($_GET['saved'])): ?>
  <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:12px;"><i class="fas fa-check-circle me-2"></i> Note and reply status saved successfully.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

  <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'status_updated'): ?>
  <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:12px;"><i class="fas fa-check-circle me-2"></i> Inquiry status updated successfully.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

  <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
  <div class="alert alert-success alert-dismissible fade show mb-4" style="border-radius:12px;"><i class="fas fa-check-circle me-2"></i> Inquiry deleted permanently.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

  <?php if (!$view): ?>
  <!-- Top Metric Cards -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
      <a href="contacts.php" class="stat-card">
        <div class="d-flex align-items-center justify-content-between">
          <div class="stat-icon" style="background:rgba(28,34,128,.08);color:#1C2280;"><i class="fas fa-inbox"></i></div>
          <span style="font-size:11px;font-weight:700;color:#64748b;">All Time</span>
        </div>
        <div class="stat-value"><?= number_format($contact_stats['total']) ?></div>
        <div class="stat-label">Total Inquiries</div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a href="contacts.php?filter=new" class="stat-card">
        <div class="d-flex align-items-center justify-content-between">
          <div class="stat-icon" style="background:rgba(204,34,40,.08);color:#CC2228;"><i class="fas fa-bell"></i></div>
          <?php if ($contact_stats['unread'] > 0): ?>
            <span class="pulse-badge"><span class="pulse-dot"></span> Needs Action</span>
          <?php endif; ?>
        </div>
        <div class="stat-value" style="color:#CC2228;"><?= number_format($contact_stats['unread']) ?></div>
        <div class="stat-label">Unread / New Inquiries</div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a href="contacts.php?filter=replied" class="stat-card">
        <div class="d-flex align-items-center justify-content-between">
          <div class="stat-icon" style="background:rgba(14,165,233,.08);color:#0284c7;"><i class="fas fa-reply-all"></i></div>
          <span style="font-size:11px;font-weight:700;color:#0284c7;">Addressed</span>
        </div>
        <div class="stat-value" style="color:#0284c7;"><?= number_format($contact_stats['replied']) ?></div>
        <div class="stat-label">Replied / Resolved</div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a href="contacts.php" class="stat-card">
        <div class="d-flex align-items-center justify-content-between">
          <div class="stat-icon" style="background:rgba(16,185,129,.08);color:#10b981;"><i class="fas fa-calendar-day"></i></div>
          <span style="font-size:11px;font-weight:700;color:#10b981;">Today</span>
        </div>
        <div class="stat-value" style="color:#10b981;"><?= number_format($contact_stats['today']) ?></div>
        <div class="stat-label">Received Today</div>
      </a>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($view): ?>
  <!-- Detail View -->
  <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="contacts.php" style="color:#1C2280;font-size:14px;font-weight:600;text-decoration:none;"><i class="fas fa-arrow-left me-1"></i> Back to Inquiries</a>
    <div class="d-flex gap-2">
      <?php if (!empty($view['phone'])): 
        $detail_wa = format_whatsapp_url($view['phone'], "Hello {$view['name']}, thank you for contacting Vortexsoft Innovations regarding {$view['service']}. How can we assist you today?");
      ?>
        <a href="<?= $detail_wa ?>" target="_blank" class="btn btn-sm btn-success fw-bold" style="border-radius:8px;background:#25D366;border-color:#25D366;">
          <i class="fab fa-whatsapp me-1"></i> WhatsApp Client
        </a>
        <a href="tel:<?= preg_replace('/[^0-9+]/', '', $view['phone']) ?>" class="btn btn-sm btn-outline-primary fw-bold" style="border-radius:8px;">
          <i class="fas fa-phone-alt me-1"></i> Call <?= htmlspecialchars($view['phone']) ?>
        </a>
      <?php endif; ?>
      <button type="button" class="btn btn-sm btn-light border fw-bold" onclick="copyInquirySummary()" style="border-radius:8px;">
        <i class="fas fa-copy me-1"></i> Copy Summary
      </button>
    </div>
  </div>

  <div class="detail-card">
    <div class="row gy-3">
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Client Name</div><div style="font-size:17px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($view['name']) ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Email Address</div><div><a href="mailto:<?= htmlspecialchars($view['email']) ?>?subject=Regarding your inquiry at Vortexsoft Innovations" style="font-size:15px;color:#1C2280;font-weight:600;"><?= htmlspecialchars($view['email']) ?></a></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Phone Number</div><div style="font-size:15px;font-weight:600;"><?= htmlspecialchars($view['phone'] ?: 'Not provided') ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Service Requested</div><div><span class="badge" style="background:#e0e7ff;color:#3730a3;font-size:12px;padding:6px 12px;"><?= htmlspecialchars($view['service'] ?? 'General Inquiry') ?></span></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Submitted Timestamp</div><div style="font-size:14px;color:#475569;"><?= date('d M Y, h:i A', strtotime($view['created_at'])) ?> (<?= time_ago($view['created_at']) ?>)</div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Client IP &amp; Geolocation</div><div style="font-size:14px;color:#94a3b8;"><?= htmlspecialchars($view['ip_address'] ?? 'Unknown') ?></div></div>
      <div class="col-12"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:8px;">Message Content</div><div style="background:#f8f9ff;border-radius:12px;padding:22px;font-size:15px;color:#374151;line-height:1.75;white-space:pre-wrap;border:1px solid #e2e8f0;"><?= htmlspecialchars($view['message']) ?></div></div>

      <!-- Admin Notes & Status Form -->
      <div class="col-12 mt-2">
        <form method="POST" action="contacts.php?view=<?= $view['id'] ?>">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <label style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;display:block;margin-bottom:6px;">Internal Staff Notes &amp; Follow-up</label>
          <textarea name="note" class="form-control" rows="3" placeholder="Enter private staff notes, deal size, client follow-up status..." style="border-radius:10px;border:1.5px solid #e2e8f0;font-size:14px;"><?= htmlspecialchars($view['notes'] ?? '') ?></textarea>
          <div class="mt-3 d-flex gap-3 align-items-center flex-wrap">
            <button type="submit" class="btn" style="background:#1C2280;color:#fff;border-radius:8px;font-size:13px;font-weight:700;padding:10px 22px;"><i class="fas fa-save me-1"></i> Save Notes &amp; Status</button>
            <label style="font-size:13px;color:#1e293b;font-weight:700;display:flex;align-items:center;gap:6px;cursor:pointer;">
              <input type="checkbox" name="is_replied" value="1" <?= $view['is_replied'] ? 'checked' : '' ?> class="form-check-input"> Mark as Replied / Addressed
            </label>
            <a href="mailto:<?= htmlspecialchars($view['email']) ?>?subject=Re: Your Inquiry at Vortexsoft Innovations" class="btn" style="background:rgba(28,34,128,.08);color:#1C2280;border-radius:8px;font-size:13px;font-weight:700;padding:10px 22px;"><i class="fas fa-reply me-1"></i> Reply via Email</a>
            <a href="contacts.php?delete=<?= $view['id'] ?>&csrf=<?= htmlspecialchars(csrf_token()) ?>" class="btn action-btn-delete ms-auto" style="border-radius:8px;font-size:13px;font-weight:700;padding:10px 22px;" onclick="return confirm('Delete this inquiry permanently?');"><i class="fas fa-trash me-1"></i> Delete</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
  function copyInquirySummary() {
    var summary = "--- CLIENT INQUIRY SUMMARY ---\n" +
      "Name: <?= addslashes($view['name']) ?>\n" +
      "Email: <?= addslashes($view['email']) ?>\n" +
      "Phone: <?= addslashes($view['phone'] ?? 'N/A') ?>\n" +
      "Service: <?= addslashes($view['service'] ?? 'General') ?>\n" +
      "Date: <?= addslashes($view['created_at']) ?>\n" +
      "Message:\n<?= addslashes(str_replace(["\r", "\n"], ' ', $view['message'])) ?>\n";
    navigator.clipboard.writeText(summary).then(function() {
      alert("Inquiry summary copied to clipboard!");
    });
  }
  </script>

  <?php else: ?>
  <!-- List View -->
  <div class="table-card">
    <div class="table-card-header">
      <div class="d-flex align-items-center gap-2">
        <h5>All Inquiries (<?= number_format($total_count ?? 0) ?>)</h5>
        <?php if ($filter || $search): ?>
          <a href="contacts.php" class="btn btn-sm btn-light border py-0 px-2" style="font-size:11px;border-radius:100px;">Clear Filters <i class="fas fa-times ms-1"></i></a>
        <?php endif; ?>
      </div>
      <form action="contacts.php" method="GET" class="d-flex gap-2 flex-wrap">
        <input type="text" name="q" class="form-control form-control-sm" placeholder="Search name, email, phone, service..." value="<?= htmlspecialchars($search) ?>" style="border-radius:8px;min-width:240px;">
        <select name="filter" class="form-select form-select-sm" style="border-radius:8px;width:auto;">
          <option value="">All Statuses</option>
          <option value="new" <?= $filter === 'new' ? 'selected' : '' ?>>Unread Only</option>
          <option value="replied" <?= $filter === 'replied' ? 'selected' : '' ?>>Replied Only</option>
          <option value="read" <?= $filter === 'read' ? 'selected' : '' ?>>Read (Pending Reply)</option>
        </select>
        <button type="submit" class="btn btn-sm btn-primary" style="border-radius:8px;font-weight:600;"><i class="fas fa-filter me-1"></i> Filter</button>
      </form>
    </div>

    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Client</th>
            <th>Email</th>
            <th>Service</th>
            <th>Phone</th>
            <th>Received</th>
            <th>Status</th>
            <th style="text-align:right;">Quick Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($contacts)): ?>
          <tr>
            <td colspan="8" style="text-align:center;padding:50px;color:#94a3b8;">
              <i class="fas fa-inbox" style="font-size:36px;display:block;margin-bottom:12px;opacity:.3;"></i>
              No contact inquiries found matching criteria.
            </td>
          </tr>
          <?php else: ?>
          <?php foreach ($contacts as $c): 
            $c_phone_clean = !empty($c['phone']) ? preg_replace('/[^0-9]/', '', $c['phone']) : '';
            $c_wa_url = !empty($c['phone']) ? format_whatsapp_url($c['phone'], "Hello {$c['name']}, thank you for contacting Vortexsoft Innovations regarding " . ($c['service'] ?? 'our services') . ". How can we assist you?") : '';
          ?>
          <tr style="<?= !$c['is_read'] ? 'background:#fffbf5;' : '' ?>">
            <td style="color:#94a3b8;font-size:12px;font-weight:600;">#<?= $c['id'] ?></td>
            <td>
              <div style="font-weight:<?= !$c['is_read'] ? '700' : '600' ?>;color:#1e293b;"><?= htmlspecialchars($c['name']) ?></div>
            </td>
            <td>
              <a href="mailto:<?= htmlspecialchars($c['email']) ?>?subject=Regarding your inquiry at Vortexsoft Innovations" style="color:#1C2280;font-size:13px;text-decoration:none;">
                <?= htmlspecialchars($c['email']) ?>
              </a>
            </td>
            <td>
              <span class="badge" style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;font-size:11px;"><?= htmlspecialchars($c['service'] ?? 'General') ?></span>
            </td>
            <td style="font-size:12.5px;color:#475569;white-space:nowrap;">
              <?php if (!empty($c['phone'])): ?>
                <?= htmlspecialchars($c['phone']) ?>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td style="font-size:12px;color:#94a3b8;white-space:nowrap;"><?= time_ago($c['created_at']) ?></td>
            <td>
              <?php if (!$c['is_read']): ?>
                <span class="status-badge badge-new"><span class="pulse-dot"></span> New</span>
              <?php elseif ($c['is_replied']): ?>
                <span class="status-badge badge-replied"><i class="fas fa-check-double"></i> Replied</span>
              <?php else: ?>
                <span class="status-badge badge-read"><i class="fas fa-check"></i> Read</span>
              <?php endif; ?>
            </td>
            <td style="text-align:right;white-space:nowrap;">
              <div class="d-inline-flex gap-1 align-items-center">
                <?php if ($c_wa_url): ?>
                  <a href="<?= $c_wa_url ?>" target="_blank" class="action-btn action-btn-wa" title="1-Click WhatsApp Chat"><i class="fab fa-whatsapp"></i></a>
                <?php endif; ?>
                <?php if (!empty($c['phone'])): ?>
                  <a href="tel:<?= preg_replace('/[^0-9+]/', '', $c['phone']) ?>" class="action-btn action-btn-call" title="Call <?= htmlspecialchars($c['phone']) ?>"><i class="fas fa-phone-alt"></i></a>
                <?php endif; ?>
                <a href="mailto:<?= htmlspecialchars($c['email']) ?>?subject=Regarding your inquiry at Vortexsoft Innovations" class="action-btn" title="Email Client"><i class="fas fa-envelope"></i></a>
                <a href="contacts.php?toggle_read=<?= $c['id'] ?>&csrf=<?= htmlspecialchars(csrf_token()) ?>" class="action-btn" title="<?= $c['is_read'] ? 'Mark as Unread' : 'Mark as Read' ?>">
                  <i class="fas <?= $c['is_read'] ? 'fa-envelope' : 'fa-envelope-open' ?>"></i>
                </a>
                <a href="contacts.php?view=<?= $c['id'] ?>" class="action-btn" title="View Details"><i class="fas fa-eye"></i></a>
                <a href="contacts.php?delete=<?= $c['id'] ?>&csrf=<?= htmlspecialchars(csrf_token()) ?>" class="action-btn action-btn-delete" title="Delete Inquiry" onclick="return confirm('Delete this inquiry permanently?');"><i class="fas fa-trash"></i></a>
              </div>
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
</body>
</html>
