<?php
/**
 * Vortexsoft Innovations — Master About Us Page (about.php)
 * Premium Enterprise Corporate Profile, Global Hubs & Strategic Pillars
 */

$page_title   = 'About Us | Global IT & BPO Partner | Vortexsoft Group';
$page_desc    = 'Discover Vortexsoft Group — an ISO 27001 certified global IT & BPO outsourcing company in Pune, Bengaluru, and USA delivering Healthcare, AI, and Software.';
$canonical_url = 'https://www.vortexsoftinnovations.com/about.php';
$prefix       = './';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ═══════ 1. HERO SECTION ═══════ -->
<div class="page-hero">
  <div class="page-hero-glow"></div>
  <div class="container" style="position:relative;z-index:2;">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb mb-2" style="background:transparent;padding:0;font-size:13px;">
        <li class="breadcrumb-item"><a href="index.php" style="color:rgba(255,255,255,0.6);text-decoration:none;"><i class="fas fa-home me-1"></i> Home</a></li>
        <li class="breadcrumb-item active" style="color:rgba(255,255,255,0.9);">About Us</li>
      </ol>
    </nav>
    <div class="section-tag mb-3">
      <i class="fas fa-building"></i> Corporate Profile &amp; Overview
    </div>
    <h1 style="font-size:clamp(1.9rem,3.8vw,2.9rem);font-weight:800;color:#fff;line-height:1.2;letter-spacing:-0.025em;margin-bottom:14px;">
      About <span style="background:linear-gradient(135deg,#FFFFFF 30%,#5BA8D4 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Vortexsoft Group</span>
    </h1>
    <p style="color:rgba(255,255,255,0.8);font-size:15px;max-width:680px;line-height:1.75;margin-bottom:24px;">
      Empowering global enterprises through the synergy of <strong>AI, Intelligent Automation, and Human Expertise</strong>. Delivering high-precision IT, BPO, Healthcare, Publishing, and AI solutions worldwide.
    </p>
    <div class="d-flex flex-wrap gap-2">
      <span style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:100px;padding:5px 14px;font-size:12px;color:#fff;"><i class="fas fa-shield-alt text-success me-1"></i> ISO 27001:2013</span>
      <span style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:100px;padding:5px 14px;font-size:12px;color:#fff;"><i class="fas fa-heartbeat text-danger me-1"></i> HIPAA Compliant</span>
      <span style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:100px;padding:5px 14px;font-size:12px;color:#fff;"><i class="fas fa-certificate text-warning me-1"></i> Startup India</span>
      <span style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:100px;padding:5px 14px;font-size:12px;color:#fff;"><i class="fas fa-globe text-info me-1"></i> Pune · Bengaluru · Wyoming, USA</span>
    </div>
  </div>
</div>

