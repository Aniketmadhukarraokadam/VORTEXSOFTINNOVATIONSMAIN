<?php
/**
 * Vortexsoft Innovations — PHP Header Partial
 * Replaces assets/partials/header.js + header.html
 * Usage: require_once ROOT_PATH . '/includes/header.php';
 * Pass $page_title, $page_desc, $canonical_url, $prefix (default './')
 */

if (!function_exists('vortex_minify_html')) {
    function vortex_minify_html(string $html): string {
        $placeholders = [];
        $patterns = [
            '/<pre\b[^>]*>.*?<\/pre>/is',
            '/<textarea\b[^>]*>.*?<\/textarea>/is',
            '/<code\b[^>]*>.*?<\/code>/is',
            '/<script\b[^>]*>.*?<\/script>/is',
            '/<style\b[^>]*>.*?<\/style>/is',
        ];
        foreach ($patterns as $pattern) {
            $html = preg_replace_callback($pattern, function($matches) use (&$placeholders) {
                $key = '###VORTEX_PRESERVE_' . count($placeholders) . '###';
                $placeholders[$key] = $matches[0];
                return $key;
            }, $html);
        }
        $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);
        $html = preg_replace('/>\s+</', '> <', $html);
        $html = preg_replace('/\s{2,}/', ' ', $html);
        if (!empty($placeholders)) {
            $html = strtr($html, $placeholders);
        }
        return trim($html);
    }
}

if (!ob_get_level() && !headers_sent()) {
    @ob_start('vortex_minify_html');
}

if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config/constants.php';
}
require_once __DIR__ . '/functions.php';

$prefix       = $prefix ?? './';
$page_title   = $page_title ?? 'Top Global IT & BPO Outsourcing Company in India | AI Services | Vortexsoft Innovations Private Limited';
$page_desc    = $page_desc ?? 'Vortexsoft Innovations Private Limited — ISO 27001 certified global IT & BPO outsourcing company in Pune & Bengaluru, India. Expert in AI Solutions, Healthcare BPO, Publishing, Real Estate, Data Annotation & 75+ services for 150+ clients worldwide.';
$page_keywords = $page_keywords ?? 'Vortexsoft, Vortexsoft Innovations, Vortexsoft Innovations Private Limited, Vortexsoft Innovations Pvt Ltd, Vortex Soft, Vortex Innovations, Vortex, Vertex, Vortex Group, IT outsourcing company India, BPO company Pune, BPO company Bengaluru, AI solutions company India, Healthcare BPO India, Medical Coding outsourcing, ICD-10 CPT medical billing services, Revenue Cycle Management RCM outsourcing, denial management AR recovery, AI Data Annotation India, machine learning training datasets, computer vision bounding box annotation, LiDAR 3D point cloud labeling, RLHF training data outsourcing, Enterprise Workforce OS, VortexEXHO, vortexHire, vortexKonnect, Vortexreach, vortexsoftpublishing, vortexsofthrms, S1000D XML conversion, prepress typesetting outsourcing, commercial lease abstraction services, CAM audit property accounting, mortgage title search settlement typing, offshore development center India, ISO 27001 BPO company, HIPAA compliant healthcare outsourcing, AI HRMS payroll automation, hire dedicated developers Pune Bengaluru';
// Detect active page & path and enforce single global canonical domain: https://www.vortexsoftinnovations.com
$current_path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
if ($current_path === '/index.php') {
    $current_path = '/';
}
$com_url = PRIMARY_COM_URL . $current_path;
if (empty($canonical_url) || str_contains($canonical_url, 'vortexsoftinnovations.in')) {
    $canonical_url = $com_url;
} else {
    $canonical_url = preg_replace('/^https?:\/\/(www\.)?vortexsoftinnovations\.(in|com)/i', PRIMARY_COM_URL, $canonical_url);
}
$og_image = $og_image ?? (PRIMARY_COM_URL . '/assets/images/vortexsoft-share-banner.png');

