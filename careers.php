<?php
/**
 * Vortexsoft Innovations — Careers Page (careers.php)
 * Shows live job listings from MySQL + application form
 */

$page_title   = 'Careers at Vortexsoft Group — Join Our Team | IT & BPO Jobs in Bengaluru, India';
$page_desc    = 'Explore exciting career opportunities at Vortexsoft Group. Join our team of 200+ professionals in Bengaluru & Pune. Apply online for IT, BPO, Healthcare, Publishing, and more roles.';
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

// Static fallback (used if DB has no jobs yet)
if (empty($jobs)) {
    $jobs = [
      ['id'=>1,'title'=>'Medical Coder','department'=>'Healthcare BPO','type'=>'Full Time','location'=>'Bengaluru','exp'=>'1–3 years','skills'=>['CPC Certified','ICD-10','CPT','E&M Coding'],'desc'=>'Review and code inpatient/outpatient medical records using ICD-10, CPT, and HCPCS codes. Ensure accuracy and compliance with payer requirements.','urgent'=>true],
      ['id'=>2,'title'=>'PHP Developer','department'=>'IT & Software','type'=>'Full Time','location'=>'Bengaluru/Remote','exp'=>'2–4 years','skills'=>['PHP','Laravel','MySQL','REST APIs'],'desc'=>'Develop and maintain scalable PHP applications. Work with Laravel framework, MySQL databases, and REST APIs. Build admin panels and client portals.','urgent'=>false],
      ['id'=>3,'title'=>'Data Annotation Specialist','department'=>'AI / Data','type'=>'Full Time','location'=>'Bengaluru','exp'=>'0–2 years','skills'=>['Image Annotation','CVAT','Labelbox','Quality Control'],'desc'=>'Perform high-quality image, video, audio, and text annotation for AI/ML training datasets. Work with tools like CVAT, Labelbox, and Scale AI.','urgent'=>false],
      ['id'=>4,'title'=>'Publishing Editor','department'=>'Publishing','type'=>'Full Time','location'=>'Bengaluru','exp'=>'2–5 years','skills'=>['InDesign','QuarkXPress','XML','ePUB3'],'desc'=>'Handle typesetting, layout, ePUB3 conversion, and proofreading of academic and trade books. Work with publishers from USA, UK, and Europe.','urgent'=>false],
      ['id'=>5,'title'=>'Digital Marketing Executive','department'=>'Marketing','type'=>'Full Time','location'=>'Bengaluru','exp'=>'1–3 years','skills'=>['SEO','Google Ads','Meta Ads','Content Marketing'],'desc'=>'Plan and execute SEO, PPC, and social media campaigns for B2B and B2C clients. Manage monthly performance reports and analytics dashboards.','urgent'=>false],
      ['id'=>6,'title'=>'Lease Administrator','department'=>'Real Estate BPO','type'=>'Full Time','location'=>'Pune','exp'=>'1–4 years','skills'=>['Lease Abstraction','CAM Reconciliation','MRI Software','Excel'],'desc'=>'Abstract and administer commercial real estate leases. Handle CAM reconciliation, rent roll management, and property accounting for US clients.','urgent'=>false],
      ['id'=>7,'title'=>'HR Executive','department'=>'Human Resources','type'=>'Full Time','location'=>'Bengaluru','exp'=>'1–3 years','skills'=>['Recruitment','Onboarding','HRMS','Labor Law'],'desc'=>'Manage end-to-end recruitment for IT and BPO roles. Handle onboarding, employee engagement, attendance, payroll coordination, and compliance.','urgent'=>false],
      ['id'=>8,'title'=>'Accounts Executive','department'=>'Finance & Accounting','type'=>'Full Time','location'=>'Bengaluru','exp'=>'1–3 years','skills'=>['Tally','QuickBooks','GST','TDS'],'desc'=>'Handle bookkeeping, accounts payable/receivable, GST filing, TDS, bank reconciliation, and monthly financial reporting for Indian and US clients.','urgent'=>false],
    ];
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
.job-card{background:#fff;border-radius:18px;padding:28px;border:1.5px solid #e8ecff;transition:all .3s;position:relative;overflow:hidden}
.job-card::before{content:'';position:absolute;top:0;left:0;width:5px;height:100%;background:linear-gradient(180deg,#1C2280,#CC2228);transform:scaleY(0);transform-origin:top;transition:.3s}
.job-card:hover{border-color:transparent;box-shadow:0 12px 40px rgba(28,34,128,.14);transform:translateY(-4px)}
.job-card:hover::before{transform:scaleY(1)}
.job-badge{font-size:11px;font-weight:700;padding:4px 10px;border-radius:100px;letter-spacing:.5px}
.urgent-badge{background:#fff0f0;color:#CC2228;border:1px solid rgba(204,34,40,.2)}
.type-badge{background:rgba(28,34,128,.07);color:#1C2280}
.skill-tag{background:rgba(28,34,128,.06);color:#1C2280;font-size:12px;font-weight:600;padding:4px 10px;border-radius:6px;border:1px solid rgba(28,34,128,.1)}
.apply-btn{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;font-family:'Poppins',sans-serif;font-size:13px;font-weight:700;padding:10px 22px;border-radius:8px;border:none;cursor:pointer;transition:.3s;display:inline-flex;align-items:center;gap:8px}
.apply-btn:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(28,34,128,.3);color:#fff}
/* Application Form */
#applyModal .modal-content{border-radius:20px;border:none;overflow:hidden}
#applyModal .modal-header{background:linear-gradient(135deg,#1C2280,#CC2228);padding:24px 28px;border:none}
#applyModal .modal-header .modal-title{color:#fff;font-size:18px;font-weight:700}
#applyModal .btn-close{filter:invert(1)}
.btn-submit-apply{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;font-family:'Poppins',sans-serif;font-size:15px;font-weight:600;padding:14px;border:none;border-radius:10px;width:100%;transition:.3s;display:flex;align-items:center;justify-content:center;gap:8px}
.btn-submit-apply:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(28,34,128,.3);color:#fff}
</style>

<!-- Hero -->
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Careers</li></ol></nav>
    <h1>Join the <span style="color:#5BA8D4;">Vortexsoft</span> Team</h1>
    <p style="color:rgba(255,255,255,.75);font-size:16px;margin-top:12px;max-width:560px;">Be part of a fast-growing global IT & BPO company. We're hiring passionate professionals across Bengaluru, Pune, and Remote positions.</p>
    <div class="d-flex gap-3 mt-4 flex-wrap">
      <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:12px;padding:12px 20px;color:#fff;font-size:13px;font-weight:600;"><i class="fas fa-users me-2" style="color:#5BA8D4;"></i> 200+ Team Members</div>
      <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:12px;padding:12px 20px;color:#fff;font-size:13px;font-weight:600;"><i class="fas fa-map-marker-alt me-2" style="color:#CC2228;"></i> Bengaluru & Pune</div>
      <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:12px;padding:12px 20px;color:#fff;font-size:13px;font-weight:600;"><i class="fas fa-rocket me-2" style="color:#f59e0b;"></i> Growing Fast</div>
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
          <p style="color:rgba(255,255,255,.8);margin-bottom:24px;">Send your resume directly to our HR team. We respond to all applications within 3–5 business days.</p>
          <div style="background:rgba(255,255,255,.1);border-radius:14px;padding:20px;margin-bottom:16px;">
            <div style="font-size:12px;color:rgba(255,255,255,.6);font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;">HR Email</div>
            <a href="mailto:<?= EMAIL_HR ?>" style="color:#fff;font-weight:700;font-size:16px;"><?= EMAIL_HR ?></a>
          </div>
          <div style="background:rgba(255,255,255,.1);border-radius:14px;padding:20px;margin-bottom:24px;">
            <div style="font-size:12px;color:rgba(255,255,255,.6);font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;">WhatsApp HR</div>
            <a href="<?= SOCIAL_WHATSAPP ?>" target="_blank" style="color:#fff;font-weight:700;font-size:16px;"><?= PHONE_INDIA ?></a>
          </div>
          <a href="mailto:<?= EMAIL_HR ?>" class="btn" style="background:#fff;color:#1C2280;font-weight:700;border-radius:10px;padding:14px 28px;width:100%;font-size:15px;"><i class="fas fa-paper-plane me-2"></i> Email Your Resume</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Job Listings -->
<section class="py-5" style="background:var(--bg-light,#f0f2ff);">
  <div class="container">
    <div class="text-center mb-5 scroll-reveal">
      <div class="section-tag">Open Positions</div>
      <h2 class="section-title">Current <span class="highlight">Job Openings</span></h2>
      <div class="section-divider"></div>
      <p class="section-subtitle">We have <?= count($jobs) ?>+ open positions across Bengaluru, Pune, and Remote. Apply online or email your resume to <a href="mailto:<?= EMAIL_HR ?>" style="color:var(--primary);"><?= EMAIL_HR ?></a>.</p>
    </div>
    <div class="row g-4">
      <?php foreach($jobs as $i=>$job): ?>
      <div class="col-lg-6 scroll-reveal" style="transition-delay:<?= ($i%2)*0.1 ?>s">
        <div class="job-card">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
              <h5 style="font-family:'Poppins',sans-serif;font-weight:700;font-size:17px;color:#1C2280;margin-bottom:4px;"><?= $job['title'] ?></h5>
              <div style="font-size:13px;color:#64748b;font-weight:500;"><i class="fas fa-sitemap me-1"></i> <?= $job['department'] ?></div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
              <?php if($job['urgent']): ?><span class="job-badge urgent-badge"><i class="fas fa-bolt me-1"></i> Urgent</span><?php endif; ?>
              <span class="job-badge type-badge"><?= $job['type'] ?></span>
            </div>
          </div>
          <div class="d-flex gap-4 mb-3" style="font-size:13px;color:#64748b;">
            <span><i class="fas fa-map-marker-alt me-1" style="color:#CC2228;"></i> <?= $job['location'] ?></span>
            <span><i class="fas fa-briefcase me-1" style="color:#CC2228;"></i> <?= $job['exp'] ?></span>
          </div>
          <p style="font-size:14px;color:#475569;line-height:1.7;margin-bottom:14px;"><?= $job['desc'] ?></p>
          <div class="d-flex flex-wrap gap-2 mb-4">
            <?php foreach($job['skills'] as $sk): ?><span class="skill-tag"><?= $sk ?></span><?php endforeach; ?>
          </div>
          <button class="apply-btn magnetic" onclick="openApplyModal('<?= htmlspecialchars($job['title']) ?>','<?= htmlspecialchars($job['department']) ?>')">
            <i class="fas fa-paper-plane"></i> Apply Now
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5 scroll-reveal">
      <div style="background:#fff;border-radius:16px;padding:32px;border:1px solid #e8ecff;display:inline-block;max-width:560px;">
        <div style="font-size:28px;margin-bottom:12px;">💼</div>
        <h5 style="font-family:'Poppins',sans-serif;font-weight:700;color:#1C2280;margin-bottom:8px;">Don't See Your Role?</h5>
        <p style="color:#64748b;font-size:14px;margin-bottom:20px;">We're always looking for talented people. Send your resume to our HR team and we'll keep you in mind for future openings.</p>
        <a href="mailto:<?= EMAIL_HR ?>" class="btn" style="background:linear-gradient(135deg,#1C2280,#CC2228);color:#fff;border-radius:10px;padding:12px 28px;font-weight:700;font-size:14px;"><i class="fas fa-envelope me-2"></i> Send Your Resume</a>
      </div>
    </div>
  </div>
</section>

<!-- Apply Modal -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" id="applyModal">
      <div class="modal-header">
        <h5 class="modal-title" id="applyModalLabel"><i class="fas fa-briefcase me-2"></i> Apply for <span id="modal-job-title">Position</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div id="apply-feedback" class="d-none mb-3" role="alert"></div>
        <form id="applyForm" enctype="multipart/form-data" novalidate>
          <input type="hidden" name="job_title" id="form-job-title">
          <input type="hidden" name="department" id="form-department">
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
              <label class="form-label fw-semibold">Upload Resume <span style="color:#64748b;font-weight:400;font-size:12px;">(PDF, DOC, DOCX &mdash; max 5MB &mdash; <em>optional but recommended</em>)</span></label>
              <input type="file" class="form-control" name="resume" id="resumeFile" accept=".pdf,.doc,.docx">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">LinkedIn Profile URL</label>
              <input type="url" class="form-control" name="linkedin_url" placeholder="https://linkedin.com/in/yourprofile">
            </div>
            <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">
            <div class="col-12">
              <label class="form-label fw-semibold">Cover Letter / Why This Role?</label>
              <textarea class="form-control" name="cover_letter" rows="4" placeholder="Tell us why you're a great fit for this role..."></textarea>
            </div>
            <div class="col-12 mb-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="consent" id="consentCheck" required checked>
                <label class="form-check-label" for="consentCheck" style="font-size:12.5px;color:#64748b;">
                  I consent to Vortexsoft Group storing & processing my personal details and resume for recruitment purposes.
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
function openApplyModal(title, dept) {
  var label = document.getElementById("applyModalLabel");
  if (label) {
    label.innerHTML = '<i class="fas fa-briefcase me-2"></i> Apply for <span id="modal-job-title">' + title + '</span>';
  }
  document.getElementById("form-job-title").value = title;
  document.getElementById("form-department").value = dept;
  var fb = document.getElementById("apply-feedback");
  if (fb) fb.className = "d-none";
  var form = document.getElementById("applyForm");
  if (form) form.reset();
  new bootstrap.Modal(document.getElementById("applyModal")).show();
}
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
