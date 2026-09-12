<?php
/**
 * Vortexsoft Innovations — Admin: Job Applications Manager
 * /admin/applications.php
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
admin_check();

$db = getDB();
$view_id = (int)($_GET['view'] ?? 0);
$view    = null;
$apps    = [];

if ($db) {
    try {
        // Delete Application
        if (isset($_GET['delete'])) {
            $did = (int)$_GET['delete'];
            $db->prepare("DELETE FROM job_applications WHERE id = :id")->execute([':id' => $did]);
            header('Location: applications.php?msg=deleted');
            exit;
        }

        // Status update & candidate email reply
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_update']) && $view_id > 0) {
            $stmtApp = $db->prepare("SELECT * FROM job_applications WHERE id = :id");
            $stmtApp->execute([':id' => $view_id]);
            $current_app = $stmtApp->fetch(PDO::FETCH_ASSOC);

            if ($current_app) {
                $new_status    = sanitize($_POST['status'] ?? 'new');
                $admin_note    = sanitize($_POST['admin_notes'] ?? '');
                $send_email    = !empty($_POST['send_email']);
                $email_subject = sanitize($_POST['email_subject'] ?? '');
                $email_message = trim($_POST['email_message'] ?? '');

                $email_status_param = '';

                if ($send_email && !empty($current_app['email'])) {
                    if (empty($email_subject)) {
                        $email_subject = "Update regarding your application for {$current_app['job_title']} — " . SITE_NAME;
                    }
                    if (empty($email_message)) {
                        $email_message = "Your application status has been updated to: " . ucfirst($new_status) . ".";
                    }

                    // Build branded HTML email
                    $candidate_name = htmlspecialchars($current_app['applicant_name']);
                    $job_title      = htmlspecialchars($current_app['job_title']);
                    $status_label   = ucfirst($new_status);
                    $formatted_msg  = nl2br(htmlspecialchars($email_message));

                    // Status pill styling
                    $status_colors = [
                        'new'         => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                        'reviewed'    => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
                        'shortlisted' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                        'interview'   => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                        'offered'     => ['bg' => '#d1fae5', 'text' => '#065f46'],
                        'rejected'    => ['bg' => '#f3f4f6', 'text' => '#4b5563'],
                        'withdrawn'   => ['bg' => '#f1f5f9', 'text' => '#475569'],
                    ];
                    $badge_style = $status_colors[$new_status] ?? ['bg' => '#e2e8f0', 'text' => '#1e293b'];

                    $html_body = "
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<style>
  body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6fb; margin: 0; padding: 20px; color: #1e293b; }
  .email-container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid #e8ecff; }
  .email-header { background: #080B1A; padding: 28px 30px; text-align: center; border-bottom: 3px solid #CC2228; }
  .email-header h1 { color: #ffffff; margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 0.5px; }
  .email-body { padding: 32px 30px; }
  .status-card { background: #f8faff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; margin: 20px 0; }
  .badge { display: inline-block; padding: 4px 14px; border-radius: 50px; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
  .message-box { background: #ffffff; border-left: 4px solid #1C2280; padding: 16px 20px; margin: 20px 0; border-radius: 0 8px 8px 0; font-size: 14.5px; line-height: 1.7; color: #334155; }
  .email-footer { background: #f8fafc; padding: 20px 30px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
  .email-footer a { color: #1C2280; text-decoration: none; font-weight: 600; }
</style>
</head>
<body>
  <div class='email-container'>
    <div class='email-header'>
      <h1>" . htmlspecialchars(SITE_NAME) . " — Careers</h1>
    </div>
    <div class='email-body'>
      <h2 style='color:#1C2280;margin-top:0;font-size:18px;'>Application Status Update</h2>
      <p style='font-size:15px;line-height:1.6;'>Dear <strong>{$candidate_name}</strong>,</p>
      <p style='font-size:14.5px;line-height:1.6;color:#475569;'>
        Thank you for your application for the <strong>{$job_title}</strong> position at <strong>" . htmlspecialchars(SITE_NAME) . "</strong> (Application Ref <strong>#{$current_app['id']}</strong>).
      </p>
      <div class='status-card'>
        <div style='font-size:12px;color:#64748b;text-transform:uppercase;font-weight:700;margin-bottom:6px;'>Current Application Status</div>
        <div>
          <span class='badge' style='background:{$badge_style['bg']};color:{$badge_style['text']};'>{$status_label}</span>
        </div>
      </div>
      <div class='message-box'>
        {$formatted_msg}
      </div>
      <p style='font-size:14px;line-height:1.6;color:#475569;margin-top:24px;'>
        If you have any questions, feel free to reply directly to this email or reach out to our recruitment team at <a href='mailto:" . EMAIL_HR . "' style='color:#1C2280;font-weight:600;'>" . EMAIL_HR . "</a>.
      </p>
      <p style='font-size:14px;color:#1e293b;margin-top:20px;font-weight:600;'>
        Best regards,<br>
        <span style='color:#CC2228;'>" . htmlspecialchars(SITE_NAME) . " Recruitment Team</span>
      </p>
    </div>
    <div class='email-footer'>
      <p style='margin:0 0 6px;'>&copy; " . date('Y') . " " . htmlspecialchars(SITE_NAME) . ". All rights reserved.</p>
      <p style='margin:0;'><a href='" . SITE_URL . "'>" . htmlspecialchars(SITE_DOMAIN) . "</a> &nbsp;|&nbsp; <a href='mailto:" . EMAIL_HR . "'>" . EMAIL_HR . "</a></p>
    </div>
  </div>
</body>
</html>";

                    $from_name = SITE_NAME . ' Careers';
                    $sent = send_notification_email($current_app['email'], $email_subject, $html_body, $from_name, EMAIL_CAREERS, EMAIL_NO_REPLY, EMAIL_CAREERS);

                    if ($sent) {
                        $email_status_param = '&mail=sent';
                        $timestamp_note = "[" . date('d M Y, h:i A') . "] Sent email to candidate (Status: {$status_label})";
                        $admin_note = $admin_note ? ($admin_note . "\n" . $timestamp_note) : $timestamp_note;
                    } else {
                        $email_status_param = '&mail=failed';
                    }

                    if (function_exists('log_admin_activity')) {
                        log_admin_activity(
                            'CANDIDATE_EMAIL_REPLY',
                            "Sent status email ({$status_label}) to {$current_app['email']} for Application #{$view_id}"
                        );
                    }
                }

                $db->prepare("UPDATE job_applications SET status=:s, admin_notes=:n WHERE id=:id")
                   ->execute([':s' => $new_status, ':n' => $admin_note, ':id' => $view_id]);

                header("Location: applications.php?view=$view_id&updated=1{$email_status_param}");
                exit;
            }
        }

        if ($view_id) {
            $stmt = $db->prepare("SELECT * FROM job_applications WHERE id=:id");
            $stmt->execute([':id' => $view_id]);
            $view = $stmt->fetch();
            if ($view && $view['status'] === 'new') {
                $db->prepare("UPDATE job_applications SET status='reviewed' WHERE id=:id")->execute([':id' => $view_id]);
                $view['status'] = 'reviewed';
            }
        }

        $filter = sanitize($_GET['filter'] ?? '');
        $search = sanitize($_GET['q']      ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));

        $where  = "WHERE 1=1";
        $params = [];
        if ($filter) { $where .= " AND status=:st"; $params[':st'] = $filter; }
        if ($search) {
            $where .= " AND (applicant_name LIKE :q OR email LIKE :q2 OR job_title LIKE :q3 OR current_location LIKE :q4)";
            $params[':q'] = $params[':q2'] = $params[':q3'] = $params[':q4'] = '%' . $search . '%';
        }

        // Count total records with bound parameters
        $cntStmt = $db->prepare("SELECT COUNT(*) FROM job_applications $where");
        $cntStmt->execute($params);
        $total_count = (int)$cntStmt->fetchColumn();

        // ── EXPORT CONTROLLER (EXCEL / CSV) ─────────────────────────
        if (isset($_GET['export']) && in_array($_GET['export'], ['excel', 'xls', 'csv'], true)) {
            $export_format = $_GET['export'] === 'csv' ? 'csv' : 'excel';
            $export_scope  = ($_GET['scope'] ?? 'filtered') === 'all' ? 'all' : 'filtered';

            $exp_where  = ($export_scope === 'all') ? "WHERE 1=1" : $where;
            $exp_params = ($export_scope === 'all') ? [] : $params;

            $exp_sql = "SELECT id, job_title, department, applicant_name, email, phone, 
                               current_location, experience_years, current_company, notice_period, 
                               expected_ctc, resume_filename, resume_path, cover_letter, 
                               linkedin_url, portfolio_url, status, admin_notes, created_at 
                        FROM job_applications 
                        $exp_where 
                        ORDER BY created_at DESC";

            $expStmt = $db->prepare($exp_sql);
            $expStmt->execute($exp_params);
            $export_rows = $expStmt->fetchAll(PDO::FETCH_ASSOC);

            // Formula injection protection (CWE-1236)
            $safe_val = function($val) {
                if ($val === null) return '';
                $str = trim((string)$val);
                if ($str !== '' && in_array($str[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
                    return "'" . $str;
                }
                return $str;
            };

            $filename = 'vortexsoft_applications_' . date('Y-m-d_His');

            // Audit log
            if (function_exists('log_admin_activity')) {
                log_admin_activity(
                    'EXPORT_APPLICATIONS',
                    "Exported " . count($export_rows) . " applications in " . strtoupper($export_format) . " format (Scope: {$export_scope})"
                );
            }

            if (ob_get_level()) ob_end_clean();

            if ($export_format === 'csv') {
                header('Content-Type: text/csv; charset=UTF-8');
                header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');

                // Output UTF-8 BOM for Excel compatibility
                echo "\xEF\xBB\xBF";

                $out = fopen('php://output', 'w');
                fputcsv($out, [
                    'ID',
                    'Applied Date',
                    'Applicant Name',
                    'Email Address',
                    'Phone Number',
                    'Job Title',
                    'Department',
                    'Current Location',
                    'Experience',
                    'Current Company',
                    'Notice Period',
                    'Expected CTC',
                    'Status',
                    'Resume File',
                    'Resume Download URL',
                    'LinkedIn URL',
                    'Portfolio URL',
                    'HR Admin Notes',
                    'Cover Letter'
                ]);

                foreach ($export_rows as $r) {
                    $resume_url = !empty($r['resume_filename']) ? SITE_URL . '/admin/download.php?id=' . $r['id'] : '';
                    $exp_text = ($r['experience_years'] !== null && $r['experience_years'] !== '') 
                        ? ($r['experience_years'] > 0 ? $r['experience_years'] . ' Yrs' : 'Fresher') 
                        : 'N/A';

                    fputcsv($out, [
                        $safe_val('#' . $r['id']),
                        $safe_val($r['created_at']),
                        $safe_val($r['applicant_name']),
                        $safe_val($r['email']),
                        $safe_val($r['phone']),
                        $safe_val($r['job_title']),
                        $safe_val($r['department'] ?? 'General'),
                        $safe_val($r['current_location'] ?? 'N/A'),
                        $safe_val($exp_text),
                        $safe_val($r['current_company'] ?? 'N/A'),
                        $safe_val($r['notice_period'] ?? 'N/A'),
                        $safe_val($r['expected_ctc'] ?? 'N/A'),
                        $safe_val(ucfirst($r['status'])),
                        $safe_val($r['resume_filename'] ?? 'None'),
                        $safe_val($resume_url),
                        $safe_val($r['linkedin_url'] ?? ''),
                        $safe_val($r['portfolio_url'] ?? ''),
                        $safe_val($r['admin_notes'] ?? ''),
                        $safe_val($r['cover_letter'] ?? '')
                    ]);
                }
                fclose($out);
                exit;

            } else {
                // EXCEL (.xls) XML/HTML SPREADSHEET
                header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
                header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
                header('Pragma: no-cache');
                header('Expires: 0');

                echo "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">\n";
                echo "<head>\n";
                echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n";
                echo "<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Job Applications</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->\n";
                echo "<style>\n";
                echo "body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #1e293b; }\n";
                echo "table { border-collapse: collapse; width: 100%; }\n";
                echo "th { background-color: #1C2280; color: #FFFFFF; font-weight: bold; border: 0.5pt solid #0d1259; padding: 10px 8px; text-align: left; vertical-align: middle; white-space: nowrap; }\n";
                echo "td { border: 0.5pt solid #CBD5E1; padding: 7px 8px; vertical-align: top; font-size: 10.5pt; }\n";
                echo ".text-cell { mso-number-format: \"\\@\"; }\n";
                echo ".num-cell { mso-number-format: \"0\"; text-align: center; }\n";
                echo ".date-cell { mso-number-format: \"yyyy\\-mm\\-dd\\ hh:mm\"; white-space: nowrap; }\n";
                echo ".row-even { background-color: #FFFFFF; }\n";
                echo ".row-odd { background-color: #F8FAFC; }\n";
                echo ".badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 9.5pt; text-align: center; }\n";
                echo ".badge-new { background-color: #FEE2E2; color: #991B1B; }\n";
                echo ".badge-reviewed { background-color: #E0E7FF; color: #3730A3; }\n";
                echo ".badge-shortlisted { background-color: #FEF3C7; color: #92400E; }\n";
                echo ".badge-interview { background-color: #DBEAFE; color: #1E40AF; }\n";
                echo ".badge-offered { background-color: #D1FAE5; color: #065F46; }\n";
                echo ".badge-rejected { background-color: #F3F4F6; color: #6B7280; }\n";
                echo ".badge-withdrawn { background-color: #F1F5F9; color: #475569; }\n";
                echo "</style>\n";
                echo "</head>\n";
                echo "<body>\n";
                echo "<table>\n";

                $filter_info = ($export_scope === 'all') ? 'All Records in Database' : ($filter ? 'Status: ' . ucfirst($filter) : 'All Applications (Filtered View)');
                if ($search) $filter_info .= ' | Search: "' . htmlspecialchars($search) . '"';

                echo "<tr><td colspan=\"19\" style=\"font-size:16pt;font-weight:bold;color:#1C2280;border:none;padding:12px 0 4px 0;\">VORTEXSOFT INNOVATIONS — JOB APPLICATIONS EXPORT</td></tr>\n";
                echo "<tr><td colspan=\"19\" style=\"font-size:10pt;color:#64748B;border:none;padding-bottom:12px;\">Generated: " . date('d M Y, h:i A') . " IST &nbsp;|&nbsp; Scope: " . htmlspecialchars($filter_info) . " &nbsp;|&nbsp; Total Records: " . count($export_rows) . "</td></tr>\n";

                echo "<thead><tr>\n";
                echo "<th>ID</th>\n";
                echo "<th>Applied Date</th>\n";
                echo "<th>Applicant Name</th>\n";
                echo "<th>Email Address</th>\n";
                echo "<th>Phone Number</th>\n";
                echo "<th>Position / Job Title</th>\n";
                echo "<th>Department</th>\n";
                echo "<th>Current Location</th>\n";
                echo "<th>Experience</th>\n";
                echo "<th>Current Company</th>\n";
                echo "<th>Notice Period</th>\n";
                echo "<th>Expected CTC</th>\n";
                echo "<th>Status</th>\n";
                echo "<th>Resume File</th>\n";
                echo "<th>Resume Download Link</th>\n";
                echo "<th>LinkedIn Profile</th>\n";
                echo "<th>Portfolio URL</th>\n";
                echo "<th>HR Admin Notes</th>\n";
                echo "<th>Cover Letter</th>\n";
                echo "</tr></thead>\n";
                echo "<tbody>\n";

                $idx = 0;
                foreach ($export_rows as $r) {
                    $row_class = ($idx++ % 2 === 0) ? 'row-even' : 'row-odd';
                    $status_key = strtolower($r['status']);
                    $badge_class = 'badge-' . ($status_key ?: 'new');
                    $resume_url = !empty($r['resume_filename']) ? SITE_URL . '/admin/download.php?id=' . $r['id'] : '';
                    $exp_text = ($r['experience_years'] !== null && $r['experience_years'] !== '') 
                        ? ($r['experience_years'] > 0 ? $r['experience_years'] . ' Yrs' : 'Fresher') 
                        : 'N/A';

                    echo "<tr class=\"{$row_class}\">\n";
                    echo "<td class=\"num-cell\">#" . (int)$r['id'] . "</td>\n";
                    echo "<td class=\"date-cell\">" . htmlspecialchars($r['created_at']) . "</td>\n";
                    echo "<td><strong>" . htmlspecialchars($safe_val($r['applicant_name'])) . "</strong></td>\n";
                    echo "<td><a href=\"mailto:" . htmlspecialchars($r['email']) . "\">" . htmlspecialchars($safe_val($r['email'])) . "</a></td>\n";
                    echo "<td class=\"text-cell\">" . htmlspecialchars($safe_val($r['phone'])) . "</td>\n";
                    echo "<td>" . htmlspecialchars($safe_val($r['job_title'])) . "</td>\n";
                    echo "<td>" . htmlspecialchars($safe_val($r['department'] ?? 'General')) . "</td>\n";
                    echo "<td>" . htmlspecialchars($safe_val($r['current_location'] ?? 'N/A')) . "</td>\n";
                    echo "<td style=\"text-align:center;\">" . htmlspecialchars($safe_val($exp_text)) . "</td>\n";
                    echo "<td>" . htmlspecialchars($safe_val($r['current_company'] ?? 'N/A')) . "</td>\n";
                    echo "<td>" . htmlspecialchars($safe_val($r['notice_period'] ?? 'N/A')) . "</td>\n";
                    echo "<td>" . htmlspecialchars($safe_val($r['expected_ctc'] ?? 'N/A')) . "</td>\n";
                    echo "<td style=\"text-align:center;\"><span class=\"badge {$badge_class}\">" . htmlspecialchars(ucfirst($r['status'])) . "</span></td>\n";
                    echo "<td>" . htmlspecialchars($safe_val($r['resume_filename'] ?? 'None')) . "</td>\n";
                    echo "<td>";
                    if ($resume_url) {
                        echo "<a href=\"" . htmlspecialchars($resume_url) . "\" target=\"_blank\">Download Resume</a>";
                    } else {
                        echo "<span style=\"color:#94a3b8;\">No File</span>";
                    }
                    echo "</td>\n";
                    echo "<td>";
                    if (!empty($r['linkedin_url'])) {
                        echo "<a href=\"" . htmlspecialchars($r['linkedin_url']) . "\" target=\"_blank\">LinkedIn</a>";
                    } else {
                        echo "—";
                    }
                    echo "</td>\n";
                    echo "<td>";
                    if (!empty($r['portfolio_url'])) {
                        echo "<a href=\"" . htmlspecialchars($r['portfolio_url']) . "\" target=\"_blank\">Portfolio</a>";
                    } else {
                        echo "—";
                    }
                    echo "</td>\n";
                    echo "<td style=\"max-width:250px;\">" . nl2br(htmlspecialchars($safe_val($r['admin_notes'] ?? ''))) . "</td>\n";
                    echo "<td style=\"max-width:300px;\">" . nl2br(htmlspecialchars($safe_val($r['cover_letter'] ?? ''))) . "</td>\n";
                    echo "</tr>\n";
                }

                echo "</tbody>\n";
                echo "</table>\n";
                echo "</body>\n";
                echo "</html>\n";
                exit;
            }
        }

        $pg   = paginate($total_count, ITEMS_PER_PAGE, $page);
        $stmt = $db->prepare("SELECT id, applicant_name, email, phone, job_title, experience_years, status, created_at, resume_filename, resume_path FROM job_applications $where ORDER BY created_at DESC LIMIT :l OFFSET :o");
        foreach ($params as $k => &$v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':l', $pg['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':o', $pg['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $apps = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Job Applications — Vortexsoft Admin Panel</title>
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="/assets/vendor/bootstrap.min.css">
<link rel="stylesheet" href="/assets/vendor/fontawesome/all.min.css">
<link rel="stylesheet" href="/assets/vendor/fonts.css">
<link rel="icon" type="image/x-icon" href="/favicon.ico?v=20260912">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=20260912">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=20260912">
<link rel="icon" type="image/jpeg" sizes="1024x1024" href="/icon.jpg?v=20260912">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--dark:#080B1A;--primary:#1C2280;--accent:#CC2228;--sidebar-w:260px}
body{font-family:'Inter',sans-serif;background:#f0f2ff;color:#1e293b;min-height:100vh;display:flex}
.admin-sidebar{width:var(--sidebar-w);background:var(--dark);min-height:100vh;position:fixed;top:0;left:0;z-index:1000;display:flex;flex-direction:column;transition:.3s}
.sidebar-logo{padding:24px 20px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;justify-content:space-between}
.sidebar-logo img{height:44px;object-fit:contain}
.sidebar-logo .sub{font-size:11px;color:rgba(255,255,255,.4);letter-spacing:1px;text-transform:uppercase;margin-top:6px}
.sidebar-nav{flex:1;padding:16px 0;overflow-y:auto}
.nav-section{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:rgba(255,255,255,.3);padding:12px 20px 6px}
.sidebar-link{display:flex;align-items:center;gap:12px;padding:11px 20px;color:rgba(255,255,255,.6);font-size:13.5px;font-weight:500;text-decoration:none;transition:.2s;position:relative}
.sidebar-link:hover,.sidebar-link.active{color:#fff;background:rgba(255,255,255,.07)}
.sidebar-link.active::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--accent);border-radius:0 3px 3px 0}
.sidebar-link .icon{width:20px;text-align:center;font-size:14px;color:rgba(255,255,255,.4)}
.sidebar-link:hover .icon,.sidebar-link.active .icon{color:var(--accent)}
.sidebar-footer{padding:16px 20px;border-top:1px solid rgba(255,255,255,.06)}
.btn-logout{background:rgba(204,34,40,.15);border:1px solid rgba(204,34,40,.3);color:#CC2228;width:100%;padding:9px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-align:center;text-decoration:none;display:block;transition:.2s}
.btn-logout:hover{background:#CC2228;color:#fff}
.admin-main{margin-left:var(--sidebar-w);flex:1;padding:28px;transition:.3s}
.mobile-header{display:none;background:var(--dark);padding:14px 20px;align-items:center;justify-content:space-between;color:#fff}
.table-card{background:#fff;border-radius:16px;border:1px solid #e8ecff;overflow:hidden}
.table-card-header{padding:18px 24px;border-bottom:1px solid #f0f4ff;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
.table-card-header h5{font-family:'Poppins',sans-serif;font-weight:700;font-size:15px;color:#1e293b;margin:0}
table{width:100%;border-collapse:collapse}
th{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;padding:12px 20px;background:#f8faff;border-bottom:1px solid #f0f4ff;white-space:nowrap}
td{font-size:13.5px;padding:13px 20px;border-bottom:1px solid #f8f9ff;vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:#fafbff}
.status-badge{font-size:11px;font-weight:700;padding:4px 10px;border-radius:100px;border:1px solid}
.status-new{background:#fff0f0;color:#CC2228;border-color:rgba(204,34,40,.2)}
.status-reviewed{background:#f0fdf4;color:#10b981;border-color:rgba(16,185,129,.2)}
.status-shortlisted,.status-interview{background:#fffbeb;color:#f59e0b;border-color:rgba(245,158,11,.2)}
.status-offered{background:#f0f9ff;color:#0ea5e9;border-color:rgba(14,165,233,.2)}
.status-rejected{background:#fef2f2;color:#ef4444;border-color:rgba(239,68,68,.2)}
.action-btn{font-size:12px;font-weight:600;padding:5px 12px;border-radius:7px;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:5px;background:rgba(28,34,128,.08);color:#1C2280;transition:.2s;margin-right:4px}
.action-btn:hover{background:#1C2280;color:#fff}
.action-btn-delete{background:rgba(204,34,40,.08);color:#CC2228}
.action-btn-delete:hover{background:#CC2228;color:#fff}
.detail-card{background:#fff;border-radius:16px;border:1px solid #e8ecff;padding:32px}
@media(max-width:1024px){
  body{flex-direction:column}
  .admin-sidebar{transform:translateX(-100%)}
  .admin-sidebar.show{transform:translateX(0)}
  .admin-main{margin-left:0;padding:20px}
  .mobile-header{display:flex}
}
</style>
</head>
<body>

<div class="mobile-header">
  <img src="/logo-header.png" alt="Vortexsoft" style="height:32px;">
  <button class="btn text-white p-0" id="sidebarToggleBtn" style="font-size:20px;"><i class="fas fa-bars"></i></button>
</div>

<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-logo">
    <div>
      <img src="/logo-header.png?v=20260912" alt="Vortexsoft Innovations">
      <div class="sub">Admin Panel</div>
    </div>
    <button class="btn text-white p-0 d-lg-none" id="sidebarCloseBtn"><i class="fas fa-times"></i></button>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">Main</div>
    <a href="dashboard.php" class="sidebar-link"><span class="icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard</a>
    <a href="contacts.php" class="sidebar-link"><span class="icon"><i class="fas fa-envelope"></i></span> Inquiries</a>
    <a href="applications.php" class="sidebar-link active"><span class="icon"><i class="fas fa-briefcase"></i></span> Applications</a>
    <div class="nav-section">Content</div>
    <a href="blog-posts.php" class="sidebar-link"><span class="icon"><i class="fas fa-pen-alt"></i></span> Blog Posts</a>
    <a href="newsletter.php" class="sidebar-link"><span class="icon"><i class="fas fa-paper-plane"></i></span> Newsletter</a>
    <div class="nav-section">System</div>
    <a href="settings.php" class="sidebar-link"><span class="icon"><i class="fas fa-cog"></i></span> Settings</a>
    <a href="/index.php" target="_blank" class="sidebar-link"><span class="icon"><i class="fas fa-external-link-alt"></i></span> View Website</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt me-2"></i> Sign Out</a>
  </div>
</aside>

<main class="admin-main">
  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
      <h1 class="mb-1"><i class="fas fa-briefcase me-2" style="color:#CC2228;"></i> Job Applications</h1>
      <div style="font-size:13px;color:#64748b;">Review, filter, and export candidate applications submitted via careers page.</div>
    </div>
    <?php if (!$view): ?>
    <div class="d-flex gap-2 align-items-center">
      <div class="btn-group">
        <a href="applications.php?<?= http_build_query(array_merge($_GET, ['export' => 'excel', 'scope' => 'filtered'])) ?>" class="btn" style="background:#10b981;color:#fff;border-radius:10px 0 0 10px;font-weight:700;font-size:13px;padding:10px 18px;border:none;box-shadow:0 2px 8px rgba(16,185,129,.25);">
          <i class="fas fa-file-excel me-1"></i> Export Sheet
        </a>
        <button type="button" class="btn dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" style="background:#059669;color:#fff;border-radius:0 10px 10px 0;border:none;padding-right:12px;padding-left:12px;">
          <span class="visually-hidden">Toggle Export Options</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="border-radius:12px;font-size:13px;min-width:270px;padding:8px;border:1px solid #e2e8f0;">
          <li><div class="dropdown-header text-uppercase" style="font-size:10px;font-weight:700;letter-spacing:.5px;color:#64748b;">Current Filter (<?= $total_count ?? 0 ?> Applications)</div></li>
          <li>
            <a class="dropdown-item d-flex align-items-center gap-2 py-2" style="border-radius:8px;font-weight:600;" href="applications.php?<?= http_build_query(array_merge($_GET, ['export' => 'excel', 'scope' => 'filtered'])) ?>">
              <i class="fas fa-file-excel text-success" style="font-size:16px;"></i>
              <div>
                <div>Export to Excel (.xls)</div>
                <small class="text-muted" style="font-size:11px;">Formatted sheet with colors &amp; styles</small>
              </div>
            </a>
          </li>
          <li>
            <a class="dropdown-item d-flex align-items-center gap-2 py-2" style="border-radius:8px;font-weight:600;" href="applications.php?<?= http_build_query(array_merge($_GET, ['export' => 'csv', 'scope' => 'filtered'])) ?>">
              <i class="fas fa-file-csv text-primary" style="font-size:16px;"></i>
              <div>
                <div>Export to CSV (.csv)</div>
                <small class="text-muted" style="font-size:11px;">Standard UTF-8 comma-separated</small>
              </div>
            </a>
          </li>
          <li><hr class="dropdown-divider my-2"></li>
          <li><div class="dropdown-header text-uppercase" style="font-size:10px;font-weight:700;letter-spacing:.5px;color:#64748b;">Entire Database</div></li>
          <li>
            <a class="dropdown-item d-flex align-items-center gap-2 py-2" style="border-radius:8px;" href="applications.php?export=excel&scope=all">
              <i class="fas fa-database text-success" style="font-size:14px;"></i> Export All to Excel (.xls)
            </a>
          </li>
          <li>
            <a class="dropdown-item d-flex align-items-center gap-2 py-2" style="border-radius:8px;" href="applications.php?export=csv&scope=all">
              <i class="fas fa-database text-primary" style="font-size:14px;"></i> Export All to CSV (.csv)
            </a>
          </li>
        </ul>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($_GET['updated'])): ?>
    <?php if (isset($_GET['mail']) && $_GET['mail'] === 'sent'): ?>
    <div class="alert alert-success mb-4 shadow-sm" style="border-radius:12px;border:1px solid #bbf7d0;background:#f0fdf4;color:#166534;">
      <i class="fas fa-paper-plane me-2 text-success"></i> <strong>Application updated &amp; email sent!</strong> The candidate has been notified at <?= htmlspecialchars($view['email'] ?? '') ?>.
    </div>
    <?php elseif (isset($_GET['mail']) && $_GET['mail'] === 'failed'): ?>
    <div class="alert alert-warning mb-4 shadow-sm" style="border-radius:12px;border:1px solid #fed7aa;background:#fffbeb;color:#9a3412;">
      <i class="fas fa-exclamation-triangle me-2 text-warning"></i> <strong>Application status updated</strong>, but email delivery to <?= htmlspecialchars($view['email'] ?? '') ?> failed. Please verify server SMTP configuration.
    </div>
    <?php else: ?>
    <div class="alert alert-success mb-4" style="border-radius:12px;"><i class="fas fa-check-circle me-2"></i> Application updated successfully.</div>
    <?php endif; ?>
  <?php endif; ?>

  <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
  <div class="alert alert-success mb-4" style="border-radius:12px;"><i class="fas fa-check-circle me-2"></i> Application deleted successfully.</div>
  <?php endif; ?>

  <?php if ($view): ?>
  <!-- Detail View -->
  <div class="mb-3"><a href="applications.php" style="color:#1C2280;font-size:14px;font-weight:600;text-decoration:none;"><i class="fas fa-arrow-left me-1"></i> Back to Applications</a></div>
  <div class="detail-card">
    <div class="row gy-3">
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Position Applied For</div><div style="font-size:18px;font-weight:800;color:#1C2280;"><?= htmlspecialchars($view['job_title']) ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Applicant Name</div><div style="font-size:16px;font-weight:700;color:#1e293b;"><?= htmlspecialchars($view['applicant_name']) ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Email</div><div><a href="mailto:<?= htmlspecialchars($view['email']) ?>" style="font-size:15px;color:#1C2280;font-weight:600;"><?= htmlspecialchars($view['email']) ?></a></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Phone</div><div style="font-size:14px;font-weight:600;"><?= htmlspecialchars($view['phone']) ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Location</div><div style="font-size:14px;"><?= htmlspecialchars($view['current_location'] ?? 'Not provided') ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Experience / Current Co.</div><div style="font-size:14px;"><?= htmlspecialchars($view['experience_years'] ? $view['experience_years'].' Yrs' : 'N/A') ?> — <?= htmlspecialchars($view['current_company'] ?? 'N/A') ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Notice Period &amp; Expected CTC</div><div style="font-size:14px;"><?= htmlspecialchars($view['notice_period'] ?? 'N/A') ?> | <?= htmlspecialchars($view['expected_ctc'] ?? 'N/A') ?></div></div>
      <div class="col-md-6"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Resume File</div>
        <?php if(!empty($view['resume_filename'])): ?>
        <a href="download.php?id=<?= $view['id'] ?>" class="btn btn-sm" style="background:#1C2280;color:#fff;border-radius:8px;font-weight:600;"><i class="fas fa-download me-1"></i> Download <?= htmlspecialchars($view['resume_filename']) ?></a>
        <?php else: ?><span style="color:#94a3b8;font-size:13px;">No resume file attached</span><?php endif; ?>
      </div>
      <?php if($view['cover_letter']): ?>
      <div class="col-12"><div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:6px;">Cover Letter</div><div style="background:#f8f9ff;border-radius:12px;padding:20px;font-size:14px;line-height:1.7;white-space:pre-wrap;"><?= htmlspecialchars($view['cover_letter']) ?></div></div>
      <?php endif; ?>

      <!-- Candidate Status Update & Email Reply Form -->
      <div class="col-12 mt-4 pt-4" style="border-top:1px solid #e8ecff;">
        <h5 style="color:#1C2280;font-weight:700;font-size:16px;margin-bottom:16px;">
          <i class="fas fa-user-check me-2" style="color:#CC2228;"></i> Update Status &amp; Reply to Candidate
        </h5>
        <form method="POST" action="applications.php?view=<?= $view['id'] ?>" id="candidateStatusForm">
          <input type="hidden" name="status_update" value="1">
          <div class="row g-3">
            <div class="col-md-4">
              <label style="font-size:12px;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:6px;">Candidate Status</label>
              <select name="status" id="statusSelect" class="form-select" style="border-radius:10px;font-weight:600;">
                <?php foreach(['new','reviewed','shortlisted','interview','offered','rejected','withdrawn'] as $st): ?>
                <option value="<?= $st ?>" <?= $view['status']===$st?'selected':'' ?>><?= ucfirst($st) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-8">
              <label style="font-size:12px;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:6px;">Internal HR Notes (Private — Not seen by candidate)</label>
              <input type="text" name="admin_notes" class="form-control" style="border-radius:10px;" placeholder="Internal note, e.g. Passed initial screening; schedule technical round" value="<?= htmlspecialchars($view['admin_notes'] ?? '') ?>">
            </div>

            <!-- Email Toggle & Composer -->
            <div class="col-12 mt-3">
              <div class="card p-3" style="background:#f8faff;border:1px solid #dbeafe;border-radius:12px;">
                <div class="form-check form-switch mb-2 d-flex align-items-center gap-2">
                  <input class="form-check-input" type="checkbox" role="switch" id="sendEmailToggle" name="send_email" value="1" checked style="width:2.5em;height:1.3em;cursor:pointer;">
                  <label class="form-check-label fw-bold" for="sendEmailToggle" style="color:#1e293b;cursor:pointer;font-size:14px;">
                    <i class="fas fa-envelope-open-text me-1" style="color:#1C2280;"></i> Send Status Update &amp; Reply Email to Candidate (<span style="color:#CC2228;"><?= htmlspecialchars($view['email']) ?></span>)
                  </label>
                </div>

                <div id="emailComposerSection" style="margin-top:12px;">
                  <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                    <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">Quick Templates:</span>
                    <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size:11.5px;border-radius:6px;" onclick="loadTemplate('shortlisted')">🎯 Shortlisted</button>
                    <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size:11.5px;border-radius:6px;" onclick="loadTemplate('interview')">📅 Interview Invitation</button>
                    <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size:11.5px;border-radius:6px;" onclick="loadTemplate('offered')">🎉 Job Offer</button>
                    <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size:11.5px;border-radius:6px;" onclick="loadTemplate('reviewed')">📝 In Review</button>
                    <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size:11.5px;border-radius:6px;" onclick="loadTemplate('rejected')">✉️ Polite Rejection</button>
                  </div>

                  <div class="mb-3">
                    <label style="font-size:12px;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:4px;">Email Subject</label>
                    <input type="text" name="email_subject" id="emailSubject" class="form-control" style="border-radius:8px;font-size:13.5px;" value="Update regarding your application for <?= htmlspecialchars($view['job_title']) ?> — <?= htmlspecialchars(SITE_NAME) ?>">
                  </div>

                  <div class="mb-2">
                    <label style="font-size:12px;font-weight:700;text-transform:uppercase;color:#64748b;display:block;margin-bottom:4px;">Email Message / Reply Body</label>
                    <textarea name="email_message" id="emailMessage" rows="5" class="form-control" style="border-radius:8px;font-size:13.5px;line-height:1.6;" placeholder="Type your message to the candidate..."></textarea>
                    <div style="font-size:11.5px;color:#64748b;margin-top:4px;">
                      <i class="fas fa-info-circle me-1"></i> The candidate will receive this message in a branded <?= htmlspecialchars(SITE_NAME) ?> HTML email layout with application reference <strong>#<?= $view['id'] ?></strong>.
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action buttons -->
            <div class="col-12 d-flex gap-2 mt-3 flex-wrap align-items-center">
              <button type="submit" class="btn" style="background:#1C2280;color:#fff;border-radius:8px;font-size:13.5px;font-weight:700;padding:10px 24px;">
                <i class="fas fa-paper-plane me-1"></i> Update Status &amp; Send Email
              </button>
              <button type="submit" onclick="document.getElementById('sendEmailToggle').checked=false;" class="btn btn-outline-secondary" style="border-radius:8px;font-size:13px;font-weight:600;padding:10px 20px;">
                <i class="fas fa-save me-1"></i> Save Status Only (No Email)
              </button>
              <a href="mailto:<?= htmlspecialchars($view['email']) ?>?subject=Regarding your application for <?= urlencode($view['job_title']) ?>" class="btn" style="background:rgba(28,34,128,.08);color:#1C2280;border-radius:8px;font-size:13px;font-weight:600;padding:10px 18px;" title="Open in default desktop email client">
                <i class="fas fa-external-link-alt me-1"></i> Desktop Client
              </a>
              <a href="applications.php?delete=<?= $view['id'] ?>" class="btn action-btn-delete ms-auto" style="border-radius:8px;font-size:13px;font-weight:700;padding:10px 20px;" onclick="return confirm('Delete this application permanently?');">
                <i class="fas fa-trash me-1"></i> Delete Application
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php else: ?>
  <!-- List View -->
  <div class="table-card">
    <div class="table-card-header">
      <div class="d-flex align-items-center gap-3 flex-wrap">
        <h5>All Job Applications <span class="badge rounded-pill bg-light text-dark border ms-1" style="font-size:12px;"><?= $total_count ?? 0 ?></span></h5>
        <div class="dropdown">
          <button class="btn btn-sm dropdown-toggle" type="button" id="tableExportDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background:rgba(16,185,129,.12);color:#059669;border-radius:8px;font-weight:700;font-size:12px;padding:5px 12px;border:1px solid rgba(16,185,129,.25);">
            <i class="fas fa-file-excel me-1"></i> Export Sheet
          </button>
          <ul class="dropdown-menu shadow-sm" aria-labelledby="tableExportDropdown" style="border-radius:10px;font-size:12.5px;min-width:220px;border:1px solid #e2e8f0;">
            <li><a class="dropdown-item py-2" href="applications.php?<?= http_build_query(array_merge($_GET, ['export' => 'excel', 'scope' => 'filtered'])) ?>"><i class="fas fa-file-excel text-success me-2"></i> Export Excel (.xls)</a></li>
            <li><a class="dropdown-item py-2" href="applications.php?<?= http_build_query(array_merge($_GET, ['export' => 'csv', 'scope' => 'filtered'])) ?>"><i class="fas fa-file-csv text-primary me-2"></i> Export CSV (.csv)</a></li>
            <li><hr class="dropdown-divider my-1"></li>
            <li><a class="dropdown-item py-2 text-muted" href="applications.php?export=excel&scope=all"><i class="fas fa-database text-success me-2"></i> Export All to Excel</a></li>
            <li><a class="dropdown-item py-2 text-muted" href="applications.php?export=csv&scope=all"><i class="fas fa-database text-primary me-2"></i> Export All to CSV</a></li>
          </ul>
        </div>
      </div>
      <form action="applications.php" method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <div class="input-group input-group-sm" style="width:auto;min-width:210px;">
          <span class="input-group-text bg-white border-end-0" style="border-radius:8px 0 0 8px;color:#94a3b8;"><i class="fas fa-search"></i></span>
          <input type="text" name="q" class="form-control form-control-sm border-start-0" placeholder="Search applicant, job, email..." value="<?= htmlspecialchars($search) ?>" style="border-radius:0 8px 8px 0;">
        </div>
        <select name="filter" class="form-select form-select-sm" style="border-radius:8px;width:auto;">
          <option value="">All Statuses</option>
          <option value="new" <?= $filter==='new'?'selected':'' ?>>New</option>
          <option value="reviewed" <?= $filter==='reviewed'?'selected':'' ?>>Reviewed</option>
          <option value="shortlisted" <?= $filter==='shortlisted'?'selected':'' ?>>Shortlisted</option>
          <option value="interview" <?= $filter==='interview'?'selected':'' ?>>Interview</option>
          <option value="offered" <?= $filter==='offered'?'selected':'' ?>>Offered</option>
          <option value="rejected" <?= $filter==='rejected'?'selected':'' ?>>Rejected</option>
          <option value="withdrawn" <?= $filter==='withdrawn'?'selected':'' ?>>Withdrawn</option>
        </select>
        <button type="submit" class="btn btn-sm btn-primary" style="border-radius:8px;background:#1C2280;border-color:#1C2280;font-weight:600;"><i class="fas fa-filter me-1"></i> Filter</button>
        <?php if ($filter || $search): ?>
        <a href="applications.php" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;" title="Clear Filters"><i class="fas fa-times"></i></a>
        <?php endif; ?>
      </form>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead><tr><th>#</th><th>Applicant</th><th>Position</th><th>Experience</th><th>Resume</th><th>Applied</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if(empty($apps)): ?>
          <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;"><i class="fas fa-briefcase" style="font-size:32px;display:block;margin-bottom:12px;opacity:.3;"></i> No applications found.</td></tr>
          <?php else: ?>
          <?php foreach($apps as $a): ?>
          <tr>
            <td style="color:#94a3b8;font-size:12px;">#<?= $a['id'] ?></td>
            <td style="font-weight:600;"><?= htmlspecialchars($a['applicant_name']) ?><br><span style="font-size:12px;color:#94a3b8;"><?= htmlspecialchars($a['email']) ?></span></td>
            <td style="font-weight:600;color:#1C2280;"><?= htmlspecialchars($a['job_title']) ?></td>
            <td style="font-size:13px;"><?= htmlspecialchars($a['experience_years'] ? $a['experience_years'].' Yrs' : 'Fresh') ?></td>
            <td>
              <?php if(!empty($a['resume_filename'])): ?>
              <a href="download.php?id=<?= $a['id'] ?>" style="font-size:12px;color:#CC2228;font-weight:700;"><i class="fas fa-file-pdf me-1"></i> Resume</a>
              <?php else: ?><span style="color:#94a3b8;font-size:12px;">None</span><?php endif; ?>
            </td>
            <td style="font-size:12px;color:#94a3b8;white-space:nowrap;"><?= time_ago($a['created_at']) ?></td>
            <td><span class="status-badge status-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
            <td>
              <a href="applications.php?view=<?= $a['id'] ?>" class="action-btn"><i class="fas fa-eye"></i> View</a>
              <a href="applications.php?delete=<?= $a['id'] ?>" class="action-btn action-btn-delete" onclick="return confirm('Delete this application?');"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if (isset($pg) && $pg['total_pages'] > 1): ?>
    <div style="padding:16px 24px;">
      <nav><ul class="pagination mb-0 gap-1">
        <?php if($pg['has_prev']): ?><li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$pg['prev_page']])) ?>" style="border-radius:6px;font-size:13px;"><i class="fas fa-chevron-left"></i></a></li><?php endif; ?>
        <?php for($n=1;$n<=$pg['total_pages'];$n++): ?><li class="page-item <?= $n==$pg['current_page']?'active':'' ?>"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$n])) ?>" style="border-radius:6px;font-size:13px;<?= $n==$pg['current_page']?'background:#1C2280;border-color:#1C2280;':'' ?>"><?= $n ?></a></li><?php endfor; ?>
        <?php if($pg['has_next']): ?><li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$pg['next_page']])) ?>" style="border-radius:6px;font-size:13px;"><i class="fas fa-chevron-right"></i></a></li><?php endif; ?>
      </ul></nav>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</main>
<script src="/assets/vendor/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('sidebarToggleBtn')?.addEventListener('click', function(){
  document.getElementById('adminSidebar').classList.toggle('show');
});
document.getElementById('sidebarCloseBtn')?.addEventListener('click', function(){
  document.getElementById('adminSidebar').classList.remove('show');
});

// Candidate Status Email Templates
<?php if (!empty($view)): ?>
(function() {
  const candidateName = <?= json_encode($view['applicant_name'] ?? 'Candidate') ?>;
  const jobTitle      = <?= json_encode($view['job_title'] ?? 'Position') ?>;
  const companyName   = <?= json_encode(SITE_NAME) ?>;
  const appId         = <?= (int)($view['id'] ?? 0) ?>;

  const templates = {
    shortlisted: {
      subject: `Application Shortlisted: ${jobTitle} — ${companyName}`,
      message: `Dear ${candidateName},\n\nWe are pleased to inform you that your profile has been shortlisted for the ${jobTitle} position at ${companyName}.\n\nOur recruitment team reviewed your qualifications and experience, and we would like to proceed with the next round of our hiring process. Our team will contact you shortly to coordinate your schedule.\n\nPlease keep your phone and email accessible. If you have any questions in the meantime, feel free to reply directly to this message.\n\nBest regards,\n${companyName} Recruitment Team`
    },
    interview: {
      subject: `Interview Invitation: ${jobTitle} — ${companyName}`,
      message: `Dear ${candidateName},\n\nWe would like to invite you for an interview for the ${jobTitle} role at ${companyName}.\n\nInterview Details:\n• Format: Online Video Call / Google Meet\n• Date & Time: [Please specify Date & Time, e.g. Tuesday at 3:00 PM IST]\n• Meeting Link: [Add Google Meet / Zoom link here]\n• Agenda: Technical discussion & role overview\n\nPlease reply to this email to confirm your availability or propose an alternative time slot if needed.\n\nBest regards,\n${companyName} Recruitment Team`
    },
    offered: {
      subject: `Job Offer: ${jobTitle} — ${companyName}`,
      message: `Dear ${candidateName},\n\nCongratulations! We are delighted to formally extend an offer for the position of ${jobTitle} at ${companyName}.\n\nWe were very impressed by your performance throughout the interview rounds and are excited about the prospect of you joining our team.\n\nOur HR department will share your formal offer letter, compensation breakdown, and onboarding documentation shortly.\n\nPlease reply to confirm receipt. We look forward to welcoming you to the ${companyName} family!\n\nWarm regards,\n${companyName} HR Team`
    },
    reviewed: {
      subject: `Application Status: Under Review for ${jobTitle} — ${companyName}`,
      message: `Dear ${candidateName},\n\nThank you for applying for the ${jobTitle} position at ${companyName} (Ref #${appId}).\n\nWe wanted to let you know that your application is actively under review by our hiring team. We appreciate your patience while we evaluate candidates.\n\nWe will follow up with you as soon as the review process is complete.\n\nBest regards,\n${companyName} Recruitment Team`
    },
    rejected: {
      subject: `Update regarding your application for ${jobTitle} — ${companyName}`,
      message: `Dear ${candidateName},\n\nThank you for your interest in ${companyName} and for taking the time to apply for the ${jobTitle} position.\n\nAfter careful consideration of all applications, we regret to inform you that we have decided to move forward with other candidates whose profiles more closely match our current requirements.\n\nPlease note that we will keep your resume in our talent network and will gladly contact you should a suitable opportunity arise in the future.\n\nWe wish you the very best in your career pursuits.\n\nSincerely,\n${companyName} Talent Acquisition`
    },
    withdrawn: {
      subject: `Application Withdrawn: ${jobTitle} — ${companyName}`,
      message: `Dear ${candidateName},\n\nThis email confirms that your application for the ${jobTitle} position at ${companyName} (Ref #${appId}) has been withdrawn as requested.\n\nThank you for considering ${companyName}, and we wish you continued success.\n\nBest regards,\n${companyName} HR Team`
    }
  };

  const statusSelect    = document.getElementById('statusSelect');
  const subjectInput    = document.getElementById('emailSubject');
  const messageInput    = document.getElementById('emailMessage');
  const sendEmailToggle = document.getElementById('sendEmailToggle');
  const emailSection    = document.getElementById('emailComposerSection');

  window.loadTemplate = function(key) {
    if (templates[key]) {
      subjectInput.value = templates[key].subject;
      messageInput.value = templates[key].message;
      if (statusSelect && statusSelect.value !== key && Array.from(statusSelect.options).some(o => o.value === key)) {
        statusSelect.value = key;
      }
      if (sendEmailToggle && !sendEmailToggle.checked) {
        sendEmailToggle.checked = true;
        if (emailSection) emailSection.style.display = 'block';
      }
    }
  };

  if (statusSelect) {
    statusSelect.addEventListener('change', function() {
      const selected = this.value;
      if (templates[selected]) {
        subjectInput.value = templates[selected].subject;
        messageInput.value = templates[selected].message;
      }
    });
  }

  if (sendEmailToggle && emailSection) {
    sendEmailToggle.addEventListener('change', function() {
      emailSection.style.display = this.checked ? 'block' : 'none';
    });
  }

  // Set initial default template based on current status if message is empty
  const initialStatus = statusSelect ? statusSelect.value : 'reviewed';
  if (messageInput && !messageInput.value.trim() && templates[initialStatus]) {
    messageInput.value = templates[initialStatus].message;
    subjectInput.value = templates[initialStatus].subject;
  }
})();
<?php endif; ?>
</script>
</body>
</html>