function nav_active(string $page, string $path): string {
    if ($page === 'home' && ($path === '/' || $path === '/index.php' || $path === '')) return 'active';
    return str_contains($path, $page) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
<!-- Critical Preloads for Fastest Core Web Vitals (0ms Font Shift & High LCP) -->
    <link rel="preload" href="<?= $prefix ?>logo-header.png?v=20260912" as="image" type="image/png" fetchpriority="high">
    <link rel="preload" href="<?= $prefix ?>assets/fonts/inter/inter-400.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= $prefix ?>assets/fonts/inter/inter-600.woff2" as="font" type="font/woff2" crossorigin>

    <!-- DNS-Prefetch & Preconnect for High-Speed Connection -->
    <link rel="dns-prefetch" href="//www.googletagmanager.com">
    <link rel="dns-prefetch" href="//www.google-analytics.com">
    <link rel="dns-prefetch" href="//connect.facebook.net">
    <link rel="dns-prefetch" href="//api.indexnow.org">
    <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
    <link rel="preconnect" href="https://www.google-analytics.com" crossorigin>

    <!-- IndexNow Instant Search Engine Indexing (Bing, Yandex, Naver, Seznam) -->
    <meta name="indexnow" content="e636a2d7f4ea4f0da00b23d5abc4235" />

    <!-- Brand Styling & Webmaster Directives -->
    <meta name="theme-color" content="#080B1A" />
    <meta name="msapplication-TileColor" content="#1C2280" />
    <meta name="msapplication-navbutton-color" content="#080B1A" />
    <meta name="application-name" content="Vortexsoft Innovations" />
    <meta name="apple-mobile-web-app-title" content="Vortexsoft" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

    <!-- Extended AI & Social Bot Crawlers -->
    <meta name="Amazonbot" content="index, follow" />
    <meta name="Timpibot" content="index, follow" />
    <meta name="facebookexternalhit" content="index, follow" />
    <meta name="linkedinbot" content="index, follow" />
    <meta name="twitterbot" content="index, follow" />
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
        .topbar{background:#070A1A;padding:8px 0;font-size:12px;font-weight:500;color:#94a3b8;border-bottom:1px solid rgba(255,255,255,.07)}
        .navbar{background:rgba(255,255,255,.94);backdrop-filter:blur(20px);box-shadow:0 1px 0 rgba(15,23,42,.06);padding:0;position:sticky;top:0;z-index:1040}
        .navbar-brand img{height:44px;object-fit:contain}
        .page-hero{background:linear-gradient(135deg,#080B1A 0%,#1C2280 55%,#0D1035 100%);padding:80px 0 70px;position:relative;overflow:hidden}
        #site-header{min-height:108px}
        @media(max-width:991px){#site-header{min-height:60px}}
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

    <!-- Canonical & Alternate Multi-Region URLs (Consolidated to Global .com) -->
    <link rel="canonical" href="<?= htmlspecialchars($canonical_url) ?>">
    <link rel="alternate" hreflang="en-IN" href="<?= htmlspecialchars($com_url) ?>">
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
          "@type": "ItemList",
          "@id": "<?= PRIMARY_COM_URL ?>/#sitelinks-navigation",
          "name": "Vortexsoft Innovations Core Navigation & Sitelinks",
          "description": "Primary corporate links and service divisions for Google Sitelinks and search navigation",
          "itemListElement": [
            {
              "@type": "SiteNavigationElement",
              "position": 1,
              "name": "Careers & Job Openings",
              "description": "Explore 30+ IT, AI, Mortgage Title & Healthcare Openings in Pune, Bengaluru & Remote",
              "url": "<?= PRIMARY_COM_URL ?>/careers.php"
            },
            {
              "@type": "SiteNavigationElement",
              "position": 2,
              "name": "Enterprise Services & Solutions",
              "description": "65+ Specialized IT, BPM, AI & Healthcare Outsourcing Services",
              "url": "<?= PRIMARY_COM_URL ?>/service.php"
            },
            {
              "@type": "SiteNavigationElement",
              "position": 3,
              "name": "About Vortexsoft",
              "description": "CMMI Level 3 Appraised & ISO 27001 Certified Corporate Overview",
              "url": "<?= PRIMARY_COM_URL ?>/about.php"
            },
            {
              "@type": "SiteNavigationElement",
              "position": 4,
              "name": "Contact & Global Offices",
              "description": "Connect with Pune Corporate HQ & Bengaluru Technology Delivery Center",
              "url": "<?= PRIMARY_COM_URL ?>/contact.php"
            },
            {
              "@type": "SiteNavigationElement",
              "position": 5,
              "name": "US Title Search & Mortgage BPM",
              "description": "Offshore 40-Year Title Searches, Examination & Commitments across 50 US States",
              "url": "<?= PRIMARY_COM_URL ?>/title-settlement/"
            },
            {
              "@type": "SiteNavigationElement",
              "position": 6,
              "name": "Healthcare BPO & HIPAA RCM",
              "description": "HIPAA-Compliant Medical Billing, Coding & Denial Management",
              "url": "<?= PRIMARY_COM_URL ?>/health-care-services/"
            },
            {
              "@type": "SiteNavigationElement",
              "position": 7,
              "name": "AI & Custom Software Solutions",
              "description": "Enterprise AI Agents, Cloud Microservices & Custom Engineering",
              "url": "<?= PRIMARY_COM_URL ?>/software-solutions/"
            },
            {
              "@type": "SiteNavigationElement",
              "position": 8,
              "name": "AI Data Annotation Services",
              "description": "LiDAR, Computer Vision & LLM Training Datasets Annotation",
              "url": "<?= PRIMARY_COM_URL ?>/data-annotation-services/"
            },
            {
              "@type": "SiteNavigationElement",
              "position": 9,
              "name": "Frequently Asked Questions (FAQ)",
              "description": "Answers on Outsourcing Models, SLAs, Data Security & Governance",
              "url": "<?= PRIMARY_COM_URL ?>/faq.php"
            },
            {
              "@type": "SiteNavigationElement",
              "position": 10,
              "name": "Insights & Industry Articles",
              "description": "Thought Leadership, Industry Research & Technical Publications",
              "url": "<?= PRIMARY_COM_URL ?>/blog.php"
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
          "slogan": "Engineering Intelligent Enterprises. Scaling Global Operations.",
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
            "https://www.instagram.com/vortexsoft_innovations",
            "https://github.com/Aniketmadhukarraokadam/VORTEXSOFTINNOVATIONSMAIN",
            "https://www.zaubacorp.com/company/VORTEXSOFT-INNOVATIONS-PRIVATE-LIMITED/U72900PN2020PTC195000",
            "https://www.tofler.in/vortexsoft-innovations-private-limited/company/U72900PN2020PTC195000"
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
          "currenciesAccepted": "USD, EUR, GBP, INR",
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
          "currenciesAccepted": "USD, EUR, GBP, INR",
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
          "currenciesAccepted": "USD, EUR, GBP, INR",
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
<!-- ── Dynamic SEO, Verification & Analytics Injection ── -->
    <?php
    $sys_ga4_id      = function_exists('get_system_setting') ? get_system_setting('ga4_measurement_id') : '';
    $sys_gtm_id      = function_exists('get_system_setting') ? get_system_setting('gtm_container_id') : '';
    $sys_g_verify    = function_exists('get_system_setting') ? get_system_setting('google_site_verification') : '';
    $sys_bing_verify = function_exists('get_system_setting') ? get_system_setting('bing_site_verification') : '';
    $sys_meta_pixel  = function_exists('get_system_setting') ? get_system_setting('meta_pixel_id') : '';
    $sys_custom_head = function_exists('get_system_setting') ? get_system_setting('custom_head_scripts') : '';
    ?>
    <?php if (!empty($sys_g_verify)): ?>
    <meta name="google-site-verification" content="<?= htmlspecialchars($sys_g_verify) ?>" />
    <?php endif; ?>
    <?php if (!empty($sys_bing_verify)): ?>
    <meta name="msvalidate.01" content="<?= htmlspecialchars($sys_bing_verify) ?>" />
    <?php endif; ?>
    <?php if (!empty($sys_ga4_id)): ?>
    <!-- Google Analytics 4 (GA4) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($sys_ga4_id) ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?= htmlspecialchars($sys_ga4_id) ?>', { 'send_page_view': true });
    </script>
    <?php endif; ?>
    <?php if (!empty($sys_gtm_id)): ?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?= htmlspecialchars($sys_gtm_id) ?>');</script>
    <?php endif; ?>
    <?php if (!empty($sys_meta_pixel)): ?>
    <!-- Meta Pixel Code -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '<?= htmlspecialchars($sys_meta_pixel) ?>');
      fbq('track', 'PageView');
    </script>
    <?php endif; ?>
    <?php if (!empty($sys_custom_head)): ?>
    <?= $sys_custom_head ?>
    <?php endif; ?>
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

<!-- ── TOPBAR (EXECUTIVE UTILITY BAR) ────────────────────────────────────── -->
<style>
/* ═══════ EXECUTIVE UTILITY TOPBAR ═══════ */
.topbar {
  background: #060918;
  padding: 8px 0;
  font-size: 12px;
  font-weight: 500;
  color: #94a3b8;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  letter-spacing: 0.15px;
}
.topbar a {
  color: #cbd5e1;
  text-decoration: none !important;
  transition: color 0.18s ease;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.topbar a:hover {
  color: #ffffff;
}
.topbar i {
  font-size: 10.5px;
  color: #38bdf8;
}
.topbar-trust-tag {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #f1f5f9;
  font-size: 11px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 100px;
  letter-spacing: 0.3px;
}
.topbar-trust-tag i {
  color: #10b981;
}
.topbar-divider {
  width: 1px;
  height: 12px;
  background: rgba(255, 255, 255, 0.15);
  display: inline-block;
  vertical-align: middle;
}
.topbar-socials a {
  color: #cbd5e1 !important;
  width: 25px;
  height: 25px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  transition: all 0.2s ease;
}
.topbar-socials a:hover {
  background: #1C2280;
  color: #ffffff !important;
  border-color: #1C2280;
  transform: translateY(-1px);
}
.topbar-socials a i {
  color: #ffffff !important;
}

/* ═══════ PREMIUM ENTERPRISE NAVBAR ═══════ */
#mainNavbar {
  background: rgba(255, 255, 255, 0.94);
  backdrop-filter: blur(20px) saturate(180%);
  -webkit-backdrop-filter: blur(20px) saturate(180%);
  border-bottom: 1px solid rgba(226, 232, 240, 0.9);
  position: sticky;
  top: 0;
  z-index: 1040;
  width: 100%;
  margin: 0;
  padding: 0;
  box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.03);
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
#mainNavbar.scrolled {
  background: rgba(255, 255, 255, 0.98);
  border-bottom-color: rgba(28, 34, 128, 0.12);
  box-shadow: 0 10px 30px -4px rgba(15, 23, 42, 0.07);
}
#mainNavbar .container,
#mainNavbar .container-xl {
  min-height: 72px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: min-height 0.25s ease;
}
#mainNavbar.scrolled .container,
#mainNavbar.scrolled .container-xl {
  min-height: 64px;
}
.navbar-brand {
  padding: 0;
  display: inline-flex;
  align-items: center;
}
.navbar-brand img {
  height: 44px;
  max-height: 46px;
  width: auto;
  object-fit: contain;
  transition: height 0.25s ease;
}
#mainNavbar.scrolled .navbar-brand img {
  height: 38px;
}

/* Primary Nav Links */
#mainNavbar .navbar-nav {
  gap: 4px;
  align-items: center;
}
#mainNavbar .nav-link {
  font-family: 'Poppins', sans-serif;
  font-size: 13.5px;
  font-weight: 500;
  color: #1e293b !important;
  padding: 8px 14px !important;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.18s ease;
  white-space: nowrap;
  letter-spacing: -0.1px;
}
#mainNavbar .nav-link:hover,
#mainNavbar .nav-link.active {
  color: #1C2280 !important;
  background: rgba(28, 34, 128, 0.05);
  font-weight: 600;
}
.nav-chevron {
  font-size: 9px;
  opacity: 0.55;
  transition: transform 0.2s ease;
  margin-left: 2px;
}

