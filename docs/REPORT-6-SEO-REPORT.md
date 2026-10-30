# REPORT 6 – Technical SEO, AEO & GEO Audit

**Organization:** Vortexsoft Innovations Private Limited  
**Website:** https://www.vortexsoftinnovations.com/  
**Repository:** Aniketmadhukarraokadam/VORTEXSOFTINNOVATIONSMAIN  
**Audit Branch:** `audit-redesign-opt`  
**Date:** October 30, 2026  

---

## 1. Technical SEO & Crawlability Architecture

### A. Indexing & Canonical URLs
- **Canonical Consistency:** All canonical tags consistently specify HTTPS, `www` domain, and `.php` or clean directory URLs (e.g., `https://www.vortexsoftinnovations.com/contact.php`).
- **Redirects:** Legacy `.html` URLs, uppercase variations, and non-www HTTP requests are permanently redirected (301) to canonical destinations via `.htaccess`.
- **404 Handling:** Custom user-friendly 404 page (`404.php`) provides helpful navigation links back to active service categories and search.

### B. Robots.txt Configuration
- Permits search engines (Googlebot, Bingbot) full access to public assets and services while shielding private administrative assets:
  - Allowed: `/`, `/uploads/blog/`, `/llms.txt`, `/ai.txt`
  - Disallowed: `/admin/`, `/uploads/resumes/`, `/backups/`, `/assets/partials/`
- Explicit directives enabled for AI search and Answer Engine Optimization (AEO/GEO): ChatGPT (`GPTBot`, `OAI-SearchBot`), Claude (`ClaudeBot`), Perplexity (`PerplexityBot`), Gemini (`Google-Extended`), Meta Llama (`Meta-ExternalAgent`).
- Sitemaps declared clearly at the foot of `robots.txt`.

### C. XML Sitemaps
1. `sitemap.xml`: Primary index covering core pages, service directories, and service subpages. Validated: 0 broken or `.html` references.
2. `jobs-sitemap.xml`: Dedicated career and job posting index.
3. `sitemap-images.xml`: High-resolution imagery and visual asset index.
4. `geo-sitemap.xml`: Geographical location sitemap covering Pune HQ, Bengaluru, and Sheridan USA branches.

---

## 2. Structured Data (Schema.org JSON-LD)

Implemented schemas across the site:
- **Organization Schema:** Declares legal corporate name (*Vortexsoft Innovations Private Limited*), registration status (CMMI Level 3 Appraised, Startup India, ISO 9001/27001), official logos, telephone, email, and social profiles.
- **WebSite Schema:** Declares site search query potential (`SearchAction`).
- **LocalBusiness Schema:** Multi-location schema for Pune Headquarters, Bengaluru branch, and Sheridan USA office.
- **Service Schema:** Configured on all 52 service subpages detailing service type, provider, area served, and terms.
- **JobPosting Schema:** Applied to active career listings on `careers.php`.
- **FAQPage Schema:** Configured on `faq.php` and service pages with valid Q&A markup.

---

## 3. On-Page SEO & Metadata Quality

- **Title Tags:** Structured as `[Page Name] | [Primary Value] | Vortexsoft Innovations Private Limited` within the 55–60 character threshold.
- **Meta Descriptions:** Unique, actionable, 140–160 character descriptions summarizing services and driving click-through rate (CTR).
- **Heading Hierarchy:** Strictly enforced one `<h1>` per page, followed by logical `<h2>` section dividers and `<h3>` component cards.
- **Image Accessibility:** All core imagery features descriptive `alt` tags and native `loading="lazy"` attributes to preserve bandwidth and assist screen readers.
- **OpenGraph & Twitter Cards:** Full social graph metadata (`og:title`, `og:description`, `og:image`, `og:url`, `twitter:card`) embedded across headers.
