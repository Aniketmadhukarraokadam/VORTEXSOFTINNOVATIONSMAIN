<?php
/**
 * ═══════════════════════════════════════════════════════════════════
 * Vortexsoft Innovations — Enterprise Services Directory (service.php)
 * Premium Editorial Layout, Real-Time Interactive Canvas & Motion
 * ═══════════════════════════════════════════════════════════════════
 */

$page_title   = 'Enterprise IT, BPO & AI Services | Vortexsoft Group';
$page_desc    = 'Explore enterprise technology, BPO operations, healthcare RCM, publishing, AI automation, and workforce solutions delivered by 200+ professionals globally.';
$canonical_url = 'https://www.vortexsoftinnovations.com/service.php';
$prefix       = './';

// Inject dedicated services stylesheet
$extra_head = '<link rel="stylesheet" href="./assets/css/services.css?v=' . time() . '">';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ═══════ 1. HERO SECTION ═══════ -->
<section class="services-hero">
  <canvas id="services-hero-canvas"></canvas>
  <div class="services-hero-glow"></div>
  <div class="services-hero-grid"></div>

  <div class="container">
    <div class="services-hero-content">
      <div class="services-hero-eyebrow">
        <span>Our Services</span>
      </div>
      <h1 class="services-hero-title">
        Technology, Operations &amp;<br>
        <span class="highlight-cyan">Intelligence — Working Together.</span>
      </h1>
      <p class="services-hero-desc">
        From AI-powered automation and enterprise software to BPO, publishing, healthcare, logistics, and technical content, Vortexsoft combines technology and operational expertise to deliver scalable business solutions.
      </p>
      <div class="services-hero-actions">
        <a href="#services-showcase" class="btn-svc-primary">
          <i class="fas fa-layer-group"></i> Explore Services
        </a>
        <a href="contact.php" class="btn-svc-secondary">
          <i class="fas fa-comments"></i> Talk to Our Team
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ═══════ 2. STICKY CATEGORY NAVIGATION ═══════ -->
<nav class="svc-nav-wrapper" id="svcNavWrapper" aria-label="Services Category Filter">
  <div class="container">
    <div class="svc-nav-scroll">
      <a href="#svc-healthcare" class="svc-nav-pill active"><i class="fas fa-heartbeat"></i> Healthcare BPO</a>
      <a href="#svc-realestate" class="svc-nav-pill"><i class="fas fa-building"></i> Real Estate</a>
      <a href="#svc-publishing" class="svc-nav-pill"><i class="fas fa-book"></i> STM Publishing</a>
      <a href="#svc-ai-automation" class="svc-nav-pill"><i class="fas fa-robot"></i> AI &amp; Automation</a>
      <a href="#svc-software" class="svc-nav-pill"><i class="fas fa-laptop-code"></i> Custom Software</a>
      <a href="#svc-erp" class="svc-nav-pill"><i class="fas fa-network-wired"></i> ERP &amp; SAP</a>
      <a href="#svc-martech" class="svc-nav-pill"><i class="fas fa-bullhorn"></i> MarTech</a>
      <a href="#svc-accounting" class="svc-nav-pill"><i class="fas fa-calculator"></i> Accounting BPO</a>
      <a href="#svc-logistics" class="svc-nav-pill"><i class="fas fa-truck"></i> Logistics BPO</a>
      <a href="#svc-techpub" class="svc-nav-pill"><i class="fas fa-file-alt"></i> Tech Publications</a>
    </div>
  </div>
</nav>