/* Desktop Hover Trigger */
@media (min-width: 992px) {
  #mainNavbar .nav-item.dropdown:hover > .dropdown-menu {
    display: block;
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    pointer-events: auto;
  }
  #mainNavbar .nav-item.dropdown > .dropdown-menu {
    display: block;
    opacity: 0;
    visibility: hidden;
    transform: translateY(8px);
    pointer-events: none;
    transition: opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1), transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.2s;
  }
  #mainNavbar .nav-item.dropdown:hover > .nav-link .nav-chevron {
    transform: rotate(180deg);
  }
}

/* Hiring Badge */
.nav-hiring-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 100px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-left: 4px;
}
.nav-hiring-badge .pulse-dot {
  width: 5px;
  height: 5px;
  background: #2563eb;
  border-radius: 50%;
  box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7);
  animation: navPulse 1.8s infinite;
}
@keyframes navPulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(37, 99, 235, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
}

/* Dropdown Shell */
#mainNavbar .dropdown-menu {
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.12), 0 8px 16px -6px rgba(15, 23, 42, 0.06);
  padding: 20px;
  margin-top: 6px;
  background: #ffffff;
}

/* ═══════ SERVICES MEGA MENU ═══════ */
.mega-menu-wrapper {
  display: grid;
  grid-template-columns: repeat(4, 1fr) 280px;
  gap: 20px;
}
@media (min-width: 1200px) {
  .smart-services-menu {
    min-width: 1160px;
    left: 50% !important;
    transform: translateX(-50%) !important;
  }
}
@media (min-width: 992px) and (max-width: 1199.98px) {
  .smart-services-menu {
    min-width: 950px;
    left: 0 !important;
  }
  .mega-menu-wrapper {
    grid-template-columns: repeat(2, 1fr) repeat(2, 1fr);
  }
  .mega-spotlight-card {
    display: none !important;
  }
}
.mega-col-header {
  font-family: 'Poppins', sans-serif;
  font-size: 11.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: #1C2280;
  padding: 4px 8px 8px;
  border-bottom: 1.5px solid #f1f5f9;
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  gap: 6px;
}
.mega-col-header i {
  color: #CC2228;
  font-size: 11px;
}
.mega-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 8px 8px;
  border-radius: 8px;
  color: #1e293b;
  text-decoration: none !important;
  transition: all 0.18s ease;
}
.mega-item:hover {
  background: #f8fafc;
  transform: translateX(3px);
}
.mega-item-icon {
  width: 28px;
  height: 28px;
  border-radius: 7px;
  background: rgba(28, 34, 128, 0.06);
  color: #1C2280;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11.5px;
  flex-shrink: 0;
  transition: all 0.18s ease;
  margin-top: 1px;
}
.mega-item:hover .mega-item-icon {
  background: #1C2280;
  color: #ffffff;
}
.mega-item-title {
  font-size: 13px;
  font-weight: 600;
  color: #1e293b;
  line-height: 1.3;
}
.mega-item:hover .mega-item-title {
  color: #1C2280;
}
.mega-item-desc {
  font-size: 11px;
  color: #64748b;
  line-height: 1.3;
  margin-top: 2px;
}

