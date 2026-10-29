# Issue Register & Defect Tracking Log
**Vortexsoft Innovations Private Limited**  
*Document Version: 2.1.0 — Post-Remediation Status*  
*Date of Audit: October 27, 2026*  
*Status Definitions: FIXED AND VERIFIED | FIXED BUT NOT FULLY VERIFIED | IDENTIFIED — NOT FIXED | BLOCKED — REQUIRES ACCESS | REQUIRES APPROVAL | NOT APPLICABLE*

---

## Master Issue Tracking Matrix

| Issue ID | Severity | Category | Description | Status | Evidence / Verification Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **ISSUE-01** | High | Marketing / SEO | Job cards displayed public packages/stipends (unprofessional appearance). | **FIXED AND VERIFIED** | All 15 job cards updated, JSON-LD schema stripped, FAQs updated, `llms.txt` cleaned, verified live on production. |
| **ISSUE-02** | High | UI / UX | Mobile and tablet responsiveness issues (navbar overflow, small touch targets, auto-zoom on iOS). | **FIXED AND VERIFIED** | Implemented responsive drawer, `calc(100% - 14px)` fluid bar, 16px input rule, touch-scrollable category pills in `usability-audit-fixes.css`. |
| **ISSUE-03** | Critical | Cybersecurity | `admin/setup_db.php` was accessible to unauthenticated visitors and reset admin passwords. | **FIXED AND VERIFIED** | Super Admin authentication + key verification added. Live HTTP probe verified: returns HTTP 403 Forbidden to public internet. |
| **ISSUE-04** | Critical | Cybersecurity | Hardcoded plain-text passwords and fallback admin accounts in `admin/login.php` (CWE-798). | **FIXED AND VERIFIED** | Purged hardcoded plaintext fallback array. Strict database password verification enforced; safe error message returned on DB interruption. |
| **ISSUE-05** | High | Cybersecurity | Single item deletion in `admin/applications.php` and `admin/contacts.php` triggered via `GET` request without CSRF verification (CWE-352). | **FIXED AND VERIFIED** | Enforced CSRF token verification `hash_equals($_SESSION['csrf_token'], $token)` on delete handlers and appended token to all admin UI delete triggers. |
| **ISSUE-06** | High | Cybersecurity | File upload in `api/apply.php` validated only file extension (no MIME check) and leaked raw DB exception strings on error (CWE-209). | **FIXED AND VERIFIED** | Added `finfo` / MIME whitelist verification (`application/pdf`, `msword`, `docx`). Sanitized error output to prevent database detail disclosure. |
| **ISSUE-07** | High | Cybersecurity | `admin/webhook_deploy.php` used hardcoded token in source code. | **FIXED AND VERIFIED** | Updated to dynamically load `DEPLOY_TOKEN` from `config/.env` with hash_equals verification. |
| **ISSUE-08** | Medium | Cybersecurity | Hardcoded database password fallback in `api/apply.php` and unauthenticated `db_health_check.php`. | **FIXED AND VERIFIED** | Removed hardcoded fallback from `api/apply.php`; locked `db_health_check.php` behind super_admin auth and added it to deployment exclude list. |
| **ISSUE-09** | Medium | Code Quality | Dangling `getJobSalaryData()` call in `careers.php`. | **FIXED AND VERIFIED** | Removed from `careers.php` line 1241, verified live without PHP fatal error. |
| **ISSUE-10** | Medium | Functionality | Job application bulk update error ("Invalid Status: The status chosen for the bulk update is not recognized"). | **FIXED AND VERIFIED** | Verified valid status enum in `admin/applications.php` (`new`, `reviewed`, `shortlisted`, `interview`, `offered`, `rejected`, `withdrawn`). |
| **ISSUE-11** | Low | Performance | GZIP compression and browser cache expirations for static assets. | **FIXED AND VERIFIED** | Verified `.htaccess` `mod_deflate` and `mod_expires` active on Hostinger live server. |
| **ISSUE-12** | Low | SEO / AEO | AI search engine optimization for Perplexity, ChatGPT, Gemini, and Claude. | **FIXED AND VERIFIED** | Verified active `llms.txt`, `llms-full.txt`, `geo-sitemap.xml`, and GeoFact Schema blocks across key landing pages. |

---

## Verification Summary
- **Total Defects Tracked**: 12
- **Fixed & Verified**: 12 (100%)
- **Remaining Open Defects**: 0
