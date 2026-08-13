# 03 — Security & Access Document
**Vortexsoft Innovations Pvt. Ltd.**
*Confirmed from codebase — August 2026*

---

## Authentication

### Admin Authentication
- **Method**: Username/Email + Password → Session
- **Password Storage**: Bcrypt (`cost=10–12` depending on context)
- **Session Settings**: `httpOnly`, `Secure`, `SameSite=Lax`, `strict_mode=1` — CONFIRMED FROM CODE
- **Brute-Force Protection**: File-based lockout in PHP temp dir — 5 failed attempts → 15-minute IP lockout — CONFIRMED
- **CSRF Protection**: Login form has dedicated `login_csrf` token (separate from general CSRF)
- **Session Fixation**: `session_regenerate_id(true)` called on successful login — CONFIRMED
- **Password Reset**: ❌ NOT IMPLEMENTED
- **Logout**: Session destruction via `admin/logout.php`
- **Session Timeout**: ❌ NOT IMPLEMENTED — sessions persist indefinitely until logout or server restart

### Public Forms
- **CSRF**: `csrf_token()` function generates 64-char hex token stored in `$_SESSION['csrf_token']`
- **Verification**: `verify_csrf()` + `hash_equals()` timing-safe comparison
- **Honeypot**: `website_hp` hidden field on contact and apply forms

---

## Authorization — Permission Matrix

| Role | View Admin | Manage Contacts | Manage Applications | Manage Blog | System Settings |
|---|---|---|---|---|---|
| `super_admin` | ✅ | ✅ | ✅ | ✅ | ✅ |
| `admin` | ✅ | ✅ | ✅ | ✅ | ❌ |
| `viewer` | ✅ | ✅ (read-only REQUIRES VERIFICATION) | ✅ (read-only REQUIRES VERIFICATION) | ❌ REQUIRES VERIFICATION | ❌ |
| Public | ❌ | ❌ | ❌ | ❌ | ❌ |

> ⚠️ `viewer` role is defined in the schema and `admin_require_role()` function, but no admin pages currently call `admin_require_role()` to enforce granular access. All admin pages only call `admin_check()` which verifies session exists — NOT role. **RBAC is defined but not enforced.** — SECURITY RISK.

---

## Web Security (What is Confirmed Present)

| Control | Status | Details |
|---|---|---|
| HTTPS Force Redirect | ✅ ACTIVE | `.htaccess` RewriteRule |
| HSTS Header | ✅ ACTIVE | `max-age=31536000; includeSubDomains; preload` |
| X-Content-Type-Options | ✅ ACTIVE | `nosniff` |
| X-Frame-Options | ✅ ACTIVE | `SAMEORIGIN` |
| X-XSS-Protection | ✅ ACTIVE | `1; mode=block` (legacy) |
| Content-Security-Policy | ✅ ACTIVE | Restricts scripts to self + CDN whitelist |
| Referrer-Policy | ✅ ACTIVE | `strict-origin-when-cross-origin` |
| Directory Listing | ✅ BLOCKED | `Options -Indexes` in `.htaccess` |
| PHP Version Disclosure | ✅ HIDDEN | `Header unset X-Powered-By` |
| Sensitive Directories | ✅ BLOCKED | `/config/`, `/includes/`, `/database/`, `/backups/`, `/uploads/resumes/` |
| SQL Injection | ✅ PROTECTED | PDO prepared statements throughout |
| XSS in Output | ✅ PROTECTED | `htmlspecialchars()` used consistently |
| Error Display (Prod) | ✅ OFF | `display_errors=0` in production |
| .env File Access | ✅ BLOCKED | `.htaccess` FilesMatch rule |
| .sql/.log/.db File Access | ✅ BLOCKED | `.htaccess` FilesMatch rule |
| CSRF (Forms) | ✅ ACTIVE | Token-based with `hash_equals()` |
| Rate Limiting | ✅ ACTIVE | Session-based per-IP |
| Honeypot (Spam) | ✅ ACTIVE | `website_hp` field |
| Admin CSRF | ✅ ACTIVE | Separate `login_csrf` token |
| Brute Force Protection | ✅ ACTIVE | File-based, 15-min lockout |
| Session Fixation | ✅ PROTECTED | `session_regenerate_id(true)` |

---

## 🚨 Security Issues Found (Tickets Required)

### SEC-001 — CRITICAL: Hardcoded Deploy Token in Source Code
- **File**: `admin/webhook_deploy.php` line 2
- **Issue**: `define('DEPLOY_TOKEN', 'VortexDeploy6498286f401141b8');` — token is committed to Git repo
- **Risk**: Anyone with repo access or leaked repo can trigger a full production deploy overwrite
- **Fix**: Move to `.env` file using `$_env` pattern like `database.php`

