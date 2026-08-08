<?php
/**
 * Vortexsoft Innovations — About Us Page (about.php)
 */

$page_title   = 'About Us — Vortexsoft Group | Leading Global IT & BPO Outsourcing Company';
$page_desc    = 'Learn about Vortexsoft Group, an ISO 27001 certified IT & BPO company delivering healthcare, publishing, real estate, IT, and AI solutions worldwide since 2020.';
$canonical_url = 'https://www.vortexsoftinnovations.com/about.php';
$prefix       = './';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.page-hero{background:linear-gradient(135deg,#080B1A 0%,#1C2280 55%,#0D1035 100%);padding:80px 0 70px;position:relative;overflow:hidden}
.page-hero::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:50px 50px}
.page-hero h1{font-size:clamp(2rem,4vw,3rem);font-weight:800;color:#fff}
.breadcrumb-item,.breadcrumb-item a{color:rgba(255,255,255,.6);font-size:14px}
.breadcrumb-item.active{color:rgba(255,255,255,.9)}
.breadcrumb-item+.breadcrumb-item::before{color:rgba(255,255,255,.4)}

.about-feature-box{background:#fff;border-radius:20px;padding:32px;border:1px solid #e8ecff;box-shadow:0 4px 20px rgba(28,34,128,.06);transition:all .3s;height:100%}
.about-feature-box:hover{transform:translateY(-6px);box-shadow:0 15px 40px rgba(28,34,128,.12);border-color:transparent}
.about-feature-icon{width:56px;height:56px;border-radius:14px;background:rgba(28,34,128,.08);color:#1C2280;display:flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:20px}

.timeline-item{position:relative;padding-left:36px;padding-bottom:32px;border-left:2px solid #e8ecff}
.timeline-item:last-child{border-left:2px solid transparent;padding-bottom:0}
.timeline-dot{position:absolute;left:-9px;top:0;width:16px;height:16px;border-radius:50%;background:#1C2280;border:3px solid #fff;box-shadow:0 0 0 2px #1C2280}
.timeline-year{font-size:12px;font-weight:800;letter-spacing:1px;color:#CC2228;text-transform:uppercase;margin-bottom:4px}
.timeline-title{font-size:16px;font-weight:700;color:#1e293b;margin-bottom:6px}
</style>

<!-- Hero -->
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">About Us</li></ol></nav>
    <h1>About <span style="color:#5BA8D4;">Vortexsoft Group</span></h1>
    <p style="color:rgba(255,255,255,.75);font-size:16px;margin-top:12px;max-width:600px;">Empowering global enterprises with smart, scalable IT, BPO, AI, and digital transformation solutions since 2020.</p>
  </div>
</div>

<!-- Main About Section -->
<section class="py-5" style="background:#fff;">
  <div class="container">
    <!-- GEO Citable Fact Block -->
    <?= render_geo_fact_block() ?>

    <div class="row align-items-center gy-5">
      <div class="col-lg-6 scroll-reveal-left">
        <div class="section-tag">Who We Are</div>
        <h2 class="section-title">Driving Excellence Through <span class="highlight">Innovation</span></h2>
        <div class="section-divider"></div>
        <p style="color:var(--text-muted);font-size:16px;line-height:1.8;">
          Vortexsoft Innovations Pvt. Ltd., a flagship company of the <strong>Vortexsoft Group</strong>, is an <strong>ISO 27001:2013 certified</strong> global IT and business process outsourcing firm headquartered in Bengaluru, Karnataka, India.
        </p>
        <p style="color:var(--text-muted);font-size:15px;line-height:1.8;">
          Since our inception in 2020, we have grown into a multi-disciplinary partner offering 65+ domain-specific services across Healthcare BPO, Publishing & Media Prepress, Real Estate & Title Settlement, IT Software Solutions, Data Annotation for AI/ML, Accounting & Finance, and Digital Marketing.
        </p>
        <div class="row g-3 mt-3">
          <div class="col-6">
            <div style="background:#f0f2ff;border-radius:14px;padding:16px;border:1px solid #dde2f5;">
              <h4 style="color:#1C2280;font-weight:800;margin:0;">150+</h4>
              <div style="font-size:13px;color:#64748b;font-weight:600;">Global Clients</div>
            </div>
          </div>
          <div class="col-6">
            <div style="background:#fff0f0;border-radius:14px;padding:16px;border:1px solid #ffd6d6;">
              <h4 style="color:#CC2228;font-weight:800;margin:0;">200+</h4>
              <div style="font-size:13px;color:#64748b;font-weight:600;">Projects Delivered</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 scroll-reveal-right">
        <div class="row g-4">
          <div class="col-6">
            <div class="about-feature-box">
              <div class="about-feature-icon"><i class="fas fa-shield-alt"></i></div>
              <h5 style="font-weight:700;color:#1e293b;">ISO 27001 Certified</h5>
              <p style="font-size:13px;color:#64748b;margin:0;">Strict information security protocols guarding enterprise data assets.</p>
            </div>
          </div>
          <div class="col-6">
            <div class="about-feature-box">
              <div class="about-feature-icon" style="background:rgba(204,34,40,.08);color:#CC2228;"><i class="fas fa-user-md"></i></div>
              <h5 style="font-weight:700;color:#1e293b;">HIPAA Compliant</h5>
              <p style="font-size:13px;color:#64748b;margin:0;">Full compliance for US healthcare billing, coding, and medical records.</p>
            </div>
          </div>
          <div class="col-6">
            <div class="about-feature-box">
              <div class="about-feature-icon" style="background:rgba(91,168,212,.08);color:#5BA8D4;"><i class="fas fa-award"></i></div>
              <h5 style="font-weight:700;color:#1e293b;">Startup India</h5>
              <p style="font-size:13px;color:#64748b;margin:0;">Recognized under Government of India Startup Initiative.</p>
            </div>
          </div>
          <div class="col-6">
            <div class="about-feature-box">
              <div class="about-feature-icon" style="background:rgba(16,185,129,.08);color:#10b981;"><i class="fas fa-globe"></i></div>
              <h5 style="font-weight:700;color:#1e293b;">Global Reach</h5>
              <p style="font-size:13px;color:#64748b;margin:0;">Offices in Bengaluru, Pune, and Sheridan, Wyoming, USA.</p>
            </div>
          </div>
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
          <div style="width:50px;height:50px;border-radius:12px;background:#1C2280;color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:20px;"><i class="fas fa-eye"></i></div>
          <h3 style="font-family:'Poppins',sans-serif;font-weight:800;color:#1C2280;margin-bottom:12px;">Our Vision</h3>
          <p style="color:#64748b;font-size:15px;line-height:1.8;margin:0;">To become the world's most trusted partner for IT, BPO, and AI solutions by continually redefining operational benchmarks, championing data security, and enabling client growth through technology.</p>
        </div>
      </div>
      <div class="col-lg-6 scroll-reveal-right">
        <div style="background:#fff;border-radius:20px;padding:40px;height:100%;border:1px solid #e8ecff;">
          <div style="width:50px;height:50px;border-radius:12px;background:#CC2228;color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:20px;"><i class="fas fa-bullseye"></i></div>
          <h3 style="font-family:'Poppins',sans-serif;font-weight:800;color:#CC2228;margin-bottom:12px;">Our Mission</h3>
          <p style="color:#64748b;font-size:15px;line-height:1.8;margin:0;">To deliver flawless execution, cost-effective outsourcing model, and AI-driven automation that helps businesses scale faster, operate leaner, and stay ahead of global competition.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Company Journey Timeline -->
<section class="py-5" style="background:#fff;">
  <div class="container">
    <div class="text-center mb-5 scroll-reveal">
      <div class="section-tag">Our History</div>
      <h2 class="section-title">Milestones &amp; <span class="highlight">Growth Journey</span></h2>
      <div class="section-divider"></div>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-year">2020</div>
          <div class="timeline-title">Incorporation &amp; Inception</div>
          <p style="color:#64748b;font-size:14px;">Vortexsoft Group was founded in Bengaluru, starting with Publishing Services and Software Consulting.</p>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-year">2021</div>
          <div class="timeline-title">Expansion into Healthcare &amp; Real Estate</div>
          <p style="color:#64748b;font-size:14px;">Expanded operations into Medical Coding/Billing and US Real Estate Lease Abstraction & CAM Audits.</p>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-year">2022</div>
          <div class="timeline-title">ISO 27001 Certification &amp; USA Entity</div>
          <p style="color:#64748b;font-size:14px;">Achieved ISO 27001:2013 certification and established US office in Sheridan, Wyoming to serve North America.</p>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-year">2023</div>
          <div class="timeline-title">Data Annotation &amp; AI Center Launch</div>
          <p style="color:#64748b;font-size:14px;">Launched dedicated AI Data Annotation division for Computer Vision, NLP, and LLM dataset training.</p>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-year">2024–2026</div>
          <div class="timeline-title">150+ Global Clients &amp; Pune Delivery Center</div>
          <p style="color:#64748b;font-size:14px;">Opened secondary delivery center in Pune and crossed 150+ active global clients with a team of 200+ professionals.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
