<?php
/**
 * Vortexsoft Innovations — Privacy Policy (privacy.php)
 * ISO 27001:2013 & HIPAA Compliant Privacy Documentation
 */

$page_title   = 'Privacy Policy | Data Protection & Security | Vortexsoft Innovations Private Limited';
$page_desc    = 'Vortexsoft Innovations Private Limited Privacy Policy. Comprehensive data protection, ISO 27001:2013 information security standards, and HIPAA compliance protocols.';
$canonical_url = 'https://www.vortexsoftinnovations.com/privacy.php';

$prefix       = './';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.privacy-hero {
  background: linear-gradient(135deg, #080B1A 0%, #1C2280 60%, #0D1035 100%);
  padding: 85px 0 65px;
  position: relative;
  overflow: hidden;
}
.privacy-hero h1 {
  font-size: clamp(2.2rem, 4vw, 3rem);
  font-weight: 800;
  color: #ffffff !important;
  margin-bottom: 12px;
}
.privacy-hero p {
  color: rgba(255, 255, 255, 0.92) !important;
  font-size: 15px;
  max-width: 650px;
  line-height: 1.6;
}
.privacy-hero .breadcrumb a {
  color: rgba(255, 255, 255, 0.75) !important;
}
.privacy-hero .breadcrumb-item.active {
  color: #5BA8D4 !important;
}

.privacy-content-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 44px 40px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 10px 35px rgba(28, 34, 128, 0.05);
  line-height: 1.85;
}
.privacy-content-card h2, .privacy-content-card h3 {
  color: #0F172A !important;
  font-weight: 700;
  margin-top: 36px;
  margin-bottom: 14px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.privacy-content-card h2:first-of-type, .privacy-content-card h3:first-of-type {
  margin-top: 0;
}
.privacy-content-card p, .privacy-content-card li {
  color: #334155 !important;
  font-size: 15px;
}
.privacy-content-card ul {
  padding-left: 20px;
  margin-bottom: 20px;
}
.privacy-content-card li {
  margin-bottom: 8px;
}
.privacy-badge-pill {
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
.privacy-contact-box {
  background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);
  border: 1.5px solid #dbeafe;
  border-radius: 16px;
  padding: 24px;
  margin-top: 32px;
}
</style>

<!-- Hero Section -->
<div class="privacy-hero">
  <div class="container position-relative" style="z-index:2;">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Privacy Policy</li>
      </ol>
    </nav>
    <div class="privacy-badge-pill">
      <i class="fas fa-shield-alt text-primary"></i> ISO 27001:2013 &amp; HIPAA Compliant
    </div>
    <h1>Privacy Policy</h1>
    <p>
      At Vortexsoft Innovations Private Limited, your privacy, confidentiality, and data protection are foundational to our enterprise IT and BPO operations.
    </p>
    <div class="d-flex gap-3 align-items-center mt-3 text-white-50" style="font-size:13px;">
      <span><i class="far fa-calendar-alt me-1 text-white"></i> Last Updated: January 2026</span>
      <span>&bull;</span>
      <span><i class="fas fa-lock me-1 text-white"></i> 256-bit SSL Encrypted</span>
    </div>
  </div>
</div>

<!-- Main Privacy Content -->
<section class="py-5" style="background:#f8fafc;">
  <div class="container" style="max-width:960px;">
    <div class="privacy-content-card">

      <h3><i class="fas fa-info-circle text-primary"></i> 1. Introduction &amp; Scope</h3>
      <p>
        This Privacy Policy outlines how <strong>Vortexsoft Innovations Private Limited</strong> ("Vortexsoft", "we", "our", or "us"), headquartered at 502, 4th Floor, Dangat Patil Empire, Vadgaon Budruk, Pune 411041, with delivery operations in Bengaluru and Sheridan, Wyoming (USA), collects, processes, stores, and safeguards personal and client business information.
      </p>
      <p>
        This policy applies to all visitors of our website (<a href="https://www.vortexsoftinnovations.com" class="text-primary fw-bold">vortexsoftinnovations.com</a>), prospective candidates submitting job applications, and enterprise clients utilizing our IT, BPO, Title Abstracting, Healthcare RCM, AI, and Software services.
      </p>

      <h3><i class="fas fa-database text-primary"></i> 2. Information We Collect</h3>
      <p>We collect information strictly necessary to provide and manage our services:</p>
      <ul>
        <li><strong>Client Inquiry Information:</strong> Full name, professional email address, phone number, company name, jurisdiction, and project requirement details submitted via contact forms.</li>
        <li><strong>Job Applicant Data:</strong> Full name, personal contact information, resumes/CVs, employment history, portfolios, and job preferences submitted via our Careers portal.</li>
        <li><strong>Title &amp; Settlement Client Data:</strong> Specific property identifiers, parcel IDs, public records, and mortgage documentation provided solely for executing title searches and commitments.</li>
        <li><strong>Technical &amp; Telemetry Data:</strong> IP address, device type, browser specifications, referrer URL, and interaction timestamps captured for DDoS mitigation, rate limiting, and analytics.</li>
      </ul>

      <h3><i class="fas fa-cogs text-primary"></i> 3. How We Use Collected Data</h3>
      <p>We process collected data exclusively for authorized business operations:</p>
      <ul>
        <li>To review business inquiries, draft Master Services Agreements (MSAs), and deliver customized technical proposals.</li>
        <li>To execute contractually defined IT, AI automation, BPO operations, and title abstracting services.</li>
        <li>To process employment applications, conduct candidate screening, and coordinate interviews through our HR portal.</li>
        <li>To maintain security audits, verify compliance under ISO 27001:2013, and prevent fraud or cyber threats.</li>
        <li>We never sell, rent, monetize, or trade client or candidate data to third-party brokers or advertisers.</li>
      </ul>

      <h3><i class="fas fa-shield-alt text-primary"></i> 4. Enterprise Data Security &amp; ISO 27001 Compliance</h3>
      <p>
        Vortexsoft Innovations maintains rigorous technical and organizational safeguards certified under <strong>ISO 27001:2013 Information Security Management</strong> standards and follows <strong>HIPAA</strong> security and privacy rules for healthcare data:
      </p>
      <ul>
        <li><strong>End-to-End Encryption:</strong> All data in transit is protected using TLS 1.3 cryptographic protocols; data at rest is secured using AES-256 encryption.</li>
        <li><strong>Access Control:</strong> Role-Based Access Control (RBAC), multi-factor authentication (MFA), and zero-trust perimeter network segmentation.</li>
        <li><strong>Non-Disclosure Agreements (NDAs):</strong> Every employee, contractor, and engineer is legally bound by bilateral confidentiality agreements.</li>
        <li><strong>Secure Clean-Room Environments:</strong> Dedicated physical and virtual pods for title abstracting, healthcare RCM, and financial processing with strict disablement of unauthorized removable media.</li>
      </ul>

      <h3><i class="fas fa-user-shield text-primary"></i> 5. Data Retention &amp; User Rights</h3>
      <p>
        In accordance with global privacy frameworks (including GDPR, CCPA, and the Digital Personal Data Protection Act), you have the right to request access to, rectification of, or permanent deletion of your personal information stored in our records.
      </p>
      <p>
        Job applicant resumes are stored securely for recruitment evaluation and are purged upon candidate request or after statutory hiring cycles.
      </p>

      <h3><i class="fas fa-envelope-open-text text-primary"></i> 6. Data Protection Officer &amp; Contact</h3>
      <p>
        If you have questions regarding this Privacy Policy, wish to exercise your data subject rights, or require an executed bilateral NDA before sharing requirements, please reach out to our compliance desk:
      </p>

      <div class="privacy-contact-box">
        <h4 style="font-size:16px;font-weight:800;color:#1C2280;margin-bottom:12px;">
          <i class="fas fa-building me-2"></i> Vortexsoft Innovations Private Limited
        </h4>
        <div style="font-size:14px;color:#334155;line-height:1.75;">
          <strong>Compliance &amp; Data Protection Officer:</strong> Aniket Madhukarrao Kadam<br>
          <strong>Email:</strong> <a href="mailto:support@vortexsoftinnovations.com" class="text-primary fw-bold">support@vortexsoftinnovations.com</a> / <a href="mailto:info@vortexsoftinnovations.com" class="text-primary fw-bold">info@vortexsoftinnovations.com</a><br>
          <strong>Corporate Phone:</strong> <a href="tel:+918308906690" class="text-dark fw-bold"><?= PHONE_INDIA ?></a><br>
          <strong>Pune HQ:</strong> 502, 4th Floor, Dangat Patil Empire, Vadgaon Budruk, Pune, Maharashtra 411041<br>
          <strong>Bengaluru Delivery Hub:</strong> No.125, Ranganath Complex, Madiwala, HSR Layout, Bengaluru 560068<br>
          <strong>USA Office:</strong> 30 N Gould St Ste 100, Sheridan, WY 82801, USA
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
