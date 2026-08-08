<?php
/**
 * Vortexsoft Innovations — Services Directory Page (service.php)
 */

$page_title   = 'All Services — Vortexsoft Group | 65+ IT, BPO, Healthcare & AI Solutions';
$page_desc    = 'Explore 65+ services by Vortexsoft Group: Healthcare BPO, Publishing, Real Estate, IT Solutions, Data Annotation for AI, Accounting, Logistics, and Digital Marketing.';
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

    <div class="text-center mb-5 scroll-reveal">
      <div class="section-tag">65+ Offerings</div>
      <h2 class="section-title">Specialized <span class="highlight">Service Domains</span></h2>
      <div class="section-divider"></div>
    </div>

    <div class="row g-4">
      <?php
      $domains = [
        [
          'title'=>'Healthcare BPO',
          'icon'=>'fa-heartbeat',
          'color'=>'rgba(204,34,40,.08)',
          'text_color'=>'#CC2228',
          'desc'=>'End-to-end revenue cycle management (RCM), medical coding, billing, denial management, and prior authorization services.',
          'items'=>['Medical Coding (ICD-10, CPT)','Medical Billing & AR Recovery','Denial Management & Appeals','Payment Posting & Verification'],
          'link'=>'health-care-services/index.php'
        ],
        [
          'title'=>'Publishing & Digital Media',
          'icon'=>'fa-book',
          'color'=>'rgba(28,34,128,.08)',
          'text_color'=>'#1C2280',
          'desc'=>'Digital prepress, typesetting, eBook conversion (ePUB3, XML), accessibility tagging, and editorial production.',
          'items'=>['ePUB3 & Kindle Conversion','Digital Prepress & Typesetting','Alt-Text Writing & Image Description','WCAG PDF/eBook Accessibility'],
          'link'=>'publishing-services/index.php'
        ],
        [
          'title'=>'Real Estate & Title Services',
          'icon'=>'fa-building',
          'color'=>'rgba(16,185,129,.08)',
          'text_color'=>'#10b981',
          'desc'=>'Lease administration, CAM audits, property accounting, title searches, policy typing, and mortgage settlement support.',
          'items'=>['CAM Reconciliation & Audit','Lease Abstraction & Administration','Property Accounting & Rent Roll','Title Search & Settlement Typing'],
          'link'=>'real-estate-services/index.php'
        ],
        [
          'title'=>'IT & Software Solutions',
          'icon'=>'fa-laptop-code',
          'color'=>'rgba(91,168,212,.08)',
          'text_color'=>'#5BA8D4',
          'desc'=>'Custom web/mobile software development, cloud infrastructure management, ERP integration, and AI app engineering.',
          'items'=>['Custom Web & Mobile Apps','ERP & CRM Customization','Cloud Maintenance & Helpdesk','Data Analytics as a Service'],
          'link'=>'software-solutions/index.php'
        ],
        [
          'title'=>'Data Annotation & AI',
          'icon'=>'fa-tags',
          'color'=>'rgba(245,158,11,.08)',
          'text_color'=>'#f59e0b',
          'desc'=>'High-precision image, video, audio, LiDAR, and text dataset labeling for computer vision and LLM model training.',
          'items'=>['Image Bounding Boxes & Polygons','Video Tracking & Action Tagging','Text & Audio NLP Annotation','Medical Image Labeling'],
          'link'=>'data-annotation-services/index.php'
        ],
        [
          'title'=>'Accounting & Finance BPO',
          'icon'=>'fa-calculator',
          'color'=>'rgba(139,92,246,.08)',
          'text_color'=>'#8b5cf6',
          'desc'=>'Full-cycle bookkeeping, payroll processing, accounts payable/receivable management, and financial reporting.',
          'items'=>['Bookkeeping & Ledger Setup','Payroll Processing & Compliance','Accounts Payable / Receivable','Financial Reporting & Tax Support'],
          'link'=>'accounting-services/index.php'
        ],
        [
          'title'=>'Logistics & Supply Chain',
          'icon'=>'fa-truck',
          'color'=>'rgba(236,72,153,.08)',
          'text_color'=>'#ec4899',
          'desc'=>'Freight document processing, bill of lading entry, dispatch coordination, and supply chain data entry.',
          'items'=>['Bill of Lading Processing','Freight Audit & Data Entry','Inventory Tagging & Tracking','Shipping Logistics Analytics'],
          'link'=>'logistics-services/index.php'
        ],
        [
          'title'=>'Digital Marketing & Growth',
          'icon'=>'fa-bullhorn',
          'color'=>'rgba(28,34,128,.08)',
          'text_color'=>'#1C2280',
          'desc'=>'Search engine optimization (SEO), Google/Meta ads management, content marketing, and e-commerce solutions.',
          'items'=>['Technical & On-Page SEO','PPC & Social Media Campaigns','E-Commerce Platform Setup','CRM Strategy & Automation'],
          'link'=>'digital-marketing-service/index.php'
        ],
        [
          'title'=>'Technical Publications',
          'icon'=>'fa-file-alt',
          'color'=>'rgba(204,34,40,.08)',
          'text_color'=>'#CC2228',
          'desc'=>'Technical writing, XML conversion (S1000D/DITA), maintenance manuals, and illustrated parts catalogs for aerospace & defense.',
          'items'=>['Technical Manual Writing','S1000D / DITA XML Conversion','Illustrated Parts Catalogs (IPC)','Multi-lingual Documentation'],
          'link'=>'technical-publication-service/index.php'
        ]
      ];
      foreach($domains as $i=>$d): ?>
      <div class="col-lg-4 col-md-6 scroll-reveal" style="transition-delay:<?= ($i%3)*0.1 ?>s">
        <div class="service-card-lg">
          <div class="icon-box" style="background:<?= $d['color'] ?>;color:<?= $d['text_color'] ?>;"><i class="fas <?= $d['icon'] ?>"></i></div>
          <h4><?= $d['title'] ?></h4>
          <p><?= $d['desc'] ?></p>
          <ul class="service-sublist">
            <?php foreach($d['items'] as $item): ?>
            <li><i class="fas fa-check"></i> <?= $item ?></li>
            <?php endforeach; ?>
          </ul>
          <a href="<?= $d['link'] ?>" class="btn" style="background:linear-gradient(135deg,#1C2280,#2d35c4);color:#fff;border-radius:10px;font-size:13px;font-weight:700;padding:10px;text-align:center;">Learn More →</a>
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
