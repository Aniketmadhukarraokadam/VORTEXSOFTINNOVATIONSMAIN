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
$page_title   = $page_title ?? 'Top Global IT & BPO Outsourcing Company in India | AI Services | Vortexsoft Innovations Private Limited';
$page_desc    = $page_desc ?? 'Vortexsoft Innovations Private Limited — ISO 27001 certified global IT & BPO outsourcing company in Pune & Bengaluru, India. Expert in AI Solutions, Healthcare BPO, Publishing, Real Estate, Data Annotation & 75+ services for 150+ clients worldwide.';
$page_keywords = $page_keywords ?? 'Vortexsoft, Vortexsoft Innovations, Vortexsoft Innovations Private Limited, Vortexsoft Innovations Pvt Ltd, Vortex Soft, Vortex Innovations, Vortex, Vertex, Vortex Group, IT outsourcing company India, BPO company Pune, BPO company Bengaluru, AI solutions company India, Healthcare BPO India, Medical Coding outsourcing, ICD-10 CPT medical billing services, Revenue Cycle Management RCM outsourcing, denial management AR recovery, AI Data Annotation India, machine learning training datasets, computer vision bounding box annotation, LiDAR 3D point cloud labeling, RLHF training data outsourcing, Enterprise Workforce OS, VortexEXHO, vortexHire, vortexKonnect, Vortexreach, vortexsoftpublishing, vortexsofthrms, S1000D XML conversion, prepress typesetting outsourcing, commercial lease abstraction services, CAM audit property accounting, mortgage title search settlement typing, offshore development center India, ISO 27001 BPO company, HIPAA compliant healthcare outsourcing, AI HRMS payroll automation, hire dedicated developers Pune Bengaluru';
$canonical_url = $canonical_url ?? SITE_URL . '/';
$og_image     = $og_image ?? (SITE_URL . '/assets/images/vortexsoft-share-banner.png');

