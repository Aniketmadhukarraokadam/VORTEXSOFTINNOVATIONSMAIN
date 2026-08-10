<?php
/**
 * Vortexsoft Innovations — Admin: Blog Posts Manager
 * /admin/blog-posts.php
 */

session_start();
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
admin_check();

$db = getDB();
$posts = [];
$msg = '';

if ($db) {
    try {
        // Toggle Publish
        if (isset($_GET['toggle'])) {
            $pid = (int)$_GET['toggle'];
            $db->prepare("UPDATE blog_posts SET is_published = NOT is_published WHERE id=:id")->execute([':id'=>$pid]);
            header('Location: blog-posts.php?msg=updated'); exit;
        }

        // Delete Post
        if (isset($_GET['delete'])) {
            $pid = (int)$_GET['delete'];
            $db->prepare("DELETE FROM blog_posts WHERE id=:id")->execute([':id'=>$pid]);
            header('Location: blog-posts.php?msg=deleted'); exit;
        }

        // Create or Edit Post
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title      = sanitize($_POST['title'] ?? '');
            $slug       = slugify($_POST['slug'] ?? $title);
            $category   = sanitize($_POST['category'] ?? 'General');
            $excerpt    = sanitize($_POST['excerpt'] ?? '');
            $content    = $_POST['content'] ?? ''; // rich content
            $author     = sanitize($_POST['author'] ?? 'Vortexsoft Team');
            $published  = isset($_POST['is_published']) ? 1 : 0;
            $featured   = isset($_POST['is_featured']) ? 1 : 0;
            $edit_id    = (int)($_POST['edit_id'] ?? 0);

            if (!empty($title)) {
                if ($edit_id) {
                    $stmt = $db->prepare("UPDATE blog_posts SET title=:t, slug=:s, category=:c, excerpt=:e, content=:cnt, author=:a, is_published=:p, is_featured=:f WHERE id=:id");
                    $stmt->execute([':t'=>$title,':s'=>$slug,':c'=>$category,':e'=>$excerpt,':cnt'=>$content,':a'=>$author,':p'=>$published,':f'=>$featured,':id'=>$edit_id]);
                } else {
                    $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, category, excerpt, content, author, is_published, is_featured, published_at) VALUES (:t, :s, :c, :e, :cnt, :a, :p, :f, NOW())");
                    $stmt->execute([':t'=>$title,':s'=>$slug,':c'=>$category,':e'=>$excerpt,':cnt'=>$content,':a'=>$author,':p'=>$published,':f'=>$featured]);
                }
                header('Location: blog-posts.php?msg=saved'); exit;
            }
        }

        $posts = $db->query("SELECT id,title,slug,category,author,views,is_published,is_featured,published_at FROM blog_posts ORDER BY created_at DESC")->fetchAll();
    } catch (PDOException $e) { error_log($e->getMessage()); }
}