/* Spotlight Card */
.mega-spotlight-card {
  background: linear-gradient(145deg, #0f172a 0%, #1e1b4b 100%);
  border-radius: 12px;
  padding: 20px 18px;
  color: #fff;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  border: 1px solid rgba(255, 255, 255, 0.08);
}
.mega-spotlight-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: rgba(255, 255, 255, 0.1);
  padding: 3px 8px;
  border-radius: 100px;
  font-size: 10px;
  font-weight: 700;
  color: #38bdf8;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  margin-bottom: 12px;
  align-self: flex-start;
}
.mega-spotlight-title {
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 700;
  color: #fff;
  line-height: 1.4;
  margin-bottom: 8px;
}
.mega-spotlight-text {
  font-size: 11.5px;
  color: #94a3b8;
  line-height: 1.5;
  margin-bottom: 16px;
}
.mega-spotlight-btn {
  background: #ffffff;
  color: #0f172a !important;
  font-family: 'Poppins', sans-serif;
  font-size: 12px;
  font-weight: 700;
  padding: 8px 14px;
  border-radius: 7px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  text-decoration: none !important;
  transition: all 0.2s ease;
}
.mega-spotlight-btn:hover {
  background: #f1f5f9;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Solutions 2-Column Menu */
.smart-solutions-menu {
  min-width: 640px;
  padding: 20px !important;
}
.solutions-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.solutions-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 9px 10px;
  border-radius: 8px;
  text-decoration: none !important;
  transition: all 0.18s ease;
}
.solutions-item:hover {
  background: #f8fafc;
  transform: translateX(3px);
}
.solutions-item-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: rgba(28, 34, 128, 0.06);
  color: #1C2280;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  flex-shrink: 0;
  transition: all 0.18s ease;
  margin-top: 2px;
}
.solutions-item:hover .solutions-item-icon {
  background: #1C2280;
  color: #ffffff;
}

