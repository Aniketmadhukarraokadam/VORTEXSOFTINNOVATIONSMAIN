# 07 — Development Tickets & Issue Backlog
**Vortexsoft Innovations Pvt. Ltd.**
*Generated from codebase audit — August 2026*

> Tickets are NOT implementation orders. They are a documented backlog.
> MUST get explicit user approval before implementing any ticket.
> Sort Order: CRITICAL → HIGH → MEDIUM → LOW → INFO

---

## 🚨 CRITICAL — Must Fix Immediately

### WEB-SEC-001 — Remove Hardcoded Deploy Token from Source Code
- **File**: `admin/webhook_deploy.php` line 2
- **Issue**: `define('DEPLOY_TOKEN', 'VortexDeploy6498286f401141b8')` is committed to GitHub. Anyone who accesses the repo can trigger a full production deployment.
- **Impact**: Full server compromise — attacker can overwrite all production files
- **Fix**: Read token from `config/.env` using the same pattern as `database.php`
- **Effort**: Small (~30 min)
- **Risk of Fix**: LOW — simple env variable read

### WEB-SEC-002 — Remove Hardcoded Admin Passwords from Source Code
- **File**: `admin/login.php` lines 173–174
- **Issue**: Plaintext passwords `'Mrunal@9996'` and `'ShivaG@1437'` in the `auto_install_tables()` function
- **Impact**: Credential exposure, account compromise
- **Fix**: Remove the auto-install function entirely or generate hashes at runtime without exposing plaintext
- **Effort**: Small (~30 min)
- **Risk of Fix**: LOW — remove hardcoded values

---

## 🔴 HIGH — Fix Soon

### WEB-SEC-003 — Delete `admin/generate_hash.php` on Production Server
- **File**: `admin/generate_hash.php`
- **Issue**: Tool for generating bcrypt hashes. If accessible on production, it could be used to understand hashing parameters or to create credentials.
- **Action Required**: Confirm whether this file is deployed on the production server. If so, delete it.
- **Effort**: Trivial
- **Risk of Fix**: NONE — deletion only

### WEB-SEC-004 — RBAC Not Enforced on Admin Pages
- **Files**: All `admin/*.php`
- **Issue**: `admin_require_role()` function is defined but never called. All admin pages only check `admin_check()` (session exists). The `viewer` role has same access as `admin`.
- **Fix**: Add `admin_require_role(['super_admin', 'admin'])` to destructive operations in contacts, applications, and blog-posts admin pages
- **Effort**: Medium (~2h)
- **Risk of Fix**: LOW — adds protection, doesn't remove features

### WEB-SEC-005 — Delete Action via GET Request (CSRF Vulnerability)
- **File**: `admin/applications.php` line 21–25
- **Issue**: `?delete=ID` via GET deletes an application without CSRF protection
- **Fix**: Convert to POST form with CSRF token
- **Effort**: Small (~1h)
- **Risk of Fix**: LOW

### WEB-MISSING-001 — Individual Blog Post Detail Page Does Not Exist
- **Issue**: `blog.php` lists posts but clicking a post has no destination. No `/blog/{slug}` or `blog-post.php` page exists. Zero blog content is indexed by Google individually.
- **Impact**: Cannot rank for blog content. Blog is functionally incomplete.
- **Fix**: Create `blog-post.php` (or `/blog/{slug}/index.php`) that:
  - Reads `slug` from URL
  - Queries `blog_posts` table
  - Renders full post with proper title/meta/canonical/schema
- **Effort**: Medium (~4h)
- **Risk of Fix**: LOW — new file only, no existing code changed

### WEB-MISSING-002 — Admin Password Reset Not Implemented
- **Issue**: No "Forgot Password" functionality for admin users. If credentials are lost, recovery must happen via direct DB query (generate_hash.php tool).
- **Fix**: Simple email-based reset flow using a token stored in `admin_users` or a separate table
- **Effort**: Medium (~4h)

### WEB-MISSING-003 — Missing DB Tables Referenced in Code
- **Tables**: `admin_activity_logs`, `email_templates`, `email_accounts`, `email_logs`
- **Issue**: Functions reference these tables, they fail silently. Activity logging doesn't work.
- **Fix**: Add these tables to `setup.sql` and implement properly
- **Effort**: Medium (~2h)

---

## 🟡 MEDIUM — Fix in Next Cycle

### WEB-SEC-006 — Session Timeout Not Set for Admin
- **Files**: All `admin/*.php`
- **Issue**: No idle session timeout. Admin sessions persist until manual logout.
- **Fix**: On each admin page load, check `$_SESSION['admin_login_time']` and compare to current time. If >60min, destroy session and redirect to login.
- **Effort**: Small (~1h)

