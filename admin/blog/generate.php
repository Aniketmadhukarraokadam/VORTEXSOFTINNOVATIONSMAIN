<?php
/**
 * Vortexsoft Innovations — Admin: AI Blog Generator
 * /admin/blog/generate.php
 *
 * Sends topic + keyword to Groq, Gemini, and OpenRouter in parallel.
 * Admin compares 3 drafts, edits the chosen one, then approves → publishes.
 * NO draft is auto-published. Human approval is REQUIRED.
 */

session_start();

// Allow up to 180 seconds for 3-provider AI API calls
@set_time_limit(180);
@ini_set('max_execution_time', '180');

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/ai-providers.php';

admin_check();

$db         = getDB();
$admin_name = $_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? 'Admin';
$admin_role = $_SESSION['admin_role'] ?? 'admin';
$admin_id   = $_SESSION['admin_id']   ?? $_SESSION['vortex_admin_id'] ?? 0;

// ── Rate Limiting: 10 generation batches per hour per admin ─────
$rate_limit_file = sys_get_temp_dir() . '/vx_aigen_' . md5((string)$admin_id) . '.json';
$rate_limit_max  = 10;
$rate_limit_window = 3600; // 1 hour

function check_ai_rate_limit(string $file, int $max, int $window): bool {
    $now  = time();
    $data = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
    // Remove timestamps outside the window
    $data = array_filter($data, fn($ts) => ($now - $ts) < $window);
    if (count($data) >= $max) return false;
    $data[] = $now;
    file_put_contents($file, json_encode(array_values($data)), LOCK_EX);
    return true;
}

function get_ai_rate_remaining(string $file, int $max, int $window): int {
    $now  = time();
    $data = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
    $data = array_filter($data, fn($ts) => ($now - $ts) < $window);
    return max(0, $max - count($data));
}

// ── Unique slug generator ────────────────────────────────────────
function generate_unique_slug(string $title, PDO $db): string {
    $base = slugify($title);
    if (empty($base)) $base = 'blog-post';
    $slug = $base;
    $i    = 2;
    while (true) {
        $stmt = $db->prepare("SELECT id FROM blog_posts WHERE slug = :s LIMIT 1");
        $stmt->execute([':s' => $slug]);
        if (!$stmt->fetch()) break;
        $slug = $base . '-' . $i++;
    }
    return $slug;
}

