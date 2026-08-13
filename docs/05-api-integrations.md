# 05 — API & Integration Specification
**Vortexsoft Innovations Pvt. Ltd.**
*Confirmed from codebase — August 2026*

---

## Internal REST API Endpoints

### 1. Contact Form API
- **Endpoint**: `POST /api/contact.php`
- **Auth**: None (public). CSRF token required.
- **Rate Limit**: 5 per 5 minutes per IP
- **Request** (form-encoded):
  ```
  fullName     = string (required, max 120 chars)
  emailAddr    = string (required, valid email)
  phone        = string (optional, 7–20 chars)
  service      = string (optional, default 'General Inquiry')
  company      = string (optional)
  msgText      = string (required, min 10, max 5000)
  csrf_token   = string (required)
  website_hp   = string (must be empty — honeypot)
  ```
- **Success Response**:
  ```json
  {"success": true, "message": "...", "inquiry_id": 142}
  ```
- **Error Response**:
  ```json
  {"success": false, "message": "Please enter a valid email address."}
  ```
- **Side Effects**: DB insert + email notification + auto-acknowledgement email
- **Security**: CSRF, honeypot, rate limiting, input sanitization

### 2. Job Application API
- **Endpoint**: `POST /api/apply.php`
- **Auth**: None (public)
- **Rate Limit**: 3 per 10 minutes per IP
- **Request** (multipart/form-data):
  ```
  applicant_name    = string (required)
  email             = string (required, valid email)
  phone             = string (required)
  job_title         = string (required)
  department        = string (optional)
  current_location  = string (optional)
  experience_years  = decimal (optional)
  current_company   = string (optional)
  notice_period     = string (optional)
  expected_ctc      = string (optional)
  linkedin_url      = string (optional)
  portfolio_url     = string (optional)
  cover_letter      = string (optional)
  resume            = file (optional, PDF/DOC/DOCX, max 5MB)
  website_hp        = string (must be empty — honeypot)
  ```
- **Success Response**:
  ```json
  {"success": true, "message": "...", "application_id": 89}
  ```
- **Side Effects**: DB insert + file saved to `/uploads/resumes/` + HR email + auto-reply

### 3. Newsletter Subscription API
- **Endpoint**: `POST /api/newsletter.php`
- **Auth**: None (public)
- **Rate Limit**: 3 per 5 minutes per IP
- **Request** (form-encoded):
  ```
  email = string (required, valid email)
  name  = string (optional)
  ```
- **Success Response**:
  ```json
  {"success": true, "message": "Thank you for subscribing!"}
  ```
- **Logic**: If already subscribed and active → error. If inactive → reactivate. New → insert.

---

## Email System

### Method
- **Current**: PHP native `mail()` function — CONFIRMED FROM CODE
- **SMTP Config**: Exists in `.env.example` (Hostinger SMTP) but NOT implemented in code. Current code uses `mail()`.
- **From Address**: `contact@vortexsoftinnovations.com` (hardcoded in `send_notification_email()`)
- **Email Logging**: Referenced in `email_logs` table in code but table NOT in `setup.sql` — REQUIRES VERIFICATION
- **Email Templates**: Referenced in `get_email_template()` function querying `email_templates` table — NOT in `setup.sql` — REQUIRES VERIFICATION

### Email Types Sent
| Trigger | Recipient | From |
|---|---|---|
| Contact form submit | `support@vortexsoftinnovations.com` | `contact@vortexsoftinnovations.com` |
| Contact form submit (auto-reply) | Submitter email | `contact@vortexsoftinnovations.com` |
| Job application submit | `careers@vortexsoftinnovations.in` | `contact@vortexsoftinnovations.com` |
| Job application submit (auto-reply) | Applicant email | `contact@vortexsoftinnovations.com` |

---

## External Integrations (Confirmed from Code/Config)

### 1. Hostinger MySQL Database
- **Service**: Hostinger web hosting
- **Database**: `u696371114_vortexsoftcom` (MySQL 8.0)
- **Auth**: DB credentials via `config/.env`
- **Fallback**: SQLite at `sqlite_dev.db` for local development

### 2. GitHub (Source Control + Webhook Deploy)
- **Repo**: `Aniketmadhukarraokadam/VORTEXSOFTINNOVATIONSMAIN`
- **Branch**: `main`
- **Deploy method**: HTTP GET/POST to `/admin/webhook_deploy.php` with `X-Deploy-Token` header
- **Security**: Static token (hardcoded — security risk — see SEC-001)
- **Scope**: Downloads ZIP archive from GitHub, extracts, syncs to `public_html`

### 3. Google Maps (Embedded)
- **Usage**: Embedded iframes on `contact.php` for office location maps
- **Method**: Standard `<iframe>` embed (no API key needed for basic embed)
- **Confirmed from**: INFERRED from context (contact page with 3 office iframes)

### 4. WhatsApp Business
- **Usage**: Floating chat button + contact options
- **URL**: `https://wa.me/918308906690?text=...`
- **Auth**: None — public WhatsApp link
- **Confirmed from**: CODE (`SOCIAL_WHATSAPP` constant)

### 5. Google Fonts / CDN Assets
- **Fonts**: Inter, Poppins — loaded via `assets/vendor/fonts.css`
- **CDN**: Bootstrap from `cdn.jsdelivr.net`, FontAwesome from `cdnjs.cloudflare.com`
- **CSP**: These CDNs are whitelisted in `.htaccess` Content-Security-Policy

---

## Integrations NOT Currently Implemented (Future Scope)

- SMTP email sending (config template exists, no code implementation)
- Google Analytics / Tag Manager
- reCAPTCHA / hCaptcha
- Payment processing
- CRM (HubSpot, Salesforce)
- Live chat (Intercom, Freshchat, Tidio)
- SMS notifications
