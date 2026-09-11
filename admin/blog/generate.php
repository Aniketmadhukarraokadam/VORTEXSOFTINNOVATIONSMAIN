<?php
/**
 * Vortexsoft Innovations — Admin: AI Blog Generator
 * /admin/blog/generate.php
 *
 * Uses Google Gemini (Default), Groq, and OpenRouter to generate SEO/AEO/GEO-optimized blogs.
 * Generates photorealistic AI featured image & LinkedIn post draft.
 * Human approval is REQUIRED before publishing.
 */

session_start();

// Allow up to 180 seconds for AI API calls
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

// ── Rate Limiting: 15 generation batches per hour per admin ─────
$rate_limit_file   = sys_get_temp_dir() . '/vx_aigen_' . md5((string)$admin_id) . '.json';
$rate_limit_max    = 15;
$rate_limit_window = 3600; // 1 hour

function check_ai_rate_limit(string $file, int $max, int $window): bool {
    $now  = time();
    $data = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
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
        // Table might not exist yet; fail silently
    }
}

// ── AJAX Endpoint: Live Image Regeneration ───────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajax_generate_image') {
    header('Content-Type: application/json');
    if (!verify_csrf()) {
        echo json_encode(['success' => false, 'message' => 'Security token invalid']);
        exit;
    }
    $prompt = sanitize($_POST['prompt'] ?? '');
    $slug   = sanitize($_POST['slug']   ?? 'blog-banner');
    $imgUrl = generateBlogAiImage($prompt, $slug);
    if ($imgUrl) {
        echo json_encode(['success' => true, 'image_url' => $imgUrl]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Image generation service timeout. Please try again.']);
    }
    exit;
}

// ── Content calendar keywords ────────────────────────────────────
$calendar_keywords = [
    'healthcare RCM automation',
    'medical billing outsourcing India',
    'AI data annotation services',
    'custom web development enterprise',
    'ISO 27001 certified BPO company',
    'publishing prepress XML ePUB3 automation',
    'real estate title settlement services',
    'accounting payroll outsourcing India',
    'HIPAA compliant healthcare BPO',
    'VortexEXHO workforce management OS',
    'AI recruitment vortexHire platform',
    'call center speech analytics vortexKonnect',
    'vortexsofthrms AI HRMS automation',
    'BPO company Pune Bengaluru Wyoming',
];

$page_state     = 'form';
$error_msg      = '';
$results        = [];
$topic_val      = '';
$keyword_val    = '';
$success_title  = '';
$success_slug   = '';
$success_image  = '';
$success_li     = '';
$remaining      = get_ai_rate_remaining($rate_limit_file, $rate_limit_max, $rate_limit_window);

