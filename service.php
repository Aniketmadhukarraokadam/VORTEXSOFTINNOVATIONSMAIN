<?php
/**
 * Vortexsoft Innovations — Services Directory Page (service.php)
 */

$page_title   = 'IT & BPO Services | Vortexsoft Innovations Private Limited';
$page_desc    = 'Explore 65+ services by Vortexsoft Innovations Private Limited: Healthcare BPO, Publishing, Real Estate, IT Solutions, Data Annotation for AI, Accounting, and Digital Marketing.';
$page_keywords = 'IT outsourcing services India, BPO services Pune Bengaluru, Healthcare BPO India, medical coding outsourcing, revenue cycle management RCM, denial management services, AI data annotation India, LiDAR annotation, computer vision labeling, custom software development Pune, offshore development center, publishing prepress services, eBook conversion ePUB3, S1000D XML conversion, lease abstraction services, commercial real estate BPO, offshore accounting bookkeeping, payroll processing outsourcing India';
$canonical_url = 'https://www.vortexsoftinnovations.com/service.php';

$prefix       = './';

require_once __DIR__ . '/config/constants.php';

// Services Directory ItemList Schema.org for Google & AEO Answer Engines
$services_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Vortexsoft Innovations Global IT & BPO Services',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'item' => [
                '@type' => 'Service',
                'name' => 'Enterprise HRMS & Autonomous Payroll Software (vortexsofthrms)',
                'description' => 'Autonomous cloud HRMS and payroll software featuring automated statutory deductions, biometric attendance sync, and workforce analytics.',
                'provider' => ['@type' => 'Organization', 'name' => 'Vortexsoft Innovations Private Limited'],
                'url' => 'https://www.vortexsoftinnovations.com/service.php#hrms'
            ]
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'item' => [
                '@type' => 'Service',
                'name' => 'Healthcare BPO & Revenue Cycle Management (RCM)',
                'description' => 'HIPAA-compliant medical coding (ICD-10, CPT), claims submission, denial management, and accounts receivable recovery for US healthcare providers.',
                'provider' => ['@type' => 'Organization', 'name' => 'Vortexsoft Innovations Private Limited'],
                'url' => 'https://www.vortexsoftinnovations.com/service.php#healthcare'
            ]
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'item' => [
                '@type' => 'Service',
                'name' => 'AI Data Annotation & Machine Learning Training Data',
                'description' => 'High-precision computer vision bounding boxes, polygon segmentation, 3D LiDAR point clouds, and RLHF text annotation.',
                'provider' => ['@type' => 'Organization', 'name' => 'Vortexsoft Innovations Private Limited'],
                'url' => 'https://www.vortexsoftinnovations.com/service.php#annotation'
            ]
        ],
        [
            '@type' => 'ListItem',
            'position' => 4,
            'item' => [
                '@type' => 'Service',
                'name' => 'Custom Web & Software Development (Offshore Development Center)',
                'description' => 'Full-stack enterprise application engineering, cloud migration, microservices, and dedicated offshore developer pods.',
                'provider' => ['@type' => 'Organization', 'name' => 'Vortexsoft Innovations Private Limited'],
                'url' => 'https://www.vortexsoftinnovations.com/service.php#software'
            ]
        ],
        [
            '@type' => 'ListItem',
            'position' => 5,
            'item' => [
                '@type' => 'Service',
                'name' => 'STM Publishing, Prepress & Digital Accessibility',
                'description' => 'Academic book typesetting, XML conversion (JATS, S1000D), ePUB3 generation, and WCAG 2.1 AA / Section 508 accessibility remediation.',
                'provider' => ['@type' => 'Organization', 'name' => 'Vortexsoft Innovations Private Limited'],
                'url' => 'https://www.vortexsoftinnovations.com/service.php#publishing'
            ]
        ],
        [
            '@type' => 'ListItem',
            'position' => 6,
            'item' => [
                '@type' => 'Service',
                'name' => 'Commercial Real Estate Lease Abstraction & Title Search',
                'description' => 'Comprehensive lease abstraction, CAM reconciliation, municipal title search, and settlement typing for title insurance firms.',
                'provider' => ['@type' => 'Organization', 'name' => 'Vortexsoft Innovations Private Limited'],
                'url' => 'https://www.vortexsoftinnovations.com/service.php#realestate'
            ]
        ]
    ]
];
$extra_head = '<script type="application/ld+json">' . "\n" .
    json_encode($services_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n" .
    '</script>' . "\n";

require_once __DIR__ . '/includes/header.php';
?>

<style>
.page-hero{background:linear-gradient(135deg,#080B1A 0%,#1C2280 55%,#0D1035 100%);padding:80px 0 70px;position:relative;overflow:hidden}
.page-hero::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:50px 50px}
.page-hero h1{font-size:clamp(2rem,4vw,3rem);font-weight:800;color:#fff}
.breadcrumb-item,.breadcrumb-item a{color:rgba(255,255,255,.6);font-size:14px}
.breadcrumb-item.active{color:rgba(255,255,255,.9)}
.breadcrumb-item+.breadcrumb-item::before{color:rgba(255,255,255,.4)}

/* Category Filter */
.svc-filter-bar{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:32px;justify-content:center}
.svc-filter-btn{background:#fff;border:1.5px solid #dde2f5;color:#475569;font-family:'Poppins',sans-serif;font-size:13px;font-weight:600;padding:8px 20px;border-radius:100px;cursor:pointer;transition:.3s;white-space:nowrap;outline:none}
.svc-filter-btn:hover{border-color:#1C2280;color:#1C2280;background:#f0f2ff}
.svc-filter-btn.active{background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;border-color:transparent;box-shadow:0 4px 14px rgba(28,34,128,.3)}
.svc-item.hidden{display:none!important}

.service-card-lg{background:#fff;border-radius:20px;padding:32px;border:1px solid #e8ecff;box-shadow:0 4px 20px rgba(28,34,128,.06);transition:all .3s;height:100%;display:flex;flex-direction:column}
.service-card-lg:hover{transform:translateY(-6px);box-shadow:0 15px 40px rgba(28,34,128,.14);border-color:transparent}
.service-card-lg .icon-box{width:60px;height:60px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:20px}
.service-card-lg h4{font-size:18px;font-weight:700;color:#1e293b;margin-bottom:10px}
.service-card-lg p{font-size:14px;color:#64748b;line-height:1.7;margin-bottom:20px;flex-grow:1}
.service-sublist{list-style:none;padding:0;margin:0 0 20px;font-size:13px;color:#475569}
.service-sublist li{padding:4px 0;display:flex;align-items:center;gap:8px}
.service-sublist li i{color:#CC2228;font-size:11px}
</style>


<!-- Hero -->
<div class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Services</li></ol></nav>
    <h1>Our <span style="color:#5BA8D4;">Services</span> Directory</h1>
    <p style="color:rgba(255,255,255,.75);font-size:16px;margin-top:12px;max-width:600px;">Comprehensive IT, BPO, Healthcare, Publishing, and AI Data solutions delivered by 200+ professionals worldwide.</p>
  </div>
</div>

<section class="py-5" style="background:#f0f2ff;">
  <div class="container">
    <!-- GEO Citable Fact Block -->
    <?= render_geo_fact_block() ?>

    <div class="text-center mb-4 scroll-reveal">
      <div class="section-tag">65+ Offerings</div>
      <h2 class="section-title">Specialized <span class="highlight">Service Domains</span></h2>
      <div class="section-divider"></div>
    </div>

    <!-- Strategic Positioning Framework -->
    <div class="row g-3 mb-5 scroll-reveal">
      <div class="col-md-6 col-lg-3">
        <div style="background:#fff;border:1px solid var(--border-light);border-radius:16px;padding:20px;height:100%;">
          <div style="font-size:20px;color:var(--primary);margin-bottom:8px;"><i class="fas fa-brain"></i></div>
          <h6 style="font-weight:700;margin-bottom:4px;color:var(--text-dark);">AI + Automation + Human Expertise</h6>
          <p style="font-size:12px;color:var(--text-muted);margin:0;">Synergizing autonomous AI agents with domain-certified expert human validation.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div style="background:#fff;border:1px solid var(--border-light);border-radius:16px;padding:20px;height:100%;">
          <div style="font-size:20px;color:#10b981;margin-bottom:8px;"><i class="fas fa-cubes"></i></div>
          <h6 style="font-weight:700;margin-bottom:4px;color:var(--text-dark);">Tech + Operations + Workforce</h6>
          <p style="font-size:12px;color:var(--text-muted);margin:0;">Full-stack software engineering, 24/7 BPO operations & staffing under one roof.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div style="background:#fff;border:1px solid var(--border-light);border-radius:16px;padding:20px;height:100%;">
          <div style="font-size:20px;color:#f59e0b;margin-bottom:8px;"><i class="fas fa-cogs"></i></div>
          <h6 style="font-weight:700;margin-bottom:4px;color:var(--text-dark);">Business Process Automation</h6>
          <p style="font-size:12px;color:var(--text-muted);margin:0;">RPA, Intelligent Document Processing (IDP) & friction-free workflow orchestration.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div style="background:#fff;border:1px solid var(--border-light);border-radius:16px;padding:20px;height:100%;">
          <div style="font-size:20px;color:#ec4899;margin-bottom:8px;"><i class="fas fa-rocket"></i></div>
          <h6 style="font-weight:700;margin-bottom:4px;color:var(--text-dark);">Digital Transformation</h6>
          <p style="font-size:12px;color:var(--text-muted);margin:0;">Cloud modernization, SAP/ERP integrations & predictive analytics solutions.</p>
        </div>
      </div>
    </div>

    <!-- Category Filter Bar -->
    <div class="svc-filter-bar scroll-reveal" id="svcFilterBar">
      <button class="svc-filter-btn active" data-filter="All" onclick="filterSvc(this)">All Services</button>
      <button class="svc-filter-btn" data-filter="HRMS & Software" onclick="filterSvc(this)" style="color:#e11d48;font-weight:700;"><i class="fas fa-users-cog me-1"></i> HRMS &amp; Payroll Software</button>
      <button class="svc-filter-btn" data-filter="Healthcare BPO" onclick="filterSvc(this)">Healthcare BPO</button>
      <button class="svc-filter-btn" data-filter="Real Estate" onclick="filterSvc(this)">Real Estate &amp; Title</button>
      <button class="svc-filter-btn" data-filter="Publishing" onclick="filterSvc(this)">STM Publishing</button>
      <button class="svc-filter-btn" data-filter="AI & Data" onclick="filterSvc(this)">AI &amp; Automation</button>
      <button class="svc-filter-btn" data-filter="IT & Software" onclick="filterSvc(this)">Custom Software &amp; ERP</button>
      <button class="svc-filter-btn" data-filter="Digital Marketing" onclick="filterSvc(this)">Marketing Automation</button>
      <button class="svc-filter-btn" data-filter="Accounting" onclick="filterSvc(this)">Accounting BPO</button>
      <button class="svc-filter-btn" data-filter="Logistics" onclick="filterSvc(this)">Logistics BPO</button>
      <button class="svc-filter-btn" data-filter="Technical Publications" onclick="filterSvc(this)">Tech Publications</button>
    </div>

    <div class="row g-4">
      <?php
      $domains = [
        [
          'title'=>'Enterprise HRMS &amp; Payroll Software (vortexsofthrms)',
          'cat'=>'HRMS & Software',
          'badge'=>'PROPRIETARY AI PLATFORM',
          'badge_color'=>'#E11D48',
          'icon'=>'fa-users-cog',
          'color'=>'rgba(225,29,72,.08)',
          'text_color'=>'#E11D48',
          'featured'=>true,
          'desc'=>'Autonomous cloud HRMS & payroll software designed for modern enterprises: automated one-click payroll calculation, statutory compliance (PF, ESIC, PT, TDS), biometric RFID attendance sync, and AI workforce analytics.',
          'items'=>[
            'Autonomous Multi-Tier Salary & Overtime Calculation Engine',
            'Biometric, Geo-Fence & RFID Attendance Real-Time Sync',
            'Automated Employee Onboarding & Digital Records Vault',
            'Statutory Tax & Compliance Deductions (PF, ESIC, TDS, PT)',
            'Employee Mobile Self-Service Portal & Predictive Attrition AI'
          ],
          'link'=>'index.php#ai-products',
          'btn_text'=>'Explore HRMS Platform →'
        ],
        [
          'title'=>'Healthcare BPO & Revenue Cycle Management',
          'cat'=>'Healthcare BPO',
          'badge'=>'FLAGSHIP HEALTHCARE BPO',
          'badge_color'=>'#CC2228',
          'icon'=>'fa-heartbeat',
          'color'=>'rgba(204,34,40,.08)',
          'text_color'=>'#CC2228',
          'featured'=>true,
          'desc'=>'Full-lifecycle HIPAA-compliant revenue cycle management (RCM), medical coding (ICD-10, CPT, HCPCS), billing, denial management, and prior authorization services.',
          'items'=>['Medical Coding (ICD-10-CM, CPT-4, HCPCS Level II)','Revenue Cycle Management & AR Recovery','Claims Denial Management & Appeals','Provider Credentialing & Prior Authorization','Payment Posting & Charge Entry Verification'],
          'link'=>'health-care-services/index.php',
          'btn_text'=>'Explore Healthcare Services →'
        ],
        [
          'title'=>'Real Estate, Title & Settlement Services',
          'cat'=>'Real Estate',
          'badge'=>'FLAGSHIP REAL ESTATE & TITLE',
          'badge_color'=>'#10b981',
          'icon'=>'fa-building',
          'color'=>'rgba(16,185,129,.08)',
          'text_color'=>'#10b981',
          'featured'=>true,
          'desc'=>'Commercial lease administration, CAM audits, property accounting, title search, commitment typing, policy preparation, and mortgage settlement support.',
          'items'=>['Commercial Lease Abstraction & Administration','CAM Expense Reconciliation & Audit','Title Search, Examination & Policy Typing','Property Accounting & Rent Roll Verification','Mortgage Closing & Settlement Support'],
          'link'=>'real-estate-services/index.php',
          'btn_text'=>'Explore Real Estate & Title →'
        ],
        [
          'title'=>'STM Publishing & Media Prepress',
          'cat'=>'Publishing',
          'badge'=>'FLAGSHIP PUBLISHING BPO',
          'badge_color'=>'#1C2280',
          'icon'=>'fa-book',
          'color'=>'rgba(28,34,128,.08)',
          'text_color'=>'#1C2280',
          'featured'=>true,
          'desc'=>'Digital prepress, automated journal typesetting, eBook conversion (ePUB3, NIMAS, XML), WCAG accessibility tagging, and copyediting production.',
          'items'=>['Academic Journal & STM Book Typesetting','ePUB3, Fixed Layout & XML Conversion','Alt-Text Writing & Mathematical Image Description','WCAG 2.1 AA PDF/eBook Accessibility Tagging','Copyediting & Proofreading Production'],
          'link'=>'publishing-services/index.php',
          'btn_text'=>'Explore Publishing Services →'
        ],
        [
          'title'=>'AI & Intelligent Automation Services',
          'cat'=>'AI & Data',
          'badge'=>'CORE ENTERPRISE DOMAIN',
          'badge_color'=>'#CC2228',
          'icon'=>'fa-robot',
          'color'=>'rgba(204,34,40,.08)',
          'text_color'=>'#CC2228',
          'featured'=>true,
          'desc'=>'Autonomous AI solutions, business process automation (BPA), Intelligent Document Processing (IDP), and high-precision AI data annotation datasets.',
          'items'=>['Custom AI Solutions & Autonomous AI Agents','Intelligent Document Processing (IDP) with OCR & NLP','Image, Video, Text & 3D LiDAR AI Data Annotation','RPA & Business Process Automation (BPA)','AI-Assisted Operations & Human-in-the-Loop Validation'],
          'link'=>'data-annotation-services/index.php',
          'btn_text'=>'Explore AI & Automation →'
        ],
        [
          'title'=>'Custom Software & Business Portals',
          'cat'=>'IT & Software',
          'badge'=>'CORE ENTERPRISE DOMAIN',
          'badge_color'=>'#5BA8D4',
          'icon'=>'fa-laptop-code',
          'color'=>'rgba(91,168,212,.08)',
          'text_color'=>'#5BA8D4',
          'featured'=>true,
          'desc'=>'Bespoke custom software engineering, enterprise CRM, ERP, HRMS platforms, customer portals, internal management dashboards, and microservices.',
          'items'=>['Custom Software & Scalable Web Application Development','Enterprise CRM, ERP & HRMS System Engineering','Executive Dashboards & Real-Time Business Management','Customer Portals & Internal Employee Portals','Custom RESTful & GraphQL API Integrations'],
          'link'=>'software-solutions/index.php',
          'btn_text'=>'Explore Software Portals →'
        ],
        [
          'title'=>'ERP & SAP Enterprise Solutions',
          'cat'=>'IT & Software',
          'badge'=>'CORE ENTERPRISE DOMAIN',
          'badge_color'=>'#1C2280',
          'icon'=>'fa-network-wired',
          'color'=>'rgba(28,34,128,.08)',
          'text_color'=>'#1C2280',
          'featured'=>true,
          'desc'=>'ERP implementation, customization, SAP consulting, enterprise workflow automation systems, and enterprise cloud application management.',
          'items'=>['ERP Solutions & End-to-End Implementation','ERP Customization & Legacy System Integration','SAP Consulting & Cloud Migration Services','Business Workflow & Enterprise Automation Systems','Custom Enterprise Applications'],
          'link'=>'software-solutions/index.php',
          'btn_text'=>'Explore ERP Solutions →'
        ],
        [
          'title'=>'Marketing Automation & MarTech',
          'cat'=>'Digital Marketing',
          'badge'=>'ENTERPRISE SERVICE',
          'badge_color'=>'#f59e0b',
          'icon'=>'fa-bullhorn',
          'color'=>'rgba(245,158,11,.08)',
          'text_color'=>'#f59e0b',
          'desc'=>'Automated lead generation, CRM & email campaign workflows, omnichannel marketing automation, customer funnel tracking, and analytics dashboards.',
          'items'=>['Marketing & Automated Lead Generation Systems','CRM Automation & Email Campaign Sequences','Multi-Channel Campaign Automation','Customer Workflow & Funnel Tracking','Real-Time Executive Reporting Dashboards'],
          'link'=>'digital-marketing-service/index.php',
          'btn_text'=>'Explore Marketing Services →'
        ],
        [
          'title'=>'Accounting & Financial BPO',
          'cat'=>'Accounting',
          'badge'=>'ENTERPRISE SERVICE',
          'badge_color'=>'#8b5cf6',
          'icon'=>'fa-calculator',
          'color'=>'rgba(139,92,246,.08)',
          'text_color'=>'#8b5cf6',
          'desc'=>'Full-cycle bookkeeping, payroll processing, accounts payable/receivable management, and financial audit & tax preparation support.',
          'items'=>['Bookkeeping & Ledger Setup','Payroll Processing & Compliance','Accounts Payable / Receivable Management','Financial Audit & Tax Filing Support'],
          'link'=>'accounting-services/index.php',
          'btn_text'=>'Explore Accounting Services →'
        ],
        [
          'title'=>'Logistics & Supply Chain Operations',
          'cat'=>'Logistics',
          'badge'=>'ENTERPRISE SERVICE',
          'badge_color'=>'#ec4899',
          'icon'=>'fa-truck',
          'color'=>'rgba(236,72,153,.08)',
          'text_color'=>'#ec4899',
          'desc'=>'Freight document processing, bill of lading entry, dispatch coordination, inventory tracking, and supply chain logistics analytics.',
          'items'=>['Bill of Lading Processing & Verification','Freight Audit & Data Entry','Inventory Tagging & Warehouse Tracking','Shipping Logistics Analytics'],
          'link'=>'logistics-services/index.php',
          'btn_text'=>'Explore Logistics Operations →'
        ],
        [
          'title'=>'Technical Publications & S1000D',
          'cat'=>'Technical Publications',
          'badge'=>'ENTERPRISE SERVICE',
          'badge_color'=>'#CC2228',
          'icon'=>'fa-file-alt',
          'color'=>'rgba(204,34,40,.08)',
          'text_color'=>'#CC2228',
          'desc'=>'Technical writing, S1000D / DITA XML conversion, equipment maintenance manuals, and illustrated parts catalogs (IPC) for defense & aerospace.',
          'items'=>['Technical Manual Writing & Authoring','S1000D / DITA XML Modular Conversion','Illustrated Parts Catalogs (IPC) Creation','Multi-lingual Technical Documentation'],
          'link'=>'technical-publication-service/index.php',
          'btn_text'=>'Explore Technical Publications →'
        ]
      ];

      foreach($domains as $i=>$d): 
        $is_featured = !empty($d['featured']);
      ?>
      <div class="col-lg-4 col-md-6 scroll-reveal svc-item" style="transition-delay:<?= ($i%3)*0.1 ?>s" data-category="<?= htmlspecialchars($d['cat'] ?? $d['title']) ?>">
        <div class="service-card-lg" style="<?= $is_featured ? 'border:1.5px solid rgba(28,34,128,0.25);box-shadow:0 8px 25px rgba(28,34,128,0.08);background:linear-gradient(180deg,#ffffff 0%,#fcfdfe 100%);' : '' ?>">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="icon-box mb-0" style="background:<?= $d['color'] ?>;color:<?= $d['text_color'] ?>;"><i class="fas <?= $d['icon'] ?>"></i></div>
            <?php if (!empty($d['badge'])): ?>
              <span class="badge" style="background:<?= $d['badge_color'] ?>;color:#fff;font-size:10px;padding:5px 10px;font-weight:700;letter-spacing:0.5px;"><?= $d['badge'] ?></span>
            <?php endif; ?>
          </div>
          <h4 style="font-weight:800;"><?= $d['title'] ?></h4>
          <p><?= $d['desc'] ?></p>
          <ul class="service-sublist">
            <?php foreach($d['items'] as $item): ?>
            <li><i class="fas fa-check-circle" style="color:<?= $d['badge_color'] ?>;"></i> <?= $item ?></li>
            <?php endforeach; ?>
          </ul>
          <a href="<?= $d['link'] ?>" class="btn mt-auto" style="background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;border-radius:10px;font-size:13px;font-weight:700;padding:12px;text-align:center;"><?= !empty($d['btn_text']) ? htmlspecialchars($d['btn_text']) : 'Explore Service →' ?></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════ INTERACTIVE B2B ENTERPRISE RATE CARD & SLA CALCULATOR ═══════ -->
<section class="section-pad" id="rate-calculator" style="background:linear-gradient(135deg,#080B1A 0%,#0f172a 60%,#1C2280 100%);color:#fff;position:relative;overflow:hidden;">
  <div style="position:absolute;top:-80px;right:-50px;width:350px;height:350px;border-radius:50%;background:radial-gradient(circle,rgba(204,34,40,0.14) 0%,transparent 70%);pointer-events:none;"></div>
  <div style="position:absolute;bottom:-100px;left:-50px;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(91,168,212,0.12) 0%,transparent 70%);pointer-events:none;"></div>

  <div class="container position-relative" style="z-index:2;">
    <div class="text-center mb-5 scroll-reveal">
      <div class="badge px-3 py-2 rounded-pill mb-2" style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);color:#93c5fd;font-size:12px;letter-spacing:1px;text-transform:uppercase;">
        <i class="fas fa-file-invoice-dollar me-1 text-warning"></i> Transparent Enterprise Pricing
      </div>
      <h2 style="font-size:clamp(2rem,3.2vw,2.8rem);font-weight:800;color:#fff;margin-bottom:12px;">
        Instant Service Rate Card &amp; <span style="color:#60a5fa;">SLA Cost Calculator</span>
      </h2>
      <p style="max-width:740px;margin:0 auto;color:#94a3b8;font-size:16px;">
        Configure your operational volume to get transparent offshore pricing benchmarks, guaranteed turnaround times, and immediate bottom-line cost reduction.
      </p>
    </div>

    <div class="row g-4 align-items-center justify-content-center">
      <!-- Selector Card (Left) -->
      <div class="col-lg-7 col-xl-7">
        <div class="p-4 p-md-5 rounded-4 shadow-lg" style="background:rgba(255,255,255,0.04);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,0.12);">
          
          <!-- Step 1: Industry Service Vertical -->
          <div class="mb-4">
            <label class="form-label text-white fw-bold d-flex justify-content-between" style="font-size:14px;letter-spacing:0.5px;">
              <span><span class="badge bg-primary me-2">1</span> SELECT OUTSOURCING DIVISION</span>
              <span id="rate-v-name" class="text-info fw-semibold">U.S. Title Search &amp; Mortgage BPM</span>
            </label>
            <div class="row g-2" id="rate-vertical-selector">
              <div class="col-6 col-md-4">
                <button type="button" class="btn w-100 p-2 text-start rate-v-btn active" data-v="title" data-name="U.S. Title Search &amp; Mortgage BPM" style="background:rgba(255,255,255,0.08);color:#fff;border:1px solid #3b82f6;border-radius:10px;font-size:12px;">
                  <i class="fas fa-landmark text-warning me-1"></i> Title &amp; Mortgage
                </button>
              </div>
              <div class="col-6 col-md-4">
                <button type="button" class="btn w-100 p-2 text-start rate-v-btn" data-v="rcm" data-name="Healthcare RCM &amp; Medical Coding" style="background:rgba(255,255,255,0.05);color:#cbd5e1;border:1px solid rgba(255,255,255,0.1);border-radius:10px;font-size:12px;">
                  <i class="fas fa-heartbeat text-danger me-1"></i> Healthcare RCM
                </button>
              </div>
              <div class="col-6 col-md-4">
                <button type="button" class="btn w-100 p-2 text-start rate-v-btn" data-v="dev" data-name="Dedicated AI &amp; Software Dev Pod" style="background:rgba(255,255,255,0.05);color:#cbd5e1;border:1px solid rgba(255,255,255,0.1);border-radius:10px;font-size:12px;">
                  <i class="fas fa-code text-info me-1"></i> AI &amp; Software Pod
                </button>
              </div>
              <div class="col-6 col-md-4">
                <button type="button" class="btn w-100 p-2 text-start rate-v-btn" data-v="staffing" data-name="Global Staffing &amp; Offshore ODC" style="background:rgba(255,255,255,0.05);color:#cbd5e1;border:1px solid rgba(255,255,255,0.1);border-radius:10px;font-size:12px;">
                  <i class="fas fa-users text-primary me-1"></i> Staffing &amp; ODC
                </button>
              </div>
              <div class="col-6 col-md-4">
                <button type="button" class="btn w-100 p-2 text-start rate-v-btn" data-v="sales" data-name="Commercial Energy &amp; B2B Sales BPO" style="background:rgba(255,255,255,0.05);color:#cbd5e1;border:1px solid rgba(255,255,255,0.1);border-radius:10px;font-size:12px;">
                  <i class="fas fa-bolt text-warning me-1"></i> Energy Sales BPO
                </button>
              </div>
              <div class="col-6 col-md-4">
                <button type="button" class="btn w-100 p-2 text-start rate-v-btn" data-v="annotation" data-name="AI Data Annotation &amp; Computer Vision" style="background:rgba(255,255,255,0.05);color:#cbd5e1;border:1px solid rgba(255,255,255,0.1);border-radius:10px;font-size:12px;">
                  <i class="fas fa-tags text-success me-1"></i> AI Annotation
                </button>
              </div>
            </div>
          </div>

          <!-- Step 2: Target Volume / Team Scale -->
          <div class="mb-4">
            <label class="form-label text-white fw-bold d-flex justify-content-between" style="font-size:14px;letter-spacing:0.5px;">
              <span><span class="badge bg-primary me-2">2</span> MONTHLY VOLUME / RESOURCE SCALE</span>
              <span id="rate-s-name" class="text-info fw-semibold">Growth Tier (50 Files / Day)</span>
            </label>
            <div class="row g-2" id="rate-scale-selector">
              <div class="col-4">
                <button type="button" class="btn w-100 p-3 text-center rate-s-btn" data-tier="starter" style="background:rgba(255,255,255,0.05);color:#cbd5e1;border:1px solid rgba(255,255,255,0.1);border-radius:10px;">
                  <div class="fw-bold" id="tier-lbl-starter" style="font-size:14px;">15 / Day</div>
                  <small id="tier-sub-starter" style="font-size:11px;color:#94a3b8;">Agile Batch</small>
                </button>
              </div>
              <div class="col-4">
                <button type="button" class="btn w-100 p-3 text-center rate-s-btn active" data-tier="growth" style="background:rgba(255,255,255,0.08);color:#fff;border:1px solid #3b82f6;border-radius:10px;">
                  <div class="fw-bold" id="tier-lbl-growth" style="font-size:14px;">50 / Day</div>
                  <small id="tier-sub-growth" style="font-size:11px;color:#94a3b8;">Scale Pod</small>
                </button>
              </div>
              <div class="col-4">
                <button type="button" class="btn w-100 p-3 text-center rate-s-btn" data-tier="enterprise" style="background:rgba(255,255,255,0.05);color:#cbd5e1;border:1px solid rgba(255,255,255,0.1);border-radius:10px;">
                  <div class="fw-bold" id="tier-lbl-enterprise" style="font-size:14px;">100+ / Day</div>
                  <small id="tier-sub-enterprise" style="font-size:11px;color:#94a3b8;">Enterprise Division</small>
                </button>
              </div>
            </div>
          </div>

          <!-- Trust Badges Strip -->
          <div class="pt-2 border-top border-secondary d-flex flex-wrap gap-2 text-white-50" style="font-size:12px;">
            <span><i class="fas fa-check-circle text-success me-1"></i> ISO 27001 Certified</span>
            <span>•</span>
            <span><i class="fas fa-shield-alt text-info me-1"></i> HIPAA Compliant</span>
            <span>•</span>
            <span><i class="fas fa-lock text-warning me-1"></i> Strict NDA</span>
            <span>•</span>
            <span><i class="fas fa-globe text-primary me-1"></i> Qualia / SoftPro / AWS Ready</span>
          </div>
        </div>
      </div>

      <!-- Rate Output Summary Card (Right) -->
      <div class="col-lg-5 col-xl-5">
        <div class="p-4 p-md-5 rounded-4 shadow-lg h-100 d-flex flex-column justify-content-between" style="background:linear-gradient(145deg,rgba(28,34,128,0.7) 0%,rgba(15,23,42,0.9) 100%);border:1px solid rgba(91,168,212,0.3);box-shadow:0 14px 45px rgba(28,34,128,0.3);">
          <div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="badge bg-success-subtle text-success border border-success px-2 py-1" style="font-size:11px;letter-spacing:0.5px;">
                <i class="fas fa-check-circle me-1"></i> ESTIMATED RATE BENCHMARK
              </span>
              <span id="rate-savings-badge" class="badge bg-warning text-dark fw-bold" style="font-size:12px;">
                68% Cost Savings
              </span>
            </div>

            <div class="mb-3">
              <div style="font-size:12px;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Transparent Offshore Pricing</div>
              <div id="rate-unit-display" style="font-size:clamp(2.2rem,3.4vw,2.8rem);font-weight:900;color:#60a5fa;font-family:'Poppins',sans-serif;line-height:1.1;">
                $14 – $18<span style="font-size:16px;color:#cbd5e1;font-weight:600;"> / file</span>
              </div>
              <div id="rate-benchmark-desc" style="font-size:12.5px;color:#94a3b8;margin-top:4px;">
                vs US in-house benchmark of <strong>$55 – $75 / file</strong>
              </div>
            </div>

            <div class="p-3 rounded-3 mb-3" style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);">
              <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:12.5px;">
                <span class="text-white-50">Estimated Monthly Savings:</span>
                <span id="rate-monthly-saved" class="text-success fw-bold" style="font-size:14px;">~$42,000 / mo</span>
              </div>
              <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:12.5px;">
                <span class="text-white-50">SLA Turnaround:</span>
                <span id="rate-sla-time" class="text-info fw-semibold">12–24 Hours</span>
              </div>
              <div class="d-flex justify-content-between align-items-center" style="font-size:12.5px;">
                <span class="text-white-50">Quality Guarantee:</span>
                <span class="text-warning fw-semibold">99.8% Exam Accuracy</span>
              </div>
            </div>
          </div>

          <div class="mt-3">
            <div class="d-grid gap-2">
              <a id="rate-wa-btn" href="https://wa.me/918308906690?text=Hello%20Vortexsoft%20Innovations%20Team%2C%20I%20would%20like%20to%20lock%20in%20pricing%20for%20US%20Title%20Search%20%2850%20files%2Fday%29" target="_blank" class="btn btn-success fw-bold py-2" style="border-radius:10px;font-size:14px;box-shadow:0 4px 15px rgba(16,185,129,0.3);">
                <i class="fab fa-whatsapp me-2"></i> Lock In Custom Rate via WhatsApp
              </a>
              <a id="rate-sow-btn" href="contact.php?service=title&amp;tier=growth" class="btn btn-outline-light fw-bold py-2" style="border-radius:10px;font-size:14px;">
                <i class="fas fa-file-contract me-2"></i> Request Formal Statement of Work (SOW)
              </a>
            </div>
            <div class="text-center mt-2" style="font-size:11px;color:#94a3b8;">
              Strict Confidentiality • Non-Disclosure Agreement (NDA) Protected • No Setup Fees
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var rateConfig = {
    title: {
      name: "U.S. Title Search & Mortgage BPM",
      starter: { lbl: "15 / Day", sub: "Agile Batch", unit: "$16 – $20", suffix: " / file", bench: "vs US in-house benchmark of $55 – $75 / file", saved: "~$14,500 / mo", sla: "24–48 Hours", reduction: "68%" },
      growth: { lbl: "50 / Day", sub: "Scale Pod", unit: "$14 – $18", suffix: " / file", bench: "vs US in-house benchmark of $55 – $75 / file", saved: "~$42,000 / mo", sla: "12–24 Hours", reduction: "70%" },
      enterprise: { lbl: "100+ / Day", sub: "Enterprise Division", unit: "$12 – $15", suffix: " / file", bench: "vs US in-house benchmark of $55 – $75 / file", saved: "~$85,000+ / mo", sla: "Real-Time 12 Hours", reduction: "74%" }
    },
    rcm: {
      name: "Healthcare RCM & Medical Coding",
      starter: { lbl: "300 Charts", sub: "Per Month", unit: "$18 – $22", suffix: " / chart", bench: "vs US in-house coding of $45 – $65 / chart", saved: "~$11,500 / mo", sla: "24 Hours Turnaround", reduction: "65%" },
      growth: { lbl: "800 Charts", sub: "Per Month", unit: "$16 – $19", suffix: " / chart", bench: "vs US in-house coding of $45 – $65 / chart", saved: "~$28,000 / mo", sla: "12–24 Hours Turnaround", reduction: "68%" },
      enterprise: { lbl: "2,000+ Charts", sub: "Hospital System", unit: "$13 – $16", suffix: " / chart", bench: "vs US in-house coding of $45 – $65 / chart", saved: "~$68,000+ / mo", sla: "Continuous Multi-Shift", reduction: "72%" }
    },
    dev: {
      name: "Dedicated AI & Software Dev Pod",
      starter: { lbl: "1 Senior Dev", sub: "160 Hrs / Mo", unit: "$22 – $28", suffix: " / hour", bench: "vs US/UK in-house senior engineer of $85 – $120 / hr", saved: "~$9,800 / mo", sla: "Immediate Onboarding", reduction: "65%" },
      growth: { lbl: "3-Engineer Pod", sub: "Lead + 2 Devs", unit: "$20 – $25", suffix: " / hour", bench: "vs US/UK in-house engineer pod of $85 – $120 / hr", saved: "~$28,500 / mo", sla: "1-Week Sprint Alignment", reduction: "68%" },
      enterprise: { lbl: "6+ Full Pod", sub: "Architect, Devs, QA", unit: "$18 – $22", suffix: " / hour", bench: "vs US/UK in-house full pod of $85 – $120 / hr", saved: "~$62,000+ / mo", sla: "Dedicated ODC Facility", reduction: "72%" }
    },
    staffing: {
      name: "Global Staffing & Offshore ODC",
      starter: { lbl: "3 Resources", sub: "Dedicated Pod", unit: "$2,200", suffix: " / FTE / mo", bench: "vs US domestic overhead of $6,500 – $8,500 / mo", saved: "~$13,500 / mo", sla: "7-Day Deployment", reduction: "68%" },
      growth: { lbl: "8 Resources", sub: "Cross-Functional", unit: "$2,000", suffix: " / FTE / mo", bench: "vs US domestic overhead of $6,500 – $8,500 / mo", saved: "~$38,000 / mo", sla: "10-Day Deployment", reduction: "70%" },
      enterprise: { lbl: "20+ Division", sub: "Enterprise ODC", unit: "$1,800", suffix: " / FTE / mo", bench: "vs US domestic overhead of $6,500 – $8,500 / mo", saved: "~$95,000+ / mo", sla: "Dedicated Floor & Security", reduction: "74%" }
    },
    sales: {
      name: "Commercial Energy & B2B Sales BPO",
      starter: { lbl: "2 SDRs", sub: "Pilot Pod", unit: "$2,200", suffix: " / SDR / mo", bench: "vs US domestic SDR cost of $5,500 / mo + commissions", saved: "~$6,600 / mo", sla: "40+ Qualified Appts / mo", reduction: "60%" },
      growth: { lbl: "5 SDRs", sub: "Campaign Team", unit: "$2,000", suffix: " / SDR / mo", bench: "vs US domestic SDR cost of $5,500 / mo + commissions", saved: "~$17,500 / mo", sla: "100+ Qualified Appts / mo", reduction: "64%" },
      enterprise: { lbl: "10+ SDR Pod", sub: "Market Domination", unit: "$1,850", suffix: " / SDR / mo", bench: "vs US domestic SDR cost of $5,500 / mo + commissions", saved: "~$36,500+ / mo", sla: "Dedicated Dialers & CRM", reduction: "68%" }
    },
    annotation: {
      name: "AI Data Annotation & Computer Vision",
      starter: { lbl: "25K Objects", sub: "Pilot Dataset", unit: "$0.04 – $0.08", suffix: " / label", bench: "vs US onshore labeling of $0.20 – $0.35 / label", saved: "~$5,500 / mo", sla: "99.8% QA Benchmark", reduction: "70%" },
      growth: { lbl: "150K Objects", sub: "Production Pipeline", unit: "$0.03 – $0.06", suffix: " / label", bench: "vs US onshore labeling of $0.20 – $0.35 / label", saved: "~$24,000 / mo", sla: "Continuous Weekly Batches", reduction: "74%" },
      enterprise: { lbl: "500K+ Objects", sub: "Enterprise AI Model", unit: "$0.02 – $0.04", suffix: " / label", bench: "vs US onshore labeling of $0.20 – $0.35 / label", saved: "~$65,000+ / mo", sla: "LiDAR / 3D Sensor Fusion", reduction: "78%" }
    }
  };

  var curV = 'title';
  var curTier = 'growth';

  function renderCalculator() {
    var vData = rateConfig[curV];
    var tData = vData[curTier];

    var nameEl = document.getElementById('rate-v-name');
    if (nameEl) nameEl.textContent = vData.name;

    // Update tier buttons labels
    ['starter', 'growth', 'enterprise'].forEach(function(tier) {
      var l = document.getElementById('tier-lbl-' + tier);
      var s = document.getElementById('tier-sub-' + tier);
      if (l) l.textContent = vData[tier].lbl;
      if (s) s.textContent = vData[tier].sub;
    });

    var sNameEl = document.getElementById('rate-s-name');
    if (sNameEl) sNameEl.textContent = tData.lbl + ' (' + tData.sub + ')';

    var unitEl = document.getElementById('rate-unit-display');
    if (unitEl) unitEl.innerHTML = tData.unit + '<span style="font-size:16px;color:#cbd5e1;font-weight:600;">' + tData.suffix + '</span>';

    var benchEl = document.getElementById('rate-benchmark-desc');
    if (benchEl) benchEl.innerHTML = tData.bench;

    var savedEl = document.getElementById('rate-monthly-saved');
    if (savedEl) savedEl.textContent = tData.saved;

    var slaEl = document.getElementById('rate-sla-time');
    if (slaEl) slaEl.textContent = tData.sla;

    var badgeEl = document.getElementById('rate-savings-badge');
    if (badgeEl) badgeEl.textContent = tData.reduction + ' Cost Savings';

    // Update actions
    var waBtn = document.getElementById('rate-wa-btn');
    if (waBtn) {
      var msg = "Hello Vortexsoft Innovations Team, I would like to lock in pricing for " + vData.name + " (" + tData.lbl + "). Estimated savings: " + tData.saved + ". Please send a formal quote.";
      waBtn.href = "https://wa.me/918308906690?text=" + encodeURIComponent(msg);
    }

    var sowBtn = document.getElementById('rate-sow-btn');
    if (sowBtn) {
      sowBtn.href = "contact.php?service=" + encodeURIComponent(curV) + "&tier=" + encodeURIComponent(curTier);
    }
  }

  // Vertical button click
  document.querySelectorAll('.rate-v-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.rate-v-btn').forEach(function(b) {
        b.classList.remove('active');
        b.style.borderColor = 'rgba(255,255,255,0.1)';
        b.style.background = 'rgba(255,255,255,0.05)';
        b.style.color = '#cbd5e1';
      });
      btn.classList.add('active');
      btn.style.borderColor = '#3b82f6';
      btn.style.background = 'rgba(255,255,255,0.08)';
      btn.style.color = '#fff';
      curV = btn.getAttribute('data-v');
      renderCalculator();
    });
  });

  // Scale tier button click
  document.querySelectorAll('.rate-s-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.rate-s-btn').forEach(function(b) {
        b.classList.remove('active');
        b.style.borderColor = 'rgba(255,255,255,0.1)';
        b.style.background = 'rgba(255,255,255,0.05)';
        b.style.color = '#cbd5e1';
      });
      btn.classList.add('active');
      btn.style.borderColor = '#3b82f6';
      btn.style.background = 'rgba(255,255,255,0.08)';
      btn.style.color = '#fff';
      curTier = btn.getAttribute('data-tier');
      renderCalculator();
    });
  });

  renderCalculator();
});
</script>

<!-- CTA -->
<section class="py-5" style="background:#fff;">
  <div class="container text-center">
    <h3 style="font-family:'Poppins',sans-serif;font-weight:800;color:#1C2280;margin-bottom:12px;">Need a Customized Solution?</h3>
    <p style="color:#64748b;font-size:16px;max-width:550px;margin:0 auto 24px;">Our engineering and operations team can tailor any workflow to match your exact requirements.</p>
    <a href="contact.php" class="btn" style="background:linear-gradient(135deg,#CC2228,#9e1a1f);color:#fff;border-radius:10px;padding:14px 32px;font-weight:700;font-size:15px;"><i class="fas fa-paper-plane me-2"></i> Request Custom Quote</a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script>
function filterSvc(btn) {
  var filter = btn.getAttribute('data-filter');
  document.querySelectorAll('.svc-filter-btn').forEach(function(b){ b.classList.remove('active'); });
  btn.classList.add('active');
  document.querySelectorAll('.svc-item').forEach(function(item){
    if (filter === 'All' || item.getAttribute('data-category') === filter) {
      item.classList.remove('hidden');
    } else {
      item.classList.add('hidden');
    }
  });
}
</script>
