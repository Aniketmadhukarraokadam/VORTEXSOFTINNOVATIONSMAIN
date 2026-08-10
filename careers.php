<?php
/**
 * Vortexsoft Innovations — Careers Page (careers.php)
 * Shows live job listings from MySQL + category filter + application form
 * V3: Category tabs, 20 job openings, general application form
 */

$page_title   = 'Careers at Vortexsoft Group — Join Our Team | IT & BPO Jobs in Bengaluru, India';
$page_desc    = 'Explore exciting career opportunities at Vortexsoft Group. Join our team of 200+ professionals in Bengaluru & Pune. Apply online for IT, BPO, Healthcare, Publishing, Data Annotation, and more roles.';
$canonical_url = 'https://www.vortexsoftinnovations.com/careers.php';
$prefix       = './';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';


// Fetch jobs from DB; fall back to static list if DB unavailable or empty
$jobs = [];
try {
    $db = getDB();
    if ($db) {
        $rows = $db->query("SELECT * FROM jobs WHERE is_active=1 ORDER BY sort_order ASC, created_at ASC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            $jobs[] = [
                'id'         => $r['id'],
                'title'      => $r['title'],
                'department' => $r['department'],
                'category'   => $r['department'], // used for filtering
                'type'       => $r['type'],
                'location'   => $r['location'],
                'exp'        => $r['experience_range'] ?? '',
                'skills'     => array_map('trim', explode(',', $r['skills_json'] ?? '')),
                'desc'       => $r['description'],
                'urgent'     => (bool)$r['is_urgent'],
            ];
        }
    }
} catch (Throwable $e) { $jobs = []; }

