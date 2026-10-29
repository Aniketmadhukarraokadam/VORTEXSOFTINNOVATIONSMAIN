# Hostinger Deployment Guide & Production Environment Reference
**Vortexsoft Innovations Private Limited**  
*Document Version: 2.0.0 — Production Hosting Specification*  
*Hosting Provider: Hostinger International Ltd.*  
*Primary Production Domain: [https://www.vortexsoftinnovations.com](https://www.vortexsoftinnovations.com)*  
*Secondary Domain: [https://www.vortexsoftinnovations.in](https://www.vortexsoftinnovations.in)*

---

## 1. Verified Infrastructure Overview

| Parameter | Production Value | Verified Configuration Notes |
| :--- | :--- | :--- |
| **Hosting Tier** | Hostinger Shared Premium Web Hosting | Multi-tenant isolated Linux environment with LiteSpeed web server modules and Apache `.htaccess` emulation. |
| **Server IP Address** | `82.180.143.14` | Hostinger European/Global Edge Datacenter node. |
| **Web Root Directory** | `/public_html` | Standard Hostinger web root hosting the complete PHP application. |
| **Database Host** | `localhost` / `127.0.0.1` | Local MySQL (MariaDB 10.11+) accessible strictly via local UNIX socket / localhost network. |
| **Database Name** | `u696371114_vortexsoftcom` | Production database schema containing inquiries, jobs, applications, blogs, and admin tables. |
| **Database User** | `u696371114_Vortexsoftcom` | Case-sensitive MySQL user credential with least-privilege DML/DDL permissions. |
| **PHP Runtime** | PHP 8.1.x / 8.2.x | Configured with `pdo_mysql`, `curl`, `openssl`, `mbstring`, `fileinfo`, `gd`, `zip` extensions enabled. |
| **SSL/TLS Certificates** | Free Automatic Let's Encrypt / Google Trust | Auto-renewing TLS certificates managed through Hostinger hPanel. |

---

## 2. Production Deployment Architecture

Deployments are executed using the dedicated PowerShell FTP synchronization utility:
[`deploy-ftp.ps1`](file:///c:/Users/anike/OneDrive/Documents/vortexsoft%20website/VORTEXSOFTINNOVATIONSMAIN/deploy-ftp.ps1)

### Advantages of the Established Deployment Pipeline:
1. **Granular Single-File Sync**: Enables atomic deployments of isolated bug fixes without full site redeployment.
2. **Exclusion Filters**: Automatically ignores private files, git metadata, and local development logs:
   - Excludes `.git/`, `.github/`, `config/.env`, `backups/`, `uploads/resumes/`, `README.md`, `*.log`, `*.tmp`.
3. **Connection Verification**: Validates FTP credentials and server reachability before initiating any file transfer.

---

## 3. Operational Deployment Commands

### A. Deploy a Single Modified File (Recommended & Safest)
```powershell
powershell -ExecutionPolicy Bypass -File .\deploy-ftp.ps1 -File "admin/applications.php"
```

### B. Preview Changes via Dry-Run (Read-Only)
```powershell
powershell -ExecutionPolicy Bypass -File .\deploy-ftp.ps1 -DryRun
```

### C. Deploy Only Modified Working Files
```powershell
powershell -ExecutionPolicy Bypass -File .\deploy-ftp.ps1 -Mode Modified
```

---

## 4. File Permissions & Security Configuration

On Hostinger Linux hosting, permissions must be strictly maintained:

| Directory / File Type | Recommended Octal Permission | Security Rationale |
| :--- | :--- | :--- |
| **Standard Directories** | `755` (`drwxr-xr-x`) | Allows web server execution and browsing traversal without public write privileges. |
| **Standard PHP / Static Files** | `644` (`-rw-r--r--`) | Allows web server read access; prevents unauthorized modification. |
| **`config/.env`** | `600` (`-rw-------`) + `.htaccess` blocked | Never publicly readable over HTTP. |
| **`uploads/resumes/`** | `755` + `.htaccess` `deny from all` | Resumes are kept private; served strictly via `admin/download.php`. |
| **`backups/`** | `755` + `.htaccess` `deny from all` | Prevents unauthorized downloads of database dumps. |

---

## 5. Web Server Configuration (`.htaccess`)

The root [`.htaccess`](file:///c:/Users/anike/OneDrive/Documents/vortexsoft%20website/VORTEXSOFTINNOVATIONSMAIN/.htaccess) enforces the following critical directives:
1. **HTTPS & WWW Enforcement**:
   ```apache
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

   RewriteCond %{HTTP_HOST} !^www\. [NC]
   RewriteCond %{HTTP_HOST} !^localhost [NC]
   RewriteRule ^(.*)$ https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```
2. **Directory & File Denials**:
   ```apache
   RewriteRule ^config/.*$          - [F,L]
   RewriteRule ^includes/.*$        - [F,L]
   RewriteRule ^database/.*$        - [F,L]
   RewriteRule ^backups/.*$         - [F,L]
   RewriteRule ^uploads/resumes/.*$ - [F,L]
   ```
3. **HTTP Compression & Asset Expiration**:
   Enables `mod_deflate` for HTML, CSS, JavaScript, JSON, SVG and sets 1-year cache headers for static media.

---

## 6. Post-Deployment Smoke Test Protocol

Immediately after executing an FTP deployment:
1. Run automated probe script:
   ```powershell
   powershell -ExecutionPolicy Bypass -File .\scratch\probe_endpoints.ps1
   ```
2. Inspect server headers:
   Verify `HTTP/1.1 200 OK` and ensure no `X-Powered-By` header is emitted.
3. Verify critical workflows:
   - Load `/careers.php` in a clean browser session.
   - Test a test contact inquiry submission on `/contact.php`.
   - Verify administrative login on `/admin/login.php`.
