<?php
/**
 * Vortexsoft Innovations — PHP Footer Partial
 * Replaces assets/partials/footer.js + footer.html
 */
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config/constants.php';
}
$prefix = $prefix ?? './';
?>

<!-- ═══════ CTA BANNER ═══════ -->
<div class="cta-banner scroll-reveal">
  <div class="container text-center position-relative" style="z-index:2">
    <div class="scroll-reveal">
      <div class="section-tag mb-4">Start Today</div>
      <h2>Ready to Transform Your Business?</h2>
      <p>Let's build innovative solutions that drive real results. Contact us today for a free consultation.</p>
      <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="<?= $prefix ?>contact.php" class="btn-cta-white magnetic">
          <i class="fas fa-paper-plane"></i> Start a Project
        </a>
        <a href="tel:<?= str_replace(['-',' '], '', PHONE_INDIA) ?>" class="btn-hero-secondary" style="border-color:rgba(255,255,255,.3);">
          <i class="fas fa-phone-alt"></i> <?= PHONE_INDIA ?>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- ═══════ TRUST MARQUEE ═══════ -->
<style>
.trust-marquee-wrapper{background:var(--dark,#080B1A);border-top:1px solid rgba(255,255,255,0.05);border-bottom:1px solid rgba(255,255,255,0.05);padding:24px 0;overflow:hidden;position:relative;display:flex}
.trust-marquee-wrapper::before,.trust-marquee-wrapper::after{content:"";position:absolute;top:0;bottom:0;width:120px;z-index:2;pointer-events:none}
.trust-marquee-wrapper::before{left:0;background:linear-gradient(to right,#080B1A,transparent)}
.trust-marquee-wrapper::after{right:0;background:linear-gradient(to left,#080B1A,transparent)}
.trust-marquee{display:flex;gap:80px;animation:slide-marquee 40s linear infinite;white-space:nowrap;align-items:center;width:max-content}
.trust-marquee:hover{animation-play-state:paused}
@keyframes slide-marquee{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
.trust-item{display:inline-flex;align-items:center;gap:14px;color:rgba(255,255,255,0.8);font-family:'Poppins',sans-serif;font-size:16px;font-weight:700;letter-spacing:0.5px;transition:transform .3s,color .3s}
.trust-item:hover{color:#fff;transform:scale(1.05)}
.trust-icon-box{width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,0.15);box-shadow:0 4px 12px rgba(0,0,0,0.2)}
.trust-icon-box i{color:#CC2228;font-size:22px}
.trust-icon-box.cyan i{color:#5BA8D4}
.trust-icon-box.gold i{color:#f59e0b}
.trust-icon-box.green i{color:#10b981}
</style>
<div class="trust-marquee-wrapper">
  <div class="trust-marquee">
    <div class="trust-item"><div class="trust-icon-box cyan"><i class="fas fa-shield-alt"></i></div> ISO 27001 Certified</div>
    <div class="trust-item"><div class="trust-icon-box gold"><i class="fas fa-medal"></i></div> ISO 9001:2015 Quality</div>
    <div class="trust-item"><div class="trust-icon-box"><i class="fas fa-user-md"></i></div> HIPAA Compliant</div>
    <div class="trust-item"><div class="trust-icon-box green"><i class="fas fa-server"></i></div> Dedicated Secure Servers</div>
    <div class="trust-item"><div class="trust-icon-box cyan"><i class="fas fa-globe"></i></div> 150+ Global Clients</div>
    <div class="trust-item"><div class="trust-icon-box gold"><i class="fas fa-project-diagram"></i></div> 200+ Projects Delivered</div>
    <div class="trust-item"><div class="trust-icon-box"><i class="fas fa-users"></i></div> 200+ Professionals</div>
    <div class="trust-item"><div class="trust-icon-box green"><i class="fas fa-award"></i></div> Startup India Registered</div>
    <!-- Duplicate for infinite effect -->
    <div class="trust-item"><div class="trust-icon-box cyan"><i class="fas fa-shield-alt"></i></div> ISO 27001 Certified</div>
    <div class="trust-item"><div class="trust-icon-box gold"><i class="fas fa-medal"></i></div> ISO 9001:2015 Quality</div>
    <div class="trust-item"><div class="trust-icon-box"><i class="fas fa-user-md"></i></div> HIPAA Compliant</div>
    <div class="trust-item"><div class="trust-icon-box green"><i class="fas fa-server"></i></div> Dedicated Secure Servers</div>
    <div class="trust-item"><div class="trust-icon-box cyan"><i class="fas fa-globe"></i></div> 150+ Global Clients</div>
    <div class="trust-item"><div class="trust-icon-box gold"><i class="fas fa-project-diagram"></i></div> 200+ Projects Delivered</div>
    <div class="trust-item"><div class="trust-icon-box"><i class="fas fa-users"></i></div> 200+ Professionals</div>
    <div class="trust-item"><div class="trust-icon-box green"><i class="fas fa-award"></i></div> Startup India Registered</div>
  </div>
</div>

<!-- ═══════ MAIN FOOTER ═══════ -->
<footer class="footer">
  <div class="container">
    <div class="row gy-5">

      <!-- Brand Column -->
      <div class="col-lg-3 col-md-6 scroll-reveal">
        <a href="<?= $prefix ?>index.php">
          <img src="<?= $prefix ?>logo-footer-new.png" alt="Vortexsoft Innovations" class="footer-logo">
        </a>
        <p class="footer-desc">
          Vortexsoft Innovations Pvt. Ltd. is a proud member of the
          <strong style="color:rgba(255,255,255,0.85);">Vortexsoft Group</strong>
          — your trusted global partner for IT and non-IT solutions. ISO 27001 Certified.
        </p>
        <div class="footer-social">
          <a href="<?= SOCIAL_FACEBOOK ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="<?= SOCIAL_LINKEDIN ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="col-lg-2 col-md-6 scroll-reveal" style="transition-delay:0.1s">
        <h6 class="footer-title">Quick Links</h6>
        <ul class="footer-links">
          <li><a href="<?= $prefix ?>index.php">Home</a></li>
          <li><a href="<?= $prefix ?>about.php">About Us</a></li>
          <li><a href="<?= $prefix ?>service.php">Services</a></li>
          <li><a href="<?= $prefix ?>careers.php">Careers</a></li>
          <li><a href="<?= $prefix ?>blog.php">Blog</a></li>
          <li><a href="<?= $prefix ?>index.php#faq">FAQ</a></li>
          <li><a href="<?= $prefix ?>privacy.php">Privacy Policy</a></li>
          <li><a href="<?= $prefix ?>contact.php">Contact</a></li>
        </ul>
      </div>

      <!-- Our Services -->
      <div class="col-lg-3 col-md-6 scroll-reveal" style="transition-delay:0.2s">
        <h6 class="footer-title">Our Services</h6>
        <ul class="footer-links">
          <li><a href="<?= $prefix ?>software-solutions/index.php">Software Solutions</a></li>
          <li><a href="<?= $prefix ?>publishing-services/index.php">Publishing Services</a></li>
          <li><a href="<?= $prefix ?>health-care-services/index.php">Health Care Services</a></li>
          <li><a href="<?= $prefix ?>real-estate-services/index.php">Real Estate Services</a></li>
          <li><a href="<?= $prefix ?>accounting-services/index.php">Accounting Services</a></li>
          <li><a href="<?= $prefix ?>manpower-payroll-services/index.php">Manpower &amp; Payroll</a></li>
          <li><a href="<?= $prefix ?>digital-marketing-service/index.php">Digital Marketing</a></li>
          <li><a href="<?= $prefix ?>data-annotation-services/index.php">Data Annotation</a></li>
          <li><a href="<?= $prefix ?>title-settlement/index.php">Title &amp; Settlement</a></li>
        </ul>


      </div>

      <!-- Contact Info -->
      <div class="col-lg-4 col-md-6 scroll-reveal" style="transition-delay:0.3s">
        <h6 class="footer-title">Headquarters &amp; Offices</h6>
        <!-- Highlighted Pune HQ -->
        <div class="footer-contact-item" style="background:rgba(204,34,40,0.12);padding:10px 14px;border-radius:10px;border:1px solid rgba(204,34,40,0.3);margin-bottom:12px;">
          <i class="fas fa-building" style="color:#ff6b6b;flex-shrink:0;margin-top:3px;font-size:16px;"></i>
          <span>
            <a href="https://share.google/XKt2SVYsKfiNqrVGx" target="_blank" style="color:#fff;font-weight:800;display:block;text-decoration:none;font-size:14px;">
              <span class="badge me-1" style="background:#CC2228;color:#fff;font-size:10px;padding:3px 7px;vertical-align:middle;">HEAD OFFICE</span> Pune Headquarters <i class="fas fa-external-link-alt" style="font-size:10px;opacity:0.8;"></i>
            </a>
            502, 4th Floor, Dangat Patil Empire, Vadgaon Budruk, Pune, Maharashtra 411041
          </span>
        </div>
        <!-- Secondary Bengaluru Branch -->
        <div class="footer-contact-item" style="margin-bottom:10px;">
          <i class="fas fa-map-marker-alt" style="color:rgba(255,255,255,0.6);flex-shrink:0;margin-top:3px;"></i>
          <span>
            <a href="https://www.google.com/maps/search/?api=1&query=Second+floor+No.125+Ranganath+Complex+Madiwala+HSR+Layout+Bengaluru+560068" target="_blank" style="color:rgba(255,255,255,0.85);font-weight:600;display:block;text-decoration:none;font-size:13px;">Bengaluru Branch Office <i class="fas fa-external-link-alt" style="font-size:9px;opacity:0.5;"></i></a>
            No.125, Ranganath Complex, Madiwala, HSR Layout, Bengaluru 560068
          </span>
        </div>
        <!-- USA Office -->
        <div class="footer-contact-item" style="margin-bottom:10px;">
          <i class="fas fa-globe-americas" style="color:rgba(255,255,255,0.6);flex-shrink:0;margin-top:3px;"></i>
          <span>
            <a href="https://maps.google.com/?cid=4698826826648482061" target="_blank" style="color:rgba(255,255,255,0.85);font-weight:600;display:block;text-decoration:none;font-size:13px;">USA Office <i class="fas fa-external-link-alt" style="font-size:9px;opacity:0.5;"></i></a>
            30 N Gould St Ste 100, Sheridan, WY 82801, USA
          </span>
        </div>
        <div class="footer-contact-item mt-2">
          <i class="fas fa-envelope" style="color:var(--accent);"></i>
          <span>
            <a href="mailto:support@vortexsoftinnovations.com" style="color:#fff;font-weight:600;">support@vortexsoftinnovations.com</a>
          </span>
        </div>
        <div class="footer-contact-item">
          <i class="fas fa-phone-alt" style="color:var(--accent);"></i>
          <span>
            <a href="tel:+918308906690" style="color:#fff;font-weight:600;"><?= PHONE_INDIA ?></a>
          </span>
        </div>
        <div class="footer-contact-item">
          <i class="fas fa-clock" style="color:#10b981;"></i>
          <span>
            <strong style="color:#10b981;">24/7 Digital Inquiries &amp; Support</strong><br>
            <small style="color:rgba(255,255,255,0.6);">Physical Office: Mon–Sat, 9 AM – 6 PM IST</small>
          </span>
        </div>


        <!-- Newsletter Form -->
        <div class="mt-4">
          <h6 class="footer-title" style="border:none;padding:0;margin-bottom:12px;">Stay Updated</h6>
          <form id="newsletter-form" class="d-flex gap-2" onsubmit="submitNewsletter(event)">
            <input type="email" name="email" id="nl-email" class="form-control" placeholder="Your email address" required style="background:rgba(255,255,255,0.07);border-color:rgba(255,255,255,0.15);color:#fff;border-radius:8px;">
            <button type="submit" class="btn" style="background:var(--accent);color:#fff;border-radius:8px;padding:8px 16px;white-space:nowrap;font-size:13px;font-weight:600;">Subscribe</button>
          </form>
          <div id="nl-msg" class="mt-2" style="font-size:12px;display:none;"></div>
        </div>
      </div>

    </div><!-- /.row -->
  </div><!-- /.container -->

  <div class="footer-bottom">
    <div class="container">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <p>Copyright &copy; <?= date('Y') ?> Vortexsoft Group. Vortexsoft Innovations Pvt. Ltd. All rights reserved.</p>
        <p style="margin:0;display:flex;gap:16px;">
          <a href="<?= $prefix ?>index.php#faq" style="color:rgba(255,255,255,.5);font-size:13px;text-decoration:none;">FAQ</a>
          <a href="<?= $prefix ?>privacy.php" style="color:rgba(255,255,255,.5);font-size:13px;text-decoration:none;">Privacy Policy</a>
        </p>
      </div>
    </div>
  </div>
</footer>

<!-- ═══════ FLOATING WIDGETS ═══════ -->
<a href="<?= SOCIAL_WHATSAPP ?>" target="_blank" rel="noopener" class="whatsapp-btn" aria-label="Chat on WhatsApp">
  <i class="fab fa-whatsapp"></i>
</a>
<button id="scrollTop" aria-label="Scroll to top" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <i class="fas fa-chevron-up"></i>
</button>

<!-- Contact Success Modal -->
<div class="modal fade" id="contactSuccessModal" tabindex="-1" aria-labelledby="contactSuccessLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:20px;border:none;overflow:hidden;">
      <div style="background:linear-gradient(135deg,#1C2280,#5BA8D4);padding:40px 30px;text-align:center;">
        <div style="width:72px;height:72px;background:rgba(255,255,255,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:#fff;"><i class="fas fa-check-circle"></i></div>
        <h4 style="color:#fff;margin-bottom:10px;">Message Sent Successfully!</h4>
        <p style="color:rgba(255,255,255,0.8);margin:0;font-size:15px;">Thank you for reaching out. Our team will get back to you within 24 hours.</p>
      </div>
      <div class="modal-body text-center p-4">
        <p style="color:#64748b;margin-bottom:20px;">Meanwhile, feel free to call us directly:</p>
        <a href="tel:+918308906690" class="btn" style="background:var(--gradient-primary,linear-gradient(135deg,#1C2280,#5BA8D4));color:#fff;border-radius:10px;padding:12px 28px;font-weight:600;margin-right:10px;"><?= PHONE_INDIA ?></a>
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:10px;padding:12px 28px;">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Application Success Modal -->
<div class="modal fade" id="applySuccessModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:20px;border:none;overflow:hidden;">
      <div style="background:linear-gradient(135deg,#1C2280,#CC2228);padding:40px 30px;text-align:center;">
        <div style="width:72px;height:72px;background:rgba(255,255,255,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:#fff;"><i class="fas fa-briefcase"></i></div>
        <h4 style="color:#fff;margin-bottom:10px;">Application Submitted!</h4>
        <p style="color:rgba(255,255,255,0.8);margin:0;font-size:15px;">We have received your application. Our HR team will review it and contact you soon.</p>
      </div>
      <div class="modal-body text-center p-4">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:10px;padding:12px 28px;">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="<?= $prefix ?>assets/vendor/bootstrap.bundle.min.js"></script>
<!-- Shared JS -->
<script src="<?= $prefix ?>assets/vortex-shared.js?v=20260810"></script>
<script>
// ── Loader ──────────────────────────────────────────────────
(function(){
  var loader = document.getElementById('page-loader');
  if (!loader) return;
  var pct = document.querySelector('.loading-pct') || document.getElementById('loader-pct');
  if (pct) pct.textContent = '100%';
  setTimeout(function(){ loader.classList.add('hide'); }, 50);
  setTimeout(function(){ if (loader && loader.parentNode) loader.parentNode.removeChild(loader); }, 350);
})();
window.addEventListener('load', function(){
  var loader = document.getElementById('page-loader');
  if (loader) {
    loader.classList.add('hide');
    setTimeout(function(){ if (loader && loader.parentNode) loader.parentNode.removeChild(loader); }, 300);
  }
});

// ── Newsletter Submission ───────────────────────────────────
function submitNewsletter(e) {
  e.preventDefault();
  var email = document.getElementById('nl-email').value.trim();
  var msg = document.getElementById('nl-msg');
  fetch('<?= $prefix ?>api/newsletter.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'email=' + encodeURIComponent(email)
  })
  .then(function(r){ return r.json(); })
  .then(function(data){
    msg.style.display = 'block';
    msg.style.color = data.success ? '#10b981' : '#CC2228';
    msg.textContent = data.message;
    if (data.success) document.getElementById('nl-email').value = '';
  })
  .catch(function(){
    msg.style.display = 'block';
    msg.style.color = '#CC2228';
    msg.textContent = 'Something went wrong. Please try again.';
  });
}
</script>
<?php if (!empty($extra_scripts)) echo $extra_scripts; ?>
</body>
</html>
