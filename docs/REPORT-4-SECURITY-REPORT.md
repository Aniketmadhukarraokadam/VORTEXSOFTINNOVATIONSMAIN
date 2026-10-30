# REPORT 4 – Security Remediation & Hardening Report

**Organization:** Vortexsoft Innovations Private Limited  
**Website:** https://www.vortexsoftinnovations.com/  
**Repository:** Aniketmadhukarraokadam/VORTEXSOFTINNOVATIONSMAIN  
**Audit Branch:** `audit-redesign-opt`  
**Date:** October 30, 2026  
**Auditor:** Senior Cybersecurity & Full-Stack Engineer  

> [!IMPORTANT]
> **Strict Security Notice:** In accordance with security protocol, no actual passwords, API tokens, database keys, or FTP credentials appear in this report. All sensitive references are abstracted into standardized configuration tokens.

---

## 1. Vulnerabilities Discovered & Remediation Completed

### SEC-01: Committed Production Credentials in Tracked Files
- **Threat Vector:** Hardcoded database passwords and deployment tokens in `config/database.php`, `admin/setup_db.php`, `admin/webhook_deploy.php`, and `deploy-ftp.ps1`.
- **Impact:** Risk of unauthorized database connection, credential harvesting from git history, or deployment interception.
- **Remediation Completed:**
  1. Removed all hardcoded credentials from source code.
  2. Implemented mandatory reading from `config/.env` or system environment variables (`getenv()`, `$_ENV`).
  3. Configured `admin/webhook_deploy.php` to fail safely (HTTP 503) if `DEPLOY_TOKEN` is unset.
  4. Created comprehensive `config/.env.example` with configuration placeholders.
  5. Verified `.gitignore` prevents tracking of live `.env` and local credential files.

### SEC-02: Plaintext Credentials in Documentation & Notes
- **Threat Vector:** Committed documentation files (`website_info.txt`, `docs/03-security-access.md`, `docs/07-tickets-backlog.md`, `README.md`, `docs/Migrating Website To PHP(1).md`) contained connection credentials.
- **Impact:** Public or internal exposure of operational infrastructure secrets.
- **Remediation Completed:**
  1. Replaced all occurrences with `[CONFIGURED_IN_ENV]`.
  2. Added `.htaccess` rule blocking direct HTTP requests to `website_info.txt` with HTTP 403 Forbidden.

### SEC-03: Insecure Resume File Uploads & Directory Access
- **Threat Vector:** In `api/apply.php`, uploaded candidate resumes were only checked by file extension. Malicious actors could rename PHP shells or executables to `.pdf` or `.docx` and upload them.
- **Impact:** Potential Remote Code Execution (RCE) or server compromise.
- **Remediation Completed:**
  1. **Magic-Byte Signature Inspection:** Implemented server-side binary header verification in PHP:
     - PDF: Must begin with `%PDF-` (`0x25 0x50 0x44 0x46 0x2D`).
     - DOCX: Must begin with Zip PK header `PK\x03\x04` (`0x50 0x4B 0x03 0x04`).
     - DOC: Must begin with Compound Document header `\xD0\xCF\x11\xE0` (`0xD0 0xCF 0x11 0xE0`).
  2. **Filename Randomization:** Resumes are hashed with cryptographic tokens and stored outside user control.
  3. **Web Server Denial:** Configured `uploads/resumes/.htaccess` with `Require all denied` and `Deny from all`, preventing direct script execution or download by unauthorized third parties.

### SEC-04: Cross-Site Scripting (XSS) & CSRF Protection
- **Remediation Completed:**
  1. All user-supplied inputs in `api/contact.php` and `api/apply.php` are sanitized with `htmlspecialchars()` before storage and display.
  2. State-changing POST requests require a valid session CSRF token via `csrf_token()` and `verify_csrf_token()`.
  3. Honeypot anti-spam fields (`website_hp`) detect and silently discard automated bots.

### SEC-05: SQL Injection Prevention
- **Remediation Completed:**
  1. Database queries throughout the application utilize PDO prepared statements with parameterized inputs.
  2. Disabled SQL string interpolation in all authentication, contact, and job application flows.

---

## 2. Server-Level Protection & Security Headers

The root `.htaccess` file was updated with enterprise-grade defensive headers:
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: SAMEORIGIN`
- `X-XSS-Protection: 1; mode=block`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Strict-Transport-Security: max-age=31536000; includeSubDomains; preload`
- `Content-Security-Policy`: Strict policy restricting script, style, font, and frame origins.
- Directory Browsing Disabled: `Options -Indexes -MultiViews`
- Sensitive Directory Access Blocked: `config/`, `includes/`, `database/`, `backups/`, `scratch/`, and `uploads/resumes/` are inaccessible via direct URL requests.

---

## 3. Credential Rotation Checklist for Website Owner

Because legacy credentials existed in historical commits prior to this audit, the website owner should execute this rotation checklist in Hostinger hPanel and Git:

1. **MySQL Database Password:**
   - Log into Hostinger hPanel > **Databases** > **MySQL Databases**.
   - Change the password for the database user.
   - Update `DB_PASS` in your live server's `config/.env` file.
2. **Hostinger FTP / SFTP Password:**
   - Go to hPanel > **Files** > **FTP Accounts**.
   - Generate a new secure password for the deployment account.
   - Update your local deployment secrets (or `$env:FTP_PASS`) accordingly.
3. **Admin Panel Administrator Passwords:**
   - Log into `https://www.vortexsoftinnovations.com/admin/login.php`.
   - Update the administrator passwords in the admin user management module.
4. **Deploy Webhook Token:**
   - Generate a new 64-character random alphanumeric string for `DEPLOY_TOKEN`.
   - Update `DEPLOY_TOKEN` in `config/.env` on the server and update your GitHub repository webhook settings.
5. **Git History Hygiene:**
   - If public distribution of the repository is planned, coordinate a Git history scrub (e.g., using `git-filter-repo` or BFG) during a scheduled maintenance window.
