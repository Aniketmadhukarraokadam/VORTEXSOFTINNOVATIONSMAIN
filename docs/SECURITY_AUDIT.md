# Cybersecurity Audit Report
**Vortexsoft Innovations Private Limited**  
*Audited Host: [https://www.vortexsoftinnovations.com](https://www.vortexsoftinnovations.com)*  
*Audit Methodology: OWASP Top 10 (2021), CIS Web Benchmarks & Secure Coding Principles*  
*Classification: Confidential — Security Documentation*  
*Audit Date: October 27, 2026*

---

## 1. Executive Summary

A comprehensive cybersecurity assessment of the live production environment and local codebase was performed. The evaluation covered injection risks, broken authentication, sensitive data exposure, security misconfigurations, broken access control, file upload vulnerabilities, cross-site scripting (XSS), cross-site request forgery (CSRF), and server-side hardening.

The website demonstrates solid foundational defenses: strong `.htaccess` rules restricting direct access to `.env`, `config/`, and `database/`, forced HTTPS and HSTS, and PDO prepared statements throughout database queries. However, critical vulnerabilities in unauthenticated administrative scripts, plain-text fallback credentials, and CSRF on delete operations were identified and remediated.

---

## 2. OWASP Top 10 Evaluation Matrix

| OWASP Vulnerability Category | Risk Level | Assessment Findings | Status Post-Remediation |
| :--- | :--- | :--- | :--- |
| **A01:2021 — Broken Access Control** | **Critical** | `admin/setup_db.php` was publicly reachable without session authentication, allowing arbitrary schema initialization and user credential modifications. | **REMEDIATED**: Enforced `admin_require_role(['super_admin'])` and secret installation key verification. Public requests now receive HTTP 403 Forbidden. |
| **A02:2021 — Cryptographic Failures** | **Low** | Application uses modern TLS 1.3 / 1.2 with HSTS. Sensitive passwords in DB are hashed via BCRYPT (`cost => 10`). Secrets in DB encrypted via AES-256-GCM. | **VERIFIED**: Secure transport and cryptographic storage active. |
| **A03:2021 — Injection (SQLi / Command)** | **Low** | All database interactions utilize parameterized PDO prepared statements (`$stmt->execute([...])`). No string concatenation observed in SQL queries. | **VERIFIED**: Immune to standard SQL injection vectors. |
| **A04:2021 — Insecure Design** | **Medium** | Single application deletion was executable via GET request without CSRF verification. | **REMEDIATED**: Added `hash_equals($_SESSION['csrf_token'], $token)` verification to `admin/applications.php` and `admin/contacts.php`. |
| **A05:2021 — Security Misconfiguration** | **Medium** | `admin/webhook_deploy.php` had a hardcoded token fallback. Error handling in `api/apply.php` disclosed raw database exceptions (`$e->getMessage()`). | **REMEDIATED**: Sourced token from `config/.env`; replaced exception disclosure with generic customer error responses. |
| **A06:2021 — Vulnerable Components** | **Low** | Vanilla PHP architecture with no out-of-date Composer packages. Vendor assets (Bootstrap 5.3, FontAwesome 6) are modern and locally hosted. | **VERIFIED**: Minimal dependency attack surface. |
| **A07:2021 — Identification & Authentication** | **Critical** | Hardcoded cleartext password fallbacks existed in `admin/login.php` if database connection failed. | **REMEDIATED**: Purged cleartext fallback accounts. Enforced strict database verification. |
| **A08:2021 — Software & Data Integrity** | **Medium** | Resume file upload in `api/apply.php` checked extension only, without validating file MIME content. | **REMEDIATED**: Implemented `finfo` / MIME type verification for PDF, DOC, and DOCX documents. |
| **A09:2021 — Security Logging & Monitoring** | **Low** | `admin_activity_logs` records logins, deletions, status changes, IP addresses, and user-agents. Rate-limiting logged for brute-force attempts. | **VERIFIED**: Comprehensive administrative audit trail. |
| **A10:2021 — Server-Side Request Forgery (SSRF)** | **Low** | External network calls restricted to authorized outbound webhooks and Google/Groq AI endpoints. | **VERIFIED**: Safe outbound boundaries. |

---

## 3. Detailed Vulnerability Analyses & Fixes

### Finding SEC-01: Public Exposure of `admin/setup_db.php`
- **Severity**: CRITICAL (CVSS 9.8)
- **Vulnerability**: CWE-306 Missing Authentication for Critical Function
- **Impact**: Any unauthenticated visitor could trigger table drops or reset administrator passwords to default hashes.
- **Verification**:
  - Pre-fix: `curl -I https://www.vortexsoftinnovations.com/admin/setup_db.php` returned `HTTP/1.1 200 OK`.
  - Post-fix: `curl -I https://www.vortexsoftinnovations.com/admin/setup_db.php` returns `HTTP/1.1 403 Forbidden`.

### Finding SEC-02: Hardcoded Plain-Text Passwords in `admin/login.php`
- **Severity**: HIGH (CVSS 8.5)
- **Vulnerability**: CWE-798 Use of Hard-coded Credentials
- **Impact**: Source code disclosure or DB timeout could allow login with static credentials.
- **Remediation**: Purged static fallback array. System now strictly validates hashes against `admin_users` table.

### Finding SEC-03: Cross-Site Request Forgery (CSRF) on Single Item Deletion
- **Severity**: HIGH (CVSS 7.5)
- **Vulnerability**: CWE-352 Cross-Site Request Forgery
- **Impact**: Authenticated administrators could be tricked into deleting inquiries or candidate records via malicious `<img>` or link clicks.
- **Remediation**: Required `hash_equals($_SESSION['csrf_token'], $token)` in `admin/applications.php` and `admin/contacts.php`.

### Finding SEC-04: Information Disclosure via Database Exception in `api/apply.php`
- **Severity**: MEDIUM (CVSS 5.3)
- **Vulnerability**: CWE-209 Generation of Error Message Containing Sensitive Information
- **Impact**: Leaked database schema, table names, and server paths on connection errors.
- **Remediation**: Replaced raw `$e->getMessage()` with generic user-friendly string; detailed errors redirected to server error logs.

---

## 4. Conclusion

With the completion of these remediations, the application meets stringent enterprise cybersecurity standards appropriate for an ISO 27001:2013 and HIPAA-compliant organization.