// ── Handle APPROVE & PUBLISH ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve') {
    if (!verify_csrf()) {
        $error_msg  = 'Security validation failed. Please refresh and try again.';
        $page_state = 'form';
    } else {
        $title       = sanitize($_POST['final_title']        ?? '');
        $excerpt     = sanitize($_POST['final_excerpt']      ?? '');
        $content     = $_POST['final_body']                  ?? '';
        $category    = sanitize($_POST['final_category']     ?? 'Technology');
        $cover_image = sanitize($_POST['final_cover_image']  ?? '');
        $li_draft    = $_POST['final_linkedin_post']         ?? '';
        $topic_val   = sanitize($_POST['gen_topic']          ?? '');
        $keyword_val = sanitize($_POST['gen_keyword']        ?? '');

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
                    (title, slug, category, excerpt, content, author, cover_image, meta_title, meta_desc,
                     is_published, is_featured, published_at, created_at, updated_at)
                    VALUES
                    (:t, :s, :c, :e, :cnt, :a, :img, :mt, :md, 1, 0, NOW(), NOW(), NOW())");
                $stmt->execute([
                    ':t'   => $title,
                    ':s'   => $slug,
                    ':c'   => $category,
                    ':e'   => $excerpt,
                    ':cnt' => $content,
                    ':a'   => $author,
                    ':img' => $cover_image ?: null,
                    ':mt'  => $meta_t,
                    ':md'  => $meta_d,
                ]);
                $new_id = $db->lastInsertId();

                // Save last published info for success screen
                $success_title = $title;
                $success_slug  = $slug;
                $success_image = $cover_image;
                $success_li    = $li_draft;

                // Clear session drafts
                unset($_SESSION['ai_gen_results'], $_SESSION['ai_gen_topic'], $_SESSION['ai_gen_keyword']);

                if (function_exists('log_admin_activity')) {
                    log_admin_activity('AI Blog Published', "Published AI blog post ID:{$new_id} title:'{$title}' slug:'{$slug}'");
                }

                $page_state = 'success';
            } catch (Throwable $e) {
                error_log('AI Blog approve error: ' . $e->getMessage());
                $error_msg  = 'Failed to save post to database: ' . $e->getMessage();
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
        $error_msg = "Rate limit reached. Maximum {$rate_limit_max} batches per hour.";
    } else {
        $topic_val   = sanitize($_POST['topic']   ?? '');
        $keyword_val = sanitize($_POST['keyword'] ?? '');
        $engine      = sanitize($_POST['engine']  ?? 'gemini');

        if (empty($topic_val) || empty($keyword_val)) {
            $error_msg = 'Please enter both a topic and a target keyword.';
        } else {
            if ($db) log_generation($db, $topic_val, $keyword_val, (int)$admin_id, $admin_name);

            if ($engine === 'gemini') {
                $results = generateGeminiDefault($topic_val, $keyword_val);
            } else {
                $results = generateAllProviders($topic_val, $keyword_val);
            }

            // Auto-generate featured image for each successful draft
            foreach ($results as $k => &$res) {
                if ($res['ok']) {
                    $imgPrompt = $res['data']['image_prompt'] ?? ($topic_val . ' corporate enterprise IT artificial intelligence 8k photorealistic');
                    $imgUrl = generateBlogAiImage($imgPrompt, $keyword_val);
                    $res['data']['image_url'] = $imgUrl ?: '/uploads/blog/default_banner.jpg';
                }
            }
            unset($res);

            $_SESSION['ai_gen_results'] = $results;
            $_SESSION['ai_gen_topic']   = $topic_val;
            $_SESSION['ai_gen_keyword'] = $keyword_val;

            $page_state = 'results';
            $remaining  = get_ai_rate_remaining($rate_limit_file, $rate_limit_max, $rate_limit_window);
        }
    }
}

// ── Restore results from session ─────────────────────────────────
if ($page_state === 'form' && !empty($_SESSION['ai_gen_results']) && empty($error_msg)) {
    $results     = $_SESSION['ai_gen_results'];
    $topic_val   = $_SESSION['ai_gen_topic']   ?? '';
    $keyword_val = $_SESSION['ai_gen_keyword'] ?? '';
    $page_state  = 'results';
}

