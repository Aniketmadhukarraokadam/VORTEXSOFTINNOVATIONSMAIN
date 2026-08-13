# 02 — Technical Architecture Document
**Vortexsoft Innovations Pvt. Ltd.**
*Confirmed from codebase — August 2026*

---

## Technology Stack

| Technology | Version | Purpose | Where Used |
|---|---|---|---|
| PHP | 8.0+ | Server-side rendering, API, admin | All `.php` files |
| MySQL / MariaDB | 8.0 | Primary database | Hostinger DB: `u696371114_vortexsoftcom` |
| SQLite | 3 (fallback) | Local dev fallback | `sqlite_dev.db` |
| PDO | Native | Database abstraction | `config/database.php` |
| Apache | 2.4+ | Web server, URL rewriting | `.htaccess` |
| Bootstrap | 5.3 | CSS framework / grid | All public pages |
| FontAwesome | 6 | Icon library | All pages |
| Vanilla JS (ES6+) | — | Frontend interactions | `assets/vortex-shared.js` |
| Service Worker | — | PWA caching | `sw.js` (v10) |
| Bcrypt | cost=10–12 | Password hashing | `admin/login.php` |
| AES-256-GCM | — | Secret encryption helper | `includes/functions.php` |

---

## Architecture Diagram

```
External User (Browser)
        ↓
  [DNS: vortexsoftinnovations.com / .in]
        ↓
  [Apache + .htaccess]
  - HTTPS redirect
  - www redirect
  - .html → .php 301 redirects
  - Security headers (CSP, HSTS, X-Frame)
  - Blocked: /config/, /includes/, /database/, /uploads/resumes/
        ↓
  [PHP Frontend Pages]
  index.php / about.php / contact.php / service.php
  careers.php / blog.php / privacy.php / terms.php
  + 70+ /service-name/index.php pages
        ↓  
  [PHP Includes]
  config/constants.php → Dynamic SITE_URL, email constants
  config/database.php → PDO MySQL (fallback: SQLite)
  includes/header.php → HTML head, meta, hreflang, canonical, nav
  includes/footer.php → Footer, scripts, newsletter
  includes/functions.php → sanitize, CSRF, rate-limit, email, auth
        ↓
  [REST API Endpoints]
  api/contact.php (POST)
  api/apply.php (POST + file upload)
  api/newsletter.php (POST)
        ↓
  [MySQL Database] — u696371114_vortexsoftcom @ Hostinger
  contact_inquiries | job_applications | blog_posts
  newsletter_subscribers | admin_users | system_settings
        ↓
  [PHP mail()] → Email delivery
  support@vortexsoftinnovations.com (contacts)
  careers@vortexsoftinnovations.in (applications)

ADMIN PATH:
  /admin/login.php → session auth → /admin/dashboard.php
  → contacts.php | applications.php | blog-posts.php | logout.php

DEPLOY PATH:
  GitHub Push → /admin/webhook_deploy.php (token auth)
  → downloads ZIP → syncs to public_html
```

---

## File and Folder Structure

```
VORTEXSOFTINNOVATIONSMAIN-main/
├── config/
│   ├── constants.php        ⚠️ DO NOT CASUALLY EDIT — Dynamic SITE_URL, all app constants
│   ├── database.php         ⚠️ DO NOT CASUALLY EDIT — PDO connection, SQLite fallback
│   ├── .env                 🔒 NOT COMMITTED — holds real DB credentials
│   └── .env.example         Template for environment variables
├── includes/
│   ├── functions.php        Core helpers — sanitize, email, CSRF, rate-limit, auth, pagination
│   ├── header.php           ⚠️ CRITICAL — HTML head, meta, hreflang, canonical, nav
│   └── footer.php           ⚠️ CRITICAL — trust marquee, footer columns, scripts
├── database/
│   └── setup.sql            MySQL schema (7 tables) + seed data
├── api/
│   ├── contact.php          Contact form endpoint
│   ├── apply.php            Job application + file upload endpoint
│   └── newsletter.php       Newsletter subscription endpoint
├── admin/
│   ├── login.php            Session auth + brute-force protection
│   ├── dashboard.php        Stats + recent entries
│   ├── contacts.php         Contact inquiry manager
│   ├── applications.php     Job application manager
│   ├── blog-posts.php       Blog CRUD manager
│   ├── logout.php           Session destroy
│   ├── generate_hash.php    ⚠️ MUST DELETE in production — password hash generator
│   └── webhook_deploy.php   🚨 CRITICAL SECURITY RISK — token hardcoded in source
├── assets/
│   ├── vortex-shared.css    Main stylesheet (30KB)
│   ├── vortex-shared.js     Main JS (14KB)
│   ├── vendor/              Bootstrap 5.3, FontAwesome 6, Google Fonts
│   ├── fonts/               Local font files
│   ├── images/              Static image assets
│   └── partials/            (LEGACY — blocked in robots.txt, purpose unclear)
├── uploads/
│   └── resumes/             Protected resume uploads (.htaccess restricts PHP execution)
├── docs/                    📁 Documentation (newly created)
├── [service-name]/index.php 75+ service-specific pages (individual directories)
├── index.php                Homepage
├── about.php, contact.php, service.php, careers.php, blog.php, privacy.php, terms.php
├── 404.php                  Custom error page
├── .htaccess                Apache rules — security headers, redirects, compression
├── robots.txt               Crawler rules + AI bot whitelist
├── sitemap.xml              Static XML sitemap (hardcoded to .com URLs)
├── manifest.json            PWA manifest
├── sw.js                    Service Worker v10
└── sqlite_dev.db            Local SQLite dev database
```

