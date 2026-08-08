<?php
/**
 * Vortexsoft Innovations — Blog Page (blog.php)
 * Reads published posts from MySQL database
 */

$page_title   = 'Blog — IT, BPO & AI Insights | Vortexsoft Group';
$page_desc    = 'Explore Vortexsoft Group\'s blog for expert insights on IT outsourcing, healthcare BPO, AI solutions, publishing services, real estate BPO, and digital transformation trends.';
$canonical_url = 'https://www.vortexsoftinnovations.com/blog.php';
$prefix       = './';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Fetch published blog posts
$db   = getDB();
$posts = [];
if ($db) {
    try {
        $cat    = sanitize($_GET['cat'] ?? '');
        $search = sanitize($_GET['q']   ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));

        $where  = "WHERE is_published = 1";
        $params = [];
        if ($cat)    { $where .= " AND category = :cat"; $params[':cat'] = $cat; }
        if ($search) { $where .= " AND (title LIKE :q OR excerpt LIKE :q2 OR tags LIKE :q3)"; $params[':q']=$params[':q2']=$params[':q3']='%'.$search.'%'; }

        $total = (int)$db->prepare("SELECT COUNT(*) FROM blog_posts $where")->execute($params) ? $db->query("SELECT COUNT(*) FROM blog_posts $where")->fetchColumn() : 0;
        $pg    = paginate($total, 9, $page);

        $stmt = $db->prepare("SELECT id,title,slug,excerpt,author,category,cover_image,published_at,views FROM blog_posts $where ORDER BY is_featured DESC, published_at DESC LIMIT :limit OFFSET :offset");
        foreach($params as $k=>&$v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit',  $pg['per_page'],  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $pg['offset'],    PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll();

        $cats_stmt = $db->query("SELECT category, COUNT(*) as cnt FROM blog_posts WHERE is_published=1 GROUP BY category ORDER BY cnt DESC");
        $categories = $cats_stmt->fetchAll();
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}

// Fallback sample posts if DB is empty or not yet configured
if (empty($posts)) {
    $posts = [
        ['id'=>1,'title'=>'How Vortexsoft Transforms Healthcare Revenue Cycle Management','slug'=>'healthcare-rcm-transformation','excerpt'=>'Learn how our end-to-end medical billing and coding services help healthcare providers reduce claim denials by up to 40% and improve cash flow.','author'=>'Vortexsoft Team','category'=>'Healthcare BPO','cover_image'=>null,'published_at'=>date('Y-m-d H:i:s',strtotime('-2 days')),'views'=>142],
        ['id'=>2,'title'=>'ePUB3 Accessibility & WCAG 2.1 Compliance in Academic Publishing','slug'=>'epub3-accessibility-wcag','excerpt'=>'A comprehensive guide to creating fully accessible digital publications that comply with WCAG 2.1 and international accessibility standards.','author'=>'Publishing Team','category'=>'Publishing','cover_image'=>null,'published_at'=>date('Y-m-d H:i:s',strtotime('-5 days')),'views'=>98],
        ['id'=>3,'title'=>'AI Data Annotation Best Practices for Computer Vision Projects','slug'=>'ai-data-annotation-best-practices','excerpt'=>'Explore the key quality metrics, annotation tools, and quality control processes that ensure high-accuracy training datasets for AI models.','author'=>'AI Research Team','category'=>'AI & Data','cover_image'=>null,'published_at'=>date('Y-m-d H:i:s',strtotime('-8 days')),'views'=>215],
        ['id'=>4,'title'=>'Top 5 Benefits of Outsourcing Real Estate CAM Audit Services','slug'=>'real-estate-cam-audit-outsourcing','excerpt'=>'Commercial real estate firms save 50%+ in operational costs and dramatically improve lease compliance when outsourcing CAM reconciliation.','author'=>'Real Estate Team','category'=>'Real Estate BPO','cover_image'=>null,'published_at'=>date('Y-m-d H:i:s',strtotime('-12 days')),'views'=>76],
        ['id'=>5,'title'=>'Why Indian BPO Companies Dominate Global IT Outsourcing in 2025','slug'=>'india-bpo-it-outsourcing-2025','excerpt'=>'India\'s BPO sector continues to lead global outsourcing with cost efficiency, skilled workforce, and rapidly advancing AI capabilities.','author'=>'Vortexsoft Team','category'=>'Industry Insights','cover_image'=>null,'published_at'=>date('Y-m-d H:i:s',strtotime('-16 days')),'views'=>324],
        ['id'=>6,'title'=>'Generative AI in Business Process Outsourcing: Opportunities & Challenges','slug'=>'generative-ai-bpo-opportunities','excerpt'=>'GenAI is reshaping BPO operations from document processing to customer service automation. Here\'s how businesses can leverage the opportunity.','author'=>'AI Research Team','category'=>'AI & Data','cover_image'=>null,'published_at'=>date('Y-m-d H:i:s',strtotime('-20 days')),'views'=>189],
    ];
    $pg = paginate(count($posts), 9, 1);
    $categories = [['category'=>'Healthcare BPO','cnt'=>1],['category'=>'Publishing','cnt'=>1],['category'=>'AI & Data','cnt'=>2],['category'=>'Real Estate BPO','cnt'=>1],['category'=>'Industry Insights','cnt'=>1]];
}

$cat_icons = ['Healthcare BPO'=>'fa-heartbeat','Publishing'=>'fa-book','AI & Data'=>'fa-robot','Real Estate BPO'=>'fa-building','Industry Insights'=>'fa-lightbulb','Company News'=>'fa-newspaper','Technology'=>'fa-laptop-code','Finance'=>'fa-chart-bar','General'=>'fa-pen'];

require_once __DIR__ . '/includes/header.php';
?>
<style>
.page-hero{background:linear-gradient(135deg,#080B1A 0%,#1C2280 55%,#0D1035 100%);padding:80px 0 70px;position:relative;overflow:hidden}
.page-hero::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:50px 50px}
.page-hero h1{font-size:clamp(2rem,4vw,3rem);font-weight:800;color:#fff}
.breadcrumb-item,.breadcrumb-item a{color:rgba(255,255,255,.6);font-size:14px}
.breadcrumb-item.active{color:rgba(255,255,255,.9)}
.breadcrumb-item+.breadcrumb-item::before{color:rgba(255,255,255,.4)}
.blog-card{background:#fff;border-radius:18px;overflow:hidden;border:1px solid #e8ecff;transition:all .3s;height:100%}
.blog-card:hover{transform:translateY(-6px);box-shadow:0 16px 40px rgba(28,34,128,.14);border-color:transparent}
.blog-card .card-img{height:200px;object-fit:cover;width:100%}
.blog-card .card-img-placeholder{height:200px;display:flex;align-items:center;justify-content:center;font-size:48px}
.blog-card .card-body{padding:24px}
.blog-cat-badge{font-size:11px;font-weight:700;padding:4px 10px;border-radius:100px;background:rgba(28,34,128,.07);color:#1C2280;display:inline-block;margin-bottom:12px}
.blog-card h3{font-size:16px;font-weight:700;color:#1C2280;line-height:1.4;margin-bottom:8px}
.blog-card p{font-size:13.5px;color:#475569;line-height:1.7;margin-bottom:16px}
.blog-meta{font-size:12px;color:#94a3b8;display:flex;gap:14px;flex-wrap:wrap}
.blog-meta i{color:#CC2228;margin-right:4px}
.read-more{font-size:13px;font-weight:700;color:#1C2280;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:.2s}
.read-more:hover{color:#CC2228;gap:10px}
.sidebar-card{background:#fff;border-radius:16px;padding:24px;border:1px solid #e8ecff;margin-bottom:20px}
.sidebar-card h5{font-family:'Poppins',sans-serif;font-weight:700;font-size:15px;color:#1C2280;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid #e8ecff}
.cat-item{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f0f4ff;text-decoration:none;color:#475569;font-size:14px;transition:.2s}
.cat-item:hover{color:#1C2280;padding-left:6px}
.cat-badge{background:rgba(28,34,128,.08);color:#1C2280;font-size:11px;font-weight:700;padding:2px 8px;border-radius:100px}
</style>

<!-- Hero -->
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Blog</li></ol></nav>
    <h1>Vortexsoft <span style="color:#5BA8D4;">Blog</span></h1>
    <p style="color:rgba(255,255,255,.75);font-size:16px;margin-top:12px;max-width:560px;">Expert insights on IT outsourcing, AI solutions, healthcare BPO, publishing, and industry trends from the Vortexsoft team.</p>
  </div>
</div>

<section class="py-5" style="background:var(--bg-light,#f0f2ff);">
  <div class="container">
    <div class="row gy-4">
      <!-- Blog Posts Grid -->
      <div class="col-lg-8">
        <?php if (!empty($search) || !empty($cat)): ?>
        <div class="mb-4" style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e8ecff;">
          <span style="color:#64748b;font-size:14px;">
            <?php if($search): ?>Search results for "<strong><?= htmlspecialchars($search) ?></strong>"<?php endif; ?>
            <?php if($cat): ?> in category "<strong><?= htmlspecialchars($cat) ?></strong>"<?php endif; ?>
            — <?= count($posts) ?> post(s) found.
          </span>
          <a href="blog.php" style="font-size:13px;color:#CC2228;float:right;">Clear filters ×</a>
        </div>
        <?php endif; ?>

        <div class="row g-4">
          <?php foreach($posts as $i=>$post):
            $bg_colors = ['rgba(28,34,128,.06)','rgba(204,34,40,.06)','rgba(91,168,212,.06)','rgba(16,185,129,.06)','rgba(245,158,11,.06)','rgba(139,92,246,.06)'];
            $icon = $cat_icons[$post['category']] ?? 'fa-pen';
            $bg   = $bg_colors[$i % count($bg_colors)];
          ?>
          <div class="col-md-6 scroll-reveal" style="transition-delay:<?= ($i%2)*0.1 ?>s">
            <div class="blog-card d-flex flex-column">
              <?php if ($post['cover_image']): ?>
                <img src="<?= htmlspecialchars($post['cover_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="card-img">
              <?php else: ?>
                <div class="card-img-placeholder" style="background:<?= $bg ?>"><i class="fas <?= $icon ?>" style="color:#1C2280;opacity:.4;"></i></div>
              <?php endif; ?>
              <div class="card-body d-flex flex-column flex-grow-1">
                <div class="blog-cat-badge"><i class="fas <?= $icon ?> me-1"></i> <?= htmlspecialchars($post['category']) ?></div>
                <h3><?= htmlspecialchars($post['title']) ?></h3>
                <p class="flex-grow-1"><?= htmlspecialchars($post['excerpt'] ?? '') ?></p>
                <div class="blog-meta mb-3">
                  <span><i class="fas fa-user"></i> <?= htmlspecialchars($post['author']) ?></span>
                  <span><i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($post['published_at'])) ?></span>
                  <span><i class="fas fa-eye"></i> <?= number_format($post['views']) ?> views</span>
                </div>
                <a href="blog/<?= htmlspecialchars($post['slug']) ?>.php" class="read-more">Read Article <i class="fas fa-arrow-right"></i></a>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if(empty($posts)): ?>
          <div class="col-12 text-center py-5">
            <div style="font-size:48px;margin-bottom:16px;">📝</div>
            <h5 style="color:#1C2280;font-weight:700;">No Posts Found</h5>
            <p style="color:#64748b;">Check back soon. We're publishing new content regularly.</p>
            <a href="blog.php" class="btn" style="background:#1C2280;color:#fff;border-radius:8px;padding:10px 24px;font-weight:600;margin-top:8px;">View All Posts</a>
          </div>
          <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pg['total_pages'] > 1): ?>
        <nav class="mt-5" aria-label="Blog pagination">
          <ul class="pagination justify-content-center gap-1">
            <?php if($pg['has_prev']): ?><li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$pg['prev_page']])) ?>" style="border-radius:8px;"><i class="fas fa-chevron-left"></i></a></li><?php endif; ?>
            <?php for($n=1;$n<=$pg['total_pages'];$n++): ?>
            <li class="page-item <?= $n==$pg['current_page']?'active':'' ?>"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$n])) ?>" style="border-radius:8px;<?= $n==$pg['current_page']?'background:#1C2280;border-color:#1C2280;':'' ?>"><?= $n ?></a></li>
            <?php endfor; ?>
            <?php if($pg['has_next']): ?><li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$pg['next_page']])) ?>" style="border-radius:8px;"><i class="fas fa-chevron-right"></i></a></li><?php endif; ?>
          </ul>
        </nav>
        <?php endif; ?>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <!-- Search -->
        <div class="sidebar-card scroll-reveal">
          <h5><i class="fas fa-search me-2" style="color:#CC2228;"></i> Search Articles</h5>
          <form action="blog.php" method="GET">
            <div class="input-group">
              <input type="text" class="form-control" name="q" placeholder="Search blog..." value="<?= htmlspecialchars($search) ?>">
              <button type="submit" class="btn" style="background:#1C2280;color:#fff;border-radius:0 8px 8px 0;"><i class="fas fa-search"></i></button>
            </div>
          </form>
        </div>

        <!-- Categories -->
        <div class="sidebar-card scroll-reveal" style="transition-delay:.1s">
          <h5><i class="fas fa-folder me-2" style="color:#CC2228;"></i> Categories</h5>
          <?php foreach($categories as $c): ?>
          <a href="blog.php?cat=<?= urlencode($c['category']) ?>" class="cat-item">
            <span><i class="fas <?= $cat_icons[$c['category']] ?? 'fa-pen' ?> me-2" style="color:#CC2228;"></i><?= htmlspecialchars($c['category']) ?></span>
            <span class="cat-badge"><?= $c['cnt'] ?></span>
          </a>
          <?php endforeach; ?>
        </div>

        <!-- Newsletter -->
        <div class="sidebar-card scroll-reveal" style="transition-delay:.2s;background:linear-gradient(135deg,#1C2280,#CC2228);border:none;">
          <h5 style="color:#fff;border-color:rgba(255,255,255,.2);">📧 Newsletter</h5>
          <p style="color:rgba(255,255,255,.8);font-size:13px;margin-bottom:16px;">Get our latest articles and industry insights delivered to your inbox.</p>
          <div id="nl-msg" class="d-none mb-2" style="font-size:12px;padding:8px 12px;border-radius:8px;"></div>
          <form onsubmit="submitNewsletter(event)">
            <input type="email" id="nl-email" class="form-control mb-2" placeholder="Your email address" required style="background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.2);color:#fff;">
            <button type="submit" class="btn" style="background:#fff;color:#1C2280;font-weight:700;width:100%;border-radius:8px;">Subscribe Free</button>
          </form>
        </div>

        <!-- Contact CTA -->
        <div class="sidebar-card scroll-reveal" style="transition-delay:.3s;text-align:center;">
          <div style="font-size:36px;margin-bottom:12px;">💡</div>
          <h5 style="margin-bottom:8px;">Have a Project?</h5>
          <p style="color:#64748b;font-size:13px;margin-bottom:16px;">Our team is ready to help you outsource smarter and grow faster.</p>
          <a href="contact.php" class="btn" style="background:#1C2280;color:#fff;border-radius:10px;font-weight:700;width:100%;padding:11px;">Get Free Consultation</a>
          <a href="tel:+918308906690" class="btn" style="background:var(--bg-light,#f0f2ff);color:#1C2280;border-radius:10px;font-weight:700;width:100%;padding:11px;margin-top:8px;"><i class="fas fa-phone-alt me-1"></i> <?= PHONE_INDIA ?></a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
