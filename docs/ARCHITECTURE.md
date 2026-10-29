# System Architecture & Technical Specification
**Vortexsoft Innovations Private Limited**  
*Document Version: 2.0.0 — System Architecture*  
*Date of Publication: October 27, 2026*  
*Architecture Tier: Enterprise Modular Web Application & Ingestion Pipeline*

---

## 1. Architectural Philosophy

The Vortexsoft Innovations platform is architected around the **Zero-Bloat Modular PHP Pattern**. Rather than introducing heavy monolithic frameworks (such as Laravel or Symfony) that require hundreds of megabytes of third-party dependencies and significant CPU overhead on shared hosting, the platform leverages native PHP 8.1+ capabilities, PDO connection persistence, and declarative Apache web server configuration.

This delivers:
- **Sub-250ms Average Latency**: Minimal cold-start overhead and rapid execution.
- **High Portability**: Runs out-of-the-box on standard Hostinger shared hosting, VPS, or Docker container environments.
- **Granular Security Boundary**: Direct control over every session, SQL query, input sanitizer, and file upload stream.

---

## 2. High-Level System Architecture Diagram

```
[ Inbound Clients & AI Crawlers ]
 (Desktop, Mobile, Tablet, ChatGPT, Perplexity, Gemini)
                    │
                    ▼
          [ HTTPS / TLS 1.3 ]
                    │
                    ▼
   [ Hostinger Edge / Apache 2.4 ]
   ├── Mod_Rewrite (Clean URLs, Canonical 301, Traversal Block)
   ├── Mod_Headers (HSTS, CSP, X-Frame-Options, NoSniff)
   └── Mod_Deflate & Mod_Expires (GZIP, 1-Year Cache)
                    │
                    ▼
     [ PHP 8.1+ Execution Core ]
     ├── config/database.php  <-- config/.env (git-ignored)
     ├── config/constants.php
     ├── includes/functions.php (Sanitizers, CSRF, RBAC)
     │
     ├── [ Public Presentation Layer ]
     │    ├── index.php, about.php, careers.php, service.php
     │    └── 71x Dedicated Vertical Service Pages
     │
     ├── [ REST Ingestion API Layer ]
     │    ├── api/contact.php (Inquiries, Auto-replies)
     │    ├── api/apply.php   (MIME validation, Resume pipeline)
     │    └── api/newsletter.php (Subscribers)
     │
     └── [ Authenticated Admin Portal ]
          ├── admin/login.php (Brute-force protection)
          ├── admin/dashboard.php
          ├── admin/applications.php (CSRF Protected)
          └── admin/contacts.php (CSRF Protected)
                    │
                    ▼
    [ MySQL Relational Storage (u696371114_vortexsoftcom) ]
    ├── contact_inquiries       ├── blog_posts
    ├── job_applications        ├── admin_users
    ├── jobs                    └── admin_activity_logs
```

---

## 3. Core Software Subsystems

### A. Configuration & Bootstrapping (`config/`)
- **`config/database.php`**: Singleton connection factory implementing the robust `.env` parser. It loads credentials safely from `config/.env` and caches the PDO handle across the request lifecycle.
- **`config/constants.php`**: Contains global corporate attributes, contact addresses, phone numbers, and operational environment definitions (`APP_ENV = 'production'`).

### B. Shared Business Utilities (`includes/`)
- **Input Sanitization**: `sanitize()` eliminates script injection tags and encodes HTML special characters using `ENT_QUOTES` and `UTF-8`.
- **CSRF Token Engine**: `csrf_token()` generates 64-character cryptographically secure pseudo-random tokens (`bin2hex(random_bytes(32))`) stored in the active session; verified via `hash_equals()`.
- **Role-Based Access Control (RBAC)**: `admin_require_role()` enforces granular permission gates for `super_admin`, `admin`, and `viewer` roles.

### C. Presentation & Asset Layer (`assets/`)
- Fluid responsive CSS design tokens (`assets/css/`, `assets/usability-audit-fixes.css`) utilizing `clamp()` formulas for scalable typography.
- Mobile drawer navigation dynamically toggled via vanilla ES6 JavaScript.

---

## 4. Security Architecture

1. **Defense-in-Depth Layering**:
   - Web Server: Apache `.htaccess` denies access to sensitive configuration files (`.env`, `.git`, `database/`, `backups/`).
   - PHP Runtime: Upload validation checks both file extension and MIME binary signatures.
   - Database: 100% prepared statements with parameterized queries.
2. **Access Control**:
   - Administrative portal requires multi-tiered session authorization.
   - Diagnostic scripts (`admin/setup_db.php`) require Super Admin session credentials.
