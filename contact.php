<?php
/**
 * Vortexsoft Innovations — Master Contact Page (contact.php)
 * Premium Enterprise UI/UX, Inquiry Intake, Verified Global Office Hubs
 */

$page_title   = 'Contact Us | Global IT & BPO Solutions | Vortexsoft Group';
$page_desc    = 'Contact Vortexsoft Group for IT outsourcing, BPO, healthcare, publishing, or AI solutions. Pune Headquarters & offices in Bengaluru and USA.';
$canonical_url = 'https://www.vortexsoftinnovations.com/contact.php';
$prefix       = './';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ═══════ 1. HERO SECTION ═══════ -->
<div class="page-hero">
  <div class="page-hero-glow"></div>
  <div class="container" style="position:relative;z-index:2;">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb mb-2" style="background:transparent;padding:0;font-size:13px;">
        <li class="breadcrumb-item"><a href="index.php" style="color:rgba(255,255,255,0.6);text-decoration:none;"><i class="fas fa-home me-1"></i> Home</a></li>
        <li class="breadcrumb-item active" style="color:rgba(255,255,255,0.9);">Contact Us</li>
      </ol>
    </nav>
    <div class="section-tag mb-3">
      <i class="fas fa-headset"></i> 24/7 Global Solution Advisory
    </div>
    <h1 style="font-size:clamp(1.9rem,3.8vw,2.9rem);font-weight:800;color:#fff;line-height:1.2;letter-spacing:-0.025em;margin-bottom:14px;">
      Let's <span style="background:linear-gradient(135deg,#FFFFFF 30%,#5BA8D4 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Work Together</span>
    </h1>
    <p style="color:rgba(255,255,255,0.8);font-size:15px;max-width:620px;line-height:1.75;margin-bottom:20px;">
      Tell us about your project requirements and our solution architects will get back to you within 24 hours with a customized scope, roadmap, and pricing proposal.
    </p>
    <div class="d-flex flex-wrap gap-2">
      <span style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:100px;padding:5px 14px;font-size:12px;color:#fff;"><i class="fas fa-clock text-success me-1"></i> Fast 24-Hour Turnaround</span>
      <span style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:100px;padding:5px 14px;font-size:12px;color:#fff;"><i class="fas fa-shield-alt text-info me-1"></i> NDA &amp; IP Protection Guaranteed</span>
    </div>
  </div>
</div>

