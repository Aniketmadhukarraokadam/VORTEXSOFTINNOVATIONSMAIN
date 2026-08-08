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

// Auto-reply to user
$auto_reply_body = "
<html><body style='font-family:Arial,sans-serif;background:#f5f5f5;padding:20px;'>
<div style='max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);'>
    <div style='background:linear-gradient(135deg,#1C2280,#5BA8D4);padding:30px;text-align:center;'>
        <img src='" . SITE_URL . "/logo-header.png' alt='Vortexsoft Group' style='height:50px;margin-bottom:16px;'>
        <h2 style='color:#fff;margin:0;font-size:22px;'>Thank You, " . htmlspecialchars($name) . "!</h2>
    </div>
    <div style='padding:30px;'>
        <p style='color:#333;font-size:16px;'>We've received your inquiry and our team will get back to you within <strong>24 hours</strong>.</p>
        <p style='color:#666;'>For urgent matters, please call us directly:</p>
        <div style='background:#f0f2ff;border-radius:10px;padding:16px;margin:16px 0;text-align:center;'>
            <a href='tel:+918308906690' style='color:#1C2280;font-weight:700;font-size:18px;text-decoration:none;display:block;'>+91-8308906690</a>
            <a href='tel:+13072050681' style='color:#1C2280;font-weight:600;font-size:15px;text-decoration:none;'>+1-307-205-0681</a>
            <p style='color:#666;font-size:13px;margin-top:8px;'>Mon–Sat, 9AM–6PM IST</p>
        </div>
        <p style='color:#666;font-size:14px;'>Or chat with us on <a href='" . SOCIAL_WHATSAPP . "' style='color:#25d366;font-weight:600;'>WhatsApp</a></p>
    </div>
    <div style='background:#f8f9ff;padding:20px 30px;text-align:center;border-top:1px solid #eee;'>
        <p style='margin:0;color:#999;font-size:12px;'>Vortexsoft Innovations Pvt. Ltd. &nbsp;|&nbsp; ISO 27001:2013 Certified</p>
        <p style='margin:6px 0 0;color:#999;font-size:12px;'>" . SITE_URL . "</p>
    </div>
</div>
</body></html>";
send_notification_email($email, 'We received your inquiry — Vortexsoft Group', $auto_reply_body, SITE_NAME, EMAIL_SUPPORT);

json_response(true, 'Thank you! Your message has been sent. Our team will reply within 24 hours.', [
    'inquiry_id' => $inserted_id ?? 0
]);
