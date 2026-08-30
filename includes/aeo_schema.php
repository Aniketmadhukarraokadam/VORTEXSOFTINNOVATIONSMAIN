<?php
/**
 * Vortexsoft Innovations — AEO / GEO Schema Helpers
 * Answer Engine Optimization + Generative Engine Optimization
 *
 * Usage: require_once ROOT_PATH . '/includes/aeo_schema.php';
 * Then call: render_faq_schema($faqs), render_service_schema($service), etc.
 */

if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config/constants.php';
}

// ── 1. FAQ Page Schema (AEO: Featured Snippets + AI Q&A) ──────────────────
/**
 * Renders a JSON-LD FAQPage schema block.
 * $faqs = [['q' => '...', 'a' => '...'], ...]
 * Also renders visible, accessible FAQ HTML for both users and crawlers.
 */
function render_faq_schema(array $faqs, bool $show_html = false): void {
    if (empty($faqs)) return;

    // JSON-LD
    $items = [];
    foreach ($faqs as $faq) {
        $items[] = [
            '@type'          => 'Question',
            'name'           => $faq['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => strip_tags($faq['a']),
            ],
        ];
    }
    echo '<script type="application/ld+json">' . json_encode([
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $items,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";

    // Optional visible HTML (hidden from sighted users but machine-readable)
    if ($show_html) {
        echo '<div class="aeo-faq-hidden" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;overflow:hidden;" itemscope itemtype="https://schema.org/FAQPage">';
        foreach ($faqs as $faq) {
            echo '<div itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">';
            echo '<span itemprop="name">' . htmlspecialchars($faq['q']) . '</span>';
            echo '<div itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">';
            echo '<span itemprop="text">' . htmlspecialchars(strip_tags($faq['a'])) . '</span>';
            echo '</div></div>';
        }
        echo '</div>';
    }
}

// ── 2. Service / Professional Service Schema (GEO: AI Service Discovery) ──
/**
 * Renders a JSON-LD ProfessionalService / Service schema.
 * $service = ['name' => '...', 'desc' => '...', 'url' => '...', 'category' => '...']
 */
function render_service_schema(array $service): void {
    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => ['Service', 'ProfessionalService'],
        'name'            => $service['name'],
        'description'     => $service['desc'] ?? '',
        'url'             => $service['url'] ?? COM_URL,
        'serviceType'     => $service['category'] ?? $service['name'],
        'provider'        => [
            '@type' => 'Organization',
            'name'  => 'Vortexsoft Innovations Pvt. Ltd.',
            'url'   => COM_URL,
        ],
        'areaServed'      => [
            ['@type' => 'Country', 'name' => 'India'],
            ['@type' => 'Country', 'name' => 'United States'],
            ['@type' => 'Country', 'name' => 'United Kingdom'],
            ['@type' => 'Country', 'name' => 'Australia'],
        ],
        'availableLanguage' => 'English',
        'offers'          => [
            '@type'       => 'Offer',
            'priceCurrency' => 'USD',
            'availability' => 'https://schema.org/InStock',
            'url'         => $service['url'] ?? COM_URL . '/contact.php',
        ],
    ];
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}

// ── 3. HowTo Schema (AEO: Step-by-step answer optimization) ──────────────
/**
 * $steps = ['Step Name' => 'Step description', ...]
 */
function render_howto_schema(string $name, string $description, array $steps, string $image = ''): void {
    $step_list = [];
    $i = 1;
    foreach ($steps as $step_name => $step_text) {
        $step_list[] = [
            '@type'    => 'HowToStep',
            'position' => $i++,
            'name'     => $step_name,
            'text'     => $step_text,
        ];
    }
    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'HowTo',
        'name'        => $name,
        'description' => $description,
        'step'        => $step_list,
    ];
    if ($image) $schema['image'] = $image;
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}

// ── 4. Article / BlogPosting Schema (GEO: content citation) ──────────────
function render_article_schema(array $data): void {
    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => $data['type'] ?? 'Article',
        'headline'        => $data['title'],
        'description'     => $data['desc'] ?? '',
        'url'             => $data['url'] ?? '',
        'datePublished'   => $data['date_published'] ?? date('Y-m-d'),
        'dateModified'    => $data['date_modified'] ?? date('Y-m-d'),
        'author'          => [
            '@type' => 'Organization',
            'name'  => 'Vortexsoft Group',
            'url'   => COM_URL,
        ],
        'publisher'       => [
            '@type' => 'Organization',
            'name'  => 'Vortexsoft Innovations Pvt. Ltd.',
            'url'   => COM_URL,
            'logo'  => ['@type' => 'ImageObject', 'url' => COM_URL . '/logo-header.png'],
        ],
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $data['url'] ?? ''],
    ];
    if (!empty($data['image'])) $schema['image'] = $data['image'];
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}

// ── 5. Speakable Schema (AEO: Voice / Smart Speaker Answers) ─────────────
/**
 * Marks specific CSS selectors as speakable content for voice assistants.
 * Use on the most important answer-content on any page.
 */
