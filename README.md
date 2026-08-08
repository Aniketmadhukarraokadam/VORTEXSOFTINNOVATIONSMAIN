# 🚀 Vortexsoft Group — Enterprise Website & Administration System

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Database](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Security](https://img.shields.io/badge/ISO%2027001-Certified-success?style=flat-square&logo=shield)](https://vortexsoftinnovations.com)
[![Status](https://img.shields.io/badge/Deployment-Production%20Ready-brightgreen?style=flat-square)](https://vortexsoftinnovations.com)

Welcome to the official source repository for **Vortexsoft Innovations Pvt. Ltd.** (a member company of **Vortexsoft Group**). This repository contains the complete, full-stack PHP & MySQL web application, REST API endpoints, enterprise administration panel, and advanced SEO/AEO/GEO optimization framework.

---

## 🏢 Executive Overview & Company Profile

- **Company Name**: Vortexsoft Innovations Pvt. Ltd.
- **Parent Group**: Vortexsoft Group
- **Certifications**: ISO 27001:2013 Certified (Information Security), HIPAA Compliant, Startup India Registered
- **Headquarters**: No.125, Ranganath Complex, Madiwala, HSR Layout 5th Sector, Bengaluru, Karnataka 560068, India
- **Delivery Centers**: Bengaluru (HQ), Pune (Maharashtra, India), Sheridan (Wyoming, USA)
- **Primary Domains**: Healthcare BPO & RCM, Custom Software Development, AI Data Annotation, Publishing Prepress & ePUB3, Real Estate Title & Settlement, Accounting & Finance BPO, Logistics, Digital Marketing, and Staffing.
- **Proprietary AI Platforms**:
  - `VortexEXHO` — Enterprise Workforce Operating System (ATS, HRMS, Payroll, LMS + AI Copilot)
  - `vortexHire` — AI Candidate Screening & Resume Qualification Platform
  - `vortexKonnect` — Call Center Speech Analytics & Quality Monitoring Platform
  - `Vortexreach` — AI B2B Outreach Automation & Lead Generation Platform

---

## 🛠️ Technology Stack & Architecture

- **Backend**: Native PHP 8.0+ (PDO, Session Security, CSRF Protection, Input Sanitization, Rate Limiting)
- **Frontend**: HTML5, Vanilla CSS3 (Custom Design Tokens), JavaScript (ES6+), Bootstrap 5.3, FontAwesome 6
- **Database**: MySQL 8.0 / MariaDB (PDO Prepared Statements, UTF-8 MB4)
- **PWA & Caching**: Service Worker (`sw.js` v9) for offline caching and instant page loads
- **Server**: Apache 2.4+ (`.htaccess` with Gzip Compression, Security Headers & 301 URL Rewriting)

---

## 📂 Directory & File Structure

```
VORTEXSOFTINNOVATIONSMAIN/
├── config/
│   ├── database.php             # PDO MySQL connection handler (Hostinger DB u696371114_adminvortex)
│   └── constants.php            # Site-wide constants, emails, phones, URLs, credentials
├── includes/
│   ├── functions.php            # Helper functions (sanitize, rate limit, email, CSRF, GEO Fact Block)
│   ├── header.php               # PHP header component with Gzip compression & AI mega-menu
│   └── footer.php               # Accelerated PHP footer component (150ms loader, FAQ link, PWA sw.js)
├── database/
│   └── setup.sql                # Complete MySQL schema & seed data (5 tables)
├── api/
│   ├── contact.php              # Contact Form API (Validation, DB Logging, Support Email, Auto-Reply)
│   ├── apply.php                # Job Application API (5MB Resume Upload, HR Email, Auto-Reply)
│   └── newsletter.php           # Newsletter Subscription API
├── admin/
│   ├── login.php                # Secure Admin Login (Bcrypt auth, Rate Limiting, Session protection)
│   ├── dashboard.php            # Admin Dashboard with live MySQL counts & recent inquiries/apps
│   ├── contacts.php             # Contact Inquiries Manager (search, notes, status, email reply)
│   ├── applications.php         # Candidate Application Manager (status pipeline, resume download)
│   ├── blog-posts.php           # Blog Post Manager (CRUD creation, editing, publishing)
│   ├── logout.php               # Session destruction handler
│   └── generate_hash.php        # Password Hash Generator tool for phpMyAdmin
├── uploads/
│   └── resumes/                 # Uploaded candidate resumes (Protected with .htaccess)
├── index.php                    # Dynamic Homepage (Hero, AI Automations, AI Products, AEO FAQs)
├── about.php                    # About Us Page (Company Timeline, Certifications, GEO Fact Block)
├── contact.php                  # Contact Page (3 Office listings, Google Maps, Form)
├── service.php                  # All 65+ Services Directory across 9 domains
├── careers.php                  # Careers Page (8 job listings + apply modal with resume upload)
├── blog.php                     # Dynamic Blog Page with MySQL article lookup & categories
├── privacy.php                  # Privacy Policy Page (ISO 27001 & HIPAA data compliance)
├── 404.php                      # Custom 404 Error Handler Page
├── .htaccess                    # Gzip compression, browser caching, HTML->PHP 301 redirects, security
├── robots.txt                   # Search Engine & AI Crawler permissions (GPTBot, PerplexityBot, etc.)
└── sitemap.xml                  # XML Sitemap referencing .php routes
```

---

## 📡 REST API Specifications

### 1. Contact Form API (`POST /api/contact.php`)
- **Content-Type**: `application/x-www-form-urlencoded` or `multipart/form-data`
- **Parameters**: `fullName` (required), `emailAddr` (required), `phone`, `service`, `company`, `msgText` (required min 10 chars)
- **Response**:
  ```json
  {
    "success": true,
    "message": "Thank you! Your message has been sent. Our team will reply within 24 hours.",
    "inquiry_id": 142
  }
  ```

### 2. Job Application API (`POST /api/apply.php`)
- **Content-Type**: `multipart/form-data`
- **Parameters**: `applicant_name` (required), `email` (required), `phone` (required), `job_title` (required), `experience_years`, `notice_period`, `expected_ctc`, `resume` (file, max 5MB, `.pdf`, `.doc`, `.docx`), `cover_letter`
- **Response**:
  ```json
  {
    "success": true,
    "message": "Your application has been submitted successfully! Our HR team will contact you soon.",
    "application_id": 89
  }
  ```

### 3. Newsletter Subscription API (`POST /api/newsletter.php`)
- **Parameters**: `email` (required)
- **Response**: `{"success": true, "message": "Thank you for subscribing!"}`

---

## 🗄️ Database Schema (`database/setup.sql`)

1. **`contact_inquiries`**: Stores name, email, phone, service, company, message, IP, read/replied status, and internal admin notes.
2. **`job_applications`**: Stores job title, candidate name, email, phone, experience, notice period, expected CTC, resume path, cover letter, and status pipeline (`new` → `reviewed` → `shortlisted` → `interview` → `offered` → `rejected`).
3. **`blog_posts`**: Stores post title, slug, category, excerpt, HTML content, author, views, featured flag, and published status.
4. **`newsletter_subscribers`**: Stores email subscriptions and active status.
5. **`admin_users`**: Stores admin username, bcrypt password hash, email, full name, role, and login audit fields.

---

## 🔐 Admin Panel Guide (`/admin`)

- **URL**: `https://vortexsoftinnovations.com/admin/login.php`
- **Security Features**:
  - Session-based authentication guard on all admin pages.
  - Bcrypt password hashing (`cost=12`).
  - Rate limiting (max 10 failed login attempts per 5 minutes per IP).
  - CSRF token verification.
- **Features**:
  - Live metric cards (Total Inquiries, Job Applications, Published Posts, Subscribers).
  - Contact Inquiry Manager (filter unread, add internal admin notes, click-to-email).
  - Candidate Application Manager (view candidate info, download resume files, transition application status).
  - Blog Post Manager (create, edit, publish/draft articles).

---

## 🎯 Strategic Optimization Plans (SEO, AEO & GEO)

### 1. Search Engine Optimization (SEO)
- Page titles, meta descriptions, canonical URLs, `hreflang` tags (`en`, `en-IN`, `en-US`, `x-default`), and hierarchical heading structure (`H1`, `H2`, `H3`).
- 4K ultra-sharp logo assets (`logo-header.png`, `logo-footer-new.png`, 1024x1024 `icon.jpg`).

### 2. Answer Engine Optimization (AEO)
- 14 Fact-First, 40–60 word Q&A blocks embedded in the homepage FAQ section.
- Full `@type: "FAQPage"` Schema.org JSON-LD graph embedded for direct featured snippet extraction by Google, ChatGPT, Claude, Perplexity, and Gemini.

### 3. Generative Engine & Geographic Optimization (GEO)
- Reusable `render_geo_fact_block()` function placed on key pages providing consistent, citable brand facts.
- GEO location meta tags (`geo.region: IN-KA`, `geo.position: 12.9141;77.6162`).
- Multi-location `Corporation` & `PostalAddress` Schema graphs for **Bengaluru HQ**, **Pune Center**, and **Wyoming, USA**.

---

## 🚀 Production Deployment Instructions (Hostinger)

1. **Upload Files**: Copy all project files to Hostinger `public_html`.
2. **Database Import**:
   - Log into Hostinger cPanel → phpMyAdmin → select `u696371114_adminvortex`.
   - Import `database/setup.sql`.
3. **Admin Password Setup**:
   - Open `https://vortexsoftinnovations.com/admin/generate_hash.php`.
   - Enter your password (e.g. `ShivaG@1437`), click **Generate**.
   - Copy the generated SQL query, run it in phpMyAdmin, and **delete** `generate_hash.php`.

---

© 2026 Vortexsoft Group. Vortexsoft Innovations Pvt. Ltd. All rights reserved.