### SEC-002 — HIGH: Admin Passwords Hardcoded in Source (Auto-Install)
- **File**: `admin/login.php` lines 173–174
- **Issue**: Plaintext passwords `'Mrunal@9996'` and `'ShivaG@1437'` present in source code (inside `auto_install_tables()`)
- **Risk**: Anyone reading the source code sees default admin passwords
- **Fix**: Remove hardcoded passwords; use `generate_hash.php` flow or admin setup wizard

### SEC-003 — HIGH: generate_hash.php Exists in Production Path
- **File**: `admin/generate_hash.php`
- **Issue**: Per README, this tool must be deleted after use. If accessible in production, anyone can visit it.
- **Status**: REQUIRES VERIFICATION — confirm if this file is deleted on the live server

### SEC-004 — HIGH: RBAC Not Enforced on Admin Pages
- **Files**: All `admin/*.php`
- **Issue**: `admin_require_role()` function exists but no admin pages call it. All pages only check `admin_check()` (session exists). Both `super_admin` and `admin` roles have identical access.
- **Fix**: Add role checks to sensitive operations

### SEC-005 — MEDIUM: Session Timeout Not Set
- **Files**: All `admin/*.php`
- **Issue**: No session timeout or idle timeout is configured. Admin sessions persist until logout.
- **Fix**: Implement 30–60 minute session timeout check on each admin page load

### SEC-006 — MEDIUM: Delete Without CSRF Verification (Applications)
- **File**: `admin/applications.php` line 21–25
- **Issue**: Delete action triggered via `GET ?delete=ID`. GET requests should never perform state changes. No CSRF protection on this path.
- **Fix**: Change to POST with CSRF token

### SEC-007 — MEDIUM: CSP Uses `unsafe-inline` and `unsafe-eval`
- **File**: `.htaccess` line 64
- **Issue**: Content-Security-Policy allows `unsafe-inline` and `unsafe-eval` for scripts
- **Risk**: Weakens XSS protection
- **Fix**: Refactor inline scripts to external files; remove `unsafe-inline`/`unsafe-eval`

### SEC-008 — MEDIUM: Resume Path in Email Uses `UPLOADS_URL` (Publicly Accessible URL)
- **File**: `api/apply.php` line 83
- **Issue**: `$resume_path = UPLOADS_URL . '/resumes/' . $resume_filename;` and `.htaccess` blocks `/uploads/resumes/` from browsers, but the full URL is stored in DB and sent in HR email as a clickable link. If .htaccess is misconfigured, resumes could be directly accessible.
- **Status**: Partially mitigated by `.htaccess` protection. REQUIRES VERIFICATION on live server.

### SEC-009 — LOW: `SSL_VERIFYPEER=false` in Webhook Deploy
- **File**: `admin/webhook_deploy.php` lines 32–33
- **Issue**: SSL peer verification disabled in cURL download of GitHub ZIP
- **Fix**: Enable SSL verification

### SEC-010 — LOW: IP Spoofing via X-Forwarded-For
- **File**: `includes/functions.php` lines 330–334
- **Issue**: `get_client_ip()` trusts `HTTP_X_FORWARDED_FOR` without validation against trusted proxies. An attacker could spoof their IP to bypass rate limiting.
- **Fix**: Only trust proxy headers if the request comes from a known reverse proxy

---

## Input Validation (Confirmed Present)

| Input | Sanitization | Validation |
|---|---|---|
| Contact Name | `sanitize()` — `htmlspecialchars + strip_tags + trim` | Length ≤120 |
| Contact Email | `sanitize_email()` | `filter_var FILTER_VALIDATE_EMAIL` |
| Contact Phone | `sanitize()` | Regex `[+0-9\-\s\(\)]{7,20}` |
| Contact Message | `sanitize()` | Min 10 chars, max 5000 chars |
| Resume File | Extension whitelist + MIME check | Max 5MB |
| Admin Login | `trim()` | — |
| Blog Slug | `slugify()` | Regex `[a-z0-9\s-]` |

---

## Error Handling (Confirmed Behavior)

| Scenario | Behavior |
|---|---|
| Invalid login | Error displayed on page, failed attempt recorded |
| Brute force lockout | Error with lockout message shown |
| CSRF failure (contact) | JSON `{success: false, message: "Security validation failed"}` |
| DB failure (contact) | Email still sends; inquiry_id returns 0 |
| DB failure (application) | Error logged; process continues |
| Resume upload failure | JSON error returned |
| Invalid email | JSON validation error |
| Rate limit exceeded | JSON `{success: false, message: "Too many requests"}` |
| 404 not found | Custom `404.php` via `.htaccess ErrorDocument` |
| Admin DB unavailable | Login shows "Database unavailable" message |
