# Production Safety Plan & Risk Mitigation Protocol
**Vortexsoft Innovations Private Limited**  
*Document Version: 2.0.0 — Production Safety Protocol*  
*Environment: Hostinger Shared Hosting (Live Production: `www.vortexsoftinnovations.com`)*  
*Enforcement Date: October 27, 2026*

---

## 1. Safety Principles & Objectives

The primary directive of this project is to **maintain, secure, and optimize the live website without causing downtime, regressions, or data loss**. Rebuilding the system or introducing disruptive framework changes is strictly prohibited.

---

## 2. Mandatory Rules of Engagement

1. **Zero Downtime Constraint**:
   - Edits must be deployed file-by-file or in cohesive, tested batches using differential FTP sync.
   - Core configuration files (`config/constants.php`, `config/database.php`, `.htaccess`) must be verified for syntax accuracy prior to deployment.
2. **Data & Secret Protection**:
   - Production secrets (`.env`, database passwords, FTP keys, API tokens) must never be committed to Git or published in client-accessible directories.
   - All environment files are restricted at the web-server level via `.htaccess` (`<FilesMatch "^\.env"> Require all denied </FilesMatch>`).
3. **Database Integrity Protection**:
   - Destructive SQL statements (`DROP DATABASE`, `TRUNCATE TABLE`, `DROP TABLE`) are strictly prohibited in production routines.
   - Any database table adjustment must be additive (`ADD COLUMN IF NOT EXISTS`) and tested against a recovery point.
4. **Reversible Atomic Batches**:
   - For every file modified, a pre-change snapshot is retained in version control or backup directory.
   - Deployments must utilize `deploy-ftp.ps1 -File "<relative-path>"` for granular, verifiable updates.
5. **Session & Access Continuity**:
   - Authentication changes must preserve active sessions and maintain access for verified administrators.

---

## 3. Pre-Change Verification Checklist

Before deploying any patch to Hostinger production:
- [x] **Local Inspection**: Read file diff and ensure no syntax or typing errors exist.
- [x] **Secret Audit**: Confirm no cleartext passwords, tokens, or personal identifiers are added.
- [x] **Backward Compatibility**: Ensure all legacy endpoints and database queries remain fully operational.
- [x] **Dry-Run Check**: Execute deployment tool in dry-run mode or target specific file only (`-File <path>`).
- [x] **Post-Deployment Probe**: Verify HTTP status code (200 OK) and DOM integrity on live server immediately after upload.

---

## 4. Emergency Rollback Protocol

If an unexpected regression, HTTP 500 Internal Server Error, or unexpected behavior is detected after an update:

### Step 1: Immediate Diagnostic Isolation
Execute diagnostic probe to isolate the affected endpoint:
```powershell
powershell -ExecutionPolicy Bypass -File .\scratch\probe_endpoints.ps1
```

### Step 2: Restore from Local Mirror / Previous Revision
Re-upload the known-stable file immediately via FTP deployment:
```powershell
powershell -ExecutionPolicy Bypass -File .\deploy-ftp.ps1 -File "<path-to-file>"
```

### Step 3: Database Rollback (if schema change was made)
If a database regression occurs:
1. Access Hostinger hPanel -> Databases -> phpMyAdmin OR use `admin/backup_db.php`.
2. Locate the timestamped backup in `/backups/db_backup_[TIMESTAMP].sql`.
3. Restore tables using non-destructive restore queries.

### Step 4: Cache Flush
Clear Hostinger LiteSpeed cache via hPanel or by requesting the URL with cache-busting query parameter (`?v=[TIMESTAMP]`).

---

## 5. Summary of Emergency Contacts & Escalation

- **Technical Lead**: Aniket Madhukarrao Kadam
- **Primary Hosting Platform**: Hostinger International Ltd. (hPanel Support)
- **Emergency Support Portal**: `support@vortexsoftinnovations.com`
- **WhatsApp Ops Desk**: `+91-8308906690`
