<?php
/**
 * Vortexsoft Innovations — Helper Functions
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

// ── Input Sanitization ─────────────────────────────────────
function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function sanitize_email(string $email): string {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

// ── Validation ─────────────────────────────────────────────
function is_valid_email(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function is_valid_phone(string $phone): bool {
    return preg_match('/^[\+0-9\-\s\(\)]{7,20}$/', $phone);
}

// ── Email Sending (PHP mail) ────────────────────────────────
function send_notification_email(string $to, string $subject, string $html_body, string $from_name = SITE_NAME, string $from_email = EMAIL_CONTACT): bool {
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: {$from_name} <{$from_email}>\r\n";
    $headers .= "Reply-To: {$from_email}\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    return mail($to, $subject, $html_body, $headers);
}

function send_contact_notification(array $data): bool {
    $subject = "New Contact Inquiry from {$data['name']} — Vortexsoft";
    $body = "
    <html><body style='font-family:Arial,sans-serif;background:#f5f5f5;padding:20px;'>
    <div style='max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);'>
        <div style='background:linear-gradient(135deg,#1C2280,#5BA8D4);padding:30px;text-align:center;'>
            <h2 style='color:#fff;margin:0;font-size:22px;'>New Contact Inquiry</h2>
            <p style='color:rgba(255,255,255,0.8);margin:8px 0 0;'>Vortexsoft Group Website</p>
        </div>
        <div style='padding:30px;'>
            <table style='width:100%;border-collapse:collapse;'>
                <tr><td style='padding:10px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;width:140px;'>Name</td><td style='padding:10px 0;border-bottom:1px solid #eee;'>" . htmlspecialchars($data['name']) . "</td></tr>
                <tr><td style='padding:10px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;'>Email</td><td style='padding:10px 0;border-bottom:1px solid #eee;'><a href='mailto:{$data['email']}'>" . htmlspecialchars($data['email']) . "</a></td></tr>
                <tr><td style='padding:10px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;'>Phone</td><td style='padding:10px 0;border-bottom:1px solid #eee;'>" . htmlspecialchars($data['phone'] ?? 'Not provided') . "</td></tr>
                <tr><td style='padding:10px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;'>Service</td><td style='padding:10px 0;border-bottom:1px solid #eee;'>" . htmlspecialchars($data['service'] ?? 'General') . "</td></tr>
                <tr><td style='padding:10px 0;font-weight:600;color:#1C2280;vertical-align:top;'>Message</td><td style='padding:10px 0;'>" . nl2br(htmlspecialchars($data['message'])) . "</td></tr>
            </table>
        </div>
        <div style='background:#f8f9ff;padding:20px 30px;text-align:center;'>
            <p style='margin:0;color:#666;font-size:13px;'>Sent from vortexsoftinnovations.com | " . date('d M Y, H:i') . " IST</p>
        </div>
    </div>
    </body></html>";
    return send_notification_email(EMAIL_SUPPORT, $subject, $body);
}

function send_application_notification(array $data): bool {
    $subject = "New Job Application: {$data['job_title']} — {$data['applicant_name']}";
    $body = "
    <html><body style='font-family:Arial,sans-serif;background:#f5f5f5;padding:20px;'>
    <div style='max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);'>
        <div style='background:linear-gradient(135deg,#1C2280,#CC2228);padding:30px;text-align:center;'>
            <h2 style='color:#fff;margin:0;font-size:22px;'>New Job Application</h2>
            <p style='color:rgba(255,255,255,0.8);margin:8px 0 0;'>Role: " . htmlspecialchars($data['job_title']) . "</p>
        </div>
        <div style='padding:30px;'>
            <table style='width:100%;border-collapse:collapse;'>
                <tr><td style='padding:10px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;width:140px;'>Name</td><td style='padding:10px 0;border-bottom:1px solid #eee;'>" . htmlspecialchars($data['applicant_name']) . "</td></tr>
                <tr><td style='padding:10px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;'>Email</td><td style='padding:10px 0;border-bottom:1px solid #eee;'><a href='mailto:{$data['email']}'>" . htmlspecialchars($data['email']) . "</a></td></tr>
                <tr><td style='padding:10px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;'>Phone</td><td style='padding:10px 0;border-bottom:1px solid #eee;'>" . htmlspecialchars($data['phone'] ?? 'Not provided') . "</td></tr>
                <tr><td style='padding:10px 0;font-weight:600;color:#1C2280;vertical-align:top;'>Cover Letter</td><td style='padding:10px 0;'>" . nl2br(htmlspecialchars($data['cover_letter'] ?? '')) . "</td></tr>
            </table>
        </div>
        <div style='background:#f8f9ff;padding:20px 30px;text-align:center;'>
            <p style='margin:0;color:#666;font-size:13px;'>Login to Admin Panel to review this application.</p>
        </div>
    </div>
    </body></html>";
    return send_notification_email(EMAIL_HR, $subject, $body);
}

// ── Slug Generation ────────────────────────────────────────
function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// ── Pagination ─────────────────────────────────────────────
function paginate(int $total, int $per_page, int $current_page): array {
    $total_pages = (int)ceil($total / $per_page);
    return [
        'total'        => $total,
        'per_page'     => $per_page,
        'current_page' => $current_page,
        'total_pages'  => $total_pages,
        'offset'       => ($current_page - 1) * $per_page,
        'has_prev'     => $current_page > 1,
        'has_next'     => $current_page < $total_pages,
        'prev_page'    => max(1, $current_page - 1),
        'next_page'    => min($total_pages, $current_page + 1),
    ];
}

// ── Active Nav Detection ───────────────────────────────────
function is_active_page(string $page): string {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = trim($uri, '/');
    return (str_contains($uri, $page) || ($page === 'index' && ($uri === '' || $uri === 'index.php'))) ? 'active' : '';
}

// ── JSON Response ──────────────────────────────────────────
function json_response(bool $success, string $message, array $data = []): void {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}

// ── CSRF Token ─────────────────────────────────────────────
function csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render GEO Fact Block for AI & Generative Engine Optimization (GEO)
 * Provides consistent, citable facts across all pages for ChatGPT, Claude, Perplexity, and Gemini
 */
