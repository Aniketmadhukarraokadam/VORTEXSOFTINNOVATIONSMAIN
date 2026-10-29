# Installation, Local Setup & Environment Guide
**Vortexsoft Innovations Private Limited**  
*Document Version: 2.0.0 — Setup Specification*  
*Date of Publication: October 27, 2026*

---

## 1. Prerequisites & System Requirements

- **PHP**: Version 8.1 or higher (with `pdo_mysql`, `curl`, `openssl`, `mbstring`, `fileinfo`, `gd`, `zip` extensions).
- **Web Server**: Apache 2.4+ (with `mod_rewrite`, `mod_headers`, `mod_deflate`, `mod_expires` enabled).
- **Database**: MySQL 8.0+ or MariaDB 10.4+.
- **FTP Client / Terminal**: PowerShell 5.1+ or cURL on Windows/macOS/Linux.

---

## 2. Local Development Setup

### Step 1: Clone Repository
```bash
git clone https://github.com/Aniketmadhukarraokadam/VORTEXSOFTINNOVATIONSMAIN.git
cd VORTEXSOFTINNOVATIONSMAIN
```

### Step 2: Configure Environment Variables
Copy the template configuration file:
```bash
cp config/.env.example config/.env
```
Open `config/.env` in your text editor and set your local database parameters:
```ini
DB_HOST=localhost
DB_NAME=vortexsoft_local
DB_USER=root
DB_PASS=your_password
```

### Step 3: Initialize Database Schema
Import the baseline schema from `database/setup.sql`:
```bash
mysql -u root -p vortexsoft_local < database/setup.sql
```

### Step 4: Launch Local Development Server
You can launch the built-in PHP development server using the bundled runner or directly:
```bash
php -S localhost:8000 router.php
```
Visit `http://localhost:8000` in your web browser.

---

## 3. Production Deployment to Hostinger

Production deployments are automated via the dedicated script [`deploy-ftp.ps1`](file:///c:/Users/anike/OneDrive/Documents/vortexsoft%20website/VORTEXSOFTINNOVATIONSMAIN/deploy-ftp.ps1).

### Verify Configuration
Ensure Hostinger FTP credentials exist in `config/.env`:
```ini
FTP_HOST=82.180.143.14
FTP_USER=u696371114.vortexsoftinnovations.com
FTP_PASS=********
FTP_DIR=public_html
```

### Deploy to Production
- **Deploy Specific File**:
  ```powershell
  powershell -ExecutionPolicy Bypass -File .\deploy-ftp.ps1 -File "admin/login.php"
  ```
- **Preview Modified Files**:
  ```powershell
  powershell -ExecutionPolicy Bypass -File .\deploy-ftp.ps1 -DryRun
  ```
- **Deploy All Modified Files**:
  ```powershell
  powershell -ExecutionPolicy Bypass -File .\deploy-ftp.ps1 -Mode Modified
  ```
