<?php
/**
 * Vortexsoft Innovations — Master Home Page (index.php)
 * Premium Enterprise UI/UX, Unified Global Design System & High-Performance Interactivity
 */

$page_title   = 'Top Global IT & BPO Company | AI Solutions | Vortexsoft Group';
$page_desc    = 'Vortexsoft Group is an ISO 27001 certified global IT & BPO outsourcing partner specializing in AI solutions, Healthcare BPO, Publishing, and Real Estate.';
$canonical_url = 'https://www.vortexsoftinnovations.com/';
$prefix       = './';

$extra_head = '
<link rel="stylesheet" href="./assets/css/home-3d.css">
<script type="application/ld+json">
{"@context":"https://schema.org","@graph":[{"@type":["Organization","LocalBusiness","ProfessionalService"],"@id":"https://www.vortexsoftinnovations.com/#organization","name":"Vortexsoft Group","alternateName":["Vortexsoft Innovations Pvt. Ltd.","Vortexsoft","Vortex Soft","Vortex Innovations","Vortex Group"],"url":"https://www.vortexsoftinnovations.com","logo":{"@type":"ImageObject","url":"https://www.vortexsoftinnovations.com/logo-header.png","width":"400","height":"100"},"description":"Vortexsoft Group is an ISO 27001:2013 certified global IT outsourcing and BPO company founded in 2020, headquartered in Pune, India.","foundingDate":"2020","numberOfEmployees":{"@type":"QuantitativeValue","value":"500"},"slogan":"Your Global AI, IT & BPO Partner","telephone":["+91-8308906690","+1-307-205-0681"],"email":"support@vortexsoftinnovations.com","openingHours":"Mo-Sa 09:00-18:00","award":["ISO 27001:2013 Certified","Startup India Registered"],"sameAs":["https://www.linkedin.com/company/vortexsoft-innovations-private-limited/","https://www.instagram.com/vortexsoft_innovations","https://www.facebook.com/profile.php?id=61575505273718"]}]}
</script>
';

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ═══════ 1. CINEMATIC HERO SECTION ═══════ -->
<section class="page-hero" id="home">
  <div class="page-hero-glow"></div>
  <div class="container" style="position:relative;z-index:2;">
    <div class="row align-items-center gy-5">
      
      <!-- Hero Left: Value Proposition -->
      <div class="col-lg-7">
        <div class="section-tag mb-3">
          ISO 27001 Certified · 6+ Years of Global Excellence
        </div>
        <h1 style="font-size:clamp(2.1rem,4.2vw,3.6rem);font-weight:800;color:#fff;line-height:1.15;letter-spacing:-0.03em;margin-bottom:20px;">
          Your Global Partner for<br>
          <span style="background:linear-gradient(135deg,#FFFFFF 30%,#5BA8D4 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
            AI, IT &amp; BPO Excellence
          </span>
        </h1>
        <p style="font-size:16px;color:rgba(255,255,255,0.78);line-height:1.75;max-width:580px;margin-bottom:32px;">
          Vortexsoft Group delivers 75+ specialized enterprise services — uniting intelligent AI automation, custom software development, healthcare BPO, STM publishing, and real estate operations for <strong style="color:#fff;">150+ global clients</strong>.
        </p>
        <div class="d-flex flex-wrap gap-3 mb-4">
          <a href="contact.php" class="btn-primary-custom">
            <i class="fas fa-paper-plane"></i> Get Free Consultation
          </a>
          <a href="service.php" class="btn-hero-secondary">
            <i class="fas fa-th-large"></i> Explore All Services
          </a>
        </div>
        <!-- Certifications Bar -->
        <div class="d-flex flex-wrap gap-2 align-items-center pt-2">
          <?php foreach([
            ['fa-shield-alt','ISO 27001:2013','#10B981'],
            ['fa-heartbeat','HIPAA Compliant','#CC2228'],
            ['fa-certificate','Startup India','#F59E0B'],
            ['fa-globe','Pune · Bengaluru · USA','#5BA8D4'],
          ] as [$ico,$label,$col]): ?>
          <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:100px;padding:5px 14px;font-size:12px;font-weight:600;color:rgba(255,255,255,0.85);">
            <i class="fas <?= $ico ?>" style="color:<?= $col ?>;font-size:11px;"></i> <?= $label ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Hero Right: Key Metrics & Service Quick-Links -->
      <div class="col-lg-5">
        <div class="row g-3">
          <?php foreach([
            ['6+','Years of Experience','fa-calendar-check','#5BA8D4'],
            ['200+','Projects Delivered','fa-rocket','#CC2228'],
            ['150+','Global Happy Clients','fa-handshake','#10B981'],
            ['200+','Domain Specialists','fa-users','#F59E0B'],
          ] as [$num,$label,$ico,$col]): ?>
          <div class="col-6">
            <div style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:18px;padding:22px 18px;backdrop-filter:blur(12px);transition:transform 0.3s ease,background 0.3s ease;">
              <div style="width:38px;height:38px;border-radius:10px;background:<?= $col ?>22;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                <i class="fas <?= $ico ?>" style="color:<?= $col ?>;font-size:16px;"></i>
              </div>
              <div style="font-family:'Poppins',sans-serif;font-size:1.85rem;font-weight:800;color:#fff;line-height:1;margin-bottom:6px;"><?= $num ?></div>
              <div style="font-size:11.5px;font-weight:600;color:rgba(255,255,255,0.6);text-transform:uppercase;letter-spacing:0.5px;line-height:1.35;"><?= $label ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <div style="margin-top:16px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:16px 20px;">
          <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,0.4);margin-bottom:10px;">Popular Service Hubs</div>
          <div class="d-flex flex-wrap gap-2">
            <?php foreach(['Healthcare BPO','AI Automation','Custom Software','Publishing','Real Estate','Accounting BPO','Digital Marketing','Data Annotation'] as $s): ?>
            <a href="service.php" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:100px;padding:4px 12px;font-size:12px;font-weight:500;color:rgba(255,255,255,0.75);text-decoration:none;">
              <?= $s ?>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ═══════ 2. OUR EXPERTISE — 6 INTERACTIVE 3D ANIMATED CANVAS CARDS ═══════ -->