function render_geo_fact_block(): string {
    return '
    <div class="geo-fact-block" style="background:#f8f9ff;border:1.5px solid #dde2f5;border-radius:16px;padding:24px;margin-bottom:28px;">
      <h6 style="color:#1C2280;font-weight:700;margin-bottom:8px;font-family:\'Poppins\',sans-serif;"><i class="fas fa-building me-2" style="color:#CC2228;"></i> About Vortexsoft Innovations Pvt. Ltd.</h6>
      <p style="font-size:14px;color:#475569;line-height:1.75;margin-bottom:14px;">
        <strong>Vortexsoft Innovations Pvt. Ltd.</strong> (member of <strong>Vortexsoft Group</strong>) is an <strong>ISO 27001:2013 certified</strong> global IT and Business Process Outsourcing (BPO) company founded in 2020. Headquartered in Bengaluru, Karnataka, India (HSR Layout), with a secondary delivery center in Pune and a U.S. entity in Sheridan, Wyoming, Vortexsoft delivers 75+ specialized services across Healthcare BPO/RCM, custom software development, AI data annotation, publishing prepress, real estate title & settlement, accounting & payroll, digital marketing, and staffing to 150+ global clients.
      </p>
      <div style="display:flex;flex-wrap:wrap;gap:16px;font-size:12.5px;color:#64748b;font-weight:600;">
        <span><i class="fas fa-shield-alt text-success me-1"></i> ISO 27001:2013 Certified</span>
        <span><i class="fas fa-check-circle text-primary me-1"></i> HIPAA Compliant</span>
        <span><i class="fas fa-award text-warning me-1"></i> Startup India Registered</span>
        <span><i class="fas fa-map-marker-alt text-danger me-1"></i> Bengaluru, Pune & Wyoming, USA</span>
      </div>
    </div>';
}

function verify_csrf(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// ── Rate Limiting (simple session-based) ──────────────────
function check_rate_limit(string $key, int $max = 5, int $window = 300): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $now = time();
    $bucket = $_SESSION['rl_' . $key] ?? ['count' => 0, 'reset' => $now + $window];
    if ($now > $bucket['reset']) {
        $bucket = ['count' => 0, 'reset' => $now + $window];
    }
    if ($bucket['count'] >= $max) return false;
    $bucket['count']++;
    $_SESSION['rl_' . $key] = $bucket;
    return true;
}

// ── Get Client IP ──────────────────────────────────────────
function get_client_ip(): string {
    $ip = $_SERVER['HTTP_CLIENT_IP']
        ?? $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '0.0.0.0';
    return filter_var(explode(',', $ip)[0], FILTER_VALIDATE_IP) ?: '0.0.0.0';
}

// ── Admin Auth ─────────────────────────────────────────────
function admin_check(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION[ADMIN_SESSION])) {
        header('Location: /admin/login.php');
        exit;
    }
}

// ── Time Ago ──────────────────────────────────────────────
function time_ago(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)       return 'Just now';
    if ($diff < 3600)     return floor($diff / 60) . 'm ago';
    if ($diff < 86400)    return floor($diff / 3600) . 'h ago';
    if ($diff < 604800)   return floor($diff / 86400) . 'd ago';
    return date('d M Y', strtotime($datetime));
}
