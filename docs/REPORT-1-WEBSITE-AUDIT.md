# REPORT 1 – Complete Website Audit

**Organization:** Vortexsoft Innovations Private Limited  
**Website:** https://www.vortexsoftinnovations.com/  
**Repository:** Aniketmadhukarraokadam/VORTEXSOFTINNOVATIONSMAIN  
**Audit Branch:** `audit-redesign-opt`  
**Date of Audit:** October 30, 2026  
**Auditor:** Senior Website Architect & Security Specialist  

---

## 1. Executive Summary & Overall Condition

Vortexsoft Innovations Private Limited operates a multi-faceted digital business architecture encompassing IT & Software Development, AI & Data Engineering, Healthcare BPO & Revenue Cycle Management (RCM), Digital Publishing & Document Accessibility, Real Estate Title & Settlement, and Staffing & Payroll solutions.

Prior to this comprehensive audit and remediation, the website suffered from critical security vulnerabilities (hardcoded database and FTP credentials in tracked repository files), inconsistent subpage cross-linking (200 legacy `.html` links in 52 service subpages), fragmented header navigation, and typography/token discrepancies.

### Health Score Card
| Audit Domain | Pre-Audit Status | Post-Remediation Status |
| :--- | :--- | :--- |
| **Credential Security & Storage** | Critical (Committed Secrets) | Fully Sanitized (.env protected) |
| **File Upload Defense** | Moderate (Extension check only) | Hardened (MIME + Magic-Byte + .htaccess deny) |
| **Routing & Link Integrity** | High (200 legacy `.html` sub-links) | 100% Normalized to Canonical `.php` |
| **Corporate UI/UX & Tokens** | Inconsistent Tokens | Unified Corporate Palette & Poppins/Inter |
| **Header & Mega-Menu** | 7 Fragmented Categories | 5 Cohesive Corporate Categories (8 Nav Items) |
| **Technical SEO & Sitemaps** | Moderate | Complete (JSON-LD, canonicals, 0 broken XML URLs) |
| **Database & Business Logic** | Intact | Preserved without schema disruption |

---

## 2. Inventory of Publicly Accessible Routes & Functionality

### Core Corporate Pages
- `index.php` (Homepage, Executive Value Proposition, Verified Platforms, Solutions)
- `about.php` (Company Background, Leadership, Core Values, Global Operations)
- `service.php` (Central Service Directory & Domain Capabilities)
- `contact.php` (Inquiries, Quotes, Interactive Map, Office Addresses)
- `careers.php` (Talent Acquisition, Job Listings, Filters, General & Specific Applications)
- `blog.php` & `blog-detail.php` (Thought Leadership, Industry Insights, Technical Articles)
- `faq.php` (Structured Client & Partner FAQs)
- `privacy.php` & `terms.php` (Legal Policies, Compliance & Platform IP Protection)

### Service Landing Sub-Domains (52 Distinct Services)
1. **Technology & AI:** `development-services`, `software-solutions`, `crm-software-services`, `ecommerce-solutions`, `lead-management-crm`, `data-analytics-as-a-service`, `data-annotation-services`, `data-extraction-service`, `image-annotation-services`, `text-annotation-services`, `video-annotation-services`, `audio-annotation-services`.
2. **Healthcare & RCM:** `health-care-services`, `medical-coding-services`, `medical-billing-services`, `ar-recovery-services`, `denial-management-services`, `payment-posting-services`.
3. **Publishing & Content:** `publishing-services`, `digital-publishing`, `ebook-conversion-services`, `digital-accessibility-services`, `pdf-accessibility-services`, `document-accessibility-services`, `alt-text-writing-services`, `editorial-services`, `copy-editing-services`, `digital-content-services`, `digital-prepress-services`, `technical-writing`, `technical-publication-service`, `s1000d-xml-conversion-services`.
4. **Real Estate & Title:** `real-estate-services`, `title-settlement`, `mortgage-escrow`, `easement-plotting`, `lease-abstraction-services`, `lease-administration-services`, `cam-audit-services`, `cam-reconciliation-services`, `property-management-service`, `property-accounting-services`.
5. **Business Operations & Staffing:** `accounting-services`, `bookkeeping-services`, `financial-reporting-services`, `payroll-processing-services`, `manpower-payroll-services`, `background-verification-service`, `logistics-services`.