<!-- ═══════ 3. INTERACTIVE SERVICES SHOWCASE ═══════ -->
<section class="svc-showcase-section" id="services-showcase">
  <div class="container">

    <!-- GEO / AEO Citable Knowledge Block -->
    <div class="mb-5">
      <?= render_geo_fact_block() ?>
    </div>

    <div class="svc-editorial-grid">

      <!-- ── 01. Healthcare BPO & Revenue Cycle Management ── -->
      <article class="svc-card" id="svc-healthcare">
        <div class="svc-card-inner">
          <div class="svc-content-wrap">
            <span class="svc-watermark-number">01</span>
            <div class="svc-tag-row">
              <span class="svc-icon-inline" style="background:rgba(204,34,40,0.08);color:#CC2228;"><i class="fas fa-heartbeat"></i></span>
              <span class="svc-category-badge">Healthcare Operations</span>
            </div>
            <h2 class="svc-card-title">Healthcare BPO &amp; Revenue Cycle Management</h2>
            <p class="svc-card-desc">
              End-to-end HIPAA-compliant revenue cycle management and medical billing operations designed to accelerate cash flow, eliminate claim denial backlogs, and maximize net collections.
            </p>
            <ul class="svc-capabilities-list">
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Medical Coding (ICD-10-CM, CPT-4, HCPCS Level II)</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Revenue Cycle Management &amp; AR Recovery</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Claims Denial Management &amp; Structured Appeals</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Provider Credentialing &amp; Prior Authorization</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Payment Posting &amp; Electronic Remittance Advice</li>
            </ul>
            <div>
              <a href="health-care-services/index.php" class="svc-explore-link">Explore Healthcare Services <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>

          <div class="svc-media-wrap">
            <img src="assets/images/service-healthcare.jpg" alt="Healthcare medical records and claims documentation workspace" class="svc-media-img" loading="lazy">
            <canvas id="healthcare-telemetry-canvas" class="svc-canvas-overlay"></canvas>
            <div class="svc-media-badge">
              <span class="svc-pulse-dot"></span>
              <span>HIPAA Compliant RCM</span>
            </div>
          </div>
        </div>
      </article>

      <!-- ── 02. Real Estate, Title & Settlement Services ── -->
      <article class="svc-card svc-card-reverse" id="svc-realestate">
        <div class="svc-card-inner">
          <div class="svc-media-wrap">
            <img src="assets/images/service-realestate.jpg" alt="Commercial real estate blueprints and title documents" class="svc-media-img" loading="lazy">
            <div class="svc-media-badge">
              <span class="svc-pulse-dot" style="background:#10B981;box-shadow:0 0 8px #10B981;"></span>
              <span>Title &amp; CAM Reconciliation</span>
            </div>
          </div>

          <div class="svc-content-wrap">
            <span class="svc-watermark-number">02</span>
            <div class="svc-tag-row">
              <span class="svc-icon-inline" style="background:rgba(16,185,129,0.08);color:#10B981;"><i class="fas fa-building"></i></span>
              <span class="svc-category-badge" style="color:#10B981;background:rgba(16,185,129,0.08);">Commercial Property &amp; Title</span>
            </div>
            <h2 class="svc-card-title">Real Estate, Title &amp; Settlement Services</h2>
            <p class="svc-card-desc">
              Comprehensive nationwide title examination, commitments, lease administration, and CAM audit reconciliations for property managers, REITs, and settlement agencies.
            </p>
            <ul class="svc-capabilities-list">
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#10B981;"></i> Commercial Lease Administration &amp; Abstraction</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#10B981;"></i> CAM Expense Reconciliation &amp; Operating Audits</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#10B981;"></i> Title Search, Examination &amp; Commitment Typing</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#10B981;"></i> Property Accounting &amp; Rent Roll Verification</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#10B981;"></i> Mortgage Closing &amp; Settlement Operations</li>
            </ul>
            <div>
              <a href="real-estate-services/index.php" class="svc-explore-link">Explore Real Estate Solutions <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </article>

      <!-- ── 03. STM Publishing & Media Prepress ── -->
      <article class="svc-card" id="svc-publishing">
        <div class="svc-card-inner">
          <div class="svc-content-wrap">
            <span class="svc-watermark-number">03</span>
            <div class="svc-tag-row">
              <span class="svc-icon-inline" style="background:rgba(28,34,128,0.08);color:#1C2280;"><i class="fas fa-book"></i></span>
              <span class="svc-category-badge">Editorial &amp; Prepress</span>
            </div>
            <h2 class="svc-card-title">STM Publishing &amp; Media Prepress</h2>
            <p class="svc-card-desc">
              High-accuracy scientific, technical, and medical publishing production — delivering automated journal typesetting, XML restructuring, and WCAG accessibility standards.
            </p>
            <ul class="svc-capabilities-list">
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Academic Journal &amp; STM Book Typesetting</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> ePUB3, Fixed Layout &amp; JATS/BITS XML Conversion</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Alt-Text Writing &amp; Complex STEM Image Description</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> WCAG 2.1 AA PDF &amp; eBook Accessibility Tagging</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Technical Copyediting &amp; Prepress Proofreading</li>
            </ul>
            <div>
              <a href="publishing-services/index.php" class="svc-explore-link">Explore Publishing Services <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>

          <div class="svc-media-wrap">
            <img src="assets/images/service-publishing.jpg" alt="Academic scientific publishing and editorial proofing workspace" class="svc-media-img" loading="lazy">
            <div class="svc-media-badge">
              <span class="svc-pulse-dot" style="background:#5BA8D4;box-shadow:0 0 8px #5BA8D4;"></span>
              <span>ePUB3 &amp; WCAG 2.1 AA</span>
            </div>
          </div>
        </div>
      </article>

      <!-- ── 04. FEATURED FULL-WIDTH SECTION: AI & Intelligent Automation ── -->
      <article class="svc-featured-ai-card" id="svc-ai-automation">
        <canvas id="ai-routing-canvas"></canvas>
        <div class="svc-card-inner">
          <div class="svc-content-wrap" style="position:relative;z-index:3;">
            <span class="svc-watermark-number">04</span>
            <div class="svc-tag-row">
              <span class="svc-icon-inline" style="background:rgba(91,168,212,0.15);color:#5BA8D4;"><i class="fas fa-robot"></i></span>
              <span class="svc-category-badge" style="background:rgba(91,168,212,0.15);color:#5BA8D4;border:1px solid rgba(91,168,212,0.3);">Featured AI Platform</span>
            </div>
            <h2 class="svc-card-title">AI &amp; Intelligent Automation</h2>
            <p class="svc-card-desc">
              Automate complex multi-step business workflows with autonomous agentic architectures, Intelligent Document Processing (IDP), and high-precision AI training datasets.
            </p>
            <ul class="svc-capabilities-list">
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Custom Autonomous AI Agents &amp; Workflow Orchestration</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Intelligent Document Processing (IDP) for Invoices &amp; EHR</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Precision Image, Video, Text &amp; LiDAR Annotation</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Robotic Process Automation (RPA) for Repetitive Tasks</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Human-in-the-Loop Validation &amp; Quality Benchmarking</li>
            </ul>
            <div>
              <a href="data-annotation-services/index.php" class="svc-explore-link">Explore AI &amp; Automation <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>

          <div class="svc-media-wrap" style="background:transparent;">
            <img src="assets/images/service-ai-automation.jpg" alt="Autonomous AI pipeline and intelligent workflow system" class="svc-media-img" loading="lazy">
            <div class="svc-media-badge" style="background:rgba(8,11,26,0.85);border-color:#5BA8D4;">
              <span class="svc-pulse-dot" style="background:#5BA8D4;box-shadow:0 0 10px #5BA8D4;"></span>
              <span>Agentic AI Core</span>
            </div>
          </div>
        </div>
      </article>

      <!-- ── 05. Custom Software & Business Portals ── -->
      <article class="svc-card" id="svc-software">
        <div class="svc-card-inner">
          <div class="svc-content-wrap">
            <span class="svc-watermark-number">05</span>
            <div class="svc-tag-row">
              <span class="svc-icon-inline" style="background:rgba(91,168,212,0.08);color:#5BA8D4;"><i class="fas fa-laptop-code"></i></span>
              <span class="svc-category-badge">Software Engineering</span>
            </div>
            <h2 class="svc-card-title">Custom Software &amp; Business Portals</h2>
            <p class="svc-card-desc">
              Tailor-made cloud applications, executive dashboards, client portals, and secure enterprise software architectures designed to scale your operations smoothly.
            </p>
            <ul class="svc-capabilities-list">
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Custom Web Applications &amp; Enterprise SaaS Engineering</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Real-Time Executive Dashboards &amp; Reporting Portals</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Customer &amp; Vendor Self-Service Management Systems</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Microservices &amp; Secure RESTful API Development</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Cloud Migration &amp; Infrastructure Optimization</li>
            </ul>
            <div>
              <a href="software-solutions/index.php" class="svc-explore-link">Explore Software Solutions <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>

          <div class="svc-media-wrap">
            <img src="assets/images/service-software.jpg" alt="Enterprise custom software engineering workstation" class="svc-media-img" loading="lazy">
            <div class="svc-media-badge">
              <span class="svc-pulse-dot" style="background:#5BA8D4;box-shadow:0 0 8px #5BA8D4;"></span>
              <span>Full-Stack &amp; Cloud</span>
            </div>
          </div>
        </div>
      </article>

      <!-- ── 06. ERP & SAP Enterprise Solutions ── -->
      <article class="svc-card svc-card-reverse" id="svc-erp">
        <div class="svc-card-inner">
          <div class="svc-media-wrap">
            <img src="assets/images/service-erp.jpg" alt="Enterprise operational systems and manufacturing integration" class="svc-media-img" loading="lazy">
            <div class="svc-media-badge">
              <span class="svc-pulse-dot" style="background:#1C2280;box-shadow:0 0 8px #1C2280;"></span>
              <span>SAP &amp; ERP Integration</span>
            </div>
          </div>

          <div class="svc-content-wrap">
            <span class="svc-watermark-number">06</span>
            <div class="svc-tag-row">
              <span class="svc-icon-inline" style="background:rgba(28,34,128,0.08);color:#1C2280;"><i class="fas fa-network-wired"></i></span>
              <span class="svc-category-badge">Enterprise Systems</span>
            </div>
            <h2 class="svc-card-title">ERP &amp; SAP Enterprise Solutions</h2>
            <p class="svc-card-desc">
              End-to-end ERP implementation, module customization, and SAP integration to unify inventory, financial, HR, and supply chain data into single synchronized workflows.
            </p>
            <ul class="svc-capabilities-list">
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Enterprise ERP Deployment &amp; Legacy Modernization</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> SAP Consulting, Integration &amp; Cloud Migration</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Cross-Departmental Business Workflow Automation</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Custom Enterprise Module Development &amp; Support</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Data Synchronization &amp; Enterprise Data Warehousing</li>
            </ul>
            <div>
              <a href="software-solutions/index.php" class="svc-explore-link">Explore ERP Solutions <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </article>

      <!-- ── 07. Marketing Automation & MarTech ── -->
      <article class="svc-card" id="svc-martech">
        <div class="svc-card-inner">
          <div class="svc-content-wrap">
            <span class="svc-watermark-number">07</span>
            <div class="svc-tag-row">
              <span class="svc-icon-inline" style="background:rgba(245,158,11,0.08);color:#F59E0B;"><i class="fas fa-bullhorn"></i></span>
              <span class="svc-category-badge" style="color:#F59E0B;background:rgba(245,158,11,0.08);">Marketing Technology</span>
            </div>
            <h2 class="svc-card-title">Marketing Automation &amp; MarTech</h2>
            <p class="svc-card-desc">
              Automated multi-channel conversion funnels, CRM integration, lead nurturing sequences, and data-driven marketing analytics that fuel customer acquisition.
            </p>
            <ul class="svc-capabilities-list">
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#F59E0B;"></i> Automated Lead Generation &amp; Scoring Pipelines</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#F59E0B;"></i> CRM &amp; Dynamic Email Nurturing Sequences</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#F59E0B;"></i> Omnichannel Campaign Tracking &amp; Attribution</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#F59E0B;"></i> Customer Lifecycle &amp; Retention Workflows</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#F59E0B;"></i> Real-Time MarTech Performance Dashboards</li>
            </ul>
            <div>
              <a href="digital-marketing-service/index.php" class="svc-explore-link">Explore MarTech Services <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>

          <div class="svc-media-wrap">
            <img src="assets/images/service-martech.jpg" alt="Marketing technology conversion funnel and acquisition pipeline" class="svc-media-img" loading="lazy">
            <div class="svc-media-badge">
              <span class="svc-pulse-dot" style="background:#F59E0B;box-shadow:0 0 8px #F59E0B;"></span>
              <span>Growth Funnel</span>
            </div>
          </div>
        </div>
      </article>

      <!-- ── 08. Accounting & Financial BPO ── -->
      <article class="svc-card svc-card-reverse" id="svc-accounting">
        <div class="svc-card-inner">
          <div class="svc-media-wrap">
            <img src="assets/images/service-accounting.jpg" alt="Professional accounting ledgers and financial management" class="svc-media-img" loading="lazy">
            <div class="svc-media-badge">
              <span class="svc-pulse-dot" style="background:#8B5CF6;box-shadow:0 0 8px #8B5CF6;"></span>
              <span>Cost-Effective BPO</span>
            </div>
          </div>

          <div class="svc-content-wrap">
            <span class="svc-watermark-number">08</span>
            <div class="svc-tag-row">
              <span class="svc-icon-inline" style="background:rgba(139,92,246,0.08);color:#8B5CF6;"><i class="fas fa-calculator"></i></span>
              <span class="svc-category-badge" style="color:#8B5CF6;background:rgba(139,92,246,0.08);">Finance &amp; Accounting</span>
            </div>
            <h2 class="svc-card-title">Accounting &amp; Financial BPO</h2>
            <p class="svc-card-desc">
              Accurate general ledger bookkeeping, payroll processing, accounts payable/receivable, and compliance reporting delivering up to 60% operational cost savings.
            </p>
            <ul class="svc-capabilities-list">
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#8B5CF6;"></i> Full-Cycle Bookkeeping &amp; General Ledger Management</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#8B5CF6;"></i> Multi-State Payroll Processing &amp; Tax Compliance</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#8B5CF6;"></i> Accounts Payable (AP) &amp; Receivable (AR) Management</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#8B5CF6;"></i> Bank Reconciliation &amp; Anomaly Detection</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#8B5CF6;"></i> Monthly Financial Statements &amp; Executive Reporting</li>
            </ul>
            <div>
              <a href="accounting-services/index.php" class="svc-explore-link">Explore Accounting Services <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </article>

      <!-- ── 09. Logistics & Supply Chain Operations ── -->
      <article class="svc-card" id="svc-logistics">
        <div class="svc-card-inner">
          <div class="svc-content-wrap">
            <span class="svc-watermark-number">09</span>
            <div class="svc-tag-row">
              <span class="svc-icon-inline" style="background:rgba(236,72,153,0.08);color:#EC4899;"><i class="fas fa-truck"></i></span>
              <span class="svc-category-badge" style="color:#EC4899;background:rgba(236,72,153,0.08);">Supply Chain &amp; Freight</span>
            </div>
            <h2 class="svc-card-title">Logistics &amp; Supply Chain Operations</h2>
            <p class="svc-card-desc">
              Freight document entry, bill of lading validation, inventory tracking, and carrier coordination keeping your supply chain lean and transparent 24/7.
            </p>
            <ul class="svc-capabilities-list">
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#EC4899;"></i> Bill of Lading (BOL) Verification &amp; Data Entry</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#EC4899;"></i> Freight Auditing &amp; Carrier Invoice Reconciliation</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#EC4899;"></i> Warehouse Inventory Tracking &amp; SKUs Indexing</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#EC4899;"></i> Real-Time Shipment Tracking &amp; Exception Handling</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle" style="color:#EC4899;"></i> Global Logistics Performance Analytics</li>
            </ul>
            <div>
              <a href="logistics-services/index.php" class="svc-explore-link">Explore Logistics Services <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>

          <div class="svc-media-wrap">
            <img src="assets/images/service-erp.jpg" alt="Logistics warehouse operations and freight distribution system" class="svc-media-img" loading="lazy">
            <div class="svc-media-badge">
              <span class="svc-pulse-dot" style="background:#EC4899;box-shadow:0 0 8px #EC4899;"></span>
              <span>24/7 Supply Chain</span>
            </div>
          </div>
        </div>
      </article>

      <!-- ── 10. Technical Publications & S1000D ── -->
      <article class="svc-card svc-card-reverse" id="svc-techpub">
        <div class="svc-card-inner">
          <div class="svc-media-wrap">
            <img src="assets/images/service-publishing.jpg" alt="Aerospace engineering documentation and technical publication manuals" class="svc-media-img" loading="lazy">
            <div class="svc-media-badge">
              <span class="svc-pulse-dot" style="background:#CC2228;box-shadow:0 0 8px #CC2228;"></span>
              <span>S1000D &amp; DITA XML</span>
            </div>
          </div>

          <div class="svc-content-wrap">
            <span class="svc-watermark-number">10</span>
            <div class="svc-tag-row">
              <span class="svc-icon-inline" style="background:rgba(204,34,40,0.08);color:#CC2228;"><i class="fas fa-file-alt"></i></span>
              <span class="svc-category-badge">Aerospace &amp; Engineering</span>
            </div>
            <h2 class="svc-card-title">Technical Publications &amp; S1000D</h2>
            <p class="svc-card-desc">
              Defense, aerospace, and heavy equipment technical documentation authoring, S1000D/DITA XML conversion, and Illustrated Parts Catalogs (IPC) compliant with global standards.
            </p>
            <ul class="svc-capabilities-list">
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> S1000D Issue 4.1/4.2 &amp; DITA XML Modular Authoring</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Illustrated Parts Catalogs (IPC) &amp; 2D/3D Schematics</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Maintenance Manuals (CMM, AMM, SRM) Authoring</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Interactive Electronic Technical Publications (IETP)</li>
              <li class="svc-capability-item"><i class="fas fa-check-circle"></i> Multi-Lingual Technical Translation &amp; Localization</li>
            </ul>
            <div>
              <a href="technical-publication-service/index.php" class="svc-explore-link">Explore Technical Publications <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </article>

    </div>
  </div>