<section class="section-pad" id="services" style="background:#F8FAFC;">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-tag"><i class="fas fa-layer-group"></i> Core Capabilities</div>
      <h2 class="section-title">Services Built for <span class="highlight">Real-World Impact</span></h2>
      <div class="section-divider"></div>
      <p class="section-subtitle">From intelligent AI automation pipelines to specialized high-accuracy BPO operations — engineered for scale, reliability, and precision.</p>
    </div>

    <div class="row g-4 mb-4">

      <!-- 1: Healthcare BPO -->
      <div class="col-lg-4 col-md-6">
        <div class="svc-animated-card" data-accent="#CC2228">
          <div class="svc-canvas-wrap">
            <canvas id="svc-canvas-healthcare"></canvas>
            <div class="svc-canvas-badge" style="border-color:rgba(204,34,40,0.4);color:#CC2228;">HIPAA Compliant</div>
          </div>
          <div class="svc-card-body">
            <div class="svc-icon-row">
              <div class="svc-icon-circle" style="background:rgba(204,34,40,0.08);color:#CC2228;"><i class="fas fa-heartbeat"></i></div>
              <div>
                <h3 class="svc-title">Healthcare BPO</h3>
                <span class="svc-sub">Medical Revenue Cycle</span>
              </div>
            </div>
            <p class="svc-desc">End-to-end HIPAA-compliant medical coding, claims billing, AR recovery, denial appeals, and prior authorization operations.</p>
            <div class="svc-tags"><span>Medical Coding</span><span>Billing &amp; AR</span><span>Denial Mgmt</span></div>
            <a href="health-care-services/index.php" class="svc-cta">Explore Healthcare <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <!-- 2: AI & Automation -->
      <div class="col-lg-4 col-md-6">
        <div class="svc-animated-card svc-card-featured" data-accent="#5BA8D4">
          <div class="svc-canvas-wrap">
            <canvas id="svc-canvas-ai"></canvas>
            <div class="svc-canvas-badge" style="border-color:rgba(91,168,212,0.4);color:#5BA8D4;">Agentic AI · Live</div>
          </div>
          <div class="svc-card-body">
            <div class="svc-icon-row">
              <div class="svc-icon-circle" style="background:rgba(91,168,212,0.08);color:#5BA8D4;"><i class="fas fa-robot"></i></div>
              <div>
                <h3 class="svc-title">AI &amp; Automations</h3>
                <span class="svc-sub">Agentic Workflows · IDP</span>
              </div>
            </div>
            <p class="svc-desc">Autonomous multi-step AI agents, intelligent document processing (IDP), custom LLM/RAG pipelines, and automated business workflows.</p>
            <div class="svc-tags"><span>Agentic AI</span><span>IDP</span><span>Custom LLM</span><span>RPA</span></div>
            <a href="service.php#ai-automation" class="svc-cta" style="color:#2563EB;">Explore AI Solutions <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <!-- 3: Custom Software -->
      <div class="col-lg-4 col-md-6">
        <div class="svc-animated-card" data-accent="#1C2280">
          <div class="svc-canvas-wrap" style="background:#080B1A;">
            <canvas id="svc-canvas-software"></canvas>
            <div class="svc-canvas-badge" style="border-color:rgba(91,168,212,0.4);color:#5BA8D4;">Cloud &amp; Fullstack</div>
          </div>
          <div class="svc-card-body">
            <div class="svc-icon-row">
              <div class="svc-icon-circle" style="background:rgba(28,34,128,0.08);color:#1C2280;"><i class="fas fa-laptop-code"></i></div>
              <div>
                <h3 class="svc-title">IT &amp; Software Dev</h3>
                <span class="svc-sub">Custom Engineering</span>
              </div>
            </div>
            <p class="svc-desc">Scalable modern web applications, mobile platforms, enterprise ERP systems, secure cloud architecture, and API ecosystems.</p>
            <div class="svc-tags"><span>Web Apps</span><span>Mobile</span><span>ERP</span><span>Cloud</span></div>
            <a href="software-solutions/index.php" class="svc-cta" style="color:#1C2280;">Explore Software <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <!-- 4: Publishing -->
      <div class="col-lg-4 col-md-6">
        <div class="svc-animated-card" data-accent="#8B5CF6">
          <div class="svc-canvas-wrap">
            <canvas id="svc-canvas-publishing"></canvas>
            <div class="svc-canvas-badge" style="border-color:rgba(139,92,246,0.4);color:#8B5CF6;">ePUB3 &amp; XML</div>
          </div>
          <div class="svc-card-body">
            <div class="svc-icon-row">
              <div class="svc-icon-circle" style="background:rgba(139,92,246,0.08);color:#8B5CF6;"><i class="fas fa-book-open"></i></div>
              <div>
                <h3 class="svc-title">Publishing &amp; Prepress</h3>
                <span class="svc-sub">STM · ePUB3 · WCAG</span>
              </div>
            </div>
            <p class="svc-desc">Digital prepress, automated typesetting, XML/JATS conversion, WCAG 2.1 AA tagging, and high-volume accessible publishing workflows.</p>
            <div class="svc-tags"><span>ePUB3</span><span>XML/SGML</span><span>WCAG 2.1</span></div>
            <a href="publishing-services/index.php" class="svc-cta" style="color:#8B5CF6;">Explore Publishing <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <!-- 5: Real Estate -->
      <div class="col-lg-4 col-md-6">
        <div class="svc-animated-card" data-accent="#10B981">
          <div class="svc-canvas-wrap">
            <canvas id="svc-canvas-realestate"></canvas>
            <div class="svc-canvas-badge" style="border-color:rgba(16,185,129,0.4);color:#10B981;">Property Tech · 3D</div>
          </div>
          <div class="svc-card-body">
            <div class="svc-icon-row">
              <div class="svc-icon-circle" style="background:rgba(16,185,129,0.08);color:#10B981;"><i class="fas fa-building"></i></div>
              <div>
                <h3 class="svc-title">Real Estate &amp; Title</h3>
                <span class="svc-sub">Property Operations</span>
              </div>
            </div>
            <p class="svc-desc">Title searches, settlement typing, lease abstraction, CAM reconciliation audits, property accounting, and mortgage escrow management.</p>
            <div class="svc-tags"><span>Title Search</span><span>Lease Abstraction</span><span>CAM Audit</span></div>
            <a href="real-estate-services/index.php" class="svc-cta" style="color:#10B981;">Explore Real Estate <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <!-- 6: Enterprise AI & Payroll -->
      <div class="col-lg-4 col-md-6">
        <div class="svc-animated-card svc-card-enterprise" data-accent="#F59E0B">
          <div class="svc-canvas-wrap" style="background:linear-gradient(135deg,#080B1A,#1a1200);">
            <canvas id="svc-canvas-enterprise"></canvas>
            <div class="svc-canvas-badge" style="border-color:rgba(245,158,11,0.5);color:#F59E0B;">Enterprise AI · Auto</div>
          </div>
          <div class="svc-card-body">
            <div class="svc-icon-row">
              <div class="svc-icon-circle" style="background:rgba(245,158,11,0.1);color:#F59E0B;"><i class="fas fa-microchip"></i></div>
              <div>
                <h3 class="svc-title">Enterprise AI &amp; Payroll</h3>
                <span class="svc-sub">AI Platforms · Payroll Automation</span>
              </div>
            </div>
            <p class="svc-desc">Automated payroll computation, tax compliance management, workforce data pipelines, and unified HRMS integrations.</p>
            <div class="svc-tags"><span>Payroll AI</span><span>HR Automation</span><span>Compliance</span><span>ERP</span></div>
            <a href="accounting-services/index.php" class="svc-cta" style="color:#F59E0B;">Explore Payroll <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>

    </div>

    <!-- More Services Strip -->
    <div style="background:#FFFFFF;border:1px solid #E2E8F0;border-radius:20px;padding:24px 28px;margin-bottom:20px;">
      <div class="row align-items-center gy-3">
        <div class="col-lg-3">
          <span class="badge" style="background:rgba(28,34,128,0.08);color:#1C2280;font-size:11px;padding:5px 12px;font-weight:700;border-radius:100px;">+ EXTENDED PORTFOLIO</span>
          <h4 style="font-size:18px;font-weight:800;color:var(--vs-text-heading);margin-top:6px;">65+ Specialized Services</h4>
        </div>
        <div class="col-lg-9">
          <div class="d-flex flex-wrap gap-2">
            <?php foreach(['AI Data Annotation','Logistics & Supply Chain','Digital Marketing & SEO','Staff Augmentation','ERP & SAP Implementation','MarTech Automation','Financial Reporting & Bookkeeping','Content Strategy','eCommerce Engineering','Customer Support BPO','Document Digitization','Cloud Migration'] as $m): ?>
            <span style="background:#F1F5F9;border:1px solid #E2E8F0;border-radius:100px;padding:5px 12px;font-size:12px;font-weight:600;color:#334155;"><?= $m ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center mt-4">
      <a href="service.php" class="btn-primary-custom"><i class="fas fa-th-large"></i> View All 65+ Services</a>
      <a href="contact.php" class="btn ms-3" style="background:transparent;border:2px solid #1C2280;color:#1C2280;border-radius:10px;padding:11px 24px;font-weight:700;font-size:14px;"><i class="fas fa-paper-plane me-1"></i> Request Custom Quote</a>
    </div>
  </div>
