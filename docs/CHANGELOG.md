# Application Changelog & Release Notes
**Vortexsoft Innovations Private Limited**

All notable improvements, security hardening updates, and bug fixes for the live web application are documented here.

---

## [Version 2.1.0] — 2026-10-27 (Production Hardening & Live Audit Release)

### Security Remediations
- **Locked Down `admin/setup_db.php`**: Protected database installer behind authenticated Super Admin session verification and secret installation key. Public anonymous requests now receive HTTP 403 Forbidden.
- **Removed Hardcoded Credentials in `admin/login.php`**: Eliminated static cleartext fallback credentials array. Enforced strict database verification with password hashing.
- **Prevented Password Resets on Setup**: Modified `setup_db.php` so it seeds initial administrator credentials only when `admin_users` table has 0 records, never overwriting existing credentials.
- **CSRF Protection on Record Deletions**: Enforced session CSRF token validation on single item deletions in `admin/applications.php` and `admin/contacts.php`.
- **MIME Validation for File Uploads**: Upgraded `api/apply.php` to validate real MIME types via `finfo` / `mime_content_type` against an allowed whitelist.
- **Database Error Sanitization**: Replaced raw database error output (`$e->getMessage()`) in `api/apply.php` with sanitized customer-facing messages.
- **Dynamic Deploy Token Sourcing**: Updated `admin/webhook_deploy.php` to load deploy tokens dynamically from `config/.env`.
- **Protected Diagnostic Utilities**: Secured `db_health_check.php` behind super_admin auth and added it to deployment exclude patterns.

### UI / UX & Responsive Enhancements
- **Touch-First Mobile Navigation**: Redesigned mobile navbar header (`width: calc(100% - 14px)`, `min-height: 60px`, `flex-wrap: wrap`) with 44×44px touch targets.
- **Mobile Quote CTA**: Embedded a prominent Request a Quote call-to-action button directly into the mobile drawer.
- **Fixed Horizontal Overflow**: Removed inline `style="min-width: 760px"` causing mobile viewport blowout on services mega-menu; replaced with responsive CSS grid.
- **iOS Safari Input Optimization**: Set minimum 16px font size on mobile form inputs to prevent automatic iOS Safari viewport zoom.
- **Fluid Typography**: Implemented CSS `clamp()` tokens in `usability-audit-fixes.css` for balanced typography across phones, tablets, and desktops.

### Marketing & Brand Improvements
- **Purged Public Salary Badges**: Removed stipend/package badges from 15 job cards in `careers.php`.
- **Updated Structured Data**: Stripped `baseSalary` property from JSON-LD Schema.org scripts.
- **Synchronized AI Discovery Feeds**: Cleaned `llms.txt` and `llms-full.txt` to remove salary disclosures while maintaining verified corporate citations.
- **Resolved Dangling Function Reference**: Removed leftover `getJobSalaryData()` reference in `careers.php`.

### Deployment & Tooling
- **Updated FTP Exclusions**: Added `^db_health_check\.php$` and `^scratch/` to `deploy-ftp.ps1` to prevent development artifacts from uploading to Hostinger.
- **Automated Endpoint Probing**: Created `scratch/probe_endpoints.ps1` for real-time uptime and latency benchmarking.