### Files That Must NOT Be Changed Casually
- `config/constants.php` — dynamic domain resolution; change breaks ALL URLs and assets
- `includes/header.php` — canonical URLs, hreflang, CSP metadata
- `config/database.php` — PDO singleton; change breaks all DB access
- `includes/functions.php` — CSRF, auth, rate-limiting; security-critical
- `admin/login.php` — brute-force protection; do not simplify
- `.htaccess` — security headers, blocking rules; test carefully

---

## Database Architecture

### Table 1: `contact_inquiries`
| Column | Type | Required | Default | Notes |
|---|---|---|---|---|
| id | INT UNSIGNED | YES | AUTO_INCREMENT | PK |
| name | VARCHAR(120) | YES | — | Submitter name |
| email | VARCHAR(180) | YES | — | Submitter email |
| phone | VARCHAR(30) | NO | NULL | Optional |
| service | VARCHAR(120) | NO | 'General Inquiry' | Service of interest |
| company | VARCHAR(120) | NO | NULL | Company name |
| message | TEXT | YES | — | Inquiry text |
| ip_address | VARCHAR(45) | NO | NULL | Submitter IP |
| user_agent | VARCHAR(255) | NO | NULL | Browser UA |
| source_page | VARCHAR(255) | NO | NULL | Referrer URL |
| is_read | TINYINT(1) | YES | 0 | Admin read flag |
| is_replied | TINYINT(1) | YES | 0 | Admin replied flag |
| notes | TEXT | NO | NULL | Admin internal notes |
| created_at | DATETIME | YES | CURRENT_TIMESTAMP | — |

Indexes: `idx_email`, `idx_is_read`, `idx_created_at`

### Table 2: `job_applications`
| Column | Type | Required | Default | Notes |
|---|---|---|---|---|
| id | INT UNSIGNED | YES | AUTO_INCREMENT | PK |
| job_title | VARCHAR(200) | YES | — | Applied position |
| department | VARCHAR(100) | NO | NULL | Department |
| applicant_name | VARCHAR(120) | YES | — | Full name |
| email | VARCHAR(180) | YES | — | Email |
| phone | VARCHAR(30) | NO | NULL | Phone |
| current_location | VARCHAR(120) | NO | NULL | City |
| experience_years | DECIMAL(4,1) | NO | NULL | Years of experience |
| current_company | VARCHAR(120) | NO | NULL | Current employer |
| notice_period | VARCHAR(60) | NO | NULL | e.g. "30 days" |
| expected_ctc | VARCHAR(60) | NO | NULL | Expected salary |
| resume_filename | VARCHAR(255) | NO | NULL | Stored filename |
| resume_path | VARCHAR(500) | NO | NULL | Full URL to resume |
| cover_letter | TEXT | NO | NULL | Cover letter text |
| linkedin_url | VARCHAR(300) | NO | NULL | LinkedIn profile |
| portfolio_url | VARCHAR(300) | NO | NULL | Portfolio URL |
| ip_address | VARCHAR(45) | NO | NULL | Submitter IP |
| status | ENUM | YES | 'new' | new/reviewed/shortlisted/interview/offered/rejected/withdrawn |
| admin_notes | TEXT | NO | NULL | Internal notes |
| created_at | DATETIME | YES | CURRENT_TIMESTAMP | — |
| updated_at | DATETIME | YES | ON UPDATE | — |

Indexes: `idx_email`, `idx_status`, `idx_job_title`, `idx_created_at`

### Table 3: `blog_posts`
| Column | Type | Notes |
|---|---|---|
| id | INT UNSIGNED PK | — |
| title | VARCHAR(300) | Post title |
| slug | VARCHAR(320) UNIQUE | URL slug |
| excerpt | VARCHAR(500) | Short description |
| content | LONGTEXT | Full HTML content |
| author | VARCHAR(100) | Default: 'Vortexsoft Team' |
| author_role | VARCHAR(100) | Optional role |
| cover_image | VARCHAR(500) | Image URL |
| category | VARCHAR(100) | Default: 'General' |
| tags | VARCHAR(500) | Comma-separated |
| meta_title | VARCHAR(300) | SEO title override |
| meta_desc | VARCHAR(500) | SEO meta description |
| views | INT UNSIGNED | View counter |
| is_published | TINYINT(1) | Published flag |
| is_featured | TINYINT(1) | Featured flag |
| published_at | DATETIME | Publish date |

