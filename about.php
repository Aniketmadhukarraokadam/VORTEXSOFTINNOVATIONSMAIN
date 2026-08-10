<?php
/**
 * Vortexsoft Innovations — About Us Page (about.php)
 * Comprehensive Corporate Profile & Strategic Overview
 */

$page_title   = 'About Us — Vortexsoft Group | Leading Global IT & BPO Outsourcing Company';
$page_desc    = 'Learn about Vortexsoft Group, an ISO 27001 certified global IT & BPO company headquartered in Pune with offices in Bengaluru and USA, delivering Healthcare BPO, Publishing, Real Estate, AI, and Software solutions since 2020.';
$canonical_url = 'https://www.vortexsoftinnovations.com/about.php';
$prefix       = './';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.page-hero{background:linear-gradient(135deg,#080B1A 0%,#1C2280 55%,#0D1035 100%);padding:90px 0 80px;position:relative;overflow:hidden}
.page-hero::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:50px 50px}
.page-hero h1{font-size:clamp(2.2rem,4.5vw,3.2rem);font-weight:800;color:#fff}
.breadcrumb-item,.breadcrumb-item a{color:rgba(255,255,255,.6);font-size:14px}
.breadcrumb-item.active{color:rgba(255,255,255,.9)}
.breadcrumb-item+.breadcrumb-item::before{color:rgba(255,255,255,.4)}

.about-card{background:#fff;border-radius:20px;padding:36px;border:1px solid #e8ecff;box-shadow:0 4px 20px rgba(28,34,128,.06);transition:all .3s;height:100%}
.about-card:hover{transform:translateY(-6px);box-shadow:0 18px 45px rgba(28,34,128,.12);border-color:transparent}
.about-icon{width:60px;height:60px;border-radius:16px;background:rgba(28,34,128,.08);color:#1C2280;display:flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:22px}

.pillar-card{background:#fff;border-radius:18px;padding:28px;border:1px solid #e2e8f0;transition:all .3s;height:100%}
.pillar-card:hover{border-color:#1C2280;box-shadow:0 10px 30px rgba(28,34,128,.1);transform:translateY(-4px)}

.timeline-item{position:relative;padding-left:40px;padding-bottom:36px;border-left:2px dashed #cbd5e1}
.timeline-item:last-child{border-left:2px solid transparent;padding-bottom:0}
.timeline-dot{position:absolute;left:-10px;top:0;width:18px;height:18px;border-radius:50%;background:#CC2228;border:3px solid #fff;box-shadow:0 0 0 3px rgba(204,34,40,.2)}
.timeline-year{font-size:13px;font-weight:800;letter-spacing:1px;color:#CC2228;text-transform:uppercase;margin-bottom:4px}
.timeline-title{font-size:18px;font-weight:700;color:#1e293b;margin-bottom:6px}

.hub-card{background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%);border-radius:20px;padding:32px;border:1px solid #e2e8f0;height:100%;transition:all .3s}
.hub-card:hover{box-shadow:0 12px 35px rgba(0,0,0,.08);transform:translateY(-4px)}
.hub-card.primary-hub{border:2px solid #CC2228;background:linear-gradient(135deg,#ffffff 0%,#fff5f5 100%);}
</style>

<!-- Hero -->
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">About Us</li></ol></nav>
    <h1>About <span style="color:#5BA8D4;">Vortexsoft Group</span></h1>
    <p style="color:rgba(255,255,255,.85);font-size:18px;margin-top:14px;max-width:700px;line-height:1.7;">
      Empowering global enterprises through the synergy of <strong>AI, Intelligent Automation, and Human Expertise</strong>. Delivering high-precision IT, BPO, Healthcare, Publishing, and AI solutions worldwide.
    </p>
  </div>
</div>

<!-- Main About Section -->
<section class="py-5" style="background:#fff;">
  <div class="container">
    <!-- GEO Citable Fact Block -->
    <?= render_geo_fact_block() ?>

    <div class="row align-items-center gy-5">
      <div class="col-lg-6 scroll-reveal-left">
        <div class="section-tag">Corporate Profile</div>
        <h2 class="section-title">Global IT &amp; Business Process <span class="highlight">Outoutsourcing Partner</span></h2>
        <div class="section-divider"></div>
        <p style="color:var(--text-muted);font-size:16px;line-height:1.8;">
          <strong>Vortexsoft Innovations Pvt. Ltd.</strong>, the primary operating division of the <strong>Vortexsoft Group</strong>, is an <strong>ISO 27001:2013 certified</strong> IT software and business process outsourcing organization headquartered in <strong>Pune, Maharashtra, India</strong> with delivery operations in <strong>Bengaluru</strong> and an international corporate entity in <strong>Sheridan, Wyoming, USA</strong>.
        </p>
        <p style="color:var(--text-muted);font-size:15px;line-height:1.8;">
          Founded in <strong>2020</strong>, over <strong>6+ years</strong> of continuous innovation we have grown into a multi-disciplinary technology powerhouse. We manage <strong>65+ specialized service domains</strong>, supporting healthcare providers, commercial real estate firms, global STM publishers, financial institutions, technology startups, and fortune enterprises across North America, Europe, Australia, and Asia-Pacific.
        </p>
        <p style="color:var(--text-muted);font-size:15px;line-height:1.8;">
          Our core differentiator lies in our hybrid <strong>"AI + Operations + Human Expertise"</strong> delivery model — combining state-of-the-art machine learning automation with domain-certified expert teams to guarantee <strong>99.9% accuracy</strong> and up to <strong>40–60% cost reduction</strong>.
        </p>

        <!-- Stats Grid -->
        <div class="row g-3 mt-4">
          <div class="col-6 col-sm-3">
            <div style="background:#f0f2ff;border-radius:14px;padding:16px;border:1px solid #dde2f5;text-align:center;">
              <h3 style="color:#1C2280;font-weight:800;margin:0;font-size:24px;">150+</h3>
              <div style="font-size:12px;color:#64748b;font-weight:600;margin-top:2px;">Global Clients</div>
            </div>
          </div>
          <div class="col-6 col-sm-3">
            <div style="background:#fff0f0;border-radius:14px;padding:16px;border:1px solid #ffd6d6;text-align:center;">
              <h3 style="color:#CC2228;font-weight:800;margin:0;font-size:24px;">200+</h3>
              <div style="font-size:12px;color:#64748b;font-weight:600;margin-top:2px;">Delivered Projects</div>
            </div>
          </div>
          <div class="col-6 col-sm-3">
            <div style="background:#f0fdf4;border-radius:14px;padding:16px;border:1px solid #bbf7d0;text-align:center;">
              <h3 style="color:#10b981;font-weight:800;margin:0;font-size:24px;">200+</h3>
              <div style="font-size:12px;color:#64748b;font-weight:600;margin-top:2px;">Expert Team</div>
            </div>
          </div>
          <div class="col-6 col-sm-3">
            <div style="background:#fef3c7;border-radius:14px;padding:16px;border:1px solid #fde68a;text-align:center;">
              <h3 style="color:#d97706;font-weight:800;margin:0;font-size:24px;">24/7</h3>
              <div style="font-size:12px;color:#64748b;font-weight:600;margin-top:2px;">Digital Support</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Feature Grid -->
      <div class="col-lg-6 scroll-reveal-right">
        <div class="row g-4">
          <div class="col-6">
            <div class="about-card">
              <div class="about-icon"><i class="fas fa-shield-alt"></i></div>
              <h5 style="font-weight:700;color:#1e293b;">ISO 27001 Certified</h5>
              <p style="font-size:13px;color:#64748b;margin:0;line-height:1.6;">ISO 27001:2013 ISMS standards guarding client IP, financial data, and source code.</p>
            </div>
          </div>
          <div class="col-6">
            <div class="about-card">
              <div class="about-icon" style="background:rgba(204,34,40,.08);color:#CC2228;"><i class="fas fa-user-md"></i></div>
              <h5 style="font-weight:700;color:#1e293b;">HIPAA Compliant</h5>
              <p style="font-size:13px;color:#64748b;margin:0;line-height:1.6;">Strict PHI data security protocols for US healthcare RCM, medical coding &amp; billing.</p>
            </div>
          </div>
          <div class="col-6">
            <div class="about-card">
              <div class="about-icon" style="background:rgba(91,168,212,.08);color:#5BA8D4;"><i class="fas fa-award"></i></div>
              <h5 style="font-weight:700;color:#1e293b;">Startup India</h5>
              <p style="font-size:13px;color:#64748b;margin:0;line-height:1.6;">Recognized under Government of India's flagship Startup India Innovation Initiative.</p>
            </div>
          </div>
          <div class="col-6">
            <div class="about-card">
              <div class="about-icon" style="background:rgba(16,185,129,.08);color:#10b981;"><i class="fas fa-globe"></i></div>
              <h5 style="font-weight:700;color:#1e293b;">Global Presence</h5>
              <p style="font-size:13px;color:#64748b;margin:0;line-height:1.6;">Pune Corporate HQ, Bengaluru Center, and Wyoming USA International Subsidiary.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Company Locations & Global Delivery Centers -->
<section class="py-5" style="background:#f8fafc;">
  <div class="container">
    <div class="text-center mb-5 scroll-reveal">
      <div class="section-tag">Global Footprint</div>
      <h2 class="section-title">Our <span class="highlight">Global Locations &amp; Delivery Hubs</span></h2>
      <div class="section-divider"></div>
      <p style="color:#64748b;font-size:16px;max-width:650px;margin:0 auto;">Strategic multi-location presence ensuring seamless time-zone overlap, business continuity, and 24/7 global client support.</p>
    </div>

    <div class="row g-4">
      <!-- Pune HQ -->
      <div class="col-lg-4 scroll-reveal" style="transition-delay:.1s">
        <div class="hub-card primary-hub">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="badge" style="background:#CC2228;color:#fff;font-size:11px;padding:6px 12px;font-weight:700;">PRIMARY HEADQUARTERS</span>
            <span style="font-size:28px;">🇮🇳</span>
          </div>
          <h4 style="font-family:'Poppins',sans-serif;font-weight:800;color:#1C2280;margin-bottom:8px;">Pune Corporate HQ</h4>
          <p style="font-size:13.5px;color:#475569;margin-bottom:16px;line-height:1.6;">
            <strong>Address:</strong> 502, 4th Floor, Dangat Patil Empire, Vadgaon Budruk, Pune, Maharashtra 411041, India
          </p>
          <p style="font-size:13px;color:#64748b;margin-bottom:20px;line-height:1.6;">
            Primary executive headquarters housing core software R&amp;D, AI engineering, enterprise solution architecture, and strategic management operations.
          </p>
          <a href="https://share.google/XKt2SVYsKfiNqrVGx" target="_blank" class="btn btn-sm" style="background:#CC2228;color:#fff;font-weight:700;padding:8px 18px;border-radius:8px;">
            <i class="fas fa-map-marked-alt me-1"></i> Open Google Map
          </a>
        </div>
      </div>

      <!-- Bengaluru Office -->
      <div class="col-lg-4 scroll-reveal" style="transition-delay:.2s">
        <div class="hub-card">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="badge" style="background:#1C2280;color:#fff;font-size:11px;padding:6px 12px;font-weight:600;">BRANCH OFFICE</span>
            <span style="font-size:28px;">🇮🇳</span>
          </div>
          <h4 style="font-family:'Poppins',sans-serif;font-weight:800;color:#1e293b;margin-bottom:8px;">Bengaluru Branch Office</h4>
          <p style="font-size:13.5px;color:#475569;margin-bottom:16px;line-height:1.6;">
            <strong>Address:</strong> No.125, Ranganath Complex, Madiwala, HSR Layout, Bengaluru 560068, India
          </p>
          <p style="font-size:13px;color:#64748b;margin-bottom:20px;line-height:1.6;">
            Secondary delivery center housing Healthcare BPO, Publishing Prepress, Title &amp; Real Estate operations, and high-throughput data annotation teams.
          </p>
          <a href="https://www.google.com/maps/search/?api=1&query=Second+floor+No.125+Ranganath+Complex+Madiwala+HSR+Layout+Bengaluru+560068" target="_blank" class="btn btn-sm btn-outline-secondary" style="font-weight:600;padding:8px 18px;border-radius:8px;">
            <i class="fas fa-directions me-1"></i> Get Directions
          </a>
        </div>
      </div>

      <!-- USA Office -->
      <div class="col-lg-4 scroll-reveal" style="transition-delay:.3s">
        <div class="hub-card">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="badge" style="background:#0284c7;color:#fff;font-size:11px;padding:6px 12px;font-weight:600;">INTERNATIONAL ENTITY</span>
            <span style="font-size:28px;">🇺🇸</span>
          </div>
          <h4 style="font-family:'Poppins',sans-serif;font-weight:800;color:#1e293b;margin-bottom:8px;">USA Corporate Office</h4>
          <p style="font-size:13.5px;color:#475569;margin-bottom:16px;line-height:1.6;">
            <strong>Address:</strong> 30 N Gould St Ste 100, Sheridan, WY 82801, United States
          </p>
          <p style="font-size:13px;color:#64748b;margin-bottom:20px;line-height:1.6;">
            North American client liaison, account management, legal compliance, and contract execution node for US and global enterprise partners.
          </p>
          <a href="https://maps.google.com/?cid=4698826826648482061" target="_blank" class="btn btn-sm btn-outline-secondary" style="font-weight:600;padding:8px 18px;border-radius:8px;">
            <i class="fas fa-directions me-1"></i> Get Directions
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Vision & Mission -->
<section class="py-5" style="background:#f0f2ff;">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-6 scroll-reveal-left">
        <div style="background:#fff;border-radius:20px;padding:40px;height:100%;border:1px solid #e8ecff;">
          <div style="width:54px;height:54px;border-radius:14px;background:#1C2280;color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:20px;"><i class="fas fa-eye"></i></div>
          <h3 style="font-family:'Poppins',sans-serif;font-weight:800;color:#1C2280;margin-bottom:14px;">Our Vision</h3>
          <p style="color:#64748b;font-size:15px;line-height:1.8;margin:0;">
            To become the premier global outsourcing brand recognized for fusing cutting-edge artificial intelligence, intelligent document processing, and human domain expertise — defining new standards of operational quality, data security, and client ROI across 65+ industry verticals.
          </p>
        </div>
      </div>
      <div class="col-lg-6 scroll-reveal-right">
        <div style="background:#fff;border-radius:20px;padding:40px;height:100%;border:1px solid #e8ecff;">
          <div style="width:54px;height:54px;border-radius:14px;background:#CC2228;color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:20px;"><i class="fas fa-bullseye"></i></div>
          <h3 style="font-family:'Poppins',sans-serif;font-weight:800;color:#CC2228;margin-bottom:14px;">Our Mission</h3>
          <p style="color:#64748b;font-size:15px;line-height:1.8;margin:0;">
            To empower global businesses to scale rapidly and operate leaner by delivering flaw-free IT applications, automated business workflows, HIPAA-secure healthcare billing, high-precision AI training datasets, and 24/7 operational support tailored to every client's unique business goals.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 8 Core Strategic Pillars -->
<section class="py-5" style="background:#fff;">
  <div class="container">
    <div class="text-center mb-5 scroll-reveal">
      <div class="section-tag">Core Competencies</div>
      <h2 class="section-title">Our <span class="highlight">Key Operational Pillars</span></h2>
      <div class="section-divider"></div>
      <p style="color:#64748b;font-size:16px;max-width:650px;margin:0 auto;">Vortexsoft Group delivers end-to-end expertise across eight primary operational domains.</p>
    </div>

    <div class="row g-4">
      <div class="col-md-6 col-lg-3 scroll-reveal">
        <div class="pillar-card">
          <div style="font-size:28px;color:#CC2228;margin-bottom:14px;"><i class="fas fa-heartbeat"></i></div>
          <h5 style="font-weight:700;color:#1e293b;margin-bottom:8px;">Healthcare BPO &amp; RCM</h5>
          <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0;">HIPAA-compliant revenue cycle management, medical coding (ICD-10/CPT), billing, and denial recovery.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 scroll-reveal" style="transition-delay:.1s">
        <div class="pillar-card">
          <div style="font-size:28px;color:#1C2280;margin-bottom:14px;"><i class="fas fa-building"></i></div>
          <h5 style="font-weight:700;color:#1e293b;margin-bottom:8px;">Title &amp; Real Estate Services</h5>
          <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0;">Lease abstraction, CAM audits, property accounting, title search, and mortgage settlement typing.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 scroll-reveal" style="transition-delay:.2s">
        <div class="pillar-card">
          <div style="font-size:28px;color:#0284c7;margin-bottom:14px;"><i class="fas fa-book"></i></div>
          <h5 style="font-weight:700;color:#1e293b;margin-bottom:8px;">STM Publishing &amp; Prepress</h5>
          <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0;">Journal typesetting, eBook XML/ePUB3 conversions, WCAG accessibility tagging, and editorial production.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 scroll-reveal" style="transition-delay:.3s">
        <div class="pillar-card">
          <div style="font-size:28px;color:#10b981;margin-bottom:14px;"><i class="fas fa-robot"></i></div>
          <h5 style="font-weight:700;color:#1e293b;margin-bottom:8px;">AI Data Annotation</h5>
          <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0;">Image, video, text, audio, and 3D LiDAR labeling for Computer Vision and LLM machine learning datasets.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 scroll-reveal" style="transition-delay:.4s">
        <div class="pillar-card">
          <div style="font-size:28px;color:#8b5cf6;margin-bottom:14px;"><i class="fas fa-laptop-code"></i></div>
          <h5 style="font-weight:700;color:#1e293b;margin-bottom:8px;">Custom Software &amp; Portals</h5>
          <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0;">Full-stack web apps, enterprise CRM/HRMS, customer portals, internal management dashboards, and APIs.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 scroll-reveal" style="transition-delay:.5s">
        <div class="pillar-card">
          <div style="font-size:28px;color:#f59e0b;margin-bottom:14px;"><i class="fas fa-network-wired"></i></div>
          <h5 style="font-weight:700;color:#1e293b;margin-bottom:8px;">ERP &amp; SAP Solutions</h5>
          <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0;">ERP implementation, customization, SAP consulting, workflow automation, and enterprise app integration.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 scroll-reveal" style="transition-delay:.6s">
        <div class="pillar-card">
          <div style="font-size:28px;color:#ec4899;margin-bottom:14px;"><i class="fas fa-bullhorn"></i></div>
          <h5 style="font-weight:700;color:#1e293b;margin-bottom:8px;">Marketing Automation</h5>
          <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0;">Lead generation automation, email workflows, CRM automation, campaign tracking, and ROI dashboards.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 scroll-reveal" style="transition-delay:.7s">
        <div class="pillar-card">
          <div style="font-size:28px;color:#14b8a6;margin-bottom:14px;"><i class="fas fa-calculator"></i></div>
          <h5 style="font-weight:700;color:#1e293b;margin-bottom:8px;">Accounting &amp; Finance BPO</h5>
          <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0;">End-to-end bookkeeping, payroll processing, accounts payable/receivable, and financial audit support.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Company Journey Timeline -->
<section class="py-5" style="background:#f8fafc;">
  <div class="container">
    <div class="text-center mb-5 scroll-reveal">
      <div class="section-tag">Growth Story</div>
      <h2 class="section-title">Milestones &amp; <span class="highlight">Growth Journey</span></h2>
      <div class="section-divider"></div>
      <p style="color:#64748b;font-size:16px;max-width:600px;margin:0 auto;">Key milestones marking our evolution from a boutique consulting firm to a global IT &amp; BPO leader.</p>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-year">2020</div>
          <div class="timeline-title">Incorporation &amp; Foundation</div>
          <p style="color:#64748b;font-size:14px;line-height:1.7;">
            Vortexsoft Group was founded in Bengaluru, India, initially launching specialized Publishing &amp; Prepress conversion services alongside custom software engineering consulting for regional clients.
          </p>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-year">2021</div>
          <div class="timeline-title">Expansion into Healthcare &amp; Real Estate</div>
          <p style="color:#64748b;font-size:14px;line-height:1.7;">
            Scaled operations into HIPAA-compliant Healthcare BPO (Medical Billing &amp; Coding) and US Real Estate Services (Commercial Lease Abstraction &amp; CAM Reconciliation).
          </p>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-year">2022</div>
          <div class="timeline-title">ISO 27001 Certification &amp; USA Subsidiary Setup</div>
          <p style="color:#64748b;font-size:14px;line-height:1.7;">
            Achieved formal ISO 27001:2013 Information Security Management Certification and incorporated our US subsidiary entity in Sheridan, Wyoming to strengthen North American client relationships.
          </p>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-year">2023</div>
          <div class="timeline-title">AI Data Annotation &amp; Automation Center Launch</div>
          <p style="color:#64748b;font-size:14px;line-height:1.7;">
            Established a dedicated AI Data Annotation division providing Computer Vision bounding box, polygon, LiDAR, and NLP text dataset training for leading AI/ML research labs.
          </p>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-year">2024–2026</div>
          <div class="timeline-title">Pune Corporate HQ &amp; 150+ Global Clients</div>
          <p style="color:#64748b;font-size:14px;line-height:1.7;">
            Established our primary corporate headquarters in Pune, Maharashtra. Expanded our multidisciplinary workforce to 200+ professionals serving over 150 active enterprise clients across USA, UK, Australia, EU, and India.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action -->
<section class="py-5" style="background:linear-gradient(135deg,#080B1A,#1C2280);">
  <div class="container text-center text-white scroll-reveal">
    <h2 style="font-family:'Poppins',sans-serif;font-weight:800;margin-bottom:14px;">Ready to Partner with Vortexsoft Group?</h2>
    <p style="color:rgba(255,255,255,0.85);font-size:16px;max-width:620px;margin:0 auto 28px;">
      Discover how our hybrid AI, software, and BPO operations can help your organization scale faster with guaranteed quality and reduced overhead.
    </p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="contact.php" class="btn" style="background:#CC2228;color:#fff;font-weight:700;padding:14px 34px;border-radius:10px;font-size:15px;">
        <i class="fas fa-paper-plane me-2"></i> Contact Our Team
      </a>
      <a href="service.php" class="btn btn-outline-light" style="font-weight:600;padding:14px 34px;border-radius:10px;font-size:15px;">
        <i class="fas fa-th-list me-2"></i> Explore Services
      </a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
