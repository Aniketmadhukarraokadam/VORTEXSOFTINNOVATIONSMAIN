# Configuration Reference & Environment Variables
**Vortexsoft Innovations Private Limited**  
*Document Version: 2.0.0 — Configuration Architecture*  
*Date of Publication: October 27, 2026*

---

## 1. Overview

Application configuration is organized into two primary tiers:
1. **Private Environment Variables (`config/.env`)**: Credentials, database passwords, FTP keys, API secrets. Excluded from version control and blocked from public web access.
2. **Global Application Constants (`config/constants.php`)**: Company profiles, public contact info, branding metadata, environment mode settings.

---

## 2. Environment Variables Specification (`config/.env`)

| Variable | Required | Description | Example |
| :--- | :--- | :--- | :--- |
| `DB_HOST` | Yes | Database hostname or IP | `localhost` |
| `DB_NAME` | Yes | Database schema name | `u696371114_vortexsoftcom` |
| `DB_USER` | Yes | Database user account | `u696371114_Vortexsoftcom` |
| `DB_PASS` | Yes | Database user password | `********` |
| `FTP_HOST` | For deploy | Hostinger FTP IP / host | `82.180.143.14` |
| `FTP_USER` | For deploy | Hostinger FTP user | `u696371114.vortexsoftinnovations.com` |
| `FTP_PASS` | For deploy | Hostinger FTP password | `********` |
| `FTP_DIR` | For deploy | Target directory on FTP | `public_html` |
| `DEPLOY_TOKEN` | Optional | Webhook deployment secret | `VortexDeploy********` |
| `SETUP_KEY` | Optional | Setup script authorization key | `VortexSetup********` |
| `GEMINI_API_KEY`| Optional | Google Gemini API key for admin AI | `AIzaSy...` |

---

## 3. Global Constants Reference (`config/constants.php`)

| Constant | Description | Production Value |
| :--- | :--- | :--- |
| `SITE_NAME` | Full registered company name | `Vortexsoft Innovations Private Limited` |
| `SITE_DOMAIN` | Clean corporate domain name | `vortexsoftinnovations.com` |
| `PRIMARY_COM_URL`| Global production portal URL | `https://www.vortexsoftinnovations.com` |
| `PRIMARY_IN_URL` | India regional portal URL | `https://www.vortexsoftinnovations.in` |
| `EMAIL_SUPPORT` | Primary contact & support inbox | `support@vortexsoftinnovations.com` |
| `EMAIL_HR` | Talent & recruitment email | `careers@vortexsoftinnovations.com` |
| `PHONE_INDIA` | Official India corporate voice/WhatsApp | `+91-8308906690` |
| `COMPANY_YEAR` | Foundation year | `2020` |
| `APP_ENV` | Application environment state | `'production'` (`display_errors = 0`) |

---

## 4. Web Server Configuration (`.htaccess`)

The `.htaccess` file manages routing and performance:
- **`RewriteEngine On`**: Enables URL rewrites (e.g. `/careers` -> `/careers.php`).
- **`Header set Strict-Transport-Security`**: Enforces HTTPS connections.
- **`AddOutputFilterByType DEFLATE`**: Compresses text/html/css/json responses.
- **`ExpiresDefault "access plus 1 month"`**: Configures browser asset caching.