Indexes: `uk_slug`, `idx_category`, `idx_is_published`, `idx_published_at`

### Table 4: `newsletter_subscribers`
| Column | Type | Notes |
|---|---|---|
| id | INT UNSIGNED PK | — |
| email | VARCHAR(180) UNIQUE | Subscriber email |
| name | VARCHAR(120) | Optional name |
| ip_address | VARCHAR(45) | Subscription IP |
| is_active | TINYINT(1) | Active flag |
| unsubscribe_token | VARCHAR(64) | Unsubscribe link token |
| subscribed_at | DATETIME | — |
| unsubscribed_at | DATETIME | NULL if active |

### Table 5: `admin_users`
| Column | Type | Notes |
|---|---|---|
| id | INT UNSIGNED PK | — |
| username | VARCHAR(60) UNIQUE | Login username |
| password_hash | VARCHAR(255) | Bcrypt hash |
| email | VARCHAR(180) UNIQUE | Admin email |
| full_name | VARCHAR(120) | Display name |
| role | ENUM | super_admin / admin / viewer |
| is_active | TINYINT(1) | Account enabled flag |
| last_login | DATETIME | Last successful login |
| login_count | INT UNSIGNED | Total login count |
| created_at | DATETIME | — |

**Seeded accounts** (from `setup.sql`):
1. `admin@vortexsoftinnovations.in` — role: super_admin
2. `Aniket@vortexsoftinnovations.in` — role: admin

### Table 6: `system_settings`
| Column | Type | Notes |
|---|---|---|
| setting_key | VARCHAR(100) PK | Key name |
| setting_value | TEXT | Value |
| updated_at | DATETIME | — |

### Tables Referenced in Code But NOT in `setup.sql` (⚠️ REQUIRES VERIFICATION)
- `admin_activity_logs` — referenced in `log_admin_activity()` function
- `email_templates` — referenced in `get_email_template()` function
- `email_accounts` — referenced in `send_notification_email()` function
- `email_logs` — referenced in `send_notification_email()` function
- `jobs` — referenced in `dashboard.php` (try/catch wraps it, so fails silently)

---

## API Architecture

### POST `/api/contact.php`
- **Auth**: None (public) | CSRF token validated
- **Rate Limit**: 5 requests per 5 minutes per IP
- **Spam**: Honeypot field `website_hp`
- **Input**: `fullName` (req), `emailAddr` (req), `phone`, `service`, `company`, `msgText` (req, min 10 chars)
- **Validation**: name ≤120, message ≤5000, email format, phone format
- **DB**: INSERT into `contact_inquiries`
- **Email**: Notification to `EMAIL_SUPPORT` + auto-reply to user
- **Output**: `{success, message, inquiry_id}`
- **Error Codes**: Validation errors return `success: false`

### POST `/api/apply.php`
- **Auth**: None (public)
- **Rate Limit**: 3 requests per 10 minutes per IP
- **Spam**: Honeypot field `website_hp`
- **Input**: `applicant_name` (req), `email` (req), `phone` (req), `job_title` (req), + optional fields
- **File Upload**: `resume` — PDF/DOC/DOCX, max 5MB. Saved to `/uploads/resumes/`
- **DB**: INSERT into `job_applications`
- **Email**: Notification to `EMAIL_HR` + auto-reply to applicant
- **Output**: `{success, message, application_id}`

### POST `/api/newsletter.php`
- **Auth**: None (public)
- **Rate Limit**: 3 requests per 5 minutes per IP
- **Input**: `email` (req), `name` (optional)
- **Logic**: Duplicate check; reactivates if previously unsubscribed
- **DB**: INSERT/UPDATE `newsletter_subscribers`
- **Output**: `{success, message}`

---

## Environment Configuration (No Secrets Exposed)

```ini
# config/.env (NOT committed to Git)
DB_HOST=localhost
DB_NAME=<DATABASE_NAME>
DB_USER=<DATABASE_USER>
DB_PASS=<SECRET>
SMTP_HOST=smtp.hostinger.com
SMTP_PORT=465
SMTP_USER=contact@vortexsoftinnovations.com
SMTP_PASS=<SECRET>
DEPLOY_TOKEN=<SECRET>
GITHUB_API_TOKEN=<SECRET>
```

> ⚠️ **CRITICAL**: `DEPLOY_TOKEN` is ALSO hardcoded in `admin/webhook_deploy.php` at line 2. This is a security defect — see ticket WEB-SEC-001.
