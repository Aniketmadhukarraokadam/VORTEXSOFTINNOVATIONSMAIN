# Production Security Maintenance Checklist
**Vortexsoft Innovations Private Limited**  
*Document Version: 2.0.0 — Continuous Security Checklist*  
*Scope: Production Infrastructure & Application Code*

---

## 1. Authentication & Access Control Checklist

- [x] All administration routes (`/admin/*`) enforce session authentication via `admin_check()`.
- [x] Super Admin restricted routes enforce RBAC checks via `admin_require_role(['super_admin'])`.
- [x] Sensitive database utilities (`setup_db.php`) blocked from anonymous public access (verified returns 403 Forbidden).
- [x] Password storage strictly uses `password_hash()` with BCRYPT and standard cost factor.
- [x] No plain-text passwords or fallback credentials in application source code.
- [x] Brute-force protection active on admin login (locks out after 5 consecutive failed attempts).
- [x] Session cookies configured with `HttpOnly`, `SameSite=Lax`, and `Secure` attributes over HTTPS.

---

## 2. Injection & Data Validation Checklist

- [x] All database interactions execute via parameterized PDO prepared statements (`execute([':key' => $val])`).
- [x] User inputs passed through `sanitize()` (`htmlspecialchars(strip_tags(trim(...)))`).
- [x] Email inputs validated through `filter_var(..., FILTER_VALIDATE_EMAIL)`.
- [x] Phone inputs validated against international phone regex patterns.
- [x] No `eval()` or dangerous code evaluation functions in production runtime.

---

## 3. Upload & File System Security Checklist

- [x] Resumes uploaded to `uploads/resumes/` are checked for both file extension and real MIME type (`finfo`).
- [x] Maximum file size enforced at 5MB per upload.
- [x] `.htaccess` in `uploads/resumes/` denies execution of PHP, CGI, and executable scripts.
- [x] Public web root `.htaccess` denies direct browser access to `/uploads/resumes/.*`.
- [x] Download handler (`admin/download.php`) uses `basename()` and requires admin session to prevent path traversal attacks.

---

## 4. CSRF & State Mutation Checklist

- [x] All state-changing POST forms generate and verify session-based CSRF tokens (`verify_csrf()`).
- [x] Single-item deletions in admin panels require CSRF validation (`hash_equals($_SESSION['csrf_token'], $token)`).
- [x] Asynchronous API endpoints (`api/apply.php`, `api/contact.php`) enforce origin and method verification.

---

## 5. Web Server & Transport Security Checklist

- [x] HTTPS enforced via 301 permanent redirect in `.htaccess`.
- [x] Canonical domain (`www.vortexsoftinnovations.com`) enforced via 301 permanent redirect.
- [x] HTTP Strict Transport Security (HSTS) header present with `includeSubDomains` and `preload`.
- [x] `X-Content-Type-Options: nosniff` header enabled.
- [x] `X-Frame-Options: SAMEORIGIN` header enabled.
- [x] `X-XSS-Protection: 1; mode=block` header enabled.
- [x] Server signature and PHP version headers unset (`ServerSignature Off`, `Header unset X-Powered-By`).
- [x] Directory listing disabled globally (`Options -Indexes`).
- [x] Direct HTTP access denied to `.env`, `config/`, `database/`, and `backups/`.

---

## 6. Audit & Monitoring Checklist

- [x] Admin activity logging table (`admin_activity_logs`) records administrative actions, IP addresses, and timestamps.
- [x] Error reporting disabled in production (`display_errors = 0`), with errors logged to private server logs.
- [x] Regular database backup utility available in admin console (`admin/backup_db.php`).
