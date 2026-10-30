<?php
/**
 * API Endpoint: Job Application Submission
 * POST /api/apply.php
 * Saves to MySQL job_applications table + sends email to HR
 */

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Method not allowed.', [], 405);
}

$ip = get_client_ip();

// Honeypot anti-spam check (silent drop for bots)
if (!empty($_POST['website_hp'])) {
    json_response(true, 'Application submitted successfully!', ['application_id' => 0], 200);
}

// Rate limiting check
if (!check_rate_limit('apply_' . md5($ip), 50, 3600)) {
    json_response(false, 'Too many applications submitted from your network. Please wait a short while before trying again.', [], 429);
}

// Collect + sanitize inputs
$job_title        = sanitize($_POST['job_title']        ?? '');
$department       = sanitize($_POST['department']       ?? '');
$applicant_name   = sanitize($_POST['applicant_name']   ?? $_POST['name'] ?? '');
$email            = sanitize_email($_POST['email']      ?? '');
$phone            = sanitize($_POST['phone']            ?? '');
$current_location = sanitize($_POST['current_location'] ?? '');
$experience       = sanitize($_POST['experience_years'] ?? '');
$current_company  = sanitize($_POST['current_company']  ?? '');
$notice_period    = sanitize($_POST['notice_period']    ?? '');
$expected_ctc     = sanitize($_POST['expected_ctc']     ?? '');
$cover_letter     = sanitize($_POST['cover_letter']     ?? '');
$linkedin_url     = sanitize($_POST['linkedin_url']     ?? '');
$portfolio_url    = sanitize($_POST['portfolio_url']    ?? '');
$consent_given    = !empty($_POST['consent']) || !empty($_POST['consent_given']);

// Default fallback for job title and department
if (empty($job_title)) {
    $job_title = 'General Application';
}
if (empty($department)) {
    $department = 'General';
}

// Validate required fields
if (empty($applicant_name) || mb_strlen($applicant_name) < 2) {
    json_response(false, 'Please enter your full legal name (at least 2 characters).', [], 422);
}
if (!is_valid_email($email)) {
    json_response(false, 'Please enter a valid email address.', [], 422);
}
if (empty($phone) || !is_valid_phone($phone)) {
    json_response(false, 'Please enter a valid contact phone number (at least 7 digits).', [], 422);
}
if (!$consent_given) {
    json_response(false, 'You must consent to data processing for recruitment purposes to submit your application.', [], 422);
}

// Handle file upload (resume)
$resume_filename = null;
$resume_path     = null;

if (!empty($_FILES['resume']) && is_array($_FILES['resume']) && ($_FILES['resume']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['resume'];

    // Check PHP upload error flags
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || ($file['size'] ?? 0) > 5 * 1024 * 1024) {
        json_response(false, 'Resume file size exceeds the 5MB limit. Please upload a smaller document.', [], 400);
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        json_response(false, 'File upload error occurred (Error code ' . (int)$file['error'] . '). Please try again.', [], 400);
    }

    $allowed_exts = ['pdf', 'doc', 'docx'];
    $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_exts, true)) {
        json_response(false, 'Resume must be a PDF, DOC, or DOCX file.', [], 400);
    }

    // MIME type verification with PHP Fileinfo
    $detected_mime = '';
    if (function_exists('finfo_open')) {
        $finfo = @finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $detected_mime = (string)@finfo_file($finfo, $file['tmp_name']);
            @finfo_close($finfo);
        }
    } elseif (function_exists('mime_content_type')) {
        $detected_mime = (string)@mime_content_type($file['tmp_name']);
    }

    $allowed_mimes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/octet-stream',
        'application/zip',
        'application/x-zip-compressed'
    ];
    if (!empty($detected_mime) && !in_array($detected_mime, $allowed_mimes, true)) {
        json_response(false, 'Invalid file type detected (' . htmlspecialchars($detected_mime) . '). Please ensure your resume is an authentic PDF, DOC, or DOCX file.', [], 400);
    }

    // Magic-byte signature verification
    $fh = @fopen($file['tmp_name'], 'rb');
    if ($fh) {
        $header = (string)fread($fh, 8);
        fclose($fh);
        $isValidSignature = false;
        if ($ext === 'pdf' && strpos($header, '%PDF-') === 0) {
            $isValidSignature = true;
        } elseif ($ext === 'docx' && strpos($header, "PK\x03\x04") === 0) {
            $isValidSignature = true;
        } elseif ($ext === 'doc' && strpos($header, "\xD0\xCF\x11\xE0") === 0) {
            $isValidSignature = true;
        }
        if (!$isValidSignature) {
            json_response(false, 'The uploaded file signature does not match authentic PDF, DOC, or DOCX format.', [], 400);
        }
    }

    // Create uploads directory and enforce access protection
    $upload_dir = UPLOADS_PATH . '/resumes/';
    if (!is_dir($upload_dir)) {
        @mkdir($upload_dir, 0755, true);
    }
    
    // Maintain protected .htaccess forbidding direct script execution and direct HTTP access
    $htaccess_content = "# Protected Resume Storage - Vortexsoft Innovations\nOptions -Indexes\n<FilesMatch \"\.(php|phtml|php3|php4|php5|php7|php8|phps|cgi|pl|exe|sh|py|jsp|asp|aspx)$\">\n    Order Deny,Allow\n    Deny from all\n</FilesMatch>\n<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n    Order allow,deny\n    Deny from all\n</IfModule>\n";
    @file_put_contents($upload_dir . '.htaccess', $htaccess_content);

    // Randomized, collision-safe filename
    $safe_name       = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
    $random_token    = bin2hex(random_bytes(8));
    $resume_filename = 'resume_' . date('Ymd_His') . '_' . $random_token . '_' . substr($safe_name, 0, 30) . '.' . $ext;
    $dest            = $upload_dir . $resume_filename;

    if (!@move_uploaded_file($file['tmp_name'], $dest)) {
        error_log("Resume upload move failed for {$file['name']} to {$dest}");
        json_response(false, 'Failed to save uploaded resume file. Please try submitting again or email hr@vortexsoftinnovations.com directly.', [], 500);
    }
    $resume_path = 'uploads/resumes/' . $resume_filename;
}

