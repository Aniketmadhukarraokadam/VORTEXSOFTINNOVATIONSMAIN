<?php
/**
 * Vortexsoft Innovations — PHP Header Partial
 * Replaces assets/partials/header.js + header.html
 * Usage: require_once ROOT_PATH . '/includes/header.php';
 * Pass $page_title, $page_desc, $canonical_url, $prefix (default './')
 */

if (!ob_get_level() && !headers_sent()) {
    if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
        @ob_start('ob_gzhandler');
    } else {
        @ob_start();
    }
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
        #page-loader{position:fixed;inset:0;background:#0A0D1F;display:flex;flex-direction:column;align-items:center;justify-content:center;z-index:99999;transition:opacity .15s ease,visibility .15s ease;overflow:hidden;pointer-events:none}
        #page-loader.hide{opacity:0;visibility:hidden;display:none !important}
        .loader-content{position:relative;z-index:2;text-align:center;width:100%;max-width:480px;padding:0 20px}
        .loader-brand{font-family:'Poppins',sans-serif;font-size:clamp(32px,6vw,56px);font-weight:900;letter-spacing:4px;line-height:1;margin-bottom:12px;display:flex;justify-content:center}
        .l-blue{color:#3544C4}.l-red{color:#DE252A}
        .loader-sub{font-size:clamp(10px,2vw,15px);font-weight:400;letter-spacing:6px;color:#8F949F;margin-bottom:50px}
        .loader-status{display:flex;justify-content:space-between;font-size:13px;font-weight:600;letter-spacing:2px;color:#565C6B;margin-bottom:12px;font-family:'Inter',sans-serif;padding:0 4px}
        .loader-track-new{width:100%;height:5px;background:#181B34;border-radius:6px;overflow:hidden;margin-bottom:24px}
        .loader-bar-new{height:100%;width:0;background:linear-gradient(90deg,#3544C4,#943BA8,#DE252A);border-radius:6px;animation:loaderFillNew .3s cubic-bezier(.4,0,.2,1) forwards;box-shadow:0 0 10px rgba(148,59,168,.5)}
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
    <meta name="designer" content="Vortexsoft Innovations Enterprise Engineering">
    <meta name="publisher" content="Vortexsoft Innovations Private Limited">
    <meta name="owner" content="Vortexsoft Innovations Private Limited">
    <meta name="copyright" content="Copyright © 2026 Vortexsoft Innovations Private Limited. All Rights Reserved.">
    <meta name="reply-to" content="support@vortexsoftinnovations.com">
    <meta name="classification" content="Business, IT Outsourcing, Artificial Intelligence, Healthcare Technology, BPO, Software Development, Data Annotation">
    <meta name="target" content="all">
    <meta name="audience" content="Global Enterprises, Healthcare Providers, Real Estate Operators, STM Publishers, Tech Startups, Financial Institutions">
    <meta name="coverage" content="Worldwide (India, United States, United Kingdom, European Union, Australia, Canada)">
    <meta name="distribution" content="Global">
    <meta name="rating" content="General">
    <meta name="revisit-after" content="3 days">
    <meta name="language" content="English">
    <meta name="referrer" content="no-referrer-when-downgrade">
    <meta name="color-scheme" content="light dark">
    <meta name="format-detection" content="telephone=yes, address=yes, email=yes">
    <meta name="url" content="<?= htmlspecialchars($canonical_url) ?>">
    <meta name="identifier-URL" content="<?= htmlspecialchars($canonical_url) ?>">
    <meta name="directory" content="submission">
    <meta name="category" content="Information Technology &amp; Business Process Outsourcing">

    <!-- Search Engine Crawler Directives -->
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1, notranslate">
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="googlebot-news" content="index, follow">
    <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="slurp" content="index, follow">
    <meta name="duckduckbot" content="index, follow">
    <meta name="baiduspider" content="index, follow">
    <meta name="yandex" content="index, follow">

    <!-- AI Agent & LLM Crawlers (AEO / GEO Optimization for Perplexity, ChatGPT, Claude, Gemini) -->
    <meta name="GPTBot" content="index, follow">
    <meta name="ChatGPT-User" content="index, follow">
    <meta name="Google-Extended" content="index, follow">
    <meta name="PerplexityBot" content="index, follow">
    <meta name="Anthropic-ai" content="index, follow">
    <meta name="Claude-Web" content="index, follow">
    <meta name="Cohere-ai" content="index, follow">
    <meta name="Applebot-Extended" content="index, follow">
    <meta name="CCBot" content="index, follow">

    <!-- Keywords & Semantic Categorization -->
    <meta name="keywords" content="<?= htmlspecialchars($page_keywords) ?>">
    <meta name="news_keywords" content="Vortexsoft Innovations, Vortexsoft, AI outsourcing India, Healthcare RCM, Medical Coding, Data Annotation, IT Services Pune Bengaluru, VortexEXHO, vortexsofthrms, Lease Abstraction, S1000D XML, US Title Search">

    <!-- Canonical & Alternate Multi-Region URLs -->
    <link rel="canonical" href="<?= htmlspecialchars($canonical_url) ?>">
    <link rel="alternate" hreflang="en-IN" href="<?= htmlspecialchars($in_url) ?>">
    <link rel="alternate" hreflang="en-US" href="<?= htmlspecialchars($com_url) ?>">
    <link rel="alternate" hreflang="en-GB" href="<?= htmlspecialchars($com_url) ?>">
    <link rel="alternate" hreflang="en-AU" href="<?= htmlspecialchars($com_url) ?>">
    <link rel="alternate" hreflang="en-CA" href="<?= htmlspecialchars($com_url) ?>">
    <link rel="alternate" hreflang="x-default" href="<?= htmlspecialchars($com_url) ?>">

    <!-- Dublin Core (DC) Semantic Metadata for Enterprise & Research Discovery -->
    <meta name="DC.title" content="<?= htmlspecialchars($page_title) ?>">
    <meta name="DC.creator" content="Vortexsoft Innovations Private Limited">
    <meta name="DC.subject" content="AI Solutions, Healthcare BPO, RCM, Data Annotation, Custom Software Engineering, Prepress STM Publishing, Title Search, Lease Abstraction">
    <meta name="DC.description" content="<?= htmlspecialchars($page_desc) ?>">
    <meta name="DC.publisher" content="Vortexsoft Innovations Private Limited">
    <meta name="DC.contributor" content="Vortexsoft Innovations Private Limited">
    <meta name="DC.date" scheme="W3CDTF" content="2026-10-29">
    <meta name="DC.type" scheme="DCMIType" content="InteractiveResource">
    <meta name="DC.format" scheme="IMT" content="text/html">
    <meta name="DC.identifier" scheme="URI" content="<?= htmlspecialchars($canonical_url) ?>">
    <meta name="DC.language" scheme="ISO639-1" content="en">
    <meta name="DC.coverage" content="Worldwide">
    <meta name="DC.rights" content="Copyright © 2026 Vortexsoft Innovations Private Limited. All Rights Reserved.">

    <!-- GEO & Local Business Coordinates (Pune Global HQ & Bengaluru Tech Hub) -->
    <meta name="geo.region" content="IN-MH">
    <meta name="geo.placename" content="Pune, Maharashtra, India">
    <meta name="geo.position" content="18.4792;73.8329">
    <meta name="ICBM" content="18.4792, 73.8329">

    <!-- OpenGraph (Facebook, LinkedIn, Pinterest, WhatsApp, Slack, Teams) -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Vortexsoft Innovations Private Limited">
    <meta property="og:locale" content="en_IN">
    <meta property="og:locale:alternate" content="en_US">
    <meta property="og:locale:alternate" content="en_GB">
    <meta property="og:locale:alternate" content="en_AU">
    <meta property="og:url" content="<?= htmlspecialchars($canonical_url) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($og_image) ?>">
    <meta property="og:image:secure_url" content="<?= htmlspecialchars($og_image) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:alt" content="Vortexsoft Innovations Private Limited — Global AI, IT &amp; BPO Partner">
    <meta property="og:updated_time" content="2026-10-29T12:00:00+05:30">
    <meta property="article:publisher" content="https://www.linkedin.com/company/vortexsoft-innovations-private-limited/">
    <meta property="business:contact_data:street_address" content="502, 4th Floor, Dangat Patil Empire, Kudale Baug, Vadgaon Budruk">
    <meta property="business:contact_data:locality" content="Pune">
    <meta property="business:contact_data:region" content="Maharashtra">
    <meta property="business:contact_data:postal_code" content="411041">
    <meta property="business:contact_data:country_name" content="India">
    <meta property="business:contact_data:email" content="support@vortexsoftinnovations.com">
    <meta property="business:contact_data:phone_number" content="+91-8308906690">

    <!-- Twitter / X Cards Optimization -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@vortexsoft">
    <meta name="twitter:creator" content="@vortexsoft">
    <meta name="twitter:domain" content="vortexsoftinnovations.com">
    <meta name="twitter:url" content="<?= htmlspecialchars($canonical_url) ?>">
    <meta name="twitter:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($page_desc) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($og_image) ?>">
    <meta name="twitter:image:alt" content="Vortexsoft Innovations Private Limited">
    <meta name="twitter:label1" content="Services Portfolio">
    <meta name="twitter:data1" content="75+ Enterprise AI, IT &amp; BPO Services">
    <meta name="twitter:label2" content="Certifications">
    <meta name="twitter:data2" content="ISO 27001:2013 | HIPAA Compliant | Startup India">

    <!-- Marketing & Conversion Tracking DataLayer (GTM, GA4, Meta Pixel Ready) -->
    <script>
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
        'event': 'vortex_page_view',
        'page_title': <?= json_encode($page_title) ?>,
        'page_path': <?= json_encode($current_path) ?>,
        'canonical_url': <?= json_encode($canonical_url) ?>,
        'site_locale': 'en',
        'site_category': 'B2B Enterprise Technology & BPO',
        'timestamp': new Date().toISOString()
    });
    </script>

    <!-- DNS Prefetch & Preconnect Resource Hints -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

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
          "numberOfEmployees": {
            "@type": "QuantitativeValue",
            "value": "200+"
          },
          "slogan": "Your Global AI, IT & BPO Partner",
          "description": "Vortexsoft Innovations Private Limited is a CMMI Level 3 appraised, ISO 27001:2013 certified, and HIPAA-compliant global IT and BPO outsourcing company founded in 2020. Headquartered in Pune with a technology delivery center in Bengaluru and a corporate entity in Wyoming, USA, Vortexsoft delivers 75+ enterprise services across AI Solutions, Healthcare BPO/RCM, AI Data Annotation, Custom Software Development, and STM Publishing for 150+ international clients.",
          "award": [
            "CMMI Level 3 Appraised",
            "ISO 27001:2013 Information Security Certified",
            "ISO 9001:2015 Quality Management Certified",
            "HIPAA Compliant Healthcare BPO Operations",
            "Startup India Registered (DPIIT)"
          ],
          "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.9",
            "reviewCount": "148",
            "bestRating": "5",
            "worstRating": "1"
          },
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
    <link rel="stylesheet" href="<?= $prefix ?>assets/vortex-shared.css?v=20261029opt">
    <link rel="stylesheet" href="<?= $prefix ?>assets/usability-audit-fixes.css?v=20261029opt">
    <link rel="stylesheet" href="<?= $prefix ?>assets/vortex-extensions.css?v=20261029opt">
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
.topbar{background:linear-gradient(90deg,#111827 0%,#1a2068 100%);padding:10px 0;font-size:12.5px;font-weight:500;color:rgba(255,255,255,.8);border-bottom:1px solid rgba(255,255,255,.08)}
.topbar a{color:rgba(255,255,255,.8);text-decoration:none;transition:color .2s;display:inline-flex;align-items:center;gap:6px}
.topbar a:hover{color:#fff}
.topbar i{font-size:11px;color:#E31E25}
.topbar-badge{background:#E31E25;color:#fff;font-size:10px;font-weight:800;padding:3px 12px;border-radius:100px;letter-spacing:1px;text-transform:uppercase}
.topbar-sep{width:1px;height:14px;background:rgba(255,255,255,.15);display:inline-block;vertical-align:middle}
.topbar-social a{color:#fff !important;width:28px;height:28px;border-radius:7px;background:rgba(255,255,255,.15);display:inline-flex;align-items:center;justify-content:center;font-size:11.5px;transition:background .2s,color .2s}
.topbar-social a:hover{background:#E31E25;color:#fff !important}
.topbar-social a i{color:#ffffff !important;font-size:12.5px}

/* ═══════ SMART NAVBAR DESIGN SYSTEM ═══════ */
#mainNavbar{background:rgba(255,255,255,.96);backdrop-filter:blur(20px) saturate(180%);-webkit-backdrop-filter:blur(20px) saturate(180%);border:1px solid rgba(229,231,235,.85);padding:0;position:sticky;top:12px;z-index:1030;box-shadow:0 10px 30px rgba(17,24,39,.06);transition:all .28s cubic-bezier(.16,1,.3,1);width:96%;max-width:1680px;margin:0 auto 15px;border-radius:14px}
@media(min-width:2560px){#mainNavbar{max-width:2000px}}
#mainNavbar.scrolled{top:8px;background:rgba(255,255,255,.98);box-shadow:0 14px 40px rgba(17,24,39,.10);border-color:rgba(28,34,128,.14)}
#mainNavbar .container{min-height:68px;display:flex;align-items:center;justify-content:space-between;transition:min-height .25s ease}
#mainNavbar.scrolled .container{min-height:60px}
.navbar-brand img{height:46px;max-height:50px;width:auto;object-fit:contain;transition:height .25s ease}
#mainNavbar.scrolled .navbar-brand img{height:40px}

/* Smart Primary Nav Links */
#mainNavbar .navbar-nav{gap:2px}
#mainNavbar .nav-link{font-family:'Poppins',sans-serif;font-size:13.5px;font-weight:600;color:#1e293b !important;padding:8px 13px !important;border-radius:8px;display:inline-flex;align-items:center;gap:6px;position:relative;transition:all .18s ease;white-space:nowrap}
#mainNavbar .nav-link:hover,#mainNavbar .nav-link.active{color:#1C2280 !important;background:rgba(28,34,128,.05)}
.nav-chevron{font-size:9.5px;opacity:.6;transition:transform .2s ease;margin-left:2px}

/* Desktop Hover Trigger for Dropdowns */
@media(min-width:992px){
  #mainNavbar .nav-item.dropdown:hover > .dropdown-menu{display:block;opacity:1;visibility:visible;transform:translateY(0);pointer-events:auto}
  #mainNavbar .nav-item.dropdown > .dropdown-menu{display:block;opacity:0;visibility:hidden;transform:translateY(8px);pointer-events:none;transition:opacity .18s ease,transform .18s ease,visibility .18s}
  #mainNavbar .nav-item.dropdown:hover > .nav-link .nav-chevron{transform:rotate(180deg)}
}

/* Smart Hiring Pulse Badge */
.smart-hiring-pill{display:inline-flex;align-items:center;gap:5px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;font-size:10px;font-weight:700;padding:2px 8px;border-radius:100px;text-transform:uppercase;letter-spacing:.4px;margin-left:4px}
.smart-hiring-pill .pulse-dot{width:6px;height:6px;background:#dc2626;border-radius:50%;box-shadow:0 0 0 0 rgba(220,38,38,.7);animation:smartPulse 1.8s infinite}
@keyframes smartPulse{0%{transform:scale(.95);box-shadow:0 0 0 0 rgba(220,38,38,.7)}70%{transform:scale(1);box-shadow:0 0 0 6px rgba(220,38,38,0)}100%{transform:scale(.95);box-shadow:0 0 0 0 rgba(220,38,38,0)}}

/* Dropdowns Base */
#mainNavbar .dropdown-menu{border:1px solid #E5E7EB;border-radius:14px;box-shadow:0 22px 50px rgba(15,23,42,.12);padding:16px;margin-top:10px;background:#fff}
#mainNavbar .dropdown-header{font-family:'Poppins',sans-serif;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#1C2280;padding:6px 10px 6px;border-bottom:1.5px solid #F1F5F9;margin-bottom:6px}
#mainNavbar .dropdown-item{font-size:12.5px;font-weight:600;color:#1e293b;padding:7px 10px;border-radius:7px;transition:all .15s ease;display:flex;align-items:center;gap:9px}
#mainNavbar .dropdown-item:hover{background:rgba(28,34,128,.05);color:#1C2280;padding-left:14px}
.sub-item-desc{font-size:11px;font-weight:400;color:#64748b;line-height:1.3;margin-top:1px}

/* Mega Menu Grid for Services */
.mega-menu-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:0 10px}
@media(min-width:1200px){.smart-services-menu{min-width:1080px;left:50% !important;transform:translateX(-50%) !important}}
@media(min-width:992px) and (max-width:1199.98px){.smart-services-menu{min-width:900px;left:0 !important}.mega-menu-grid{grid-template-columns:repeat(3,1fr);gap:10px}}

/* Solutions 2-Column Dropdown */
.smart-solutions-menu{min-width:620px;padding:18px !important}
.solutions-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}

/* Company Dropdown */
.smart-company-menu{min-width:320px;padding:12px !important}

/* Smart Action Cluster (Right) */
.smart-nav-actions{display:flex;align-items:center;gap:10px}
.smart-call-btn{background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:7px 11px;color:#334155;font-size:12.5px;font-weight:600;display:inline-flex;align-items:center;gap:6px;text-decoration:none !important;transition:all .2s ease}
.smart-call-btn:hover{background:#fff;color:#1C2280;border-color:#cbd5e1;box-shadow:0 3px 8px rgba(0,0,0,.05)}
.smart-call-btn i{color:#CC2228;font-size:11px}
.smart-quote-cta{background:linear-gradient(135deg,#1C2280 0%,#151a66 100%) !important;color:#fff !important;font-family:'Poppins',sans-serif;font-size:13px !important;font-weight:700 !important;padding:8px 18px !important;border-radius:8px !important;display:inline-flex;align-items:center;gap:7px;text-decoration:none !important;letter-spacing:.2px;box-shadow:0 4px 12px rgba(28,34,128,.22);transition:all .2s ease;white-space:nowrap}
.smart-quote-cta:hover{background:linear-gradient(135deg,#252b8c 0%,#1a2068 100%) !important;color:#fff !important;transform:translateY(-2px);box-shadow:0 6px 18px rgba(28,34,128,.32)}
.smart-quote-cta i{font-size:11.5px;transition:transform .2s ease}
.smart-quote-cta:hover i{transform:translateX(3px)}

/* Mobile Responsive */
@media(max-width:991.98px){
  #mainNavbar{width:calc(100% - 20px) !important;top:8px !important;border-radius:14px !important}
  #mainNavbar .container{min-height:58px !important;padding-left:14px !important;padding-right:14px !important}
  .navbar-brand img{height:38px !important;max-height:40px !important}
  #mainNavbar .navbar-collapse{max-height:calc(85vh - 65px) !important;overflow-y:auto !important;-webkit-overflow-scrolling:touch !important;padding:12px 6px 18px !important;border-top:1px solid rgba(28,34,128,.08) !important;margin-top:8px !important}
  #mainNavbar .dropdown-menu{min-width:100% !important;max-width:100% !important;box-shadow:none !important;border:1px solid rgba(28,34,128,.08) !important;padding:12px !important;border-radius:12px !important;background:#f8fafc !important}
  .mega-menu-grid,.solutions-grid{grid-template-columns:1fr !important;gap:12px !important}
  #mainNavbar .nav-link{padding:9px 12px !important;font-size:14.5px !important}
}
@media(max-width:575.98px){
  #mainNavbar{width:calc(100% - 14px) !important;top:6px !important}
  .navbar-brand img{height:34px !important;max-height:36px !important}
}
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

<!-- ── SMART MAIN NAVBAR ────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg" id="mainNavbar">
  <div class="container">
    <a class="navbar-brand" href="<?= $prefix ?>index.php" aria-label="Vortexsoft Innovations Home">
      <picture>
        <source srcset="<?= $prefix ?>logo-header.webp?v=20261008" type="image/webp">
        <img src="<?= $prefix ?>logo-header.png?v=20261008" alt="Vortexsoft Innovations — Global IT &amp; BPO Company" width="220" height="64" fetchpriority="high">
      </picture>
    </a>

    <!-- Mobile Hamburger Toggler -->
    <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
            aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" style="width:42px;height:42px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;border:none !important;background:rgba(28,34,128,0.06);">
      <i class="fas fa-bars" style="color:#1C2280;font-size:20px;"></i>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <!-- Primary Clean Navigation Links -->
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <!-- 1. Home -->
        <li class="nav-item">
          <a class="nav-link <?= nav_active('home', $current_path) ?>" href="<?= $prefix ?>index.php">Home</a>
        </li>

        <!-- 2. Services (Interactive Mega Menu) -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= nav_active('service', $current_path) ?>" href="<?= $prefix ?>service.php"
             id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Services <i class="fas fa-chevron-down nav-chevron"></i>
          </a>
          <ul class="dropdown-menu smart-services-menu p-3" aria-labelledby="servicesDropdown">
            <div class="mega-menu-grid">
              <!-- Division 1: Technology & AI -->
              <div>
                <h6 class="dropdown-header"><i class="fas fa-microchip me-1"></i> Technology &amp; AI</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>software-solutions/index.php"><i class="fas fa-laptop-code text-primary"></i> Software Development</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>service.php#ai-automation"><i class="fas fa-robot text-primary"></i> AI &amp; Machine Learning</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>data-annotation-services/index.php"><i class="fas fa-tags text-primary"></i> Data Annotation</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>data-extraction-service/index.php"><i class="fas fa-database text-primary"></i> Data Extraction</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>development-services/index.php"><i class="fas fa-cloud text-primary"></i> Cloud &amp; Consulting</a></li>
              </div>

              <!-- Division 2: Healthcare & RCM -->
              <div>
                <h6 class="dropdown-header"><i class="fas fa-heartbeat me-1"></i> Healthcare &amp; RCM</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>medical-coding-services/index.php"><i class="fas fa-file-medical text-danger"></i> Medical Coding</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>medical-billing-services/index.php"><i class="fas fa-file-invoice-dollar text-danger"></i> Medical Billing</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>health-care-services/index.php"><i class="fas fa-sync-alt text-danger"></i> Revenue Cycle Mgmt</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>ar-recovery-services/index.php"><i class="fas fa-hand-holding-usd text-danger"></i> AR Recovery</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>denial-management-services/index.php"><i class="fas fa-shield-alt text-danger"></i> Denial Management</a></li>
              </div>

              <!-- Division 3: Publishing & Prepress -->
              <div>
                <h6 class="dropdown-header"><i class="fas fa-book-open me-1"></i> Publishing &amp; Prepress</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>publishing-services/index.php"><i class="fas fa-book text-info"></i> Digital Publishing</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>ebook-conversion-services/index.php"><i class="fas fa-tablet-alt text-info"></i> eBook Conversion</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>pdf-accessibility-services/index.php"><i class="fas fa-universal-access text-info"></i> PDF Accessibility</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>s1000d-xml-conversion-services/index.php"><i class="fas fa-code text-info"></i> XML Conversion</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>technical-writing/index.php"><i class="fas fa-pen-nib text-info"></i> Technical Writing</a></li>
              </div>

              <!-- Division 4: Real Estate & Title -->
              <div>
                <h6 class="dropdown-header"><i class="fas fa-landmark me-1"></i> Real Estate &amp; Title</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>title-settlement/index.php"><i class="fas fa-search-location text-warning"></i> Title Search</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>title-settlement/index.php"><i class="fas fa-home text-warning"></i> Title &amp; Settlement</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>mortgage-escrow/index.php"><i class="fas fa-file-signature text-warning"></i> Mortgage Support</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>lease-abstraction-services/index.php"><i class="fas fa-scroll text-warning"></i> Lease Abstraction</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>property-accounting-services/index.php"><i class="fas fa-calculator text-warning"></i> Property Accounting</a></li>
              </div>

              <!-- Division 5: Business Operations -->
              <div>
                <h6 class="dropdown-header"><i class="fas fa-briefcase me-1"></i> Business Operations</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>accounting-services/index.php"><i class="fas fa-balance-scale text-success"></i> Accounting</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>payroll-processing-services/index.php"><i class="fas fa-money-check-alt text-success"></i> Payroll Processing</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>manpower-payroll-services/index.php"><i class="fas fa-users-cog text-success"></i> Manpower Supply</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>background-verification-service/index.php"><i class="fas fa-user-check text-success"></i> Staffing &amp; Screening</a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>service.php"><i class="fas fa-headset text-success"></i> BPO Operations</a></li>
              </div>

              <!-- Mega Menu Footer Action Strip -->
              <div class="pt-2 mt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2" style="grid-column: 1 / -1; font-size:12.5px; background:#F8FAFC; padding:10px 16px; border-radius:8px;">
                <span><i class="fas fa-layer-group text-primary me-1"></i> Global Enterprise Portfolio: <strong>75+ Scalable Solutions</strong> across India &amp; Worldwide</span>
                <a href="<?= $prefix ?>service.php" class="btn btn-sm text-white px-3 fw-semibold" style="background:#1C2280; font-size:11.5px; border-radius:6px;">Explore All 75+ Services <i class="fas fa-arrow-right ms-1"></i></a>
              </div>
            </div>
          </ul>
        </li>

        <!-- 3. Solutions (Products + Industries Smart Dropdown) -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="solutionsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Solutions <i class="fas fa-chevron-down nav-chevron"></i>
          </a>
          <ul class="dropdown-menu smart-solutions-menu p-3" aria-labelledby="solutionsDropdown">
            <div class="solutions-grid">
              <!-- Left: Enterprise AI Platforms -->
              <div>
                <h6 class="dropdown-header"><i class="fas fa-cubes me-1"></i> Proprietary AI Platforms</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>index.php#ai-products"><i class="fas fa-id-card-alt text-primary"></i> <div><strong>VortexEXHO</strong><div class="sub-item-desc">Enterprise Workforce OS &amp; Copilot</div></div></a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>index.php#ai-products"><i class="fas fa-user-astronaut text-primary"></i> <div><strong>vortexHire</strong><div class="sub-item-desc">Autonomous AI Candidate Screening</div></div></a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>index.php#ai-products"><i class="fas fa-headset text-primary"></i> <div><strong>vortexKonnect</strong><div class="sub-item-desc">Call Center Speech &amp; Sentiment AI</div></div></a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>index.php#ai-products"><i class="fas fa-bullhorn text-primary"></i> <div><strong>Vortexreach</strong><div class="sub-item-desc">Autonomous B2B Outreach Engine</div></div></a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>publishing-services/index.php"><i class="fas fa-book-reader text-primary"></i> <div><strong>vortexsoftpublishing</strong><div class="sub-item-desc">Automated STM &amp; XML Typesetting</div></div></a></li>
              </div>

              <!-- Right: Key Industry Verticals -->
              <div>
                <h6 class="dropdown-header"><i class="fas fa-industry me-1"></i> Key Industries Served</h6>
                <li><a class="dropdown-item" href="<?= $prefix ?>health-care-services/index.php"><i class="fas fa-hospital text-danger"></i> <div><strong>Healthcare &amp; Hospital Systems</strong><div class="sub-item-desc">Hospitals, Clinics, RCM &amp; Payers</div></div></a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>title-settlement/index.php"><i class="fas fa-building text-warning"></i> <div><strong>Real Estate &amp; Title Settlement</strong><div class="sub-item-desc">Underwriters, Escrow &amp; Mortgages</div></div></a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>publishing-services/index.php"><i class="fas fa-newspaper text-info"></i> <div><strong>Publishing &amp; Media</strong><div class="sub-item-desc">STM Journals, eBooks &amp; Prepress</div></div></a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>accounting-services/index.php"><i class="fas fa-chart-line text-success"></i> <div><strong>BFSI &amp; Corporate Accounting</strong><div class="sub-item-desc">Statutory Tax, Audit &amp; Payroll</div></div></a></li>
                <li><a class="dropdown-item" href="<?= $prefix ?>software-solutions/index.php"><i class="fas fa-code text-primary"></i> <div><strong>Tech &amp; High-Growth Startups</strong><div class="sub-item-desc">Offshore Pods &amp; Dedicated Devs</div></div></a></li>
              </div>

              <!-- Bottom Action -->
              <div class="pt-2 mt-2 border-top d-flex align-items-center justify-content-between" style="grid-column: 1 / -1; font-size:12px; background:#F8FAFC; padding:8px 14px; border-radius:6px;">
                <span><i class="fas fa-award text-warning me-1"></i> Proven Track Record across 150+ Global Enterprise Engagements</span>
                <a href="<?= $prefix ?>index.php#case-studies" class="fw-bold text-primary" style="font-size:11.5px; text-decoration:none;">View Case Studies <i class="fas fa-arrow-right ms-1"></i></a>
              </div>
            </div>
          </ul>
        </li>

        <!-- 4. Company (About, Compliance, Hubs) -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= str_contains($current_path, 'about') ? 'active' : '' ?>" href="#"
             id="companyDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Company <i class="fas fa-chevron-down nav-chevron"></i>
          </a>
          <ul class="dropdown-menu smart-company-menu p-2" aria-labelledby="companyDropdown">
            <li><a class="dropdown-item" href="<?= $prefix ?>about.php"><i class="fas fa-info-circle text-primary"></i> <div><strong>About Vortexsoft</strong><div class="sub-item-desc">Mission, Leadership &amp; Global Story</div></div></a></li>
            <li><a class="dropdown-item" href="<?= $prefix ?>about.php#certifications"><i class="fas fa-shield-alt text-success"></i> <div><strong>Quality &amp; Compliance</strong><div class="sub-item-desc">ISO 27001:2013 &amp; HIPAA Certified</div></div></a></li>
            <li><a class="dropdown-item" href="<?= $prefix ?>about.php#locations"><i class="fas fa-map-marked-alt text-danger"></i> <div><strong>Global Delivery Hubs</strong><div class="sub-item-desc">Pune HQ, BLR, HYD, MUM &amp; USA</div></div></a></li>
          </ul>
        </li>

        <!-- 5. Careers (Standout with Vibrant Live Hiring Pill) -->
        <li class="nav-item">
          <a class="nav-link <?= nav_active('careers', $current_path) ?>" href="<?= $prefix ?>careers.php">
            Careers <span class="smart-hiring-pill"><span class="pulse-dot"></span> Hiring 30+</span>
          </a>
        </li>
      </ul>

      <!-- Smart Right-Side Actions -->
      <div class="smart-nav-actions d-none d-lg-flex">
        <!-- Quick Call Pill -->
        <a href="tel:<?= str_replace(['-',' '], '', PHONE_INDIA) ?>" class="smart-call-btn" title="Call Sales &amp; Inquiries: <?= PHONE_INDIA ?>" aria-label="Call <?= PHONE_INDIA ?>">
          <i class="fas fa-phone-alt"></i>
          <span class="d-none d-xxl-inline"><?= PHONE_INDIA ?></span>
        </a>

        <!-- Primary CTA: Get Free Quote -->
        <a href="<?= $prefix ?>contact.php" class="smart-quote-cta magnetic" aria-label="Get Free Project Quote">
          <span>Get Free Quote</span>
          <i class="fas fa-arrow-right"></i>
        </a>
      </div>

      <!-- Mobile Menu Actions inside Collapse -->
      <div class="d-lg-none mt-3 pt-3 border-top">
        <div class="d-flex flex-column gap-2">
          <a href="<?= $prefix ?>contact.php" class="btn btn-primary w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2" style="background:#1C2280; border-color:#1C2280; border-radius:10px;">
            <i class="fas fa-paper-plane"></i> Get Free Quote
          </a>
          <a href="tel:<?= str_replace(['-',' '], '', PHONE_INDIA) ?>" class="btn btn-outline-secondary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" style="border-radius:10px;">
            <i class="fas fa-phone-alt text-danger"></i> Call: <?= PHONE_INDIA ?>
          </a>
          <a href="https://wa.me/918308906690?text=Hello%20Vortexsoft%20Innovations%20Team" target="_blank" class="btn btn-outline-success w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" style="border-radius:10px;">
            <i class="fab fa-whatsapp"></i> WhatsApp Support
          </a>
        </div>
      </div>
    </div>
  </div>
</nav>
</div><!-- /#site-header -->
