<?php
/**
 * Vortexsoft Innovations — Admin User Management
 * Multi-role admin management (Super Admin, HR Admin, Content Admin)
 * Support for adding, editing, disabling, and permanently deleting user accounts from scratch.
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();
admin_require_role(['super_admin']);

$db = getDB();
$message = $error = '';
$current_admin_id = (int)($_SESSION['vortex_admin_id'] ?? $_SESSION['admin_id'] ?? $_SESSION[ADMIN_USER_KEY] ?? 0);

if (!empty($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $del_user = sanitize($_GET['user'] ?? 'User');
    $message = "Admin user account <strong>" . htmlspecialchars($del_user) . "</strong> has been permanently deleted from scratch.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Security token mismatch. Please refresh and try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'add' || $action === 'edit') {
            $id        = (int)($_POST['user_id'] ?? 0);
            $username  = sanitize($_POST['username'] ?? '');
            $email     = sanitize_email($_POST['email'] ?? '');
            $full_name = sanitize($_POST['full_name'] ?? '');
            $role      = sanitize($_POST['role'] ?? 'admin');
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $password  = $_POST['password'] ?? '';

            if (empty($username) || empty($email)) {
                $error = 'Username and email are required.';
            } else {
                if ($action === 'add') {
                    if (empty($password)) {
                        $error = 'Password is required for new admin users.';
                    } else {
                        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
                        $stmt = $db->prepare("INSERT INTO admin_users (username, password_hash, email, full_name, role, is_active) VALUES (?,?,?,?,?,?)");
                        try {
                            $stmt->execute([$username, $hash, $email, $full_name, $role, $is_active]);
                            log_admin_activity('Admin Created', "Created admin user: {$username} ({$role})");
                            $message = "Admin user <strong>" . htmlspecialchars($username) . "</strong> created successfully.";
                        } catch (Throwable $e) {
                            $error = 'Failed to create user (username or email may already exist).';
                        }
                    }
                } else {
                    // Edit existing
                    if (!empty($password)) {
                        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
                        $stmt = $db->prepare("UPDATE admin_users SET username=?, email=?, full_name=?, role=?, is_active=?, password_hash=? WHERE id=?");
                        $stmt->execute([$username, $email, $full_name, $role, $is_active, $hash, $id]);
                        log_admin_activity('Admin Password Reset', "Updated password for admin user ID: {$id}");
                    } else {
                        $stmt = $db->prepare("UPDATE admin_users SET username=?, email=?, full_name=?, role=?, is_active=? WHERE id=?");
                        $stmt->execute([$username, $email, $full_name, $role, $is_active, $id]);
                    }
                    log_admin_activity('Admin Updated', "Updated admin user ID: {$id} ({$username})");
                    $message = "Admin user <strong>" . htmlspecialchars($username) . "</strong> updated successfully.";
                }
            }
        }

        // Toggle active status
        if ($action === 'toggle_active') {
            $id = (int)($_POST['user_id'] ?? 0);
            $val = (int)($_POST['value'] ?? 0);
            if ($id > 0) {
                if ($id === $current_admin_id && $val === 0) {
                    $error = "You cannot disable your own active account while logged in.";
                } else {
                    $db->prepare("UPDATE admin_users SET is_active=? WHERE id=?")->execute([$val, $id]);
                    log_admin_activity('Admin Status Toggled', "Toggled status to {$val} for user ID: {$id}");
                    $message = "User status updated.";
                }
            }
        }

        // Permanently Delete User Account From Scratch
        if ($action === 'delete') {
            $id = (int)($_POST['user_id'] ?? 0);

            if ($id <= 0) {
                $error = 'Invalid user account ID.';
            } elseif ($id === $current_admin_id) {
                $error = 'Security Protection: You cannot delete your own account while currently logged in.';
            } else {
                $uStmt = $db->prepare("SELECT id, username, role FROM admin_users WHERE id = ?");
                $uStmt->execute([$id]);
                $targetUser = $uStmt->fetch(PDO::FETCH_ASSOC);

                if (!$targetUser) {
                    $error = 'User account not found in database.';
                } else {
                    if ($targetUser['role'] === 'super_admin') {
                        $superCount = (int)$db->query("SELECT COUNT(*) FROM admin_users WHERE role = 'super_admin'")->fetchColumn();
                        if ($superCount <= 1) {
                            $error = 'Cannot delete this account: At least one Super Administrator must remain active in the system.';
                        }
                    }

                    if (empty($error)) {
                        $delStmt = $db->prepare("DELETE FROM admin_users WHERE id = ?");
                        $delStmt->execute([$id]);
                        log_admin_activity('Admin Deleted', "Permanently deleted admin user account from scratch: ID {$id} ({$targetUser['username']})");
                        $message = "User account <strong>" . htmlspecialchars($targetUser['username']) . "</strong> has been permanently deleted from scratch.";
                    }
                }
            }
        }

        // Bulk Delete User Accounts From Scratch
        if ($action === 'bulk_delete') {
            $raw_ids = $_POST['selected_ids'] ?? [];
            $ids = array_values(array_filter(array_map('intval', (array)$raw_ids), fn($i) => $i > 0));
            // Exclude own account
            $ids = array_values(array_filter($ids, fn($i) => $i !== $current_admin_id));

            if (empty($ids)) {
                $error = 'No valid user accounts selected for deletion (you cannot delete your own account).';
            } else {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $checkStmt = $db->prepare("SELECT COUNT(*) FROM admin_users WHERE role = 'super_admin' AND id NOT IN ($placeholders)");
                $checkStmt->execute($ids);
                $remainingSuper = (int)$checkStmt->fetchColumn();

                if ($remainingSuper < 1) {
                    $error = 'Cannot delete selected accounts: At least one Super Administrator account must remain active.';
                } else {
                    $nameStmt = $db->prepare("SELECT username FROM admin_users WHERE id IN ($placeholders)");
                    $nameStmt->execute($ids);
                    $names = $nameStmt->fetchAll(PDO::FETCH_COLUMN);

                    $delStmt = $db->prepare("DELETE FROM admin_users WHERE id IN ($placeholders)");
                    $delStmt->execute($ids);
                    $delCount = $delStmt->rowCount();

                    log_admin_activity('Admin Bulk Delete', "Permanently deleted {$delCount} user account(s) from scratch: " . implode(', ', $names));
                    $message = "Permanently deleted <strong>{$delCount}</strong> user account(s) from scratch (" . htmlspecialchars(implode(', ', $names)) . ").";
                }
            }
        }
    }
}

$users = $db->query("SELECT id, username, email, full_name, role, is_active, last_login, login_count, created_at FROM admin_users ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin User Management — Vortexsoft Admin</title>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="../assets/vendor/bootstrap.min.css">
<link rel="stylesheet" href="../assets/vendor/fontawesome/all.min.css">
<link rel="icon" type="image/x-icon" href="/favicon.ico?v=20260912">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=20260912">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=20260912">
<link rel="icon" type="image/jpeg" sizes="1024x1024" href="/icon.jpg?v=20260912">
<style>
body{font-family:'Inter',sans-serif;background:#f0f2ff;margin:0;color:#1e293b}
.admin-sidebar{position:fixed;top:0;left:0;width:260px;height:100vh;background:linear-gradient(180deg,#080B1A 0%,#1C2280 100%);padding:0;color:#fff}
.sidebar-logo{padding:20px;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:space-between}
.sidebar-logo img{height:44px;object-fit:contain}
.sidebar-logo .sub{font-size:11px;color:rgba(255,255,255,.4);letter-spacing:1px;margin-top:6px}
.sidebar-nav{padding:16px 12px;overflow-y:auto;height:calc(100vh - 85px)}
.sidebar-nav a{display:flex;align-items:center;gap:12px;padding:10px 14px;border-radius:10px;color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px;font-weight:500;margin-bottom:2px}
.sidebar-nav a:hover,.sidebar-nav a.active{background:rgba(255,255,255,.1);color:#fff}
.sidebar-nav a i{width:18px;text-align:center;color:#CC2228}
.sidebar-section{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:rgba(255,255,255,.3);padding:14px 14px 6px}
.main-content{margin-left:260px;padding:28px;min-height:100vh}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.topbar h1{font-size:22px;font-weight:800;color:#1C2280;margin:0}
.card{background:#fff;border-radius:16px;border:1px solid #e8ecff;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.02)}
.card-header{padding:16px 22px;border-bottom:1px solid #f0f2ff;display:flex;justify-content:space-between;align-items:center;font-weight:700;color:#1C2280;flex-wrap:wrap;gap:10px}
table{width:100%;border-collapse:collapse}
th,td{padding:12px 18px;text-align:left;font-size:13.5px;border-bottom:1px solid #f0f2ff;vertical-align:middle}
th{background:#f8fafc;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:.5px}
.btn-primary-custom{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;border:none;border-radius:10px;padding:9px 18px;font-size:13px;font-weight:700;cursor:pointer;transition:.2s}
.btn-primary-custom:hover{opacity:.95;transform:translateY(-1px)}
.role-badge{font-size:11px;font-weight:700;padding:3px 10px;border-radius:100px;background:rgba(28,34,128,.08);color:#1C2280}
.form-check-input{cursor:pointer;border-color:#cbd5e1}
.form-check-input:checked{background-color:#CC2228;border-color:#CC2228}
@media(max-width:992px){
    .admin-sidebar{position:static;width:100%;height:auto}
    .main-content{margin-left:0;padding:20px}
}
</style>
</head>
<body>
<div class="admin-sidebar">
    <div class="sidebar-logo">
        <div>
            <img src="/logo-header.png?v=20260912" alt="Vortexsoft Innovations Private Limited">
            <div class="sub">Admin Panel</div>
        </div>
    </div>
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
        <a href="users.php" class="active"><i class="fas fa-users-cog"></i> Admin Users</a>
        <a href="backup_db.php"><i class="fas fa-database"></i> DB Backup</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>
<div class="main-content">
    <div class="topbar">
        <div>
            <h1><i class="fas fa-users-cog" style="color:#CC2228;margin-right:10px;"></i>Admin User Management</h1>
            <div style="font-size:13px;color:#64748b;margin-top:2px;">Manage administrator accounts, roles, access rights, and delete accounts from scratch.</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" id="bulkDeleteBtn" class="btn btn-sm btn-danger d-none" style="border-radius:10px;font-weight:700;font-size:13px;padding:9px 18px;" onclick="openBulkDeleteModal()">
                <i class="fas fa-trash-alt me-1"></i> Delete Selected (<span id="selectedUserCount">0</span>)
            </button>
            <button class="btn-primary-custom" onclick="openAddModal()"><i class="fas fa-user-plus me-2"></i>Add New Admin</button>
        </div>
    </div>

    <?php if($message): ?><div class="alert alert-success" style="padding:14px;border-radius:10px;margin-bottom:20px;background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;"><i class="fas fa-check-circle me-2"></i><?= $message ?></div><?php endif; ?>
    <?php if($error): ?><div class="alert alert-danger" style="padding:14px;border-radius:10px;margin-bottom:20px;background:#fff0f0;color:#991b1b;border:1px solid #fecaca;"><i class="fas fa-exclamation-triangle me-2"></i><?= $error ?></div><?php endif; ?>

    <div class="card">
        <div class="card-header">
            <span>Admin Users List (<?= count($users) ?>)</span>
            <span style="font-size:12px;color:#94a3b8;font-weight:400;"><i class="fas fa-shield-alt me-1"></i> Passwords are salted bcrypt hashes &bull; Permanent deletion completely erases account records</span>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center;">
                            <input type="checkbox" id="masterUserCheck" class="form-check-input" title="Select all deleteable accounts">
                        </th>
                        <th>#</th>
                        <th>User / Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Logins</th>
                        <th style="min-width:180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr>
                        <td style="text-align:center;">
                            <input type="checkbox" class="form-check-input user-check" value="<?= (int)$u['id'] ?>" data-name="<?= htmlspecialchars($u['username']) ?>" <?= $u['id'] == $current_admin_id ? 'disabled title="You cannot delete your own account"' : '' ?>>
                        </td>
                        <td style="color:#94a3b8;font-weight:600;">#<?= $u['id'] ?></td>
                        <td>
                            <div style="font-weight:700;color:#1C2280;"><?= htmlspecialchars($u['username']) ?></div>
                            <div style="font-size:12px;color:#64748b;"><?= htmlspecialchars($u['full_name'] ?? '—') ?></div>
                        </td>
                        <td><a href="mailto:<?= htmlspecialchars($u['email']) ?>" style="color:#1C2280;text-decoration:none;font-weight:500;"><?= htmlspecialchars($u['email']) ?></a></td>
                        <td><span class="role-badge"><?= htmlspecialchars(str_replace('_', ' ', strtoupper($u['role']))) ?></span></td>
                        <td>
                            <?php if($u['is_active']): ?>
                            <span class="badge bg-success" style="font-size:11px;">Active</span>
                            <?php else: ?>
                            <span class="badge bg-secondary" style="font-size:11px;">Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12.5px;color:#64748b;"><?= $u['last_login'] ?? 'Never' ?></td>
                        <td style="font-size:12.5px;color:#64748b;"><?= $u['login_count'] ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <button class="btn btn-sm btn-outline-primary" onclick='openEditModal(<?= json_encode($u) ?>)' title="Edit Account Details"><i class="fas fa-edit"></i> Edit</button>
                                <?php if($u['id'] == $current_admin_id): ?>
                                <span class="badge bg-light text-muted border" style="font-size:11px;padding:6px 10px;" title="Currently logged in as this user"><i class="fas fa-user-shield me-1"></i> Current User</span>
                                <?php else: ?>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick='confirmDeleteUser(<?= (int)$u['id'] ?>, <?= json_encode($u['username']) ?>, <?= json_encode($u['role']) ?>)' title="Delete this account from scratch">
                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add/Edit -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;">
      <div class="modal-header" style="background:linear-gradient(135deg,#1C2280,#CC2228);color:#fff;">
        <h5 class="modal-title" id="modalTitle">Add Admin User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="action" id="userAction" value="add">
        <input type="hidden" name="user_id" id="userId" value="0">
        <div class="modal-body p-4">
            <div class="mb-3">
                <label class="form-label fw-bold">Username</label>
                <input type="text" name="username" id="u_username" class="form-control" required style="border-radius:8px;">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Full Name</label>
                <input type="text" name="full_name" id="u_fullname" class="form-control" style="border-radius:8px;">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Email Address</label>
                <input type="email" name="email" id="u_email" class="form-control" required style="border-radius:8px;">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Role</label>
                <select name="role" id="u_role" class="form-select" style="border-radius:8px;">
                    <option value="super_admin">Super Admin (Full Access)</option>
                    <option value="admin">HR & Operations Admin</option>
                    <option value="viewer">Content Admin (Jobs & Blog only)</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Password <span style="font-weight:400;font-size:12px;color:#64748b;">(leave blank to keep existing password)</span></label>
                <input type="password" name="password" class="form-control" placeholder="********" style="border-radius:8px;">
            </div>
            <div class="form-check form-switch mt-3">
                <input class="form-check-input" type="checkbox" name="is_active" id="u_active" checked>
                <label class="form-check-label" for="u_active">Account Active</label>
            </div>
        </div>
        <div class="modal-footer" style="padding:16px 24px;">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-primary-custom">Save Account</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Delete User Account From Scratch -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;box-shadow:0 20px 50px rgba(0,0,0,0.3);">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" style="font-weight:700;"><i class="fas fa-exclamation-triangle me-2"></i> Delete Account From Scratch</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="user_id" id="deleteUserId" value="0">
        <div class="modal-body p-4">
          <div class="alert alert-danger" style="border-radius:10px;font-size:13.5px;line-height:1.6;">
            <strong>Warning: Permanent Deletion From Scratch!</strong><br>
            You are about to permanently delete user account <strong id="deleteUserTargetName"></strong> from the MySQL database. All permissions, sessions, and records associated with this account will be erased completely.
          </div>
          <p style="font-size:13.5px;color:#475569;margin-bottom:8px;">
            To confirm permanent deletion from scratch, type <strong class="text-danger">DELETE</strong> in the box below:
          </p>
          <input type="text" id="confirmDeleteInput" class="form-control" placeholder="Type DELETE to confirm" autocomplete="off" oninput="toggleDeleteBtn(this.value)" style="border-radius:8px;font-weight:600;letter-spacing:1px;">
        </div>
        <div class="modal-footer" style="padding:16px 24px;background:#f8fafc;">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px;">Cancel</button>
          <button type="submit" id="btnConfirmDelete" class="btn btn-danger" disabled style="border-radius:8px;font-weight:700;">
            <i class="fas fa-trash-alt me-1"></i> Delete From Scratch
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Bulk Delete Users From Scratch -->
<div class="modal fade" id="bulkDeleteUserModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;box-shadow:0 20px 50px rgba(0,0,0,0.3);">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" style="font-weight:700;"><i class="fas fa-users-slash me-2"></i> Bulk Delete User Accounts</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="bulkDeleteUserForm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="action" value="bulk_delete">
        <div id="bulkDeleteIdsContainer"></div>
        <div class="modal-body p-4">
          <div class="alert alert-danger" style="border-radius:10px;font-size:13.5px;line-height:1.6;">
            <strong>Warning: Permanent Bulk Deletion!</strong><br>
            You have selected <strong id="bulkDeleteCountText">0</strong> user account(s) for permanent deletion. They will be erased from the database completely.
          </div>
          <p style="font-size:13.5px;color:#475569;">
            Are you sure you want to delete these user accounts from scratch? This cannot be undone.
          </p>
        </div>
        <div class="modal-footer" style="padding:16px 24px;background:#f8fafc;">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px;">Cancel</button>
          <button type="submit" class="btn btn-danger" style="border-radius:8px;font-weight:700;">
            <i class="fas fa-trash-alt me-1"></i> Yes, Delete All Selected
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="../assets/vendor/bootstrap.bundle.min.js"></script>
<script>
function openAddModal(){
    document.getElementById('modalTitle').innerText = 'Add New Admin';
    document.getElementById('userAction').value = 'add';
    document.getElementById('userId').value = '0';
    document.getElementById('u_username').value = '';
    document.getElementById('u_fullname').value = '';
    document.getElementById('u_email').value = '';
    document.getElementById('u_active').checked = true;
    new bootstrap.Modal(document.getElementById('userModal')).show();
}

function openEditModal(u){
    document.getElementById('modalTitle').innerText = 'Edit Admin: ' + u.username;
    document.getElementById('userAction').value = 'edit';
    document.getElementById('userId').value = u.id;
    document.getElementById('u_username').value = u.username;
    document.getElementById('u_fullname').value = u.full_name || '';
    document.getElementById('u_email').value = u.email;
    document.getElementById('u_role').value = u.role;
    document.getElementById('u_active').checked = (u.is_active == 1);
    new bootstrap.Modal(document.getElementById('userModal')).show();
}

function confirmDeleteUser(id, username, role){
    document.getElementById('deleteUserId').value = id;
    document.getElementById('deleteUserTargetName').textContent = username + ' (' + role.replace('_', ' ').toUpperCase() + ')';
    document.getElementById('confirmDeleteInput').value = '';
    document.getElementById('btnConfirmDelete').disabled = true;
    new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
}

function toggleDeleteBtn(val){
    document.getElementById('btnConfirmDelete').disabled = (val.trim().toUpperCase() !== 'DELETE');
}

// Master checkbox & Bulk selection
const masterCheck = document.getElementById('masterUserCheck');
const userChecks  = document.querySelectorAll('.user-check');
const bulkBtn     = document.getElementById('bulkDeleteBtn');
const countSpan   = document.getElementById('selectedUserCount');

function updateBulkState(){
    const checked = Array.from(userChecks).filter(c => c.checked && !c.disabled);
    if (countSpan) countSpan.textContent = checked.length;
    if (bulkBtn) {
        if (checked.length > 0) {
            bulkBtn.classList.remove('d-none');
        } else {
            bulkBtn.classList.add('d-none');
        }
    }
}

masterCheck?.addEventListener('change', function(){
    userChecks.forEach(c => {
        if (!c.disabled) c.checked = masterCheck.checked;
    });
    updateBulkState();
});

userChecks.forEach(c => c.addEventListener('change', updateBulkState));

function openBulkDeleteModal(){
    const checked = Array.from(userChecks).filter(c => c.checked && !c.disabled);
    if (checked.length === 0) return;
    const container = document.getElementById('bulkDeleteIdsContainer');
    container.innerHTML = '';
    checked.forEach(c => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'selected_ids[]';
        inp.value = c.value;
        container.appendChild(inp);
    });
    document.getElementById('bulkDeleteCountText').textContent = checked.length;
    new bootstrap.Modal(document.getElementById('bulkDeleteUserModal')).show();
}
</script>
</body>
</html>