/* Company Menu */
.smart-company-menu {
  min-width: 340px;
  padding: 14px !important;
}

/* Right-Side Actions Cluster */
.nav-actions-cluster {
  display: flex;
  align-items: center;
  gap: 12px;
}
.nav-phone-link {
  color: #334155;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 12.5px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  text-decoration: none !important;
  transition: all 0.2s ease;
}
.nav-phone-link:hover {
  background: #ffffff;
  color: #1C2280;
  border-color: #cbd5e1;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
.nav-phone-link i {
  color: #CC2228;
  font-size: 11.5px;
}
.btn-nav-consultation {
  background: linear-gradient(135deg, #1C2280 0%, #252db5 100%) !important;
  color: #ffffff !important;
  font-family: 'Poppins', sans-serif;
  font-size: 13px !important;
  font-weight: 600 !important;
  padding: 9px 20px !important;
  border-radius: 8px !important;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none !important;
  letter-spacing: 0.1px;
  box-shadow: 0 4px 14px rgba(28, 34, 128, 0.25);
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  white-space: nowrap;
}
.btn-nav-consultation:hover {
  background: linear-gradient(135deg, #252db5 0%, #151a66 100%) !important;
  color: #ffffff !important;
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(28, 34, 128, 0.35);
}
.btn-nav-consultation i {
  font-size: 11px;
  transition: transform 0.2s ease;
}
.btn-nav-consultation:hover i {
  transform: translateX(3px);
}

/* Mobile Responsive */
@media (max-width: 991.98px) {
  #mainNavbar {
    border-bottom: 1px solid #e2e8f0;
  }
  #mainNavbar .container,
  #mainNavbar .container-xl {
    min-height: 60px !important;
    padding-left: 16px !important;
    padding-right: 16px !important;
  }
  .navbar-brand img {
    height: 38px !important;
  }
  #mainNavbar .navbar-collapse {
    max-height: calc(88vh - 65px) !important;
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch !important;
    padding: 16px 10px 24px !important;
    border-top: 1px solid #f1f5f9 !important;
    margin-top: 10px !important;
  }
  #mainNavbar .nav-link {
    padding: 10px 14px !important;
    font-size: 15px !important;
    font-weight: 600 !important;
  }
  #mainNavbar .dropdown-menu {
    box-shadow: none !important;
    border: 1px solid #e2e8f0 !important;
    padding: 12px !important;
    border-radius: 12px !important;
    background: #f8fafc !important;
    margin: 6px 0 12px !important;
  }
  .mega-menu-wrapper,
  .solutions-grid {
    grid-template-columns: 1fr !important;
    gap: 16px !important;
  }
  .mega-spotlight-card {
    display: none !important;
  }
}
</style>

