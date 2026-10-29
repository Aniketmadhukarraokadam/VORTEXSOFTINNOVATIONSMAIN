# Known Issues, Limitations & Future Roadmap
**Vortexsoft Innovations Private Limited**  
*Document Version: 2.0.0 — Operational Status*  
*Date of Publication: October 27, 2026*

---

## 1. Current System Status

All critical defects, cybersecurity vulnerabilities, and responsive layout issues identified during the comprehensive audit have been **100% remediated, verified, and deployed to live production**.

There are currently **0 open blocking or critical defects** on the live website.

---

## 2. Low-Risk Architectural Limitations & Observations

| Item ID | Description | Impact | Mitigation / Recommendation |
| :--- | :--- | :--- | :--- |
| **OBS-01** | Content Security Policy includes `'unsafe-inline'` and `'unsafe-eval'`. | Low (CSP Warning) | Required for Bootstrap modal triggers and inline tracking snippets. In future major refactoring, migrate toward nonce-based CSP. |
| **OBS-02** | Hostinger shared hosting lacks shell/SSH access on the current basic plan. | Low (Operational constraint) | Managed effectively via FTP deployment tool (`deploy-ftp.ps1`) and admin backup utility. |
| **OBS-03** | Email sending relies on PHP `mail()` function fallback if SMTP account is not configured in `admin/email-accounts.php`. | Low (Deliverability) | Ensure active Hostinger SMTP credentials are saved in `email_accounts` table for optimal inbox delivery. |

---

## 3. Future Enhancement Roadmap

1. **Two-Factor Authentication (2FA)**: Introduce TOTP-based 2FA (Google Authenticator) for Super Admin accounts in a future release.
2. **Cloudflare CDN Integration**: Add Cloudflare Edge proxy in front of Hostinger to provide global asset caching and WAF bot defense.
3. **Automated Cron Backups**: Configure a scheduled daily cron job in Hostinger hPanel to execute `admin/backup_db.php` automatically.
