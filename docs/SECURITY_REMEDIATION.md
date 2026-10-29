# Security Remediation & Hardening Guide
**Vortexsoft Innovations Private Limited**  
*Document Version: 2.0.0 — Hardening Implementation Record*  
*Target: Production Web Application (`www.vortexsoftinnovations.com`)*  
*Remediation Date: October 27, 2026*

---

## 1. Overview of Remediations Applied

This document details the exact technical changes implemented across the application source code and web server configuration to address the vulnerabilities identified in `docs/SECURITY_AUDIT.md`.

---

## 2. Code Changes by Component

### A. Administrative Setup Lockdown (`admin/setup_db.php`)
- **Old Behavior**: Anonymous visitors could run schema routines and password resets.
- **Implemented Fix**:
  ```php
  if (session_status() === PHP_SESSION_NONE) session_start();
  require_once __DIR__ . '/../config/constants.php';
  require_once __DIR__ . '/../config/database.php';
  require_once __DIR__ . '/../includes/functions.php';

  $is_super_admin = (!empty($_SESSION[ADMIN_SESSION]) && (($_SESSION['vortex_admin_role'] ?? $_SESSION['admin_role'] ?? '') === 'super_admin'));
  $setup_key = $_GET['setup_key'] ?? '';
  $valid_setup_key = defined('SETUP_KEY') ? SETUP_KEY : 'VortexSetup2026SecureAdminKey';

  if (!$is_super_admin && !hash_equals($valid_setup_key, $setup_key)) {
      http_response_code(403);
      die("403 Forbidden — Super Admin Required");
  }
  ```
- **Password Protection**: Removed automatic `REPLACE INTO admin_users` query. Added condition to only insert default accounts if `SELECT COUNT(*) FROM admin_users` is 0.

### B. Authentication Hardening (`admin/login.php`)
- **Old Behavior**: Hardcoded password fallback in array when database was unreachable.
- **Implemented Fix**:
  - Deleted the `$fallback` array containing plain-text credentials.
  - Enforced strict `password_verify($password, $admin['password_hash'])`.
  - Added user-friendly message when database is unavailable without granting fallback access.

### C. CSRF Protection for Record Deletion (`admin/applications.php` & `admin/contacts.php`)
- **Old Behavior**: Records could be deleted via direct URL navigation (`?delete=123`).
- **Implemented Fix**:
  ```php
  if (isset($_GET['delete'])) {
      $token = $_GET['csrf'] ?? $_POST['csrf_token'] ?? '';
      if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
          header('Location: applications.php?err=csrf');
          exit;
      }
      $did = (int)$_GET['delete'];
      // Perform deletion...
  }
  ```
  - Appended `&csrf=<?= htmlspecialchars(csrf_token()) ?>` to all delete action links in the admin UI.

### D. File Upload MIME Validation (`api/apply.php`)
- **Old Behavior**: Validated only `.pdf`, `.doc`, `.docx` string extensions.
- **Implemented Fix**:
  ```php
  $detected_mime = '';
  if (function_exists('finfo_open')) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      if ($finfo) {
          $detected_mime = finfo_file($finfo, $file['tmp_name']);
          finfo_close($finfo);
      }
  } elseif (function_exists('mime_content_type')) {
      $detected_mime = @mime_content_type($file['tmp_name']);
  }

  $allowed_mimes = [
      'application/pdf',
      'application/msword',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'application/octet-stream',
      'application/zip'
  ];
  if (!empty($detected_mime) && !in_array($detected_mime, $allowed_mimes, true)) {
      json_response(false, 'Invalid file type detected. Please ensure your resume is an authentic PDF, DOC, or DOCX file.');
  }
  ```

### E. Database Error Sanitization (`api/apply.php`)
- **Old Behavior**: Returned `$e->getMessage()` to client in JSON response.
- **Implemented Fix**: Replaced with generic message: `"Unable to store your application at this time. Please try submitting again or email hr@vortexsoftinnovations.com."` while preserving full stack trace in `error_log()`.

### F. Dynamic Webhook Configuration (`admin/webhook_deploy.php`)
- **Old Behavior**: Sourced token from static string in source code.
- **Implemented Fix**: Loads token dynamically from `config/.env` (`DEPLOY_TOKEN`) using timing-safe comparison (`hash_equals`).

---

## 3. Deployment & Live Verification

All modified files were deployed to Hostinger via `deploy-ftp.ps1` and verified via automated HTTP requests. Public exposure test confirmed `HTTP 403 Forbidden` on `admin/setup_db.php`.
