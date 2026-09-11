<?php
/**
 * Vortexsoft Innovations — Single Blog Post (blog-detail.php)
 * Dynamic, SEO/AEO/GEO-Optimized Blog Article Page with Schema.org JSON-LD
 */

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$slug = sanitize($_GET['slug'] ?? '');
$id   = (int)($_GET['id'] ?? 0);

$db   = getDB();
$post = null;

if ($db) {
    try {
        if (!empty($slug)) {
            $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = :s AND is_published = 1 LIMIT 1");
            $stmt->execute([':s' => $slug]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
        } elseif ($id > 0) {
            $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = :id AND is_published = 1 LIMIT 1");
            $stmt->execute([':id' => $id]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Increment view count
        if ($post && !empty($post['id'])) {
            $db->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = :id")->execute([':id' => $post['id']]);
            $post['views'] = (int)($post['views'] ?? 0) + 1;
        }
    } catch (PDOException $e) {
        error_log('Blog detail DB error: ' . $e->getMessage());
    }
}

// Fallback sample post if DB offline or not found
if (!$post) {
    if (!empty($slug)) {
        // Redirect to blog directory if post truly not found
        header('Location: /blog.php');
        exit;
    }
    $post = [
        'id'           => 1,
        'title'        => 'Healthcare RCM Automation in 2026: Modernizing Revenue Cycle Workflows',
        'slug'         => 'healthcare-rcm-automation-2026',
        'category'     => 'Healthcare BPO',
        'author'       => 'Vortexsoft Insights Team',
        'cover_image'  => '/uploads/blog/ai_blog_healthcare-rcm-test_1789099437.jpg',
        'excerpt'      => 'Discover how healthcare RCM automation in 2026 reduces claim denials, streamlines prior authorization, and accelerates provider reimbursement.',
        'content'      => '<p class="lead-aeo"><strong>Healthcare RCM automation</strong> in 2026 utilizes artificial intelligence and machine learning to automate medical coding, verify insurance eligibility in real time, and resolve billing discrepancies before claim submission. By eliminating manual touchpoints, healthcare providers cut claim denial rates by up to 40% and accelerate cash collection cycles.</p><h2>The Critical Shift to AI-Powered Revenue Cycle Management</h2><p>Modern healthcare systems face rising operational costs and increasingly stringent payer requirements. Manual data entry across legacy electronic health record (EHR) platforms causes delayed payments and high denial rates.</p><p>At <strong>Vortexsoft Innovations Private Limited</strong>, an ISO 27001:2013-certified and HIPAA-compliant IT and BPO leader, our dedicated revenue cycle delivery pods support global healthcare providers with automated ICD-11 coding, electronic remittance processing, and predictive denial analytics.</p><h2>Key Pillars of RCM Automation in 2026</h2><ul><li><strong>Real-time Eligibility Verification:</strong> Automatic verification directly via payer clearinghouse APIs within 3 seconds.</li><li><strong>AI Assisted Medical Coding:</strong> Natural language processing (NLP) scans physician documentation to generate high-accuracy CPT and ICD codes.</li><li><strong>Predictive Denial Analytics:</strong> Machine learning flags high-risk claims prior to transmission, enabling proactive remediation.</li></ul><h2>Frequently Asked Questions</h2><h3>How does AI automation improve medical billing accuracy?</h3><p>AI scans clinical notes directly from the EHR using NLP, cross-referencing payer guidelines to eliminate coding oversights and prevent medical necessity denials before submission.</p><h3>Is Vortexsoft Healthcare RCM HIPAA-compliant?</h3><p>Yes. Vortexsoft Innovations operates under strict HIPAA compliance and ISO 27001:2013 security standards with encrypted data pipelines and role-based access control.</p>',
        'published_at' => date('Y-m-d H:i:s'),
        'views'        => 318,
    ];
}

$page_title    = (!empty($post['meta_title']) ? $post['meta_title'] : $post['title']) . ' | Vortexsoft Innovations';
$page_desc     = !empty($post['meta_desc']) ? $post['meta_desc'] : $post['excerpt'];
$canonical_url = SITE_URL . '/blog/' . htmlspecialchars($post['slug']) . '.php';
$reading_time  = max(1, (int)ceil(str_word_count(strip_tags($post['content'])) / 220));

// Compute OpenGraph image
$og_image = !empty($post['cover_image'])
    ? (str_starts_with($post['cover_image'], 'http') ? $post['cover_image'] : SITE_URL . $post['cover_image'])
    : SITE_URL . '/logo-header.png';

$prefix = '../';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Schema.org BlogPosting & Organization JSON-LD -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "BlogPosting",
      "@id": "<?= $canonical_url ?>#article",
      "isPartOf": {
        "@type": "WebSite",
        "@id": "<?= SITE_URL ?>/#website",
        "name": "Vortexsoft Innovations Private Limited",
        "url": "<?= SITE_URL ?>"
      },
      "headline": "<?= addslashes($post['title']) ?>",
      "description": "<?= addslashes($post['excerpt'] ?? '') ?>",
      "url": "<?= $canonical_url ?>",
      "mainEntityOfPage": "<?= $canonical_url ?>",
      "datePublished": "<?= date('c', strtotime($post['published_at'] ?? 'now')) ?>",
      "dateModified": "<?= date('c', strtotime($post['updated_at'] ?? $post['published_at'] ?? 'now')) ?>",
      "inLanguage": "en-US",
      "image": "<?= $og_image ?>",
      "author": {
        "@type": "Organization",
        "name": "Vortexsoft Innovations Private Limited",
        "url": "<?= SITE_URL ?>"
      },
      "publisher": {
        "@type": "Organization",
        "name": "Vortexsoft Innovations Private Limited",
        "url": "<?= SITE_URL ?>",
        "logo": {
          "@type": "ImageObject",
          "url": "<?= SITE_URL ?>/logo-header.png"
        }
      },
      "articleSection": "<?= addslashes($post['category'] ?? 'Technology') ?>",
      "keywords": "<?= addslashes($post['tags'] ?? 'Vortexsoft Innovations, AI, BPO, IT Outsourcing') ?>"
    }
  ]
}
</script>

