<?php
/**
 * Vortexsoft Innovations — Contact Page (contact.php)
 */

$page_title   = 'Contact Us — Vortexsoft Group | IT & BPO Outsourcing India';
$page_desc    = 'Contact Vortexsoft Group for IT outsourcing, BPO, healthcare, publishing, or AI solutions. Call +91-8308906690 or email contact@vortexsoftinnovations.in. Offices in Bengaluru, Pune, and USA.';
$canonical_url = 'https://www.vortexsoftinnovations.com/contact.php';
$prefix       = './';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';
?>
<style>
.page-hero{background:linear-gradient(135deg,#080B1A 0%,#1C2280 55%,#0D1035 100%);padding:80px 0 70px;position:relative;overflow:hidden}
.page-hero::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:50px 50px}
.page-hero h1{font-size:clamp(2rem,4vw,3rem);font-weight:800;color:#fff}
.page-hero .breadcrumb-item,.page-hero .breadcrumb-item a{color:rgba(255,255,255,.6);font-size:14px}
.page-hero .breadcrumb-item.active{color:rgba(255,255,255,.9)}
.page-hero .breadcrumb-item+.breadcrumb-item::before{color:rgba(255,255,255,.4)}
.contact-card{background:#fff;border-radius:20px;padding:36px;box-shadow:0 4px 20px rgba(28,34,128,.1);border:1px solid rgba(28,34,128,.06);height:100%}
.contact-icon-box{width:54px;height:54px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:16px}
.map-container{width:100%;height:400px;border-radius:20px;overflow:hidden;border:1px solid var(--border-light)}
.form-control:focus,.form-select:focus{border-color:#1C2280;box-shadow:0 0 0 3px rgba(28,34,128,.1)}
.form-floating .form-control{border:1.5px solid #dde2f5;border-radius:10px}
.btn-send{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;font-family:'Poppins',sans-serif;font-size:15px;font-weight:600;padding:14px 36px;border-radius:10px;border:none;transition:all .3s;display:inline-flex;align-items:center;gap:8px}
.btn-send:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(28,34,128,.3);color:#fff}
</style>

<!-- Hero -->
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Contact</li></ol></nav>
    <h1>Contact <span style="color:#CC2228;">Vortexsoft Group</span></h1>
    <p style="color:rgba(255,255,255,.7);font-size:16px;margin-top:12px;max-width:550px;">Submit an inquiry anytime &mdash; we respond within 24 hours on business days. Our team is available Mon&ndash;Sat, 9AM&ndash;6PM IST.</p>
  </div>
</div>

<!-- Contact Cards -->
<section class="py-5" style="background:var(--bg-light,#f0f2ff);">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-3 col-md-6 scroll-reveal">
        <div class="contact-card text-center">
          <div class="contact-icon-box mx-auto" style="background:rgba(28,34,128,.08);color:#1C2280;"><i class="fas fa-phone-alt"></i></div>
          <h5 style="font-family:'Poppins',sans-serif;font-weight:700;margin-bottom:8px;">Call Us</h5>
          <p style="font-size:13px;color:#64748b;margin-bottom:4px;">Mon&ndash;Sat, 9AM&ndash;6PM IST</p>
          <p style="font-size:12px;color:#10b981;font-weight:600;margin-bottom:12px;"><i class="fas fa-circle" style="font-size:8px;margin-right:4px;"></i>Online Inquiries: 24/7</p>
          <a href="tel:+918308906690" style="display:block;font-weight:700;color:#1C2280;font-size:15px;margin-bottom:4px;"><?= PHONE_INDIA ?></a>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 scroll-reveal" style="transition-delay:.1s">
        <div class="contact-card text-center">
          <div class="contact-icon-box mx-auto" style="background:rgba(204,34,40,.08);color:#CC2228;"><i class="fas fa-envelope"></i></div>
          <h5 style="font-family:'Poppins',sans-serif;font-weight:700;margin-bottom:8px;">Email Us</h5>
          <p style="font-size:13px;color:#64748b;margin-bottom:12px;">We reply within 24 hours</p>
          <a href="mailto:<?= EMAIL_SUPPORT ?>" style="display:block;font-weight:600;color:#CC2228;font-size:13px;margin-bottom:4px;word-break:break-all;"><?= EMAIL_SUPPORT ?></a>
          <a href="mailto:<?= EMAIL_INFO ?>" style="display:block;font-weight:500;color:#64748b;font-size:13px;word-break:break-all;"><?= EMAIL_INFO ?></a>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 scroll-reveal" style="transition-delay:.2s">
        <div class="contact-card text-center">
          <div class="contact-icon-box mx-auto" style="background:rgba(37,211,102,.08);color:#25d366;"><i class="fab fa-whatsapp"></i></div>
          <h5 style="font-family:'Poppins',sans-serif;font-weight:700;margin-bottom:8px;">WhatsApp</h5>
          <p style="font-size:13px;color:#64748b;margin-bottom:12px;">Quick response in &lt;30 min</p>
          <a href="<?= SOCIAL_WHATSAPP ?>" target="_blank" class="btn" style="background:#25d366;color:#fff;border-radius:8px;font-size:13px;font-weight:700;padding:8px 20px;"><i class="fab fa-whatsapp me-1"></i> Chat Now</a>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 scroll-reveal" style="transition-delay:.3s">
        <div class="contact-card text-center">
          <div class="contact-icon-box mx-auto" style="background:rgba(91,168,212,.08);color:#5BA8D4;"><i class="fas fa-map-marker-alt"></i></div>
          <h5 style="font-family:'Poppins',sans-serif;font-weight:700;margin-bottom:8px;">Headquarters</h5>
          <p style="font-size:13px;color:#64748b;">No.125, Ranganath Complex, Madiwala, HSR Layout, Bengaluru 560068</p>
          <a href="https://www.google.com/maps/search/?api=1&query=Ranganath+Complex+Madiwala+HSR+Layout+Bengaluru+560068" target="_blank" style="font-size:13px;color:#5BA8D4;font-weight:600;"><i class="fas fa-directions me-1"></i>Get Directions</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Contact Form + Map -->
<section class="py-5 pb-6" style="background:#fff;">
  <div class="container">
    <div class="row gy-5 align-items-start">
      <!-- Form -->
      <div class="col-lg-6 scroll-reveal-left">
        <div style="max-width:540px;">
          <div class="mb-4">
            <div class="section-tag">Get In Touch</div>
            <h2 class="section-title">Send Us a <span class="highlight">Message</span></h2>
            <div class="section-divider"></div>
            <p style="color:var(--text-muted);font-size:15px;">Fill in the form below and our specialist team will provide a tailored response within 24 hours.</p>
          </div>
          <div id="form-feedback" class="d-none mb-4" role="alert"></div>
          <form id="contactForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold" for="fullName">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Your full name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" for="emailAddr">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="emailAddr" name="emailAddr" placeholder="your@email.com" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" for="phone">Phone</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+91 XXXXX XXXXX">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" for="company">Company</label>
                <input type="text" class="form-control" id="company" name="company" placeholder="Company name">
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold" for="service">Service Needed</label>
                <select class="form-select" id="service" name="service">
                  <option value="General Inquiry">General Inquiry</option>
                  <option value="IT & Software Solutions">IT & Software Solutions</option>
                  <option value="Healthcare BPO">Healthcare BPO</option>
                  <option value="Publishing Services">Publishing Services</option>
                  <option value="Real Estate Services">Real Estate Services</option>
                  <option value="Data Annotation & AI">Data Annotation & AI</option>
                  <option value="Accounting & Finance">Accounting & Finance</option>
                  <option value="Digital Marketing">Digital Marketing</option>
                  <option value="Logistics Services">Logistics Services</option>
                  <option value="Technical Publications">Technical Publications</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold" for="msgText">Message <span class="text-danger">*</span></label>
                <textarea class="form-control" id="msgText" name="msgText" rows="5" placeholder="Describe your project or requirements in detail..." required></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-send magnetic" id="submitBtn" style="width:100%;justify-content:center;">
                  <i class="fas fa-paper-plane"></i> Send Message
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Info + Map -->
      <div class="col-lg-6 scroll-reveal-right">
        <div class="mb-4">
          <div class="section-tag">Our Offices</div>
          <h2 class="section-title">Visit <span class="highlight">Our Offices</span></h2>
          <div class="section-divider"></div>
        </div>
        <!-- Office Cards -->
        <?php $offices = [
          ['name'=>'Bengaluru HQ','flag'=>'🇮🇳','addr'=>'No.125, Ranganath Complex, above Greenline Travels, Madiwala, HSR Layout 5th Sector, Bengaluru 560068','map'=>'https://www.google.com/maps/search/?api=1&query=Second+floor+No.125+Ranganath+Complex+Madiwala+HSR+Layout+Bengaluru+560068','phone'=>PHONE_INDIA,'email'=>EMAIL_CONTACT],
          ['name'=>'Pune Office','flag'=>'🇮🇳','addr'=>'502, 4th Floor, Dangat Patil Empire, Kudale Baug, Vadgaon Budruk, Pune 411041','map'=>'https://www.google.com/maps/search/?api=1&query=502+4th+Floor+Dangat+Patil+Empire+Vadgaon+Budruk+Pune+411041','phone'=>PHONE_INDIA,'email'=>EMAIL_INFO],
          ['name'=>'USA Office','flag'=>'🇺🇸','addr'=>'30 N Gould St Ste 100, Sheridan, WY 82801, United States','map'=>'https://maps.google.com/?cid=4698826826648482061','phone'=>'+1 (307) 218-0000','email'=>EMAIL_IT],
        ]; ?>

        <?php foreach($offices as $o): ?>
        <div style="background:var(--bg-light,#f0f2ff);border-radius:16px;padding:20px 24px;margin-bottom:16px;border:1px solid var(--border-light,#dde2f5);display:flex;gap:18px;align-items:flex-start;">
          <div style="font-size:30px;flex-shrink:0;margin-top:2px;"><?= $o['flag'] ?></div>
          <div style="flex:1;">
            <h6 style="font-family:'Poppins',sans-serif;font-weight:700;color:#1C2280;margin-bottom:4px;"><?= $o['name'] ?></h6>
            <p style="font-size:13px;color:#475569;margin-bottom:8px;"><?= $o['addr'] ?></p>
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
              <a href="<?= $o['map'] ?>" target="_blank" style="font-size:12px;font-weight:600;color:#CC2228;text-decoration:none;"><i class="fas fa-directions me-1"></i>Directions</a>
              <a href="tel:<?= preg_replace('/[^+0-9]/','',$o['phone']) ?>" style="font-size:12px;font-weight:600;color:#1C2280;text-decoration:none;"><i class="fas fa-phone-alt me-1"></i><?= $o['phone'] ?></a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

        <!-- Map -->
        <div class="map-container mt-4">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3888.599766835388!2d77.6141!3d12.9141!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTLCsDU0JzUwLjciTiA3N8KwMzYnNTEuOCJF!5e0!3m2!1sen!2sin!4v1700000000000"
            width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade" title="Vortexsoft Group Bengaluru HQ Map">
          </iframe>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
$extra_scripts = '
<script>
(function(){
  var form = document.getElementById("contactForm");
  if (!form) return;
  form.addEventListener("submit", function(e) {
    e.preventDefault();
    var btn = document.getElementById("submitBtn");
    var fb  = document.getElementById("form-feedback");
    btn.innerHTML = \'<i class="fas fa-spinner fa-spin"></i> Sending...\';
    btn.disabled = true;
    fb.className = "d-none";
    fetch("api/contact.php", {method:"POST", body: new FormData(form)})
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          form.reset();
          var m = document.getElementById("contactSuccessModal");
          if (m && window.bootstrap) new bootstrap.Modal(m).show();
          else { fb.className = "alert alert-success"; fb.innerHTML = \'<i class="fas fa-check-circle me-2"></i>\' + res.message; }
        } else {
          fb.className = "alert alert-danger";
          fb.innerHTML = \'<i class="fas fa-exclamation-circle me-2"></i>\' + res.message;
        }
      })
      .catch(() => { fb.className="alert alert-danger"; fb.textContent="Network error. Please try again."; })
      .finally(() => { btn.innerHTML=\'<i class="fas fa-paper-plane"></i> Send Message\'; btn.disabled=false; });
  });
})();
</script>';
require_once __DIR__ . '/includes/footer.php';
?>
