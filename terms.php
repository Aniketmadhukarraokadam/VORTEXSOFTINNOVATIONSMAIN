<?php
/**
 * Vortexsoft Innovations — Terms of Service (terms.php)
 */

$page_title   = 'Terms of Service | Vortexsoft Group';
$page_desc    = 'Vortexsoft Group Terms of Service & Conditions. Learn about our service agreements, website terms of use, intellectual property, and compliance standards.';
$canonical_url = SITE_URL . '/terms.php';

$prefix       = './';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero" style="background:linear-gradient(135deg,#080B1A,#1C2280);padding:70px 0 50px;">
  <div class="container">
    <h1 style="color:#fff;font-weight:800;font-size:2.2rem;">Terms of Service</h1>
    <p style="color:rgba(255,255,255,.7);font-size:14px;margin-top:6px;">Last Updated: January 2026 | Vortexsoft Innovations Pvt. Ltd.</p>
  </div>
</div>

<section class="py-5" style="background:#fff;">
  <div class="container" style="max-width:860px;">
    <div style="font-size:15px;color:#475569;line-height:1.8;">
      <h3 style="color:#1C2280;font-weight:700;margin-bottom:12px;">1. Agreement to Terms</h3>
      <p>By accessing or using the website and services of Vortexsoft Innovations Pvt. Ltd. ("Vortexsoft Group"), you agree to be bound by these Terms of Service. If you do not agree to all terms and conditions, you must not access our website or utilize our services.</p>

      <h3 style="color:#1C2280;font-weight:700;margin-top:32px;margin-bottom:12px;">2. Services & Scope</h3>
      <p>Vortexsoft Group provides global IT, AI, software development, BPO, publishing, healthcare RCM, accounting, real estate title settlement, and workforce solutions. Detailed scope of work, deliverables, SLAs, and commercial terms are governed by specific Master Services Agreements (MSA) or Statements of Work (SOW) executed with individual clients.</p>

      <h3 style="color:#1C2280;font-weight:700;margin-top:32px;margin-bottom:12px;">3. Intellectual Property Rights</h3>
      <p>All materials on this website—including software code, graphics, logos, trademarks, content, and design elements—are the exclusive intellectual property of Vortexsoft Group or its licensors. Unauthorized reproduction, modification, or distribution of any site content is strictly prohibited.</p>

      <h3 style="color:#1C2280;font-weight:700;margin-top:32px;margin-bottom:12px;">4. User Conduct & Acceptable Use</h3>
      <p>When using our website, contact forms, or applicant portals, you agree not to:</p>
      <ul>
        <li>Submit false, fraudulent, or misleading information.</li>
        <li>Attempt unauthorized access to our servers, databases, or client systems.</li>
        <li>Transmit malicious code, viruses, or automated scraping scripts.</li>
        <li>Violate any applicable local, national, or international laws or regulations.</li>
      </ul>

      <h3 style="color:#1C2280;font-weight:700;margin-top:32px;margin-bottom:12px;">5. Data Protection & Compliance</h3>
      <p>Vortexsoft Group operates under ISO 27001:2013 information security standards and HIPAA-compliant data practices. Our handling of personal data is detailed in our <a href="<?= $prefix ?>privacy.php" style="color:#1C2280;font-weight:600;">Privacy Policy</a>.</p>

      <h3 style="color:#1C2280;font-weight:700;margin-top:32px;margin-bottom:12px;">6. Limitation of Liability</h3>
      <p>To the maximum extent permitted by law, Vortexsoft Group shall not be liable for indirect, incidental, punitive, or consequential damages resulting from website downtime, third-party network interruptions, or unauthorized access beyond our control.</p>

      <h3 style="color:#1C2280;font-weight:700;margin-top:32px;margin-bottom:12px;">7. Governing Law</h3>
      <p>These terms shall be governed by and construed in accordance with the laws of India. Any legal disputes arising out of these terms shall be subject to the exclusive jurisdiction of the courts in Pune / Bengaluru, India.</p>

      <h3 style="color:#1C2280;font-weight:700;margin-top:32px;margin-bottom:12px;">8. Contact Information</h3>
      <p>For any questions regarding these Terms of Service, please contact us at:</p>
      <div style="background:#f0f2ff;border-radius:12px;padding:20px;margin-top:16px;">
        <strong style="color:#1C2280;"><?= SITE_NAME ?></strong><br>
        Email: <a href="mailto:<?= EMAIL_SUPPORT ?>" style="color:#1C2280;"><?= EMAIL_SUPPORT ?></a><br>
        Phone: <?= PHONE_INDIA ?><br>
        Address: 502, 4th Floor, Dangat Patil Empire, Vadgaon Budruk, Pune, Maharashtra 411041
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
