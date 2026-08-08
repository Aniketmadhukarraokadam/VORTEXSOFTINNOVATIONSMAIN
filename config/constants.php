<?php
/**
 * Vortexsoft Innovations — Global Constants
 */

// ── Site Identity ──────────────────────────────────────────
define('SITE_NAME',    'Vortexsoft Group');
define('SITE_URL',     'https://www.vortexsoftinnovations.com');
define('SITE_DOMAIN',  'vortexsoftinnovations.com');
define('SITE_TAGLINE', 'Your Global AI, IT & BPO Partner');

// ── Email Addresses ────────────────────────────────────────
define('EMAIL_SUPPORT',   'support@vortexsoftinnovations.com');
define('EMAIL_INFO',      'info@vortexsoftinnovations.in');
define('EMAIL_HR',        'careers@vortexsoftinnovations.in');
define('EMAIL_IT',        'it@vortexsoftinnovations.com');
define('EMAIL_CONTACT',   'contact@vortexsoftinnovations.in');
define('EMAIL_FROM_NAME', 'Vortexsoft Group');

// ── Phone Numbers ──────────────────────────────────────────
define('PHONE_INDIA', '+91-8308906690');
define('PHONE_USA',   '+1-307-205-0681');

// ── Social Media ───────────────────────────────────────────
define('SOCIAL_FACEBOOK',  'https://www.facebook.com/profile.php?id=61575505273718');
define('SOCIAL_INSTAGRAM', 'https://www.instagram.com/vortexsoft_innovations');
define('SOCIAL_LINKEDIN',  'https://www.linkedin.com/company/vortexsoft-innovations-private-limited/');
define('SOCIAL_TWITTER',   'https://twitter.com/vortexsoft');
define('SOCIAL_WHATSAPP',  'https://wa.me/918308906690?text=Hello%20Vortexsoft%20Innovations%2C%20I%20would%20like%20to%20know%20more%20about%20your%20services.');

// ── Company Info ───────────────────────────────────────────
define('COMPANY_YEAR',     '2020');
define('COMPANY_CLIENTS',  '150+');
define('COMPANY_PROJECTS', '200+');
define('COMPANY_TEAM',     '200+');

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