// ── Log generation batch ─────────────────────────────────────────
function log_generation(PDO $db, string $topic, string $keyword, int $admin_id, string $admin_name): void {
    try {
        $db->prepare("INSERT INTO ai_generation_logs (topic, target_keyword, admin_id, admin_username, created_at)
                      VALUES (:t, :k, :ai, :an, NOW())")
           ->execute([':t' => $topic, ':k' => $keyword, ':ai' => $admin_id, ':an' => $admin_name]);
    } catch (Throwable $e) {
        error_log('ai_generation_logs insert failed: ' . $e->getMessage());
    }
}

// ── Content calendar keywords (sample list — expand as needed) ───
$calendar_keywords = [
    'healthcare RCM outsourcing',
    'medical billing services India',
    'AI data annotation services',
    'BPO company Bengaluru',
    'ISO 27001 certified IT company India',
    'custom software development outsourcing',
    'publishing prepress services',
    'real estate title settlement services',
    'accounting payroll outsourcing India',
    'digital marketing services B2B',
    'manpower staffing solutions India',
    'HIPAA compliant BPO services',
    'VortexEXHO workforce management',
    'AI recruitment software India',
];

$page_state    = 'form';     // form | results | success
$error_msg     = '';
$results       = [];
$topic_val     = '';
$keyword_val   = '';
$selected_idx  = '';
$success_title = '';
$remaining     = get_ai_rate_remaining($rate_limit_file, $rate_limit_max, $rate_limit_window);

// ── Handle APPROVE & PUBLISH ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve') {
    if (!verify_csrf()) {
        $error_msg  = 'Security validation failed. Please refresh and try again.';
        $page_state = 'form';
    } else {
        $title    = sanitize($_POST['final_title']   ?? '');
        $excerpt  = sanitize($_POST['final_excerpt'] ?? '');
        $content  = $_POST['final_body']             ?? ''; // raw HTML — allowed for blog content
        $category = sanitize($_POST['final_category'] ?? 'Technology');
        $topic_val   = sanitize($_POST['gen_topic']   ?? '');
        $keyword_val = sanitize($_POST['gen_keyword'] ?? '');

        if (empty($title) || empty($content)) {
            $error_msg  = 'Title and body cannot be empty.';
            $page_state = 'form';
        } elseif ($db) {
            try {
                $slug    = generate_unique_slug($title, $db);
                $author  = htmlspecialchars($admin_name);
                $meta_t  = substr($title, 0, 300);
                $meta_d  = substr($excerpt, 0, 500);

                $stmt = $db->prepare("INSERT INTO blog_posts
                    (title, slug, category, excerpt, content, author, meta_title, meta_desc,
                     is_published, is_featured, published_at, created_at, updated_at)
                    VALUES
                    (:t, :s, :c, :e, :cnt, :a, :mt, :md, 1, 0, NOW(), NOW(), NOW())");
                $stmt->execute([
                    ':t'   => $title,
                    ':s'   => $slug,
                    ':c'   => $category,
                    ':e'   => $excerpt,
                    ':cnt' => $content,
                    ':a'   => $author,
                    ':mt'  => $meta_t,
                    ':md'  => $meta_d,
                ]);
                $new_id = $db->lastInsertId();

                // Clear session results — drafts discarded
                unset($_SESSION['ai_gen_results'], $_SESSION['ai_gen_topic'], $_SESSION['ai_gen_keyword']);

                log_admin_activity('AI Blog Published',
                    "Published AI-generated blog post ID:{$new_id} title:'{$title}' slug:'{$slug}' keyword:'{$keyword_val}'");

                $success_title = $title;
                $success_slug  = $slug;
                $page_state    = 'success';
            } catch (Throwable $e) {
                error_log('AI Blog approve error: ' . $e->getMessage());
                $error_msg  = 'Failed to save post. Please try again.';
                $page_state = 'form';
            }
        }
    }
}

// ── Handle GENERATE ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate') {
    if (!verify_csrf()) {
        $error_msg = 'Security validation failed.';
    } elseif (!check_ai_rate_limit($rate_limit_file, $rate_limit_max, $rate_limit_window)) {
        $error_msg = "Rate limit reached. You can generate at most {$rate_limit_max} batches per hour. Please wait before generating again.";
    } else {
        $topic_val   = sanitize($_POST['topic']   ?? '');
        $keyword_val = sanitize($_POST['keyword'] ?? '');

        if (empty($topic_val) || empty($keyword_val)) {
            $error_msg = 'Please enter both a topic and a target keyword.';
        } else {
            // Log the generation batch
            if ($db) log_generation($db, $topic_val, $keyword_val, (int)$admin_id, $admin_name);

            // Generate from all 3 providers
            $results = generateAllProviders($topic_val, $keyword_val);

            // Store in session for approval step
            $_SESSION['ai_gen_results'] = $results;
            $_SESSION['ai_gen_topic']   = $topic_val;
            $_SESSION['ai_gen_keyword'] = $keyword_val;

            $page_state = 'results';
            $remaining  = get_ai_rate_remaining($rate_limit_file, $rate_limit_max, $rate_limit_window);
        }
    }
}

// ── Restore results from session (e.g. page refresh after generate) ─
if ($page_state === 'form' && !empty($_SESSION['ai_gen_results']) && empty($error_msg)) {
    $results     = $_SESSION['ai_gen_results'];
    $topic_val   = $_SESSION['ai_gen_topic']   ?? '';
    $keyword_val = $_SESSION['ai_gen_keyword'] ?? '';
    $page_state  = 'results';
}