// Static fallback — 20 diverse openings (used if DB has no jobs yet)
if (empty($jobs)) {
    $jobs = [
      // ── Healthcare BPO ────────────────────────────────────────
      ['id'=>1,'title'=>'Medical Coder (CPC)','department'=>'Healthcare BPO','category'=>'Healthcare BPO','type'=>'Full Time','location'=>'Bengaluru','exp'=>'1–3 years','skills'=>['CPC Certified','ICD-10','CPT','E&M Coding','HCC Risk Adjustment'],'desc'=>'Review and code inpatient/outpatient medical records using ICD-10-CM, CPT, and HCPCS Level II codes. Ensure accuracy, compliance with payer requirements, and achieve >95% quality scores.','urgent'=>true],
      ['id'=>2,'title'=>'Medical Billing Executive','department'=>'Healthcare BPO','category'=>'Healthcare BPO','type'=>'Full Time','location'=>'Bengaluru','exp'=>'1–2 years','skills'=>['Medical Billing','Claims Submission','EDI 837','AR Follow-up','EHR Systems'],'desc'=>'Handle end-to-end medical billing cycle — from charge entry and claims submission to payment posting and AR follow-up for US healthcare providers.','urgent'=>false],
      ['id'=>3,'title'=>'Denial Management Specialist','department'=>'Healthcare BPO','category'=>'Healthcare BPO','type'=>'Full Time','location'=>'Bengaluru / Remote','exp'=>'2–4 years','skills'=>['Claim Denials','Appeal Letters','Payer Policies','Revenue Cycle','CMS Guidelines'],'desc'=>'Analyze denied claims, research root causes, and prepare appeal letters to overturn denials. Work with US insurance payers and Medicare/Medicaid guidelines.','urgent'=>false],

      // ── IT & Software ─────────────────────────────────────────
      ['id'=>4,'title'=>'PHP Developer','department'=>'IT & Software','category'=>'IT & Software','type'=>'Full Time','location'=>'Bengaluru / Remote','exp'=>'2–4 years','skills'=>['PHP','Laravel','MySQL','REST APIs','JavaScript'],'desc'=>'Develop and maintain scalable PHP applications. Work with Laravel framework, MySQL databases, and REST APIs. Build admin panels and client portals.','urgent'=>false],
      ['id'=>5,'title'=>'React.js Frontend Developer','department'=>'IT & Software','category'=>'IT & Software','type'=>'Full Time','location'=>'Bengaluru / Remote','exp'=>'1–3 years','skills'=>['React.js','TypeScript','REST APIs','CSS3','Git'],'desc'=>'Build modern, responsive web applications using React.js and TypeScript. Collaborate with backend teams on API integration and UI component library design.','urgent'=>true],
      ['id'=>6,'title'=>'Python Developer / AI Engineer','department'=>'IT & Software','category'=>'IT & Software','type'=>'Full Time','location'=>'Bengaluru / Remote','exp'=>'2–5 years','skills'=>['Python','FastAPI','TensorFlow / PyTorch','LLM Integration','AWS / GCP'],'desc'=>'Develop AI-powered applications and APIs. Work on LLM integrations, ML model deployment, and data pipeline engineering for global clients.','urgent'=>false],

      // ── AI / Data Annotation ─────────────────────────────────
      ['id'=>7,'title'=>'Data Annotation Specialist','department'=>'AI / Data','category'=>'AI / Data','type'=>'Full Time','location'=>'Bengaluru','exp'=>'0–2 years','skills'=>['Image Annotation','CVAT','Labelbox','Quality Control','RLHF'],'desc'=>'Perform high-quality image, video, audio, and text annotation for AI/ML training datasets. Work with tools like CVAT, Labelbox, and Scale AI for global tech clients.','urgent'=>false],
      ['id'=>8,'title'=>'Data Annotation Team Lead','department'=>'AI / Data','category'=>'AI / Data','type'=>'Full Time','location'=>'Bengaluru','exp'=>'3–6 years','skills'=>['Team Management','Quality Auditing','AI/ML Concepts','Client Reporting','Process Design'],'desc'=>'Lead a team of 10–15 data annotators. Drive quality audits, SLA adherence, client onboarding, and continuous process improvements for AI training projects.','urgent'=>false],

      // ── Publishing ────────────────────────────────────────────
      ['id'=>9,'title'=>'Publishing Editor / Typesetter','department'=>'Publishing','category'=>'Publishing','type'=>'Full Time','location'=>'Bengaluru','exp'=>'2–5 years','skills'=>['InDesign','QuarkXPress','XML','ePUB3','Proofreading'],'desc'=>'Handle typesetting, layout, ePUB3 conversion, and proofreading of academic and trade books. Work with publishers from USA, UK, and Europe.','urgent'=>false],
      ['id'=>10,'title'=>'ePUB / Accessibility Specialist','department'=>'Publishing','category'=>'Publishing','type'=>'Full Time','location'=>'Bengaluru / Remote','exp'=>'1–3 years','skills'=>['ePUB3','WCAG 2.1','ARIA','PDF Accessibility','Alt-Text Writing'],'desc'=>'Convert books and documents to WCAG 2.1 accessible ePUB3 and PDF formats. Write alt-text for images, add ARIA markup, and ensure compliance with accessibility standards for US/UK publishers.','urgent'=>false],
      ['id'=>11,'title'=>'Technical Publications Author','department'=>'Publishing','category'=>'Publishing','type'=>'Full Time','location'=>'Bengaluru','exp'=>'3–6 years','skills'=>['S1000D','DITA XML','Aerospace Manuals','Illustrated Parts Catalogs','Tech Writing'],'desc'=>'Create and maintain aircraft maintenance manuals, IPC documents, and technical publications in S1000D/DITA XML format for aerospace and defense clients worldwide.','urgent'=>false],

      // ── Real Estate BPO ───────────────────────────────────────
      ['id'=>12,'title'=>'Lease Administrator','department'=>'Real Estate BPO','category'=>'Real Estate BPO','type'=>'Full Time','location'=>'Pune','exp'=>'1–4 years','skills'=>['Lease Abstraction','CAM Reconciliation','MRI Software','Excel','Real Estate Law'],'desc'=>'Abstract and administer commercial real estate leases. Handle CAM reconciliation, rent roll management, and property accounting for US clients.','urgent'=>false],
      ['id'=>13,'title'=>'Title Search & Settlement Analyst','department'=>'Real Estate BPO','category'=>'Real Estate BPO','type'=>'Full Time','location'=>'Pune','exp'=>'1–3 years','skills'=>['Title Search','Settlement Statements','HUD-1','Property Records','US Real Estate'],'desc'=>'Conduct title searches on US residential and commercial properties. Prepare HUD-1 settlement statements, review deeds, liens, and encumbrances for title insurance companies.','urgent'=>true],

      // ── Accounting & Finance ──────────────────────────────────
      ['id'=>14,'title'=>'Accounts Executive','department'=>'Finance & Accounting','category'=>'Finance & Accounting','type'=>'Full Time','location'=>'Bengaluru','exp'=>'1–3 years','skills'=>['Tally','QuickBooks','GST','TDS','Bank Reconciliation'],'desc'=>'Handle bookkeeping, accounts payable/receivable, GST filing, TDS, bank reconciliation, and monthly financial reporting for Indian and US clients.','urgent'=>false],
      ['id'=>15,'title'=>'Payroll Processing Executive','department'=>'Finance & Accounting','category'=>'Finance & Accounting','type'=>'Full Time','location'=>'Bengaluru','exp'=>'1–3 years','skills'=>['Payroll Software','Statutory Compliance','PF/ESI','Salary Structuring','Excel'],'desc'=>'Process monthly payroll for 500+ employees across multiple clients. Handle statutory compliance — PF, ESI, PT, TDS — payslip generation, and full-and-final settlements.','urgent'=>false],

      // ── Digital Marketing ─────────────────────────────────────
      ['id'=>16,'title'=>'Digital Marketing Executive','department'=>'Digital Marketing','category'=>'Digital Marketing','type'=>'Full Time','location'=>'Bengaluru','exp'=>'1–3 years','skills'=>['SEO','Google Ads','Meta Ads','Content Marketing','Analytics'],'desc'=>'Plan and execute SEO, PPC, and social media campaigns for B2B and B2C clients. Manage monthly performance reports and analytics dashboards.','urgent'=>false],
      ['id'=>17,'title'=>'SEO Specialist','department'=>'Digital Marketing','category'=>'Digital Marketing','type'=>'Full Time','location'=>'Bengaluru / Remote','exp'=>'2–4 years','skills'=>['Technical SEO','Keyword Research','Link Building','Core Web Vitals','Content Strategy'],'desc'=>'Lead on-page and off-page SEO strategy for multiple client websites. Conduct technical audits, keyword research, and content gap analysis to drive organic traffic growth.','urgent'=>false],

      // ── Human Resources ───────────────────────────────────────
      ['id'=>18,'title'=>'HR Executive (IT/BPO Recruiter)','department'=>'Human Resources','category'=>'Human Resources','type'=>'Full Time','location'=>'Bengaluru','exp'=>'1–3 years','skills'=>['Recruitment','Naukri/LinkedIn Sourcing','Onboarding','HRMS','Labor Law'],'desc'=>'Manage end-to-end recruitment for IT and BPO roles. Handle onboarding, employee engagement, attendance, payroll coordination, and compliance.','urgent'=>false],

      // ── Logistics ─────────────────────────────────────────────
      ['id'=>19,'title'=>'Logistics Data Entry Specialist','department'=>'Logistics','category'=>'Logistics','type'=>'Full Time','location'=>'Bengaluru / Pune','exp'=>'0–2 years','skills'=>['Bill of Lading','Freight Data Entry','MS Excel','ERP Systems','Attention to Detail'],'desc'=>'Process freight documents, bills of lading, and shipping records for US logistics and supply chain companies. Ensure accurate data entry with high speed and quality.','urgent'=>false],

      // ── General ───────────────────────────────────────────────
      ['id'=>20,'title'=>'Business Development Executive (BPO Sales)','department'=>'Sales & Business Dev','category'=>'Sales & Business Dev','type'=>'Full Time','location'=>'Bengaluru / Remote','exp'=>'2–5 years','skills'=>['B2B Sales','Cold Calling','LinkedIn Outreach','CRM (Salesforce/HubSpot)','Proposal Writing'],'desc'=>'Drive new business development for Vortexsoft\'s BPO and IT services. Prospect US/UK/AUS clients via LinkedIn, cold calling, and email campaigns. Manage the complete sales funnel from lead to deal closure.','urgent'=>false],
    ];
}

