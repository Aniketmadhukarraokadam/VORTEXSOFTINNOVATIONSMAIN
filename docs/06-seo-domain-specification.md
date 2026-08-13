# 06 — SEO & Domain Specification
**Vortexsoft Innovations Pvt. Ltd.**
*Confirmed from codebase — August 2026*

---

## Domain Architecture (Confirmed)

### Primary Domain: `vortexsoftinnovations.com`
- Intended for global/international audience
- Main brand domain
- Sitemap hardcoded to `.com` URLs

### Secondary Domain: `vortexsoftinnovations.in`
- Intended for India-specific traffic
- Same codebase, added as cPanel Alias/Parked Domain
- No separate sitemap for `.in`
- No `.in` property in Google Search Console (REQUIRES VERIFICATION)

### Domain Resolution (Confirmed from `config/constants.php`)
```php
$protocol = HTTPS detection via $_SERVER['HTTPS'] and SERVER_PORT
$raw_host  = strtolower($_SERVER['HTTP_HOST']) → e.g. "www.vortexsoftinnovations.in"
$clean_domain = strip www prefix
SITE_URL = $protocol . $raw_host
```

**What this means:**
- All asset URLs, canonical URLs, and email links will auto-update based on which domain serves the request
- `SITE_URL` will be `https://www.vortexsoftinnovations.com` for `.com` visitors
- `SITE_URL` will be `https://www.vortexsoftinnovations.in` for `.in` visitors
- CONFIRMED FROM CODE

---

## Per-Page SEO Metadata (Confirmed from `includes/header.php`)

Every page includes the following head elements:
```html
<title>...</title>
<meta name="description" content="...">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="author" content="Vortexsoft Group">
<meta name="language" content="English">
<meta name="rating" content="general">
<meta name="revisit-after" content="5 days">
<link rel="canonical" href="{canonical_url}">
<link rel="alternate" hreflang="en-IN" href="{in_url}">
<link rel="alternate" hreflang="en-US" href="{com_url}">
<link rel="alternate" hreflang="x-default" href="{com_url}">
<meta property="og:type" content="website">
<meta property="og:locale" content="en_IN">
<meta property="og:url" content="{canonical_url}">
<meta property="og:title" content="...">
<meta property="og:description" content="...">
<meta property="og:image" content="...">
<meta name="twitter:card" content="summary_large_image">
<meta name="geo.region" content="IN-KA">
<meta name="geo.placename" content="Bengaluru, Karnataka, India">
<meta name="geo.position" content="12.9141;77.6162">
<meta name="ICBM" content="12.9141, 77.6162">
```

---

## Hreflang Strategy (Current Implementation)

```html
<link rel="canonical" href="{SITE_URL}/{current_path}">
<link rel="alternate" hreflang="en-IN" href="https://www.vortexsoftinnovations.in/{current_path}">
<link rel="alternate" hreflang="en-US" href="https://www.vortexsoftinnovations.com/{current_path}">
<link rel="alternate" hreflang="x-default" href="https://www.vortexsoftinnovations.com/{current_path}">
```

**Analysis:**
- ✅ Cross-references both domains per page
- ✅ `x-default` points to `.com` (correct — international default)
- ⚠️ `canonical` points to `SITE_URL` (whichever domain is being served) — this is correct BUT:
  - When `.in` serves a page, canonical is `.in` — which tells Google the `.in` version is authoritative for `.in` visitors
  - When `.com` serves a page, canonical is `.com` — correct for global
  - ✅ Strategy is sound

---

## Sitemap

### sitemap.xml (Static — NOT dynamic)
- **Location**: `/sitemap.xml`
- **Format**: Standard XML sitemap
- **URLs**: ALL hardcoded to `https://www.vortexsoftinnovations.com/...`
- **Coverage**: ~50+ URLs (primary pages + service sub-pages)
- **Missing**: `.in` domain has no separate sitemap
- **Missing**: Blog post individual pages (none exist)
- **Missing**: `terms.php` was recently added to sitemap — CONFIRMED

### robots.txt
- **Location**: `/robots.txt`
- **Sitemap reference**: `https://www.vortexsoftinnovations.com/sitemap.xml` (hardcoded to .com)
- **Also references**: `sitemap-images.xml` — file does NOT exist ⚠️
- **Blocks**: `/admin/`, `/uploads/`, `/backups/`, `/career/`, `/assets/partials/`
- **AI Crawlers**: Explicitly whitelisted: GPTBot, ClaudeBot, PerplexityBot, ChatGPT-User, Google-Extended, Meta-ExternalAgent, Applebot, YouBot, cohere-ai