<!-- ═══════ 2. CORPORATE PROFILE SECTION ═══════ -->
<section class="section-pad" style="background:#FFFFFF;">
  <div class="container">
    
    <!-- GEO Citable Fact Block -->
    <div class="mb-5">
      <?= render_geo_fact_block() ?>
    </div>

    <div class="row align-items-center gy-5">
      <div class="col-lg-6">
        <div class="section-tag">Corporate Background</div>
        <h2 class="section-title">Global IT &amp; Business Process <span class="highlight">Outsourcing Partner</span></h2>
        <div class="section-divider" style="margin-left:0;"></div>
        <p style="color:var(--vs-text-body);font-size:15px;line-height:1.75;margin-bottom:16px;">
          <strong>Vortexsoft Innovations Pvt. Ltd.</strong>, the primary operating division of the <strong>Vortexsoft Group</strong>, is an <strong>ISO 27001:2013 certified</strong> IT software and business process outsourcing organization headquartered in <strong>Pune, Maharashtra, India</strong> with delivery operations in <strong>Bengaluru</strong> and an international corporate entity in <strong>Sheridan, Wyoming, USA</strong>.
        </p>
        <p style="color:var(--vs-text-muted);font-size:14.5px;line-height:1.75;margin-bottom:16px;">
          Founded in <strong>2020</strong>, over <strong>6+ years</strong> of continuous innovation we have grown into a multi-disciplinary technology powerhouse. We manage <strong>65+ specialized service domains</strong>, supporting healthcare providers, commercial real estate firms, global STM publishers, financial institutions, technology startups, and enterprise partners across North America, Europe, Australia, and Asia-Pacific.
        </p>
        <p style="color:var(--vs-text-muted);font-size:14.5px;line-height:1.75;margin-bottom:24px;">
          Our core differentiator lies in our hybrid <strong>"AI + Operations + Human Expertise"</strong> delivery model — combining state-of-the-art machine learning automation with domain-certified expert teams to guarantee <strong>99.9% accuracy</strong> and up to <strong>40–60% cost reduction</strong>.
        </p>

        <!-- Stats Grid -->
        <div class="row g-3">
          <div class="col-6 col-sm-3">
            <div style="background:#F1F5F9;border-radius:12px;padding:14px;border:1px solid #E2E8F0;text-align:center;">
              <h3 style="color:#1C2280;font-weight:800;margin:0;font-size:22px;">150+</h3>
              <div style="font-size:11.5px;color:#64748B;font-weight:600;margin-top:2px;">Global Clients</div>
            </div>
          </div>
          <div class="col-6 col-sm-3">
            <div style="background:#FFF5F5;border-radius:12px;padding:14px;border:1px solid #FED7D7;text-align:center;">
              <h3 style="color:#CC2228;font-weight:800;margin:0;font-size:22px;">200+</h3>
              <div style="font-size:11.5px;color:#64748B;font-weight:600;margin-top:2px;">Projects Done</div>
            </div>
          </div>
          <div class="col-6 col-sm-3">
            <div style="background:#F0FDF4;border-radius:12px;padding:14px;border:1px solid #BBF7D0;text-align:center;">
              <h3 style="color:#10B981;font-weight:800;margin:0;font-size:22px;">200+</h3>
              <div style="font-size:11.5px;color:#64748B;font-weight:600;margin-top:2px;">Expert Team</div>
            </div>
          </div>
          <div class="col-6 col-sm-3">
            <div style="background:#FEF3C7;border-radius:12px;padding:14px;border:1px solid #FDE68A;text-align:center;">
              <h3 style="color:#D97706;font-weight:800;margin:0;font-size:22px;">24/7</h3>
              <div style="font-size:11.5px;color:#64748B;font-weight:600;margin-top:2px;">Support SLA</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Feature Grid Right -->
      <div class="col-lg-6">
        <div class="row g-3">
          <div class="col-sm-6">
            <div class="vs-card">
              <div class="vs-icon-box" style="background:rgba(28,34,128,0.08);color:#1C2280;">
                <i class="fas fa-shield-alt"></i>
              </div>
              <h5 style="font-weight:700;color:var(--vs-text-heading);margin-bottom:6px;">ISO 27001 Certified</h5>
              <p style="font-size:12.5px;color:var(--vs-text-muted);margin:0;line-height:1.6;">ISO 27001:2013 ISMS protocols safeguarding client IP, financial data, and codebases.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="vs-card">
              <div class="vs-icon-box" style="background:rgba(204,34,40,0.08);color:#CC2228;">
                <i class="fas fa-user-md"></i>
              </div>
              <h5 style="font-weight:700;color:var(--vs-text-heading);margin-bottom:6px;">HIPAA Compliant</h5>
              <p style="font-size:12.5px;color:var(--vs-text-muted);margin:0;line-height:1.6;">Strict PHI data handling standards for US medical coding, billing, and revenue cycle ops.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="vs-card">
              <div class="vs-icon-box" style="background:rgba(91,168,212,0.1);color:#5BA8D4;">
                <i class="fas fa-award"></i>
              </div>
              <h5 style="font-weight:700;color:var(--vs-text-heading);margin-bottom:6px;">Startup India</h5>
              <p style="font-size:12.5px;color:var(--vs-text-muted);margin:0;line-height:1.6;">Officially recognized under the Government of India's flagship Innovation Initiative.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="vs-card">
              <div class="vs-icon-box" style="background:rgba(16,185,129,0.1);color:#10B981;">
                <i class="fas fa-globe"></i>
              </div>
              <h5 style="font-weight:700;color:var(--vs-text-heading);margin-bottom:6px;">Global Network</h5>
              <p style="font-size:12.5px;color:var(--vs-text-muted);margin:0;line-height:1.6;">Pune Corporate HQ, Bengaluru Tech Center, and Wyoming USA International Entity.</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ═══════ 3. GLOBAL LOCATIONS & DELIVERY HUBS ═══════ -->
