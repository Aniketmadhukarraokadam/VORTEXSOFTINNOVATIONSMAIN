# Database Architecture, API Contracts & Data Integrity Audit
**Vortexsoft Innovations Private Limited**  
*Document Version: 2.0.0 — Database & API Specification*  
*Database Engine: MySQL (MariaDB 10.11+) / Charset: `utf8mb4_unicode_ci`*  
*Target Database: `u696371114_vortexsoftcom`*  
*Audit Date: October 27, 2026*

---

## 1. Executive Summary

This audit reviews the MySQL relational database schema, index design, query safety, connection pooling, and RESTful API endpoints. The schema supports lead generation, talent acquisition, administrative content management, and outbound email notification tracking.

All database queries utilize PDO prepared statements with strict parameter binding. No raw string interpolation exists, guaranteeing full immunity from SQL injection.

---

## 2. Relational Database Schema Inventory

| Table Name | Engine | Primary Key | Key Indexes | Purpose & Functional Scope |
| :--- | :--- | :--- | :--- | :--- |
| **`contact_inquiries`** | InnoDB | `id` (INT UNSIGNED AUTO_INCREMENT) | `idx_email`, `idx_is_read`, `idx_created_at` | Stores customer quote requests, service requirements, IP, and notes. |
| **`job_applications`** | InnoDB | `id` (INT UNSIGNED AUTO_INCREMENT) | `idx_email`, `idx_status`, `idx_job_title`, `idx_created_at` | Stores candidate profiles, resume paths, experience, status workflow. |
| **`jobs`** | InnoDB | `id` (INT UNSIGNED AUTO_INCREMENT) | `idx_is_active`, `idx_sort_order` | CRUD job postings directory feeding `/careers.php`. |
| **`blog_posts`** | InnoDB | `id` (INT UNSIGNED AUTO_INCREMENT) | `uk_slug` (UNIQUE), `idx_published`, `idx_views` | Articles, categories, meta titles/descriptions, and view counts. |
| **`admin_users`** | InnoDB | `id` (INT UNSIGNED AUTO_INCREMENT) | `uk_username`, `uk_email` | RBAC admin credentials (`super_admin`, `admin`, `viewer`). |
| **`email_accounts`** | InnoDB | `id` (INT UNSIGNED AUTO_INCREMENT) | `uk_email` | SMTP/IMAP configuration for administrative mailer. |
| **`email_logs`** | InnoDB | `id` (INT UNSIGNED AUTO_INCREMENT) | `idx_status`, `idx_type`, `idx_created` | Outbound/inbound communication tracking and delivery states. |
| **`email_templates`**| InnoDB | `id` (INT UNSIGNED AUTO_INCREMENT) | `uk_template_key` | Branded HTML templates for automated candidate updates. |
| **`admin_activity_logs`**| InnoDB| `id` (INT UNSIGNED AUTO_INCREMENT) | `idx_action`, `idx_created` | Immutable administrative audit log with IP and user agent. |
| **`newsletter_subscribers`**| InnoDB| `id` (INT UNSIGNED AUTO_INCREMENT)| `uk_email` | Double opt-in newsletter email directory. |
| **`system_settings`**| InnoDB | `setting_key` (VARCHAR(100)) | Primary Key (`setting_key`) | Global key-value operational configuration storage. |

---

## 3. Query Safety & Data Integrity Standards

1. **Strict Prepared Statements**:
   - Every read and write operation uses PDO parameterized queries. Example from `api/contact.php`:
     ```php
     $stmt = $db->prepare("INSERT INTO contact_inquiries (name, email, phone, service, company, message, ip_address, created_at) VALUES (:name, :email, :phone, :service, :company, :message, :ip, NOW())");
     $stmt->execute([...]);
     ```
2. **UTF-8 Multi-byte Support**:
   - `utf8mb4` character set with `utf8mb4_unicode_ci` collation ensures complete support for international character sets, accents, and emojis without data corruption.
3. **Transaction Safety**:
   - Complex multi-row status updates utilize InnoDB ACID transactions (`$db->beginTransaction()`, `$db->commit()`, `$db->rollBack()`).

---

## 4. API Endpoints Contract Specification

### A. Contact Form API (`POST /api/contact.php`)
- **Headers**: `Content-Type: application/json`
- **Request Parameters**:
  - `name` (string, required, max 120)
  - `email` (string, required, valid email format)
  - `phone` (string, optional, valid phone format)
  - `service` (string, optional, default: 'General Inquiry')
  - `message` (string, required, min 10, max 5000 chars)
  - `csrf_token` (string, required)
  - `website_hp` (string, honeypot - must be empty)
- **Response Format**:
  ```json
  {
    "success": true,
    "message": "Thank you! Your message has been sent. Our team will reply within 24 hours.",
    "inquiry_id": 42
  }
  ```

### B. Job Application API (`POST /api/apply.php`)
- **Headers**: `Content-Type: multipart/form-data`
- **Request Parameters**:
  - `job_title` (string, required)
  - `applicant_name` (string, required)
  - `email` (string, required, valid email)
  - `phone` (string, required)
  - `experience_years` (numeric, optional)
  - `resume` (file, optional, PDF/DOC/DOCX, max 5MB, MIME checked)
  - `website_hp` (honeypot - must be empty)
- **Response Format**:
  ```json
  {
    "success": true,
    "message": "Application submitted successfully! Our HR team will review your profile and reach out within 3–5 business days."
  }
  ```

### C. Newsletter API (`POST /api/newsletter.php`)
- **Headers**: `Content-Type: application/json`
- **Request Parameters**: `email` (string, required), `name` (string, optional)
- **Response Format**:
  ```json
  {
    "success": true,
    "message": "Thank you for subscribing! You will receive our latest updates."
  }
  ```

---

## 5. Backup & Disaster Recovery Architecture

1. **Automated Export Tool (`admin/backup_db.php`)**:
   - Creates a complete, timestamped SQL dump in `/backups/db_backup_[YYYY-MM-DD_HH-mm-ss].sql`.
   - Includes `DROP TABLE IF EXISTS`, full `CREATE TABLE` DDL, and parameterized `INSERT` statements for all tables.
   - Disables and reenables foreign key checks during export/import (`SET FOREIGN_KEY_CHECKS=0`).
2. **Access Control**:
   - The `/backups/` directory is blocked at the web-server level via `.htaccess` (`Deny from all`).