$edit_post = null;
if (isset($_GET['edit']) && $db) {
    $eid = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id=:id");
    $stmt->execute([':id'=>$eid]);
    $edit_post = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog Posts — Vortexsoft Admin Panel</title>
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
.badge-published{background:#f0fdf4;color:#10b981}
.badge-draft{background:#fff0f0;color:#CC2228}
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
    <a href="blog-posts.php" class="sidebar-link active"><span class="icon"><i class="fas fa-pen-alt"></i></span> Blog Posts</a>
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
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
      <h1><i class="fas fa-pen-alt me-2" style="color:#CC2228;"></i> Blog Posts Manager</h1>
      <div style="font-size:13px;color:#64748b;">Create, edit, and publish blog posts to your website.</div>
    </div>
    <button class="btn" style="background:#1C2280;color:#fff;border-radius:10px;font-weight:700;font-size:13px;padding:10px 20px;" onclick="document.getElementById('postEditorModal').style.display='block';">
      <i class="fas fa-plus me-1"></i> New Blog Post
    </button>
  </div>

  <?php if (!empty($_GET['msg'])): ?>
  <div class="alert alert-success mb-4" style="border-radius:12px;"><i class="fas fa-check-circle me-2"></i> Post saved/updated successfully.</div>
  <?php endif; ?>

  <!-- Blog Posts Table -->
  <div class="table-card">
    <div class="table-card-header">
      <h5>All Posts (<?= count($posts) ?>)</h5>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>#</th><th>Title</th><th>Category</th><th>Author</th><th>Views</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if(empty($posts)): ?>
          <tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;"><i class="fas fa-pen-alt" style="font-size:32px;display:block;margin-bottom:12px;opacity:.3;"></i> No blog posts yet. Click "New Blog Post" to publish one.</td></tr>
          <?php else: ?>
          <?php foreach($posts as $p): ?>
          <tr>
            <td style="color:#94a3b8;font-size:12px;">#<?= $p['id'] ?></td>
            <td style="font-weight:600;max-width:320px;"><?= htmlspecialchars($p['title']) ?></td>
            <td><span class="badge" style="background:rgba(28,34,128,.08);color:#1C2280;"><?= htmlspecialchars($p['category']) ?></span></td>
            <td style="font-size:13px;color:#64748b;"><?= htmlspecialchars($p['author']) ?></td>
            <td style="font-size:13px;"><?= number_format($p['views']) ?></td>
            <td><span class="status-badge <?= $p['is_published']?'badge-published':'badge-draft' ?>"><?= $p['is_published']?'Published':'Draft' ?></span></td>
            <td>
              <a href="blog-posts.php?toggle=<?= $p['id'] ?>" class="action-btn" style="background:rgba(16,185,129,.1);color:#10b981;" title="Toggle Publish"><i class="fas fa-eye"></i></a>
              <a href="blog-posts.php?edit=<?= $p['id'] ?>" class="action-btn" style="background:rgba(28,34,128,.1);color:#1C2280;"><i class="fas fa-edit"></i> Edit</a>
              <a href="blog-posts.php?delete=<?= $p['id'] ?>" class="action-btn" style="background:rgba(204,34,40,.1);color:#CC2228;" onclick="return confirm('Delete this post?');"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Create/Edit Form Card -->
  <div style="background:#fff;border-radius:16px;border:1px solid #e8ecff;padding:32px;margin-top:24px;<?= $edit_post?'':'display:none;' ?>" id="postEditorModal">
    <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#1C2280;margin-bottom:20px;"><?= $edit_post ? 'Edit Blog Post #'.$edit_post['id'] : 'Create New Blog Post' ?></h4>
    <form method="POST" action="blog-posts.php">
      <input type="hidden" name="edit_id" value="<?= $edit_post['id'] ?? 0 ?>">
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label fw-semibold">Post Title <span class="text-danger">*</span></label>
          <input type="text" name="title" class="form-control" placeholder="Enter post title" required value="<?= htmlspecialchars($edit_post['title'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Category</label>
          <select name="category" class="form-select">
            <?php foreach(['General','Healthcare BPO','Publishing','AI & Data','Real Estate BPO','Industry Insights','Technology','Finance'] as $cat): ?>
            <option value="<?= $cat ?>" <?= ($edit_post['category']??'')===$cat?'selected':'' ?>><?= $cat ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Slug (URL string)</label>
          <input type="text" name="slug" class="form-control" placeholder="auto-generated-from-title" value="<?= htmlspecialchars($edit_post['slug'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Author</label>
          <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($edit_post['author'] ?? 'Vortexsoft Team') ?>">
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Short Excerpt</label>
          <textarea name="excerpt" class="form-control" rows="2" placeholder="Brief 2-sentence summary..."><?= htmlspecialchars($edit_post['excerpt'] ?? '') ?></textarea>
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Post Content (HTML allowed)</label>
          <textarea name="content" class="form-control" rows="10" placeholder="<p>Write your article content here...</p>"><?= htmlspecialchars($edit_post['content'] ?? '') ?></textarea>
        </div>
        <div class="col-12 d-flex gap-4 align-items-center mt-3">
          <label style="font-size:14px;font-weight:600;color:#1e293b;display:flex;align-items:center;gap:8px;">
            <input type="checkbox" name="is_published" value="1" <?= (!isset($edit_post) || !empty($edit_post['is_published']))?'checked':'' ?>> Publish Immediately
          </label>
          <label style="font-size:14px;font-weight:600;color:#1e293b;display:flex;align-items:center;gap:8px;">
            <input type="checkbox" name="is_featured" value="1" <?= !empty($edit_post['is_featured'])?'checked':'' ?>> Featured Post
          </label>
        </div>
        <div class="col-12 d-flex gap-3 mt-4">
          <button type="submit" class="btn" style="background:#1C2280;color:#fff;border-radius:8px;font-weight:700;padding:12px 28px;"><i class="fas fa-save me-1"></i> Save Post</button>
          <a href="blog-posts.php" class="btn btn-outline-secondary" style="border-radius:8px;padding:12px 24px;">Cancel</a>
        </div>
      </div>
    </form>
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
