<?php
/**
 * Vortexsoft Innovations — Admin Unified Sidebar Component
 * /admin/_sidebar.php
 */

$sidebar_cur = basename($_SERVER['PHP_SELF']);
$sidebar_adm_name = $_SESSION['admin_name'] ?? 'Admin';
$sidebar_adm_role = $_SESSION['admin_role'] ?? 'admin';

// Compute counts if not already computed
if (!isset($sidebar_counts)) {
    $sidebar_counts = ['new_contacts' => 0, 'new_apps' => 0, 'jobs_active' => 0];
    if (isset($db) && $db) {
        try {
            $sidebar_counts['new_contacts'] = (int)$db->query("SELECT COUNT(*) FROM contact_inquiries WHERE is_read=0")->fetchColumn();
            $sidebar_counts['new_apps']     = (int)$db->query("SELECT COUNT(*) FROM job_applications WHERE status='new'")->fetchColumn();
            $sidebar_counts['jobs_active']  = (int)$db->query("SELECT COUNT(*) FROM jobs WHERE is_active=1")->fetchColumn();
        } catch (Throwable $t) {}
    }
}
?>

<!-- Mobile Header Bar -->
<div class="mobile-header">
  <img src="/logo-header.png" alt="Vortexsoft" style="height:32px;">
  <button type="button" class="btn text-white p-0" id="sidebarToggleBtn" aria-label="Toggle Navigation" style="font-size:20px;">
    <i class="fas fa-bars"></i>
  </button>
</div>

<!-- Admin Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-logo">
    <div>
      <img src="/logo-header.png?v=20260912" alt="Vortexsoft Innovations Private Limited">
      <div class="sub">Admin Panel</div>
    </div>
    <button type="button" class="btn text-white p-0 d-lg-none" id="sidebarCloseBtn" aria-label="Close Navigation">
      <i class="fas fa-times"></i>
    </button>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">Main</div>
    <a href="dashboard.php" class="sidebar-link <?= $sidebar_cur === 'dashboard.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard
    </a>
    <a href="contacts.php" class="sidebar-link <?= $sidebar_cur === 'contacts.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-envelope"></i></span> Inquiries
      <?php if (!empty($sidebar_counts['new_contacts'])): ?>
        <span class="badge" title="<?= (int)$sidebar_counts['new_contacts'] ?> Unread Inquiries"><?= (int)$sidebar_counts['new_contacts'] ?></span>
      <?php endif; ?>
    </a>
    <a href="applications.php" class="sidebar-link <?= $sidebar_cur === 'applications.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-briefcase"></i></span> Applications
      <?php if (!empty($sidebar_counts['new_apps'])): ?>
        <span class="badge" title="<?= (int)$sidebar_counts['new_apps'] ?> New Applications"><?= (int)$sidebar_counts['new_apps'] ?></span>
      <?php endif; ?>
    </a>
    <a href="jobs.php" class="sidebar-link <?= $sidebar_cur === 'jobs.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-clipboard-list"></i></span> Jobs
      <span style="margin-left:auto;background:rgba(28,34,128,.14);color:#a5b4fc;font-size:10px;font-weight:700;padding:2px 7px;border-radius:100px;"><?= (int)$sidebar_counts['jobs_active'] ?> active</span>
    </a>

    <div class="nav-section">Email &amp; Security</div>
    <a href="emails.php" class="sidebar-link <?= $sidebar_cur === 'emails.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-inbox"></i></span> Email Activity
    </a>
    <a href="email-accounts.php" class="sidebar-link <?= $sidebar_cur === 'email-accounts.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-mail-bulk"></i></span> Mail Accounts
    </a>
    <a href="email-templates.php" class="sidebar-link <?= $sidebar_cur === 'email-templates.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-file-code"></i></span> Templates
    </a>
    <a href="activity-log.php" class="sidebar-link <?= $sidebar_cur === 'activity-log.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-history"></i></span> Activity Log
    </a>
    <a href="users.php" class="sidebar-link <?= $sidebar_cur === 'users.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-users-cog"></i></span> Admin Users
    </a>

    <div class="nav-section">Content &amp; System</div>
    <a href="blog-posts.php" class="sidebar-link <?= $sidebar_cur === 'blog-posts.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-pen-alt"></i></span> Blog Posts
    </a>
    <a href="blog/generate.php" class="sidebar-link <?= $sidebar_cur === 'generate.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-robot"></i></span> AI Blog Generator
    </a>
    <a href="newsletter.php" class="sidebar-link <?= $sidebar_cur === 'newsletter.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-paper-plane"></i></span> Newsletter
    </a>
    <a href="audit_services.php" class="sidebar-link <?= $sidebar_cur === 'audit_services.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-check-circle"></i></span> 65-Service Audit
    </a>
    <a href="backup_db.php" class="sidebar-link <?= $sidebar_cur === 'backup_db.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-database"></i></span> DB Backup
    </a>
    <a href="settings.php" class="sidebar-link <?= $sidebar_cur === 'settings.php' ? 'active' : '' ?>">
      <span class="icon"><i class="fas fa-cog"></i></span> Settings
    </a>
    <a href="/index.php" target="_blank" class="sidebar-link">
      <span class="icon"><i class="fas fa-external-link-alt"></i></span> View Website
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="admin-user-info">
      <div class="admin-avatar"><?= strtoupper(substr($sidebar_adm_name, 0, 1)) ?></div>
      <div>
        <div class="admin-name"><?= htmlspecialchars($sidebar_adm_name) ?></div>
        <div class="admin-role"><?= str_replace('_', ' ', $sidebar_adm_role) ?></div>
      </div>
    </div>
    <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt me-2"></i> Sign Out</a>
  </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var toggleBtn = document.getElementById('sidebarToggleBtn');
  var closeBtn = document.getElementById('sidebarCloseBtn');
  var sidebar = document.getElementById('adminSidebar');
  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', function() {
      sidebar.classList.toggle('show');
    });
  }
  if (closeBtn && sidebar) {
    closeBtn.addEventListener('click', function() {
      sidebar.classList.remove('show');
    });
  }
});
</script>
