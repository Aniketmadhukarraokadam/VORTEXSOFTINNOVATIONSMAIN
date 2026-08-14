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
// ── Encryption Helpers (AES-256-GCM for Secrets) ───────────
define('APP_SECRET_KEY', hash('sha256', 'VortexsoftSecretKey_2026_V2_Secure!'));

function encrypt_secret(string $plaintext): string {
    if (empty($plaintext)) return '';
    $iv = openssl_random_pseudo_bytes(12);
    $tag = '';
    $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', APP_SECRET_KEY, 0, $iv, $tag);
    return base64_encode($iv . $tag . $ciphertext);
}

function decrypt_secret(string $encoded): string {
    if (empty($encoded)) return '';
    $raw = base64_decode($encoded);
    if (strlen($raw) < 28) return '';
    $iv = substr($raw, 0, 12);
    $tag = substr($raw, 12, 16);
    $ciphertext = substr($raw, 28);
    return (string)openssl_decrypt($ciphertext, 'aes-256-gcm', APP_SECRET_KEY, 0, $iv, $tag);
}

// ── Admin Activity Logging ─────────────────────────────────
function log_admin_activity(string $action, string $details = '', $record_id = null): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $admin_id   = $_SESSION['admin_id'] ?? $_SESSION['vortex_admin_id'] ?? null;
    $admin_name = $_SESSION['admin_username'] ?? $_SESSION['vortex_admin_user'] ?? 'System / Anonymous';
    $ip         = get_client_ip();
    $ua         = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

    try {
        $db = getDB();
        if ($db) {
            $stmt = $db->prepare("INSERT INTO admin_activity_logs (admin_id, admin_username, action, details, ip_address, user_agent, created_at) VALUES (?,?,?,?,?,?,NOW())");
            return $stmt->execute([$admin_id, $admin_name, $action, $details, $ip, $ua]);
        }
    } catch (Throwable $e) {}
    return false;
}

// ── RBAC Authorization Check ──────────────────────────────
function admin_require_role(array $allowed_roles): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    admin_check();
    $user_role = $_SESSION['vortex_admin_role'] ?? $_SESSION['admin_role'] ?? 'admin';
    if ($user_role === 'super_admin') return; // Super admin has full access
    if (!in_array($user_role, $allowed_roles, true)) {
        http_response_code(403);
        echo '<div style="font-family:sans-serif;padding:40px;text-align:center;color:#b91c1c;"><h2>403 Forbidden</h2><p>You do not have permission to access this module.</p><p><a href="dashboard.php">Return to Dashboard</a></p></div>';
        exit;
    }
}

// ── Email Template Helper ──────────────────────────────────
function get_email_template(string $key, array $vars = []): ?array {
    try {
        $db = getDB();
        if ($db) {
            $stmt = $db->prepare("SELECT * FROM email_templates WHERE template_key=?");
            $stmt->execute([$key]);
            $tpl = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($tpl) {
                $subject = $tpl['subject'];
                $body    = $tpl['body_html'];
                $vars['company_name'] = $vars['company_name'] ?? 'Vortexsoft Group';
                $vars['submission_date'] = $vars['submission_date'] ?? date('d M Y, H:i') . ' IST';

                foreach ($vars as $k => $v) {
                    $subject = str_replace('{{' . $k . '}}', (string)$v, $subject);
                    $body    = str_replace('{{' . $k . '}}', (string)$v, $body);
                }
                return ['subject' => $subject, 'body_html' => $body];
            }
        }
    } catch (Throwable $e) {}
    return null;
}