---

## Structured Data / Schema (Confirmed from `index.php` and `includes/header.php`)

### Homepage JSON-LD Schemas
1. **Organization + LocalBusiness + ProfessionalService**
   - Name, URL, logo, description, telephone, email, openingHours
   - `sameAs` links to LinkedIn, Instagram, Facebook
   - Multi-location address (Bengaluru HQ, Pune, Wyoming USA)

2. **FAQPage**
   - 14 FAQ entries covering core services
   - Optimized for rich snippets and AEO (Answer Engine Optimization)

3. **Sitewide Breadcrumb Schema** (in `header.php`)
   - Basic WebSite + Corporation schema on all pages

---

## SEO Issues Found (Tickets Required)

### SEO-001 — MEDIUM: `sitemap-images.xml` Referenced But Does Not Exist
- **File**: `robots.txt` line 71
- **Issue**: Sitemap for images is declared but file doesn't exist. Google will report this as an error in Search Console.
- **Fix**: Either create the image sitemap or remove the reference from robots.txt

### SEO-002 — MEDIUM: Sitemap Only Has `.com` URLs — `.in` Not Covered
- **Issue**: `sitemap.xml` is hardcoded to `.com`. If `.in` is submitted to Google Search Console separately, there's no sitemap to point to.
- **Fix**: Either create `sitemap-in.xml` with `.in` URLs, or register `.in` as a property with the same sitemap and note it as a geo-targeted domain

### SEO-003 — MEDIUM: `privacy.php` Has Hardcoded Canonical URL
- **File**: `privacy.php` line 8
- **Issue**: `$canonical_url = 'https://www.vortexsoftinnovations.com/privacy.php';` — static hardcode overrides the dynamic canonical system. When accessed via `.in`, the canonical will still point to `.com`.
- **Fix**: Remove hardcoded canonical; let `header.php` generate it dynamically from `SITE_URL`

### SEO-004 — LOW: Service Worker Caches `.php` URLs But Shortcut URLs in manifest.json Use `.html`
- **File**: `manifest.json` shortcuts use `/contact.html`, `/careers.html`, `/service.html`
- **Issue**: These resolve via 301 redirect to `.php`, creating an unnecessary round trip for PWA users
- **Fix**: Update manifest.json shortcuts to `.php` URLs directly

### SEO-005 — LOW: `lang="en-US"` Set Globally But Content Is Bilingual Indian Business
- **File**: `includes/header.php`
- **Issue**: `<html lang="en-US">` is used on all pages. For the `.in` domain, `lang="en-IN"` would be more accurate.
- **Fix**: Dynamically set lang attribute based on domain (`en-IN` for `.in`, `en-US` for `.com`)

### SEO-006 — LOW: Blog Posts Have No Individual Detail Pages
- **Issue**: Individual blog post URLs do not exist. Clicking a blog listing item has no destination. Google can't index blog content individually.
- **Fix**: Create `blog-post.php` or `/blog/{slug}/index.php` with proper title/meta/canonical

### SEO-007 — INFO: `robots.txt` Disallows `/career/` (Different Path Than `/careers/`)
- **Issue**: `Disallow: /career/` — legacy path. The active path is `/careers/` and `/careers.php`. This may be intentional (old redirect) or accidental.
- **Status**: REQUIRES VERIFICATION

---

## Redirect Configuration (Confirmed from `.htaccess`)

| Request | Redirect To | Code |
|---|---|---|
| `http://` | `https://` | 301 |
| `non-www` | `www.` | 301 |
| `*.html` | `*.php` | 301 |
| `index.html` | `index.php` | 301 |
| `about.html` | `about.php` | 301 |
| `contact.html` | `contact.php` | 301 |
| `service.html` | `service.php` | 301 |
| `careers.html` | `careers.php` | 301 |
| `blog.html` | `blog.php` | 301 |

---

## Open Graph & Social Preview (Confirmed)

- `og:type`: website
- `og:locale`: en_IN (all pages — does not change by domain)
- `og:image`: Defaults to `SITE_URL . '/logo-header.jpg'`
- Twitter card: summary_large_image
- Twitter site: `@vortexsoft` (referenced in `header.php`)
