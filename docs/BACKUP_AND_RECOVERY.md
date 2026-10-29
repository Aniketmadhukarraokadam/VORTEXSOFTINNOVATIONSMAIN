# Backup, Disaster Recovery & Business Continuity Protocol
**Vortexsoft Innovations Private Limited**  
*Document Version: 2.0.0 — Continuity & Disaster Recovery*  
*Target Environment: Hostinger Shared Hosting*  
*Date of Publication: October 27, 2026*

---

## 1. Overview & Objectives

In compliance with ISO 27001:2013 and HIPAA data availability requirements, this document establishes protocols for database backups, static asset archival, point-in-time recovery, and emergency disaster response.

---

## 2. Backup Methods

### Method A: Admin Web-Based SQL Backup (`admin/backup_db.php`)
- **Execution**: Log into Admin Panel -> Database Backup -> Click "Create New Backup".
- **Storage Location**: Stored on server disk in `/backups/db_backup_[TIMESTAMP].sql`.
- **Security**: The `/backups/` directory is blocked from direct HTTP download via Apache `.htaccess` (`Order allow,deny / Deny from all`).
- **Contents**: Full SQL schema DDL and parameterized `INSERT` statements for all 11 application tables.

### Method B: Hostinger Automated Backups (hPanel)
- Hostinger maintains automated daily and weekly server snapshots accessible via Hostinger hPanel -> Backups.
- Allows 1-click restoration of all files and MySQL databases to a previous timestamp.

---

## 3. Disaster Recovery & Restoration Procedures

### Step 1: Database Restoration
1. Access phpMyAdmin via Hostinger hPanel OR access MySQL console.
2. Select target database `u696371114_vortexsoftcom`.
3. Select the latest `.sql` backup file from `/backups/`.
4. Execute SQL import.

### Step 2: Codebase & File Restoration
If a file is accidentally overwritten or corrupted:
1. Identify the affected file.
2. Use the local workspace mirror to re-upload the known clean file using `deploy-ftp.ps1 -File "<path>"`.
3. Verify live status code using `probe_endpoints.ps1`.

---

## 4. Continuity & Recovery Metrics

| Objective | Target | Verified Capability |
| :--- | :--- | :--- |
| **Recovery Time Objective (RTO)** | < 30 minutes | Database and code can be fully restored in < 15 minutes via FTP/phpMyAdmin |
| **Recovery Point Objective (RPO)** | < 24 hours | Daily Hostinger snapshots + admin on-demand backups |
