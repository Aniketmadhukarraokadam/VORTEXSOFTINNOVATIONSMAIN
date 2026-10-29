# Project Discovery & Technology Stack Audit
**Vortexsoft Innovations Private Limited**  
*Document Version: 2.0.0 — Production Environment Discovery*  
*Date of Audit: October 27, 2026*  
*Target URL: [https://www.vortexsoftinnovations.com](https://www.vortexsoftinnovations.com)*  
*Hosting Infrastructure: Hostinger International (Shared Premium Web Hosting / LiteSpeed & Apache)*

---

## 1. Executive Summary

This Project Discovery document establishes an empirical, read-only baseline of the Vortexsoft Innovations live web application. The audit was conducted directly against the local workspace repository and verified against the live production server hosted on Hostinger.

The application is an enterprise-grade corporate platform for an ISO 27001:2013 certified, ISO 9001:2015 accredited, and HIPAA-compliant global IT and BPO service provider headquartered in Pune, Maharashtra, with an advanced tech center in Bengaluru and a corporate registration in Sheridan, Wyoming, USA.

---

## 2. Verified Technology Stack

| Layer | Verified Technology | Implementation Details & Version Notes |
| :--- | :--- | :--- |
| **Server Runtime** | PHP 8.1+ / Apache 2.4 | Running on Hostinger Shared Web Platform (`u696371114`) with LiteSpeed caching modules. |
| **Application Architecture** | Vanilla Modular PHP (MVC-lite) | Clean procedural/object separation. No bulky external frameworks (Laravel/Symfony), ensuring sub-300ms page response times. |
| **Database Engine** | MySQL (MariaDB 10.11+) | Managed via PHP PDO (`mysql:host=localhost;dbname=u696371114_vortexsoftcom`). Character set: `utf8mb4_unicode_ci`. |
| **Frontend UI Core** | HTML5 Semantic / Vanilla CSS3 / ES6+ JavaScript | Modern design system with responsive custom CSS (`assets/css/`, `assets/usability-audit-fixes.css`), fluid clamp typography, touch-first responsive drawers. |
| **UI Frameworks & Icons** | Bootstrap 5.3.0 & FontAwesome 6.4.0 | Minified vendor bundles served locally and via CDN fallback (`assets/vendor/`). |
| **Web Server Rewriting** | Apache `mod_rewrite`, `mod_deflate`, `mod_expires` | Managed via root [`.htaccess`](file:///c:/Users/anike/OneDrive/Documents/vortexsoft%20website/VORTEXSOFTINNOVATIONSMAIN/.htaccess) providing 301 canonical redirects, gzip compression, browser cache expires, and directory traversal blocks. |
| **Deployment Engine** | FTP Sync Automation (`deploy-ftp.ps1`) | Differential and atomic file synchronization to Hostinger production web root (`public_html`). |
| **AI & Search Optimization** | GEO & AEO Protocols (`llms.txt`, `llms-full.txt`, JSON-LD) | Structured Schema.org (`Organization`, `JobPosting`, `Service`, `BreadcrumbList`) and LLM discovery endpoints for ChatGPT, Claude, Gemini, and Perplexity. |

---

## 3. Directory & File Organization

The application structure consists of 71 service subdirectories and root-level orchestrator files:

```
VORTEXSOFTINNOVATIONSMAIN/
├── .htaccess                      # Apache security headers, GZIP compression, URL rewrite engine
├── config/
│   ├── .env                       # Environment variables (DB, FTP, API keys) - git-ignored
│   ├── .env.example               # Sanitized environment template
│   ├── constants.php              # Global site parameters, company metadata, branding URLs
│   └── database.php               # PDO singleton, robust .env parser, connection pool
├── includes/
│   ├── header.php                 # Global responsive navigation, canonical tags, Open Graph meta
│   ├── footer.php                 # Global footer, certification badges, contact links, legal citations
│   ├── functions.php              # Security sanitizers, CSRF token management, mailer, RBAC checks
│   └── service_template.php       # Unified layout for dynamic service offerings
├── admin/                         # Authenticated administration console
│   ├── dashboard.php              # Executive metrics (inquiries, applications, views)
│   ├── applications.php           # Career candidate portal, resume inspection, status workflow
│   ├── contacts.php               # Inbound quote inquiries manager
│   ├── jobs.php                   # Career openings CRUD manager
│   ├── users.php                  # Multi-role admin management (super_admin, admin, viewer)
│   ├── backup_db.php              # Self-contained SQL backup generator
│   ├── download.php               # Path-traversal protected resume downloader
│   ├── login.php                  # Brute-force protected admin authentication
│   └── setup_db.php               # Database table generator (Audit Flag: needs session lock)
├── api/                           # RESTful JSON ingestion endpoints
│   ├── apply.php                  # Career application ingestion + file attachment verification
│   ├── contact.php                # Contact form processing + automated acknowledgement
│   └── newsletter.php             # Double opt-in newsletter subscription
├── assets/                        # Static styling, scripting, and media assets
│   ├── css/                       # Core styles, animations, responsive design tokens
│   ├── js/                        # Form handlers, mobile navigation toggler, carousel logic
│   └── vendor/                    # Bootstrap & FontAwesome local offline mirrors
├── docs/                          # Architectural documentation, safety plans, audit reports
├── database/                      # SQL schema migrations and initial seed dumps
└── uploads/                       # Upload repository (resumes, attachments) protected via .htaccess
```

---

## 4. Entry Points & Routing Analysis

1. **Root Routing (`/` → `index.php`)**:
   - Routes clean URLs without `.php` extensions using `.htaccess` rules.
   - Example: `/careers` rewrites cleanly to `/careers.php`, `/about` to `/about.php`, `/contact` to `/contact.php`.
2. **Dedicated Service Landing Pages**:
   - 71 dedicated directories (e.g., `/medical-coding-services/index.php`, `/data-annotation-services/index.php`, `/cam-reconciliation-services/index.php`) contain hyper-focused SEO content, schema markup, and conversion forms.
3. **API Endpoints (`/api/*`)**:
   - Handle asynchronous `POST` submissions with rate limiting, honeypot spam traps, and JSON responses.
4. **Administration Portal (`/admin/*`)**:
   - Restricts unauthenticated access via `admin_check()` and RBAC role gating (`admin_require_role()`).

---

## 5. Third-Party Integrations & External Dependencies

- **CDNs & Fonts**: Google Fonts (`Inter`, `Plus Jakarta Sans`, `Poppins`), cdnjs/jsdelivr for Bootstrap 5 & FontAwesome.
- **AI API Connectors**: Optional Gemini API and Groq API connectors configured in `constants.php` for admin content intelligence.
- **Social & Communications**: Direct API links to WhatsApp Business API (`wa.me/918308906690`), LinkedIn Corporate, Facebook, and Instagram.

---

## 6. Conclusion

The application possesses a lean, high-performance architecture perfectly suited for Hostinger shared web hosting. It avoids framework bloat while providing modular scalability. Immediate operational focus must center on locking down unauthenticated setup scripts, eliminating hardcoded password fallbacks, tightening CSRF controls, and enforcing resume upload MIME validation.
