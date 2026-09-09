<?php
/**
 * Vortexsoft Innovations — Careers Page (careers.php)
 * Shows live job listings from MySQL + category filter + application form
 * V3: Category tabs, 20 job openings, general application form
 */

$page_title   = 'Careers & Job Openings | Vortexsoft Group';
$page_desc    = 'Explore career opportunities at Vortexsoft Group. Join 200+ professionals in Bengaluru & Pune. Apply online for IT, BPO, and Healthcare roles.';
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
.job-card.hidden, .job-item.hidden { display: none !important; }
.hidden { display: none !important; }
.no-jobs-msg{display:none;text-align:center;padding:40px 20px;color:#64748b;font-size:15px}

/* Application Form Modal */
#applyModal .modal-content{border-radius:20px;border:none;overflow:hidden}
#applyModal .modal-header{background:linear-gradient(135deg,#1C2280,#CC2228);padding:24px 28px;border:none}
#applyModal .modal-header .modal-title{color:#fff;font-size:18px;font-weight:700}
#applyModal .btn-close{filter:invert(1)}
.btn-submit-apply{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;font-family:'Poppins',sans-serif;font-size:15px;font-weight:600;padding:14px;border:none;border-radius:100px;width:100%;transition:.3s;display:flex;align-items:center;justify-content:center;gap:8px}
.btn-submit-apply:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(28,34,128,.3);color:#fff}
.resume-optional-note{background:#f0f7ff;border-left:3px solid #1C2280;border-radius:4px;padding:10px 14px;font-size:12.5px;color:#475569;margin-top:6px}

/* ── Share Job Opening Styling ───────────────────────────── */
.btn-job-share{
  background:#f8f9ff;
  color:#1C2280;
  font-family:'Poppins',sans-serif;
  font-size:13px;
  font-weight:600;
  padding:9px 18px;
  border-radius:8px;
  border:1.5px solid #dde2f5;
  cursor:pointer;
  transition:all .25s ease;
  display:inline-flex;
  align-items:center;
  gap:7px;
  text-decoration:none;
}
.btn-job-share:hover{
  background:#1C2280;
  color:#fff;
  border-color:#1C2280;
  box-shadow:0 6px 18px rgba(28,34,128,.18);
  transform:translateY(-1px);
}
.btn-job-share i{
  font-size:13px;
}

#shareJobModal .modal-content{
  border-radius:20px;
  border:none;
  overflow:hidden;
  box-shadow:0 25px 50px -12px rgba(15,23,42,.3);
}
#shareJobModal .modal-header{
  background:linear-gradient(135deg,#080B1A 0%,#1C2280 60%,#CC2228 100%);
  padding:22px 28px;
  border:none;
}
#shareJobModal .modal-header .modal-title{
  color:#fff;
  font-size:18px;
  font-weight:700;
  display:flex;
  align-items:center;
}
#shareJobModal .btn-close{filter:invert(1)}

.share-job-preview{
  background:#f8faff;
  border:1.5px solid #e2e8f5;
  border-left:4px solid #1C2280;
  border-radius:12px;
  padding:16px 18px;
  margin-bottom:22px;
}
.share-preview-title{
  font-family:'Poppins',sans-serif;
  font-weight:700;
  font-size:16px;
  color:#1C2280;
  margin-bottom:6px;
}
.share-preview-meta{
  display:flex;
  flex-wrap:wrap;
  gap:12px;
  font-size:12.5px;
  color:#64748b;
}

