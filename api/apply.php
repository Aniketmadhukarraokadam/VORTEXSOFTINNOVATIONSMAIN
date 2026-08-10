<?php
/**
 * API Endpoint: Job Application Submission
 * POST /api/apply.php
 * Saves to MySQL job_applications table + sends email to HR
 */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Method not allowed.');
}

$ip = get_client_ip();
if (!check_rate_limit('apply_' . md5($ip), 3, 600)) {
    json_response(false, 'Too many applications. Please wait before trying again.');
}

// Collect + sanitize
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

// Validate required
if (empty($applicant_name)) json_response(false, 'Please enter your full name.');
if (!is_valid_email($email)) json_response(false, 'Please enter a valid email address.');
if (empty($phone))           json_response(false, 'Please enter your phone number.');
if (empty($job_title))       json_response(false, 'Please specify the position you are applying for.');

// Handle file upload (resume)
$resume_filename = null;
$resume_path     = null;

if (!empty($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['resume'];
    $allowed_types = ['application/pdf', 'application/msword',
                      'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $allowed_exts  = ['pdf', 'doc', 'docx'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_exts)) {
        json_response(false, 'Resume must be a PDF, DOC, or DOCX file.');
    }
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        json_response(false, 'Resume file size must be under 5MB.');
    }

    // Create uploads directory
    $upload_dir = UPLOADS_PATH . '/resumes/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
        file_put_contents($upload_dir . '.htaccess', "Options -Indexes\n<FilesMatch \"\.(php|phtml|php3|php4|php5|phps|cgi|pl|exe|sh)$\">\n    Order allow,deny\n    Deny from all\n</FilesMatch>\n");
    }

    $safe_name       = preg_replace('/[^a-zA-Z0-9\-_]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
    $resume_filename = date('Ymd_His') . '_' . $safe_name . '.' . $ext;
    $dest            = $upload_dir . $resume_filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        json_response(false, 'Failed to upload resume. Please try again.');
    }
    $resume_path = UPLOADS_URL . '/resumes/' . $resume_filename;
}

// Save to database
$db = getDB();
$inserted_id = 0;
if ($db) {
    try {
        $stmt = $db->prepare("INSERT INTO job_applications
            (job_title, department, applicant_name, email, phone, current_location,
             experience_years, current_company, notice_period, expected_ctc,
             resume_filename, resume_path, cover_letter, linkedin_url, portfolio_url,
             ip_address, status, created_at)
            VALUES
            (:job_title, :dept, :name, :email, :phone, :location,
             :exp, :curr_co, :notice, :ctc,
             :rf, :rp, :cover, :linkedin, :portfolio,
             :ip, 'new', NOW())");
        $stmt->execute([
            ':job_title' => $job_title,
            ':dept'      => $department,
            ':name'      => $applicant_name,
            ':email'     => $email,
            ':phone'     => $phone,
            ':location'  => $current_location,
            ':exp'       => $experience ?: null,
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
        $inserted_id = $db->lastInsertId();
    } catch (PDOException $e) {
        error_log('Apply form DB error: ' . $e->getMessage());
    }
}

// Send HR notification
$email_data = [
    'job_title'      => $job_title,
    'applicant_name' => $applicant_name,
    'email'          => $email,
    'phone'          => $phone,
    'cover_letter'   => $cover_letter,
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
        <p style='color:#555;'>Thank you for applying to Vortexsoft Group. We have received your application for <strong>" . htmlspecialchars($job_title) . "</strong>.</p>
        <p style='color:#555;'>Our HR team will review your profile and get back to you within <strong>3-5 business days</strong>.</p>
        <p style='color:#555;'>For queries, contact: <a href='mailto:" . EMAIL_HR . "' style='color:#1C2280;'>" . EMAIL_HR . "</a></p>
    </div>
    <div style='background:#f8f9ff;padding:20px;text-align:center;'>
        <p style='color:#999;font-size:12px;margin:0;'>Vortexsoft Innovations Pvt. Ltd. | " . SITE_URL . "</p>
    </div>
</div>
</body></html>";
send_notification_email($email, 'Application Received — Vortexsoft Group', $body);

json_response(true, 'Your application has been submitted successfully! Our HR team will contact you soon.', [
    'application_id' => $inserted_id
]);