$provider_meta = [
    'gemini'     => ['name' => 'Gemini (Default)', 'model' => GEMINI_MODEL,     'color' => '#1C2280', 'icon' => 'fa-gem'],
    'groq'       => ['name' => 'Groq Llama-3',     'model' => GROQ_MODEL,       'color' => '#f97316', 'icon' => 'fa-bolt'],
    'openrouter' => ['name' => 'OpenRouter',       'model' => OPENROUTER_MODEL, 'color' => '#8b5cf6', 'icon' => 'fa-route'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI Blog Generator (Gemini Default) — Vortexsoft Admin</title>
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="/assets/vendor/bootstrap.min.css">
<link rel="stylesheet" href="/assets/vendor/fontawesome/all.min.css">
<link rel="stylesheet" href="/assets/vendor/fonts.css">
<link rel="icon" href="/icon.jpg">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--dark:#080B1A;--primary:#1C2280;--accent:#CC2228;--sidebar-w:260px;--gemini:#1C2280;--groq:#f97316;--openrouter:#8b5cf6}
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

/* UI Cards */
.gen-card{background:#fff;border-radius:16px;border:1px solid #e8ecff;padding:28px;margin-bottom:24px;box-shadow:0 4px 15px rgba(28,34,128,.04)}
.gen-card-title{font-family:'Poppins',sans-serif;font-size:16px;font-weight:700;color:#1e293b;margin-bottom:18px;display:flex;align-items:center;gap:10px}
.form-label{font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;display:block}
.form-control,.form-select{border:1.5px solid #e2e8f0;border-radius:10px;padding:11px 14px;font-size:14px;width:100%;font-family:'Inter',sans-serif;transition:.3s;background:#fff;color:#1e293b}
.form-control:focus,.form-select:focus{border-color:#1C2280;box-shadow:0 0 0 3px rgba(28,34,128,.1);outline:none}

.btn-gemini-default{background:linear-gradient(135deg,#1C2280,#2e37c9);color:#fff;font-family:'Poppins',sans-serif;font-size:14.5px;font-weight:700;padding:13px 28px;border:none;border-radius:10px;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:10px}
.btn-gemini-default:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(28,34,128,.35);color:#fff}
.btn-compare{background:rgba(28,34,128,.08);color:#1C2280;border:1.5px solid rgba(28,34,128,.2);font-size:13.5px;font-weight:600;padding:12px 22px;border-radius:10px;cursor:pointer;transition:.2s;display:inline-flex;align-items:center;gap:8px}
.btn-compare:hover{background:#1C2280;color:#fff}

.rate-badge{background:rgba(28,34,128,.07);border:1px solid rgba(28,34,128,.12);border-radius:8px;padding:6px 14px;font-size:12px;font-weight:600;color:#1C2280;display:inline-flex;align-items:center;gap:6px}
.rate-badge.warn{background:rgba(204,34,40,.07);border-color:rgba(204,34,40,.2);color:#CC2228}

/* Provider columns */
.provider-col{background:#fff;border-radius:16px;border:2px solid #e8ecff;transition:.3s;height:100%;display:flex;flex-direction:column}
.provider-col.selected{border-color:var(--primary);box-shadow:0 0 0 4px rgba(28,34,128,.15)}
.provider-col.failed{border-color:#fecaca;background:#fff5f5}
.provider-header{padding:16px 20px;border-bottom:1px solid #f0f4ff;display:flex;align-items:center;justify-content:space-between;gap:10px}
.provider-badge{font-size:11px;font-weight:700;padding:4px 10px;border-radius:100px;color:#fff;display:inline-flex;align-items:center;gap:5px}
.provider-meta{font-size:11px;color:#94a3b8;margin-top:2px}
.provider-body{padding:16px 20px;flex:1;overflow:hidden}
.draft-title{font-family:'Poppins',sans-serif;font-size:15px;font-weight:700;color:#1e293b;margin-bottom:8px;line-height:1.4}
.draft-excerpt{font-size:12.5px;color:#64748b;margin-bottom:12px;font-style:italic;border-left:3px solid #e2e8f0;padding-left:10px}
.draft-preview{font-size:13px;color:#475569;line-height:1.7;max-height:220px;overflow:hidden;position:relative}
.draft-preview::after{content:'';position:absolute;bottom:0;left:0;right:0;height:60px;background:linear-gradient(transparent,#fff);pointer-events:none}
.btn-select{background:rgba(28,34,128,.07);border:1.5px solid rgba(28,34,128,.15);color:#1C2280;font-size:12.5px;font-weight:600;padding:6px 16px;border-radius:8px;cursor:pointer;transition:.2s;display:inline-flex;align-items:center;gap:5px}
.btn-select:hover,.btn-select.active{background:#1C2280;color:#fff;border-color:#1C2280}

/* Editor & Enhancements */
.editor-section{background:#fff;border-radius:16px;border:2px solid #1C2280;padding:26px;margin-top:24px;display:none}
.editor-section.visible{display:block}
.editor-label{font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;margin-bottom:6px;display:block}
.editor-field{border:1.5px solid #e2e8f0;border-radius:10px;padding:12px 14px;font-size:14px;font-family:'Inter',sans-serif;width:100%;color:#1e293b;transition:.3s}
.editor-body{border:1.5px solid #e2e8f0;border-radius:10px;padding:14px;font-size:13.5px;font-family:'Inter',sans-serif;color:#1e293b;min-height:360px;width:100%;resize:vertical;line-height:1.8}

/* AI Featured Image Card */
.image-preview-card{background:#f8faff;border:1.5px solid #dbeafe;border-radius:14px;padding:18px;margin-bottom:22px}
.image-preview-card img{width:100%;max-height:320px;object-fit:cover;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,.08)}

/* LinkedIn Draft Card */
.linkedin-draft-card{background:#f0f7ff;border:1.5px solid #bae6fd;border-radius:14px;padding:20px;margin-bottom:22px}
.linkedin-draft-card textarea{background:#fff;border:1.5px solid #93c5fd;border-radius:10px;font-size:13px;line-height:1.6;color:#1e293b}
.btn-copy-li{background:#0a66c2;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;cursor:pointer;transition:.2s}
.btn-copy-li:hover{background:#004182;color:#fff}
.btn-copy-li.copied{background:#10b981}

.btn-approve{background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-family:'Poppins',sans-serif;font-size:15px;font-weight:700;padding:14px 36px;border:none;border-radius:12px;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:10px}
.btn-approve:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(16,185,129,.35)}
.btn-new{background:rgba(100,116,139,.08);border:1.5px solid rgba(100,116,139,.2);color:#64748b;font-size:13px;font-weight:600;padding:10px 20px;border-radius:10px;cursor:pointer;text-decoration:none;transition:.2s;display:inline-flex;align-items:center;gap:6px}
.btn-new:hover{background:#64748b;color:#fff}

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
      <img src="/logo-header.png" alt="Vortexsoft Innovations Private Limited">
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
      <h1><i class="fas fa-robot me-2" style="color:#CC2228;font-size:20px;"></i>AI Blog &amp; Image Generator</h1>
      <div class="subtitle">Default Engine: Google Gemini 3.6 Flash &bull; SEO, AEO &amp; GEO Optimized &bull; AI Image &bull; LinkedIn Draft</div>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="rate-badge <?= $remaining <= 2 ? 'warn' : '' ?>">
        <i class="fas fa-bolt"></i> <?= $remaining ?>/<?= $rate_limit_max ?> generations remaining
      </span>
      <a href="/admin/blog-posts.php" class="btn-new"><i class="fas fa-list"></i> All Blog Posts</a>
    </div>
  </div>

  <?php if ($error_msg): ?>
  <div class="alert-error"><i class="fas fa-exclamation-circle mt-1"></i><div><?= htmlspecialchars($error_msg) ?></div></div>
  <?php endif; ?>

  <?php if ($page_state === 'success'): ?>
  <!-- ── SUCCESS STATE ── -->
  <div class="gen-card" style="text-align:center;border-top:4px solid #10b981;">
    <div style="font-size:48px;color:#10b981;margin-bottom:12px;"><i class="fas fa-check-circle"></i></div>
    <h2 style="font-family:'Poppins',sans-serif;font-weight:700;color:#065f46;margin-bottom:8px;">Blog Post Published Live!</h2>
    <p style="color:#047857;font-size:15px;margin-bottom:20px;">"<strong><?= htmlspecialchars($success_title) ?></strong>" is now live on the website.</p>

    <?php if ($success_image): ?>
    <div style="max-width:560px;margin:0 auto 24px;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,.1);">
      <img src="<?= htmlspecialchars($success_image) ?>" alt="Featured Image" style="width:100%;height:auto;display:block;">
    </div>
    <?php endif; ?>

    <div class="d-flex gap-3 justify-content-center flex-wrap mb-4">
      <a href="/blog/<?= htmlspecialchars($success_slug) ?>.php" target="_blank" class="btn-gemini-default" style="background:#10b981;">
        <i class="fas fa-external-link-alt"></i> View Live Article
      </a>
      <a href="/admin/blog-posts.php" class="btn-new"><i class="fas fa-list"></i> Manage Posts</a>
      <a href="/admin/blog/generate.php?new=1" class="btn-new"><i class="fas fa-magic"></i> Generate Next Article</a>
    </div>

    <?php if (!empty($success_li)): ?>
    <!-- Ready LinkedIn Post -->
    <div class="linkedin-draft-card text-start" style="max-width:720px;margin:24px auto 0;">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 style="margin:0;font-size:15px;font-weight:700;color:#0a66c2;"><i class="fab fa-linkedin me-2"></i>LinkedIn Post Draft (Ready to Publish)</h5>
        <button type="button" class="btn-copy-li" onclick="navigator.clipboard.writeText(document.getElementById('final_li_copy').value); this.innerHTML='<i class=\'fas fa-check\'></i> Copied!'; setTimeout(()=>this.innerHTML='<i class=\'fas fa-copy\'></i> Copy Draft', 2500);">
          <i class="fas fa-copy"></i> Copy Draft
        </button>
      </div>
      <p style="font-size:12px;color:#64748b;margin-bottom:10px;">Copy this draft and post directly to the Vortexsoft Innovations LinkedIn company page.</p>
      <textarea id="final_li_copy" class="form-control" rows="8" readonly><?= htmlspecialchars($success_li) ?></textarea>
    </div>
    <?php endif; ?>
  </div>

  <?php elseif ($page_state === 'form' || empty($results)): ?>
  <!-- ── FORM STATE ── -->
  <div class="gen-card">
    <div class="gen-card-title"><i class="fas fa-magic" style="color:#CC2228;"></i> Generate New AI-Optimized Blog Post</div>

    <form method="POST" id="generateForm">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <input type="hidden" name="action" value="generate">

      <div class="row g-3 mb-3">
        <div class="col-md-7">
          <label class="form-label" for="topic">Blog Topic / Primary Intent <span style="color:#CC2228">*</span></label>
          <input type="text" class="form-control" id="topic" name="topic" required
                 placeholder="e.g. Healthcare RCM Automation in 2026: Reducing Denials with Enterprise AI"
                 value="<?= htmlspecialchars($topic_val) ?>">
          <div style="font-size:11.5px;color:#94a3b8;margin-top:5px;">Clear, intent-focused title or concept. The AI automatically structures H2/H3s and AEO direct-answer snippets.</div>
        </div>
        <div class="col-md-5">
          <label class="form-label" for="keyword">Target Keyword <span style="color:#CC2228">*</span></label>
          <input type="text" class="form-control" id="keyword" name="keyword" required
                 placeholder="e.g. healthcare RCM automation"
                 value="<?= htmlspecialchars($keyword_val) ?>"
                 list="keyword-suggestions">
          <datalist id="keyword-suggestions">
            <?php foreach ($calendar_keywords as $kw): ?>
            <option value="<?= htmlspecialchars($kw) ?>">
            <?php endforeach; ?>
          </datalist>
          <div style="font-size:11.5px;color:#94a3b8;margin-top:5px;">Target keyword for Google SEO ranking, Perplexity &amp; ChatGPT citations.</div>
        </div>
      </div>

      <div class="d-flex align-items-center gap-3 flex-wrap mt-4">
        <!-- Default: Gemini Button -->
        <button type="submit" name="engine" value="gemini" class="btn-gemini-default" id="geminiBtn"
                <?= $remaining <= 0 ? 'disabled' : '' ?>>
          <i class="fas fa-gem"></i>
          Generate with Gemini (Default AI)
        </button>

        <!-- Multi-engine option -->
        <button type="submit" name="engine" value="all" class="btn-compare" id="allBtn"
                <?= $remaining <= 0 ? 'disabled' : '' ?>>
          <i class="fas fa-layer-group"></i>
          Compare 3 Engines (Gemini + Groq + OpenRouter)
        </button>

        <span style="font-size:12.5px;color:#64748b;"><i class="fas fa-clock me-1"></i> Gemini takes ~2–4 seconds &bull; Includes AI Image &amp; LinkedIn Post</span>
      </div>

      <div style="background:#f8faff;border:1px solid #dbeafe;border-radius:10px;padding:14px;font-size:12.5px;color:#1e3a8a;margin-top:20px;display:flex;align-items:flex-start;gap:12px;">
        <i class="fas fa-shield-alt" style="color:#2563eb;font-size:16px;margin-top:2px;"></i>
        <div>
          <strong>SEO &bull; AEO &bull; GEO Optimizations Automated:</strong>
          Generates a 45-word direct answer in paragraph 1 for Google Featured Snippets &amp; AI Answer Engines, semantic H2/H3 tags, built-in FAQ schema, verifiable Vortexsoft entity facts (ISO 27001:2013, HIPAA, Pune HQ, Bengaluru tech center, Wyoming USA entity), photorealistic 1200x630 featured banner, and LinkedIn draft with 1-click copy.
        </div>
      </div>
    </form>
  </div>

  <!-- Content Calendar Quick Reference -->
  <div class="gen-card">
    <div class="gen-card-title"><i class="fas fa-calendar-alt" style="color:#1C2280;"></i> Quick Topic &amp; Keyword Suggestions</div>
    <div class="d-flex flex-wrap gap-2">
      <?php foreach ($calendar_keywords as $kw): ?>
      <button type="button" class="btn-select" onclick="document.getElementById('keyword').value='<?= htmlspecialchars(addslashes($kw)) ?>'; document.getElementById('topic').value='The Ultimate Guide to <?= htmlspecialchars(addslashes(ucwords($kw))) ?> in 2026';">
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
      Drafts generated for: <strong><?= htmlspecialchars($topic_val) ?></strong> | Target Keyword: <strong><?= htmlspecialchars($keyword_val) ?></strong>
      &nbsp;—&nbsp;<a href="/admin/blog/generate.php?new=1" style="color:#075985;font-weight:600;">Start over</a>
    </div>
  </div>

  <form method="POST" id="approveForm">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
    <input type="hidden" name="action" value="approve">
    <input type="hidden" name="gen_topic" value="<?= htmlspecialchars($topic_val) ?>">
    <input type="hidden" name="gen_keyword" value="<?= htmlspecialchars($keyword_val) ?>">
    <input type="hidden" name="final_title"        id="hidden_title">
    <input type="hidden" name="final_excerpt"      id="hidden_excerpt">
    <input type="hidden" name="final_body"         id="hidden_body">
    <input type="hidden" name="final_category"     id="hidden_category" value="Technology">
    <input type="hidden" name="final_cover_image"  id="hidden_cover_image">
    <input type="hidden" name="final_linkedin_post" id="hidden_linkedin_post">

    <!-- Provider Results Grid -->
    <div class="row g-3 mb-3">
      <?php foreach (array_keys($results) as $key):
        $r    = $results[$key] ?? ['ok' => false, 'error' => 'No result'];
        $meta = $provider_meta[$key] ?? ['name' => ucfirst($key), 'model' => '', 'color' => '#1C2280', 'icon' => 'fa-robot'];
        $colClass = count($results) === 1 ? 'col-lg-12' : (count($results) === 2 ? 'col-lg-6' : 'col-lg-4');
      ?>
      <div class="<?= $colClass ?>">
        <div class="provider-col <?= $r['ok'] ? '' : 'failed' ?>" id="col_<?= $key ?>">
          <div class="provider-header">
            <div>
              <span class="provider-badge" style="background:<?= $meta['color'] ?>;">
                <i class="fas <?= $meta['icon'] ?>"></i> <?= $meta['name'] ?>
              </span>
              <div class="provider-meta"><?= htmlspecialchars($meta['model']) ?></div>
            </div>
            <?php if ($r['ok']): ?>
            <button type="button" class="btn-select" id="select_<?= $key ?>" onclick="selectDraft('<?= $key ?>')">
              <i class="fas fa-check-circle"></i> Select &amp; Edit
            </button>
            <?php endif; ?>
          </div>

          <div class="provider-body">
            <?php if (!$r['ok']): ?>
            <div class="failed-msg text-danger p-3">
              <i class="fas fa-exclamation-triangle mt-1"></i>
              <div>
                <strong>Generation failed:</strong><br>
                <span style="font-size:12px;"><?= htmlspecialchars($r['error'] ?? 'Unknown error') ?></span>
              </div>
            </div>
            <?php else:
              $d  = $resData = $r['data'];
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

    <!-- ── Inline Editor & Publishing Console (Appears on Select) ── -->
    <div class="editor-section" id="editorSection">
      <div class="gen-card-title" style="margin-bottom:20px;justify-content:space-between;">
        <span><i class="fas fa-edit" style="color:#1C2280;"></i> Edit &amp; Publish — <span id="editorProviderName" style="color:#CC2228;"></span> Draft</span>
        <span class="badge bg-primary px-3 py-2" style="font-size:11px;">SEO + AEO + GEO Optimized</span>
      </div>

      <!-- 1. AI Featured Image Preview & Regenerator -->
      <div class="image-preview-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <label class="editor-label m-0" style="color:#1e3a8a;"><i class="fas fa-image me-1"></i> AI Featured Banner Image (1200×630)</label>
          <button type="button" class="btn btn-sm btn-outline-primary" id="regenImgBtn" onclick="regenerateImageLive()">
            <i class="fas fa-sync-alt me-1"></i> Regenerate Image
          </button>
        </div>
        <div class="row g-3 align-items-center">
          <div class="col-md-5">
            <img id="cover_image_preview" src="" alt="Blog Featured Banner" style="width:100%;height:170px;object-fit:cover;border-radius:8px;">
          </div>
          <div class="col-md-7">
            <label class="editor-label">Image Generation Prompt</label>
            <textarea class="form-control" id="edit_image_prompt" rows="3" style="font-size:12.5px;"></textarea>
            <div style="font-size:11px;color:#64748b;margin-top:4px;">You can customize the prompt above and click "Regenerate Image" to render a fresh AI banner.</div>
          </div>
        </div>
      </div>

      <!-- 2. LinkedIn Post Draft (Admin One-Click Copy) -->
      <div class="linkedin-draft-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <label class="editor-label m-0" style="color:#0a66c2;font-size:13px;"><i class="fab fa-linkedin me-1" style="font-size:16px;"></i> LinkedIn Post Draft (Admin Exclusive)</label>
          <button type="button" class="btn-copy-li" id="copyLiBtn" onclick="copyLinkedInDraft()">
            <i class="fas fa-copy"></i> Copy LinkedIn Post
          </button>
        </div>
        <div style="font-size:12px;color:#64748b;margin-bottom:8px;">Generated with engagement hooks, bullet points, CTA to Vortexsoft Innovations, and hashtags:</div>
        <textarea class="form-control" id="edit_linkedin_post" rows="7" placeholder="LinkedIn post draft"></textarea>
      </div>

      <!-- 3. Title & Category -->
      <div class="row g-3 mb-3">
        <div class="col-md-8">
          <label class="editor-label">Post Title (SEO Optimized)</label>
          <input type="text" class="editor-field" id="edit_title" placeholder="Post title">
        </div>
        <div class="col-md-4">
          <label class="editor-label">Category</label>
          <select class="form-select" id="edit_category" onchange="document.getElementById('hidden_category').value=this.value">
            <?php foreach (['Healthcare BPO','Technology','AI & Automation','Publishing','Real Estate BPO','Accounting & Payroll','Digital Marketing','Staffing & HR','General'] as $cat): ?>
            <option value="<?= $cat ?>"><?= $cat ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- 4. Excerpt -->
      <div class="mb-3">
        <label class="editor-label">Meta Description Excerpt <span style="color:#94a3b8;font-weight:400;text-transform:none;">(under 160 chars)</span></label>
        <input type="text" class="editor-field" id="edit_excerpt" maxlength="160" placeholder="One sentence summary for search engines">
        <div id="excerpt_counter" style="font-size:11px;color:#94a3b8;margin-top:4px;">0/160</div>
      </div>

      <!-- 5. Body HTML -->
      <div class="mb-4">
        <label class="editor-label">Body Content (HTML)</label>
        <div style="font-size:11.5px;color:#94a3b8;margin-bottom:8px;">Formatted in semantic HTML. The opening paragraph contains the AEO direct-answer snippet. End includes FAQs.</div>
        <textarea class="editor-body" id="edit_body" rows="18"></textarea>
      </div>

      <div class="d-flex align-items-center gap-3 flex-wrap">
        <button type="submit" class="btn-approve" id="approveBtn" onclick="prepareApprove()">
          <i class="fas fa-check-circle"></i> Approve &amp; Publish Blog Post
        </button>
        <a href="/admin/blog/generate.php?new=1" class="btn-new"><i class="fas fa-redo"></i> Discard &amp; Regenerate</a>
        <div style="font-size:12px;color:#94a3b8;margin-left:auto;">
          <i class="fas fa-shield-alt me-1"></i> Post will immediately publish to website with live URL and cover image
        </div>
      </div>
    </div><!-- /editor -->
  </form>

  <!-- Quick Regenerate bar -->
  <div class="gen-card mt-3" style="padding:20px;">
    <form method="POST" id="regenForm">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <input type="hidden" name="action" value="generate">
      <input type="hidden" name="engine" value="gemini">
      <div class="row g-2 align-items-end">
        <div class="col-md-5">
          <label class="form-label" style="font-size:12px;">Refine Topic</label>
          <input type="text" class="form-control" name="topic" value="<?= htmlspecialchars($topic_val) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label" style="font-size:12px;">Target Keyword</label>
          <input type="text" class="form-control" name="keyword" value="<?= htmlspecialchars($keyword_val) ?>">
        </div>
        <div class="col-md-3">
          <button type="submit" class="btn-gemini-default w-100" style="padding:11px 16px;">
            <i class="fas fa-sync-alt"></i> Regenerate with Gemini
          </button>
        </div>
      </div>
    </form>
  </div>

  <?php endif; ?>

</main>

<script src="/assets/vendor/bootstrap.bundle.min.js"></script>
<script>
// Store draft data
const drafts = <?= json_encode(array_map(function($r) {
    if (!$r['ok']) return null;
    return [
        'title'         => $r['data']['title']         ?? '',
        'excerpt'       => $r['data']['excerpt']       ?? '',
        'body'          => $r['data']['body']          ?? '',
        'image_prompt'  => $r['data']['image_prompt']  ?? '',
        'image_url'     => $r['data']['image_url']     ?? '',
        'linkedin_post' => $r['data']['linkedin_post'] ?? '',
    ];
}, $results)) ?>;

const draftKeys  = <?= json_encode(array_keys($results)) ?>;
let selectedProvider = null;

function selectDraft(key) {
    const d = drafts[key];
    if (!d) return;

    // Highlight selected card
    draftKeys.forEach(k => {
        const col = document.getElementById('col_' + k);
        const btn = document.getElementById('select_' + k);
        if (col) col.classList.toggle('selected', k === key);
        if (btn) btn.classList.toggle('active', k === key);
    });

    const providerNames = {gemini: 'Gemini (Default)', groq: 'Groq', openrouter: 'OpenRouter'};
    document.getElementById('editorProviderName').textContent = providerNames[key] || key;
    document.getElementById('edit_title').value          = d.title;
    document.getElementById('edit_excerpt').value        = d.excerpt;
    document.getElementById('edit_body').value           = d.body;
    document.getElementById('edit_image_prompt').value   = d.image_prompt;
    document.getElementById('edit_linkedin_post').value  = d.linkedin_post;

    // Update image preview & hidden input
    const imgPreview = document.getElementById('cover_image_preview');
    if (imgPreview) {
        imgPreview.src = d.image_url || '/icon.jpg';
    }
    document.getElementById('hidden_cover_image').value = d.image_url || '';

    updateExcerptCounter();

    const editorSection = document.getElementById('editorSection');
    editorSection.classList.add('visible');
    editorSection.scrollIntoView({behavior:'smooth', block:'start'});
    selectedProvider = key;
}

// Auto-select Gemini draft if only one draft generated
<?php if (count($results) === 1 && isset($results['gemini']) && $results['gemini']['ok']): ?>
document.addEventListener('DOMContentLoaded', () => {
    selectDraft('gemini');
});
<?php endif; ?>

function prepareApprove() {
    document.getElementById('hidden_title').value        = document.getElementById('edit_title').value.trim();
    document.getElementById('hidden_excerpt').value      = document.getElementById('edit_excerpt').value.trim();
    document.getElementById('hidden_body').value         = document.getElementById('edit_body').value.trim();
    document.getElementById('hidden_category').value     = document.getElementById('edit_category').value;
    document.getElementById('hidden_linkedin_post').value = document.getElementById('edit_linkedin_post').value.trim();
}

function updateExcerptCounter() {
    const el = document.getElementById('edit_excerpt');
    if (!el) return;
    const len = el.value.length;
    const counter = document.getElementById('excerpt_counter');
    if (counter) {
        counter.textContent = len + '/160';
        counter.style.color = len > 155 ? '#CC2228' : '#94a3b8';
    }
}
document.getElementById('edit_excerpt')?.addEventListener('input', updateExcerptCounter);

function copyLinkedInDraft() {
    const txt = document.getElementById('edit_linkedin_post').value;
    navigator.clipboard.writeText(txt).then(() => {
        const btn = document.getElementById('copyLiBtn');
        btn.classList.add('copied');
        btn.innerHTML = '<i class="fas fa-check"></i> Copied to Clipboard!';
        setTimeout(() => {
            btn.classList.remove('copied');
            btn.innerHTML = '<i class="fas fa-copy"></i> Copy LinkedIn Post';
        }, 3000);
    });
}

function regenerateImageLive() {
    const btn = document.getElementById('regenImgBtn');
    const prompt = document.getElementById('edit_image_prompt').value;
    const slug = document.getElementById('edit_title').value || 'blog-banner';
    const csrf = document.querySelector('input[name="csrf_token"]').value;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generating AI Image...';

    const fd = new FormData();
    fd.append('action', 'ajax_generate_image');
    fd.append('csrf_token', csrf);
    fd.append('prompt', prompt);
    fd.append('slug', slug);

    fetch('/admin/blog/generate.php', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Regenerate Image';
        if (res.success && res.image_url) {
            document.getElementById('cover_image_preview').src = res.image_url + '?t=' + Date.now();
            document.getElementById('hidden_cover_image').value = res.image_url;
            if (selectedProvider && drafts[selectedProvider]) {
                drafts[selectedProvider].image_url = res.image_url;
            }
        } else {
            alert(res.message || 'Image generation failed. Please try again.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Regenerate Image';
        alert('Error connecting to image generation service.');
    });
}

// Button loading state
['generateForm', 'regenForm'].forEach(id => {
    const f = document.getElementById(id);
    if (f) f.addEventListener('submit', function(e) {
        const submitter = e.submitter;
        if (submitter) {
            submitter.disabled = true;
            submitter.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating with AI (~3–6s)...';
        }
    });
});

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
if (isset($_GET['new'])) {
    unset($_SESSION['ai_gen_results'], $_SESSION['ai_gen_topic'], $_SESSION['ai_gen_keyword']);
}
?>