<section class="section-pad" style="background:#F8FAFC;">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-tag">Global Footprint</div>
      <h2 class="section-title">Our <span class="highlight">Global Locations &amp; Delivery Hubs</span></h2>
      <div class="section-divider"></div>
      <p class="section-subtitle">Strategic multi-location presence ensuring seamless time-zone overlap, business continuity, and 24/7 global client support.</p>
    </div>

    <div class="row g-4">
      <!-- Pune HQ -->
      <div class="col-lg-4">
        <div class="vs-card" style="border:2px solid #CC2228;background:linear-gradient(180deg,#FFFFFF 0%,#FFF5F5 100%);">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="badge" style="background:#CC2228;color:#fff;font-size:10.5px;padding:5px 10px;font-weight:700;">PRIMARY HEADQUARTERS</span>
            <span style="font-size:24px;">🇮🇳</span>
          </div>
          <h4 style="font-size:17.5px;font-weight:800;color:#1C2280;margin-bottom:8px;">Pune Corporate HQ</h4>
          <p style="font-size:13px;color:#334155;margin-bottom:12px;line-height:1.6;">
            <strong>Address:</strong> 502, 4th Floor, Dangat Patil Empire, Vadgaon Budruk, Pune, Maharashtra 411041, India
          </p>
          <p style="font-size:12.5px;color:#64748B;margin-bottom:20px;line-height:1.6;flex-grow:1;">
            Primary executive headquarters housing core software R&amp;D, AI engineering, enterprise solution architecture, and strategic management operations.
          </p>
          <a href="https://share.google/XKt2SVYsKfiNqrVGx" target="_blank" class="btn btn-sm" style="background:#CC2228;color:#fff;font-weight:700;padding:8px 18px;border-radius:8px;">
            <i class="fas fa-map-marked-alt me-1"></i> Open Google Map
          </a>
        </div>
      </div>

      <!-- Bengaluru Office -->
      <div class="col-lg-4">
        <div class="vs-card">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="badge" style="background:#1C2280;color:#fff;font-size:10.5px;padding:5px 10px;font-weight:600;">DELIVERY CENTER</span>
            <span style="font-size:24px;">🇮🇳</span>
          </div>
          <h4 style="font-size:17.5px;font-weight:800;color:var(--vs-text-heading);margin-bottom:8px;">Bengaluru Tech Center</h4>
          <p style="font-size:13px;color:#334155;margin-bottom:12px;line-height:1.6;">
            <strong>Address:</strong> No.125, Ranganath Complex, Madiwala, HSR Layout, Bengaluru 560068, India
          </p>
          <p style="font-size:12.5px;color:#64748B;margin-bottom:20px;line-height:1.6;flex-grow:1;">
            Secondary delivery center housing Healthcare BPO, Publishing Prepress, Title &amp; Real Estate operations, and high-throughput data annotation teams.
          </p>
          <a href="https://www.google.com/maps/search/?api=1&query=Second+floor+No.125+Ranganath+Complex+Madiwala+HSR+Layout+Bengaluru+560068" target="_blank" class="btn btn-sm btn-outline-secondary" style="font-weight:600;padding:8px 18px;border-radius:8px;">
            <i class="fas fa-directions me-1"></i> Get Directions
          </a>
        </div>
      </div>

      <!-- USA Office -->
      <div class="col-lg-4">
        <div class="vs-card">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="badge" style="background:#0284C7;color:#fff;font-size:10.5px;padding:5px 10px;font-weight:600;">INTERNATIONAL NODE</span>
            <span style="font-size:24px;">🇺🇸</span>
          </div>
          <h4 style="font-size:17.5px;font-weight:800;color:var(--vs-text-heading);margin-bottom:8px;">USA Corporate Office</h4>
          <p style="font-size:13px;color:#334155;margin-bottom:12px;line-height:1.6;">
            <strong>Address:</strong> 30 N Gould St Ste 100, Sheridan, WY 82801, United States
          </p>
          <p style="font-size:12.5px;color:#64748B;margin-bottom:20px;line-height:1.6;flex-grow:1;">
            North American client liaison, account management, legal compliance, and contract execution node for US and global enterprise partners.
          </p>
          <a href="https://maps.google.com/?cid=4698826826648482061" target="_blank" class="btn btn-sm btn-outline-secondary" style="font-weight:600;padding:8px 18px;border-radius:8px;">
            <i class="fas fa-directions me-1"></i> View US Office
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════ 4. MILESTONES & TIMELINE ═══════ -->
<section class="section-pad" style="background:#FFFFFF;">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-tag">Evolution &amp; Scale</div>
      <h2 class="section-title">Our Growth <span class="highlight">Journey</span></h2>
      <div class="section-divider"></div>
      <p class="section-subtitle">A journey of continuous innovation, domain diversification, and global partner trust since 2020.</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div style="position:relative;padding-left:32px;border-left:2px dashed #CBD5E1;">
          <?php foreach([
            ['2020','Company Founded in Pune','Established Vortexsoft Innovations with a core team of software engineers & BPO professionals delivering digital publishing and medical coding.'],
            ['2022','ISO 27001 Certification & US Entity','Achieved ISO 27001:2013 ISMS certification and incorporated our international corporate entity in Sheridan, Wyoming, USA.'],
            ['2023','Bengaluru Tech Center Expansion','Opened our secondary high-throughput delivery center in Bengaluru (HSR Layout) scaling up Healthcare BPO and Data Annotation.'],
            ['2024','Proprietary AI Platform Suite','Launched VortexEXHO and autonomous agentic workflows, cutting operational turnaround times by 60% for global publishing and real estate partners.'],
            ['2026','Global Scale & 75+ Service Domains','150+ international enterprise clients across North America, Europe, and APAC with 200+ multidisciplinary team members.'],
          ] as [$year,$title,$desc]): ?>
          <div style="position:relative;padding-bottom:32px;">
            <div style="position:absolute;left:-41px;top:0;width:16px;height:16px;border-radius:50%;background:#CC2228;border:3px solid #fff;box-shadow:0 0 0 3px rgba(204,34,40,0.2);"></div>
            <div style="font-size:12px;font-weight:800;letter-spacing:1px;color:#CC2228;text-transform:uppercase;margin-bottom:2px;"><?= $year ?></div>
            <h4 style="font-size:17px;font-weight:700;color:var(--vs-text-heading);margin-bottom:6px;"><?= $title ?></h4>
            <p style="font-size:13.5px;color:var(--vs-text-muted);line-height:1.65;margin:0;"><?= $desc ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