// Build unique categories for filter tabs
$categories = ['All'];
foreach ($jobs as $j) {
    if (!in_array($j['category'], $categories, true)) {
        $categories[] = $j['category'];
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<style>
.page-hero{background:linear-gradient(135deg,#080B1A 0%,#1C2280 55%,#CC2228 100%);padding:80px 0 70px;position:relative;overflow:hidden}
.page-hero::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:50px 50px}
.page-hero h1{font-size:clamp(2rem,4vw,3rem);font-weight:800;color:#fff}
.breadcrumb-item,.breadcrumb-item a{color:rgba(255,255,255,.6);font-size:14px}
.breadcrumb-item.active{color:rgba(255,255,255,.9)}
.breadcrumb-item+.breadcrumb-item::before{color:rgba(255,255,255,.4)}

/* Category Filter Tabs */
.filter-bar{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:36px;padding:20px 0 0}
.filter-btn{background:#fff;border:1.5px solid #dde2f5;color:#475569;font-family:'Poppins',sans-serif;font-size:13px;font-weight:600;padding:8px 20px;border-radius:100px;cursor:pointer;transition:.3s;white-space:nowrap;outline:none}
.filter-btn:hover{border-color:#1C2280;color:#1C2280;background:#f0f2ff}
.filter-btn.active{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;border-color:transparent;box-shadow:0 4px 14px rgba(28,34,128,.3)}
.job-count-badge{background:rgba(255,255,255,.15);color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:100px;margin-left:4px}
.filter-btn.active .job-count-badge{background:rgba(255,255,255,.2)}

/* Job Cards */
.job-card{background:#fff;border-radius:18px;padding:28px;border:1.5px solid #e8ecff;transition:all .3s;position:relative;overflow:hidden}
.job-card::before{content:'';position:absolute;top:0;left:0;width:5px;height:100%;background:linear-gradient(180deg,#1C2280,#CC2228);transform:scaleY(0);transform-origin:top;transition:.3s}
.job-card:hover{border-color:transparent;box-shadow:0 12px 40px rgba(28,34,128,.14);transform:translateY(-4px)}
.job-card:hover::before{transform:scaleY(1)}
.job-card.hidden{display:none!important}
.job-badge{font-size:11px;font-weight:700;padding:4px 10px;border-radius:100px;letter-spacing:.5px}
.urgent-badge{background:#fff0f0;color:#CC2228;border:1px solid rgba(204,34,40,.2)}
.type-badge{background:rgba(28,34,128,.07);color:#1C2280}
.skill-tag{background:rgba(28,34,128,.06);color:#1C2280;font-size:12px;font-weight:600;padding:4px 10px;border-radius:6px;border:1px solid rgba(28,34,128,.1)}
.apply-btn{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;font-family:'Poppins',sans-serif;font-size:13px;font-weight:700;padding:10px 22px;border-radius:8px;border:none;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:8px}
.apply-btn:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(28,34,128,.3);color:#fff}
.no-jobs-msg{display:none;text-align:center;padding:40px 20px;color:#64748b;font-size:15px}

/* Application Form Modal */
#applyModal .modal-content{border-radius:20px;border:none;overflow:hidden}
#applyModal .modal-header{background:linear-gradient(135deg,#1C2280,#CC2228);padding:24px 28px;border:none}
#applyModal .modal-header .modal-title{color:#fff;font-size:18px;font-weight:700}
#applyModal .btn-close{filter:invert(1)}
.btn-submit-apply{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;font-family:'Poppins',sans-serif;font-size:15px;font-weight:600;padding:14px;border:none;border-radius:10px;width:100%;transition:.3s;display:flex;align-items:center;justify-content:center;gap:8px}
.btn-submit-apply:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(28,34,128,.3);color:#fff}
.resume-optional-note{background:#f0f7ff;border-left:3px solid #1C2280;border-radius:4px;padding:10px 14px;font-size:12.5px;color:#475569;margin-top:6px}
</style>

<!-- Hero -->
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Careers</li></ol></nav>
    <h1>Join the <span style="color:#5BA8D4;">Vortexsoft</span> Team</h1>
    <p style="color:rgba(255,255,255,.75);font-size:16px;margin-top:12px;max-width:560px;">Be part of a fast-growing global IT &amp; BPO company. We're hiring passionate professionals across Bengaluru, Pune, and Remote positions.</p>
    <div class="d-flex gap-3 mt-4 flex-wrap">
      <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:12px;padding:12px 20px;color:#fff;font-size:13px;font-weight:600;"><i class="fas fa-users me-2" style="color:#5BA8D4;"></i> 200+ Team Members</div>
      <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:12px;padding:12px 20px;color:#fff;font-size:13px;font-weight:600;"><i class="fas fa-map-marker-alt me-2" style="color:#CC2228;"></i> Bengaluru, Pune &amp; Remote</div>
      <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:12px;padding:12px 20px;color:#fff;font-size:13px;font-weight:600;"><i class="fas fa-rocket me-2" style="color:#f59e0b;"></i> <?= count($jobs) ?>+ Open Positions</div>
    </div>
  </div>
</div>

<!-- Culture Section -->
<section class="py-5" style="background:#fff;">
  <div class="container">
    <div class="row gy-4 align-items-center">
      <div class="col-lg-6 scroll-reveal-left">
        <div class="section-tag">Life at Vortexsoft</div>
        <h2 class="section-title">Why Work <span class="highlight">With Us?</span></h2>
        <div class="section-divider"></div>
        <p style="color:var(--text-muted);font-size:15px;line-height:1.8;">At Vortexsoft, we believe in fostering a culture of innovation, collaboration, and growth. We invest in our people with continuous training, international exposure, and career advancement opportunities.</p>
        <div class="row g-3 mt-2">
          <?php $perks = [
            ['icon'=>'fa-graduation-cap','title'=>'Continuous Learning','desc'=>'Certifications, workshops, and training programs'],
            ['icon'=>'fa-globe','title'=>'Global Exposure','desc'=>'Work with 150+ international clients'],
            ['icon'=>'fa-heart','title'=>'Health Benefits','desc'=>'Comprehensive health and wellness programs'],
            ['icon'=>'fa-trophy','title'=>'Performance Rewards','desc'=>'Quarterly bonuses and recognition awards'],
            ['icon'=>'fa-laptop-house','title'=>'Flexible Work','desc'=>'Remote and hybrid work options available'],
            ['icon'=>'fa-chart-line','title'=>'Career Growth','desc'=>'Fast-track promotions and leadership paths'],
          ];
          foreach($perks as $p): ?>
          <div class="col-6">
            <div style="display:flex;gap:12px;align-items:flex-start;padding:16px;background:var(--bg-light,#f0f2ff);border-radius:12px;border:1px solid #e8ecff;">
              <div style="width:38px;height:38px;border-radius:10px;background:#1C2280;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas <?= $p['icon'] ?>" style="color:#fff;font-size:14px;"></i></div>
              <div><div style="font-weight:700;font-size:13px;color:#1C2280;"><?= $p['title'] ?></div><div style="font-size:12px;color:#64748b;"><?= $p['desc'] ?></div></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-6 scroll-reveal-right">
        <div style="background:linear-gradient(135deg,#1C2280,#CC2228);border-radius:24px;padding:40px;color:#fff;">
          <h3 style="font-weight:800;margin-bottom:8px;font-size:26px;">Ready to Apply?</h3>
          <p style="color:rgba(255,255,255,.8);margin-bottom:24px;">Browse our openings below, or send your resume directly to our HR team. We respond to all applications within 3–5 business days.</p>
          <div style="background:rgba(255,255,255,.1);border-radius:14px;padding:20px;margin-bottom:16px;">
            <div style="font-size:12px;color:rgba(255,255,255,.6);font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;">HR Email</div>
            <a href="mailto:<?= EMAIL_HR ?>" style="color:#fff;font-weight:700;font-size:16px;"><?= EMAIL_HR ?></a>
          </div>
          <div style="background:rgba(255,255,255,.1);border-radius:14px;padding:20px;margin-bottom:24px;">
            <div style="font-size:12px;color:rgba(255,255,255,.6);font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;">WhatsApp HR</div>
            <a href="<?= SOCIAL_WHATSAPP ?>" target="_blank" style="color:#fff;font-weight:700;font-size:16px;"><?= PHONE_INDIA ?></a>
          </div>
          <button class="btn" style="background:#fff;color:#1C2280;font-weight:700;border-radius:10px;padding:14px 28px;width:100%;font-size:15px;" onclick="openApplyModal('General Application','General')">
            <i class="fas fa-paper-plane me-2"></i> Submit General Application
          </button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Job Listings -->
<section class="py-5" style="background:var(--bg-light,#f0f2ff);">
  <div class="container">
    <div class="text-center mb-4 scroll-reveal">
      <div class="section-tag">Open Positions</div>
      <h2 class="section-title">Current <span class="highlight">Job Openings</span></h2>
      <div class="section-divider"></div>
      <p class="section-subtitle">We have <strong><?= count($jobs) ?>+</strong> open positions across Bengaluru, Pune, and Remote. Apply online or email your resume to <a href="mailto:<?= EMAIL_HR ?>" style="color:var(--primary);"><?= EMAIL_HR ?></a>.</p>
    </div>

    <!-- Category Filter Tabs -->
    <div class="filter-bar scroll-reveal" id="filterBar">
      <?php foreach($categories as $i => $cat): ?>
      <?php
        $cnt = ($cat === 'All') ? count($jobs) : count(array_filter($jobs, fn($j) => $j['category'] === $cat));
      ?>
      <button
        class="filter-btn <?= $i === 0 ? 'active' : '' ?>"
        data-filter="<?= htmlspecialchars($cat) ?>"
        onclick="filterJobs(this)"
        id="filter-<?= $i ?>"
      >
        <?= htmlspecialchars($cat) ?>
        <span class="job-count-badge"><?= $cnt ?></span>
      </button>
      <?php endforeach; ?>
    </div>

    <div class="row g-4" id="jobsGrid">
      <?php foreach($jobs as $i=>$job): ?>
      <div class="col-lg-6 scroll-reveal job-item" style="transition-delay:<?= ($i%2)*0.08 ?>s" data-category="<?= htmlspecialchars($job['category']) ?>">
        <div class="job-card">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
              <h5 style="font-family:'Poppins',sans-serif;font-weight:700;font-size:17px;color:#1C2280;margin-bottom:4px;"><?= htmlspecialchars($job['title']) ?></h5>
              <div style="font-size:13px;color:#64748b;font-weight:500;"><i class="fas fa-sitemap me-1"></i> <?= htmlspecialchars($job['department']) ?></div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
              <?php if($job['urgent']): ?><span class="job-badge urgent-badge"><i class="fas fa-bolt me-1"></i> Urgent</span><?php endif; ?>
              <span class="job-badge type-badge"><?= htmlspecialchars($job['type']) ?></span>
            </div>
          </div>
          <div class="d-flex gap-4 mb-3" style="font-size:13px;color:#64748b;">
            <span><i class="fas fa-map-marker-alt me-1" style="color:#CC2228;"></i> <?= htmlspecialchars($job['location']) ?></span>
            <span><i class="fas fa-briefcase me-1" style="color:#CC2228;"></i> <?= htmlspecialchars($job['exp']) ?></span>
          </div>
          <p style="font-size:14px;color:#475569;line-height:1.7;margin-bottom:14px;"><?= htmlspecialchars($job['desc']) ?></p>
          <div class="d-flex flex-wrap gap-2 mb-4">
            <?php foreach($job['skills'] as $sk): ?><span class="skill-tag"><?= htmlspecialchars($sk) ?></span><?php endforeach; ?>
          </div>
          <button class="apply-btn magnetic" onclick="openApplyModal('<?= htmlspecialchars($job['title'], ENT_QUOTES) ?>','<?= htmlspecialchars($job['department'], ENT_QUOTES) ?>')">
            <i class="fas fa-paper-plane"></i> Apply Now
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="no-jobs-msg" id="noJobsMsg">
      <i class="fas fa-search" style="font-size:40px;color:#dde2f5;margin-bottom:12px;display:block;"></i>
      No openings in this category right now. Check back soon or <a href="mailto:<?= EMAIL_HR ?>">email your resume</a>.
    </div>

    <div class="text-center mt-5 scroll-reveal">
      <div style="background:#fff;border-radius:16px;padding:32px;border:1px solid #e8ecff;display:inline-block;max-width:560px;">
        <div style="font-size:28px;margin-bottom:12px;">💼</div>
        <h5 style="font-family:'Poppins',sans-serif;font-weight:700;color:#1C2280;margin-bottom:8px;">Don't See Your Role?</h5>
        <p style="color:#64748b;font-size:14px;margin-bottom:20px;">We're always looking for talented people. Submit a general application and we'll keep you in mind for future openings.</p>
        <button onclick="openApplyModal('General Application','General')" class="btn" style="background:linear-gradient(135deg,#1C2280,#CC2228);color:#fff;border-radius:10px;padding:12px 28px;font-weight:700;font-size:14px;"><i class="fas fa-paper-plane me-2"></i> Submit General Application</button>
      </div>
    </div>
  </div>
</section>

<!-- Apply Modal -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="applyModalLabel"><i class="fas fa-briefcase me-2"></i> Apply for <span id="modal-job-title">Position</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div id="apply-feedback" class="d-none mb-3" role="alert"></div>
        <form id="applyForm" enctype="multipart/form-data" novalidate>
          <input type="hidden" name="job_title" id="form-job-title">
          <input type="hidden" name="department" id="form-department">
          <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="applicant_name" placeholder="Your full name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control" name="email" placeholder="your@email.com" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
              <input type="tel" class="form-control" name="phone" placeholder="+91 XXXXX XXXXX" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Current Location</label>
              <input type="text" class="form-control" name="current_location" placeholder="City, State">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Years of Experience</label>
              <input type="number" class="form-control" name="experience_years" placeholder="e.g. 2.5" step="0.5" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Notice Period</label>
              <select class="form-select" name="notice_period">
                <option value="">Select notice period</option>
                <option>Immediate / Available Now</option>
                <option>15 Days</option>
                <option>30 Days</option>
                <option>60 Days</option>
                <option>90 Days</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Current Company</label>
              <input type="text" class="form-control" name="current_company" placeholder="Company name (or Fresher)">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Expected CTC / Salary</label>
              <input type="text" class="form-control" name="expected_ctc" placeholder="e.g. 4.5 LPA or Open to discuss">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Upload Resume</label>
              <input type="file" class="form-control" name="resume" id="resumeFile" accept=".pdf,.doc,.docx">
              <div class="resume-optional-note">
                <i class="fas fa-info-circle me-1" style="color:#1C2280;"></i>
                <strong>Resume is optional.</strong> You can apply without uploading a resume. If you have one, please upload PDF, DOC, or DOCX format (max 5MB).
              </div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">LinkedIn Profile URL <span style="color:#64748b;font-weight:400;font-size:12px;">(optional)</span></label>
              <input type="url" class="form-control" name="linkedin_url" placeholder="https://linkedin.com/in/yourprofile">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Cover Letter / Why This Role? <span style="color:#64748b;font-weight:400;font-size:12px;">(optional)</span></label>
              <textarea class="form-control" name="cover_letter" rows="4" placeholder="Tell us why you're a great fit for this role..."></textarea>
            </div>
            <div class="col-12 mb-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="consent" id="consentCheck" required checked>
                <label class="form-check-label" for="consentCheck" style="font-size:12.5px;color:#64748b;">
                  I consent to Vortexsoft Group storing &amp; processing my personal details and resume for recruitment purposes.
                </label>
              </div>
            </div>
            <div class="col-12">
              <button type="submit" class="btn-submit-apply magnetic" id="applySubmitBtn">
                <i class="fas fa-paper-plane"></i> Submit Application
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$extra_scripts = '
<script>
// ── Category Filter ─────────────────────────────────────────────
function filterJobs(btn) {
  var filter = btn.getAttribute("data-filter");
  document.querySelectorAll(".filter-btn").forEach(function(b){ b.classList.remove("active"); });
  btn.classList.add("active");

  var items = document.querySelectorAll(".job-item");
  var visible = 0;
  items.forEach(function(item) {
    if (filter === "All" || item.getAttribute("data-category") === filter) {
      item.classList.remove("hidden");
      visible++;
    } else {
      item.classList.add("hidden");
    }
  });
  var noMsg = document.getElementById("noJobsMsg");
  noMsg.style.display = (visible === 0) ? "block" : "none";
}

// ── Apply Modal ─────────────────────────────────────────────────
function openApplyModal(title, dept) {
  var label = document.getElementById("applyModalLabel");
  if (label) {
    label.innerHTML = \'<i class="fas fa-briefcase me-2"></i> Apply for <span id="modal-job-title">\' + title + \'</span>\';
  }
  document.getElementById("form-job-title").value = title;
  document.getElementById("form-department").value = dept;
  var fb = document.getElementById("apply-feedback");
  if (fb) fb.className = "d-none";
  var form = document.getElementById("applyForm");
  if (form) form.reset();
  new bootstrap.Modal(document.getElementById("applyModal")).show();
}

// ── Application Form Submit ─────────────────────────────────────
(function(){
  var form = document.getElementById("applyForm");
  if (!form) return;
  form.addEventListener("submit", function(e){
    e.preventDefault();
    var btn = document.getElementById("applySubmitBtn");
    var fb  = document.getElementById("apply-feedback");
    btn.innerHTML = \'<i class="fas fa-spinner fa-spin"></i> Submitting...\';
    btn.disabled = true;
    fb.className = "d-none";
    fetch("api/apply.php", {method:"POST", body: new FormData(form)})
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          form.reset();
          bootstrap.Modal.getInstance(document.getElementById("applyModal")).hide();
          var m = document.getElementById("applySuccessModal");
          if (m) new bootstrap.Modal(m).show();
          else {
            fb.className = "alert alert-success";
            fb.innerHTML = \'<i class="fas fa-check-circle me-2"></i>\' + res.message;
            setTimeout(function(){ bootstrap.Modal.getInstance(document.getElementById("applyModal")).hide(); }, 2500);
          }
        } else {
          fb.className = "alert alert-danger";
          fb.innerHTML = \'<i class="fas fa-exclamation-circle me-2"></i>\' + res.message;
        }
      })
      .catch(() => { fb.className = "alert alert-danger"; fb.textContent = "Network error. Please try again."; })
      .finally(() => { btn.innerHTML = \'<i class="fas fa-paper-plane"></i> Submit Application\'; btn.disabled = false; });
  });
})();
</script>';
require_once __DIR__ . '/includes/footer.php';
?>