// Detect active page & path
$current_path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$canonical_url = $canonical_url ?? (SITE_URL . $current_path);
$com_url       = PRIMARY_COM_URL . $current_path;
$in_url        = PRIMARY_IN_URL . $current_path;
$og_image     = $og_image ?? (SITE_URL . '/assets/images/vortexsoft-share-banner.png');

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
        html,body{width:100%;max-width:100%;overflow-x:clip;overflow-x:hidden}
        body{font-family:'Inter',sans-serif;color:#0D0F2B;background:#fff;line-height:1.7}
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
    <meta name="author" content="Vortexsoft Innovations Private Limited">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="rating" content="general">
    <meta name="revisit-after" content="5 days">
    <meta name="language" content="English">

    <meta name="keywords" content="<?= htmlspecialchars($page_keywords) ?>">
    <meta name="news_keywords" content="Vortexsoft Innovations, Vortexsoft, AI outsourcing India, Healthcare RCM, Medical Coding, Data Annotation, IT Services Pune Bengaluru, VortexEXHO, vortexsofthrms, Lease Abstraction, S1000D XML">

    <link rel="canonical" href="<?= htmlspecialchars($canonical_url) ?>">
    <link rel="alternate" hreflang="en-IN" href="<?= htmlspecialchars($in_url) ?>">
    <link rel="alternate" hreflang="en-US" href="<?= htmlspecialchars($com_url) ?>">
    <link rel="alternate" hreflang="x-default" href="<?= htmlspecialchars($com_url) ?>">

    <!-- GEO & Local Business Coordinates (Pune HQ, Bengaluru Delivery Hub & US Entity) -->
    <meta name="geo.region" content="IN-MH">
    <meta name="geo.placename" content="Pune, Maharashtra, India">
    <meta name="geo.position" content="18.4792;73.8329">
    <meta name="ICBM" content="18.4792, 73.8329">
    <meta name="format-detection" content="telephone=yes, address=yes, email=yes">

    <!-- OpenGraph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Vortexsoft Innovations Private Limited">
    <meta property="og:locale" content="en_IN">
    <meta property="og:locale:alternate" content="en_US">
    <meta property="og:url" content="<?= htmlspecialchars($canonical_url) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($og_image) ?>">
    <meta property="og:image:secure_url" content="<?= htmlspecialchars($og_image) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:alt" content="Vortexsoft Innovations Private Limited — Global AI, IT &amp; BPO Partner">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@vortexsoft">
    <meta name="twitter:creator" content="@vortexsoft">
    <meta name="twitter:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($page_desc) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($og_image) ?>">
    <meta name="twitter:image:alt" content="Vortexsoft Innovations Private Limited">

    <!-- Sitewide Breadcrumb, WebSite, Corporation & Knowledge Graph Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "WebSite",
          "@id": "<?= SITE_URL ?>/#website",
          "url": "<?= SITE_URL ?>",
          "name": "Vortexsoft Innovations Private Limited",
          "alternateName": ["Vortexsoft", "Vortex Soft", "Vortex Innovations", "Vortex", "Vertex"],
          "description": "Global AI, IT Outsourcing & BPO Services — Healthcare RCM, Data Annotation, Software Engineering",
          "potentialAction": {
            "@type": "SearchAction",
            "target": {
              "@type": "EntryPoint",
              "urlTemplate": "<?= SITE_URL ?>/service.php?q={search_term_string}"
            },
            "query-input": "required name=search_term_string"
          }
        },
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
              "name": "<?= htmlspecialchars(str_replace([' — Vortexsoft Innovations Private Limited', ' — Vortexsoft', ' - Vortexsoft Innovations Private Limited'], '', $page_title)) ?>",
              "item": "<?= htmlspecialchars($canonical_url) ?>"
            }
          ]
        },
        {
          "@type": "Corporation",
          "@id": "<?= SITE_URL ?>/#corporation",
          "name": "Vortexsoft Innovations Private Limited",
          "legalName": "Vortexsoft Innovations Pvt. Ltd.",
          "alternateName": ["Vortexsoft", "Vortex Soft", "Vortex Innovations", "Vortex", "Vertex", "Vortex Group"],
          "url": "<?= SITE_URL ?>",
          "logo": "<?= SITE_URL ?>/logo-header.png",
          "image": "<?= SITE_URL ?>/logo-header.png",
          "foundingDate": "2020",
          "founder": {
            "@type": "Person",
            "name": "Aniket Madhukarrao Kadam"
          },
          "numberOfEmployees": {
            "@type": "QuantitativeValue",
            "value": "200+"
          },
          "slogan": "Your Global AI, IT & BPO Partner",
          "description": "Vortexsoft Innovations Private Limited is an ISO 27001:2013 certified and HIPAA-compliant global IT and BPO outsourcing company founded in 2020. Headquartered in Pune with a technology delivery center in Bengaluru and a corporate entity in Wyoming, USA, Vortexsoft delivers 75+ enterprise services across AI Solutions, Healthcare BPO/RCM, AI Data Annotation, Custom Software Development, and STM Publishing for 150+ international clients.",
          "award": [
            "ISO 27001:2013 Information Security Certified",
            "ISO 9001:2015 Quality Management Certified",
            "HIPAA Compliant Healthcare BPO Operations",
            "Startup India Registered (DPIIT)"
          ],
          "sameAs": [
            "https://www.linkedin.com/company/vortexsoft-innovations-private-limited/",
            "https://www.facebook.com/profile.php?id=61575505273718",
            "https://www.instagram.com/vortexsoft_innovations"
          ],
          "knowsAbout": [
            "Healthcare Revenue Cycle Management (RCM)",
            "Medical Coding ICD-10-CM & CPT",
            "Denial Management & Accounts Receivable Recovery",
            "AI Data Annotation & Computer Vision",
            "3D LiDAR Point Cloud & Sensor Fusion Annotation",
            "Reinforcement Learning from Human Feedback (RLHF)",
            "Custom Web & Enterprise Software Development",
            "Dedicated Offshore Development Center (ODC) India",
            "Agentic AI Workflow Automation",
            "S1000D XML Conversion & Prepress Typesetting",
            "ePUB3 & Section 508 WCAG Accessibility Remediation",
            "Commercial Real Estate Lease Abstraction",
            "CAM Audit & Property Accounting",
            "Mortgage Title Search & Settlement Typing",
            "ISO 27001:2013 Information Security Protocols",
            "HIPAA Compliance Data Standards",
            "Enterprise HRMS & Autonomous Payroll Automation"
          ],
          "contactPoint": [
            {
              "@type": "ContactPoint",
              "telephone": "+91-8308906690",
              "contactType": "customer service",
              "areaServed": ["IN", "US", "GB", "EU", "AU", "CA"],
              "availableLanguage": ["English", "Hindi"]
            },
            {
              "@type": "ContactPoint",
              "telephone": "+1-307-205-0681",
              "contactType": "sales",
              "areaServed": ["US", "CA"],
              "availableLanguage": "English"
            }
          ],
          "address": [
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
              "streetAddress": "No.125, Ranganath Complex, Madiwala, HSR Layout 5th Sector",
              "addressLocality": "Bengaluru",
              "addressRegion": "Karnataka",
              "postalCode": "560068",
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
          ],
          "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Vortexsoft Innovations Global Service Portfolio",
            "itemListElement": [
              {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Healthcare BPO & Revenue Cycle Management (RCM)"}},
              {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "AI Data Annotation & Computer Vision Training Data"}},
              {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Custom Software Engineering & Offshore Development Center"}},
              {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Publishing Prepress, ePUB3 & S1000D XML Conversion"}},
              {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Commercial Real Estate Lease Abstraction & CAM Reconciliation"}},
              {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Mortgage Title Search & Settlement Support"}},
              {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Offshore Bookkeeping, Financial Reporting & Payroll BPO"}},
              {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Digital Marketing, SEO & B2B Lead Generation"}},
              {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Dedicated Manpower Staffing & Workforce Solutions"}}
            ]
          }
        },
        {
          "@type": ["LocalBusiness", "ProfessionalService"],
          "@id": "<?= SITE_URL ?>/#headquarters-pune",
          "name": "Vortexsoft Innovations Private Limited - Global Corporate Headquarters",
          "legalName": "Vortexsoft Innovations Private Limited",
          "alternateName": ["Vortexsoft Pune HQ", "Vortex Soft Pune"],
          "url": "<?= SITE_URL ?>",
          "telephone": "+91-8308906690",
          "email": "support@vortexsoftinnovations.com",
          "image": "<?= SITE_URL ?>/logo-header.png",
          "hasMap": "https://share.google/XKt2SVYsKfiNqrVGx",
          "priceRange": "$$",
          "openingHours": "Mo-Sa 09:00-18:00",
          "address": {
            "@type": "PostalAddress",
            "streetAddress": "502, 4th Floor, Dangat Patil Empire, Kudale Baug, Vadgaon Budruk",
            "addressLocality": "Pune",
            "addressRegion": "Maharashtra",
            "postalCode": "411041",
            "addressCountry": "IN"
          },
          "geo": {
            "@type": "GeoCoordinates",
            "latitude": 18.4792,
            "longitude": 73.8329
          }
        },
        {
          "@type": ["LocalBusiness", "ProfessionalService"],
          "@id": "<?= SITE_URL ?>/#delivery-center-bengaluru",
          "name": "Vortexsoft Innovations Private Limited - Tech Delivery Center",
          "alternateName": ["Vortexsoft Bengaluru Center", "Vortexsoft HSR Layout"],
          "url": "<?= SITE_URL ?>",
          "telephone": "+91-8308906690",
          "email": "support@vortexsoftinnovations.com",
          "priceRange": "$$",
          "openingHours": "Mo-Sa 09:00-18:00",
          "address": {
            "@type": "PostalAddress",
            "streetAddress": "No.125, Ranganath Complex, Madiwala, HSR Layout 5th Sector",
            "addressLocality": "Bengaluru",
            "addressRegion": "Karnataka",
            "postalCode": "560068",
            "addressCountry": "IN"
          },
          "geo": {
            "@type": "GeoCoordinates",
            "latitude": 12.9226,
            "longitude": 77.6258
          }
        },
        {
          "@type": ["LocalBusiness", "ProfessionalService"],
          "@id": "<?= SITE_URL ?>/#us-entity-sheridan",
          "name": "Vortexsoft Innovations Private Limited - USA Entity",
          "alternateName": ["Vortexsoft Innovations USA"],
          "url": "<?= SITE_URL ?>",
          "telephone": "+1-307-205-0681",
          "email": "support@vortexsoftinnovations.com",
          "priceRange": "$$",
          "address": {
            "@type": "PostalAddress",
            "streetAddress": "30 N Gould St Ste 100",
            "addressLocality": "Sheridan",
            "addressRegion": "WY",
            "postalCode": "82801",
            "addressCountry": "US"
          },
          "geo": {
            "@type": "GeoCoordinates",
            "latitude": 44.7972,
            "longitude": -106.9562
          }
        },
        {
          "@type": "SoftwareApplication",
          "@id": "<?= SITE_URL ?>/#vortexexho",
          "name": "VortexEXHO",
          "alternateName": "Vortex EXHO",
          "applicationCategory": "BusinessApplication",
          "operatingSystem": "Web Cloud",
          "description": "Enterprise Workforce Operating System combining ATS, HRMS, payroll, LMS, and an AI copilot into a unified platform for mid-size and enterprise workforce operations.",
          "creator": {"@type": "Organization", "name": "Vortexsoft Innovations Private Limited"}
        },
        {
          "@type": "SoftwareApplication",
          "@id": "<?= SITE_URL ?>/#vortexhire",
          "name": "vortexHire",
          "alternateName": "Vortex Hire",
          "applicationCategory": "HumanResourcesApplication",
          "operatingSystem": "Web Cloud",
          "description": "AI candidate screening, resume parsing, multi-tier automated qualification, and predictive recruitment intelligence platform.",
          "creator": {"@type": "Organization", "name": "Vortexsoft Innovations Private Limited"}
        },
        {
          "@type": "SoftwareApplication",
          "@id": "<?= SITE_URL ?>/#vortexkonnect",
          "name": "vortexKonnect",
          "alternateName": "Vortex Konnect",
          "applicationCategory": "AnalyticsApplication",
          "operatingSystem": "Web Cloud",
          "description": "AI call center conversation analytics, acoustic sentiment intelligence, automated compliance auditing, and agent coaching platform.",
          "creator": {"@type": "Organization", "name": "Vortexsoft Innovations Private Limited"}
        },
        {
          "@type": "SoftwareApplication",
          "@id": "<?= SITE_URL ?>/#vortexreach",
          "name": "Vortexreach",
          "alternateName": "Vortex Reach",
          "applicationCategory": "SalesApplication",
          "operatingSystem": "Web Cloud",
          "description": "Autonomous B2B sales outreach, prospect intelligence research, personalized messaging drafting, and multi-channel sequencing platform.",
          "creator": {"@type": "Organization", "name": "Vortexsoft Innovations Private Limited"}
        },
        {
          "@type": "SoftwareApplication",
          "@id": "<?= SITE_URL ?>/#vortexsoftpublishing",
          "name": "vortexsoftpublishing",
          "alternateName": "Vortexsoft Publishing",
          "applicationCategory": "PublishingApplication",
          "operatingSystem": "Web Cloud",
          "description": "Automated publishing prepress engine for ePUB3/Kindle conversion, XML structuring (JATS, BITS, S1000D), and Section 508 / WCAG 2.1 AA accessibility tagging.",
          "creator": {"@type": "Organization", "name": "Vortexsoft Innovations Private Limited"}
        },
        {
          "@type": "SoftwareApplication",
          "@id": "<?= SITE_URL ?>/#vortexsofthrms",
          "name": "vortexsofthrms",
          "alternateName": "Vortexsoft HRMS",
          "applicationCategory": "HumanResourcesApplication",
          "operatingSystem": "Web Cloud",
          "description": "Enterprise Human Resource Management System with AI automations for autonomous payroll calculation, biometric attendance sync, automated employee onboarding, performance KPI appraisal, AI leave workflows, and predictive workforce analytics.",
          "creator": {"@type": "Organization", "name": "Vortexsoft Innovations Private Limited"}
        },
        {
          "@type": "WebPage",
          "@id": "<?= htmlspecialchars($canonical_url) ?>#webpage",
          "url": "<?= htmlspecialchars($canonical_url) ?>",
          "name": "<?= htmlspecialchars($page_title) ?>",
          "speakable": {
            "@type": "SpeakableSpecification",
            "cssSelector": [".geo-fact-block", ".hero-title", ".section-subtitle", ".section-title"]
          }
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

    <!-- Favicons & Touch Icons (Latest Company Brand Emblem) -->
    <link rel="icon" type="image/x-icon" href="<?= $prefix ?>favicon.ico?v=20260912">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $prefix ?>favicon-32x32.png?v=20260912">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $prefix ?>favicon-16x16.png?v=20260912">
    <link rel="icon" type="image/png" sizes="48x48" href="<?= $prefix ?>favicon-48x48.png?v=20260912">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= $prefix ?>icon-192.png?v=20260912">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $prefix ?>apple-touch-icon.png?v=20260912">
    <link rel="icon" type="image/jpeg" sizes="1024x1024" href="<?= $prefix ?>icon.jpg?v=20260912">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= $prefix ?>assets/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $prefix ?>assets/vendor/fontawesome/all.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="<?= $prefix ?>assets/vendor/fonts.css">
    <link rel="stylesheet" href="<?= $prefix ?>assets/vortex-shared.css?v=20260917">
    <style>@media (prefers-reduced-motion: reduce) { .scroll-reveal, .scroll-reveal-left, .scroll-reveal-right { opacity: 1 !important; transform: none !important; transition: none !important; } }</style>

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
#mainNavbar{background:rgba(255,255,255,0.88);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid rgba(255,255,255,0.9);padding:0;position:sticky;top:15px;z-index:1030;box-shadow:0 12px 40px rgba(28,34,128,.08);transition:all .4s ease;width:96%;max-width:1740px;margin:0 auto 15px;border-radius:20px}
@media(min-width:2560px){#mainNavbar{max-width:2100px}}
#mainNavbar.scrolled{top:10px;background:rgba(255,255,255,0.96);box-shadow:0 15px 50px rgba(28,34,128,.12)}
#mainNavbar .container{min-height:72px;display:flex;align-items:center}
.navbar-brand img{height:54px;max-height:58px;width:auto;object-fit:contain;transition:all .3s ease;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.05))}
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
        <span class="topbar-badge">Vortexsoft Innovations Private Limited</span>
        <span class="topbar-sep"></span>
        <a href="mailto:<?= EMAIL_SUPPORT ?>"><i class="fas fa-envelope"></i> <?= EMAIL_SUPPORT ?></a>
        <span class="topbar-sep d-none d-lg-inline-block"></span>
        <a href="tel:<?= str_replace(['-',' '], '', PHONE_INDIA) ?>" class="d-none d-lg-inline-flex"><i class="fas fa-phone"></i> <?= PHONE_INDIA ?></a>
        <span class="topbar-sep d-none d-xl-inline-block"></span>
        <span class="d-none d-xl-inline-flex align-items-center gap-2"><i class="fas fa-map-marker-alt"></i> Pune HQ &amp; Bengaluru, India | USA</span>
      </div>
      <div class="topbar-social d-flex align-items-center gap-1">
        <a href="<?= SOCIAL_FACEBOOK ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="<?= SOCIAL_LINKEDIN ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
      </div>
    </div>
  </div>
</div>

<!-- ── MAIN NAVBAR ────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg" id="mainNavbar">
  <div class="container">
    <a class="navbar-brand" href="<?= $prefix ?>index.php">
      <img src="<?= $prefix ?>logo-header.png?v=20260912" alt="Vortexsoft Innovations — Global IT &amp; BPO Company" width="240" height="70">
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
                <h6 class="dropdown-header">Accounting &amp; HR Software</h6>
                <li style="background:rgba(225,29,72,0.08);border-radius:8px;margin:2px 0;"><a class="dropdown-item fw-bold" href="<?= $prefix ?>index.php#ai-products" style="color:#e11d48;"><i class="fas fa-users-cog text-danger"></i> HR &amp; Payroll Software <span class="badge bg-danger ms-1" style="font-size:9px;">AI SOFTWARE</span></a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>bookkeeping-services/index.php"><i class="fas fa-book-open"></i> Bookkeeping</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>payroll-processing-services/index.php"><i class="fas fa-money-check-alt"></i> Payroll Processing</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>manpower-payroll-services/index.php"><i class="fas fa-users"></i> Manpower &amp; Staffing</a></li>
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
              <!-- Featured Platform Strip -->
              <div class="pt-2 mt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2" style="grid-column: 1 / -1; font-size:12.5px; background:rgba(225,29,72,0.04); padding:8px 12px; border-radius:8px;">
                <span><i class="fas fa-star text-warning me-1"></i> Featured Platform: <strong style="color:#e11d48;">vortexsofthrms</strong> — Enterprise AI HRMS &amp; Autonomous Payroll Software</span>
                <a href="<?= $prefix ?>index.php#ai-products" class="btn btn-sm btn-danger py-1 px-3 fw-bold" style="font-size:11px;border-radius:6px;background:#e11d48;border-color:#e11d48;">Explore vortexsofthrms <i class="fas fa-arrow-right ms-1"></i></a>
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