// Provider display names
$provider_meta = [
    'groq'       => ['name' => 'Groq',       'model' => GROQ_MODEL,         'color' => '#f97316', 'icon' => 'fa-bolt'],
    'gemini'     => ['name' => 'Gemini',     'model' => GEMINI_MODEL,       'color' => '#4285f4', 'icon' => 'fa-gem'],
    'openrouter' => ['name' => 'OpenRouter', 'model' => OPENROUTER_MODEL,   'color' => '#8b5cf6', 'icon' => 'fa-route'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI Blog Generator — Vortexsoft Admin Panel</title>
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="/assets/vendor/bootstrap.min.css">
<link rel="stylesheet" href="/assets/vendor/fontawesome/all.min.css">
<link rel="stylesheet" href="/assets/vendor/fonts.css">
<link rel="icon" href="/icon.jpg">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--dark:#080B1A;--primary:#1C2280;--accent:#CC2228;--sidebar-w:260px;--groq:#f97316;--gemini:#4285f4;--openrouter:#8b5cf6}
body{font-family:'Inter',sans-serif;background:#f0f2ff;color:#1e293b;min-height:100vh;display:flex}

/* Sidebar — exact match to existing admin */
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
.admin-user-info{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.admin-avatar{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px;flex-shrink:0}
.admin-name{font-size:13px;font-weight:600;color:#fff}
.admin-role{font-size:11px;color:rgba(255,255,255,.4);text-transform:capitalize}
.btn-logout{background:rgba(204,34,40,.15);border:1px solid rgba(204,34,40,.3);color:#CC2228;width:100%;padding:9px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-align:center;text-decoration:none;display:block;transition:.2s}
.btn-logout:hover{background:#CC2228;color:#fff}
.admin-main{margin-left:var(--sidebar-w);flex:1;padding:28px;min-height:100vh;transition:.3s}
.mobile-header{display:none;background:var(--dark);padding:14px 20px;align-items:center;justify-content:space-between;color:#fff}
.admin-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}
.admin-header h1{font-family:'Poppins',sans-serif;font-size:22px;font-weight:700;color:#1e293b}
.admin-header .subtitle{font-size:13px;color:#64748b;margin-top:2px}

/* Generator UI */
.gen-card{background:#fff;border-radius:16px;border:1px solid #e8ecff;padding:28px;margin-bottom:24px}
.gen-card-title{font-family:'Poppins',sans-serif;font-size:16px;font-weight:700;color:#1e293b;margin-bottom:18px;display:flex;align-items:center;gap:10px}
.form-label{font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;display:block}
.form-control,.form-select{border:1.5px solid #e2e8f0;border-radius:10px;padding:11px 14px;font-size:14px;width:100%;font-family:'Inter',sans-serif;transition:.3s;background:#fff;color:#1e293b}
.form-control:focus,.form-select:focus{border-color:#1C2280;box-shadow:0 0 0 3px rgba(28,34,128,.1);outline:none}
.btn-generate{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;font-family:'Poppins',sans-serif;font-size:14px;font-weight:600;padding:12px 28px;border:none;border-radius:10px;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:8px}
.btn-generate:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(28,34,128,.3)}
.btn-generate:disabled{opacity:.6;cursor:not-allowed;transform:none;box-shadow:none}

/* Cost note */
.cost-note{background:#fffbeb;border:1px solid rgba(245,158,11,.25);border-radius:10px;padding:12px 16px;font-size:12.5px;color:#92400e;display:flex;align-items:flex-start;gap:10px;margin-top:12px}
.cost-note i{color:#f59e0b;margin-top:2px;flex-shrink:0}

/* Rate limit badge */
.rate-badge{background:rgba(28,34,128,.07);border:1px solid rgba(28,34,128,.12);border-radius:8px;padding:6px 14px;font-size:12px;font-weight:600;color:#1C2280;display:inline-flex;align-items:center;gap:6px}
.rate-badge.warn{background:rgba(204,34,40,.07);border-color:rgba(204,34,40,.2);color:#CC2228}

/* Provider columns */
.provider-col{background:#fff;border-radius:16px;border:2px solid #e8ecff;transition:.3s;height:100%;display:flex;flex-direction:column}
.provider-col.selected{border-color:var(--primary);box-shadow:0 0 0 4px rgba(28,34,128,.12)}
.provider-col.failed{border-color:#fecaca;background:#fff5f5}
.provider-header{padding:16px 20px;border-bottom:1px solid #f0f4ff;display:flex;align-items:center;justify-content:space-between;gap:10px}
.provider-badge{font-size:11px;font-weight:700;padding:4px 10px;border-radius:100px;color:#fff;display:inline-flex;align-items:center;gap:5px}
.provider-meta{font-size:11px;color:#94a3b8;margin-top:2px}
.provider-body{padding:16px 20px;flex:1;overflow:hidden}
.draft-title{font-family:'Poppins',sans-serif;font-size:15px;font-weight:700;color:#1e293b;margin-bottom:6px;line-height:1.4}
.draft-excerpt{font-size:12.5px;color:#64748b;margin-bottom:12px;font-style:italic;border-left:3px solid #e2e8f0;padding-left:10px}
.draft-preview{font-size:13px;color:#475569;line-height:1.7;max-height:280px;overflow:hidden;position:relative}
.draft-preview::after{content:'';position:absolute;bottom:0;left:0;right:0;height:60px;background:linear-gradient(transparent,#fff);pointer-events:none}
.draft-preview h2,.draft-preview h3{font-family:'Poppins',sans-serif;font-size:14px;font-weight:700;color:#1e293b;margin:10px 0 4px}
.draft-preview p{margin-bottom:8px}
.provider-footer{padding:12px 20px;border-top:1px solid #f0f4ff;display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap}
.wc-badge{font-size:11px;color:#94a3b8;font-weight:500}
.token-badge{font-size:11px;color:#94a3b8;font-weight:500}
.btn-select{background:rgba(28,34,128,.07);border:1.5px solid rgba(28,34,128,.15);color:#1C2280;font-size:12.5px;font-weight:600;padding:6px 16px;border-radius:8px;cursor:pointer;transition:.2s;display:inline-flex;align-items:center;gap:5px}
.btn-select:hover,.btn-select.active{background:#1C2280;color:#fff;border-color:#1C2280}
.failed-msg{color:#CC2228;font-size:13px;padding:16px 0;display:flex;align-items:flex-start;gap:8px}

/* Editor */
.editor-section{background:#fff;border-radius:16px;border:2px solid #1C2280;padding:24px;margin-top:24px;display:none}
.editor-section.visible{display:block}
.editor-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;margin-bottom:6px}
.editor-field{border:1.5px solid #e2e8f0;border-radius:10px;padding:12px 14px;font-size:14px;font-family:'Inter',sans-serif;width:100%;color:#1e293b;transition:.3s;resize:vertical}
.editor-field:focus{border-color:#1C2280;outline:none;box-shadow:0 0 0 3px rgba(28,34,128,.1)}
.editor-body{border:1.5px solid #e2e8f0;border-radius:10px;padding:14px;font-size:13.5px;font-family:'Inter',sans-serif;color:#1e293b;min-height:400px;width:100%;resize:vertical;line-height:1.8;transition:.3s}
.editor-body:focus{border-color:#1C2280;outline:none;box-shadow:0 0 0 3px rgba(28,34,128,.1)}
.btn-approve{background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-family:'Poppins',sans-serif;font-size:15px;font-weight:700;padding:14px 36px;border:none;border-radius:12px;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:10px}
.btn-approve:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(16,185,129,.35)}
.btn-new{background:rgba(100,116,139,.08);border:1.5px solid rgba(100,116,139,.2);color:#64748b;font-size:13px;font-weight:600;padding:10px 20px;border-radius:10px;cursor:pointer;text-decoration:none;transition:.2s;display:inline-flex;align-items:center;gap:6px}
.btn-new:hover{background:#64748b;color:#fff}
.category-select{border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-size:14px;font-family:'Inter',sans-serif;color:#1e293b;background:#fff;width:100%;transition:.3s}
.category-select:focus{border-color:#1C2280;outline:none;box-shadow:0 0 0 3px rgba(28,34,128,.1)}

/* Success */
.success-card{background:#f0fdf4;border:2px solid rgba(16,185,129,.3);border-radius:16px;padding:40px;text-align:center}
.success-card i{font-size:52px;color:#10b981;margin-bottom:16px}
.success-card h2{font-family:'Poppins',sans-serif;font-size:22px;font-weight:700;color:#065f46;margin-bottom:8px}
.success-card p{color:#047857;font-size:14px}

/* Error / Alert */
.alert-error{background:#fff5f5;border:1px solid rgba(204,34,40,.2);color:#CC2228;border-radius:10px;padding:12px 16px;font-size:14px;margin-bottom:20px;display:flex;align-items:flex-start;gap:10px}
.alert-info{background:#f0f9ff;border:1px solid rgba(14,165,233,.2);color:#075985;border-radius:10px;padding:12px 16px;font-size:14px;margin-bottom:20px;display:flex;align-items:flex-start;gap:10px}

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

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-logo">
    <div>
      <img src="/logo-header.png" alt="Vortexsoft Group">
      <div class="sub">Admin Panel</div>
    </div>
    <button class="btn text-white p-0 d-lg-none" id="sidebarCloseBtn"><i class="fas fa-times"></i></button>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">Main</div>
    <a href="/admin/dashboard.php" class="sidebar-link"><span class="icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard</a>
    <a href="/admin/contacts.php" class="sidebar-link"><span class="icon"><i class="fas fa-envelope"></i></span> Inquiries</a>
    <a href="/admin/applications.php" class="sidebar-link"><span class="icon"><i class="fas fa-briefcase"></i></span> Applications</a>

    <div class="nav-section">Content &amp; System</div>
    <a href="/admin/blog-posts.php" class="sidebar-link"><span class="icon"><i class="fas fa-pen-alt"></i></span> Blog Posts</a>
    <a href="/admin/blog/generate.php" class="sidebar-link active"><span class="icon"><i class="fas fa-robot"></i></span> AI Blog Generator</a>
    <a href="/admin/newsletter.php" class="sidebar-link"><span class="icon"><i class="fas fa-paper-plane"></i></span> Newsletter</a>
    <a href="/admin/settings.php" class="sidebar-link"><span class="icon"><i class="fas fa-cog"></i></span> Settings</a>
    <a href="/index.php" target="_blank" class="sidebar-link"><span class="icon"><i class="fas fa-external-link-alt"></i></span> View Website</a>
  </nav>

  <div class="sidebar-footer">
    <div class="admin-user-info">
      <div class="admin-avatar"><?= strtoupper(substr($admin_name, 0, 1)) ?></div>
      <div>
        <div class="admin-name"><?= htmlspecialchars($admin_name) ?></div>
        <div class="admin-role"><?= str_replace('_', ' ', $admin_role) ?></div>
      </div>
    </div>
    <a href="/admin/logout.php" class="btn-logout"><i class="fas fa-sign-out-alt me-2"></i>Sign Out</a>
  </div>
</aside>

<!-- Main Content -->
<main class="admin-main">
  <div class="admin-header">
    <div>
      <h1><i class="fas fa-robot me-2" style="color:#CC2228;font-size:20px;"></i>AI Blog Generator</h1>
      <div class="subtitle">Generate blog drafts with Groq, Gemini &amp; OpenRouter — approve to publish</div>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="rate-badge <?= $remaining <= 2 ? 'warn' : '' ?>">
        <i class="fas fa-zap"></i> <?= $remaining ?>/<?= $rate_limit_max ?> generations remaining this hour
      </span>
      <a href="/admin/blog-posts.php" class="btn-new"><i class="fas fa-list"></i> All Blog Posts</a>
    </div>
  </div>

  <?php if ($error_msg): ?>
  <div class="alert-error"><i class="fas fa-exclamation-circle mt-1"></i><div><?= htmlspecialchars($error_msg) ?></div></div>
  <?php endif; ?>

  <?php if ($page_state === 'success'): ?>
  <!-- ── SUCCESS STATE ── -->
  <div class="success-card">
    <i class="fas fa-check-circle"></i>
    <h2>Blog Post Published!</h2>
    <p style="margin-bottom:20px;">"<strong><?= htmlspecialchars($success_title ?? '') ?></strong>" is now live on the blog.</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="/admin/blog-posts.php" class="btn-generate"><i class="fas fa-list"></i> View All Posts</a>
      <a href="/blog.php" target="_blank" class="btn-new"><i class="fas fa-external-link-alt"></i> View on Website</a>
      <a href="/admin/blog/generate.php?new=1" class="btn-new"><i class="fas fa-plus"></i> Generate Another</a>
    </div>
  </div>

  <?php elseif ($page_state === 'form' || empty($results)): ?>
  <!-- ── FORM STATE ── -->
  <div class="gen-card">
    <div class="gen-card-title"><i class="fas fa-magic" style="color:#CC2228;"></i> Generate Blog Post</div>

    <form method="POST" id="generateForm">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <input type="hidden" name="action" value="generate">

      <div class="row g-3 mb-3">
        <div class="col-md-7">
          <label class="form-label" for="topic">Blog Topic <span style="color:#CC2228">*</span></label>
          <input type="text" class="form-control" id="topic" name="topic" required
                 placeholder="e.g. How Healthcare BPO Reduces Medical Billing Errors"
                 value="<?= htmlspecialchars($topic_val) ?>">
          <div style="font-size:11.5px;color:#94a3b8;margin-top:5px;">Write a clear topic sentence. The more specific, the better the output.</div>
        </div>
        <div class="col-md-5">
          <label class="form-label" for="keyword">Target Keyword <span style="color:#CC2228">*</span></label>
          <input type="text" class="form-control" id="keyword" name="keyword" required
                 placeholder="e.g. healthcare RCM outsourcing"
                 value="<?= htmlspecialchars($keyword_val) ?>"
                 list="keyword-suggestions">
          <datalist id="keyword-suggestions">
            <?php foreach ($calendar_keywords as $kw): ?>
            <option value="<?= htmlspecialchars($kw) ?>">
            <?php endforeach; ?>
          </datalist>
          <div style="font-size:11.5px;color:#94a3b8;margin-top:5px;">Select from calendar or type your own.</div>
        </div>
      </div>

      <div class="d-flex align-items-center gap-3 flex-wrap">
        <button type="submit" class="btn-generate" id="generateBtn"
                <?= $remaining <= 0 ? 'disabled' : '' ?>>
          <i class="fas fa-robot"></i>
          <?= $remaining <= 0 ? 'Rate Limit Reached' : 'Generate with 3 AI Providers' ?>
        </button>
        <?php if ($remaining > 0): ?>
        <span style="font-size:12.5px;color:#94a3b8;">~30–60s • 3 parallel API calls</span>
        <?php endif; ?>
      </div>

      <div class="cost-note">
        <i class="fas fa-coins"></i>
        <div>
          <strong>Cost awareness:</strong> Each click triggers <strong>3 separate API calls</strong> (Groq + Gemini + OpenRouter) — one per provider. This is intentional (compare-and-pick), but means costs scale 3× vs a single-provider tool.
          Token usage per draft is shown after generation. Rate limit: <?= $rate_limit_max ?> batches/hour.
        </div>
      </div>
    </form>
  </div>

  <!-- Content Calendar Quick Reference -->
  <div class="gen-card">
    <div class="gen-card-title"><i class="fas fa-calendar-alt" style="color:#1C2280;"></i> Content Calendar Keywords</div>
    <div class="d-flex flex-wrap gap-2">
      <?php foreach ($calendar_keywords as $kw): ?>
      <button type="button" class="btn-select" onclick="document.getElementById('keyword').value='<?= htmlspecialchars(addslashes($kw)) ?>'">
        <?= htmlspecialchars($kw) ?>
      </button>
      <?php endforeach; ?>
    </div>
  </div>

  <?php else: ?>
  <!-- ── RESULTS STATE ── -->
  <div class="alert-info">
    <i class="fas fa-info-circle mt-1"></i>
    <div>
      Generated for: <strong><?= htmlspecialchars($topic_val) ?></strong> | Keyword: <strong><?= htmlspecialchars($keyword_val) ?></strong>
      &nbsp;—&nbsp;<a href="/admin/blog/generate.php?new=1" style="color:#075985;font-weight:600;">Start over</a>
    </div>
  </div>

  <form method="POST" id="approveForm">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
    <input type="hidden" name="action" value="approve">
    <input type="hidden" name="gen_topic" value="<?= htmlspecialchars($topic_val) ?>">
    <input type="hidden" name="gen_keyword" value="<?= htmlspecialchars($keyword_val) ?>">
    <input type="hidden" name="final_title"   id="hidden_title">
    <input type="hidden" name="final_excerpt" id="hidden_excerpt">
    <input type="hidden" name="final_body"    id="hidden_body">
    <input type="hidden" name="final_category" id="hidden_category" value="Technology">

    <!-- 3-Column Provider Results -->
    <div class="row g-3 mb-3">
      <?php foreach (['groq', 'gemini', 'openrouter'] as $key):
        $r    = $results[$key] ?? ['ok' => false, 'error' => 'No result'];
        $meta = $provider_meta[$key];
      ?>
      <div class="col-lg-4">
        <div class="provider-col <?= $r['ok'] ? '' : 'failed' ?>" id="col_<?= $key ?>">
          <div class="provider-header">
            <div>
              <span class="provider-badge" style="background:<?= $meta['color'] ?>;">
                <i class="fas <?= $meta['icon'] ?>"></i> <?= $meta['name'] ?>
              </span>
              <div class="provider-meta"><?= htmlspecialchars($meta['model']) ?></div>
            </div>
            <?php if ($r['ok']): ?>
            <button type="button" class="btn-select" id="select_<?= $key ?>"
                    onclick="selectDraft('<?= $key ?>')">
              <i class="fas fa-check-circle"></i> Select
            </button>
            <?php endif; ?>
          </div>

          <div class="provider-body">
            <?php if (!$r['ok']): ?>
            <div class="failed-msg">
              <i class="fas fa-exclamation-triangle mt-1"></i>
              <div>
                <strong>Generation failed</strong><br>
                <span style="font-size:12px;"><?= htmlspecialchars($r['error'] ?? 'Unknown error') ?></span>
              </div>
            </div>
            <?php else:
              $d = $r['data'];
              $wc = str_word_count(strip_tags($d['body']));
            ?>
            <div class="draft-title"><?= htmlspecialchars($d['title']) ?></div>
            <div class="draft-excerpt"><?= htmlspecialchars($d['excerpt']) ?></div>
            <div class="draft-preview"><?= $d['body'] ?></div>
            <?php endif; ?>
          </div>

          <?php if ($r['ok']): ?>
          <div class="provider-footer">
            <span class="wc-badge"><i class="fas fa-align-left me-1"></i><?= number_format($wc ?? 0) ?> words</span>
            <span class="token-badge"><i class="fas fa-microchip me-1"></i><?= formatUsage($r['data']['usage'] ?? null) ?></span>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- ── Inline Editor (shown when a draft is selected) ── -->
    <div class="editor-section" id="editorSection">
      <div class="gen-card-title" style="margin-bottom:20px;">
        <i class="fas fa-edit" style="color:#1C2280;"></i>
        Edit &amp; Approve — <span id="editorProviderName" style="color:#CC2228;"></span> Draft
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-8">
          <label class="editor-label">Post Title</label>
          <input type="text" class="editor-field" id="edit_title" placeholder="Post title">
        </div>
        <div class="col-md-4">
          <label class="editor-label">Category</label>
          <select class="category-select" id="edit_category" onchange="document.getElementById('hidden_category').value=this.value">
            <?php foreach (['Technology','Healthcare','Business','AI & Automation','Accounting','Publishing','Real Estate','Staffing','Digital Marketing','Company News','General'] as $cat): ?>
            <option value="<?= $cat ?>"><?= $cat ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="editor-label">Meta Excerpt <span style="color:#94a3b8;font-weight:400;text-transform:none;">(under 160 chars)</span></label>
        <input type="text" class="editor-field" id="edit_excerpt" maxlength="160" placeholder="One sentence summary for SEO">
        <div id="excerpt_counter" style="font-size:11px;color:#94a3b8;margin-top:4px;">0/160</div>
      </div>

      <div class="mb-4">
        <label class="editor-label">Body (HTML)</label>
        <div style="font-size:11.5px;color:#94a3b8;margin-bottom:8px;">Edit the raw HTML directly. Use &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt; tags. Changes here are what gets published.</div>
        <textarea class="editor-body" id="edit_body" rows="20"></textarea>
      </div>

      <div class="d-flex align-items-center gap-3 flex-wrap">
        <button type="submit" class="btn-approve" id="approveBtn" onclick="prepareApprove()">
          <i class="fas fa-check-circle"></i> Approve &amp; Publish
        </button>
        <a href="/admin/blog/generate.php?new=1" class="btn-new"><i class="fas fa-redo"></i> Discard &amp; Regenerate</a>
        <div style="font-size:12px;color:#94a3b8;margin-left:auto;">
          <i class="fas fa-lock me-1"></i> Post will be immediately published as is_published=1
        </div>
      </div>
    </div><!-- /editor -->
  </form>

  <!-- Regenerate form -->
  <div class="gen-card mt-3" style="padding:20px;">
    <form method="POST" id="regenForm">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <input type="hidden" name="action" value="generate">
      <div class="row g-2 align-items-end">
        <div class="col-md-5">
          <label class="form-label" style="font-size:12px;">Refine Topic</label>
          <input type="text" class="form-control" name="topic" value="<?= htmlspecialchars($topic_val) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label" style="font-size:12px;">Keyword</label>
          <input type="text" class="form-control" name="keyword" value="<?= htmlspecialchars($keyword_val) ?>">
        </div>
        <div class="col-md-3">
          <button type="submit" class="btn-generate w-100" style="padding:11px 16px;" <?= $remaining <= 0 ? 'disabled' : '' ?>>
            <i class="fas fa-sync-alt"></i> Regenerate All
          </button>
        </div>
      </div>
    </form>
  </div>

  <?php endif; ?>

</main><!-- /admin-main -->

<script src="/assets/vendor/bootstrap.bundle.min.js"></script>
<script>
// Store draft data from PHP results
const drafts = <?= json_encode(array_map(function($r, $key) {
    if (!$r['ok']) return null;
    return [
        'title'   => $r['data']['title']   ?? '',
        'excerpt' => $r['data']['excerpt'] ?? '',
        'body'    => $r['data']['body']    ?? '',
    ];
}, $results, array_keys($results))) ?>;

const draftKeys  = <?= json_encode(array_keys($results)) ?>;
const draftMap   = {};
draftKeys.forEach((k, i) => draftMap[k] = drafts[i]);

let selectedProvider = null;

function selectDraft(key) {
    const d = draftMap[key];
    if (!d) return;

    // Update selection UI
    draftKeys.forEach(k => {
        const col = document.getElementById('col_' + k);
        const btn = document.getElementById('select_' + k);
        if (col) col.classList.toggle('selected', k === key);
        if (btn) btn.classList.toggle('active', k === key);
    });

    // Populate editor
    const providerNames = {groq: 'Groq', gemini: 'Gemini', openrouter: 'OpenRouter'};
    document.getElementById('editorProviderName').textContent = providerNames[key] || key;
    document.getElementById('edit_title').value   = d.title;
    document.getElementById('edit_excerpt').value = d.excerpt;
    document.getElementById('edit_body').value    = d.body;
    updateExcerptCounter();

    document.getElementById('editorSection').classList.add('visible');
    document.getElementById('editorSection').scrollIntoView({behavior:'smooth', block:'start'});
    selectedProvider = key;
}

function prepareApprove() {
    document.getElementById('hidden_title').value    = document.getElementById('edit_title').value.trim();
    document.getElementById('hidden_excerpt').value  = document.getElementById('edit_excerpt').value.trim();
    document.getElementById('hidden_body').value     = document.getElementById('edit_body').value.trim();
    document.getElementById('hidden_category').value = document.getElementById('edit_category').value;
}

function updateExcerptCounter() {
    const el = document.getElementById('edit_excerpt');
    if (!el) return;
    const len = el.value.length;
    const counter = document.getElementById('excerpt_counter');
    if (counter) {
        counter.textContent = len + '/160';
        counter.style.color = len > 150 ? '#CC2228' : '#94a3b8';
    }
}

document.getElementById('edit_excerpt')?.addEventListener('input', updateExcerptCounter);

// Show spinner on generate/regenerate
['generateForm', 'regenForm'].forEach(id => {
    const f = document.getElementById(id);
    if (f) f.addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating — please wait (30–60s)...';
        }
    });
});

// Approve button guard
document.getElementById('approveBtn')?.addEventListener('click', function(e) {
    if (!selectedProvider) {
        e.preventDefault();
        alert('Please select a draft first by clicking "Select" on one of the three provider cards.');
        return;
    }
    if (!document.getElementById('edit_title').value.trim()) {
        e.preventDefault();
        alert('Post title cannot be empty.');
        return;
    }
    if (!confirm('Publish this post immediately? It will go live on the website.')) {
        e.preventDefault();
    }
});

// Clear results when navigating to ?new=1
const params = new URLSearchParams(window.location.search);
if (params.get('new') === '1') {
    // Server-side handles clearing session; ensure clean state
}

// Sidebar mobile toggle
const sidebar  = document.getElementById('adminSidebar');
const toggleBtn = document.getElementById('sidebarToggleBtn');
const closeBtn  = document.getElementById('sidebarCloseBtn');
if (toggleBtn) toggleBtn.addEventListener('click', () => sidebar?.classList.toggle('show'));
if (closeBtn)  closeBtn.addEventListener('click',  () => sidebar?.classList.remove('show'));
</script>
</body>
</html>
<?php
// Clear session on ?new=1
if (isset($_GET['new'])) {
    unset($_SESSION['ai_gen_results'], $_SESSION['ai_gen_topic'], $_SESSION['ai_gen_keyword']);
}
