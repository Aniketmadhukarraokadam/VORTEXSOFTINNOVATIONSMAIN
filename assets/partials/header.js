const VORTEX_HEADER_TEMPLATE = `<style>
/* ═══════════════════════════════
   VORTEXSOFT — CLEAN HEADER v4
   Minimal · Crisp · Professional
═══════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; }

/* ── Sticky wrapper */
#site-header {
  position: sticky;
  top: 0;
  z-index: 1040;
  font-family: 'Inter', sans-serif;
}

/* ════════════════════════════════
   TOPBAR — Minimal strip
════════════════════════════════ */
.vs-topbar {
  background: #f8f9fc;
  border-bottom: 1px solid #e8eaf2;
  height: 38px;
  display: flex;
  align-items: center;
}
.vs-topbar .container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}

/* LEFT — Brand name */
.vs-tb-brand {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}
.vs-live-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #22c55e;
  animation: vsPulse 2s ease-out infinite;
  flex-shrink: 0;
}
@keyframes vsPulse {
  0%   { box-shadow: 0 0 0 0 rgba(34,197,94,0.5); }
  70%  { box-shadow: 0 0 0 5px rgba(34,197,94,0); }
  100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
}
.vs-tb-name {
  font-family: 'Poppins', sans-serif;
  font-size: 11px;
  font-weight: 700;
  color: #1C2280;
  letter-spacing: 0.5px;
  white-space: nowrap;
}
.vs-tb-sep {
  width: 1px;
  height: 14px;
  background: #d1d5e8;
  flex-shrink: 0;
}
.vs-tb-avail {
  font-size: 10.5px;
  font-weight: 600;
  color: #16a34a;
  white-space: nowrap;
  letter-spacing: 0.2px;
}

/* CENTER — Contact items */
.vs-tb-center {
  display: flex;
  align-items: center;
  gap: 0;
}
.vs-tb-item {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 0 16px;
  border-right: 1px solid #e8eaf2;
  text-decoration: none;
  transition: background 0.18s;
  height: 38px;
  position: relative;
}
.vs-tb-item:first-child { border-left: 1px solid #e8eaf2; }
.vs-tb-item:hover { background: rgba(28,34,128,0.04); }
.vs-tb-item::after {
  content: '';
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 2px;
  background: #1C2280;
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.22s ease;
}
.vs-tb-item:hover::after { transform: scaleX(1); }

.vs-tb-icon {
  font-size: 11px;
  color: #1C2280;
  opacity: 0.65;
  width: 14px;
  text-align: center;
  flex-shrink: 0;
}
.vs-tb-text {
  font-size: 12px;
  font-weight: 500;
  color: #374151;
  white-space: nowrap;
}
.vs-tb-text strong {
  font-weight: 700;
  color: #111827;
  font-size: 12px;
}
.vs-tb-item:hover .vs-tb-text { color: #1C2280; }
.vs-tb-item:hover .vs-tb-text strong { color: #1C2280; }

/* RIGHT — ISO + Socials */
.vs-tb-right {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-shrink: 0;
}
.vs-tb-iso {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 0 10px;
  height: 22px;
  border-radius: 5px;
  background: rgba(28,34,128,0.06);
  border: 1px solid rgba(28,34,128,0.12);
  margin-right: 6px;
}
.vs-tb-iso i { font-size: 10px; color: #1C2280; opacity: 0.7; }
.vs-tb-iso span {
  font-family: 'Poppins', sans-serif;
  font-size: 9.5px;
  font-weight: 800;
  color: #1C2280;
  letter-spacing: 0.3px;
}

.vs-tb-soc {
  width: 26px;
  height: 26px;
  border-radius: 6px;
  background: transparent;
  border: 1px solid #e0e3f0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  font-size: 11px;
  text-decoration: none;
  transition: all 0.2s ease;
}
.vs-tb-soc:hover { transform: translateY(-1px); color: #fff; border-color: transparent; }
.vs-tb-soc.fb:hover { background: #1877f2; }
.vs-tb-soc.ig:hover { background: linear-gradient(135deg,#f09433,#dc2743,#bc1888); }
.vs-tb-soc.li:hover { background: #0a66c2; }
.vs-tb-soc.wa:hover { background: #25d366; }
.vs-tb-soc.tw:hover { background: #111; border-color: #333; }

/* Responsive breakpoints */
@media (max-width: 1199px) { .vs-ci-loc { display: none !important; } }
@media (max-width: 991px)  { .vs-ci-wa  { display: none !important; } }
@media (max-width: 767px)  { .vs-topbar { display: none !important; } }

/* ════════════════════════════════
   NAVBAR — Premium white bar
════════════════════════════════ */
#mainNavbar {
  background: #ffffff;
  border-bottom: 1px solid #e8eaf2;
  padding: 0;
  position: relative;
  z-index: 1;
  transition: box-shadow 0.3s;
  box-shadow: 0 2px 16px rgba(28,34,128,0.07);
}

/* Animated gradient rule on very top of navbar */
#mainNavbar::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  background: linear-gradient(90deg,
    #1C2280 0%, #3d48d4 25%, #5BA8D4 50%, #CC2228 75%, #1C2280 100%
  );
  background-size: 200% 100%;
  animation: vsRuleFlow 8s linear infinite;
  z-index: 2;
  pointer-events: none;
}
@keyframes vsRuleFlow {
  from { background-position: 0% 0; }
  to   { background-position: -200% 0; }
}
#mainNavbar.scrolled { box-shadow: 0 4px 30px rgba(28,34,128,0.12); }

#mainNavbar .container {
  display: flex;
  align-items: center;
  min-height: 68px;
  padding-top: 3px;
}

/* Logo */
.navbar-brand img {
  height: 52px;
  object-fit: contain;
  transition: opacity 0.2s, transform 0.25s;
}
.navbar-brand:hover img { opacity: 0.88; transform: scale(1.02); }

/* Nav links */
#mainNavbar .nav-link {
  font-family: 'Poppins', sans-serif;
  font-size: 13.5px;
  font-weight: 600;
  color: #1a1d3a !important;
  padding: 8px 13px !important;
  border-radius: 7px;
  position: relative;
  transition: color 0.2s, background 0.2s;
  white-space: nowrap;
}
#mainNavbar .nav-link::after {
  content: '';
  position: absolute;
  bottom: 4px; left: 13px; right: 13px;
  height: 2px;
  background: linear-gradient(90deg, #1C2280, #CC2228);
  border-radius: 2px;
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.25s cubic-bezier(0.4,0,0.2,1);
}
#mainNavbar .nav-link:hover,
#mainNavbar .nav-link.active {
  color: #1C2280 !important;
  background: rgba(28,34,128,0.04);
}
#mainNavbar .nav-link:hover::after,
#mainNavbar .nav-link.active::after { transform: scaleX(1); }

/* Dropdown mega-menu */
#mainNavbar .dropdown-menu {
  border: none;
  border-radius: 14px;
  box-shadow:
    0 0 0 1px rgba(28,34,128,0.07),
    0 20px 60px rgba(28,34,128,0.14);
  padding: 22px;
  margin-top: 10px;
  animation: vsDrop 0.18s cubic-bezier(0.4,0,0.2,1);
  background: #fff;
}
@keyframes vsDrop {
  from { opacity:0; transform:translateY(-8px); }
  to   { opacity:1; transform:translateY(0); }
}
.mega-menu-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 0 10px; }

#mainNavbar .dropdown-header {
  font-family: 'Poppins', sans-serif;
  font-size: 9.5px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: rgba(28,34,128,0.4);
  padding: 10px 10px 5px;
  border-bottom: 1.5px solid #eef0f8;
  margin-bottom: 4px;
  display: flex;
  align-items: center;
  gap: 5px;
}
#mainNavbar .dropdown-header i { opacity: 0.55; font-size: 10px; }
#mainNavbar .dropdown-item {
  font-size: 13px;
  font-weight: 500;
  color: #374151;
  padding: 6px 10px;
  border-radius: 7px;
  transition: background 0.15s, color 0.15s, padding-left 0.18s;
  display: flex;
  align-items: center;
  gap: 8px;
}
#mainNavbar .dropdown-item i { font-size: 11px; width: 15px; text-align: center; color: #CC2228; opacity: 0.65; flex-shrink: 0; }
#mainNavbar .dropdown-item:hover { background: rgba(28,34,128,0.05); color: #1C2280; padding-left: 14px; }
#mainNavbar .dropdown-item:hover i { opacity: 1; }

/* All-services CTA row in dropdown */
.vs-dd-all {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 8px 10px;
  border-radius: 7px;
  background: rgba(204,34,40,0.06);
  border: 1px solid rgba(204,34,40,0.14);
  color: #CC2228 !important;
  font-weight: 700 !important;
  font-size: 12.5px !important;
  text-decoration: none;
  transition: background 0.2s;
  margin-top: 8px;
}
.vs-dd-all:hover { background: rgba(204,34,40,0.1) !important; }
.vs-dd-all i { color: #CC2228 !important; opacity: 1 !important; }

/* Mini contact card inside dropdown */
.vs-dd-card {
  margin-top: 12px;
  padding: 11px 12px;
  border-radius: 9px;
  background: #f8f9fc;
  border: 1px solid #e8eaf2;
}
.vs-dd-card-avail {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 10px;
  font-weight: 700;
  color: #16a34a;
  margin-bottom: 7px;
}
.vs-dd-card-avail::before {
  content: '';
  width: 5px; height: 5px;
  border-radius: 50%;
  background: #22c55e;
  animation: vsPulse 2s ease-out infinite;
}
.vs-dd-card-row {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11.5px;
  color: #374151;
  font-weight: 500;
  margin-top: 3px;
  text-decoration: none;
  transition: color 0.15s;
}
.vs-dd-card-row:hover { color: #1C2280; }
.vs-dd-card-row i { font-size: 10px; color: #1C2280; opacity: 0.6; width: 12px; }

/* Right side — phone block */
.vs-nav-phones {
  display: flex;
  flex-direction: column;
  gap: 3px;
  padding-left: 18px;
  margin-left: 6px;
  border-left: 1.5px solid #e8eaf2;
  flex-shrink: 0;
}
.vs-nav-ph-row {
  display: flex;
  align-items: center;
  gap: 6px;
  text-decoration: none;
  transition: opacity 0.2s;
}
.vs-nav-ph-row:hover { opacity: 0.7; }
.vs-nav-ph-icon {
  width: 20px; height: 20px;
  border-radius: 5px;
  background: rgba(28,34,128,0.07);
  display: flex; align-items: center; justify-content: center;
  font-size: 9px;
  color: #1C2280;
  flex-shrink: 0;
}
.vs-nav-ph-num {
  font-family: 'Poppins', sans-serif;
  font-size: 12px;
  font-weight: 700;
  color: #1a1d3a;
  white-space: nowrap;
  line-height: 1;
}
.vs-nav-avail {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 9.5px;
  font-weight: 700;
  color: #16a34a;
  padding-left: 26px;
  white-space: nowrap;
}
.vs-nav-avail::before {
  content: '';
  width: 5px; height: 5px;
  border-radius: 50%;
  background: #22c55e;
  animation: vsPulse 2s ease-out infinite;
}

/* CTA button */
#mainNavbar .nav-cta {
  background: linear-gradient(135deg, #CC2228 0%, #a01820 100%) !important;
  color: #fff !important;
  font-family: 'Poppins', sans-serif !important;
  font-size: 13px !important;
  font-weight: 700 !important;
  padding: 10px 20px !important;
  border-radius: 8px !important;
  box-shadow: 0 3px 14px rgba(204,34,40,0.26) !important;
  transition: transform 0.2s, box-shadow 0.2s !important;
  white-space: nowrap;
  margin-left: 14px;
  display: inline-flex;
  align-items: center;
  gap: 7px;
}
#mainNavbar .nav-cta::after { display: none !important; }
#mainNavbar .nav-cta i { color: #fff !important; }
#mainNavbar .nav-cta:hover {
  color: #fff !important;
  transform: translateY(-2px) !important;
  box-shadow: 0 7px 22px rgba(204,34,40,0.38) !important;
}

/* Toggler */
.navbar-toggler {
  border: none !important;
  padding: 7px 9px;
  box-shadow: none !important;
  background: rgba(28,34,128,0.05) !important;
  border-radius: 8px !important;
}
.navbar-toggler:focus { box-shadow: none !important; }

/* Mobile adjustments */
@media (max-width: 991.98px) {
  #mainNavbar .dropdown-menu { min-width: unset !important; border-radius: 10px; }
  .mega-menu-grid { grid-template-columns: 1fr 1fr; }
  #mainNavbar .nav-cta { margin: 10px 0 6px !important; }
  .vs-nav-phones { display: none !important; }
}

/* Mobile ticker */
.vs-ticker {
  display: none;
  background: #1C2280;
  height: 32px;
  overflow: hidden;
  align-items: center;
}
@media (max-width: 767px) { .vs-ticker { display: flex; } }
.vs-ticker-track {
  display: flex;
  white-space: nowrap;
  animation: vsTicker 24s linear infinite;
}
.vs-ticker-track span {
  font-size: 11px;
  font-weight: 600;
  color: rgba(255,255,255,0.8);
  padding: 0 28px;
  letter-spacing: 0.3px;
  font-family: 'Inter', sans-serif;
}
.vs-ticker-track span::before { content: '· '; opacity: 0.4; }
@keyframes vsTicker {
  from { transform: translateX(0); }
  to   { transform: translateX(-50%); }
}
</style>


<!-- ════════════════════════════
     NAVBAR
════════════════════════════ -->
<nav class="navbar navbar-expand-lg" id="mainNavbar">
  <div class="container">

    <a class="navbar-brand me-4" href="{{PREFIX}}index.html">
      <img src="{{PREFIX}}logo-header.jpg" alt="Vortexsoft Innovations Pvt. Ltd." />
    </a>

    <button class="navbar-toggler ms-auto" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarContent"
            aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <i class="fas fa-bars" style="color:#1C2280;font-size:19px;"></i>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
        <li class="nav-item"><a class="nav-link" href="{{PREFIX}}index.html">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="{{PREFIX}}about.html">About</a></li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="{{PREFIX}}service.html"
             id="servicesDropdown" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">Services</a>
          <ul class="dropdown-menu" style="min-width:820px;">
            <div class="mega-menu-grid">
              <!-- Col 1 -->
              <div>
                <h6 class="dropdown-header"><i class="fas fa-book"></i>Publishing</h6>
                <li><a class="dropdown-item" href="{{PREFIX}}publishing-services/index.html"><i class="fas fa-book"></i>Publishing Services</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}editorial-services/index.html"><i class="fas fa-pencil-alt"></i>Editorial Services</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}digital-prepress-services/index.html"><i class="fas fa-print"></i>Digital Prepress</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}copy-editing-services/index.html"><i class="fas fa-spell-check"></i>Copy Editing</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}digital-content-services/index.html"><i class="fas fa-file-alt"></i>Digital Content</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}ebook-conversion-services/index.html"><i class="fas fa-tablet-alt"></i>eBook Conversion</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}alt-text-writing-services/index.html"><i class="fas fa-image"></i>Alt Text Writing</a></li>
                <h6 class="dropdown-header mt-2"><i class="fas fa-tags"></i>Data Annotation</h6>
                <li><a class="dropdown-item" href="{{PREFIX}}image-annotation-services/index.html"><i class="fas fa-image"></i>Image Annotation</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}audio-annotation-services/index.html"><i class="fas fa-microphone"></i>Audio Annotation</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}video-annotation-services/index.html"><i class="fas fa-video"></i>Video Annotation</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}text-annotation-services/index.html"><i class="fas fa-font"></i>Text Annotation</a></li>
              </div>
              <!-- Col 2 -->
              <div>
                <h6 class="dropdown-header"><i class="fas fa-heartbeat"></i>Health Care</h6>
                <li><a class="dropdown-item" href="{{PREFIX}}medical-coding-services/index.html"><i class="fas fa-code"></i>Medical Coding</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}medical-billing-services/index.html"><i class="fas fa-file-invoice-dollar"></i>Medical Billing</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}payment-posting-services/index.html"><i class="fas fa-credit-card"></i>Payment Posting</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}denial-management-services/index.html"><i class="fas fa-ban"></i>Denial Management</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}ar-recovery-services/index.html"><i class="fas fa-undo"></i>AR Recovery</a></li>
                <h6 class="dropdown-header mt-2"><i class="fas fa-building"></i>Real Estate</h6>
                <li><a class="dropdown-item" href="{{PREFIX}}cam-audit-services/index.html"><i class="fas fa-search-dollar"></i>CAM Audit</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}cam-reconciliation-services/index.html"><i class="fas fa-balance-scale"></i>CAM Reconciliation</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}lease-administration-services/index.html"><i class="fas fa-file-contract"></i>Lease Administration</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}lease-abstraction-services/index.html"><i class="fas fa-scroll"></i>Lease Abstraction</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}property-accounting-services/index.html"><i class="fas fa-calculator"></i>Property Accounting</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}property-management-service/index.html"><i class="fas fa-building"></i>Property Management</a></li>
              </div>
              <!-- Col 3 -->
              <div>
                <h6 class="dropdown-header"><i class="fas fa-calculator"></i>Accounting</h6>
                <li><a class="dropdown-item" href="{{PREFIX}}bookkeeping-services/index.html"><i class="fas fa-book-open"></i>Bookkeeping</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}payroll-processing-services/index.html"><i class="fas fa-money-check-alt"></i>Payroll Processing</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}manpower-payroll-services/index.html"><i class="fas fa-users-cog"></i>Manpower &amp; Payroll</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}financial-reporting-services/index.html"><i class="fas fa-chart-bar"></i>Financial Reporting</a></li>
                <h6 class="dropdown-header mt-2"><i class="fas fa-laptop-code"></i>IT &amp; Digital</h6>
                <li><a class="dropdown-item" href="{{PREFIX}}software-solutions/index.html"><i class="fas fa-laptop-code"></i>Software Solutions</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}digital-marketing-service/index.html"><i class="fas fa-bullhorn"></i>Digital Marketing</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}ecommerce-solutions/index.html"><i class="fas fa-shopping-cart"></i>E-Commerce</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}data-analytics-as-a-service/index.html"><i class="fas fa-chart-line"></i>Data Analytics</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}crm-software-services/index.html"><i class="fas fa-users"></i>CRM Services</a></li>
              </div>
              <!-- Col 4 -->
              <div>
                <h6 class="dropdown-header"><i class="fas fa-th-large"></i>More Services</h6>
                <li><a class="dropdown-item" href="{{PREFIX}}logistics-services/index.html"><i class="fas fa-truck"></i>Logistics</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}title-settlement/index.html"><i class="fas fa-home"></i>Title &amp; Settlement</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}mortgage-escrow/index.html"><i class="fas fa-landmark"></i>Mortgage &amp; Escrow</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}background-verification-service/index.html"><i class="fas fa-user-check"></i>Background Verification</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}technical-publication-service/index.html"><i class="fas fa-file-code"></i>Technical Publication</a></li>
                <li><a class="dropdown-item" href="{{PREFIX}}technical-writing/index.html"><i class="fas fa-pen-nib"></i>Technical Writing</a></li>
                <li>
                  <a class="vs-dd-all" href="{{PREFIX}}service.html">
                    <i class="fas fa-th-large"></i>View All 65+ Services →
                  </a>
                </li>
                <div class="vs-dd-card">
                  <div class="vs-dd-card-avail">Available 24/7</div>
                  <a href="mailto:support@vortexsoftinnovations.com" class="vs-dd-card-row"><i class="fas fa-envelope"></i>support@vortexsoftinnovations.com</a>
                  <a href="tel:+918308906690" class="vs-dd-card-row"><i class="fas fa-phone-alt"></i>+91-8308906690</a>
                </div>
              </div>
            </div>
          </ul>
        </li>

        <li class="nav-item"><a class="nav-link" href="{{PREFIX}}careers.html">Careers</a></li>
        <li class="nav-item"><a class="nav-link" href="{{PREFIX}}blog.html">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="{{PREFIX}}contact.html">Contact</a></li>
      </ul>

      <!-- Phone block -->
      <div class="vs-nav-phones d-none d-xl-flex">
        <a href="tel:+918308906690" class="vs-nav-ph-row">
          <div class="vs-nav-ph-icon"><i class="fas fa-phone-alt"></i></div>
          <span class="vs-nav-ph-num">🇮🇳 +91-8308906690</span>
        </a>
        <a href="tel:+13072050681" class="vs-nav-ph-row">
          <div class="vs-nav-ph-icon"><i class="fas fa-phone-alt"></i></div>
          <span class="vs-nav-ph-num">🇺🇸 +1-307-205-0681</span>
        </a>
        <span class="vs-nav-avail">Available 24/7</span>
      </div>

      <!-- CTA -->
      <a href="{{PREFIX}}contact.html"
         class="nav-link nav-cta d-none d-lg-inline-flex magnetic">
        <i class="fas fa-paper-plane"></i> Get Free Quote
      </a>
    </div>
  </div>
</nav>
`;