</section>

<style>
.svc-animated-card{background:#FFFFFF;border:1px solid #E2E8F0;border-radius:20px;overflow:hidden;height:100%;display:flex;flex-direction:column;transition:transform 0.35s cubic-bezier(0.16,1,0.3,1),box-shadow 0.35s ease,border-color 0.3s ease;position:relative;}
.svc-animated-card:hover{transform:translateY(-6px);box-shadow:0 20px 45px rgba(15,23,42,0.1);border-color:rgba(37,99,235,0.3);}
.svc-card-featured{border-color:rgba(91,168,212,0.35);}
.svc-card-enterprise{border-color:rgba(245,158,11,0.3);}
.svc-canvas-wrap{position:relative;height:150px;background:linear-gradient(135deg,#F8FAFC,#EEF2F6);overflow:hidden;border-bottom:1px solid #E2E8F0;flex-shrink:0;}
.svc-canvas-wrap canvas{display:block;width:100%!important;height:100%!important;position:absolute;top:0;left:0;}
.svc-canvas-badge{position:absolute;top:10px;right:10px;background:rgba(6,9,20,0.75);backdrop-filter:blur(10px);border:1px solid;border-radius:100px;padding:3px 10px;font-size:10px;font-weight:800;letter-spacing:0.5px;text-transform:uppercase;z-index:2;}
.svc-card-body{padding:22px;display:flex;flex-direction:column;flex-grow:1;}
.svc-icon-row{display:flex;align-items:center;gap:12px;margin-bottom:12px;}
.svc-icon-circle{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;transition:transform 0.3s ease;}
.svc-animated-card:hover .svc-icon-circle{transform:scale(1.1) rotate(-4deg);}
.svc-title{font-size:16.5px;font-weight:800;color:var(--vs-text-heading);margin:0 0 2px;line-height:1.25;}
.svc-sub{font-size:11px;font-weight:700;color:var(--vs-text-muted);text-transform:uppercase;letter-spacing:0.5px;}
.svc-desc{font-size:13px;color:var(--vs-text-muted);line-height:1.65;flex-grow:1;margin-bottom:14px;}
.svc-tags{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;}
.svc-tags span{background:#F1F5F9;border-radius:100px;padding:2px 9px;font-size:11px;font-weight:600;color:#475569;}
.svc-cta{display:inline-flex;align-items:center;gap:6px;font-weight:700;font-size:13px;color:var(--vs-blue-primary);text-decoration:none;transition:gap 0.2s ease;}
.svc-cta:hover{gap:9px;}
</style>

<!-- ═══════ 3. STRATEGIC VALUE FRAMEWORK ═══════ -->
<section class="section-pad" style="background:#FFFFFF;">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-tag"><i class="fas fa-cubes"></i> Operating Model</div>
      <h2 class="section-title">Strategic Value <span class="highlight">Framework</span></h2>
      <div class="section-divider"></div>
      <p class="section-subtitle">Combining state-of-the-art technological automation with domain-certified operations and agile global staffing.</p>
    </div>

    <div class="row g-4">
      <?php foreach([
        ['fa-brain','AI + Operations + Humans','Harmonizing autonomous AI agents, machine learning automation, and certified domain specialist oversight to guarantee 99.9% operational accuracy.','#CC2228','rgba(204,34,40,0.08)'],
        ['fa-cubes','Tech + Delivery + Staffing','Integrated full-stack solutions uniting custom software engineering, 24/7 business BPO operations, and on-demand global staffing augmentation.','#1C2280','rgba(28,34,128,0.08)'],
        ['fa-cogs','Intelligent Process Automation','Eliminating operational bottlenecks with Intelligent Document Processing (IDP), Robotic Process Automation (RPA), and automated enterprise workflows.','#5BA8D4','rgba(91,168,212,0.1)'],
        ['fa-rocket','Full Digital Transformation','Accelerating enterprise modernization through custom cloud applications, SAP/ERP integrations, predictive analytics, and marketing technology.','#10B981','rgba(16,185,129,0.1)'],
      ] as [$ico,$title,$desc,$col,$bg]): ?>
      <div class="col-md-6 col-lg-3">
        <div class="vs-card">
          <div class="vs-icon-box" style="background:<?= $bg ?>;color:<?= $col ?>;">
            <i class="fas <?= $ico ?>"></i>
          </div>
          <h4 style="font-size:17px;font-weight:800;margin-bottom:10px;color:var(--vs-text-heading);"><?= $title ?></h4>
          <p style="font-size:13px;color:var(--vs-text-muted);line-height:1.65;margin:0;"><?= $desc ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════ 4. NEXT-GEN AI & AGENTIC AUTOMATIONS ═══════ -->
<section class="section-pad" style="background:linear-gradient(135deg,#060914 0%,#0F1633 55%,#080C1E 100%);color:#FFFFFF;">
  <div class="container">
    <div class="row align-items-center gy-5">
      
      <div class="col-lg-6">
        <div class="section-tag tag-red mb-3">
          <i class="fas fa-robot me-1"></i> Autonomous AI Pipelines
        </div>
        <h2 style="font-size:clamp(1.8rem,3vw,2.7rem);font-weight:800;color:#FFFFFF;line-height:1.2;margin-bottom:16px;">
          Accelerate Operations with <span style="background:linear-gradient(135deg,#FFFFFF 30%,#5BA8D4 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Agentic Workflows</span>
        </h2>
        <p style="color:rgba(255,255,255,0.78);font-size:15px;line-height:1.75;margin-bottom:24px;">
          We build custom, autonomous AI automation pipelines tailored to your industry. From Intelligent Document Processing (IDP) in Healthcare and Real Estate to Agentic AI workflows in Accounting and Publishing — we automate repetitive tasks, cut processing costs by up to 70%, and elevate human accuracy.
        </p>

        <div class="row g-3 mb-4">
          <?php foreach([
            ['fa-brain','Agentic AI Workflows','Multi-step autonomous task execution with human-in-the-loop validation.','#5BA8D4'],
            ['fa-file-invoice','Intelligent Doc Processing','Instant parsing & extraction for medical records, leases, invoices & PDFs.','#FF5A5F'],
            ['fa-tags','AI Training Datasets','High-precision image, video, audio & text annotation for custom ML models.','#F59E0B'],
            ['fa-comments','Custom LLM & RAG','Private enterprise chatbots trained on your confidential knowledge base.','#10B981'],
          ] as [$ico,$title,$desc,$col]): ?>
          <div class="col-sm-6">
            <div style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:14px;padding:16px;">
              <i class="fas <?= $ico ?>" style="color:<?= $col ?>;font-size:18px;margin-bottom:8px;display:block;"></i>
              <h5 style="font-size:14px;font-weight:700;color:#FFFFFF;margin-bottom:4px;"><?= $title ?></h5>
              <p style="font-size:12px;color:rgba(255,255,255,0.65);margin:0;line-height:1.55;"><?= $desc ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <a href="contact.php" class="btn-primary-custom">
          <i class="fas fa-robot"></i> Explore AI Automations for Your Business
        </a>
      </div>

      <div class="col-lg-6">
        <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:20px;padding:24px;position:relative;">
          
          <div class="ai-3d-stage mb-3">
            <canvas id="home-ai-canvas"></canvas>
            <div class="ai-stage-badge">
              <span class="ai-stage-status"><span class="ai-pulse-light"></span> Autonomous Agentic Core</span>
              <span style="background:rgba(91,168,212,0.2);border:1px solid #5BA8D4;color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:100px;">60 FPS Real-time</span>
            </div>
          </div>

          <h4 style="font-size:16px;font-weight:700;color:#FFFFFF;margin-bottom:14px;">Domain AI Implementations</h4>
          <div class="d-flex flex-column gap-2">
            <?php foreach([
              ['fa-heartbeat','Healthcare AI','Automated claim scrubbers, EHR data extraction & prior-auth assistance','#FF5A5F','rgba(204,34,40,0.2)'],
              ['fa-building','Real Estate AI','Automated lease abstraction, CAM audit matching & document indexing','#5BA8D4','rgba(91,168,212,0.2)'],
              ['fa-book-open','Publishing AI','AI alt-text generation, XML auto-tagging & accessibility compliance','#F59E0B','rgba(245,158,11,0.2)'],
              ['fa-calculator','Accounting AI','Automated invoice OCR, bank reconciliation matching & anomaly detection','#10B981','rgba(16,185,129,0.2)'],
            ] as [$ico,$title,$desc,$col,$bg]): ?>
            <div style="display:flex;gap:12px;align-items:center;padding:12px 14px;background:rgba(0,0,0,0.25);border-radius:10px;">
              <div style="width:36px;height:36px;border-radius:8px;background:<?= $bg ?>;color:<?= $col ?>;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;">
                <i class="fas <?= $ico ?>"></i>
              </div>
              <div>
                <strong style="color:#FFFFFF;display:block;font-size:13.5px;"><?= $title ?></strong>
                <span style="font-size:11.5px;color:rgba(255,255,255,0.65);"><?= $desc ?></span>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

        </div>
      </div>

    </div>
  </div>
</section>

<!-- ═══════ 5. WHY GLOBAL LEADERS TRUST VORTEXSOFT ═══════ -->
<section class="section-pad" style="background:#FFFFFF;">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-tag"><i class="fas fa-award"></i> Proven Track Record</div>
      <h2 class="section-title">Why Global Enterprises <span class="highlight">Choose Vortexsoft</span></h2>
      <div class="section-divider"></div>
      <p class="section-subtitle">Delivering high-accuracy offshore execution with stringent security and substantial operational savings.</p>
    </div>

    <div class="row g-4">
      <?php foreach([
        ['fa-shield-alt','ISO 27001:2013','Certified Information Security Management Standards guarding client IP and financial data.','#2563EB'],
        ['fa-clock','24/7 Coverage','Continuous round-the-clock digital support and real-time client account management.','#10B981'],
        ['fa-percentage','40–60% Savings','Significant cost reductions compared to managing in-house operational teams.','#CC2228'],
        ['fa-globe','15+ Global Industries','Specialized multi-industry domain knowledge supporting North America, Europe & APAC.','#F59E0B'],
        ['fa-star','99.9% Quality SLA','Strict multi-tier quality assurance benchmarks guaranteeing error-free deliverables.','#8B5CF6'],
        ['fa-bolt','Rapid Turnaround','Agile delivery pods and dedicated capacity scaling for tight project deadlines.','#06B6D4'],
      ] as [$ico,$title,$desc,$col]): ?>
      <div class="col-lg-4 col-md-6">
        <div class="vs-card text-center align-items-center">
          <div class="vs-icon-box" style="background:<?= $col ?>18;color:<?= $col ?>;">
            <i class="fas <?= $ico ?>"></i>
          </div>
          <h4 style="font-size:17px;font-weight:800;color:var(--vs-text-heading);margin-bottom:8px;"><?= $title ?></h4>
          <p style="font-size:13px;color:var(--vs-text-muted);line-height:1.65;margin:0;"><?= $desc ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════ 6. CLIENT TESTIMONIALS ═══════ -->
<section class="section-pad" style="background:#F8FAFC;" id="testimonials">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-tag"><i class="fas fa-quote-left"></i> Verified Reviews</div>
      <h2 class="section-title">What Our <span class="highlight">Clients Say</span></h2>
      <div class="section-divider"></div>
      <p class="section-subtitle">Real experiences from healthcare providers, STM publishers, asset managers, and technology leaders.</p>
    </div>

    <div class="row g-4">
      <?php foreach([
        ['Vortexsoft transformed our healthcare billing process completely. Their medical coding accuracy is exceptional — we reduced claim denials by 40% within the first quarter.','Dr. Sarah Mitchell','Medical Director, CareFirst Health Group, USA','S'],
        ['Outstanding publishing services. Their ePUB3 conversion and accessibility compliance work saved us weeks of production time. Highly professional team with deep technical expertise.','James Thornton','Head of Production, Oxford Academic Press, UK','J'],
        ['The data annotation quality from Vortexsoft is remarkably precise. Our AI models perform significantly better since switching to their annotation services. Truly reliable partner.','Priya Mehta','ML Lead, TechVision AI, Bengaluru','P'],
        ['Vortexsoft handles our entire property accounting and CAM audit process. Their real estate team is knowledgeable, responsive, and consistently delivers error-free reports.','Robert Davidson','CFO, Horizon Property Group, Florida, USA','R'],
        ['We outsourced our digital marketing to Vortexsoft and saw 3x growth in organic traffic within 6 months. Their SEO and PPC strategy is data-driven and results-focused.','Ananya Sharma','Founder, GrowthEdge Startup, Pune','A'],
        ['Exceptional accounting and payroll services. Vortexsoft handles our entire financial operations seamlessly. Cost savings of over 55% compared to in-house team. Highly recommend.','Michael Chen','Operations Director, GlobalLink Corp, Singapore','M'],
      ] as [$comment,$name,$title,$init]): ?>
      <div class="col-lg-4 col-md-6">
        <div class="vs-card">
          <div class="d-flex align-items-center gap-1 mb-3" style="color:#F59E0B;font-size:13px;">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
          </div>
          <p style="font-size:13.5px;color:var(--vs-text-body);font-style:italic;line-height:1.7;margin-bottom:20px;flex-grow:1;">
            "<?= $comment ?>"
          </p>
          <div class="d-flex align-items-center gap-3 pt-3" style="border-top:1px solid #E2E8F0;">
            <div style="width:40px;height:40px;border-radius:50%;background:var(--vs-grad-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0;">
              <?= $init ?>
            </div>
            <div>
              <div style="font-weight:700;font-size:14px;color:var(--vs-text-heading);"><?= $name ?></div>
              <div style="font-size:11.5px;color:var(--vs-text-muted);"><?= $title ?></div>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════ 7. PROPRIETARY AI PLATFORMS ═══════ -->
<section class="section-pad" style="background:#FFFFFF;" id="ai-products">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-tag"><i class="fas fa-laptop-code"></i> Proprietary Tech</div>
      <h2 class="section-title">Enterprise <span class="highlight">AI Platforms</span></h2>
      <div class="section-divider"></div>
      <p class="section-subtitle">Intelligent workforce, recruiting, speech analytics, and outreach automation built by Vortexsoft.</p>
    </div>

    <div class="row g-4">
      <?php foreach([
        ['VortexEXHO','Enterprise Workforce Operating System','Unified platform combining ATS, HRMS, payroll, LMS, and an AI copilot into one system, built to replace fragmented point solutions for mid-size and enterprise HR operations.','fa-layer-group','#1C2280'],
        ['vortexHire','AI Candidate Screening Platform','Automates candidate sourcing, resume screening, and initial qualification against role requirements — reducing manual screening time by 70%.','fa-user-check','#CC2228'],
        ['vortexKonnect','Call Center AI Speech Analytics','Analyzes call center conversations for quality monitoring, agent performance, and customer sentiment — giving managers visibility without manual review.','fa-headset','#5BA8D4'],
        ['Vortexreach','AI B2B Outreach Automation','Automates lead research, personalized message drafting, and multi-channel outreach sequencing for B2B sales and business development teams.','fa-paper-plane','#10B981'],
        ['vortexsoftpublishing','Automated ePUB & XML Engine','Automates ePUB3/Kindle conversion, XML restructuring (JATS/BITS/S1000D), and WCAG 2.1 AA PDF accessibility tagging for academic publishers.','fa-book-open','#8B5CF6'],
      ] as [$name,$type,$desc,$ico,$col]): ?>
      <div class="col-lg-4 col-md-6">
        <div class="vs-card">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="vs-icon-box mb-0" style="background:<?= $col ?>18;color:<?= $col ?>;">
              <i class="fas <?= $ico ?>"></i>
            </div>
            <span style="font-size:10.5px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:<?= $col ?>;"><?= $name ?></span>
          </div>
          <h4 style="font-size:16.5px;font-weight:800;color:var(--vs-text-heading);margin-bottom:6px;"><?= $name ?></h4>
          <div style="font-size:11.5px;font-weight:600;color:var(--vs-text-muted);margin-bottom:12px;"><?= $type ?></div>
          <p style="font-size:13px;color:var(--vs-text-body);line-height:1.65;margin:0;"><?= $desc ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════ 8. FAQ ACCORDION ═══════ -->
<section class="section-pad" style="background:#F8FAFC;" id="faq">
  <div class="container">
    <div class="row gy-5">
      <div class="col-lg-4">
        <div class="section-tag">FAQ</div>
        <h2 class="section-title">Frequently Asked <span class="highlight">Questions</span></h2>
        <div class="section-divider" style="margin-left:0;"></div>
        <p style="font-size:14.5px;color:var(--vs-text-muted);margin-bottom:24px;">Can't find what you're looking for? Reach out to our solution advisory team for a direct consultation.</p>
        <a href="contact.php" class="btn-primary-custom"><i class="fas fa-envelope"></i> Contact Us</a>
      </div>

      <div class="col-lg-8">
        <?php foreach([
          ['What is healthcare RCM outsourcing and how does it work?','Revenue cycle management (RCM) outsourcing means an external partner handles medical coding, billing, claims submission, denial management, and accounts receivable recovery on behalf of a healthcare provider. Vortexsoft Innovations manages this end-to-end under HIPAA-compliant processes, reducing claim rejections and accelerating provider cash flow without the provider hiring an in-house billing team.'],
          ['How much does medical coding outsourcing cost in India?','Medical coding outsourcing costs vary by claim volume, specialty complexity, and service scope (coding-only vs. full RCM). Vortexsoft Innovations prices per-claim for smaller practices and offers monthly retainer models for ongoing volume — request a custom quote based on your claim mix for an accurate figure.'],
          ['How does denial management reduce claim rejections?','Denial management identifies why claims were rejected, corrects the root cause (coding errors, missing documentation, eligibility issues), and resubmits within payer deadlines. Vortexsoft Innovations denial management service tracks rejection patterns across a providers claims to prevent repeat denials, directly improving first-pass claim acceptance rates.'],
          ['What does a data annotation company do?','A data annotation company labels raw data — images, video, audio, or text — so machine learning models can learn from it. Vortexsoft Innovations provides image, video, audio, and text annotation services for AI/ML training pipelines, with human annotators trained on client-specific labeling guidelines and quality benchmarks.'],
          ['Is Vortexsoft Innovations ISO 27001 certified?','Yes. Vortexsoft Innovations Pvt. Ltd. is ISO 27001:2013 certified for information security management, and its healthcare operations additionally follow HIPAA-compliant data handling practices — relevant for clients evaluating data security before outsourcing sensitive processes like medical billing or financial data handling.'],
        ] as $idx => [$q,$a]): ?>
        <div class="faq-item <?= $idx === 0 ? 'open' : '' ?>" style="background:#FFFFFF;border:1px solid #E2E8F0;border-radius:12px;margin-bottom:12px;overflow:hidden;">
          <button class="faq-question" style="width:100%;padding:18px 22px;background:none;border:none;display:flex;align-items:center;justify-content:space-between;text-align:left;cursor:pointer;">
            <span style="font-weight:700;font-size:14.5px;color:var(--vs-text-heading);"><?= $q ?></span>
            <div class="faq-icon"><i class="fas fa-chevron-down"></i></div>
          </button>
          <div class="faq-answer" style="max-height:<?= $idx === 0 ? '300px' : '0' ?>;overflow:hidden;transition:max-height 0.3s ease;">
            <div style="padding:0 22px 18px;font-size:13.5px;color:var(--vs-text-muted);line-height:1.7;">
              <?= $a ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php
$extra_scripts = '<script src="./assets/js/home-services.js?v=' . time() . '"></script>';
require_once __DIR__ . '/includes/footer.php';
?>
