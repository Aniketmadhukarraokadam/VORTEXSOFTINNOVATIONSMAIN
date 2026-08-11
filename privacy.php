<?php
/**
 * Vortexsoft Innovations — Privacy Policy (privacy.php)
 */

$page_title   = 'Privacy Policy | Vortexsoft Group';
$page_desc    = 'Vortexsoft Group Privacy Policy. Learn how we collect, process, and protect client data under ISO 27001 & HIPAA compliance standards.';
$canonical_url = 'https://www.vortexsoftinnovations.com/privacy.php';

$prefix       = './';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero" style="background:linear-gradient(135deg,#080B1A,#1C2280);padding:70px 0 50px;">
  <div class="container">
    <h1 style="color:#fff;font-weight:800;font-size:2.2rem;">Privacy Policy</h1>
    <p style="color:rgba(255,255,255,.7);font-size:14px;margin-top:6px;">Last Updated: January 2026 | ISO 27001:2013 Compliant</p>
  </div>
</div>

<section class="py-5" style="background:#fff;">
  <div class="container" style="max-width:860px;">
    <div style="font-size:15px;color:#475569;line-height:1.8;">
      <h3 style="color:#1C2280;font-weight:700;margin-bottom:12px;">1. Information We Collect</h3>
      <p>Vortexsoft Innovations Pvt. Ltd. ("Vortexsoft Group") collects minimal information necessary to deliver quality services. This includes:</p>
      <ul>
        <li>Contact information submitted via forms (Name, Email, Phone, Company name, Service requirement).</li>
        <li>Job application data (Name, Resume files, Experience, Contact details).</li>
        <li>Technical data automatically logged (IP address, Browser user agent, Page referrer) for security and rate-limiting.</li>
      </ul>

      <h3 style="color:#1C2280;font-weight:700;margin-top:32px;margin-bottom:12px;">2. How We Use Your Information</h3>
      <p>We use collected data solely for legitimate business purposes:</p>
      <ul>
        <li>To respond to user inquiries and send customized service proposals.</li>
        <li>To process job applications and contact candidates.</li>
        <li>To send periodic newsletters or product updates (only if opted in).</li>
        <li>To protect our website infrastructure against automated spam or malicious attacks.</li>
      </ul>

      <h3 style="color:#1C2280;font-weight:700;margin-top:32px;margin-bottom:12px;">3. Data Security &amp; ISO 27001</h3>
      <p>As an ISO 27001:2013 certified company, Vortexsoft Group employs enterprise-grade data protection mechanisms. Client data, resumes, and communications are stored on encrypted, access-restricted servers. We never sell or share user data with third-party advertisers.</p>

      <h3 style="color:#1C2280;font-weight:700;margin-top:32px;margin-bottom:12px;">4. Contact Us Regarding Privacy</h3>
      <p>If you have questions regarding our privacy practices or wish to request data removal, please contact our Data Protection Officer at:</p>
      <div style="background:#f0f2ff;border-radius:12px;padding:20px;margin-top:16px;">
        <strong style="color:#1C2280;"><?= SITE_NAME ?></strong><br>
        Email: <a href="mailto:<?= EMAIL_SUPPORT ?>" style="color:#1C2280;"><?= EMAIL_SUPPORT ?></a><br>
        Phone: <?= PHONE_INDIA ?><br>
        Address: No.125, Ranganath Complex, Madiwala, HSR Layout, Bengaluru 560068
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
