<?php
/**
 * Vortexsoft Innovations — Services Directory Page (service.php)
 */

$page_title   = 'IT & BPO Services | Vortexsoft Group';
$page_desc    = 'Explore 65+ services by Vortexsoft Group: Healthcare BPO, Publishing, Real Estate, IT Solutions, Data Annotation for AI, Accounting, and Digital Marketing.';
$canonical_url = 'https://www.vortexsoftinnovations.com/service.php';

$prefix       = './';

require_once __DIR__ . '/config/constants.php';
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
          'title'=>'Healthcare BPO & Revenue Cycle Management',
          'cat'=>'Healthcare BPO',
          'badge'=>'FLAGSHIP TITLE SERVICE',
          'badge_color'=>'#CC2228',
          'icon'=>'fa-heartbeat',
          'color'=>'rgba(204,34,40,.08)',
          'text_color'=>'#CC2228',
          'featured'=>true,
          'desc'=>'Full-lifecycle HIPAA-compliant revenue cycle management (RCM), medical coding (ICD-10, CPT, HCPCS), billing, denial management, and prior authorization services.',
          'items'=>['Medical Coding (ICD-10-CM, CPT-4, HCPCS Level II)','Revenue Cycle Management & AR Recovery','Claims Denial Management & Appeals','Provider Credentialing & Prior Authorization','Payment Posting & Charge Entry Verification'],
          'link'=>'health-care-services/index.php'
        ],
        [
          'title'=>'Real Estate, Title & Settlement Services',
          'cat'=>'Real Estate',
          'badge'=>'FLAGSHIP TITLE SERVICE',
          'badge_color'=>'#10b981',
          'icon'=>'fa-building',
          'color'=>'rgba(16,185,129,.08)',
          'text_color'=>'#10b981',
          'featured'=>true,
          'desc'=>'Commercial lease administration, CAM audits, property accounting, title search, commitment typing, policy preparation, and mortgage settlement support.',
          'items'=>['Commercial Lease Abstraction & Administration','CAM Expense Reconciliation & Audit','Title Search, Examination & Policy Typing','Property Accounting & Rent Roll Verification','Mortgage Closing & Settlement Support'],
          'link'=>'real-estate-services/index.php'
        ],
        [
          'title'=>'STM Publishing & Media Prepress',
          'cat'=>'Publishing',
          'badge'=>'FLAGSHIP TITLE SERVICE',
          'badge_color'=>'#1C2280',
          'icon'=>'fa-book',
          'color'=>'rgba(28,34,128,.08)',
          'text_color'=>'#1C2280',
          'featured'=>true,
          'desc'=>'Digital prepress, automated journal typesetting, eBook conversion (ePUB3, NIMAS, XML), WCAG accessibility tagging, and copyediting production.',
          'items'=>['Academic Journal & STM Book Typesetting','ePUB3, Fixed Layout & XML Conversion','Alt-Text Writing & Mathematical Image Description','WCAG 2.1 AA PDF/eBook Accessibility Tagging','Copyediting & Proofreading Production'],
          'link'=>'publishing-services/index.php'
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
          'link'=>'data-annotation-services/index.php'
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
          'link'=>'software-solutions/index.php'
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
          'link'=>'software-solutions/index.php'
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
          'link'=>'digital-marketing-service/index.php'
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
          'link'=>'accounting-services/index.php'
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
          'link'=>'logistics-services/index.php'
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
          'link'=>'technical-publication-service/index.php'
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
          <a href="<?= $d['link'] ?>" class="btn mt-auto" style="background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;border-radius:10px;font-size:13px;font-weight:700;padding:12px;text-align:center;">Explore Title Service →</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


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