// ── Email Sending (PHP mail + Email Logging + Security Rules) ──
function send_notification_email(string $to, string $subject, string $html_body, string $from_name = SITE_NAME, string $reply_to = EMAIL_CONTACT, ?string $from_email_override = null): bool {
    // SECURITY RULE #6: Always send from company approved mailbox
    $from_email = 'contact@vortexsoftinnovations.com';
    
    // Check if custom active email account is configured
    try {
        $db = getDB();
        if ($db) {
            $acc = $db->query("SELECT email_address, display_name FROM email_accounts WHERE is_active=1 ORDER BY is_default DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if ($acc && !empty($acc['email_address'])) {
                $from_email = $acc['email_address'];
                if (!empty($acc['display_name']) && $from_name === SITE_NAME) {
                    $from_name = $acc['display_name'];
                }
            }
        }
    } catch (Throwable $e) {}

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: {$from_name} <{$from_email}>\r\n";
    $headers .= "Reply-To: {$reply_to}\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    $sent = @mail($to, $subject, $html_body, $headers);
    $status = $sent ? 'sent' : 'failed';
    $err_msg = $sent ? null : 'Native mail() returned false. Verify server SMTP / Sendmail config.';

    // Log to email_logs table
    try {
        if (isset($db) && $db) {
            $stmtLog = $db->prepare("INSERT INTO email_logs (type, sender, recipient, subject, body_html, status, error_message, created_at) VALUES ('outgoing', ?, ?, ?, ?, ?, ?, NOW())");
            $stmtLog->execute([$from_email, $to, $subject, $html_body, $status, $err_msg]);
        }
    } catch (Throwable $e) {}

    return $sent;
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
    $resume_link = !empty($data['resume_path'])
        ? "<a href='{$data['resume_path']}' style='color:#1C2280;font-weight:700;'>Download Resume</a>"
        : '<span style="color:#94a3b8;">Not uploaded</span>';
    $body = "
    <html><body style='font-family:Arial,sans-serif;background:#f5f5f5;padding:20px;'>
    <div style='max-width:640px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);'>
        <div style='background:linear-gradient(135deg,#1C2280,#CC2228);padding:30px;text-align:center;'>
            <h2 style='color:#fff;margin:0;font-size:22px;'>New Job Application</h2>
            <p style='color:rgba(255,255,255,0.8);margin:8px 0 0;'>Role: " . htmlspecialchars($data['job_title']) . " | Dept: " . htmlspecialchars($data['department'] ?? '') . "</p>
        </div>
        <div style='padding:30px;'>
            <table style='width:100%;border-collapse:collapse;'>
                <tr><td style='padding:9px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;width:160px;'>Applicant Name</td><td style='padding:9px 0;border-bottom:1px solid #eee;'>" . htmlspecialchars($data['applicant_name']) . "</td></tr>
                <tr><td style='padding:9px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;'>Email</td><td style='padding:9px 0;border-bottom:1px solid #eee;'><a href='mailto:{$data['email']}'>" . htmlspecialchars($data['email']) . "</a></td></tr>
                <tr><td style='padding:9px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;'>Phone</td><td style='padding:9px 0;border-bottom:1px solid #eee;'>" . htmlspecialchars($data['phone'] ?? 'Not provided') . "</td></tr>
                <tr><td style='padding:9px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;'>Experience</td><td style='padding:9px 0;border-bottom:1px solid #eee;'>" . htmlspecialchars($data['experience_years'] ?? '—') . " years</td></tr>
                <tr><td style='padding:9px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;'>Current Company</td><td style='padding:9px 0;border-bottom:1px solid #eee;'>" . htmlspecialchars($data['current_company'] ?? 'Not provided') . "</td></tr>
                <tr><td style='padding:9px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;'>Notice Period</td><td style='padding:9px 0;border-bottom:1px solid #eee;'>" . htmlspecialchars($data['notice_period'] ?? 'Not provided') . "</td></tr>
                <tr><td style='padding:9px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;'>Expected CTC</td><td style='padding:9px 0;border-bottom:1px solid #eee;'>" . htmlspecialchars($data['expected_ctc'] ?? 'Not provided') . "</td></tr>
                <tr><td style='padding:9px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;'>LinkedIn</td><td style='padding:9px 0;border-bottom:1px solid #eee;'>" . (!empty($data['linkedin_url']) ? "<a href='{$data['linkedin_url']}'>{$data['linkedin_url']}</a>" : '—') . "</td></tr>
                <tr><td style='padding:9px 0;border-bottom:1px solid #eee;font-weight:600;color:#1C2280;'>Resume</td><td style='padding:9px 0;border-bottom:1px solid #eee;'>" . $resume_link . "</td></tr>
                <tr><td style='padding:9px 0;font-weight:600;color:#1C2280;vertical-align:top;'>Cover Letter</td><td style='padding:9px 0;'>" . nl2br(htmlspecialchars($data['cover_letter'] ?? '—')) . "</td></tr>
            </table>
        </div>
        <div style='background:#f8f9ff;padding:20px 30px;text-align:center;'>
            <p style='margin:0;color:#666;font-size:13px;'>Login to Admin Panel to review and update status.</p>
            <p style='margin:8px 0 0;color:#999;font-size:12px;'>" . date('d M Y, H:i') . " IST</p>
        </div>
    </div>
    </body></html>";
    return send_notification_email(EMAIL_HR, $subject, $body);
}

