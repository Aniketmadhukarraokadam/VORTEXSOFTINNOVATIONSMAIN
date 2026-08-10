<?php
/**
 * Vortexsoft Innovations — Automatic Web Database & Webhook Installer
 * Access in browser: https://vortexsoftinnovations.com/admin/setup_db.php
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: text/html; charset=utf-8');

// 1. Update webhook_deploy.php on server
$webhookCode = <<<'PHP'
<?php
define('DEPLOY_TOKEN', 'VortexDeploy6498286f401141b8');
define('GITHUB_USER',   'Aniketmadhukarraokadam');
define('GITHUB_REPO',   'VORTEXSOFTINNOVATIONSMAIN');
define('GITHUB_BRANCH', 'main');
define('PUBLIC_HTML',   dirname(__DIR__));

header('Content-Type: application/json');

$receivedToken = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? $_GET['token'] ?? '';
if (!hash_equals(DEPLOY_TOKEN, $receivedToken)) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized']));
}

$tempDir = PUBLIC_HTML . '/uploads/temp';
if (!is_dir($tempDir)) {
    @mkdir($tempDir, 0777, true);
}

function downloadFile($url, $dest) {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        $fp = fopen($dest, 'w+');
        if ($fp) {
            curl_setopt_array($ch, [
                CURLOPT_FILE           => $fp,
                CURLOPT_TIMEOUT        => 120,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 5,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) VortexDeploy/1.0',
            ]);
            $exec = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            fclose($fp);
            if ($exec && $httpCode >= 200 && $httpCode < 300 && filesize($dest) > 1000) {
                return true;
            }
        }
    }
    $ctx = stream_context_create([
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
        'http' => ['header' => "User-Agent: VortexDeploy/1.0\r\n"]
    ]);
    $data = @file_get_contents($url, false, $ctx);
    if ($data !== false && strlen($data) > 1000) {
        return @file_put_contents($dest, $data) !== false;
    }
    return false;
}

$zipUrl  = "https://codeload.github.com/" . GITHUB_USER . "/" . GITHUB_REPO . "/zip/refs/heads/" . GITHUB_BRANCH;
$tempZip = $tempDir . '/deploy_' . time() . '.zip';

if (!downloadFile($zipUrl, $tempZip)) {
    $zipUrl = "https://github.com/" . GITHUB_USER . "/" . GITHUB_REPO . "/archive/refs/heads/" . GITHUB_BRANCH . ".zip";
    if (!downloadFile($zipUrl, $tempZip)) {
        http_response_code(500);
        die(json_encode(['error' => 'Failed to download repository ZIP from GitHub']));
    }
}

$extractDir = $tempDir . '/extract_' . time();
$zip = new ZipArchive();
if ($zip->open($tempZip) !== true) {
    @unlink($tempZip);
    http_response_code(500);
    die(json_encode(['error' => 'Failed to open ZIP archive']));
}

$zip->extractTo($extractDir);
$zip->close();
@unlink($tempZip);

$sourceDir = $extractDir . '/' . GITHUB_REPO . '-' . GITHUB_BRANCH;
if (!$sourceDir || !is_dir($sourceDir)) {
    $dirs = glob($extractDir . '/*', GLOB_ONLYDIR);
    $sourceDir = $dirs[0] ?? null;
}

if (!$sourceDir || !is_dir($sourceDir)) {
    http_response_code(500);
    die(json_encode(['error' => 'Could not find extracted repository folder']));
}

$skip = ['.git', '.github', 'README.md', 'package.json', 'vortexsoft_website_details.txt', 'website_info.txt'];

function syncDir($src, $dst, $skip = []) {
    if (!is_dir($dst)) @mkdir($dst, 0755, true);
    $count = 0;
    foreach (scandir($src) as $item) {
        if ($item === '.' || $item === '..' || in_array($item, $skip)) continue;
        $s = "$src/$item";
        $d = "$dst/$item";
        if (is_dir($s)) {
            $count += syncDir($s, $d);
        } else {
            @copy($s, $d);
            $count++;
        }
    }
    return $count;
}

$filesCopied = syncDir($sourceDir, PUBLIC_HTML, $skip);

function cleanupDir($dir) {
    if (!is_dir($dir)) return;
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $p = "$dir/$f";
        is_dir($p) ? cleanupDir($p) : @unlink($p);
    }
    @rmdir($dir);
}
cleanupDir($extractDir);

@file_put_contents(PUBLIC_HTML . '/admin/deploy.log', date('Y-m-d H:i:s') . " - Auto-deploy success ($filesCopied files)\n", FILE_APPEND);

echo json_encode([
    'success'      => true,
    'message'      => 'Auto-deploy completed successfully',
    'files_copied' => $filesCopied,
    'timestamp'    => date('Y-m-d H:i:s')
]);
PHP;

@file_put_contents(__DIR__ . '/webhook_deploy.php', $webhookCode);

$db = getDB();
if (!$db) {
    die("
    <div style='font-family:sans-serif;max-width:600px;margin:50px auto;padding:30px;background:#fff5f5;border:1px solid #fed7d7;border-radius:12px;color:#9b2c2c;'>
        <h2>❌ Database Connection Failed</h2>
        <p>Could not connect to MySQL server database <code>u696371114_vortexsoftcom</code>. Please verify database credentials in <code>config/database.php</code>.</p>
    </div>");
}

$queries = [
    "SET NAMES utf8mb4;",
    "CREATE TABLE IF NOT EXISTS `contact_inquiries` (
      `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `name`         VARCHAR(120) NOT NULL,
      `email`        VARCHAR(180) NOT NULL,
      `phone`        VARCHAR(30)  DEFAULT NULL,
      `service`      VARCHAR(120) DEFAULT 'General Inquiry',
      `company`      VARCHAR(120) DEFAULT NULL,
      `message`      TEXT         NOT NULL,
      `ip_address`   VARCHAR(45)  DEFAULT NULL,
      `user_agent`   VARCHAR(255) DEFAULT NULL,
      `source_page`  VARCHAR(255) DEFAULT NULL,
      `is_read`      TINYINT(1)   NOT NULL DEFAULT 0,
      `is_replied`   TINYINT(1)   NOT NULL DEFAULT 0,
      `notes`        TEXT         DEFAULT NULL,
      `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      INDEX `idx_email`      (`email`),
      INDEX `idx_is_read`    (`is_read`),
      INDEX `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    "CREATE TABLE IF NOT EXISTS `job_applications` (
      `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
      `job_title`       VARCHAR(200)  NOT NULL,
      `department`      VARCHAR(100)  DEFAULT NULL,
      `applicant_name`  VARCHAR(120)  NOT NULL,
      `email`           VARCHAR(180)  NOT NULL,
      `phone`           VARCHAR(30)   DEFAULT NULL,
      `current_location`VARCHAR(120)  DEFAULT NULL,
      `experience_years`DECIMAL(4,1)  DEFAULT NULL,
      `current_company` VARCHAR(120)  DEFAULT NULL,
      `notice_period`   VARCHAR(60)   DEFAULT NULL,
      `expected_ctc`    VARCHAR(60)   DEFAULT NULL,
      `resume_filename` VARCHAR(255)  DEFAULT NULL,
      `resume_path`     VARCHAR(500)  DEFAULT NULL,
      `cover_letter`    TEXT          DEFAULT NULL,
      `linkedin_url`    VARCHAR(300)  DEFAULT NULL,
      `portfolio_url`   VARCHAR(300)  DEFAULT NULL,
      `ip_address`      VARCHAR(45)   DEFAULT NULL,
      `status`          ENUM('new','reviewed','shortlisted','interview','offered','rejected','withdrawn')
                                      NOT NULL DEFAULT 'new',
      `admin_notes`     TEXT          DEFAULT NULL,
      `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      INDEX `idx_email`      (`email`),
      INDEX `idx_status`     (`status`),
      INDEX `idx_job_title`  (`job_title`),
      INDEX `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    "CREATE TABLE IF NOT EXISTS `blog_posts` (
      `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `title`        VARCHAR(300) NOT NULL,
      `slug`         VARCHAR(320) NOT NULL,
      `excerpt`      VARCHAR(500) DEFAULT NULL,
      `content`      LONGTEXT     NOT NULL,
      `author`       VARCHAR(100) NOT NULL DEFAULT 'Vortexsoft Team',
      `author_role`  VARCHAR(100) DEFAULT NULL,
      `cover_image`  VARCHAR(500) DEFAULT NULL,
      `category`     VARCHAR(100) NOT NULL DEFAULT 'General',
      `tags`         VARCHAR(500) DEFAULT NULL,
      `meta_title`   VARCHAR(300) DEFAULT NULL,
      `meta_desc`    VARCHAR(500) DEFAULT NULL,
      `views`        INT UNSIGNED NOT NULL DEFAULT 0,
      `is_published` TINYINT(1)   NOT NULL DEFAULT 0,
      `is_featured`  TINYINT(1)   NOT NULL DEFAULT 0,
      `published_at` DATETIME     DEFAULT NULL,
      `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uk_slug`         (`slug`),
      INDEX `idx_category`         (`category`),
      INDEX `idx_is_published`     (`is_published`),
      INDEX `idx_published_at`     (`published_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    "CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
      `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `email`          VARCHAR(180) NOT NULL,
      `name`           VARCHAR(120) DEFAULT NULL,
      `ip_address`     VARCHAR(45)  DEFAULT NULL,
      `is_active`      TINYINT(1)   NOT NULL DEFAULT 1,
      `unsubscribe_token` VARCHAR(64) DEFAULT NULL,
      `subscribed_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `unsubscribed_at`DATETIME     DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uk_email` (`email`),
      INDEX `idx_is_active` (`is_active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    "CREATE TABLE IF NOT EXISTS `admin_users` (
      `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `username`     VARCHAR(60)  NOT NULL,
      `password_hash`VARCHAR(255) NOT NULL,
      `email`        VARCHAR(180) NOT NULL,
      `full_name`    VARCHAR(120) DEFAULT NULL,
      `role`         ENUM('super_admin','admin','viewer') NOT NULL DEFAULT 'admin',
      `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
      `last_login`   DATETIME     DEFAULT NULL,
      `login_count`  INT UNSIGNED NOT NULL DEFAULT 0,
      `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uk_username` (`username`),
      UNIQUE KEY `uk_email`    (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    "CREATE TABLE IF NOT EXISTS `system_settings` (
      `setting_key`   VARCHAR(100) NOT NULL,
      `setting_value` TEXT         DEFAULT NULL,
      `updated_at`    DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`setting_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    "CREATE TABLE IF NOT EXISTS `jobs` (
      `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `title`            VARCHAR(200) NOT NULL,
      `department`       VARCHAR(100) NOT NULL,
      `type`             VARCHAR(60)  NOT NULL DEFAULT 'Full Time',
      `location`         VARCHAR(120) NOT NULL,
      `experience_range` VARCHAR(60)  DEFAULT NULL,
      `skills_json`      TEXT         DEFAULT NULL,
      `description`      TEXT         NOT NULL,
      `is_urgent`        TINYINT(1)   NOT NULL DEFAULT 0,
      `is_active`        TINYINT(1)   NOT NULL DEFAULT 1,
      `sort_order`       INT UNSIGNED NOT NULL DEFAULT 0,
      `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      INDEX `idx_is_active`  (`is_active`),
      INDEX `idx_sort_order` (`sort_order`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    "CREATE TABLE IF NOT EXISTS `email_accounts` (
      `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `email_address`     VARCHAR(180) NOT NULL,
      `display_name`      VARCHAR(120) DEFAULT 'Vortexsoft Group',
      `provider`          VARCHAR(60)  DEFAULT 'Hostinger / Custom SMTP',
      `smtp_host`         VARCHAR(180) DEFAULT NULL,
      `smtp_port`         INT UNSIGNED DEFAULT 587,
      `smtp_encryption`   VARCHAR(10)  DEFAULT 'tls',
      `smtp_username`     VARCHAR(180) DEFAULT NULL,
      `smtp_password_enc` TEXT         DEFAULT NULL,
      `imap_host`         VARCHAR(180) DEFAULT NULL,
      `imap_port`         INT UNSIGNED DEFAULT 993,
      `imap_encryption`   VARCHAR(10)  DEFAULT 'ssl',
      `imap_username`     VARCHAR(180) DEFAULT NULL,
      `imap_password_enc` TEXT         DEFAULT NULL,
      `is_active`         TINYINT(1)   NOT NULL DEFAULT 1,
      `is_default`        TINYINT(1)   NOT NULL DEFAULT 0,
      `created_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uk_email` (`email_address`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    "CREATE TABLE IF NOT EXISTS `email_logs` (
      `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `account_id`    INT UNSIGNED DEFAULT NULL,
      `type`          ENUM('outgoing','incoming') NOT NULL DEFAULT 'outgoing',
      `sender`        VARCHAR(180) NOT NULL,
      `recipient`     VARCHAR(180) NOT NULL,
      `subject`       VARCHAR(255) NOT NULL,
      `body_html`     MEDIUMTEXT   DEFAULT NULL,
      `status`        ENUM('queued','sending','sent','failed','bounced') NOT NULL DEFAULT 'sent',
      `error_message` TEXT         DEFAULT NULL,
      `retry_count`   INT UNSIGNED NOT NULL DEFAULT 0,
      `last_retry_at` DATETIME     DEFAULT NULL,
      `related_type`  VARCHAR(50)  DEFAULT NULL,
      `related_id`    INT UNSIGNED DEFAULT NULL,
      `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      INDEX `idx_status` (`status`),
      INDEX `idx_type`   (`type`),
      INDEX `idx_created` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    "CREATE TABLE IF NOT EXISTS `email_templates` (
      `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `template_key`   VARCHAR(80)  NOT NULL,
      `name`           VARCHAR(120) NOT NULL,
      `subject`        VARCHAR(255) NOT NULL,
      `body_html`      MEDIUMTEXT   NOT NULL,
      `variables_json` TEXT         DEFAULT NULL,
      `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uk_template_key` (`template_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    "CREATE TABLE IF NOT EXISTS `admin_activity_logs` (
      `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `admin_id`       INT UNSIGNED DEFAULT NULL,
      `admin_username` VARCHAR(80)  NOT NULL,
      `action`         VARCHAR(100) NOT NULL,
      `details`        TEXT         DEFAULT NULL,
      `ip_address`     VARCHAR(45)  NOT NULL,
      `user_agent`     VARCHAR(255) DEFAULT NULL,
      `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      INDEX `idx_action` (`action`),
      INDEX `idx_created` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
];

$is_sqlite = ($db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite');

foreach ($queries as $q) {
    try {
        if ($is_sqlite) {
            $q = preg_replace('/ENGINE=InnoDB[^;]*/i', '', $q);
            $q = str_replace('INT UNSIGNED NOT NULL AUTO_INCREMENT', 'INTEGER PRIMARY KEY AUTOINCREMENT', $q);
            $q = str_replace('AUTO_INCREMENT', 'INTEGER PRIMARY KEY AUTOINCREMENT', $q);
            $q = preg_replace('/ENUM\([^)]+\)/i', 'TEXT', $q);
            $q = preg_replace('/INDEX `[^`]+` \([^)]+\),?/i', '', $q);
            $q = preg_replace('/UNIQUE KEY `[^`]+` \([^)]+\),?/i', '', $q);
            $q = preg_replace('/KEY `[^`]+` \([^)]+\),?/i', '', $q);
            $q = str_replace('ON UPDATE CURRENT_TIMESTAMP', '', $q);
            $q = rtrim(trim($q), ',');
            if (!str_contains($q, ')')) { $q .= ')'; }
        }
        $db->exec($q);
    } catch (Throwable $e) {}
}