<!-- ── EXECUTIVE UTILITY TOPBAR ────────────────────────────────────── -->
<div class="topbar d-none d-lg-block">
  <div class="container-xl">
    <div class="d-flex justify-content-between align-items-center">
      <!-- Left: Certifications & Delivery Hubs -->
      <div class="d-flex align-items-center gap-3">
        <span class="topbar-trust-tag">
          <i class="fas fa-shield-alt"></i> ISO 27001 Certified &bull; CMMI Level 3 &bull; HIPAA Compliant
        </span>
        <span class="topbar-divider"></span>
        <span style="font-size:11.5px;color:#94a3b8;">
          <i class="fas fa-building text-info me-1"></i> Delivery Hubs: <strong class="text-white">Pune HQ</strong> &bull; <strong class="text-white">Bengaluru Center</strong>
        </span>
      </div>

      <!-- Right: Direct Executive Contact & Social Links -->
      <div class="d-flex align-items-center gap-3">
        <a href="tel:<?= str_replace(['-',' '], '', PHONE_INDIA) ?>" class="d-inline-flex align-items-center gap-1">
          <i class="fas fa-phone-alt text-danger"></i> <?= PHONE_INDIA ?>
        </a>
        <span class="topbar-divider"></span>
        <a href="mailto:<?= EMAIL_SUPPORT ?>" class="d-inline-flex align-items-center gap-1">
          <i class="fas fa-envelope text-info"></i> <?= EMAIL_SUPPORT ?>
        </a>
        <span class="topbar-divider"></span>
        <div class="topbar-socials d-flex align-items-center gap-1">
          <a href="<?= SOCIAL_LINKEDIN ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
          <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="<?= SOCIAL_FACEBOOK ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── PREMIUM MAIN NAVBAR ────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg" id="mainNavbar">
  <div class="container-xl">
    <!-- Brand Logo -->
    <a class="navbar-brand" href="<?= $prefix ?>index.php" aria-label="Vortexsoft Innovations Home">
      <picture>
        <source srcset="<?= $prefix ?>logo-header.webp?v=20261008" type="image/webp">
        <img src="<?= $prefix ?>logo-header.png?v=20261008" alt="Vortexsoft Innovations — Global IT &amp; BPO Company" width="220" height="64" fetchpriority="high">
      </picture>
    </a>

    <!-- Mobile Hamburger Toggler -->
    <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
            aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" style="width:40px;height:40px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #e2e8f0 !important;background:#f8fafc;">
      <i class="fas fa-bars" style="color:#1C2280;font-size:18px;"></i>
    </button>

    <!-- Nav Collapse Content -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <!-- 1. Home -->
        <li class="nav-item">
          <a class="nav-link <?= nav_active('home', $current_path) ?>" href="<?= $prefix ?>index.php">Home</a>
        </li>

        <!-- 2. Services (Architectural Mega Menu) -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= nav_active('service', $current_path) ?>" href="<?= $prefix ?>service.php"
             id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Services <i class="fas fa-chevron-down nav-chevron"></i>
          </a>
          <div class="dropdown-menu smart-services-menu" aria-labelledby="servicesDropdown">
            <div class="mega-menu-wrapper">
              <!-- Pillar 1: AI & Engineering -->
              <div>
                <div class="mega-col-header"><i class="fas fa-microchip"></i> AI &amp; Engineering</div>
                <a class="mega-item" href="<?= $prefix ?>software-solutions/index.php">
                  <span class="mega-item-icon"><i class="fas fa-code"></i></span>
                  <div>
                    <div class="mega-item-title">Custom Software Engineering</div>
                    <div class="mega-item-desc">Enterprise web, cloud &amp; microservices</div>
                  </div>
                </a>
                <a class="mega-item" href="<?= $prefix ?>service.php#ai-automation">
                  <span class="mega-item-icon"><i class="fas fa-robot"></i></span>
                  <div>
                    <div class="mega-item-title">AI &amp; Machine Learning</div>
                    <div class="mega-item-desc">Generative AI, LLMs &amp; automation</div>
                  </div>
                </a>
                <a class="mega-item" href="<?= $prefix ?>data-annotation-services/index.php">
                  <span class="mega-item-icon"><i class="fas fa-tags"></i></span>
                  <div>
                    <div class="mega-item-title">Data Annotation &amp; Vision</div>
                    <div class="mega-item-desc">Bounding box, LiDAR &amp; NLP datasets</div>
                  </div>
                </a>
                <a class="mega-item" href="<?= $prefix ?>development-services/index.php">
                  <span class="mega-item-icon"><i class="fas fa-cloud"></i></span>
                  <div>
                    <div class="mega-item-title">Cloud &amp; DevOps Pods</div>
                    <div class="mega-item-desc">AWS, Kubernetes &amp; CI/CD pipelines</div>
                  </div>
                </a>
              </div>

              <!-- Pillar 2: Healthcare & RCM -->
              <div>
                <div class="mega-col-header"><i class="fas fa-heartbeat"></i> Healthcare BPO</div>
                <a class="mega-item" href="<?= $prefix ?>health-care-services/index.php">
                  <span class="mega-item-icon"><i class="fas fa-sync-alt"></i></span>
                  <div>
                    <div class="mega-item-title">Revenue Cycle Management</div>
                    <div class="mega-item-desc">End-to-end hospital &amp; clinic RCM</div>
                  </div>
                </a>
                <a class="mega-item" href="<?= $prefix ?>medical-coding-services/index.php">
                  <span class="mega-item-icon"><i class="fas fa-file-medical"></i></span>
                  <div>
                    <div class="mega-item-title">Medical Coding (ICD-10/CPT)</div>
                    <div class="mega-item-desc">CPC-certified chart auditing</div>
                  </div>
                </a>
                <a class="mega-item" href="<?= $prefix ?>medical-billing-services/index.php">
                  <span class="mega-item-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                  <div>
                    <div class="mega-item-title">Medical Billing &amp; Posting</div>
                    <div class="mega-item-desc">Claims scrubbing &amp; electronic filing</div>
                  </div>
                </a>
                <a class="mega-item" href="<?= $prefix ?>denial-management-services/index.php">
                  <span class="mega-item-icon"><i class="fas fa-shield-alt"></i></span>
                  <div>
                    <div class="mega-item-title">Denial &amp; AR Recovery</div>
                    <div class="mega-item-desc">Payer appeals &amp; cash acceleration</div>
                  </div>
                </a>
              </div>

              <!-- Pillar 3: Mortgage & Title BPM -->
              <div>
                <div class="mega-col-header"><i class="fas fa-landmark"></i> Mortgage &amp; Title</div>
                <a class="mega-item" href="<?= $prefix ?>title-settlement/index.php">
                  <span class="mega-item-icon"><i class="fas fa-search-location"></i></span>
                  <div>
                    <div class="mega-item-title">40-Year Title Search</div>
                    <div class="mega-item-desc">Deed chains, liens &amp; tax research</div>
                  </div>
                </a>
                <a class="mega-item" href="<?= $prefix ?>mortgage-escrow/index.php">
                  <span class="mega-item-icon"><i class="fas fa-file-signature"></i></span>
                  <div>
                    <div class="mega-item-title">Mortgage &amp; Escrow Support</div>
                    <div class="mega-item-desc">TRID closing packages &amp; disclosures</div>
                  </div>
                </a>
                <a class="mega-item" href="<?= $prefix ?>lease-abstraction-services/index.php">
                  <span class="mega-item-icon"><i class="fas fa-scroll"></i></span>
                  <div>
                    <div class="mega-item-title">Commercial Lease Abstraction</div>
                    <div class="mega-item-desc">Yardi &amp; MRI clause extraction</div>
                  </div>
                </a>
                <a class="mega-item" href="<?= $prefix ?>cam-audit-services/index.php">
                  <span class="mega-item-icon"><i class="fas fa-calculator"></i></span>
                  <div>
                    <div class="mega-item-title">CAM Audit &amp; Accounting</div>
                    <div class="mega-item-desc">Reconciliation &amp; tenant escalations</div>
                  </div>
                </a>
              </div>

              <!-- Pillar 4: Publishing & Enterprise BPO -->
              <div>
                <div class="mega-col-header"><i class="fas fa-book-open"></i> Publishing &amp; BPO</div>
                <a class="mega-item" href="<?= $prefix ?>publishing-services/index.php">
                  <span class="mega-item-icon"><i class="fas fa-book"></i></span>
                  <div>
                    <div class="mega-item-title">STM Publishing &amp; Prepress</div>
                    <div class="mega-item-desc">JATS/BITS XML &amp; InDesign layouts</div>
                  </div>
                </a>
                <a class="mega-item" href="<?= $prefix ?>document-accessibility-services/index.php">
                  <span class="mega-item-icon"><i class="fas fa-universal-access"></i></span>
                  <div>
                    <div class="mega-item-title">Document Accessibility</div>
                    <div class="mega-item-desc">PDF/UA, Section 508 &amp; WCAG 2.1</div>
                  </div>
                </a>
                <a class="mega-item" href="<?= $prefix ?>manpower-payroll-services/index.php">
                  <span class="mega-item-icon"><i class="fas fa-users"></i></span>
                  <div>
                    <div class="mega-item-title">Dedicated Global Staffing</div>
                    <div class="mega-item-desc">Technical pods &amp; offshore workforce</div>
                  </div>
                </a>
                <a class="mega-item" href="<?= $prefix ?>accounting-services/index.php">
                  <span class="mega-item-icon"><i class="fas fa-balance-scale"></i></span>
                  <div>
                    <div class="mega-item-title">Finance &amp; Multi-Country Payroll</div>
                    <div class="mega-item-desc">Statutory accounting &amp; tax compliance</div>
                  </div>
                </a>
              </div>

              <!-- Pillar 5: Spotlight Card -->
              <div class="mega-spotlight-card">
                <div>
                  <span class="mega-spotlight-badge"><i class="fas fa-certificate"></i> ISO 27001 Certified</span>
                  <div class="mega-spotlight-title">Enterprise-Grade Global Delivery</div>
                  <div class="mega-spotlight-text">Over 200+ mission-critical platforms and multi-shift BPM operations delivered with 99.8% SLA compliance.</div>
                </div>
                <a href="<?= $prefix ?>service.php" class="mega-spotlight-btn">
                  <span>Explore All 65+ Services</span>
                  <i class="fas fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>
        </li>

        <!-- 3. Solutions (AI Platforms & Industry Verticals) -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="solutionsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Solutions <i class="fas fa-chevron-down nav-chevron"></i>
          </a>
          <div class="dropdown-menu smart-solutions-menu" aria-labelledby="solutionsDropdown">
            <div class="solutions-grid">
              <!-- Left: Proprietary Platforms -->
              <div>
                <div class="mega-col-header"><i class="fas fa-cubes"></i> Proprietary AI Platforms</div>
                <a class="solutions-item" href="<?= $prefix ?>index.php#ai-products">
                  <span class="solutions-item-icon"><i class="fas fa-id-card-alt"></i></span>
                  <div>
                    <div class="mega-item-title">VortexEXHO</div>
                    <div class="mega-item-desc">Enterprise Workforce OS &amp; AI Copilot</div>
                  </div>
                </a>
                <a class="solutions-item" href="<?= $prefix ?>index.php#ai-products">
                  <span class="solutions-item-icon"><i class="fas fa-user-astronaut"></i></span>
                  <div>
                    <div class="mega-item-title">vortexHire</div>
                    <div class="mega-item-desc">Autonomous AI Candidate Screening</div>
                  </div>
                </a>
                <a class="solutions-item" href="<?= $prefix ?>index.php#ai-products">
                  <span class="solutions-item-icon"><i class="fas fa-headset"></i></span>
                  <div>
                    <div class="mega-item-title">vortexKonnect</div>
                    <div class="mega-item-desc">Call Center Speech &amp; Sentiment AI</div>
                  </div>
                </a>
                <a class="solutions-item" href="<?= $prefix ?>index.php#ai-products">
                  <span class="solutions-item-icon"><i class="fas fa-bullhorn"></i></span>
                  <div>
                    <div class="mega-item-title">Vortexreach</div>
                    <div class="mega-item-desc">Autonomous B2B Outreach Engine</div>
                  </div>
                </a>
              </div>

              <!-- Right: Key Industry Verticals -->
              <div>
                <div class="mega-col-header"><i class="fas fa-industry"></i> Industry Verticals</div>
                <a class="solutions-item" href="<?= $prefix ?>health-care-services/index.php">
                  <span class="solutions-item-icon"><i class="fas fa-hospital"></i></span>
                  <div>
                    <div class="mega-item-title">Healthcare &amp; Hospital Systems</div>
                    <div class="mega-item-desc">HIPAA-compliant RCM &amp; clinical coding</div>
                  </div>
                </a>
                <a class="solutions-item" href="<?= $prefix ?>title-settlement/index.php">
                  <span class="solutions-item-icon"><i class="fas fa-building"></i></span>
                  <div>
                    <div class="mega-item-title">Real Estate &amp; Title Underwriters</div>
                    <div class="mega-item-desc">Nationwide US title &amp; escrow production</div>
                  </div>
                </a>
                <a class="solutions-item" href="<?= $prefix ?>publishing-services/index.php">
                  <span class="solutions-item-icon"><i class="fas fa-newspaper"></i></span>
                  <div>
                    <div class="mega-item-title">Academic &amp; STM Publishers</div>
                    <div class="mega-item-desc">Automated XML, MathML &amp; ePUB3</div>
                  </div>
                </a>
                <a class="solutions-item" href="<?= $prefix ?>software-solutions/index.php">
                  <span class="solutions-item-icon"><i class="fas fa-laptop-code"></i></span>
                  <div>
                    <div class="mega-item-title">Tech &amp; High-Growth Startups</div>
                    <div class="mega-item-desc">Dedicated offshore engineering pods</div>
                  </div>
                </a>
              </div>
            </div>

            <!-- Bottom Action Strip -->
            <div class="pt-2 mt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size:12px; background:#f8fafc; padding:8px 14px; border-radius:8px;">
              <span class="text-muted"><i class="fas fa-award text-warning me-1"></i> Proven Track Record across 150+ Global Enterprise Engagements</span>
              <a href="<?= $prefix ?>contact.php" class="fw-bold text-primary" style="font-size:11.5px; text-decoration:none;">Speak to a Solutions Architect <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
          </div>
        </li>

        <!-- 4. Company -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= str_contains($current_path, 'about') ? 'active' : '' ?>" href="#"
             id="companyDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Company <i class="fas fa-chevron-down nav-chevron"></i>
          </a>
          <div class="dropdown-menu smart-company-menu" aria-labelledby="companyDropdown">
            <a class="solutions-item" href="<?= $prefix ?>about.php">
              <span class="solutions-item-icon"><i class="fas fa-info-circle"></i></span>
              <div>
                <div class="mega-item-title">About Vortexsoft</div>
                <div class="mega-item-desc">Mission, Leadership &amp; Global Story</div>
              </div>
            </a>
            <a class="solutions-item" href="<?= $prefix ?>about.php#certifications">
              <span class="solutions-item-icon"><i class="fas fa-shield-alt text-success"></i></span>
              <div>
                <div class="mega-item-title">Quality &amp; Compliance</div>
                <div class="mega-item-desc">ISO 27001:2013 &amp; HIPAA Certified</div>
              </div>
            </a>
            <a class="solutions-item" href="<?= $prefix ?>about.php#locations">
              <span class="solutions-item-icon"><i class="fas fa-map-marked-alt text-danger"></i></span>
              <div>
                <div class="mega-item-title">Global Delivery Centers</div>
                <div class="mega-item-desc">Pune Corporate HQ &amp; Bengaluru Hub</div>
              </div>
            </a>
          </div>
        </li>

        <!-- 5. Careers -->
        <li class="nav-item">
          <a class="nav-link <?= nav_active('careers', $current_path) ?>" href="<?= $prefix ?>careers.php">
            Careers <span class="nav-hiring-badge"><span class="pulse-dot"></span> Hiring 30+</span>
          </a>
        </li>
      </ul>

      <!-- Right-Side Action Cluster -->
      <div class="nav-actions-cluster d-none d-lg-flex">
        <!-- Direct Phone Trigger -->
        <a href="tel:<?= str_replace(['-',' '], '', PHONE_INDIA) ?>" class="nav-phone-link" title="Call Sales &amp; Consultations: <?= PHONE_INDIA ?>" aria-label="Call <?= PHONE_INDIA ?>">
          <i class="fas fa-phone-alt"></i>
          <span class="d-none d-xl-inline"><?= PHONE_INDIA ?></span>
        </a>

        <!-- Executive Primary CTA -->
        <a href="<?= $prefix ?>contact.php" class="btn-nav-consultation" aria-label="Schedule an Executive Consultation">
          <span>Schedule Consultation</span>
          <i class="fas fa-arrow-right"></i>
        </a>
      </div>

      <!-- Mobile Actions inside Collapse -->
      <div class="d-lg-none mt-3 pt-3 border-top">
        <div class="d-flex flex-column gap-2">
          <a href="<?= $prefix ?>contact.php" class="btn btn-nav-consultation w-100 justify-content-center py-2">
            <span>Schedule Consultation</span> <i class="fas fa-arrow-right ms-1"></i>
          </a>
          <a href="tel:<?= str_replace(['-',' '], '', PHONE_INDIA) ?>" class="btn btn-outline-secondary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" style="border-radius:8px;font-size:13px;">
            <i class="fas fa-phone-alt text-danger"></i> Call: <?= PHONE_INDIA ?>
          </a>
          <a href="https://wa.me/918308906690?text=Hello%20Vortexsoft%20Innovations%20Team" target="_blank" class="btn btn-outline-success w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" style="border-radius:8px;font-size:13px;">
            <i class="fab fa-whatsapp"></i> WhatsApp Direct
          </a>
        </div>
      </div>
    </div>
  </div>
</nav>
</div><!-- /#site-header -->
