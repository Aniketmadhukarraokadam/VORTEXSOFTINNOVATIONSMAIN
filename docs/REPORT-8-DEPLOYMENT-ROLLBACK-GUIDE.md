# REPORT 8 – Safe Deployment & Rollback Guide for Hostinger

**Organization:** Vortexsoft Innovations Private Limited  
**Website:** https://www.vortexsoftinnovations.com/  
**Repository:** Aniketmadhukarraokadam/VORTEXSOFTINNOVATIONSMAIN  
**Audit Branch:** `audit-redesign-opt`  
**Date:** October 30, 2026  

---

## 1. Prerequisites & Production Safety Checklist

Before deploying changes to the live Hostinger environment:

1. **Verify Git Branch:** Ensure you are merging from `audit-redesign-opt` after final review.
2. **Current Production Backup:**
   - Take a full file backup of `/public_html` via Hostinger hPanel > **Files** > **Backups**.
   - Generate a MySQL database dump via Hostinger hPanel > **Databases** > **phpMyAdmin** > **Export**.
3. **Environment Configuration File:**
   - Ensure a secure `config/.env` exists on the server (based on `config/.env.example`) containing:
     - `DB_HOST=localhost`
     - `DB_USER=<production_db_user>`
     - `DB_PASS=<production_db_password>`
     - `DB_NAME=<production_db_name>`
     - `DEPLOY_TOKEN=<secure_random_64_char_token>`
     - `SETUP_KEY=<secure_random_setup_key>`

---

## 2. Deployment Procedures

### Method A: Git-Based Deployment (Recommended)

1. **Push Changes to GitHub:**
   ```bash
   git checkout audit-redesign-opt
   git push origin audit-redesign-opt
   ```
2. **Create & Merge Pull Request:**
   - Create a Pull Request into `main`.
   - Review file diffs and merge.
3. **Trigger Webhook or Pull on Hostinger:**
   - If using Hostinger Git deployment: Click **Deploy** in hPanel Git repository manager.
   - If using Webhook: Send POST request to `https://www.vortexsoftinnovations.com/admin/webhook_deploy.php` with `token=<DEPLOY_TOKEN>`.
   - Or pull via SSH:
     ```bash
     cd public_html
     git pull origin main
     ```

### Method B: Secure FTP / SFTP Deployment (`deploy-ftp.ps1`)

1. Set the FTP password in your local PowerShell session (do NOT hardcode in script):
   ```powershell
   $env:FTP_PASS = "YourHostingerFTPPasswordHere"
   .\deploy-ftp.ps1
   ```
2. The script uploads modified files (`index.php`, `contact.php`, `careers.php`, `includes/`, `assets/`, `api/`, `config/database.php`, `.htaccess`, subpages) while excluding `.git/`, `.env`, and local caches.

---

## 3. Database Considerations

- **Schema Stability:** This audit made **no destructive changes** to database tables.
- All existing tables (`contact_inquiries`, `job_openings`, `job_applications`, `blog_posts`, `subscribers`, `admin_users`) remain completely compatible.
- `database/setup.sql` has commented-out admin password seeding, ensuring that running schema scripts will never overwrite existing production admin credentials.

---

## 4. Post-Deployment Verification Checklist

Immediately following deployment, test the following:
- [ ] Visit `https://www.vortexsoftinnovations.com/` and confirm hero renders cleanly with corporate tokens.
- [ ] Test the primary Services mega-menu: open and inspect all 5 categories.
- [ ] Test contact form submission on `/contact.php` (verify success feedback).
- [ ] Test career portal on `/careers.php` (verify job filters and resume upload modal).
- [ ] Test a service subpage (e.g., `/accounting-services/`) and verify sidebar links navigate to `.php` endpoints.
- [ ] Confirm direct browser access to `/config/.env` or `/website_info.txt` returns `403 Forbidden`.

---

## 5. Rollback Procedure

In the unlikely event of an unexpected production issue:

### Instant Git Rollback
```bash
# On server via SSH or hPanel Git
git checkout <previous_commit_hash>
# or revert commit
git revert HEAD
git push origin main
```

### Hostinger hPanel Snapshot Rollback
1. Open Hostinger hPanel > **Files** > **Backups**.
2. Select **File Backups** > Choose the snapshot taken prior to deployment > Click **Restore**.
3. Select **Database Backups** > Restore the database backup if any data was altered.
4. Purge server cache via Hostinger hPanel > **Performance** > **Cache Manager** > **Purge All**.
