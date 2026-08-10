<?php
/**
 * API Endpoint: Contact Form Submission
 * POST /api/contact.php
 * Saves to MySQL contact_inquiries table + sends email notification
 */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Bootstrap
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Method not allowed.');
}

// Honeypot anti-spam check (silent drop for bots)
if (!empty($_POST['website_hp'])) {
    json_response(true, 'Thank you! Your message has been sent.');
}

// CSRF verification
if (session_status() === PHP_SESSION_NONE) session_start();
$csrf_token = $_POST['csrf_token'] ?? '';
if (!empty($_SESSION['csrf_token']) && !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
    // Log potential CSRF attempt but don't reveal reason to attacker
    error_log('CSRF check failed on contact form from IP: ' . get_client_ip());
    json_response(false, 'Security validation failed. Please refresh the page and try again.');
}

// Rate limiting: max 5 submissions per 5 minutes per IP
$ip = get_client_ip();
if (!check_rate_limit('contact_' . md5($ip), 5, 300)) {
    json_response(false, 'Too many requests. Please wait a few minutes before trying again.');
}

// Collect + sanitize inputs
$name    = sanitize($_POST['fullName']    ?? $_POST['name']    ?? '');
$email   = sanitize_email($_POST['emailAddr'] ?? $_POST['email'] ?? '');
$phone   = sanitize($_POST['phone']       ?? '');
$service = sanitize($_POST['service']     ?? 'General Inquiry');
$company = sanitize($_POST['company']     ?? '');
$message = sanitize($_POST['msgText']     ?? $_POST['message'] ?? '');
$source  = sanitize($_SERVER['HTTP_REFERER'] ?? '');

// Validate required fields
if (empty($name)) {
    json_response(false, 'Please enter your full name.');
}
if (!is_valid_email($email)) {
    json_response(false, 'Please enter a valid email address.');
}
if (empty($message) || strlen($message) < 10) {
    json_response(false, 'Please enter a message (at least 10 characters).');
}
if (!empty($phone) && !is_valid_phone($phone)) {
    json_response(false, 'Please enter a valid phone number.');
}
if (strlen($name) > 120) {
    json_response(false, 'Name is too long.');
}
if (strlen($message) > 5000) {
    json_response(false, 'Message is too long (max 5000 characters).');
}

// Save to database
$db = getDB();
if ($db) {
    try {
        $stmt = $db->prepare(
            "INSERT INTO contact_inquiries 
             (name, email, phone, service, company, message, ip_address, user_agent, source_page, created_at)
             VALUES (:name, :email, :phone, :service, :company, :message, :ip, :ua, :source, NOW())"
        );
        $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':phone'   => $phone,
            ':service' => $service,
            ':company' => $company,
            ':message' => $message,
            ':ip'      => $ip,
            ':ua'      => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ':source'  => substr($source, 0, 255),
        ]);
        $inserted_id = $db->lastInsertId();
    } catch (PDOException $e) {
        error_log('Contact form DB error: ' . $e->getMessage());
        // Continue and still send email even if DB fails
        $inserted_id = 0;
    }
}

// Send email notification
$email_data = compact('name', 'email', 'phone', 'service', 'company', 'message');
$email_sent = send_contact_notification($email_data);

// Send auto-acknowledgement to the submitter (V2: uses branded template)
send_contact_acknowledgement(compact('name', 'email', 'phone', 'service', 'message'));

json_response(true, 'Thank you! Your message has been sent. Our team will reply within 24 hours.', [
    'inquiry_id' => $inserted_id ?? 0
]);