.share-grid{
  display:grid;
  grid-template-columns:repeat(3, 1fr);
  gap:12px;
  margin-bottom:22px;
}
@media (max-width: 575px) {
  .share-grid{grid-template-columns:repeat(2, 1fr);}
}
.share-channel-btn{
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  gap:8px;
  padding:14px 10px;
  border-radius:12px;
  text-decoration:none !important;
  font-size:12px;
  font-weight:600;
  color:#334155;
  border:1.5px solid #e8ecff;
  background:#fff;
  transition:all .25s cubic-bezier(.2,0,0,1);
  cursor:pointer;
  outline:none;
}
.share-channel-btn i{
  font-size:22px;
  transition:transform .25s ease;
}
.share-channel-btn:hover{
  transform:translateY(-3px);
  box-shadow:0 8px 20px rgba(28,34,128,.12);
}
.share-channel-btn:hover i{
  transform:scale(1.15);
}
.share-channel-btn.btn-wa:hover{background:#25d366;color:#fff !important;border-color:#25d366}
.share-channel-btn.btn-wa i{color:#25d366}
.share-channel-btn.btn-wa:hover i{color:#fff}

.share-channel-btn.btn-li:hover{background:#0a66c2;color:#fff !important;border-color:#0a66c2}
.share-channel-btn.btn-li i{color:#0a66c2}
.share-channel-btn.btn-li:hover i{color:#fff}

.share-channel-btn.btn-tw:hover{background:#0f1419;color:#fff !important;border-color:#0f1419}
.share-channel-btn.btn-tw i{color:#0f1419}
.share-channel-btn.btn-tw:hover i{color:#fff}

.share-channel-btn.btn-tg:hover{background:#229ED9;color:#fff !important;border-color:#229ED9}
.share-channel-btn.btn-tg i{color:#229ED9}
.share-channel-btn.btn-tg:hover i{color:#fff}

.share-channel-btn.btn-fb:hover{background:#1877f2;color:#fff !important;border-color:#1877f2}
.share-channel-btn.btn-fb i{color:#1877f2}
.share-channel-btn.btn-fb:hover i{color:#fff}

.share-channel-btn.btn-mail:hover{background:#CC2228;color:#fff !important;border-color:#CC2228}
.share-channel-btn.btn-mail i{color:#CC2228}
.share-channel-btn.btn-mail:hover i{color:#fff}

.share-channel-btn.btn-native:hover{background:#1C2280;color:#fff !important;border-color:#1C2280}
.share-channel-btn.btn-native i{color:#1C2280}
.share-channel-btn.btn-native:hover i{color:#fff}

.share-copy-box{
  position:relative;
  display:flex;
  align-items:center;
  background:#fff;
  border:1.5px solid #cbd5e1;
  border-radius:10px;
  overflow:hidden;
  transition:border-color .2s, box-shadow .2s;
}
.share-copy-box:focus-within{border-color:#1C2280;box-shadow:0 0 0 3px rgba(28,34,128,.15)}
.share-copy-input{
  border:none;
  background:transparent;
  padding:12px 14px;
  font-size:13px;
  color:#334155;
  width:100%;
  outline:none;
}
.share-copy-btn{
  background:#1C2280;
  color:#fff;
  border:none;
  padding:12px 20px;
  font-size:13px;
  font-weight:600;
  cursor:pointer;
  white-space:nowrap;
  transition:all .2s;
  display:flex;
  align-items:center;
  gap:6px;
}
.share-copy-btn:hover{background:#2d35c4}
.share-copy-btn.copied{background:#16a34a !important}

/* Highlight effect when navigated via direct link */
.job-card-highlighted{
  animation:jobCardPulse 3s cubic-bezier(.22,1,.36,1) forwards;
}
@keyframes jobCardPulse{
  0%{box-shadow:0 0 0 0 rgba(28,34,128,.7);border-color:#1C2280;transform:translateY(-6px)}
  20%{box-shadow:0 0 0 16px rgba(28,34,128,0);border-color:#CC2228}
  40%{box-shadow:0 0 0 8px rgba(28,34,128,.25);border-color:#1C2280;transform:translateY(-4px)}
  100%{box-shadow:0 12px 40px rgba(28,34,128,.14);border-color:#1C2280;transform:translateY(0)}
}
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
      <div class="col-lg-6 scroll-reveal job-item" style="transition-delay:<?= ($i%2)*0.08 ?>s" data-category="<?= htmlspecialchars($job['category']) ?>" id="job-<?= $job['id'] ?>">
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
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-3 border-top">
            <button class="apply-btn magnetic" onclick="openApplyModal('<?= htmlspecialchars($job['title'], ENT_QUOTES) ?>','<?= htmlspecialchars($job['department'], ENT_QUOTES) ?>','<?= $job['id'] ?>')">
              <i class="fas fa-paper-plane"></i> Apply Now
            </button>
            <button type="button" class="btn-job-share" onclick="openJobShare(<?= htmlspecialchars(json_encode([
              'id'         => (string)$job['id'],
              'title'      => $job['title'],
              'department' => $job['department'],
              'location'   => $job['location'],
              'exp'        => $job['exp'],
              'type'       => $job['type']
            ]), ENT_QUOTES, 'UTF-8') ?>)" title="Share opening with friends &amp; family" aria-label="Share <?= htmlspecialchars($job['title']) ?> job opening">
              <i class="fas fa-share-nodes"></i> <span>Share Opening</span>
            </button>
          </div>
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
          <input type="hidden" name="job_id" id="form-job-id">
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
            <div class="col-12 mt-2 pt-3 border-top text-center">
              <span class="text-muted small">Know someone suited for this opening?</span>
              <button type="button" class="btn btn-link btn-sm text-primary fw-semibold p-0 ms-1" onclick="shareFromApplyModal()" style="text-decoration:none;">
                <i class="fas fa-share-nodes me-1"></i> Share with friends &amp; family
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Share Job Opening Modal -->
<div class="modal fade" id="shareJobModal" tabindex="-1" aria-labelledby="shareJobModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="shareJobModalLabel">
          <i class="fas fa-share-nodes me-2" style="color:#5BA8D4;"></i> Share Job Opening
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <!-- Job info summary -->
        <div class="share-job-preview">
          <div class="share-preview-title" id="share-modal-title">Position</div>
          <div class="share-preview-meta">
            <span id="share-modal-dept"><i class="fas fa-sitemap me-1" style="color:#1C2280;"></i> Department</span>
            <span id="share-modal-loc"><i class="fas fa-map-marker-alt me-1" style="color:#CC2228;"></i> Location</span>
            <span id="share-modal-exp" style="display:none;"><i class="fas fa-briefcase me-1" style="color:#f59e0b;"></i> Exp</span>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2">
          <span style="font-size:11px;font-weight:700;letter-spacing:1px;color:#64748b;text-transform:uppercase;">Share via Social &amp; Messaging</span>
          <span style="font-size:11px;color:#94a3b8;"><i class="fas fa-user-friends me-1"></i> Quick 1-Click</span>
        </div>

        <div class="share-grid">
          <!-- WhatsApp -->
          <a href="#" id="share-wa" target="_blank" rel="noopener noreferrer" class="share-channel-btn btn-wa" title="Share on WhatsApp">
            <i class="fab fa-whatsapp"></i>
            <span>WhatsApp</span>
          </a>
          <!-- LinkedIn -->
          <a href="#" id="share-li" target="_blank" rel="noopener noreferrer" class="share-channel-btn btn-li" title="Share on LinkedIn">
            <i class="fab fa-linkedin-in"></i>
            <span>LinkedIn</span>
          </a>
          <!-- X / Twitter -->
          <a href="#" id="share-tw" target="_blank" rel="noopener noreferrer" class="share-channel-btn btn-tw" title="Share on X (Twitter)">
            <i class="fab fa-x-twitter"></i>
            <span>X (Twitter)</span>
          </a>
          <!-- Telegram -->
          <a href="#" id="share-tg" target="_blank" rel="noopener noreferrer" class="share-channel-btn btn-tg" title="Share on Telegram">
            <i class="fab fa-telegram"></i>
            <span>Telegram</span>
          </a>
          <!-- Facebook -->
          <a href="#" id="share-fb" target="_blank" rel="noopener noreferrer" class="share-channel-btn btn-fb" title="Share on Facebook">
            <i class="fab fa-facebook-f"></i>
            <span>Facebook</span>
          </a>
          <!-- Email -->
          <a href="#" id="share-mail" class="share-channel-btn btn-mail" title="Send via Email">
            <i class="fas fa-envelope"></i>
            <span>Email</span>
          </a>
          <!-- Native Mobile Share Sheet (Visible when supported) -->
          <button type="button" id="share-native-btn" class="share-channel-btn btn-native d-none" onclick="triggerNativeShare()" title="Open System Share Sheet">
            <i class="fas fa-arrow-up-from-bracket"></i>
            <span>More Apps</span>
          </button>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2">
          <span style="font-size:11px;font-weight:700;letter-spacing:1px;color:#64748b;text-transform:uppercase;">Or Copy Job Link</span>
        </div>
        <div class="share-copy-box">
          <input type="text" id="share-link-input" class="share-copy-input" readonly value="">
          <button type="button" id="share-copy-btn" class="share-copy-btn" onclick="copyShareJobLink()">
            <i class="fas fa-copy"></i>
            <span id="copy-btn-text">Copy Link</span>
          </button>
        </div>
        <div id="copy-success-note" class="text-success small mt-2 d-none fw-semibold">
          <i class="fas fa-check-circle me-1"></i> Direct link copied! Paste it in chats, SMS, or emails to friends &amp; family.
        </div>
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
      item.style.display = "";
      item.classList.remove("hidden");
      visible++;
    } else {
      item.style.display = "none";
      item.classList.add("hidden");
    }
  });
  var noMsg = document.getElementById("noJobsMsg");
  noMsg.style.display = (visible === 0) ? "block" : "none";
}

// ── Apply Modal ─────────────────────────────────────────────────
function openApplyModal(title, dept, id) {
  var label = document.getElementById("applyModalLabel");
  if (label) {
    label.innerHTML = \'<i class="fas fa-briefcase me-2"></i> Apply for <span id="modal-job-title">\' + title + \'</span>\';
  }
  document.getElementById("form-job-title").value = title;
  document.getElementById("form-department").value = dept;
  var idInput = document.getElementById("form-job-id");
  if (idInput) idInput.value = id || "";
  var fb = document.getElementById("apply-feedback");
  if (fb) fb.className = "d-none";
  var form = document.getElementById("applyForm");
  if (form) form.reset();
  new bootstrap.Modal(document.getElementById("applyModal")).show();
}

// ── Job Share System ────────────────────────────────────────────
var currentShareJob = null;

function openJobShare(job) {
  currentShareJob = job;
  document.getElementById("share-modal-title").textContent = job.title || "Job Opening";
  document.getElementById("share-modal-dept").innerHTML = \'<i class="fas fa-sitemap me-1" style="color:#1C2280;"></i> \' + (job.department || "Vortexsoft Group");
  document.getElementById("share-modal-loc").innerHTML = \'<i class="fas fa-map-marker-alt me-1" style="color:#CC2228;"></i> \' + (job.location || "Pan India");
  
  var expEl = document.getElementById("share-modal-exp");
  if (job.exp) {
    expEl.innerHTML = \'<i class="fas fa-briefcase me-1" style="color:#f59e0b;"></i> \' + job.exp;
    expEl.style.display = "inline-flex";
  } else {
    expEl.style.display = "none";
  }

  // Direct canonical link pointing specifically to this job opening
  var baseUrl = window.location.origin + window.location.pathname;
  var shareUrl = baseUrl + "?job=" + encodeURIComponent(job.id) + "#job-" + encodeURIComponent(job.id);
  document.getElementById("share-link-input").value = shareUrl;

  // Reset copy state
  var copyBtn = document.getElementById("share-copy-btn");
  if (copyBtn) copyBtn.classList.remove("copied");
  var copyText = document.getElementById("copy-btn-text");
  if (copyText) copyText.textContent = "Copy Link";
  var copyNote = document.getElementById("copy-success-note");
  if (copyNote) copyNote.classList.add("d-none");

  // WhatsApp share URL with pre-composed message
  var waMessage = "🚀 *Job Opening at Vortexsoft Innovations*\\n\\n"
    + "📌 *Role:* " + job.title + "\\n"
    + "🏢 *Department:* " + job.department + "\\n"
    + "📍 *Location:* " + job.location + (job.exp ? " (" + job.exp + ")" : "") + "\\n\\n"
    + "Know someone looking for this opportunity? Explore & apply here:\\n"
    + shareUrl;
  document.getElementById("share-wa").href = "https://api.whatsapp.com/send?text=" + encodeURIComponent(waMessage);

  // LinkedIn share URL
  document.getElementById("share-li").href = "https://www.linkedin.com/sharing/share-offsite/?url=" + encodeURIComponent(shareUrl);

  // X / Twitter share URL
  var twText = "We are hiring: " + job.title + " (" + job.location + ") at @VortexsoftGroup! Apply or share with friends:";
  document.getElementById("share-tw").href = "https://twitter.com/intent/tweet?text=" + encodeURIComponent(twText) + "&url=" + encodeURIComponent(shareUrl);

  // Telegram share URL
  document.getElementById("share-tg").href = "https://t.me/share/url?url=" + encodeURIComponent(shareUrl) + "&text=" + encodeURIComponent("Job Opening: " + job.title + " at Vortexsoft Group");

  // Facebook share URL
  document.getElementById("share-fb").href = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(shareUrl);

  // Email to friends & family
  var emailSubject = "Job Opening: " + job.title + " at Vortexsoft Group";
  var emailBody = "Hi,\\n\\nI found this job opening at Vortexsoft Innovations and thought of sharing it with you:\\n\\n"
    + "Role: " + job.title + "\\n"
    + "Department: " + job.department + "\\n"
    + "Location: " + job.location + (job.exp ? " (" + job.exp + ")" : "") + "\\n\\n"
    + "You can view full details and apply directly here:\\n" + shareUrl + "\\n\\n"
    + "Best wishes";
  document.getElementById("share-mail").href = "mailto:?subject=" + encodeURIComponent(emailSubject) + "&body=" + encodeURIComponent(emailBody);

  // Check if native Web Share API is available (phones/tablets/supported browsers)
  var nativeBtn = document.getElementById("share-native-btn");
  if (nativeBtn) {
    if (navigator.share) {
      nativeBtn.classList.remove("d-none");
    } else {
      nativeBtn.classList.add("d-none");
    }
  }

  // Open modal
  new bootstrap.Modal(document.getElementById("shareJobModal")).show();
}

function triggerNativeShare() {
  if (!currentShareJob || !navigator.share) return;
  var shareUrl = document.getElementById("share-link-input").value;
  navigator.share({
    title: currentShareJob.title + " — Vortexsoft Group Careers",
    text: "Check out this job opening: " + currentShareJob.title + " (" + currentShareJob.location + ") at Vortexsoft Innovations!",
    url: shareUrl
  }).catch(function(){ /* dismissed */ });
}

function copyShareJobLink() {
  var input = document.getElementById("share-link-input");
  if (!input) return;
  input.select();
  input.setSelectionRange(0, 99999);

  function markCopied() {
    var copyBtn = document.getElementById("share-copy-btn");
    var copyText = document.getElementById("copy-btn-text");
    var copyNote = document.getElementById("copy-success-note");
    if (copyBtn) copyBtn.classList.add("copied");
    if (copyText) copyText.innerHTML = \'<i class="fas fa-check me-1"></i> Copied!\';
    if (copyNote) copyNote.classList.remove("d-none");
    setTimeout(function(){
      if (copyBtn) copyBtn.classList.remove("copied");
      if (copyText) copyText.textContent = "Copy Link";
    }, 2800);
  }

  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(input.value).then(markCopied).catch(function(){
      document.execCommand("copy");
      markCopied();
    });
  } else {
    document.execCommand("copy");
    markCopied();
  }
}

function shareFromApplyModal() {
  var title = document.getElementById("form-job-title").value;
  var dept  = document.getElementById("form-department").value;
  var id    = document.getElementById("form-job-id").value;
  var applyModalEl = document.getElementById("applyModal");
  var applyModalInstance = bootstrap.Modal.getInstance(applyModalEl);
  if (applyModalInstance) applyModalInstance.hide();
  setTimeout(function(){
    openJobShare({
      id: id || "1",
      title: title || "Open Position",
      department: dept || "Vortexsoft Group",
      location: "Bengaluru / Remote",
      exp: ""
    });
  }, 350);
}

// ── Direct Link Anchor & Pulse Highlight Listener ───────────────
(function(){
  function checkDirectJob() {
    var params = new URLSearchParams(window.location.search);
    var jobId = params.get("job");
    if (!jobId && window.location.hash) {
      var match = window.location.hash.match(/#job-([a-zA-Z0-9_-]+)/);
      if (match) jobId = match[1];
    }
    if (jobId) {
      var target = document.getElementById("job-" + jobId);
      if (target) {
        var cat = target.getAttribute("data-category");
        var activeFilter = document.querySelector(".filter-btn.active");
        if (activeFilter && activeFilter.getAttribute("data-filter") !== "All" && activeFilter.getAttribute("data-filter") !== cat) {
          var targetFilterBtn = document.querySelector(\'.filter-btn[data-filter="\' + CSS.escape(cat) + \'"]\') || document.querySelector(\'.filter-btn[data-filter="All"]\');
          if (targetFilterBtn) filterJobs(targetFilterBtn);
        }
        setTimeout(function(){
          target.scrollIntoView({ behavior: "smooth", block: "center" });
          var card = target.querySelector(".job-card");
          if (card) {
            card.classList.add("job-card-highlighted");
            setTimeout(function(){ card.classList.remove("job-card-highlighted"); }, 3500);
          }
        }, 400);
      }
    }
  }

  if (document.readyState === "complete" || document.readyState === "interactive") {
    setTimeout(checkDirectJob, 250);
  } else {
    document.addEventListener("DOMContentLoaded", function(){ setTimeout(checkDirectJob, 250); });
  }
})();

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
