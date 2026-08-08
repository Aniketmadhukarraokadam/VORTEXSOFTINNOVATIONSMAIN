<?php
/**
 * API Endpoint: Newsletter Subscription
 * POST /api/newsletter.php
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
if (!check_rate_limit('newsletter_' . md5($ip), 3, 300)) {
    json_response(false, 'Too many attempts. Please try again later.');
}

$email = sanitize_email($_POST['email'] ?? '');
$name  = sanitize($_POST['name'] ?? '');

if (!is_valid_email($email)) {
    json_response(false, 'Please enter a valid email address.');
}

$db = getDB();
if ($db) {
    try {
        // Check if already subscribed
        $check = $db->prepare("SELECT id, is_active FROM newsletter_subscribers WHERE email = :email");
        $check->execute([':email' => $email]);
        $existing = $check->fetch();

        if ($existing) {
            if ($existing['is_active']) {
                json_response(false, 'This email is already subscribed to our newsletter.');
            } else {
                // Reactivate
                $db->prepare("UPDATE newsletter_subscribers SET is_active=1, subscribed_at=NOW(), unsubscribed_at=NULL WHERE email=:email")
                   ->execute([':email' => $email]);
                json_response(true, 'Welcome back! You have been re-subscribed to our newsletter.');
            }
        }

        $token = bin2hex(random_bytes(32));
        $stmt = $db->prepare("INSERT INTO newsletter_subscribers (email, name, ip_address, unsubscribe_token, subscribed_at) VALUES (:email, :name, :ip, :token, NOW())");
        $stmt->execute([
            ':email' => $email,
            ':name'  => $name,
            ':ip'    => $ip,
            ':token' => $token,
        ]);

    } catch (PDOException $e) {
        error_log('Newsletter DB error: ' . $e->getMessage());
        json_response(false, 'Something went wrong. Please try again.');
    }
}

json_response(true, 'Thank you for subscribing! You will receive our latest updates.');
