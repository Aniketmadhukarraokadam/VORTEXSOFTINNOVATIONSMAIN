# 🚀 Vortexsoft Group — Updated Features & System Changelog

**Repository**: [Vortexsoft Innovations Main](https://github.com/Aniketmadhukarraokadam/VORTEXSOFTINNOVATIONSMAIN)  
**Branch**: `main`  
**Latest Synced Commit**: `15a14ac`  
**Status**: Up-to-date with remote repository  
**Document Version**: 2.6  
**Last Updated**: August 2026  

---

## 📌 Executive Summary

This document provides a comprehensive inventory of all updated features, architectural enhancements, security upgrades, AI tools, administration modules, and SEO/AEO/GEO optimizations implemented in the **Vortexsoft Group Enterprise Web Platform & Admin System**.

---

## 📑 Table of Contents

1. [Git Synchronization & Codebase Status](#1-git-synchronization--codebase-status)
2. [AI & Content Generation Engine (V2 Feature)](#2-ai--content-generation-engine-v2-feature)
3. [Enterprise Administration Panel (/admin)](#3-enterprise-administration-panel-admin)
4. [Database & Backend Infrastructure](#4-database--backend-infrastructure)
5. [Frontend & User Experience Upgrades](#5-frontend--user-experience-upgrades)
6. [Proprietary Product Platforms Showcase](#6-proprietary-product-platforms-showcase)
7. [REST APIs & Form Processing Endpoints](#7-rest-apis--form-processing-endpoints)
8. [Security Hardening & Access Control](#8-security-hardening--access-control)
9. [SEO, AEO & GEO Search Optimization](#9-seo-aeo--geo-search-optimization)
10. [Recent Commit History & Changelog](#10-recent-commit-history--changelog)

---

## 1. Git Synchronization & Codebase Status

- **Remote Origin**: `https://github.com/Aniketmadhukarraokadam/VORTEXSOFTINNOVATIONSMAIN`
- **Branch**: `main`
- **Sync Command**: `git pull origin main` executed successfully (`Already up to date`).
- **Head Commit Hash**: `15a14ac` — *[SECURITY]: Remove temporary DB diagnostic script after confirming live MySQL connection*

---

## 2. AI & Content Generation Engine (V2 Feature)

### 🤖 Multi-Provider AI Blog Generator (`admin/blog/generate.php`)
- **3-Way AI Engine Comparison**: Generates blog articles simultaneously or individually using three leading LLM providers:
  1. **Google Gemini**: Models `gemini-3.6-flash` / `gemini-2.5-flash` via Google Generative Language API.
  2. **Groq**: Model `llama-3.3-70b-versatile` with ultra-low latency inference.
  3. **OpenRouter**: Model `meta-llama/llama-3.3-70b-instruct` with multi-model fallback.
- **Provider Abstraction Layer (`includes/ai-providers.php`)**:
  - Centralized API key management via `.env` file (`GROQ_API_KEY`, `GEMINI_API_KEY`, `OPENROUTER_API_KEY`).
  - Structured prompt engineering enforcing strict company facts (Pune HQ, Bengaluru & Wyoming offices, 200+ projects, 150+ clients, 6+ years).
  - Resilient JSON parser capable of extracting and sanitizing JSON payloads enclosed in markdown code fences or raw strings.
  - cURL SSL fallback and configurable 180s execution timeouts for reliable execution across localhost and production environments.
- **Side-by-Side Review & Editing UI**:
  - Live side-by-side comparison tab interface for evaluating outputs from all 3 providers.
  - In-browser HTML editor allowing instant manual edits before saving.
  - Direct 1-click publishing or saving as draft into the MySQL `blog_posts` table with automatic slug, excerpt, and category assignment.

---

## 3. Enterprise Administration Panel (`/admin`)

| Admin Module | File Path | Key Capabilities |
|---|---|---|
| **Executive Dashboard** | `admin/dashboard.php` | Live KPI cards (Total Inquiries, Job Applications, Blog Posts, Subscribers), real-time activity feeds, quick action buttons. |
| **Authentication & Login** | `admin/login.php` | Bcrypt password verification (`cost=12`), rate-limiting (10 failed attempts / 5 mins), database fallback authentication, CSRF token validation. |
| **Contact Inquiries Manager** | `admin/contacts.php` | Search & filter inquiries by status (new/read/replied), read message modal, internal admin notes, click-to-email reply integration, CSV export. |
| **Job Applications Manager** | `admin/applications.php` | Recruitment pipeline tracking (`new` → `reviewed` → `shortlisted` → `interview` → `offered` → `rejected`), candidate ratings, filter by job role. |
| **Secure Resume Downloader** | `admin/download.php` | Permission-checked binary file delivery preventing direct directory traversal or public file access. |
| **Job Postings CRUD** | `admin/jobs.php` | Create, edit, activate/deactivate job listings; set job type (Full-time, Remote, Hybrid), experience requirements, and location. |
| **Blog Post Manager** | `admin/blog-posts.php` | Complete CRUD for blog posts, slug generation, rich HTML editing, featured post toggle, category tags, view counters. |
| **AI Blog Generator** | `admin/blog/generate.php` | Multi-provider AI generation interface with side-by-side comparison and 1-click publishing. |
| **Email Accounts Manager** | `admin/email-accounts.php` | Manage multiple SMTP outgoing mail accounts, port/encryption settings, and live connection test tool. |
| **Email Templates Engine** | `admin/email-templates.php` | Visual template manager supporting dynamic tags (`{{name}}`, `{{inquiry_id}}`, `{{job_title}}`, `{{company}}`). |
| **Email Logs Viewer** | `admin/emails.php` | Audit log of all outgoing and received automated emails with delivery status indicators. |
| **Newsletter Subscribers** | `admin/newsletter.php` | Subscriber list management, status toggles, unsubscribe handling, and CSV export. |
| **Admin User Management** | `admin/users.php` | Multi-role user administration (`Super Admin`, `Admin`, `HR Admin`, `Editor`), password resets, account locking. |
| **Audit & Activity Log** | `admin/activity-log.php` | Chronological log of admin actions (logins, status changes, deletions, exports) with IP tracking. |
| **Database Backup Utility** | `admin/backup_db.php` | One-click `.sql` database backup download tool for disaster recovery. |
| **Global Website Settings** | `admin/settings.php` | Web-based management of company contact details, social links, notifications, and metadata. |
| **Database Setup & Migrations** | `admin/setup_db.php` | Automated migration runner with self-healing schema creation for fresh deployments. |
| **CI/CD Webhook Deployer** | `admin/webhook_deploy.php` | Instant GitHub webhook deployment handler with background cURL extraction and FTPS support. |
| **Service Directory Audit** | `admin/audit_services.php` | Diagnostic tool verifying links, routes, and metadata across all 65+ service subpages. |

---

## 4. Database & Backend Infrastructure

- **Robust Connection Handler (`config/database.php`)**:
  - Primary connection to MySQL 8.0 / MariaDB on Hostinger (`u696371114_vortexsoftcom`).
  - Hierarchical `.env` file loader with multi-path scanning and line-by-line fallback parser.
  - Zero-config local development fallback to SQLite (`sqlite_dev.db`) when MySQL is unreachable locally.
  - Self-healing schema validation: automatically creates missing tables if a query detects missing schema objects.
- **Site-Wide Configuration (`config/constants.php`)**:
  - Dynamic host and protocol detection supporting dual domains (`.com` and `.in`).
  - Company stats standardized: **6+ years of experience**, 200+ projects, 150+ clients, 200+ team size.
  - Cleaned social channels (Facebook, Instagram, LinkedIn, WhatsApp; discontinued Twitter/X).
  - Head office anchored to **Pune, Maharashtra**, with delivery hubs in **Bengaluru** and **Wyoming, USA**.

---

## 5. Frontend & User Experience Upgrades

- **Unified Component Layout**:
  - Single central header (`includes/header.php`) and footer (`includes/footer.php`) shared across all root pages and service subfolders.
  - Asset version cache-busting (`?v=2.6`) on CSS and JavaScript files to prevent browser cache lag.
  - Fail-safe page preloader dismissal with a 150ms timeout script to prevent loading freeze on slow connections.
- **Ultra-Sharp 4K Visual Branding**:
  - High-resolution 4K transparent RGBA brand logos (`vortexsoft logo.png`, `logo-header.png`, `logo-footer-new.png`, `icon.jpg`).
- **Dynamic Service Directory (`service.php` & Subpages)**:
  - 65+ specialized service offerings categorized across 9 core industry pillars:
    1. Real Estate Title & Settlement Services
    2. Healthcare BPO & Revenue Cycle Management (RCM)
    3. Custom Software & Web Development
    4. AI Data Annotation & Training Data Solutions
    5. Digital Publishing, ePUB3 & Prepress Services
    6. Accounting, Bookkeeping & Financial BPO
    7. Logistics & Supply Chain Back-Office Operations
    8. Digital Marketing & Marketing Automation
    9. Manpower, Staffing & Payroll Management
- **Interactive Careers Experience (`careers.php`)**:
  - DB-driven live job listings with dynamic category and location filtering.
  - Interactive AJAX job application modal supporting optional resume upload (PDF/DOC/DOCX up to 5MB).
- **Dynamic Thought Leadership Blog (`blog.php`)**:
  - Live article rendering from the database with category filtering, reading time estimates, and view counts.
- **Legal Compliance Pages**:
  - `privacy.php`: ISO 27001:2013 and HIPAA compliance data protection disclosures.
  - `terms.php`: Comprehensive terms of service for enterprise B2B engagements.
- **Custom 404 Error Handler (`404.php`)**:
  - Informative error landing page with quick navigation links and search redirect.

---

## 6. Proprietary Product Platforms Showcase

The website prominently showcases Vortexsoft’s suite of proprietary platforms:

1. **VortexEXHO**: Enterprise Workforce Operating System combining ATS, HRMS, Payroll, LMS, and AI HR Copilot.
2. **vortexHire**: AI-powered candidate resume screening, skill matching, and pre-qualification engine.
3. **vortexKonnect**: Call center speech analytics, sentiment detection, and automated QA scoring platform.
4. **Vortexreach**: AI B2B lead generation, multi-channel outreach, and pipeline acceleration platform.
5. **vortexsoftpublishing**: Proprietary digital publishing automation system for XML, ePUB3, and PDF accessibility remediation (Section 508 / WCAG 2.1).

---

## 7. REST APIs & Form Processing Endpoints

### 1. Contact Form API (`POST /api/contact.php`)
- **Inputs**: `fullName`, `emailAddr`, `phone`, `service`, `company`, `msgText`, `csrf_token`, `website` (honeypot).
- **Processing**:
  - Anti-spam honeypot verification.
  - Rate limiting (max 5 requests per 5 minutes per IP).
  - Data sanitization and database logging into `contact_inquiries`.
  - Notification dispatch to support inbox and automated confirmation email to the user.

### 2. Job Application API (`POST /api/apply.php`)
- **Inputs**: `applicant_name`, `email`, `phone`, `job_title`, `experience_years`, `notice_period`, `expected_ctc`, `resume` (file), `cover_letter`.
- **Processing**:
  - MIME-type and extension validation for uploaded resumes (`.pdf`, `.doc`, `.docx`, max 5MB).
  - Secure randomized file storage in `/uploads/resumes/` with `.htaccess` execution prevention.
  - Database record creation in `job_applications`.
  - HR email dispatch and applicant auto-acknowledgement.

### 3. Newsletter Subscription API (`POST /api/newsletter.php`)
- **Inputs**: `email`, `csrf_token`.
- **Processing**: Email format validation, duplicate prevention, and logging into `newsletter_subscribers`.

---

## 8. Security Hardening & Access Control

- **HTTP Security Headers (`.htaccess` & `includes/header.php`)**:
  - `X-Frame-Options: SAMEORIGIN` (Clickjacking prevention).
  - `X-Content-Type-Options: nosniff` (MIME-sniffing prevention).
  - `X-XSS-Protection: 1; mode=block` (Cross-site scripting filter).
  - `Referrer-Policy: strict-origin-when-cross-origin`.
  - `Strict-Transport-Security: max-age=31536000; includeSubDomains; preload`.
- **SQL Injection Prevention**:
  - 100% of database queries execute through PDO prepared statements with parameterized inputs.
- **Uploads Directory Lockdown (`uploads/.htaccess`)**:
  - PHP execution disabled inside upload folders (`php_flag engine off`, `SetHandler none`).
- **Authentication Security**:
  - Session hijacking protection with IP and User-Agent fingerprint validation.
  - Strong password hashing using PHP `password_hash()` with Bcrypt algorithm.
  - Rate-limited login endpoints preventing brute-force dictionary attacks.

---

## 9. SEO, AEO & GEO Search Optimization

- **Search Engine Optimization (SEO)**:
  - Concise page titles (<60 characters) and high-CTR meta descriptions (<160 characters).
  - Canonical link tags with multi-domain support (`.com` and `.in`).
  - Hreflang alternates (`en`, `en-IN`, `en-US`, `x-default`).
  - Full `sitemap.xml` index covering all service verticals and static pages.
- **Answer Engine Optimization (AEO)**:
  - 14 fact-first Q&A answer blocks optimized for direct citation in Google AI Overviews, Perplexity AI, Claude, and ChatGPT search.
  - Schema.org `@type: "FAQPage"` JSON-LD markup embedded on key landing pages.
- **Generative Engine & Geographic Optimization (GEO)**:
  - Reusable `render_geo_fact_block()` providing machine-readable brand entity definitions.
  - Schema.org `@type: "Organization"` with complete `sameAs` social authority links.
  - Geographic coordinates embedded (`geo.region: IN-MH`, `geo.position: Pune, Maharashtra`).
- **AI Crawlers Permission Matrix (`robots.txt`)**:
  - Explicit allow rules for major search and AI crawlers (`Googlebot`, `Bingbot`, `GPTBot`, `PerplexityBot`, `ClaudeBot`, `Applebot`).

---

## 10. Recent Commit History & Changelog

| Commit Hash | Author & Date | Commit Message / Feature Description |
|---|---|---|
| `15a14ac` | Aniket (2026-08-15) | **[SECURITY]**: Removed temporary DB diagnostic script after verifying live MySQL connection. |
| `cd7be14` | Aniket (2026-08-15) | **[FIX]**: Database connection with robust `.env` loader, Hostinger MySQL defaults, and table auto-init. |
| `be9ec9e` | Aniket (2026-08-14) | **[REVERT]**: Restored streamlined username/password login with database + fallback authentication. |
| `c8b41f4` | Aniket (2026-08-14) | **[FIX]**: Resolved 500 error on login credentials validation block. |
| `212f7cd` | Aniket (2026-08-14) | **[FIX]**: Admin login DB fallback auth with graceful fallback handling. |
| `baceef9` | Aniket (2026-08-14) | **[FEAT]**: Admin login security upgrades and attempt tracking. |
| `2ae0a14` | Aniket (2026-08-14) | **[FIX]**: Set Pune as primary Headquarters across site, prompts, and metadata; Bengaluru as delivery hub. |
| `a266838` | Aniket (2026-08-13) | **[FEAT]**: Final AI Blog Generator & 3-provider integration verified with SQLite fallback. |
| `07fcc72` | Aniket (2026-08-13) | **[FIX]**: Set 180s max execution time for multi-provider AI generation. |
| `9ef9092` | Aniket (2026-08-13) | **[FIX]**: Added SSL verification fallback in cURL for local dev compatibility. |
| `a572bab` | Aniket (2026-08-13) | **[FIX]**: Optimized Gemini payload for robust JSON extraction. |
| `665d7e4` | Aniket (2026-08-13) | **[FIX]**: Resilient JSON parser + 4096 max tokens for AI blog generator. |
| `ef42955` | Aniket (2026-08-13) | **[FIX]**: Updated Gemini model target to `gemini-3.6-flash`. |
| `e28f1b9` | Aniket (2026-08-13) | **[FIX]**: Updated default AI models to active versions (`gemini-2.5-flash` & `llama-3.3-70b-instruct`). |
| `f6773c5` | Aniket (2026-08-13) | **[FEAT]**: AI Blog Generator — Groq + Gemini + OpenRouter 3-way compare with admin review & approval. |
| `5e722ea` | Aniket (2026-08-13) | **[DOCS]**: Complete 9-document architecture and engineering audit in `/docs`. |
| `5a1ad23` | Aniket (2026-08-13) | **[FEAT]**: Dual domain support (`.com` & `.in`) and Terms of Service page. |
| `7310736` | Aniket (2026-08-11) | **[FIX]**: Resolved all 25 audit report issues (titles <60 chars, rel attributes, logo dimensions, meta tags). |
| `82e806a` | Aniket (2026-08-11) | **[DOCS]**: Completed 100-point SEO/AEO/GEO optimization verification. |
| `e2e1c54` | Aniket (2026-08-11) | **[FEAT]**: SEO/AEO/GEO master optimization overhaul (JSON-LD schemas, AEO answers). |
| `811cfbf` | Aniket (2026-08-10) | **[STYLE]**: Enhanced brand logo to ultra-sharp 4K RGBA resolution (`3840x1389`). |
| `69b1d31` | Aniket (2026-08-10) | **[FEAT]**: Introduced `vortexsoftpublishing` proprietary publishing automation platform. |
| `019ea2c` | Aniket (2026-08-10) | **[FEAT]**: V2 Admin Suite — Email Accounts, Sent/Received Viewer, Connection Tester, Templates, Activity Log, Users, DB Backup. |
| `2f137f1` | Aniket (2026-08-10) | **[FEAT]**: Version 2 upgrades — Jobs CRUD, DB-driven careers, 6+ years experience, security headers. |

---

*Document compiled automatically by Antigravity AI assistant for Vortexsoft Innovations Pvt. Ltd.*