<!-- ═══════ 2. DIRECT COMMUNICATION CHANNELS ═══════ -->
<section class="section-pad-sm" style="background:#F8FAFC;">
  <div class="container">
    <div class="row g-3">
      
      <!-- Phone India -->
      <div class="col-lg-3 col-md-6">
        <div class="vs-card text-center align-items-center">
          <div class="vs-icon-box" style="background:rgba(28,34,128,0.08);color:#1C2280;">
            <i class="fas fa-phone-alt"></i>
          </div>
          <h5 style="font-size:15px;font-weight:700;margin-bottom:4px;color:var(--vs-text-heading);">Phone Support</h5>
          <p style="font-size:12px;color:var(--vs-text-muted);margin-bottom:8px;">Direct Advisory Line</p>
          <a href="tel:<?= str_replace(['-',' '], '', PHONE_INDIA) ?>" style="font-weight:800;color:#1C2280;font-size:15px;display:block;margin-bottom:6px;"><?= PHONE_INDIA ?></a>
          <span style="font-size:11px;color:#10B981;font-weight:600;"><i class="fas fa-circle me-1" style="font-size:7px;"></i> Mon–Sat 9AM–6PM IST</span>
        </div>
      </div>

      <!-- Email Us -->
      <div class="col-lg-3 col-md-6">
        <div class="vs-card text-center align-items-center">
          <div class="vs-icon-box" style="background:rgba(204,34,40,0.08);color:#CC2228;">
            <i class="fas fa-envelope"></i>
          </div>
          <h5 style="font-size:15px;font-weight:700;margin-bottom:4px;color:var(--vs-text-heading);">Official Email</h5>
          <p style="font-size:12px;color:var(--vs-text-muted);margin-bottom:8px;">Inquiries &amp; Proposals</p>
          <a href="mailto:<?= EMAIL_SUPPORT ?>" style="font-weight:700;color:#CC2228;font-size:13px;word-break:break-all;display:block;margin-bottom:6px;"><?= EMAIL_SUPPORT ?></a>
          <span style="font-size:11px;color:#64748B;font-weight:600;"><i class="fas fa-bolt text-warning me-1"></i> &lt;24h Response SLA</span>
        </div>
      </div>

      <!-- WhatsApp Fast Chat -->
      <div class="col-lg-3 col-md-6">
        <div class="vs-card text-center align-items-center">
          <div class="vs-icon-box" style="background:rgba(37,211,102,0.1);color:#25D366;">
            <i class="fab fa-whatsapp"></i>
          </div>
          <h5 style="font-size:15px;font-weight:700;margin-bottom:4px;color:var(--vs-text-heading);">WhatsApp Fast Chat</h5>
          <p style="font-size:12px;color:var(--vs-text-muted);margin-bottom:12px;">Instant Business Query (&lt;30m)</p>
          <a href="<?= SOCIAL_WHATSAPP ?>" target="_blank" class="btn btn-sm" style="background:#25D366;color:#fff;font-weight:700;border-radius:8px;padding:6px 16px;font-size:12.5px;">
            <i class="fab fa-whatsapp me-1"></i> Chat on WhatsApp
          </a>
        </div>
      </div>

      <!-- Head Office Card (Pune) -->
      <div class="col-lg-3 col-md-6">
        <div class="vs-card text-center align-items-center" style="border:1.5px solid #CC2228;background:linear-gradient(180deg,#fff 0%,#fff5f5 100%);">
          <div class="vs-icon-box" style="background:rgba(204,34,40,0.12);color:#CC2228;">
            <i class="fas fa-building"></i>
          </div>
          <span class="badge mb-1" style="background:#CC2228;color:#fff;font-size:9.5px;padding:3px 8px;">HEAD OFFICE</span>
          <h5 style="font-size:15px;font-weight:800;margin-bottom:4px;color:#1e293b;">Pune Headquarters</h5>
          <p style="font-size:11.5px;color:#475569;margin-bottom:10px;line-height:1.4;">502, Dangat Patil Empire, Vadgaon Budruk, Pune 411041</p>
          <a href="https://share.google/XKt2SVYsKfiNqrVGx" target="_blank" class="btn btn-sm" style="background:#CC2228;color:#fff;font-size:11.5px;font-weight:600;padding:4px 12px;border-radius:6px;">
            <i class="fas fa-map-marked-alt me-1"></i> View Pune Map
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ═══════ 3. INQUIRY FORM & GLOBAL HUBS WITH MAP ═══════ -->
<section class="section-pad" style="background:#FFFFFF;">
  <div class="container">
    <div class="row gy-5 align-items-start">
      
      <!-- Left Column: Inquiry Form -->
      <div class="col-lg-6">
        <div style="background:#FFFFFF;border:1px solid #E2E8F0;border-radius:20px;padding:32px 28px;box-shadow:var(--vs-shadow-sm);">
          <div class="mb-4">
            <div class="section-tag mb-2">Project Intake</div>
            <h2 style="font-size:1.8rem;font-weight:800;color:var(--vs-text-heading);margin-bottom:8px;">
              Send Us a <span class="highlight">Message</span>
            </h2>
            <p style="font-size:13.5px;color:var(--vs-text-muted);margin:0;">Fill out the form below and an industry specialist will connect with you.</p>
          </div>

          <div id="form-feedback" class="d-none mb-3" role="alert"></div>

          <form id="contactForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">
            
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" for="fullName">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Your full name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="emailAddr">Work Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="emailAddr" name="emailAddr" placeholder="name@company.com" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="phone">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+1 (555) 000-0000">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="company">Company / Organization</label>
                <input type="text" class="form-control" id="company" name="company" placeholder="Company name">
              </div>
              <div class="col-12">
                <label class="form-label" for="service">Service Domain</label>
                <select class="form-select" id="service" name="service">
                  <option value="Healthcare BPO & Medical Coding">Healthcare BPO & Medical Coding</option>
                  <option value="AI & Intelligent Automation">AI & Intelligent Automation</option>
                  <option value="IT & Custom Software Development">IT & Custom Software Development</option>
                  <option value="Publishing & Accessibility Prepress">Publishing & Accessibility Prepress</option>
                  <option value="Real Estate, Lease & Title Services">Real Estate, Lease & Title Services</option>
                  <option value="Accounting & Payroll Outsourcing">Accounting & Payroll Outsourcing</option>
                  <option value="Data Annotation & AI Training">Data Annotation & AI Training</option>
                  <option value="Digital Marketing & Growth">Digital Marketing & Growth</option>
                  <option value="Other Enterprise Inquiries">Other Enterprise Inquiries</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label" for="msgText">Project Scope &amp; Details <span class="text-danger">*</span></label>
                <textarea class="form-control" id="msgText" name="msgText" rows="4" placeholder="Briefly describe your objectives, timelines, and required team size..." required></textarea>
              </div>
              <div class="col-12 mt-4">
                <button type="submit" class="btn-primary-custom" id="submitBtn" style="width:100%;justify-content:center;padding:14px;">
                  <i class="fas fa-paper-plane"></i> Submit Inquiry
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Right Column: Location Cards & Interactive Map -->
      <div class="col-lg-6">
        <div class="mb-4">
          <div class="section-tag mb-2">Global Presence</div>
          <h2 style="font-size:1.8rem;font-weight:800;color:var(--vs-text-heading);margin-bottom:8px;">
            Headquarters &amp; <span class="highlight">Offices</span>
          </h2>
          <p style="font-size:13.5px;color:var(--vs-text-muted);margin:0;">Physical delivery nodes across India and North America.</p>
        </div>

        <!-- Pune HQ Card -->
        <div style="background:#FFF5F5;border:1.5px solid #CC2228;border-radius:16px;padding:20px;margin-bottom:14px;display:flex;gap:16px;align-items:flex-start;">
          <div style="font-size:26px;flex-shrink:0;">🇮🇳</div>
          <div style="flex:1;">
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="badge" style="background:#CC2228;color:#fff;font-size:9.5px;padding:3px 7px;">HEAD OFFICE</span>
              <h5 style="font-size:15px;font-weight:800;color:#1C2280;margin:0;">Pune Headquarters</h5>
            </div>
            <p style="font-size:12.5px;color:#334155;margin-bottom:8px;line-height:1.5;">
              502, 4th Floor, Dangat Patil Empire, Vadgaon Budruk, Pune, Maharashtra 411041, India
            </p>
            <div class="d-flex gap-3 align-items-center">
              <a href="https://share.google/XKt2SVYsKfiNqrVGx" target="_blank" style="font-size:12px;font-weight:700;color:#CC2228;"><i class="fas fa-map-marked-alt me-1"></i>Open Google Maps</a>
              <a href="tel:+918308906690" style="font-size:12px;font-weight:600;color:#1C2280;"><i class="fas fa-phone-alt me-1"></i>+91-8308906690</a>
            </div>
          </div>
        </div>

        <!-- Bengaluru Branch -->
        <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:16px;padding:18px;margin-bottom:14px;display:flex;gap:16px;align-items:flex-start;">
          <div style="font-size:24px;flex-shrink:0;">🇮🇳</div>
          <div style="flex:1;">
            <h6 style="font-size:14px;font-weight:700;color:var(--vs-text-heading);margin-bottom:4px;">Bengaluru Tech Center</h6>
            <p style="font-size:12.5px;color:#64748B;margin-bottom:6px;">No.125, Ranganath Complex, Madiwala, HSR Layout, Bengaluru 560068</p>
            <a href="https://www.google.com/maps/search/?api=1&query=Second+floor+No.125+Ranganath+Complex+Madiwala+HSR+Layout+Bengaluru+560068" target="_blank" style="font-size:12px;font-weight:600;color:#64748B;"><i class="fas fa-directions me-1"></i>Get Directions</a>
          </div>
        </div>

        <!-- USA Office -->
        <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:16px;padding:18px;margin-bottom:20px;display:flex;gap:16px;align-items:flex-start;">
          <div style="font-size:24px;flex-shrink:0;">🇺🇸</div>
          <div style="flex:1;">
            <h6 style="font-size:14px;font-weight:700;color:var(--vs-text-heading);margin-bottom:4px;">USA Corporate Office</h6>
            <p style="font-size:12.5px;color:#64748B;margin-bottom:6px;">30 N Gould St Ste 100, Sheridan, WY 82801, United States</p>
            <a href="https://maps.google.com/?cid=4698826826648482061" target="_blank" style="font-size:12px;font-weight:600;color:#64748B;"><i class="fas fa-directions me-1"></i>View Location</a>
          </div>
        </div>

        <!-- Google Map Embed -->
        <div style="border-radius:16px;overflow:hidden;border:1px solid #E2E8F0;box-shadow:var(--vs-shadow-sm);">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3783.923832104593!2d73.8329!3d18.4792!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc2bfaf7a26f8d1%3A0x6b093256037e42d7!2sDangat%20Patil%20Empire%2C%20Vadgaon%20Budruk%2C%20Pune%2C%20Maharashtra%20411041!5e0!3m2!1sen!2sin!4v1700000000000"
            width="100%" height="280" style="border:0;display:block;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade" title="Vortexsoft Group Pune Headquarters Map">
          </iframe>
        </div>

      </div>

    </div>
  </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