// ── Slug Generation ────────────────────────────────────────

/**
 * Send a branded auto-acknowledgement email to a contact form submitter.
 */
function send_contact_acknowledgement(array $data): bool {
    $subject = 'We\'ve received your inquiry — Vortexsoft Group';
    $body = "
    <html><body style='font-family:Arial,sans-serif;background:#f5f5f5;padding:20px;'>
    <div style='max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);'>
        <div style='background:linear-gradient(135deg,#1C2280,#5BA8D4);padding:30px;text-align:center;'>
            <h2 style='color:#fff;margin:0;font-size:22px;'>Thank You for Contacting Us!</h2>
            <p style='color:rgba(255,255,255,0.8);margin:8px 0 0;'>Vortexsoft Group</p>
        </div>
        <div style='padding:30px;'>
            <p style='color:#333;font-size:15px;'>Dear <strong>" . htmlspecialchars($data['name']) . "</strong>,</p>
            <p style='color:#555;font-size:14px;line-height:1.7;'>We have received your inquiry and our team will review it shortly. We typically respond within <strong>24 hours</strong> on business days (Mon&ndash;Sat, 9AM&ndash;6PM IST).</p>
            <div style='background:#f0f2ff;border-radius:10px;padding:20px;margin:20px 0;'>
                <p style='margin:0 0 8px;font-size:13px;font-weight:700;color:#1C2280;'>Your Inquiry Summary:</p>
                <p style='margin:0;font-size:13px;color:#475569;'><strong>Subject:</strong> " . htmlspecialchars($data['service'] ?? 'General Inquiry') . "</p>
                <p style='margin:6px 0 0;font-size:13px;color:#475569;'>" . nl2br(htmlspecialchars(mb_strimwidth($data['message'] ?? '', 0, 200, '...'))) . "</p>
            </div>
            <p style='color:#555;font-size:14px;'>Need an immediate response? Contact us via:</p>
            <ul style='color:#475569;font-size:13px;line-height:2;padding-left:20px;'>
                <li>WhatsApp: <a href='https://wa.me/918308906690' style='color:#1C2280;'>+91-8308906690</a></li>
                <li>Email: <a href='mailto:" . EMAIL_SUPPORT . "' style='color:#1C2280;'>" . EMAIL_SUPPORT . "</a></li>
            </ul>
        </div>
        <div style='background:#f8f9ff;padding:20px 30px;text-align:center;'>
            <p style='margin:0;color:#999;font-size:12px;'>Vortexsoft Innovations Pvt. Ltd. | " . SITE_URL . "</p>
            <p style='margin:4px 0 0;color:#bbb;font-size:11px;'>This is an automated confirmation. Please do not reply to this email.</p>
        </div>
    </div>
    </body></html>";
    return send_notification_email($data['email'], $subject, $body, EMAIL_FROM_NAME, EMAIL_CONTACT);
}
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
        <strong>Vortexsoft Innovations Pvt. Ltd.</strong> (member of <strong>Vortexsoft Group</strong>) is an <strong>ISO 27001:2013 certified</strong> global IT and Business Process Outsourcing (BPO) company founded in 2020. Headquartered in Pune, Maharashtra, India, with a delivery center in Bengaluru (HSR Layout) and a U.S. entity in Sheridan, Wyoming, Vortexsoft delivers 75+ specialized services across Healthcare BPO/RCM, custom software development, AI data annotation, publishing prepress, real estate title & settlement, accounting & payroll, digital marketing, and staffing to 150+ global clients.
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