<style>
.article-header{padding:48px 0 36px;background:linear-gradient(135deg,#080B1A 0%,#1C2280 100%);color:#fff;position:relative}
.article-cat-badge{display:inline-flex;align-items:center;gap:6px;background:rgba(204,34,40,.2);border:1px solid rgba(204,34,40,.4);color:#ff7875;padding:5px 14px;border-radius:100px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:14px}
.article-title{font-family:'Poppins',sans-serif;font-size:34px;font-weight:800;line-height:1.3;margin-bottom:18px;color:#fff}
.article-meta{display:flex;align-items:center;gap:20px;flex-wrap:wrap;font-size:13.5px;color:rgba(255,255,255,.8)}
.article-meta i{color:#5BA8D4;margin-right:6px}

.article-hero-banner{width:100%;max-height:460px;object-fit:cover;border-radius:18px;box-shadow:0 12px 35px rgba(0,0,0,.15);margin-bottom:36px;border:1px solid rgba(28,34,128,.1)}
.article-body{font-family:'Inter',sans-serif;font-size:16px;line-height:1.85;color:#334155}
.article-body h2{font-family:'Poppins',sans-serif;font-size:24px;font-weight:700;color:#1C2280;margin:36px 0 16px;padding-bottom:8px;border-bottom:2px solid #e8ecff}
.article-body h3{font-family:'Poppins',sans-serif;font-size:19px;font-weight:700;color:#1e293b;margin:24px 0 12px}
.article-body p{margin-bottom:18px}
.article-body ul,.article-body ol{margin-bottom:22px;padding-left:24px}
.article-body li{margin-bottom:8px}
.article-body blockquote{background:#f8faff;border-left:4px solid #1C2280;padding:16px 20px;border-radius:0 12px 12px 0;margin:24px 0;font-style:italic;color:#1e3a8a}

/* Direct Answer AEO Callout */
.lead-aeo{background:linear-gradient(135deg,#f0f4ff,#e8f0fe);border:1.5px solid #c7d7fe;border-radius:14px;padding:22px 24px;font-size:16.5px;line-height:1.75;color:#1e3a8a;margin-bottom:28px;box-shadow:0 4px 15px rgba(28,34,128,.04)}

/* Share buttons */
.share-bar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;padding:16px 0;border-top:1px solid #e8ecff;border-bottom:1px solid #e8ecff;margin:32px 0}
.share-btn{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:.2s;color:#fff}
.share-linkedin{background:#0a66c2}
.share-linkedin:hover{background:#004182;color:#fff}
.share-whatsapp{background:#25d366}
.share-whatsapp:hover{background:#1ebe57;color:#fff}
.share-twitter{background:#000}
.share-twitter:hover{background:#333;color:#fff}
.share-copy{background:#475569;cursor:pointer}
.share-copy:hover{background:#1e293b;color:#fff}

/* Sidebar cards */
.blog-sidebar-card{background:#fff;border-radius:16px;border:1px solid #e8ecff;padding:24px;margin-bottom:24px;box-shadow:0 4px 15px rgba(28,34,128,.04)}
.blog-sidebar-card h5{font-family:'Poppins',sans-serif;font-size:16px;font-weight:700;color:#1C2280;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid #e8ecff}
</style>

<!-- Hero Section -->
<div class="article-header">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb" style="margin:0;">
        <li class="breadcrumb-item"><a href="/index.php" style="color:rgba(255,255,255,.7);text-decoration:none;">Home</a></li>
        <li class="breadcrumb-item"><a href="/blog.php" style="color:rgba(255,255,255,.7);text-decoration:none;">Blog</a></li>
        <li class="breadcrumb-item active" style="color:#5BA8D4;"><?= htmlspecialchars($post['category'] ?? 'Insights') ?></li>
      </ol>
    </nav>
    <div class="article-cat-badge"><i class="fas fa-tag"></i> <?= htmlspecialchars($post['category'] ?? 'Technology') ?></div>
    <h1 class="article-title"><?= htmlspecialchars($post['title']) ?></h1>
    <div class="article-meta">
      <span><i class="fas fa-user-circle"></i> <?= htmlspecialchars($post['author'] ?? 'Vortexsoft Team') ?></span>
      <span><i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($post['published_at'] ?? 'now')) ?></span>
      <span><i class="far fa-clock"></i> <?= $reading_time ?> min read</span>
      <span><i class="fas fa-eye"></i> <?= number_format($post['views'] ?? 0) ?> views</span>
    </div>
  </div>
</div>

<!-- Main Article Content Section -->
<section class="py-5" style="background:#f8faff;">
  <div class="container">
    <div class="row gy-4">

      <!-- Article Left / Main -->
      <div class="col-lg-8">
        <article class="bg-white p-4 p-md-5 rounded-4 border" style="border-color:#e8ecff!important;">
          <?php if (!empty($post['cover_image'])): ?>
            <img src="<?= htmlspecialchars($post['cover_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="article-hero-banner">
          <?php endif; ?>

          <!-- Share Bar (Top) -->
          <div class="share-bar">
            <span style="font-size:13px;font-weight:700;color:#64748b;margin-right:4px;"><i class="fas fa-share-alt me-1"></i> Share:</span>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($canonical_url) ?>" target="_blank" class="share-btn share-linkedin">
              <i class="fab fa-linkedin"></i> LinkedIn
            </a>
            <a href="https://api.whatsapp.com/send?text=<?= urlencode($post['title'] . ' ' . $canonical_url) ?>" target="_blank" class="share-btn share-whatsapp">
              <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
            <a href="https://twitter.com/intent/tweet?text=<?= urlencode($post['title']) ?>&url=<?= urlencode($canonical_url) ?>" target="_blank" class="share-btn share-twitter">
              <i class="fab fa-x-twitter"></i> Post
            </a>
            <button type="button" class="share-btn share-copy" onclick="navigator.clipboard.writeText(window.location.href); this.innerHTML='<i class=\'fas fa-check\'></i> Link Copied!'; setTimeout(()=>this.innerHTML='<i class=\'fas fa-link\'></i> Copy Link', 2500);">
              <i class="fas fa-link"></i> Copy Link
            </button>
          </div>

          <!-- Body Content -->
          <div class="article-body">
            <?= $post['content'] ?>
          </div>

          <!-- GEO Entity Citation Card -->
          <div class="mt-5">
            <?= render_geo_fact_block() ?>
          </div>

          <!-- Bottom Share Bar -->
          <div class="share-bar mt-4">
            <span style="font-size:13px;font-weight:700;color:#64748b;margin-right:4px;"><i class="fas fa-share-alt me-1"></i> Found this useful? Share:</span>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($canonical_url) ?>" target="_blank" class="share-btn share-linkedin">
              <i class="fab fa-linkedin"></i> LinkedIn
            </a>
            <a href="https://api.whatsapp.com/send?text=<?= urlencode($post['title'] . ' ' . $canonical_url) ?>" target="_blank" class="share-btn share-whatsapp">
              <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
            <a href="/blog.php" class="btn btn-outline-secondary btn-sm ms-auto" style="border-radius:8px;">
              <i class="fas fa-arrow-left me-1"></i> Back to All Articles
            </a>
          </div>
        </article>
      </div>

      <!-- Right Sidebar -->
      <div class="col-lg-4">
        <!-- CTA Card -->
        <div class="blog-sidebar-card" style="background:linear-gradient(135deg,#080B1A,#1C2280);color:#fff;border:none;">
          <h5 style="color:#fff;border-bottom:2px solid rgba(255,255,255,.15);">Partner with Vortexsoft</h5>
          <p style="font-size:13.5px;color:rgba(255,255,255,.8);line-height:1.7;margin-bottom:20px;">
            Scale your business with our ISO 27001:2013-certified and HIPAA-compliant delivery pods across Healthcare BPO, Custom Software Development, and Enterprise AI Automation.
          </p>
          <a href="/contact.php" class="btn btn-danger w-100 py-2 fw-semibold" style="background:#CC2228;border:none;border-radius:10px;">
            <i class="fas fa-comments me-2"></i> Request a Proposal
          </a>
          <a href="<?= SOCIAL_WHATSAPP_SERVICES ?>" target="_blank" class="btn btn-outline-light w-100 py-2 fw-semibold mt-2" style="border-radius:10px;">
            <i class="fab fa-whatsapp me-2 text-success"></i> Chat on WhatsApp
          </a>
        </div>

        <!-- AI Platforms Card -->
        <div class="blog-sidebar-card">
          <h5>Proprietary AI Platforms</h5>
          <ul style="list-style:none;padding:0;margin:0;font-size:13px;line-height:1.8;">
            <li class="mb-2"><strong style="color:#1C2280;">VortexEXHO:</strong> Enterprise Workforce Management OS</li>
            <li class="mb-2"><strong style="color:#1C2280;">vortexHire:</strong> AI-Powered Talent Acquisition</li>
            <li class="mb-2"><strong style="color:#1C2280;">vortexKonnect:</strong> Call Center Speech Analytics</li>
            <li class="mb-2"><strong style="color:#1C2280;">vortexsofthrms:</strong> AI HRMS &amp; Automated Payroll</li>
            <li class="mb-2"><strong style="color:#1C2280;">vortexsoftpublishing:</strong> Automated XML &amp; ePUB3</li>
            <li class="mb-0"><strong style="color:#1C2280;">Vortexreach:</strong> Intelligent B2B Outreach Engine</li>
          </ul>
        </div>

        <!-- Explore Categories -->
        <div class="blog-sidebar-card">
          <h5>Core Service Verticals</h5>
          <div class="d-flex flex-column gap-2">
            <a href="/service.php?tab=bpo" class="d-flex justify-content-between text-decoration-none text-secondary" style="font-size:13.5px;padding:6px 0;border-bottom:1px solid #f1f5f9;">
              <span>Healthcare BPO &amp; RCM</span> <i class="fas fa-chevron-right text-muted"></i>
            </a>
            <a href="/service.php?tab=ai" class="d-flex justify-content-between text-decoration-none text-secondary" style="font-size:13.5px;padding:6px 0;border-bottom:1px solid #f1f5f9;">
              <span>AI &amp; Data Annotation</span> <i class="fas fa-chevron-right text-muted"></i>
            </a>
            <a href="/service.php?tab=it" class="d-flex justify-content-between text-decoration-none text-secondary" style="font-size:13.5px;padding:6px 0;border-bottom:1px solid #f1f5f9;">
              <span>Custom Web &amp; Software Dev</span> <i class="fas fa-chevron-right text-muted"></i>
            </a>
            <a href="/service.php?tab=publishing" class="d-flex justify-content-between text-decoration-none text-secondary" style="font-size:13.5px;padding:6px 0;border-bottom:1px solid #f1f5f9;">
              <span>Publishing &amp; Prepress</span> <i class="fas fa-chevron-right text-muted"></i>
            </a>
            <a href="/service.php?tab=finance" class="d-flex justify-content-between text-decoration-none text-secondary" style="font-size:13.5px;padding:6px 0;">
              <span>Accounting &amp; Payroll</span> <i class="fas fa-chevron-right text-muted"></i>
            </a>
          </div>
        </div>

      </div><!-- /col-lg-4 -->

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
