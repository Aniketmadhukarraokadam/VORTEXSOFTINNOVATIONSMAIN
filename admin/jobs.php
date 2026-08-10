<?php
/**
 * Vortexsoft Innovations — Admin: Jobs Management
 * CRUD for the `jobs` table — feeds careers.php with live postings
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();
admin_check();

$db = getDB();

// ── Handle POST actions ────────────────────────────────────────────
$success_msg = $error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error_msg = 'Security token mismatch. Please refresh and try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'add' || $action === 'edit') {
            $id          = (int)($_POST['job_id'] ?? 0);
            $title       = sanitize($_POST['title'] ?? '');
            $department  = sanitize($_POST['department'] ?? '');
            $type        = sanitize($_POST['type'] ?? 'Full Time');
            $location    = sanitize($_POST['location'] ?? '');
            $exp         = sanitize($_POST['experience_range'] ?? '');
            $skills_raw  = sanitize($_POST['skills'] ?? '');
            $description = strip_tags(trim($_POST['description'] ?? ''));
            $is_urgent   = isset($_POST['is_urgent']) ? 1 : 0;
            $is_active   = isset($_POST['is_active']) ? 1 : 0;
            $sort_order  = (int)($_POST['sort_order'] ?? 0);

            if (empty($title) || empty($department) || empty($description)) {
                $error_msg = 'Title, department, and description are required.';
            } else {
                if ($action === 'add') {
                    $stmt = $db->prepare("INSERT INTO jobs (title,department,type,location,experience_range,skills_json,description,is_urgent,is_active,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?)");
                    $stmt->execute([$title,$department,$type,$location,$exp,$skills_raw,$description,$is_urgent,$is_active,$sort_order]);
                    $success_msg = "Job posting &ldquo;{$title}&rdquo; added successfully.";
                } else {
                    $stmt = $db->prepare("UPDATE jobs SET title=?,department=?,type=?,location=?,experience_range=?,skills_json=?,description=?,is_urgent=?,is_active=?,sort_order=?,updated_at=NOW() WHERE id=?");
                    $stmt->execute([$title,$department,$type,$location,$exp,$skills_raw,$description,$is_urgent,$is_active,$sort_order,$id]);
                    $success_msg = "Job posting &ldquo;{$title}&rdquo; updated successfully.";
                }
            }
        }

        if ($action === 'delete') {
            $id = (int)($_POST['job_id'] ?? 0);
            if ($id > 0) {
                $db->prepare("DELETE FROM jobs WHERE id=?")->execute([$id]);
                $success_msg = 'Job posting deleted.';
            }
        }

        if ($action === 'toggle_active') {
            $id  = (int)($_POST['job_id'] ?? 0);
            $val = (int)($_POST['value'] ?? 0);
            $db->prepare("UPDATE jobs SET is_active=?,updated_at=NOW() WHERE id=?")->execute([$val,$id]);
        }

        if ($action === 'toggle_urgent') {
            $id  = (int)($_POST['job_id'] ?? 0);
            $val = (int)($_POST['value'] ?? 0);
            $db->prepare("UPDATE jobs SET is_urgent=?,updated_at=NOW() WHERE id=?")->execute([$val,$id]);
        }
    }
}

// Fetch all jobs
$jobs = $db->query("SELECT * FROM jobs ORDER BY sort_order ASC, created_at ASC")->fetchAll(PDO::FETCH_ASSOC);
$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jobs Management — Vortexsoft Admin</title>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="../assets/vendor/bootstrap.min.css">
<link rel="stylesheet" href="../assets/vendor/fontawesome/all.min.css">
<link rel="icon" href="../icon.jpg">
<style>
*{box-sizing:border-box}
body{margin:0;font-family:'Inter',sans-serif;background:#f0f2ff;color:#1a1d3a}
.admin-sidebar{position:fixed;top:0;left:0;width:260px;height:100vh;background:linear-gradient(180deg,#080B1A 0%,#1C2280 100%);padding:0;overflow-y:auto;z-index:100;display:flex;flex-direction:column}
.sidebar-logo{padding:24px 20px 20px;border-bottom:1px solid rgba(255,255,255,.08)}
.sidebar-logo .brand{font-size:20px;font-weight:800;letter-spacing:2px;color:#fff}.sidebar-logo .brand span{color:#CC2228}
.sidebar-logo .sub{font-size:11px;color:rgba(255,255,255,.4);letter-spacing:1px;margin-top:2px}
.sidebar-nav{padding:16px 12px;flex:1}
.sidebar-nav a{display:flex;align-items:center;gap:12px;padding:10px 14px;border-radius:10px;color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px;font-weight:500;margin-bottom:2px;transition:all .2s}
.sidebar-nav a:hover,.sidebar-nav a.active{background:rgba(255,255,255,.1);color:#fff}
.sidebar-nav a i{width:18px;text-align:center;font-size:14px;color:#CC2228;opacity:.9}
.sidebar-section{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:rgba(255,255,255,.3);padding:14px 14px 6px;margin-top:8px}
.main-content{margin-left:260px;padding:28px;min-height:100vh}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;flex-wrap:wrap;gap:12px}
.topbar h1{font-size:22px;font-weight:800;color:#1C2280;margin:0}
.page-breadcrumb{font-size:13px;color:#64748b;margin-top:2px}
.stats-row{display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap}
.stat-card{background:#fff;border-radius:14px;padding:18px 22px;flex:1;min-width:140px;border:1px solid #e8ecff;display:flex;align-items:center;gap:14px}
.stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.stat-num{font-size:22px;font-weight:800;color:#1C2280;line-height:1}
.stat-label{font-size:12px;color:#64748b;font-weight:600;margin-top:2px}
.card{background:#fff;border-radius:16px;border:1px solid #e8ecff;overflow:hidden}
.card-header{padding:18px 22px;border-bottom:1px solid #f0f2ff;display:flex;justify-content:space-between;align-items:center}
.card-header h5{margin:0;font-weight:700;font-size:16px;color:#1C2280}
.btn-add{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;border:none;border-radius:10px;padding:10px 20px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .2s;text-decoration:none}
.btn-add:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(28,34,128,.3);color:#fff}
table{width:100%;border-collapse:collapse}
thead th{padding:12px 16px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;background:#f8fafc;border-bottom:1px solid #e8ecff;text-align:left}
tbody td{padding:14px 16px;border-bottom:1px solid #f0f2ff;font-size:13.5px;vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:#fafbff}
.badge-type{background:rgba(28,34,128,.07);color:#1C2280;font-size:10px;font-weight:700;padding:3px 10px;border-radius:100px;white-space:nowrap}
.toggle-switch{position:relative;display:inline-block;width:40px;height:22px}
.toggle-switch input{opacity:0;width:0;height:0}
.toggle-slider{position:absolute;cursor:pointer;inset:0;background:#e2e8f0;border-radius:22px;transition:.3s}
.toggle-slider:before{position:absolute;content:"";height:16px;width:16px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s;box-shadow:0 1px 4px rgba(0,0,0,.15)}
input:checked+.toggle-slider{background:#10b981}
input:checked+.toggle-slider:before{transform:translateX(18px)}
.btn-action{border:none;background:none;cursor:pointer;padding:6px 10px;border-radius:8px;font-size:12px;font-weight:600;transition:all .15s}
.btn-edit{color:#1C2280;background:rgba(28,34,128,.06)}
.btn-edit:hover{background:rgba(28,34,128,.14)}
.btn-del{color:#CC2228;background:rgba(204,34,40,.06)}
.btn-del:hover{background:rgba(204,34,40,.14)}
.modal-content{border-radius:20px;border:none;overflow:hidden}
.modal-header{background:linear-gradient(135deg,#1C2280,#CC2228);padding:22px 28px;border:none}
.modal-title{color:#fff;font-size:17px;font-weight:700}
.form-label{font-size:13px;font-weight:600;color:#334155;margin-bottom:5px}
.form-control,.form-select{border:1.5px solid #dde2f5;border-radius:10px;font-size:13.5px;padding:10px 14px}
.form-control:focus,.form-select:focus{border-color:#1C2280;box-shadow:0 0 0 3px rgba(28,34,128,.08);outline:none}
.btn-submit{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;border:none;border-radius:10px;padding:12px 28px;font-weight:700;font-size:14px;cursor:pointer;transition:all .2s}
.btn-submit:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(28,34,128,.3)}
.alert{padding:12px 16px;border-radius:10px;font-size:13.5px;margin-bottom:20px;display:flex;align-items:center;gap:10px}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d}
.alert-danger{background:#fff0f0;border:1px solid #fecaca;color:#9b2c2c}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="admin-sidebar">
    <div class="sidebar-logo">
        <div class="brand">VORTEX<span>SOFT</span></div>
        <div class="sub">ADMIN PANEL</div>
    </div>
    <nav class="sidebar-nav">
        <div class="sidebar-section">Main</div>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="contacts.php"><i class="fas fa-envelope"></i> Contacts</a>
        <a href="applications.php"><i class="fas fa-briefcase"></i> Applications</a>
        <a href="jobs.php" class="active"><i class="fas fa-clipboard-list"></i> Jobs</a>
        <a href="blog-posts.php"><i class="fas fa-pen-nib"></i> Blog Posts</a>
        <a href="newsletter.php"><i class="fas fa-paper-plane"></i> Newsletter</a>
        <div class="sidebar-section">System</div>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="setup_db.php"><i class="fas fa-database"></i> DB Setup</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="topbar">
        <div>
            <h1><i class="fas fa-clipboard-list" style="color:#CC2228;margin-right:10px;font-size:20px;"></i>Jobs Management</h1>
            <div class="page-breadcrumb">Admin Panel &rsaquo; Jobs &mdash; Manage live job postings shown on careers.php</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="https://www.vortexsoftinnovations.com/careers.php" target="_blank" class="btn-add" style="background:linear-gradient(135deg,#10b981,#059669);">
                <i class="fas fa-external-link-alt"></i> View Careers Page
            </a>
            <button class="btn-add" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add New Job
            </button>
        </div>
    </div>

    <?php if ($success_msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success_msg ?></div><?php endif; ?>
    <?php if ($error_msg): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error_msg ?></div><?php endif; ?>

    <!-- Stats -->
    <div class="stats-row">
        <?php
        $total  = count($jobs);
        $active = count(array_filter($jobs, fn($j) => $j['is_active']));
        $urgent = count(array_filter($jobs, fn($j) => $j['is_urgent']));
        ?>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(28,34,128,.08);color:#1C2280;"><i class="fas fa-list"></i></div>
            <div><div class="stat-num"><?= $total ?></div><div class="stat-label">Total Jobs</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.08);color:#10b981;"><i class="fas fa-check"></i></div>
            <div><div class="stat-num"><?= $active ?></div><div class="stat-label">Active / Visible</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(204,34,40,.08);color:#CC2228;"><i class="fas fa-bolt"></i></div>
            <div><div class="stat-num"><?= $urgent ?></div><div class="stat-label">Urgent Openings</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(245,158,11,.08);color:#f59e0b;"><i class="fas fa-eye-slash"></i></div>
            <div><div class="stat-num"><?= $total - $active ?></div><div class="stat-label">Hidden / Draft</div></div>
        </div>
    </div>

    <!-- Jobs Table -->
    <div class="card">
        <div class="card-header">
            <h5>All Job Postings (<?= $total ?>)</h5>
            <span style="font-size:12px;color:#94a3b8;">Only <strong>active</strong> jobs appear on careers.php</span>
        </div>
        <?php if (empty($jobs)): ?>
        <div style="text-align:center;padding:60px;color:#94a3b8;">
            <i class="fas fa-clipboard-list" style="font-size:48px;opacity:.3;margin-bottom:12px;display:block;"></i>
            <p style="font-size:14px;">No jobs yet. Click <strong>Add New Job</strong> to create the first posting.</p>
        </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title / Department</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Exp.</th>
                    <th>Urgent</th>
                    <th>Active</th>
                    <th>Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($jobs as $j): ?>
            <tr>
                <td style="color:#94a3b8;font-weight:600;"><?= $j['id'] ?></td>
                <td>
                    <div style="font-weight:700;color:#1C2280;"><?= htmlspecialchars($j['title']) ?></div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;"><i class="fas fa-sitemap" style="font-size:10px;margin-right:4px;"></i><?= htmlspecialchars($j['department']) ?></div>
                </td>
                <td><span class="badge-type"><?= htmlspecialchars($j['type']) ?></span></td>
                <td style="font-size:13px;color:#475569;"><i class="fas fa-map-marker-alt" style="color:#CC2228;font-size:11px;margin-right:4px;"></i><?= htmlspecialchars($j['location']) ?></td>
                <td style="font-size:13px;color:#64748b;"><?= htmlspecialchars($j['experience_range'] ?? '—') ?></td>
                <td>
                    <label class="toggle-switch">
                        <input type="checkbox" <?= $j['is_urgent'] ? 'checked' : '' ?>
                            onchange="toggleField(<?= $j['id'] ?>,'urgent',this.checked?1:0)" style="--slider-on:#CC2228">
                        <span class="toggle-slider" style="<?= $j['is_urgent'] ? 'background:#CC2228' : '' ?>"></span>
                    </label>
                </td>
                <td>
                    <label class="toggle-switch">
                        <input type="checkbox" <?= $j['is_active'] ? 'checked' : '' ?>
                            onchange="toggleField(<?= $j['id'] ?>,'active',this.checked?1:0)">
                        <span class="toggle-slider"></span>
                    </label>
                </td>
                <td style="font-size:13px;color:#64748b;font-weight:600;"><?= $j['sort_order'] ?></td>
                <td>
                    <button class="btn-action btn-edit" onclick='openEditModal(<?= json_encode($j) ?>)'>
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-action btn-del" onclick="confirmDelete(<?= $j['id'] ?>, '<?= htmlspecialchars(addslashes($j['title'])) ?>')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="jobModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="jobModalTitle"><i class="fas fa-briefcase me-2"></i> Add New Job</h5>
        <button type="button" class="btn-close" style="filter:invert(1);opacity:.7;" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form id="jobForm" method="POST">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
          <input type="hidden" name="action" id="form_action" value="add">
          <input type="hidden" name="job_id" id="form_job_id" value="0">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Job Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="title" id="f_title" placeholder="e.g. PHP Developer" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Sort Order <span style="color:#94a3b8;font-weight:400;">(lower = first)</span></label>
              <input type="number" class="form-control" name="sort_order" id="f_sort" value="0" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label">Department <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="department" id="f_dept" placeholder="e.g. IT & Software" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Job Type</label>
              <select class="form-select" name="type" id="f_type">
                <option>Full Time</option>
                <option>Part Time</option>
                <option>Contract</option>
                <option>Internship</option>
                <option>Remote</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Location</label>
              <input type="text" class="form-control" name="location" id="f_location" placeholder="e.g. Bengaluru">
            </div>
            <div class="col-md-4">
              <label class="form-label">Experience Range</label>
              <input type="text" class="form-control" name="experience_range" id="f_exp" placeholder="e.g. 1-3 years">
            </div>
            <div class="col-md-8">
              <label class="form-label">Skills <span style="color:#94a3b8;font-weight:400;">(comma separated)</span></label>
              <input type="text" class="form-control" name="skills" id="f_skills" placeholder="e.g. PHP, Laravel, MySQL">
            </div>
            <div class="col-12">
              <label class="form-label">Job Description <span class="text-danger">*</span></label>
              <textarea class="form-control" name="description" id="f_desc" rows="5" placeholder="Describe the role, responsibilities, and requirements..." required></textarea>
            </div>
            <div class="col-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_urgent" id="f_urgent" role="switch">
                <label class="form-check-label" for="f_urgent" style="font-size:13px;"><strong>Mark as Urgent</strong> <span style="color:#64748b;">(shows lightning badge)</span></label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="f_active" role="switch" checked>
                <label class="form-check-label" for="f_active" style="font-size:13px;"><strong>Active</strong> <span style="color:#64748b;">(visible on careers page)</span></label>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer" style="padding:16px 24px;border-top:1px solid #f0f2ff;">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" form="jobForm" class="btn-submit"><i class="fas fa-save me-2"></i>Save Job Posting</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete confirm form -->
<form id="deleteForm" method="POST" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="job_id" id="delete_job_id" value="">
</form>

<!-- Toggle form -->
<form id="toggleForm" method="POST" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
    <input type="hidden" name="action" id="toggle_action" value="">
    <input type="hidden" name="job_id" id="toggle_job_id" value="">
    <input type="hidden" name="value" id="toggle_value" value="">
</form>

<script src="../assets/vendor/bootstrap.bundle.min.js"></script>
<script>
function openAddModal() {
    document.getElementById('jobModalTitle').innerHTML = '<i class="fas fa-plus me-2"></i> Add New Job';
    document.getElementById('form_action').value = 'add';
    document.getElementById('form_job_id').value = '0';
    document.getElementById('jobForm').reset();
    document.getElementById('f_active').checked = true;
    new bootstrap.Modal(document.getElementById('jobModal')).show();
}
function openEditModal(job) {
    document.getElementById('jobModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i> Edit: ' + job.title;
    document.getElementById('form_action').value = 'edit';
    document.getElementById('form_job_id').value = job.id;
    document.getElementById('f_title').value    = job.title || '';
    document.getElementById('f_dept').value     = job.department || '';
    document.getElementById('f_type').value     = job.type || 'Full Time';
    document.getElementById('f_location').value = job.location || '';
    document.getElementById('f_exp').value      = job.experience_range || '';
    document.getElementById('f_skills').value   = job.skills_json || '';
    document.getElementById('f_desc').value     = job.description || '';
    document.getElementById('f_sort').value     = job.sort_order || 0;
    document.getElementById('f_urgent').checked = job.is_urgent == 1;
    document.getElementById('f_active').checked = job.is_active == 1;
    new bootstrap.Modal(document.getElementById('jobModal')).show();
}
function confirmDelete(id, title) {
    if (confirm('Delete "' + title + '"?\n\nThis cannot be undone.')) {
        document.getElementById('delete_job_id').value = id;
        document.getElementById('deleteForm').submit();
    }
}
function toggleField(id, field, value) {
    document.getElementById('toggle_action').value = 'toggle_' + field;
    document.getElementById('toggle_job_id').value = id;
    document.getElementById('toggle_value').value  = value;
    document.getElementById('toggleForm').submit();
}
</script>
</body>
</html>