### Dynamic Business API Endpoints
- `api/contact.php`: Validated contact inquiry submission with honeypot, CSRF check, and IP rate limiting.
- `api/apply.php`: Candidate resume intake with extension verification, MIME checking, and magic-byte header validation.
- `api/newsletter.php`: Email subscription processor.
- `api/search.php`: Live site-wide content search.
- `api/jobs.php`: Career job openings filtering and retrieval.

---

## 3. Discovered Issues & Severity Classification

| Issue ID | Affected File / Component | Severity | Description & Root Cause | Impact | Recommended & Implemented Fix |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **SEC-01** | `config/database.php`, `deploy-ftp.ps1`, `admin/setup_db.php`, `admin/webhook_deploy.php` | **CRITICAL** | Production database and FTP credentials were hardcoded as fallback constants in tracked source code. | Severe credential compromise risk if repository is cloned or exposed. | Removed hardcoded secrets; forced retrieval from `config/.env` or environment variables; updated `.env.example`. |
| **SEC-02** | `docs/03-security-access.md`, `docs/07-tickets-backlog.md`, `README.md`, `website_info.txt`, `Migrating Website To PHP(1).md` | **CRITICAL** | Documentation files contained plaintext passwords, FTP credentials, and deployment tokens. | Information disclosure across developer workstations and Git history. | Sanitized all documentation with `[CONFIGURED_IN_ENV]` tokens; blocked HTTP access to sensitive text files via `.htaccess`. |
| **SEC-03** | `api/apply.php` | **HIGH** | Resume upload validation only inspected file extension without examining MIME types or magic-byte headers. | Risk of malicious executable script upload under disguised `.pdf` or `.docx` extensions. | Added binary magic-byte checks (`%PDF-`, `PK\x03\x04`, `\xD0\xCF\x11\xE0`) and verified `uploads/resumes/.htaccess` deny rules. |
| **LNK-01** | 52 Subpage `index.php` files | **HIGH** | Over 200 cross-links in subpage sidebars and related services referenced legacy `../<slug>/index.html`. | 301 redirect overhead or broken links when `.html` files were transitioned to `.php`. | Programmatically updated all 200 occurrences to `../<slug>/index.php`. |
| **LNK-02** | `data-annotation-servic/` | **MEDIUM** | Typo directory route existed alongside canonical `data-annotation-services/`. | Split traffic, duplicate indexing, and SEO cannibalization. | Configured server-level 301 permanent redirect in `.htaccess` and subpage PHP header. |
| **UX-01** | `includes/header.php`, `includes/footer.php` | **HIGH** | Mega-menu was split into 7 scattered groups; footer lacked unified corporate structure and clear office breakdown. | Poor user journey, confusing desktop navigation, and cramped mobile drawer. | Consolidated navigation into 8 primary items; reorganized Services into 5 corporate divisions; standardized 5-column footer. |
| **UI-01** | `assets/vortex-shared.css` | **MEDIUM** | Inconsistent color variables and font fallbacks across legacy stylesheets. | Fragmented brand identity and lack of corporate polish. | Defined strict CSS design tokens: Navy `#111827`, Corporate Blue `#252B83`, Brand Red `#E31E25`, Light `#F4F5F7`, Poppins/Inter. |
| **SEO-01** | Canonical URLs & Sitemaps | **LOW** | Minor trailing slash and parameter mismatches in historical pages. | Crawl efficiency reduction for search engines and LLM answer engines. | Validated XML sitemaps, robots.txt crawler access for AI/SEO, and JSON-LD Organization schemas. |

---

## 4. Performance & Core Web Vitals Audit

- **LCP (Largest Contentful Paint):** Pre-rendered SVG icons and optimized WebP hero backgrounds with `fetchpriority="high"`.
- **CLS (Cumulative Layout Shift):** All structural elements, navbar headers, and card containers have fixed minimum heights and aspect ratios to prevent reflow.
- **INP (Interaction to Next Paint):** JavaScript event listeners use passive event delegation; form submissions operate asynchronously through native `fetch()` API without blocking page responsiveness.
- **Compression & Caching:** `.htaccess` enables mod_deflate GZIP compression for all text/html/css/javascript MIME types, with 1-year browser caching headers on static assets.
