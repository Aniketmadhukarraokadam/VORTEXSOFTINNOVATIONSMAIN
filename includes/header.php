<?php
/**
 * Vortexsoft Innovations — PHP Header Partial
 * Replaces assets/partials/header.js + header.html
 * Usage: require_once ROOT_PATH . '/includes/header.php';
 * Pass $page_title, $page_desc, $canonical_url, $prefix (default './')
 */

if (!ob_get_level() && !headers_sent()) {
    ob_start();
}

if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config/constants.php';
}
require_once __DIR__ . '/functions.php';

$prefix       = $prefix ?? './';
$page_title   = $page_title ?? 'Top Global IT & BPO Outsourcing Company in India | AI Services | Vortexsoft Group';
$page_desc    = $page_desc ?? 'Vortexsoft Group — ISO 27001 certified global IT & BPO outsourcing company in Bengaluru, India. Expert in AI Solutions, Healthcare BPO, Publishing, Real Estate, Data Annotation & 75+ services for 150+ clients worldwide.';
$canonical_url = $canonical_url ?? SITE_URL . '/';
$og_image     = $og_image ?? SITE_URL . '/logo-header.jpg';

// Detect active page
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
function nav_active(string $page, string $path): string {
    if ($page === 'home' && ($path === '/' || $path === '/index.php' || $path === '')) return 'active';
    return str_contains($path, $page) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <style id="critical-css">
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{scroll-behavior:smooth}
        body{font-family:'Inter',sans-serif;color:#0D0F2B;background:#fff;overflow-x:hidden;line-height:1.7}
        h1,h2,h3,h4,h5,h6{font-family:'Poppins',sans-serif;font-weight:700;line-height:1.25}
        #page-loader{position:fixed;inset:0;background:#0A0D1F;display:flex;flex-direction:column;align-items:center;justify-content:center;z-index:99999;transition:opacity .5s ease,visibility .5s ease;overflow:hidden}
        #page-loader.hide{opacity:0;visibility:hidden}
        .loader-content{position:relative;z-index:2;text-align:center;width:100%;max-width:480px;padding:0 20px}
        .loader-brand{font-family:'Poppins',sans-serif;font-size:clamp(32px,6vw,56px);font-weight:900;letter-spacing:4px;line-height:1;margin-bottom:12px;display:flex;justify-content:center}
        .l-blue{color:#3544C4}.l-red{color:#DE252A}
        .loader-sub{font-size:clamp(10px,2vw,15px);font-weight:400;letter-spacing:6px;color:#8F949F;margin-bottom:50px}
        .loader-status{display:flex;justify-content:space-between;font-size:13px;font-weight:600;letter-spacing:2px;color:#565C6B;margin-bottom:12px;font-family:'Inter',sans-serif;padding:0 4px}
        .loader-track-new{width:100%;height:5px;background:#181B34;border-radius:6px;overflow:hidden;margin-bottom:24px}
        .loader-bar-new{height:100%;width:0;background:linear-gradient(90deg,#3544C4,#943BA8,#DE252A);border-radius:6px;animation:loaderFillNew 1s cubic-bezier(.4,0,.2,1) forwards;box-shadow:0 0 10px rgba(148,59,168,.5)}
        @keyframes loaderFillNew{0%{width:0}100%{width:100%}}
        .loader-tagline{font-size:13px;font-weight:500;letter-spacing:4px;color:#414757;font-family:'Inter',sans-serif}
        .topbar{background:#080B1A;padding:9px 0;font-size:13px;font-weight:500;color:rgba(255,255,255,.7);border-bottom:1px solid rgba(255,255,255,.05)}
        .navbar{background:rgba(255,255,255,.98);backdrop-filter:blur(20px);box-shadow:0 1px 0 rgba(28,34,128,.07);padding:0;position:sticky;top:0;z-index:1030}
        .navbar-brand img{height:52px;object-fit:contain}
        .page-hero{background:linear-gradient(135deg,#080B1A 0%,#1C2280 55%,#0D1035 100%);padding:90px 0 80px;position:relative;overflow:hidden}
        #site-header{min-height:115px}
        @media(max-width:991px){#site-header{min-height:70px}}
    </style>

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
    <meta name="author" content="Vortexsoft Group">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="rating" content="general">
    <meta name="revisit-after" content="5 days">
    <meta name="language" content="English">

    <link rel="canonical" href="<?= htmlspecialchars($canonical_url) ?>">
    <link rel="alternate" hreflang="en" href="<?= htmlspecialchars($canonical_url) ?>">
    <link rel="alternate" hreflang="en-IN" href="<?= htmlspecialchars($canonical_url) ?>">
    <link rel="alternate" hreflang="en-US" href="<?= htmlspecialchars($canonical_url) ?>">
    <link rel="alternate" hreflang="x-default" href="<?= htmlspecialchars($canonical_url) ?>">

    <!-- GEO -->
    <meta name="geo.region" content="IN-KA">
    <meta name="geo.placename" content="Bengaluru, Karnataka, India">
    <meta name="geo.position" content="12.9141;77.6162">
    <meta name="ICBM" content="12.9141, 77.6162">

    <!-- OpenGraph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Vortexsoft Group">
    <meta property="og:locale" content="en_IN">
    <meta property="og:url" content="<?= htmlspecialchars($canonical_url) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($og_image) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@vortexsoft">
    <meta name="twitter:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($page_desc) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($og_image) ?>">

    <!-- Sitewide Breadcrumb & Location Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "BreadcrumbList",
          "itemListElement": [
            {
              "@type": "ListItem",
              "position": 1,
              "name": "Home",
              "item": "<?= SITE_URL ?>/"
            },
            {
              "@type": "ListItem",
              "position": 2,
              "name": "<?= htmlspecialchars(str_replace([' — Vortexsoft Group', ' — Vortexsoft', ' - Vortexsoft Group'], '', $page_title)) ?>",
              "item": "<?= htmlspecialchars($canonical_url) ?>"
            }
          ]
        },
        {
          "@type": "Corporation",
          "@id": "<?= SITE_URL ?>/#corporation",
          "name": "Vortexsoft Group",
          "legalName": "Vortexsoft Innovations Pvt. Ltd.",
          "url": "<?= SITE_URL ?>",
          "logo": "<?= SITE_URL ?>/logo-header.png",
          "contactPoint": [
            {
              "@type": "ContactPoint",
              "telephone": "+91-8308906690",
              "contactType": "customer service",
              "areaServed": ["IN", "US", "GB", "EU", "AU"],
              "availableLanguage": ["English", "Hindi"]
            },
            {
              "@type": "ContactPoint",
              "telephone": "+1-307-205-0681",
              "contactType": "sales",
              "areaServed": "US",
              "availableLanguage": "English"
            }
          ],
          "address": [
            {
              "@type": "PostalAddress",
              "streetAddress": "No.125, Ranganath Complex, Madiwala, HSR Layout 5th Sector",
              "addressLocality": "Bengaluru",
              "addressRegion": "Karnataka",
              "postalCode": "560068",
              "addressCountry": "IN"
            },
            {
              "@type": "PostalAddress",
              "streetAddress": "502, 4th Floor, Dangat Patil Empire, Kudale Baug, Vadgaon Budruk",
              "addressLocality": "Pune",
              "addressRegion": "Maharashtra",
              "postalCode": "411041",
              "addressCountry": "IN"
            },
            {
              "@type": "PostalAddress",
              "streetAddress": "30 N Gould St Ste 100",
              "addressLocality": "Sheridan",
              "addressRegion": "WY",
              "postalCode": "82801",
              "addressCountry": "US"
            }
          ]
        }
      ]
    }
    </script>

    <!-- PWA -->
    <link rel="manifest" href="<?= $prefix ?>manifest.json">
    <meta name="theme-color" content="#1C2280">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Vortexsoft">

    <!-- Resource Hints -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Icons -->
    <link rel="icon" href="<?= $prefix ?>icon.jpg" sizes="32x32">
    <link rel="icon" href="<?= $prefix ?>icon.jpg" sizes="192x192">
    <link rel="apple-touch-icon" href="<?= $prefix ?>icon.jpg">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= $prefix ?>assets/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $prefix ?>assets/vendor/fontawesome/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="<?= $prefix ?>assets/vendor/fontawesome/all.min.css"></noscript>
    <link rel="stylesheet" href="<?= $prefix ?>assets/vendor/fonts.css">
    <link rel="stylesheet" href="<?= $prefix ?>assets/vortex-shared.css?v=20260810">
    <style>.scroll-reveal,.scroll-reveal-left,.scroll-reveal-right{opacity:1;transform:none;}</style>

    <?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
<body>

<div id="page-loader">
    <div class="loader-content">
        <div class="loader-brand">
            <span class="l-blue">VORTEX</span><span class="l-red">SOFT</span>
        </div>
        <div class="loader-sub">INNOVATIONS</div>
        <div class="loader-status">
            <span>INITIALIZING</span>
            <span class="loading-pct" id="loader-pct">100%</span>
        </div>
        <div class="loader-track-new">
            <div class="loader-bar-new"></div>
        </div>
        <div class="loader-tagline">YOUR GLOBAL AI &amp; BPO PARTNER</div>
    </div>
</div>
<script>
(function(){
  function dismissLoader(){
    var l = document.getElementById('page-loader');
    if (l && !l.classList.contains('hide')) {
      l.classList.add('hide');
      setTimeout(function(){ if (l && l.parentNode) l.parentNode.removeChild(l); }, 400);
    }
  }
  setTimeout(dismissLoader, 150);
  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    dismissLoader();
  } else {
    document.addEventListener('DOMContentLoaded', dismissLoader);
    window.addEventListener('load', dismissLoader);
  }
})();
</script>

<!-- ═══════ SITE HEADER ═══════ -->
<div id="site-header">

<!-- ── TOPBAR ────────────────────────────────────── -->
<style>
.topbar{background:linear-gradient(90deg,#080B1A 0%,#111536 100%);padding:11px 0;font-size:12.5px;font-weight:500;color:rgba(255,255,255,.75);border-bottom:1px solid rgba(255,255,255,.06)}
.topbar a{color:rgba(255,255,255,.75);text-decoration:none;transition:color .2s;display:inline-flex;align-items:center;gap:6px}
.topbar a:hover{color:#fff}
.topbar i{font-size:11px;color:#CC2228}
.topbar-badge{background:#CC2228;color:#fff;font-size:10px;font-weight:800;padding:3px 13px;border-radius:100px;letter-spacing:1.2px;text-transform:uppercase}
.topbar-sep{width:1px;height:14px;background:rgba(255,255,255,.12);display:inline-block;vertical-align:middle}
.topbar-social a{color:#fff !important;width:29px;height:29px;border-radius:7px;background:rgba(255,255,255,.15);display:inline-flex;align-items:center;justify-content:center;font-size:11.5px;transition:background .2s,color .2s}
.topbar-social a:hover{background:#CC2228;color:#fff !important}
.topbar-social a i{color:#ffffff !important;font-size:13px}
#mainNavbar{background:rgba(255,255,255,0.88);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid rgba(255,255,255,0.9);padding:0;position:sticky;top:15px;z-index:1030;box-shadow:0 12px 40px rgba(28,34,128,.08);transition:all .4s ease;width:96%;max-width:1320px;margin:0 auto 15px;border-radius:20px}
#mainNavbar.scrolled{top:10px;background:rgba(255,255,255,0.96);box-shadow:0 15px 50px rgba(28,34,128,.12)}
#mainNavbar .container{min-height:72px;display:flex;align-items:center}
.navbar-brand img{height:58px;object-fit:contain}
#mainNavbar .nav-link{font-family:'Poppins',sans-serif;font-size:14.5px;font-weight:600;color:#1a1d3a !important;padding:8px 15px !important;border-radius:8px;position:relative;transition:color .2s,background .2s;letter-spacing:.1px}
#mainNavbar .nav-link::after{content:'';position:absolute;bottom:3px;left:15px;right:15px;height:2.5px;background:#CC2228;border-radius:2px;transform:scaleX(0);transform-origin:left;transition:transform .25s ease}
#mainNavbar .nav-link:hover,#mainNavbar .nav-link.active{color:#1C2280 !important;background:rgba(28,34,128,.05)}
#mainNavbar .nav-link:hover::after,#mainNavbar .nav-link.active::after{transform:scaleX(1)}
#mainNavbar .dropdown-menu{border:none;border-radius:14px;box-shadow:0 20px 60px rgba(28,34,128,.14),0 0 0 1px rgba(28,34,128,.06);padding:20px;margin-top:8px;animation:dropFade .18s ease}
@keyframes dropFade{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
.mega-menu-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:0 8px}
#mainNavbar .dropdown-header{font-family:'Poppins',sans-serif;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#1C2280;padding:10px 12px 6px;border-bottom:1.5px solid rgba(28,34,128,.1);margin-bottom:4px}
#mainNavbar .dropdown-item{font-size:13px;font-weight:500;color:#2d3060;padding:6px 12px;border-radius:8px;transition:background .15s,color .15s,padding-left .15s;display:flex;align-items:center;gap:8px}
#mainNavbar .dropdown-item i{font-size:12px;width:16px;color:#CC2228;opacity:.8}
#mainNavbar .dropdown-item:hover{background:rgba(28,34,128,.06);color:#1C2280;padding-left:18px}
#mainNavbar .nav-cta{background:linear-gradient(135deg,#CC2228 0%,#a31720 100%) !important;color:#fff !important;font-family:'Poppins',sans-serif;font-size:13.5px !important;font-weight:700 !important;padding:11px 24px !important;border-radius:10px !important;letter-spacing:.2px;box-shadow:0 4px 16px rgba(204,34,40,.28) !important;transition:transform .2s,box-shadow .2s !important;white-space:nowrap}
#mainNavbar .nav-cta::after{display:none !important}
#mainNavbar .nav-cta i{color:#ffffff !important}
#mainNavbar .nav-cta:hover{color:#fff !important;transform:translateY(-2px) !important;box-shadow:0 8px 24px rgba(204,34,40,.4) !important}
.navbar-phone-info{border-left:1.5px solid rgba(28,34,128,.1);padding-left:18px;margin-left:6px}
.pn-number{font-family:'Poppins',sans-serif;font-size:12.5px;font-weight:700;color:#1a1d3a;white-space:nowrap;line-height:1.4}
.pn-hours{font-size:11px;color:rgba(28,34,128,.55);font-weight:500}
.navbar-toggler{border:none !important;padding:6px;box-shadow:none !important;outline:none !important}
@media(max-width:991px){#mainNavbar .dropdown-menu{min-width:unset !important;box-shadow:0 4px 20px rgba(0,0,0,.1)}.mega-menu-grid{grid-template-columns:1fr 1fr}#mainNavbar .nav-cta{margin:10px 14px 6px;display:inline-flex !important}}
</style>

<div class="topbar d-none d-md-block">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <span class="topbar-badge">Vortexsoft Group</span>
        <span class="topbar-sep"></span>
        <a href="mailto:<?= EMAIL_SUPPORT ?>"><i class="fas fa-envelope"></i> <?= EMAIL_SUPPORT ?></a>
        <span class="topbar-sep d-none d-lg-inline-block"></span>
        <a href="tel:<?= str_replace(['-',' '], '', PHONE_INDIA) ?>" class="d-none d-lg-inline-flex"><i class="fas fa-phone"></i> <?= PHONE_INDIA ?></a>
        <span class="topbar-sep d-none d-xl-inline-block"></span>
        <span class="d-none d-xl-inline-flex align-items-center gap-2"><i class="fas fa-map-marker-alt"></i> Bengaluru &amp; Pune, India</span>
      </div>
      <div class="topbar-social d-flex align-items-center gap-1">
        <a href="<?= SOCIAL_FACEBOOK ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="<?= SOCIAL_LINKEDIN ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
      </div>
    </div>
  </div>
</div>

<!-- ── MAIN NAVBAR ────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg" id="mainNavbar">
  <div class="container">
    <a class="navbar-brand" href="<?= $prefix ?>index.php">
      <img src="<?= $prefix ?>logo-header.png" alt="Vortexsoft Innovations — Global IT &amp; BPO Company">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
            aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <i class="fas fa-bars" style="color:#1C2280;font-size:22px;"></i>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link <?= nav_active('home', $current_path) ?>" href="<?= $prefix ?>index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link <?= nav_active('about', $current_path) ?>" href="<?= $prefix ?>about.php">About</a></li>

        <!-- Services Mega Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= nav_active('service', $current_path) ?>" href="<?= $prefix ?>service.php"
             id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Services</a>
          <ul class="dropdown-menu p-3" style="min-width:760px;">
            <div class="mega-menu-grid">
              <!-- Col 1: AI Automations + Publishing -->
              <div>
                <h6 class="dropdown-header" style="color:#CC2228;"><i class="fas fa-robot me-1"></i> AI &amp; Automations</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>service.php#ai-automation"><i class="fas fa-brain"></i> AI Automation Services</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>service.php#ai-automation"><i class="fas fa-cogs"></i> Agentic AI Workflows</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>service.php#ai-automation"><i class="fas fa-file-contract"></i> Intelligent Doc Processing</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>data-annotation-services/index.php"><i class="fas fa-tags"></i> AI Data Annotation</a></li>
                <h6 class="dropdown-header mt-2">Publishing</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>publishing-services/index.php"><i class="fas fa-book"></i> Publishing Services</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>editorial-services/index.php"><i class="fas fa-pencil-alt"></i> Editorial Services</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>digital-prepress-services/index.php"><i class="fas fa-print"></i> Digital Prepress</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>ebook-conversion-services/index.php"><i class="fas fa-tablet-alt"></i> eBook Conversion</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>alt-text-writing-services/index.php"><i class="fas fa-image"></i> Alt Text Writing</a></li>
              </div>
              <!-- Col 2: Health Care + Real Estate -->
              <div>
                <h6 class="dropdown-header">Health Care</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>medical-coding-services/index.php"><i class="fas fa-code"></i> Medical Coding</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>medical-billing-services/index.php"><i class="fas fa-file-invoice-dollar"></i> Medical Billing</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>payment-posting-services/index.php"><i class="fas fa-credit-card"></i> Payment Posting</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>denial-management-services/index.php"><i class="fas fa-ban"></i> Denial Management</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>ar-recovery-services/index.php"><i class="fas fa-undo"></i> AR Recovery</a></li>
                <h6 class="dropdown-header mt-2">Real Estate</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>cam-audit-services/index.php"><i class="fas fa-search-dollar"></i> CAM Audit</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>cam-reconciliation-services/index.php"><i class="fas fa-balance-scale"></i> CAM Reconciliation</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>lease-administration-services/index.php"><i class="fas fa-file-contract"></i> Lease Administration</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>lease-abstraction-services/index.php"><i class="fas fa-scroll"></i> Lease Abstraction</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>property-accounting-services/index.php"><i class="fas fa-calculator"></i> Property Accounting</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>property-management-service/index.php"><i class="fas fa-building"></i> Property Management</a></li>
              </div>
              <!-- Col 3: Accounting + IT & Digital -->
              <div>
                <h6 class="dropdown-header">Accounting</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>bookkeeping-services/index.php"><i class="fas fa-book-open"></i> Bookkeeping</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>payroll-processing-services/index.php"><i class="fas fa-money-check-alt"></i> Payroll Processing</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>manpower-payroll-services/index.php"><i class="fas fa-users-cog"></i> Manpower &amp; Payroll</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>financial-reporting-services/index.php"><i class="fas fa-chart-bar"></i> Financial Reporting</a></li>
                <h6 class="dropdown-header mt-2">IT &amp; Digital</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>software-solutions/index.php"><i class="fas fa-laptop-code"></i> Software Solutions</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>digital-marketing-service/index.php"><i class="fas fa-bullhorn"></i> Digital Marketing</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>ecommerce-solutions/index.php"><i class="fas fa-shopping-cart"></i> E-Commerce</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>data-analytics-as-a-service/index.php"><i class="fas fa-chart-line"></i> Data Analytics</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>crm-software-services/index.php"><i class="fas fa-users"></i> CRM Services</a></li>
              </div>
              <!-- Col 4: More Services + View All -->
              <div>
                <h6 class="dropdown-header">More Services</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>logistics-services/index.php"><i class="fas fa-truck"></i> Logistics</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>title-settlement/index.php"><i class="fas fa-home"></i> Title &amp; Settlement</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>mortgage-escrow/index.php"><i class="fas fa-landmark"></i> Mortgage &amp; Escrow</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>background-verification-service/index.php"><i class="fas fa-user-check"></i> Background Verification</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>technical-writing/index.php"><i class="fas fa-file-alt"></i> Technical Writing</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>digital-accessibility-services/index.php"><i class="fas fa-universal-access"></i> Digital Accessibility</a></li>
                <h6 class="dropdown-header mt-2">View All</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>service.php" style="font-weight:700;color:#CC2228;"><i class="fas fa-th-large"></i> All Services</a></li>
              </div>
            </div>
          </ul>
        </li>

        <li class="nav-item"><a class="nav-link <?= nav_active('careers', $current_path) ?>" href="<?= $prefix ?>careers.php">Careers</a></li>
        <li class="nav-item"><a class="nav-link <?= nav_active('blog', $current_path) ?>" href="<?= $prefix ?>blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link <?= nav_active('contact', $current_path) ?>" href="<?= $prefix ?>contact.php">Contact</a></li>
      </ul>

      <!-- Phone Info -->
      <div class="d-none d-xl-flex align-items-center navbar-phone-info">
        <div>
          <div class="pn-number"><i class="fas fa-phone-alt" style="color:#CC2228;font-size:11px;margin-right:5px;"></i><?= PHONE_INDIA ?></div>
          <div class="pn-hours">24/7 Online Inquiry &nbsp;|&nbsp; Mon–Sat 9AM–6PM IST</div>
        </div>
      </div>

      <!-- CTA Button -->
      <a href="<?= $prefix ?>contact.php" class="nav-link nav-cta ms-3 d-none d-lg-inline-flex align-items-center gap-2 magnetic">
        <i class="fas fa-paper-plane"></i> Get Free Quote
      </a>
    </div>
  </div>
</nav>
</div><!-- /#site-header -->
