<?php
/**
 * Vortexsoft Innovations — 404 Error Page (404.php)
 */

http_response_code(404);
$page_title    = '404 – Page Not Found | Vortexsoft Group';
$page_desc     = 'The page you are looking for does not exist or has been moved.';
$canonical_url = 'https://www.vortexsoftinnovations.com/';
$prefix        = '/';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.err-num{font-family:'Poppins',sans-serif;font-size:clamp(5rem,15vw,9rem);font-weight:900;background:linear-gradient(135deg,#1C2280,#CC2228);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1;margin-bottom:16px;animation:pulse 2.5s ease-in-out infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.7}}
.quick-link{display:flex;align-items:center;gap:10px;padding:12px 18px;background:#fff;border-radius:12px;border:1.5px solid #e8ecff;font-size:13.5px;font-weight:600;color:#1C2280;text-decoration:none;transition:all .2s}
.quick-link:hover{border-color:#1C2280;background:#f0f2ff;color:#1C2280;transform:translateY(-2px)}
.quick-link i{color:#CC2228;width:16px}
</style>

<section class="py-5" style="background:linear-gradient(135deg,#f0f2ff 0%,#fff 100%);min-height:75vh;display:flex;align-items:center;">
  <div class="container text-center">
    <div class="err-num">404</div>
    <h1 style="font-family:'Poppins',sans-serif;font-weight:800;color:#1e293b;margin-bottom:10px;">Page Not Found</h1>
    <p style="color:#64748b;font-size:16px;max-width:500px;margin:0 auto 32px;">The page you're looking for might have been moved, renamed, or doesn't exist. Let us help you find what you need.</p>
    <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap">
      <a href="/index.php" class="btn" style="background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;border-radius:10px;padding:13px 28px;font-weight:700;"><i class="fas fa-home me-2"></i>Go to Homepage</a>
      <a href="/contact.php" class="btn" style="background:#fff;color:#1C2280;border:2px solid #1C2280;border-radius:10px;padding:13px 28px;font-weight:700;"><i class="fas fa-envelope me-2"></i>Contact Us</a>
    </div>

    <div style="max-width:620px;margin:0 auto;">
      <p style="font-size:13px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;">Popular Pages</p>
      <div class="row g-2">
        <div class="col-6 col-md-4"><a class="quick-link" href="/service.php"><i class="fas fa-th-large"></i>All Services</a></div>
        <div class="col-6 col-md-4"><a class="quick-link" href="/careers.php"><i class="fas fa-briefcase"></i>Careers</a></div>
        <div class="col-6 col-md-4"><a class="quick-link" href="/about.php"><i class="fas fa-building"></i>About Us</a></div>
        <div class="col-6 col-md-4"><a class="quick-link" href="/medical-billing-services/index.php"><i class="fas fa-file-medical"></i>Medical Billing</a></div>
        <div class="col-6 col-md-4"><a class="quick-link" href="/software-solutions/index.php"><i class="fas fa-laptop-code"></i>Software Solutions</a></div>
        <div class="col-6 col-md-4"><a class="quick-link" href="/data-annotation-services/index.php"><i class="fas fa-tags"></i>Data Annotation</a></div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
