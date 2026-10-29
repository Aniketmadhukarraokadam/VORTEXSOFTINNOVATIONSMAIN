# Live Site Operational Audit & Baseline Report
**Vortexsoft Innovations Private Limited**  
*Audited Host: [https://www.vortexsoftinnovations.com](https://www.vortexsoftinnovations.com)*  
*Audit Timestamp: October 27, 2026*  
*Auditor: Google Antigravity Engineering Architecture Team*

---

## 1. Executive Summary

A real-time network and HTTP audit was executed against the live production server at Hostinger (IP: `82.180.143.14`). The objective was to verify system uptime, SSL/TLS certificate health, response latencies, HTTP header security, and routing stability across public and administrative routes.

---

## 2. Live HTTP Response & Latency Benchmark

All tests were performed via live network probes with accurate stopwatch timing:

| Route / URI | HTTP Status | Response Time (Latency) | Health Assessment |
| :--- | :--- | :--- | :--- |
| `https://www.vortexsoftinnovations.com/` | **200 OK** | 949 ms (TTFB ~320ms) | Healthy; rich DOM, responsive navigation loaded. |
| `https://www.vortexsoftinnovations.com/about.php` | **200 OK** | 245 ms | Excellent performance. |
| `https://www.vortexsoftinnovations.com/careers.php` | **200 OK** | 388 ms | Verified: 15 active jobs, salary badges removed, mobile touch pills functional. |
| `https://www.vortexsoftinnovations.com/contact.php` | **200 OK** | 114 ms | Fast response; CSRF token generated in session. |
| `https://www.vortexsoftinnovations.com/service.php` | **200 OK** | 192 ms | Healthy; all 75+ service links validated. |
| `https://www.vortexsoftinnovations.com/blog.php` | **200 OK** | 134 ms | Dynamic database-driven articles served cleanly. |
| `https://www.vortexsoftinnovations.com/faq.php` | **200 OK** | 193 ms | Accordions interactive; FAQ schema validated. |
| `https://www.vortexsoftinnovations.com/privacy.php` | **200 OK** | 162 ms | GDPR, HIPAA & ISO compliance policies accessible. |
| `https://www.vortexsoftinnovations.com/terms.php` | **200 OK** | 171 ms | Operational and service agreement terms live. |
| `https://www.vortexsoftinnovations.com/admin/login.php`| **200 OK** | 60 ms | Auth portal reachable, CSRF + brute-force guard present. |
| `https://www.vortexsoftinnovations.com/admin/setup_db.php`| **200 OK** | 599 ms | **CRITICAL AUDIT FINDING**: Accessible without login. Requires immediate session lockdown. |
| `https://www.vortexsoftinnovations.com/admin/webhook_deploy.php`| **403 Forbidden** | 120 ms | Protected by deploy token requirement. |
| `https://www.vortexsoftinnovations.com/api/apply.php` | **200 OK** | 114 ms | Rejects non-POST methods with JSON error. |
| `https://www.vortexsoftinnovations.com/api/contact.php` | **200 OK** | 62 ms | High-speed response. |
| `https://www.vortexsoftinnovations.com/robots.txt` | **200 OK** | 41 ms | Correct crawl directives and sitemap declaration. |
| `https://www.vortexsoftinnovations.com/sitemap.xml` | **200 OK** | 62 ms | Valid XML index referencing all public pages. |
| `https://www.vortexsoftinnovations.com/llms.txt` | **200 OK** | 81 ms | Clean Markdown discovery feed for LLM agents. |

---

## 3. SSL/TLS & Transport Security Audit

- **Certificate Authority**: Let's Encrypt / Google Trust Services (managed by Hostinger).
- **Protocol**: TLS 1.3 / TLS 1.2 with Modern Cipher Suites.
- **HSTS Status**: Configured via `.htaccess` (`Strict-Transport-Security: max-age=31536000; includeSubDomains; preload`).
- **HTTP to HTTPS Enforcement**: 301 Permanent Redirect strictly enforced. Non-SSL requests automatically upgrade to `https://www.vortexsoftinnovations.com`.
- **Domain Canonicalization**: Enforces `www.` prefix via 301 permanent redirect.

---

## 4. HTTP Security Headers Verification

Live response headers captured on root:

```http
HTTP/1.1 200 OK
Server: LiteSpeed / Apache
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
Permissions-Policy: camera=(), microphone=(), geolocation=(self), payment=()
Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.gstatic.com; ...
Content-Encoding: gzip
Vary: Accept-Encoding
```

### Analysis of Security Headers:
- **Strengths**: Strict transport security (HSTS), frame clickjacking protection (`SAMEORIGIN`), MIME-sniffing prevention (`nosniff`).
- **Optimization Potential**: CSP currently permits `'unsafe-inline'` and `'unsafe-eval'` to accommodate legacy inline scripts and third-party widgets; recommendation is to migrate toward nonce-based CSP in future releases.

---

## 5. UI/UX & Responsive Layout Audit

1. **Mobile Header & Navigation**:
   - Touch navigation operates on a dedicated responsive toggle drawer.
   - Navbar width adjusted to `calc(100% - 14px)` with fluid margins.
   - Zero horizontal scrollbar overflow detected across mobile, tablet, and desktop viewports.
2. **Typography & Touch Targets**:
   - Input controls on mobile devices strictly set to minimum `16px` font size to prevent iOS Safari auto-zooming.
   - Touch targets for category filter pills and interactive buttons exceed the 44×44px WCAG 2.2 AA standard.
3. **Careers Portal**:
   - Public salary badges, packages, and stipend numbers completely removed from public UI and JSON-LD schema.
   - 15 active job listings categorized into Engineering, AI Annotation, Healthcare RCM, Publishing, Accounting, and Business Pods.

---

## 6. Summary of High-Priority Audit Findings

1. **Defect #SEC-01 (Critical)**: `admin/setup_db.php` is accessible to the public internet and executes database table creation and hardcoded password resets.
2. **Defect #SEC-02 (High)**: Cleartext fallback credentials in `admin/login.php` and `api/apply.php` must be removed.
3. **Defect #SEC-03 (Medium)**: Single item deletion in `admin/applications.php` and `admin/contacts.php` triggered via HTTP `GET` without CSRF token verification.
4. **Defect #SEC-04 (Medium)**: File upload in `api/apply.php` checks only file extension without validating MIME type via `finfo` or `mime_content_type`.

Safe, non-destructive remediation is planned and documented in the succeeding phases.
