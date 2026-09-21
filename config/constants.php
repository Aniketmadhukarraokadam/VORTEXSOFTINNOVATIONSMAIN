<?php
/**
 * Vortexsoft Innovations — Global Constants
 */

// ── Dynamic Site Identity & Domain Resolution ──────────────
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$raw_host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : 'www.vortexsoftinnovations.com';
$clean_domain = preg_replace('/^www\./', '', $raw_host);

define('SITE_NAME',    'Vortexsoft Innovations Private Limited');
define('SITE_DOMAIN',  $clean_domain);
define('SITE_HOST',    $raw_host);
define('SITE_URL',     $protocol . $raw_host);
define('SITE_TAGLINE', 'Your Global AI, IT & BPO Partner');
define('PRIMARY_COM_URL', 'https://www.vortexsoftinnovations.com');
define('PRIMARY_IN_URL',  'https://www.vortexsoftinnovations.in');

// ── Email Addresses ────────────────────────────────────────
define('EMAIL_SUPPORT',   'support@vortexsoftinnovations.com');
define('EMAIL_INFO',      'support@vortexsoftinnovations.com');
define('EMAIL_HR',        'careers@vortexsoftinnovations.com');
define('EMAIL_CAREERS',   'careers@vortexsoftinnovations.com');
define('EMAIL_NO_REPLY',  'no-reply@vortexsoftinnovations.com');
define('EMAIL_IT',        'support@vortexsoftinnovations.com');
define('EMAIL_CONTACT',   'support@vortexsoftinnovations.com');
define('EMAIL_FROM_NAME', 'Vortexsoft Innovations Private Limited');


// ── Phone Numbers ──────────────────────────────────────────
define('PHONE_INDIA', '+91-8308906690');
// PHONE_USA removed — V2: USA number no longer displayed on public UI

// ── Social Media ───────────────────────────────────────────
define('SOCIAL_FACEBOOK',  'https://www.facebook.com/profile.php?id=61575505273718');
define('SOCIAL_INSTAGRAM', 'https://www.instagram.com/vortexsoft_innovations');
define('SOCIAL_LINKEDIN',  'https://www.linkedin.com/company/vortexsoft-innovations-private-limited/');
define('SOCIAL_WHATSAPP_SERVICES', 'https://wa.me/918308906690?text=Hello%20Vortexsoft%20Innovations%20Sales%20Team%2C%20I%20am%20looking%20for%20business%20services%20(IT%20%2F%20BPO%20%2F%20AI).%20I%20would%20like%20to%20discuss%20our%20project%20requirements%20and%20request%20a%20quote.');
define('SOCIAL_WHATSAPP_CAREERS',  'https://wa.me/918308906690?text=Hello%20Vortexsoft%20Innovations%20HR%20Team%2C%20I%20am%20a%20job%20candidate%20inquiring%20about%20career%20opportunities%20and%20openings.%20I%20would%20like%20to%20share%20my%20profile%20for%20review.');
define('SOCIAL_WHATSAPP',          SOCIAL_WHATSAPP_SERVICES);

// ── Company Info ───────────────────────────────────────────
define('COMPANY_YEAR',       '2020');
define('COMPANY_EXPERIENCE', '6+');   // V2: Updated from 5+ to 6+ years
define('COMPANY_CLIENTS',    '150+');
define('COMPANY_PROJECTS',   '200+');
define('COMPANY_TEAM',       '200+');

// ── Paths ──────────────────────────────────────────────────
define('ROOT_PATH', dirname(__DIR__));
define('ASSETS_URL', SITE_URL . '/assets');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('UPLOADS_URL',  SITE_URL . '/uploads');

// ── Admin Settings ─────────────────────────────────────────
define('ADMIN_EMAIL',    EMAIL_SUPPORT);
define('ADMIN_SESSION',  'vortex_admin_logged_in');
define('ADMIN_USER_KEY', 'vortex_admin_id');

// ── Pagination ─────────────────────────────────────────────
define('ITEMS_PER_PAGE', 15);

// ── AI Engine Defaults (Admin Blog & Image Generator) ────────
// IMPORTANT: Set GEMINI_API_KEY in config/.env — get a free key at https://aistudio.google.com/apikey
// Gemini now supports Google's newer authentication-key format (including AQ.* keys).
define('DEFAULT_GEMINI_API_KEY', '');  // Leave empty — key must be set in .env
define('DEFAULT_GEMINI_MODEL',   'gemini-3.6-flash');
define('DEFAULT_GROQ_API_KEY',   '');
define('DEFAULT_GROQ_MODEL',     'llama-3.3-70b-versatile');

// ── Environment ────────────────────────────────────────────
define('APP_ENV', 'production'); // 'development' | 'production'
define('DEBUG_MODE', false);

if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
