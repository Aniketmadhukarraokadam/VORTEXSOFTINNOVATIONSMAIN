# Production Troubleshooting & Diagnostics Guide
**Vortexsoft Innovations Private Limited**  
*Document Version: 2.0.0 — Diagnostic Protocols*  
*Date of Publication: October 27, 2026*

---

## 1. Quick Diagnostic Triaging

When an issue occurs, run the diagnostic probe from the repository root:
```powershell
powershell -ExecutionPolicy Bypass -File .\scratch\probe_endpoints.ps1
```

---

## 2. Common Issues & Solutions

### A. Database Connection Failure (`Database connection failed`)
- **Symptoms**: Inquiries or career forms return "Database service is temporarily unavailable".
- **Root Causes**:
  - `config/.env` missing or containing incorrect MySQL credentials.
  - MySQL database server restarted or hitting maximum connection limits.
- **Remediation**:
  1. Inspect Hostinger hPanel -> Databases to ensure `u696371114_vortexsoftcom` is online.
  2. Verify database username (`u696371114_Vortexsoftcom`) and password in `config/.env`.
  3. Verify Hostinger database host is set to `localhost`.

### B. Admin Login Lockout (`Too many failed attempts`)
- **Symptoms**: Login page displays "Too many failed attempts. This IP is locked out for 15 minutes."
- **Root Cause**: 5 consecutive failed login attempts recorded for the client IP in the temporary lockout file (`vx_bf_[MD5].json`).
- **Remediation**:
  - Wait 15 minutes for the automated timer to expire, OR
  - Clear the temporary lockout file from the server's temp directory or log in from an alternate network/IP.

### C. File Upload Fails in Career Application
- **Symptoms**: Form displays "Failed to save uploaded resume" or "Invalid file type detected".
- **Root Causes**:
  - File is not an authentic PDF, DOC, or DOCX document (fails MIME binary check).
  - File exceeds the 5MB size limit.
  - `uploads/resumes/` directory lacks write permissions on the Hostinger server.
- **Remediation**:
  1. Check file size is under 5MB.
  2. Verify directory permissions on Hostinger: `uploads/resumes/` must have octal `755`.

### D. FTP Deployment Fails (`Could not authenticate or reach`)
- **Symptoms**: `deploy-ftp.ps1` outputs `[FAILED] Could not authenticate`.
- **Root Causes**:
  - Local machine internet interruption.
  - FTP password or IP changed in Hostinger hPanel.
- **Remediation**:
  1. Check internet connectivity.
  2. Log into Hostinger hPanel -> Files -> FTP Accounts and verify the host IP (`82.180.143.14`) and user password.