### WEB-SEC-007 — CSP `unsafe-inline` and `unsafe-eval` Should Be Removed
- **File**: `.htaccess` CSP header
- **Issue**: Weakens XSS protection significantly
- **Fix**: Move all inline `<script>` blocks to external JS files. Use CSP nonces for remaining inline scripts.
- **Effort**: Large (~8h) — requires restructuring all pages

### WEB-SEC-008 — Rate Limiting Is Session-Based (Not Persistent)
- **Issue**: `check_rate_limit()` uses `$_SESSION` for rate limiting. A bot that doesn't send session cookies bypasses this entirely.
- **Fix**: Implement DB or file-based rate limiting keyed by IP
- **Effort**: Small (~2h)

### WEB-SEO-001 — `sitemap-images.xml` Referenced But Does Not Exist
- **File**: `robots.txt` line 71
- **Fix**: Create image sitemap or remove reference
- **Effort**: Small (~1h)

### WEB-SEO-002 — `privacy.php` Has Hardcoded Canonical
- **File**: `privacy.php` line 8
- **Fix**: Remove hardcoded `$canonical_url` override; use dynamic system
- **Effort**: Trivial

### WEB-SEO-003 — `lang` Attribute Not Locale-Specific
- **File**: `includes/header.php`
- **Fix**: `lang="en-IN"` for `.in` domain, `lang="en-US"` for `.com` domain
- **Effort**: Trivial

### WEB-PERF-001 — Logo PNG Files Are 1MB+ Each
- **Files**: `logo-header.png`, `logo-footer.png`, `logo-footer-new.png`
- **Issue**: Each logo PNG is ~1MB. Dramatically increases page load time.
- **Fix**: Convert to WebP format (<100KB), serve with `<picture>` fallback
- **Effort**: Small (~1h) — image optimization + code update

### WEB-PERF-002 — Service Worker Does Not Cache `terms.php`, `privacy.php`
- **File**: `sw.js` line 3 — CRITICAL array
- **Fix**: Add `terms.php` and `privacy.php` to the CRITICAL cache list
- **Effort**: Trivial

### WEB-BUG-001 — manifest.json Shortcuts Use `.html` Extension
- **File**: `manifest.json` shortcuts section
- **Fix**: Change `/contact.html` → `/contact.php`, etc.
- **Effort**: Trivial

---

## 🟢 LOW — Nice to Have

### WEB-SMTP-001 — Implement SMTP Email Instead of PHP `mail()`
- **Issue**: PHP `mail()` is unreliable. Many shared hosting providers have poor deliverability with `mail()`. Config template for Hostinger SMTP exists in `.env.example` but not implemented.
- **Fix**: Implement PHPMailer or Symfony Mailer with Hostinger SMTP credentials
- **Effort**: Medium (~4h)

### WEB-PERF-003 — Enable PHP OPcache
- **Issue**: No OPcache configuration present. PHP is interpreting scripts on every request.
- **Fix**: Add OPcache configuration to Hostinger PHP settings panel
- **Effort**: Small (configuration only)

### WEB-FEATURE-001 — Newsletter Broadcast System
- **Issue**: Subscriptions are captured and stored in DB but there is no way to send newsletters to subscribers.
- **Fix**: Build a `admin/newsletters.php` page to compose and send broadcast emails
- **Effort**: Large (~8h)

### WEB-FEATURE-002 — Admin Activity Log Viewer
- **Issue**: `log_admin_activity()` is called but the `admin_activity_logs` table doesn't exist. No way to audit admin actions.
- **Fix**: Create the table (WEB-MISSING-003) + create `admin/activity-log.php` viewer
- **Effort**: Medium (~3h)

### WEB-FEATURE-003 — Google Analytics / Tag Manager Integration
- **Issue**: No analytics tracking is implemented. No visibility into traffic.
- **Fix**: Add Google Analytics 4 script (or GTM container) to `includes/header.php` or `footer.php`
- **Effort**: Small (~30 min)

---

## ℹ️ INFO / REQUIRES VERIFICATION

| ID | Question |
|---|---|
| INFO-001 | Is `admin/generate_hash.php` present on the production server? |
| INFO-002 | Are the 4 missing DB tables (`admin_activity_logs`, `email_templates`, `email_accounts`, `email_logs`) present on the production DB? |
| INFO-003 | Is `vortexsoftinnovations.in` configured in Google Search Console? |
| INFO-004 | Is the `.in` domain using cPanel Alias, Parked Domain, or Addon Domain? |
| INFO-005 | Is PHP `mail()` successfully delivering emails on Hostinger? |
| INFO-006 | Is the `config/.env` file present on the production server with DB credentials? |
| INFO-007 | Is `sqlite_dev.db` present on the production server? (should not be) |
| INFO-008 | Is the `/assets/partials/` directory still in use? (blocked in robots.txt) |
| INFO-009 | Does `admin/webhook_deploy.php` need to be publicly accessible, or can it be moved out of public_html? |
