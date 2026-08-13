# 01 — Current Product Requirements Document (PRD)
**Vortexsoft Innovations Pvt. Ltd. — Website & Admin System**
*Based on confirmed codebase audit — August 2026*

> All items marked as: **CONFIRMED FROM CODE** | **CONFIRMED FROM CONFIG** | **INFERRED** | **REQUIRES VERIFICATION**

---

## Problem Statement

Vortexsoft Innovations Pvt. Ltd. requires a professional, globally-visible corporate website that:
1. Markets 75+ IT and BPO services to international and Indian clients
2. Captures and qualifies inbound leads via contact forms
3. Handles job applications for internal HR
4. Publishes thought-leadership blog content
5. Manages internal records via a secure admin dashboard

---

## Target Users

| User Group | Description | Primary Flow |
|---|---|---|
| Global Enterprise Clients | B2B buyers from USA, UK, Europe seeking IT/BPO outsourcing | Browse Services → Contact Form |
| Indian Business Clients | SME/corporates in India seeking accounting, payroll, or software services | Browse Services → Contact Form |
| Job Seekers | Candidates applying for open positions | Careers → Apply Modal |
| HR Admin | Internal team reviewing applications | Admin Panel → Applications |
| Content Manager | Internal team publishing blog articles | Admin Panel → Blog Posts |
| Operations Admin | Internal team monitoring inquiries | Admin Panel → Contacts |

---

## Product Vision

A high-performance, SEO-optimized PHP corporate website serving as the primary digital front for Vortexsoft Group across two domains (`.com` global, `.in` India), with a fully operational admin backend.

---

## Current Features

### F-01 — Homepage (`index.php`)
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Description**: Landing page with animated hero, 75+ service categories, AI product showcase (VortexEXHO, vortexHire, vortexKonnect, Vortexreach), company stats, FAQ schema, trust marquee, and CTA sections
- **Dependencies**: `includes/header.php`, `includes/footer.php`, `config/constants.php`
- **Confirmed From**: CODE

### F-02 — Services Page (`service.php`)
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Description**: Comprehensive directory of 65+ services grouped across 9 industry domains
- **Confirmed From**: CODE

### F-03 — About Page (`about.php`)
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Description**: Company overview, timeline, certifications (ISO 27001, HIPAA, Startup India), team stats, GEO fact block
- **Confirmed From**: CODE

### F-04 — Contact Page (`contact.php`)
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Description**: 3-office listings (Pune HQ, Bengaluru, USA), Google Maps embeds, contact form with AJAX submission
- **Confirmed From**: CODE

### F-05 — Careers Page (`careers.php`)
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Description**: Lists open job positions (8+ roles), apply modal with resume upload (PDF/DOC/DOCX, 5MB max)
- **Confirmed From**: CODE

### F-06 — Blog Page (`blog.php`)
- **Status**: ACTIVE ✅
- **Classification**: SHOULD HAVE
- **Description**: Dynamic blog listing from MySQL. Category filtering. 1 sample post seeded. No individual blog post detail page exists.
- **Confirmed From**: CODE

### F-07 — Privacy Policy Page (`privacy.php`)
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Confirmed From**: CODE

### F-08 — Terms of Service Page (`terms.php`)
- **Status**: ACTIVE ✅ — recently added
- **Classification**: MUST HAVE
- **Confirmed From**: CODE

### F-09 — Custom 404 Page (`404.php`)
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Confirmed From**: CODE

### F-10 — Contact Form API (`api/contact.php`)
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Description**: POST endpoint, CSRF, honeypot, rate limiting (5/5min), DB save, email notification + auto-acknowledgement
- **Confirmed From**: CODE

### F-11 — Job Application API (`api/apply.php`)
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Description**: Resume file upload (PDF/DOC/DOCX, 5MB), rate limiting (3/10min), DB save, HR email + auto-reply
- **Confirmed From**: CODE

### F-12 — Newsletter Subscription API (`api/newsletter.php`)
- **Status**: ACTIVE ✅
- **Classification**: SHOULD HAVE
- **Description**: Email subscription with duplicate/reactivation handling, rate limiting (3/5min)
- **Confirmed From**: CODE

### F-13 — Admin Login (`admin/login.php`)
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Description**: Bcrypt auth, CSRF token, file-based brute-force lockout (5 attempts → 15-min lockout), session regeneration
- **Confirmed From**: CODE

### F-14 — Admin Dashboard (`admin/dashboard.php`)
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Description**: Live counts of inquiries, applications, blog posts, subscribers. Recent entries tables.
- **Confirmed From**: CODE

### F-15 — Admin Contact Manager (`admin/contacts.php`)
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Confirmed From**: CODE

### F-16 — Admin Application Manager (`admin/applications.php`)
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Description**: Full pipeline (new → reviewed → shortlisted → interview → offered → rejected → withdrawn), resume download, pagination
- **Confirmed From**: CODE

### F-17 — Admin Blog Manager (`admin/blog-posts.php`)
- **Status**: ACTIVE ✅
- **Classification**: SHOULD HAVE
- **Confirmed From**: INFERRED (referenced in dashboard, README)

### F-18 — Webhook Auto-Deploy (`admin/webhook_deploy.php`)
- **Status**: ACTIVE ✅ — ⚠️ SECURITY RISK
- **Classification**: NICE TO HAVE
- **Description**: Downloads GitHub ZIP and syncs to production. Auth via hardcoded static token in source code.
- **Confirmed From**: CODE

### F-19 — Progressive Web App (PWA)
- **Status**: ACTIVE ✅
- **Classification**: NICE TO HAVE
- **Description**: Service Worker v10, manifest.json, cache-first for assets
- **Confirmed From**: CODE

### F-20 — 75+ Service Sub-pages
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Confirmed From**: DIRECTORY LISTING

### F-21 — GEO Fact Block (AI Engine Optimization)
- **Status**: ACTIVE ✅
- **Classification**: SHOULD HAVE
- **Description**: `render_geo_fact_block()` helper for AEO/GEO content
- **Confirmed From**: CODE

### F-22 — Dual Domain Support (.com + .in)
- **Status**: ACTIVE ✅
- **Classification**: MUST HAVE
- **Confirmed From**: CODE

---

## Current User Flows (Confirmed)

### Flow 1: Lead Capture
```
Homepage → Service pages → Contact Page → Fill Form → AJAX POST /api/contact.php
→ DB save + email to support@ → Auto-reply to user → Success modal shown
```

### Flow 2: Job Application
```
Careers Page → Browse Jobs → Click Apply → Modal opens → Fill form + upload resume
→ AJAX POST /api/apply.php → DB save + email to careers@ → Auto-reply → Success modal
```

### Flow 3: Admin — Contact Management
```
Admin Login → Dashboard → Contacts → Filter/Search → View Inquiry → Add Note → Reply
```

### Flow 4: Admin — Application Pipeline
```
Admin Login → Applications → View Application → Update Status → Add Notes → Download Resume
```

### Flow 5: Blog Browse (INCOMPLETE)
```
Blog Page → Category Filter → View Post Listing → Click Post → [MISSING: Individual post page]
```

---

## MVP (Currently Essential)

- Homepage, all service pages, about, contact, careers, privacy, terms
- Contact form + email delivery
- Job application form + resume upload + HR email
- Admin login + dashboard + inquiry/application management

---

## Out of Scope (Not Currently Implemented)

- Individual blog post detail pages
- Password reset for admin
- SMTP email (currently PHP `mail()`)
- Public user login/registration
- Newsletter broadcast/send feature (subscription saved only)
- E-commerce or payments
