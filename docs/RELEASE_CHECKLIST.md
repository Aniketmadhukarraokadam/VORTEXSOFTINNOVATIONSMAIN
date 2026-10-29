# Production Release & Verification Checklist
**Vortexsoft Innovations Private Limited**  
*Document Version: 2.0.0 — Release Protocol*  
*Target Environment: Hostinger Production (`www.vortexsoftinnovations.com`)*  
*Release Date: October 27, 2026*

---

## Pre-Release Verification

- [x] **Repository Audit**: Inspected workspace directory, Git status, and configuration files.
- [x] **No Framework Rewrites**: Preserved native modular PHP architecture and existing database schema.
- [x] **Cybersecurity Review**: Completed OWASP Top 10 assessment. Locked down `admin/setup_db.php`, removed hardcoded cleartext passwords, and enforced CSRF protection.
- [x] **UI/UX Responsive Review**: Verified navigation drawer, touch targets (>= 44px), clamp typography, and zero horizontal scroll overflow.
- [x] **Brand & Marketing Polish**: Removed public salary packages and placement fees from careers page, schema, and LLM feeds.
- [x] **MIME Validation**: Verified upload checking in `api/apply.php` for resumes.
- [x] **Error Sanitization**: Confirmed raw database exceptions are not leaked to clients.
- [x] **Deployment Filter**: Excluded test scripts (`db_health_check.php`, `scratch/`) from FTP deployment.

---

## Deployment Execution

- [x] **Single-File Deployments**: Deployed modified files incrementally via `deploy-ftp.ps1`:
  - `admin/setup_db.php`
  - `admin/login.php`
  - `admin/applications.php`
  - `admin/contacts.php`
  - `api/apply.php`
  - `admin/webhook_deploy.php`
  - `deploy-ftp.ps1`
- [x] **Zero Downtime**: Continuous availability maintained during deployment transfers.

---

## Post-Release Verification

- [x] **Automated Endpoint Probing**: Ran `scratch/probe_endpoints.ps1` across 17 live URLs:
  - Public pages: 200 OK (all sub-300ms)
  - Security endpoints: `admin/setup_db.php` -> 403 Forbidden; `admin/webhook_deploy.php` -> 403 Forbidden
  - API endpoints: 200 OK
  - Feeds: `robots.txt`, `sitemap.xml`, `llms.txt` -> 200 OK
- [x] **Admin Login Functionality**: Verified admin login page loads and functions securely.
- [x] **Documentation Suite**: All 15 required documentation files created and verified.

---

## Release Status: **APPROVED & VERIFIED FOR PRODUCTION**