// Experience conversion
$exp_val = ($experience !== '' && is_numeric($experience)) ? (float)$experience : null;

// Database Connection & Persistence
$db = getDB();
if (!$db) {
    error_log('Apply form error: Database connection is unavailable.');
    json_response(false, 'Database service is temporarily unavailable. Please try again in a few moments or email your resume directly to hr@vortexsoftinnovations.com.', [], 503);
}

$inserted_id = 0;
try {
    $stmt = $db->prepare("INSERT INTO job_applications
        (job_title, department, applicant_name, email, phone, current_location,
         experience_years, current_company, notice_period, expected_ctc,
         resume_filename, resume_path, cover_letter, linkedin_url, portfolio_url,
         ip_address, status, consent_given, consent_timestamp, created_at)
        VALUES
        (:job_title, :dept, :name, :email, :phone, :location,
         :exp, :curr_co, :notice, :ctc,
         :rf, :rp, :cover, :linkedin, :portfolio,
         :ip, 'new', 1, NOW(), NOW())");
    $stmt->execute([
        ':job_title' => $job_title,
        ':dept'      => $department,
        ':name'      => $applicant_name,
        ':email'     => $email,
        ':phone'     => $phone,
        ':location'  => $current_location,
        ':exp'       => $exp_val,
        ':curr_co'   => $current_company,
        ':notice'    => $notice_period,
        ':ctc'       => $expected_ctc,
        ':rf'        => $resume_filename,
        ':rp'        => $resume_path,
        ':cover'     => $cover_letter,
        ':linkedin'  => $linkedin_url,
        ':portfolio' => $portfolio_url,
        ':ip'        => $ip,
    ]);
    $inserted_id = (int)$db->lastInsertId();
} catch (PDOException $e) {
    error_log('Apply form primary DB error: ' . $e->getMessage());
    // Fallback in case consent columns are pending on legacy production schema
    try {
        $stmtFallback = $db->prepare("INSERT INTO job_applications
            (job_title, department, applicant_name, email, phone, current_location,
             experience_years, current_company, notice_period, expected_ctc,
             resume_filename, resume_path, cover_letter, linkedin_url, portfolio_url,
             ip_address, status, created_at)
            VALUES
            (:job_title, :dept, :name, :email, :phone, :location,
             :exp, :curr_co, :notice, :ctc,
             :rf, :rp, :cover, :linkedin, :portfolio,
             :ip, 'new', NOW())");
        $stmtFallback->execute([
            ':job_title' => $job_title,
            ':dept'      => $department,
            ':name'      => $applicant_name,
            ':email'     => $email,
            ':phone'     => $phone,
            ':location'  => $current_location,
            ':exp'       => $exp_val,
            ':curr_co'   => $current_company,
            ':notice'    => $notice_period,
            ':ctc'       => $expected_ctc,
            ':rf'        => $resume_filename,
            ':rp'        => $resume_path,
            ':cover'     => $cover_letter,
            ':linkedin'  => $linkedin_url,
            ':portfolio' => $portfolio_url,
            ':ip'        => $ip,
        ]);
        $inserted_id = (int)$db->lastInsertId();
    } catch (PDOException $e2) {
        error_log('Apply form fallback DB error: ' . $e2->getMessage());
        json_response(false, 'Unable to store your application at this time. Please try submitting again or email hr@vortexsoftinnovations.com.', [], 500);
    }
}

if ($inserted_id === 0) {
    error_log('Apply form error: Record was not saved, inserted_id is 0.');
    json_response(false, 'Could not save your application to our database. Please try submitting again or email hr@vortexsoftinnovations.com directly.', [], 500);
}

// Notifications wrapped in try-catch so mail delivery issues never abort application confirmation
try {
    // Send HR notification
    $email_data = [
        'job_title'        => $job_title,
        'department'       => $department,
        'applicant_name'   => $applicant_name,
        'email'            => $email,
        'phone'            => $phone,
        'experience_years' => $experience,
        'current_company'  => $current_company,
        'notice_period'    => $notice_period,
        'expected_ctc'     => $expected_ctc,
        'linkedin_url'     => $linkedin_url,
        'resume_filename'  => $resume_filename,
        'resume_path'      => $resume_path,
        'cover_letter'     => $cover_letter,
        'application_id'   => $inserted_id,
    ];
    send_application_notification($email_data);

    // Auto-reply to applicant
    $body = "
    <html><body style='font-family:Arial,sans-serif;background:#f5f5f5;padding:20px;'>
    <div style='max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);'>
        <div style='background:linear-gradient(135deg,#1C2280,#CC2228);padding:30px;text-align:center;'>
            <h2 style='color:#fff;margin:0;font-size:22px;'>Application Received!</h2>
            <p style='color:rgba(255,255,255,0.8);margin:8px 0 0;'>Role: " . htmlspecialchars($job_title) . "</p>
        </div>
        <div style='padding:30px;'>
            <p style='color:#333;'>Dear <strong>" . htmlspecialchars($applicant_name) . "</strong>,</p>
            <p style='color:#555;'>Thank you for applying to Vortexsoft Innovations Private Limited. We have received your application for <strong>" . htmlspecialchars($job_title) . "</strong>.</p>
            <p style='color:#555;'>Our HR and Talent Acquisition teams will review your profile and get back to you within <strong>48 business hours</strong>.</p>
            <p style='color:#555;'>For any questions regarding your application (Reference ID: #" . $inserted_id . "), please contact our recruitment desk: <a href='mailto:" . EMAIL_HR . "' style='color:#1C2280;'>" . EMAIL_HR . "</a></p>
        </div>
        <div style='background:#f8f9ff;padding:20px;text-align:center;'>
            <p style='color:#999;font-size:12px;margin:0;'>Vortexsoft Innovations Private Limited | " . SITE_URL . "</p>
        </div>
    </div>
    </body></html>";

    $careers_email_addr = function_exists('get_careers_email') ? get_careers_email() : EMAIL_CAREERS;
    send_notification_email($email, 'Application Received — Vortexsoft Innovations Private Limited', $body, SITE_NAME . ' Careers', $careers_email_addr, EMAIL_NO_REPLY, $careers_email_addr);
} catch (Throwable $e) {
    error_log('Application email notification error: ' . $e->getMessage());
    if ($db && $inserted_id > 0) {
        try {
            $db->prepare("UPDATE job_applications SET admin_notes = CONCAT(COALESCE(admin_notes,''), '\n[System Notice] Automated notification email failed: ', :err) WHERE id = :id")
               ->execute([':err' => substr($e->getMessage(), 0, 180), ':id' => $inserted_id]);
        } catch (Throwable $t) {}
    }
}

// Return verified success only after actual database persistence
json_response(true, 'Your application has been submitted successfully! Our talent acquisition team will review your profile and contact you within 48 business hours.', [
    'application_id' => $inserted_id
], 200);