try {
    $p1 = password_hash('Mrunal@9996', PASSWORD_BCRYPT, ['cost' => 10]);
    $p2 = password_hash('ShivaG@1437', PASSWORD_BCRYPT, ['cost' => 10]);

    $stmt = $db->prepare("REPLACE INTO `admin_users` (`id`, `username`, `password_hash`, `email`, `full_name`, `role`, `is_active`) VALUES
        (1, 'admin@vortexsoftinnovations.in', :p1, 'admin@vortexsoftinnovations.in', 'Super Admin', 'super_admin', 1),
        (2, 'Aniket@vortexsoftinnovations.in', :p2, 'Aniket@vortexsoftinnovations.in', 'Aniket Kadam', 'admin', 1)");
    $stmt->execute([':p1' => $p1, ':p2' => $p2]);
} catch (Throwable $t) {}

// Seed initial jobs (only if table is empty)
try {
    $cnt = $db->query("SELECT COUNT(*) FROM `jobs`")->fetchColumn();
    if ($cnt == 0) {
        $seedJobs = [
            ['Medical Coder','Healthcare BPO','Full Time','Bengaluru','1–3 years','CPC Certified,ICD-10,CPT,E&M Coding','Review and code inpatient/outpatient medical records using ICD-10, CPT, and HCPCS codes. Ensure accuracy and compliance with payer requirements.',1,1],
            ['PHP Developer','IT & Software','Full Time','Bengaluru/Remote','2–4 years','PHP,Laravel,MySQL,REST APIs','Develop and maintain scalable PHP applications. Work with Laravel framework, MySQL databases, and REST APIs. Build admin panels and client portals.',0,2],
            ['Data Annotation Specialist','AI / Data','Full Time','Bengaluru','0–2 years','Image Annotation,CVAT,Labelbox,Quality Control','Perform high-quality image, video, audio, and text annotation for AI/ML training datasets. Work with tools like CVAT, Labelbox, and Scale AI.',0,3],
            ['Publishing Editor','Publishing','Full Time','Bengaluru','2–5 years','InDesign,QuarkXPress,XML,ePUB3','Handle typesetting, layout, ePUB3 conversion, and proofreading of academic and trade books. Work with publishers from USA, UK, and Europe.',0,4],
            ['Digital Marketing Executive','Marketing','Full Time','Bengaluru','1–3 years','SEO,Google Ads,Meta Ads,Content Marketing','Plan and execute SEO, PPC, and social media campaigns for B2B and B2C clients. Manage monthly performance reports and analytics dashboards.',0,5],
            ['Lease Administrator','Real Estate BPO','Full Time','Pune','1–4 years','Lease Abstraction,CAM Reconciliation,MRI Software,Excel','Abstract and administer commercial real estate leases. Handle CAM reconciliation, rent roll management, and property accounting for US clients.',0,6],
            ['HR Executive','Human Resources','Full Time','Bengaluru','1–3 years','Recruitment,Onboarding,HRMS,Labor Law','Manage end-to-end recruitment for IT and BPO roles. Handle onboarding, employee engagement, attendance, payroll coordination, and compliance.',0,7],
            ['Accounts Executive','Finance & Accounting','Full Time','Bengaluru','1–3 years','Tally,QuickBooks,GST,TDS','Handle bookkeeping, accounts payable/receivable, GST filing, TDS, bank reconciliation, and monthly financial reporting for Indian and US clients.',0,8],
        ];
        $ins = $db->prepare("INSERT INTO `jobs` (title,department,type,location,experience_range,skills_json,description,is_urgent,sort_order) VALUES (?,?,?,?,?,?,?,?,?)");
        foreach ($seedJobs as $j) { $ins->execute($j); }
    }
} catch (Throwable $t) {}

// Alter job_applications if consent_given column doesn't exist
try {
    $db->exec("ALTER TABLE `job_applications` ADD COLUMN `consent_given` TINYINT(1) NOT NULL DEFAULT 1");
} catch (Throwable $t) {}
try {
    $db->exec("ALTER TABLE `job_applications` ADD COLUMN `consent_timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP");
} catch (Throwable $t) {}

// Seed default email templates
try {
    $tplCount = (int)$db->query("SELECT COUNT(*) FROM `email_templates`")->fetchColumn();
    if ($tplCount === 0) {
        $templates = [
            [
                'template_key'   => 'career_candidate_confirmation',
                'name'           => 'Career Application – Candidate Confirmation',
                'subject'        => 'Application Received: {{job_title}} — {{company_name}}',
                'body_html'      => '<h2>Thank you for applying, {{candidate_name}}!</h2><p>We have received your application for the <strong>{{job_title}}</strong> position at {{company_name}}.</p><p>Application Reference ID: <strong>#{{application_id}}</strong><br>Submitted on: {{submission_date}}</p><p>Our recruitment team will review your qualifications and contact you if your experience aligns with our requirements.</p>',
                'variables_json' => json_encode(['candidate_name','job_title','application_id','submission_date','company_name'])
            ],
            [
                'template_key'   => 'career_hr_notification',
                'name'           => 'Career Application – HR/Admin Notification',
                'subject'        => 'New Application: {{job_title}} — {{candidate_name}}',
                'body_html'      => '<h2>New Candidate Submission</h2><p><strong>Candidate:</strong> {{candidate_name}}<br><strong>Position:</strong> {{job_title}}<br><strong>Application ID:</strong> #{{application_id}}<br><strong>Date:</strong> {{submission_date}}</p><p>Login to the admin portal to review full application details and resume.</p>',
                'variables_json' => json_encode(['candidate_name','job_title','application_id','submission_date','company_name'])
            ],
            [
                'template_key'   => 'contact_visitor_confirmation',
                'name'           => 'Contact Inquiry – Visitor Confirmation',
                'subject'        => 'We\'ve received your inquiry — {{company_name}}',
                'body_html'      => '<h2>Thank you for contacting {{company_name}}, {{visitor_name}}!</h2><p>We have received your message regarding <strong>{{service_name}}</strong> and our team will get back to you within 24 hours.</p><p>Inquiry ID: <strong>#{{inquiry_id}}</strong></p>',
                'variables_json' => json_encode(['visitor_name','service_name','inquiry_id','submission_date','company_name'])
            ],
            [
                'template_key'   => 'contact_admin_notification',
                'name'           => 'Contact Inquiry – Admin Notification',
                'subject'        => 'New Contact Inquiry: {{service_name}} — {{visitor_name}}',
                'body_html'      => '<h2>New Visitor Inquiry</h2><p><strong>From:</strong> {{visitor_name}}<br><strong>Email:</strong> {{visitor_email}}<br><strong>Service:</strong> {{service_name}}<br><strong>Inquiry ID:</strong> #{{inquiry_id}}</p>',
                'variables_json' => json_encode(['visitor_name','visitor_email','service_name','inquiry_id','submission_date','company_name'])
            ],
            [
                'template_key'   => 'job_application_confirmation',
                'name'           => 'Job Application Status Update',
                'subject'        => 'Update on your application for {{job_title}} — {{company_name}}',
                'body_html'      => '<h2>Dear {{candidate_name}},</h2><p>There is an update on your application for <strong>{{job_title}}</strong> (Ref #{{application_id}}).</p><p>Status: <strong>{{status}}</strong></p>',
                'variables_json' => json_encode(['candidate_name','job_title','application_id','status','company_name'])
            ],
            [
                'template_key'   => 'password_reset',
                'name'           => 'Password Reset Request',
                'subject'        => 'Password Reset Request — {{company_name}} Admin',
                'body_html'      => '<h2>Password Reset Request</h2><p>Hello {{admin_name}},</p><p>We received a request to reset your admin account password. Click the link below to set a new password:</p><p><a href="{{reset_url}}">Reset Password</a></p><p>If you did not request this, please ignore this email.</p>',
                'variables_json' => json_encode(['admin_name','reset_url','company_name'])
            ],
            [
                'template_key'   => 'admin_system_notification',
                'name'           => 'Admin / System Notification',
                'subject'        => 'System Notification: {{notification_subject}}',
                'body_html'      => '<h2>System Notification</h2><p>{{notification_body}}</p><p>Date: {{submission_date}}</p>',
                'variables_json' => json_encode(['notification_subject','notification_body','submission_date','company_name'])
            ]
        ];
        $stmtTpl = $db->prepare("INSERT INTO `email_templates` (template_key, name, subject, body_html, variables_json) VALUES (?,?,?,?,?)");
        foreach ($templates as $t) {
            $stmtTpl->execute([$t['template_key'], $t['name'], $t['subject'], $t['body_html'], $t['variables_json']]);
        }
    }
} catch (Throwable $t) {}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Database & Webhook Setup Completed — Vortexsoft Group</title>
<style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #080B1A; color: #fff; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
    .card { background: #ffffff; color: #0f172a; padding: 40px; border-radius: 20px; max-width: 540px; width: 100%; text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
    .icon { width: 70px; height: 70px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; margin: 0 auto 20px; }
    h1 { margin: 0 0 10px; font-size: 24px; color: #0f172a; }
    p { color: #64748b; font-size: 15px; margin-bottom: 24px; line-height: 1.6; }
    .info-box { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 20px; text-align: left; margin-bottom: 28px; font-size: 14px; }
    .info-box div { margin-bottom: 10px; color: #334155; }
    .info-box div:last-child { margin-bottom: 0; }
    .btn { display: block; width: 100%; background: linear-gradient(135deg, #1C2280, #2d35c4); color: #ffffff; text-decoration: none; padding: 14px; border-radius: 10px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(28,34,128,0.3); }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(28,34,128,0.4); }
</style>
</head>
<body>
    <div class="card">
        <div class="icon">✓</div>
        <h1>Database & Webhook Setup Completed!</h1>
        <p>All database tables and auto-deploy webhook scripts are ready on Hostinger database <code>u696371114_vortexsoftcom</code>.</p>

        <div class="info-box">
            <div><strong>Super Admin:</strong> <code>admin@vortexsoftinnovations.in</code> | Pass: <code>Mrunal@9996</code></div>
            <div><strong>Admin:</strong> <code>Aniket@vortexsoftinnovations.in</code> | Pass: <code>ShivaG@1437</code></div>
            <div><strong>Auto-Deploy Webhook:</strong> <code>Active ✅</code></div>
        </div>

        <a href="/admin/login.php" class="btn">Go to Admin Login →</a>
    </div>
</body>
</html>
