# REPORT 2 – Changes Implemented

**Organization:** Vortexsoft Innovations Private Limited  
**Website:** https://www.vortexsoftinnovations.com/  
**Repository:** Aniketmadhukarraokadam/VORTEXSOFTINNOVATIONSMAIN  
**Audit Branch:** `audit-redesign-opt`  
**Date:** October 30, 2026  

---

## 1. Summary of Changes

Every file modification was performed in a non-destructive manner to safeguard existing database schemas, admin authentication, applicant resume tracking, inquiry logging, and subpage SEO rankings.

---

## 2. Comprehensive Inventory of Modified Files

### A. Security Configuration & Infrastructure
1. `config/database.php`
   - **Changes:** Removed hardcoded production database password fallback; sanitized default credential seeding in `ensureDatabaseTablesExist()`.
   - **Reason:** Eliminate committed credentials in Git and enforce reading from `config/.env` or system environment variables.
   - **Features Affected:** Database connection initialization, table auto-creation.
2. `admin/setup_db.php`
   - **Changes:** Removed hardcoded setup keys, fallback database passwords, and plaintext admin credentials from the completion banner.
   - **Reason:** Prevent unauthorized database manipulation and plaintext credential exposure in the browser.
   - **Features Affected:** One-time database setup script.
3. `admin/webhook_deploy.php`
   - **Changes:** Removed fallback deploy secret token; configured script to halt with HTTP 503 if `DEPLOY_TOKEN` is unset in `.env`.
   - **Reason:** Prevent unauthenticated arbitrary code pulling or git checkout triggers.
   - **Features Affected:** Automated git deployment webhook.
4. `deploy-ftp.ps1`
   - **Changes:** Sanitized plaintext FTP credentials; required `$env:FTP_PASS` or `.env` configuration, exiting with an error prompt if missing.
   - **Reason:** Prevent FTP host and password exposure.
   - **Features Affected:** Developer FTP synchronization script.
5. `config/.env.example`
   - **Changes:** Added complete placeholder template covering DB, SMTP, Admin default password, Setup key, FTP, and Deploy token.
   - **Reason:** Provide clear configuration guidelines for production deployment without storing actual secrets.
   - **Features Affected:** Environment setup.
6. `database/setup.sql`
   - **Changes:** Commented out hardcoded admin bcrypt inserts in `REPLACE INTO admin_users`.
   - **Reason:** Prevent accidental overwriting of production admin credentials upon SQL import.
   - **Features Affected:** Database schema initialization.
7. `.htaccess`
   - **Changes:** Added 403 Forbidden rules for `website_info.txt`, `scratch/`, and sensitive file types (`.env`, `.sql`, `.log`, `.conf`). Added 301 canonical rewrite for `data-annotation-servic/` to `/data-annotation-services/`.
   - **Reason:** Prevent direct web access to sensitive text/data files; consolidate duplicate URLs.
   - **Features Affected:** Web server security and URL routing.
8. `api/apply.php`
   - **Changes:** Implemented binary magic-byte inspection (`%PDF-`, `PK\x03\x04`, `\xD0\xCF\x11\xE0`) alongside MIME and extension checks for uploaded resumes.
   - **Reason:** Prevent disguised executable uploads and enforce defense-in-depth on candidate file intake.
   - **Features Affected:** Careers application pipeline and resume uploads.

### B. Documentation Sanitization
9. `website_info.txt`
   - **Changes:** Replaced committed production passwords (database, admin, FTP) with `[CONFIGURED_IN_ENV]`.
   - **Reason:** Prevent secret leaks in repository files.
   - **Features Affected:** Corporate server notes.
10. `README.md`
   - **Changes:** Replaced example passwords with generic placeholders.
   - **Reason:** Documentation security best practices.
   - **Features Affected:** Repository readme.
11. `docs/03-security-access.md` & `docs/07-tickets-backlog.md`
   - **Changes:** Redacted all legacy plaintext credentials and tokens.
   - **Reason:** Audit compliance and secrets elimination.
   - **Features Affected:** Architectural documentation.
12. `docs/Migrating Website To PHP(1).md`
   - **Changes:** Sanitized all occurrences of database, FTP, and webhook passwords.
   - **Reason:** Comprehensive repository cleanup.
   - **Features Affected:** Migration documentation.

### C. UI/UX Design System, Navigation & Structure
13. `assets/vortex-shared.css`
   - **Changes:** Standardized color tokens (`--primary-navy: #111827`, `--corporate-blue: #252B83`, `--brand-red: #E31E25`, `--bg-light: #F4F5F7`, `--text-primary: #111827`, `--text-secondary: #64748B`, `--border-color: #E5E7EB`).
   - **Reason:** Establish a consistent, premium corporate design system across all pages.
   - **Features Affected:** Global typography, buttons, cards, headers, footers.
14. `includes/header.php`
   - **Changes:** Restructured primary navbar into 8 core items: Home, About Us, Services, Products, Industries, Insights, Careers, Contact Us. Rebuilt Services mega-menu into 5 corporate divisions (Technology & AI, Healthcare, Publishing, Real Estate & Title, Business Operations) in a 5-column desktop grid with responsive mobile drawer.
   - **Reason:** Improve scannability, simplify navigation, and position company capabilities clearly.
   - **Features Affected:** Global header, navigation menu, mobile drawer.
15. `includes/footer.php`
   - **Changes:** Replaced fragmented footer with a standardized 5-column corporate layout: Brand & Credentials (ISO 9001/27001, HIPAA, Startup India), Company & Resources, Services, Verified Proprietary Products, and Contact & Global Offices (Pune HQ, Bengaluru, Sheridan USA).
   - **Reason:** Enhance corporate authority, improve SEO internal linking, and provide clear geographic credibility.
   - **Features Affected:** Global footer, newsletter signup, social media links.
16. `index.php`
   - **Changes:** Refined hero section with corporate headline *"Technology. Intelligence. Operational Excellence."*, polished supporting copy, streamlined primary CTAs (*"Explore Our Services"* and *"Partner With Us"*), and added dedicated Request Demo CTAs to proprietary product cards (`VortexEXHO`, `vortexHire`, `vortexKonnect`, `Vortexreach`, `vortexsoftpublishing`, `vortexsofthrms`).
   - **Reason:** Elevate homepage presentation to an international corporate tier and drive conversion.
   - **Features Affected:** Homepage hero, products showcase, CTA funnels.

### D. Service Subpage Cross-Links (52 Subpages)
17–68. `accounting-services/index.php` through `video-annotation-services/index.php` (52 files)
   - **Changes:** Normalized over 200 relative cross-links from `../<slug>/index.html` to `../<slug>/index.php`.
   - **Reason:** Eliminate unnecessary 301 redirects and ensure seamless internal navigation.
   - **Features Affected:** Subpage sidebars, related services menus, and cross-linking widgets.
69. `data-annotation-servic/index.php`
   - **Changes:** Added native PHP 301 header redirect to canonical `/data-annotation-services/`.
   - **Reason:** Prevent 404 errors or duplicate content from legacy typo URLs.
   - **Features Affected:** Legacy URL resolution.
