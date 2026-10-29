<?php
/**
 * Vortexsoft Innovations — Terms of Service (terms.php)
 * Master Terms & Enterprise Service Engagement Conditions
 */

$page_title   = 'Terms of Service | Master Service Agreement | Vortexsoft Innovations Private Limited';
$page_desc    = 'Vortexsoft Innovations Private Limited Terms of Service. Enterprise IT contracts, SLA standards, intellectual property, confidentiality, and governing jurisdiction.';
$canonical_url = 'https://www.vortexsoftinnovations.com/terms.php';

$prefix       = './';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.terms-hero {
  background: linear-gradient(135deg, #080B1A 0%, #1C2280 60%, #0D1035 100%);
  padding: 85px 0 65px;
  position: relative;
  overflow: hidden;
}
.terms-hero h1 {
  font-size: clamp(2.2rem, 4vw, 3rem);
  font-weight: 800;
  color: #ffffff !important;
  margin-bottom: 12px;
}
.terms-hero p {
  color: rgba(255, 255, 255, 0.92) !important;
  font-size: 15px;
  max-width: 650px;
  line-height: 1.6;
}
.terms-hero .breadcrumb a {
  color: rgba(255, 255, 255, 0.75) !important;
}
.terms-hero .breadcrumb-item.active {
  color: #5BA8D4 !important;
}

.terms-content-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 44px 40px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 10px 35px rgba(28, 34, 128, 0.05);
  line-height: 1.85;
}
.terms-content-card h2, .terms-content-card h3 {
  color: #0F172A !important;
  font-weight: 700;
  margin-top: 36px;
  margin-bottom: 14px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.terms-content-card h2:first-of-type, .terms-content-card h3:first-of-type {
  margin-top: 0;
}
.terms-content-card p, .terms-content-card li {
  color: #334155 !important;
  font-size: 15px;
}
.terms-content-card ul {
  padding-left: 20px;
  margin-bottom: 20px;
}
.terms-content-card li {
  margin-bottom: 8px;
}
.terms-badge-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #eef2ff;
  color: #1C2280;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  margin-bottom: 16px;
}
.terms-contact-box {
  background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);
  border: 1.5px solid #dbeafe;
  border-radius: 16px;
  padding: 24px;
  margin-top: 32px;
}
</style>

<!-- Hero Section -->
<div class="terms-hero">
  <div class="container position-relative" style="z-index:2;">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Terms of Service</li>
      </ol>
    </nav>
    <div class="terms-badge-pill">
      <i class="fas fa-file-contract text-primary"></i> Legal &amp; Commercial Terms
    </div>
    <h1>Terms of Service</h1>
    <p>
      Please review the terms and conditions governing the engagement of our IT software development, AI automations, BPO operations, and title abstracting services.
    </p>
    <div class="d-flex gap-3 align-items-center mt-3 text-white-50" style="font-size:13px;">
      <span><i class="far fa-calendar-alt me-1 text-white"></i> Last Updated: January 2026</span>
      <span>&bull;</span>
      <span><i class="fas fa-balance-scale me-1 text-white"></i> Governing Law: India &amp; International SOWs</span>
    </div>
  </div>
</div>

