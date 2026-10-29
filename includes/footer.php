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
<style>
.footer { background: #080B1A !important; color: #f1f5f9 !important; }
.footer p, .footer .footer-desc { color: #e2e8f0 !important; font-size: 14.5px !important; line-height: 1.8 !important; }
.footer .footer-desc strong { color: #ffffff !important; font-weight: 700 !important; }
.footer .footer-desc span { color: #e2e8f0 !important; }
.footer-title { color: #ffffff !important; font-weight: 700 !important; font-size: 15px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.18) !important; padding-bottom: 10px !important; margin-bottom: 18px !important; }
.footer-links li a { color: #cbd5e1 !important; font-size: 14px !important; transition: all 0.25s ease !important; text-decoration: none !important; }
.footer-links li a:hover { color: #ffffff !important; text-shadow: 0 0 10px rgba(91,168,212,0.6) !important; padding-left: 6px !important; }
.footer-contact-item span { color: #e2e8f0 !important; font-size: 13.5px !important; line-height: 1.6 !important; }
.footer-contact-item a { color: #ffffff !important; text-decoration: none !important; font-weight: 600 !important; }
.footer-contact-item a:hover { color: #5BA8D4 !important; }
.footer-bottom { border-top: 1px solid rgba(255, 255, 255, 0.12) !important; padding: 22px 0 !important; background: #050713 !important; }
.footer-bottom p { color: #cbd5e1 !important; font-size: 13.5px !important; }
.footer-bottom a { color: #ffffff !important; font-weight: 600 !important; text-decoration: none !important; transition: color 0.2s ease !important; }
.footer-bottom a:hover { color: #5BA8D4 !important; }
</style>
<footer class="footer">
  <div class="container">
    <div class="row gy-5">

      <!-- Brand Column -->
      <div class="col-lg-3 col-md-6 scroll-reveal">
        <a href="<?= $prefix ?>index.php">
          <picture>
            <source srcset="<?= $prefix ?>logo-footer.webp?v=20261008" type="image/webp">
            <img src="<?= $prefix ?>logo-footer.png?v=20261008" alt="Vortexsoft Innovations" class="footer-logo" width="200" height="55" loading="lazy" decoding="async">
          </picture>
        </a>

        <p class="footer-desc" style="color:#e2e8f0 !important;font-size:14.5px;line-height:1.8;margin-bottom:20px;">
          <strong style="color:#ffffff !important;font-weight:700;">Vortexsoft Innovations Private Limited</strong>
          <span style="color:#e2e8f0 !important;">is an ISO 27001:2013 certified and HIPAA-compliant global IT and BPO outsourcing partner headquartered in Pune with advanced tech delivery in Bengaluru and a corporate presence in Wyoming, USA. Empowering 150+ international clients across AI Software, Healthcare RCM, Publishing, Data Annotation, and Enterprise BPO.</span>
        </p>
        <div class="d-flex gap-2 flex-wrap mt-2 mb-3" style="font-size:11px;color:#f8fafc;font-weight:600;">
          <span style="background:rgba(255,255,255,0.12);color:#ffffff;padding:4px 9px;border-radius:6px;border:1px solid rgba(255,255,255,0.2);"><i class="fas fa-shield-alt text-success me-1"></i> ISO 27001:2013</span>
          <span style="background:rgba(255,255,255,0.12);color:#ffffff;padding:4px 9px;border-radius:6px;border:1px solid rgba(255,255,255,0.2);"><i class="fas fa-user-md text-info me-1"></i> HIPAA Compliant</span>
          <span style="background:rgba(255,255,255,0.12);color:#ffffff;padding:4px 9px;border-radius:6px;border:1px solid rgba(255,255,255,0.2);"><i class="fas fa-award text-warning me-1"></i> Startup India</span>
        </div>
        <div class="footer-social">
          <a href="<?= SOCIAL_FACEBOOK ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook" style="background:rgba(255,255,255,0.12);color:#fff;border-color:rgba(255,255,255,0.2);"><i class="fab fa-facebook-f" style="color:#fff;"></i></a>
          <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram" style="background:rgba(255,255,255,0.12);color:#fff;border-color:rgba(255,255,255,0.2);"><i class="fab fa-instagram" style="color:#fff;"></i></a>
          <a href="<?= SOCIAL_LINKEDIN ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" style="background:rgba(255,255,255,0.12);color:#fff;border-color:rgba(255,255,255,0.2);"><i class="fab fa-linkedin-in" style="color:#fff;"></i></a>
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
          <li><a href="<?= $prefix ?>faq.php">FAQ</a></li>
          <li><a href="<?= $prefix ?>privacy.php">Privacy Policy</a></li>
          <li><a href="<?= $prefix ?>terms.php">Terms of Service</a></li>
          <li><a href="<?= $prefix ?>contact.php">Contact</a></li>
        </ul>
      </div>

      <!-- Our Services -->
      <div class="col-lg-3 col-md-6 scroll-reveal" style="transition-delay:0.2s">
        <h6 class="footer-title">Our Services</h6>
        <ul class="footer-links">
          <li><a href="<?= $prefix ?>index.php#ai-products" style="color:#ff8585;font-weight:700;"><i class="fas fa-users-cog me-1"></i> HR &amp; Payroll Software <span class="badge bg-danger ms-1" style="font-size:9px;">vortexsofthrms</span></a></li>
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
          <i class="fas fa-map-marker-alt" style="color:#5BA8D4;flex-shrink:0;margin-top:3px;"></i>
          <span>
            <a href="https://www.google.com/maps/search/?api=1&query=Second+floor+No.125+Ranganath+Complex+Madiwala+HSR+Layout+Bengaluru+560068" target="_blank" style="color:#ffffff;font-weight:700;display:block;text-decoration:none;font-size:13.5px;">Bengaluru Branch Office <i class="fas fa-external-link-alt" style="font-size:10px;opacity:0.8;"></i></a>
            <span style="color:#e2e8f0;">No.125, Ranganath Complex, Madiwala, HSR Layout, Bengaluru 560068</span>
          </span>
        </div>
        <!-- USA Office -->
        <div class="footer-contact-item" style="margin-bottom:10px;">
          <i class="fas fa-globe-americas" style="color:#5BA8D4;flex-shrink:0;margin-top:3px;"></i>
          <span>
            <a href="https://maps.google.com/?cid=4698826826648482061" target="_blank" style="color:#ffffff;font-weight:700;display:block;text-decoration:none;font-size:13.5px;">USA Office <i class="fas fa-external-link-alt" style="font-size:10px;opacity:0.8;"></i></a>
            <span style="color:#e2e8f0;">30 N Gould St Ste 100, Sheridan, WY 82801, USA</span>
          </span>
        </div>
        <div class="footer-contact-item mt-2">
          <i class="fas fa-envelope" style="color:#ff6b6b;"></i>
          <span>
            <a href="mailto:support@vortexsoftinnovations.com" style="color:#ffffff;font-weight:600;">support@vortexsoftinnovations.com</a>
          </span>
        </div>
        <div class="footer-contact-item">
          <i class="fas fa-phone-alt" style="color:#ff6b6b;"></i>
          <span>
            <a href="tel:+918308906690" style="color:#ffffff;font-weight:600;"><?= PHONE_INDIA ?></a>
          </span>
        </div>
        <div class="footer-contact-item">
          <i class="fas fa-clock" style="color:#10b981;"></i>
          <span>
            <strong style="color:#10b981;">24/7 Digital Inquiries &amp; Support</strong><br>
            <small style="color:#cbd5e1;">Physical Office: Mon–Sat, 9 AM – 6 PM IST</small>
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
        <p style="color:#cbd5e1 !important;font-size:13.5px;margin:0;">Copyright &copy; <?= date('Y') ?> Vortexsoft Innovations Private Limited. All rights reserved.</p>
        <p style="margin:0;display:flex;gap:20px;flex-wrap:wrap;">
          <a href="<?= $prefix ?>faq.php" style="color:#ffffff !important;font-size:13.5px;text-decoration:none;font-weight:600;">FAQ</a>
          <a href="<?= $prefix ?>privacy.php" style="color:#ffffff !important;font-size:13.5px;text-decoration:none;font-weight:600;">Privacy Policy</a>
          <a href="<?= $prefix ?>terms.php" style="color:#ffffff !important;font-size:13.5px;text-decoration:none;font-weight:600;">Terms &amp; Conditions</a>
        </p>
      </div>
    </div>
  </div>
</footer>

<!-- ═══════ FLOATING WIDGETS (UNIFIED RIGHT-SIDE WHATSAPP BOT) ═══════ -->
<style>
@keyframes waSlideUp {
  from { opacity: 0; transform: translateY(16px) scale(0.96); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}
.whatsapp-btn {
  width: 58px;
  height: 58px;
  border-radius: 50%;
  background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 30px;
  box-shadow: 0 10px 28px rgba(37, 211, 102, 0.45);
  position: relative;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.whatsapp-btn:hover {
  transform: scale(1.08);
  box-shadow: 0 14px 34px rgba(37, 211, 102, 0.6);
  color: #fff;
}
.whatsapp-btn::after {
  content: '';
  position: absolute;
  inset: -4px;
  border-radius: 50%;
  border: 2px solid rgba(37, 211, 102, 0.5);
  animation: waRing 2.5s infinite;
  pointer-events: none;
}
@keyframes waRing {
  0% { transform: scale(0.95); opacity: 1; }
  100% { transform: scale(1.3); opacity: 0; }
}
#scrollTop {
  position: fixed;
  bottom: 94px;
  right: 24px;
  width: 44px;
  height: 44px;
  background: var(--gradient-primary, linear-gradient(135deg, #1C2280 0%, #2D35C4 100%));
  color: #fff;
  border: 0;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(28, 34, 128, 0.28);
  z-index: 998;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
}
#scrollTop.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}
#scrollTop:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(28, 34, 128, 0.45);
}
@media (max-width: 576px) {
  #vortexWhatsAppWidget { bottom: 18px; right: 18px; }
  #scrollTop { bottom: 84px; right: 18px; width: 40px; height: 40px; }
  #whatsappChoicePopup { right: 0; width: calc(100vw - 36px) !important; }
}
</style>

<div id="vortexWhatsAppWidget" style="position:fixed;bottom:24px;right:24px;z-index:99999;font-family:'Inter',sans-serif;">
  <!-- WhatsApp Bot Interactive Popup -->
  <div id="whatsappChoicePopup" style="display:none;position:absolute;bottom:70px;right:0;width:360px;max-width:calc(100vw - 32px);background:#ffffff;border-radius:20px;box-shadow:0 24px 60px rgba(0,0,0,0.25),0 0 0 1px rgba(0,0,0,0.06);overflow:hidden;animation:waSlideUp .28s cubic-bezier(0.16,1,0.3,1);">
    <!-- Header -->
    <div style="background:linear-gradient(135deg,#075E54 0%,#128C7E 100%);color:#fff;padding:16px 18px;display:flex;align-items:center;justify-content:space-between;">
      <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:22px;">
          <i class="fab fa-whatsapp"></i>
        </div>
        <div>
          <div style="font-family:'Poppins',sans-serif;font-weight:700;font-size:15px;line-height:1.2;">Vortexsoft WhatsApp Bot</div>
          <div style="font-size:11.5px;color:rgba(255,255,255,0.9);display:flex;align-items:center;gap:6px;margin-top:2px;">
            <span style="width:7px;height:7px;border-radius:50%;background:#25d366;box-shadow:0 0 6px #25d366;"></span> Online &bull; Instant Connect
          </div>
        </div>
      </div>
      <button type="button" onclick="closeWhatsAppModal()" style="background:none;border:none;color:rgba(255,255,255,0.85);font-size:24px;line-height:1;cursor:pointer;padding:2px 6px;" aria-label="Close">&times;</button>
    </div>

    <!-- Body -->
    <div style="padding:16px;background:#f8fafc;">
      <div style="background:#fff;border-radius:12px;padding:12px 14px;border:1px solid #e2e8f0;font-size:13px;color:#334155;line-height:1.5;margin-bottom:14px;box-shadow:0 2px 6px rgba(0,0,0,0.03);">
        👋 <strong>Hello!</strong> Welcome to Vortexsoft Innovations. How can our team assist you today?
      </div>

      <!-- Quick Option 1: Business -->
      <a href="<?= SOCIAL_WHATSAPP_SERVICES ?>" target="_blank" rel="noopener noreferrer" onclick="closeWhatsAppModal()" style="display:flex;align-items:center;gap:12px;background:#ffffff;border:1.5px solid #e2e8f0;border-radius:14px;padding:12px 14px;margin-bottom:10px;text-decoration:none;transition:all .2s ease;" onmouseover="this.style.borderColor='#128C7E';this.style.background='#f0fdf4';this.style.transform='translateY(-2px)';" onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#ffffff';this.style.transform='translateY(0)';">
        <div style="width:38px;height:38px;border-radius:10px;background:rgba(28,34,128,0.08);color:#1C2280;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">
          <i class="fas fa-briefcase"></i>
        </div>
        <div style="flex:1;">
          <strong style="display:block;font-size:13.5px;color:#0F172A;font-weight:700;">Business &amp; Outsourcing</strong>
          <span style="display:block;font-size:11.5px;color:#64748b;margin-top:2px;">US Title Search, Healthcare BPO, AI, IT Solutions</span>
        </div>
        <i class="fas fa-chevron-right" style="color:#94a3b8;font-size:11px;"></i>
      </a>

      <!-- Quick Option 2: Careers -->
      <a href="<?= SOCIAL_WHATSAPP_CAREERS ?>" target="_blank" rel="noopener noreferrer" onclick="closeWhatsAppModal()" style="display:flex;align-items:center;gap:12px;background:#ffffff;border:1.5px solid #e2e8f0;border-radius:14px;padding:12px 14px;margin-bottom:14px;text-decoration:none;transition:all .2s ease;" onmouseover="this.style.borderColor='#CC2228';this.style.background='#fff1f2';this.style.transform='translateY(-2px)';" onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#ffffff';this.style.transform='translateY(0)';">
        <div style="width:38px;height:38px;border-radius:10px;background:rgba(204,34,40,0.08);color:#CC2228;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">
          <i class="fas fa-user-tie"></i>
        </div>
        <div style="flex:1;">
          <strong style="display:block;font-size:13.5px;color:#0F172A;font-weight:700;">Careers &amp; Recruitment</strong>
          <span style="display:block;font-size:11.5px;color:#64748b;margin-top:2px;">Job Openings, Apply Online, Submit Resume</span>
        </div>
        <i class="fas fa-chevron-right" style="color:#94a3b8;font-size:11px;"></i>
      </a>

      <!-- Custom message input -->
      <form onsubmit="sendCustomWhatsApp(event)" style="display:flex;gap:8px;">
        <input type="text" id="waCustomMessage" placeholder="Type your inquiry here..." style="flex:1;border:1.5px solid #cbd5e1;border-radius:10px;padding:9px 12px;font-size:13px;outline:none;background:#fff;" onfocus="this.style.borderColor='#128C7E'" onblur="this.style.borderColor='#cbd5e1'">
        <button type="submit" style="background:#25d366;color:#fff;border:none;border-radius:10px;padding:9px 14px;font-weight:700;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:4px;white-space:nowrap;">
          <i class="fas fa-paper-plane"></i>
        </button>
      </form>
    </div>
  </div>

  <!-- Trigger Button -->
  <button type="button" id="whatsappFloatingBtn" class="whatsapp-btn" aria-label="Open WhatsApp Bot" onclick="toggleWhatsAppModal(event)" style="border:none;cursor:pointer;">
    <i class="fab fa-whatsapp"></i>
  </button>
</div>

<button id="scrollTop" aria-label="Scroll to top" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <i class="fas fa-chevron-up"></i>
</button>

<script>
function toggleWhatsAppModal(e) {
  if (e) e.stopPropagation();
  var popup = document.getElementById('whatsappChoicePopup');
  if (!popup) return;
  if (popup.style.display === 'none' || popup.style.display === '') {
    popup.style.display = 'block';
  } else {
    popup.style.display = 'none';
  }
}
function closeWhatsAppModal() {
  var popup = document.getElementById('whatsappChoicePopup');
  if (popup) popup.style.display = 'none';
}
function sendCustomWhatsApp(e) {
  e.preventDefault();
  var input = document.getElementById('waCustomMessage');
  var msg = (input ? input.value : '').trim();
  if (!msg) {
    msg = 'Hello Vortexsoft Innovations, I would like to inquire about your services.';
  }
  var url = 'https://wa.me/918308906690?text=' + encodeURIComponent(msg);
  window.open(url, '_blank', 'noopener,noreferrer');
  closeWhatsAppModal();
}
document.addEventListener('click', function(e) {
  var popup = document.getElementById('whatsappChoicePopup');
  var btn = document.getElementById('whatsappFloatingBtn');
  if (popup && popup.style.display === 'block') {
    if (!popup.contains(e.target) && (!btn || !btn.contains(e.target))) {
      popup.style.display = 'none';
    }
  }
});
</script>

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
<script defer src="<?= $prefix ?>assets/vendor/bootstrap.bundle.min.js"></script>
<!-- Shared JS -->
<script defer src="<?= $prefix ?>assets/vortex-shared.js?v=20261029opt"></script>
<script defer src="<?= $prefix ?>assets/usability-audit-fixes.js?v=20261029opt"></script>
<script defer src="<?= $prefix ?>assets/vortex-extensions.js?v=20261029opt"></script>
<script>
// ── Loader (Instant Dismissal) ──────────────────────────────
(function(){
  var loader = document.getElementById('page-loader');
  if (!loader) return;
  loader.classList.add('hide');
  setTimeout(function(){ if (loader && loader.parentNode) loader.parentNode.removeChild(loader); }, 150);
})();
window.addEventListener('load', function(){
  var loader = document.getElementById('page-loader');
  if (loader && loader.parentNode) {
    loader.parentNode.removeChild(loader);
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
