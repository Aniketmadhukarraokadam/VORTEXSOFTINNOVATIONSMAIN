<?php
/**
 * Vortexsoft Innovations — 404 Error Page (404.php)
 */

http_response_code(404);
$page_title   = '404 - Page Not Found | Vortexsoft Group';
$page_desc    = 'The page you are looking for does not exist or has been moved.';
$canonical_url = 'https://www.vortexsoftinnovations.com/404.php';
$prefix       = '/';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-6" style="background:#f0f2ff;min-height:70vh;display:flex;align-items:center;">
  <div class="container text-center">
    <div style="font-family:'Poppins',sans-serif;font-size:6rem;font-weight:900;color:#1C2280;line-height:1;">404</div>
    <h2 style="font-family:'Poppins',sans-serif;font-weight:800;color:#1e293b;margin-top:12px;margin-bottom:12px;">Page Not Found</h2>
    <p style="color:#64748b;font-size:16px;max-width:480px;margin:0 auto 28px;">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
    <div class="d-flex justify-content-center gap-3">
      <a href="/index.php" class="btn" style="background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;border-radius:10px;padding:12px 28px;font-weight:700;"><i class="fas fa-home me-2"></i> Go to Homepage</a>
      <a href="/contact.php" class="btn" style="background:#fff;color:#1C2280;border:1.5px solid #1C2280;border-radius:10px;padding:12px 28px;font-weight:700;"><i class="fas fa-envelope me-2"></i> Contact Us</a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