<!-- Main Terms Content -->
<section class="py-5" style="background:#f8fafc;">
  <div class="container" style="max-width:960px;">
    <div class="terms-content-card">

      <h3><i class="fas fa-handshake text-primary"></i> 1. Acceptance of Terms</h3>
      <p>
        These Terms of Service constitute a legally binding agreement between you (whether individually or on behalf of an entity, "Client", "User") and <strong>Vortexsoft Innovations Private Limited</strong> ("Vortexsoft", "we", "us", or "our"). By accessing our website, engaging our services, or submitting online project briefs, you confirm your acceptance of these Terms.
      </p>

      <h3><i class="fas fa-layer-group text-primary"></i> 2. Service Scope &amp; Statement of Work (SOW)</h3>
      <p>
        Vortexsoft provides global technology, AI development, BPO outsourcing, title abstracting, healthcare revenue cycle management (RCM), digital publishing, and accounting operations. Specific deliverables, project milestones, SLAs, hourly/fixed fee rates, and technical specifications are formally governed by project-specific <strong>Statements of Work (SOW)</strong> or <strong>Master Services Agreements (MSA)</strong> executed between the parties.
      </p>

      <h3><i class="fas fa-award text-primary"></i> 3. SLA Benchmarks &amp; Quality Commitments</h3>
      <p>
        We take pride in industry-leading quality benchmarks:
      </p>
      <ul>
        <li><strong>Title Search &amp; Abstracting:</strong> 24-to-48 hour standard turnaround with 99.8% title commitment accuracy across 50 US States.</li>
        <li><strong>Healthcare RCM &amp; Medical Coding:</strong> 98%+ first-pass clean claim rate with dedicated denial management resolution.</li>
        <li><strong>Software &amp; AI Engineering:</strong> Agile sprint delivery with continuous integration, automated unit testing, and guaranteed security patching.</li>
        <li><strong>Business Process Outsourcing:</strong> 24/7 omnichannel uptime and strict adherence to agreed-upon KPI thresholds.</li>
      </ul>

      <h3><i class="fas fa-copyright text-primary"></i> 4. Intellectual Property Rights</h3>
      <p>
        <strong>Client Deliverables:</strong> Upon full payment of all contracted fees, all custom software code, title reports, database annotations, and specialized deliverables developed exclusively for the Client transfer to the Client.
      </p>
      <p>
        <strong>Vortexsoft Proprietary Platforms:</strong> Proprietary platforms and frameworks (including <em>VortexEXHO</em>, <em>vortexHire</em>, <em>vortexKonnect</em>, <em>Vortexreach</em>, <em>vortexsoftpublishing</em>, and <em>vortexsofthrms</em>) remain the exclusive intellectual property of Vortexsoft Innovations Private Limited.
      </p>

      <h3><i class="fas fa-lock text-primary"></i> 5. Confidentiality &amp; Non-Disclosure</h3>
      <p>
        Both parties agree to hold all proprietary trade secrets, technical source code, financial information, customer records, and property data in strict confidence. Vortexsoft enforces bilateral NDAs across all personnel, and complies with <strong>ISO 27001:2013</strong> information security controls and <strong>HIPAA</strong> privacy mandates.
      </p>

      <h3><i class="fas fa-shield-virus text-primary"></i> 6. User Conduct &amp; Prohibited Uses</h3>
      <p>When interacting with our systems, websites, or candidate application forms, you agree NOT to:</p>
      <ul>
        <li>Upload malicious payloads, ransomware, trojans, or automated vulnerability scraping bots.</li>
        <li>Attempt unauthorized administrative access to our cloud environments or client portals.</li>
        <li>Submit fraudulent employment applications, fake credentials, or fabricated identity documents.</li>
        <li>Reverse engineer or decompile any of our proprietary AI or software platforms.</li>
      </ul>

      <h3><i class="fas fa-gavel text-primary"></i> 7. Limitation of Liability &amp; Dispute Resolution</h3>
      <p>
        In no event shall Vortexsoft Innovations Private Limited be liable for indirect, punitive, incidental, or consequential damages. Any legal controversy arising under these Terms shall be resolved via mutual good-faith arbitration, subject to the jurisdiction of the courts of Pune / Bengaluru, India, or as explicitly designated in an international client MSA.
      </p>

      <h3><i class="fas fa-comments text-primary"></i> 8. Inquiries &amp; Legal Notices</h3>
      <p>
        All formal legal notices, contract inquiries, or partnership proposals should be addressed in writing to:
      </p>

      <div class="terms-contact-box">
        <h4 style="font-size:16px;font-weight:800;color:#1C2280;margin-bottom:12px;">
          <i class="fas fa-balance-scale me-2"></i> Legal &amp; Compliance Department
        </h4>
        <div style="font-size:14px;color:#334155;line-height:1.75;">
          <strong>Entity:</strong> Vortexsoft Innovations Private Limited<br>
          <strong>Corporate Legal Desk:</strong> Vortexsoft Innovations Legal &amp; Compliance Division<br>
          <strong>Email:</strong> <a href="mailto:support@vortexsoftinnovations.com" class="text-primary fw-bold">support@vortexsoftinnovations.com</a><br>
          <strong>Phone:</strong> <a href="tel:+918308906690" class="text-dark fw-bold"><?= PHONE_INDIA ?></a><br>
          <strong>Registered Office:</strong> 502, 4th Floor, Dangat Patil Empire, Vadgaon Budruk, Pune, Maharashtra 411041, India
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