</section>

<!-- ═══════ 4. CAPABILITIES & GLOBAL SCALE METRICS ═══════ -->
<section class="svc-capabilities-section">
  <div class="container">
    <div class="row g-4">
      <div class="col-6 col-lg-3">
        <div class="svc-stat-card">
          <div class="svc-stat-num">150<span>+</span></div>
          <div class="svc-stat-label">Global Clients</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="svc-stat-card">
          <div class="svc-stat-num">200<span>+</span></div>
          <div class="svc-stat-label">Projects Delivered</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="svc-stat-card">
          <div class="svc-stat-num">200<span>+</span></div>
          <div class="svc-stat-label">Professionals</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="svc-stat-card">
          <div class="svc-stat-num">6<span>+</span></div>
          <div class="svc-stat-label">Years of Excellence</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════ 5. CINEMATIC CALL TO ACTION ═══════ -->
<section class="svc-cta-section">
  <div class="svc-cta-glow"></div>
  <div class="container position-relative" style="z-index: 2;">
    <h2 class="svc-cta-title">
      Have a complex business process?<br>
      Let's simplify it.
    </h2>
    <p class="svc-cta-desc">
      Tell us what you're trying to improve, automate or scale. Our team can help design the right technology and operations solution.
    </p>
    <div class="d-flex justify-content-center gap-3 flex-wrap">
      <a href="contact.php" class="btn-svc-primary">
        <i class="fas fa-paper-plane"></i> Start a Conversation
      </a>
      <a href="about.php" class="btn-svc-secondary">
        <i class="fas fa-building"></i> Explore Our Company
      </a>
    </div>
  </div>
</section>

<!-- Service Interactive Scripts -->
<script src="./assets/js/services.js?v=<?= time() ?>"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