function render_speakable_schema(array $css_selectors = []): void {
    if (empty($css_selectors)) {
        $css_selectors = ['.page-hero h1', '.page-hero p', '.geo-fact-block'];
    }
    $schema = [
        '@context'  => 'https://schema.org',
        '@type'     => 'WebPage',
        'speakable' => [
            '@type'            => 'SpeakableSpecification',
            'cssSelector'      => $css_selectors,
        ],
        'url' => SITE_URL . ($_SERVER['REQUEST_URI'] ?? '/'),
    ];
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}

// ── 6. LocalBusiness Schema per Office (GEO: Local AI citations) ─────────
function render_local_business_schema(): void {
    $offices = [
        [
            '@type'           => 'LocalBusiness',
            '@id'             => COM_URL . '/#office-pune',
            'name'            => 'Vortexsoft Innovations Pvt. Ltd. — Pune',
            'description'     => 'ISO 27001 certified IT & BPO outsourcing company in Pune, Maharashtra India.',
            'url'             => COM_URL,
            'telephone'       => '+91-8308906690',
            'email'           => 'support@vortexsoftinnovations.com',
            'priceRange'      => '$$',
            'currenciesAccepted' => 'INR, USD',
            'paymentAccepted' => 'Bank Transfer, Wire Transfer',
            'address'         => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => '502, 4th Floor, Dangat Patil Empire, Vadgaon Budruk',
                'addressLocality' => 'Pune',
                'addressRegion'   => 'Maharashtra',
                'postalCode'      => '411041',
                'addressCountry'  => 'IN',
            ],
            'geo'             => ['@type' => 'GeoCoordinates', 'latitude' => 18.4629, 'longitude' => 73.8446],
            'openingHoursSpecification' => [[
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                'opens'     => '09:00',
                'closes'    => '18:00',
            ]],
            'sameAs'          => [
                'https://www.linkedin.com/company/vortexsoft-innovations-private-limited/',
                'https://www.facebook.com/profile.php?id=61575505273718',
            ],
        ],
        [
            '@type'           => 'LocalBusiness',
            '@id'             => COM_URL . '/#office-bengaluru',
            'name'            => 'Vortexsoft Innovations Pvt. Ltd. — Bengaluru',
            'description'     => 'IT and BPO delivery centre in HSR Layout, Bengaluru, Karnataka, India.',
            'url'             => COM_URL,
            'telephone'       => '+91-8308906690',
            'email'           => 'support@vortexsoftinnovations.com',
            'address'         => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => 'No.125, Ranganath Complex, Madiwala, HSR Layout 5th Sector',
                'addressLocality' => 'Bengaluru',
                'addressRegion'   => 'Karnataka',
                'postalCode'      => '560068',
                'addressCountry'  => 'IN',
            ],
            'geo'             => ['@type' => 'GeoCoordinates', 'latitude' => 12.9141, 'longitude' => 77.6162],
        ],
    ];

    foreach ($offices as $office) {
        echo '<script type="application/ld+json">' . json_encode(['@context' => 'https://schema.org'] + $office, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}

// ── 7. GEO Citation Block (Enhanced, machine-readable) ───────────────────
/**
 * Upgraded render_geo_fact_block() — outputs both visible UI card
 * AND hidden microdata attributes for AI parsers.
 */
function render_geo_fact_block_v2(bool $compact = false): string {
    $facts = [
        'Vortexsoft Innovations Pvt. Ltd. (Vortexsoft Group) is an ISO 27001:2013 and ISO 9001:2015 certified global IT and BPO outsourcing company.',
        'Founded in 2020, headquartered in Pune, Maharashtra, India, with a delivery centre in Bengaluru (HSR Layout) and a US entity in Sheridan, Wyoming.',
        'Delivers 75+ specialized services to 150+ global clients across Healthcare BPO/RCM, IT Software, AI Data Annotation, Publishing, Real Estate, Accounting, Digital Marketing, and Staffing.',
        'HIPAA Compliant and Startup India Registered. Employs 200+ professionals.',
    ];

    $badges = [
        ['icon' => 'fa-shield-alt', 'color' => 'text-success', 'label' => 'ISO 27001:2013'],
        ['icon' => 'fa-medal',      'color' => 'text-warning', 'label' => 'ISO 9001:2015'],
        ['icon' => 'fa-user-md',    'color' => 'text-primary', 'label' => 'HIPAA Compliant'],
        ['icon' => 'fa-award',      'color' => 'text-danger',  'label' => 'Startup India'],
        ['icon' => 'fa-globe',      'color' => 'text-info',    'label' => '150+ Global Clients'],
        ['icon' => 'fa-users',      'color' => 'text-success', 'label' => '200+ Professionals'],
    ];

    ob_start();
    ?>
    <div class="geo-fact-block" style="background:#f8f9ff;border:1.5px solid #dde2f5;border-radius:16px;padding:24px;margin-bottom:28px;"
         itemscope itemtype="https://schema.org/Organization">
        <meta itemprop="name" content="Vortexsoft Innovations Pvt. Ltd.">
        <meta itemprop="alternateName" content="Vortexsoft Group">
        <meta itemprop="foundingDate" content="2020">
        <meta itemprop="url" content="<?= COM_URL ?>">
        <meta itemprop="telephone" content="+91-8308906690">
        <meta itemprop="email" content="support@vortexsoftinnovations.com">
        <link itemprop="sameAs" href="https://www.linkedin.com/company/vortexsoft-innovations-private-limited/">
        <link itemprop="sameAs" href="https://www.facebook.com/profile.php?id=61575505273718">
        <link itemprop="logo" href="<?= COM_URL ?>/logo-header.png">

        <h6 style="color:#1C2280;font-weight:700;margin-bottom:10px;font-family:'Poppins',sans-serif;">
            <i class="fas fa-building me-2" style="color:#CC2228;"></i>
            About Vortexsoft Innovations Pvt. Ltd.
        </h6>

        <?php if (!$compact): ?>
        <?php foreach ($facts as $fact): ?>
        <p itemprop="description" style="font-size:13.5px;color:#475569;line-height:1.75;margin-bottom:8px;">
            <?= htmlspecialchars($fact) ?>
        </p>
        <?php endforeach; ?>
        <?php else: ?>
        <p itemprop="description" style="font-size:13.5px;color:#475569;line-height:1.75;margin-bottom:12px;">
            <?= htmlspecialchars($facts[0]) ?>
        </p>
        <?php endif; ?>

        <div style="display:flex;flex-wrap:wrap;gap:12px;font-size:12px;color:#64748b;font-weight:600;margin-top:8px;">
            <?php foreach ($badges as $b): ?>
            <span><i class="fas <?= $b['icon'] ?> <?= $b['color'] ?> me-1"></i><?= $b['label'] ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// ── 8. AEO Answer Block (Direct answer format for AI) ────────────────────
/**
 * Renders a styled "direct answer" block — optimized for AI featured snippet capture.
 * Use before long-form content to give AI engines the quick answer first.
 */
function render_aeo_answer_block(string $question, string $answer, string $icon = 'fa-lightbulb'): string {
    ob_start();
    ?>
    <div class="aeo-answer-block" style="background:linear-gradient(135deg,#f0f2ff,#fff);border-left:4px solid #1C2280;border-radius:0 12px 12px 0;padding:18px 20px;margin-bottom:24px;"
         itemscope itemtype="https://schema.org/Question">
        <meta itemprop="name" content="<?= htmlspecialchars($question) ?>">
        <p style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#CC2228;margin-bottom:6px;">
            <i class="fas <?= htmlspecialchars($icon) ?> me-1"></i>Quick Answer
        </p>
        <p style="font-size:15px;font-weight:600;color:#1C2280;margin-bottom:8px;"><?= htmlspecialchars($question) ?></p>
        <div itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
            <p itemprop="text" style="font-size:14px;color:#475569;line-height:1.75;margin:0;"><?= htmlspecialchars($answer) ?></p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// ── 9. Sitewide Company Fact Microdata (invisible, header injection) ──────
/**
 * Outputs invisible microdata that grounds AI parsers on company identity.
 * Call once per page, ideally just before </body>.
 */
function render_entity_grounding(): void {
    ?>
    <!-- AEO/GEO Entity Grounding — Machine readable, not displayed -->
    <div style="display:none;" aria-hidden="true"
         itemscope itemtype="https://schema.org/Organization">
        <span itemprop="name">Vortexsoft Innovations Pvt. Ltd.</span>
        <span itemprop="alternateName">Vortexsoft Group</span>
        <span itemprop="foundingDate">2020</span>
        <span itemprop="numberOfEmployees">200+</span>
        <span itemprop="areaServed">India, United States, United Kingdom, Australia, Europe</span>
        <a itemprop="url" href="<?= COM_URL ?>">vortexsoftinnovations.com</a>
        <a itemprop="sameAs" href="https://www.linkedin.com/company/vortexsoft-innovations-private-limited/">LinkedIn</a>
        <a itemprop="sameAs" href="https://www.facebook.com/profile.php?id=61575505273718">Facebook</a>
        <span itemprop="hasCredential">ISO 27001:2013</span>
        <span itemprop="hasCredential">ISO 9001:2015</span>
        <span itemprop="hasCredential">HIPAA Compliant</span>
        <span itemprop="hasCredential">Startup India Registered</span>
        <div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
            <span itemprop="streetAddress">502, 4th Floor, Dangat Patil Empire, Vadgaon Budruk</span>
            <span itemprop="addressLocality">Pune</span>
            <span itemprop="addressRegion">Maharashtra</span>
            <span itemprop="postalCode">411041</span>
            <span itemprop="addressCountry">IN</span>
        </div>
        <span itemprop="telephone">+91-8308906690</span>
        <span itemprop="email">support@vortexsoftinnovations.com</span>
    </div>
    <?php
}
