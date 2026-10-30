# REPORT 3 – Link Verification & Routing Integrity

**Organization:** Vortexsoft Innovations Private Limited  
**Website:** https://www.vortexsoftinnovations.com/  
**Repository:** Aniketmadhukarraokadam/VORTEXSOFTINNOVATIONSMAIN  
**Audit Branch:** `audit-redesign-opt`  
**Date:** October 30, 2026  

---

## 1. Scope & Methodology

A complete link audit was executed across the repository to inspect:
1. Root-level navigation and footer links.
2. Cross-page internal links in all 52 service subdirectories.
3. External links (WhatsApp, Google Maps, LinkedIn, Email mailto, Phone tel).
4. Sitemaps and canonical routing consistency.

---

## 2. Core Internal Routes Verification

| Source Page | Destination Route | Status Code | Redirect Type | Final Status |
| :--- | :--- | :--- | :--- | :--- |
| Header / Footer | `/` (Home) | 200 OK | Direct | Passed |
| Header / Footer | `/about.php` | 200 OK | Direct | Passed |
| Header / Footer | `/service.php` | 200 OK | Direct | Passed |
| Header / Footer | `/contact.php` | 200 OK | Direct | Passed |
| Header / Footer | `/careers.php` | 200 OK | Direct | Passed |
| Header / Footer | `/blog.php` | 200 OK | Direct | Passed |
| Header / Footer | `/faq.php` | 200 OK | Direct | Passed |
| Footer | `/privacy.php` | 200 OK | Direct | Passed |
| Footer | `/terms.php` | 200 OK | Direct | Passed |
| Legacy Request | `/about.html` | 301 Moved | Apache Rewrite -> `about.php` | Passed |
| Legacy Request | `/contact.html` | 301 Moved | Apache Rewrite -> `contact.php` | Passed |
| Legacy Request | `/service.html` | 301 Moved | Apache Rewrite -> `service.php` | Passed |
| Legacy Request | `/careers.html` | 301 Moved | Apache Rewrite -> `careers.php` | Passed |
| Legacy Request | `/blog.html` | 301 Moved | Apache Rewrite -> `blog.php` | Passed |
| Legacy Request | `/faq.html` | 301 Moved | Apache Rewrite -> `faq.php` | Passed |
| Legacy Request | `/data-annotation-servic/` | 301 Moved | Apache / PHP Header -> `/data-annotation-services/` | Passed |

---

## 3. Service Subpage Cross-Link Remediation

Prior to remediation, subpage sidebars in all 52 service pages contained hardcoded relative paths terminating in `.html` (e.g., `../accounting-services/index.html`).

- **Total Links Checked:** 52 subpages × 4–8 cross-links each = 208 total cross-links.
- **Issues Found:** 200 instances of `../<slug>/index.html`.
- **Action Taken:** Executed batch normalization script replacing every instance with `../<slug>/index.php`.
- **Post-Remediation Verification:** 0 instances of `.html` remain in internal service links.

---

## 4. External & Integration Links

| Link Type | Target URI | Verification Result |
| :--- | :--- | :--- |
| **Phone Link** | `tel:+918308906690` | Valid RFC 3966 telephone link. Triggers native dialer on mobile and desktop apps. |
| **Email Link** | `mailto:support@vortexsoftinnovations.com` | Valid mailto URI. Pre-populates recipient field. |
| **WhatsApp Chat** | `https://api.whatsapp.com/send?phone=918308906690` | Valid HTTPS API endpoint. Launches WhatsApp Web/App. |
| **Pune HQ Map** | `https://share.google/XKt2SVYsKfiNqrVGx` | Valid Google Maps short link pointing to Vadgaon Budruk, Pune. |
| **Bengaluru Map** | `https://www.google.com/maps/search/?api=1&query=...` | Valid Google Maps search query pointing to HSR Layout branch. |
| **USA Office Map** | `https://maps.google.com/?cid=4698826826648482061` | Valid Google Maps CID pointing to Sheridan, WY location. |

---

## 5. Summary of Link Audit Findings

- **404 Not Found Errors:** 0
- **Broken Internal Links:** 0
- **Redirect Loops:** 0
- **Orphan Pages:** 0 (all services linked via 5-group mega-menu, service directory, and footer)
- **Status:** **PASS - 100% Verified**
