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
$all_count = 0;

if ($db) {
    try {
        // Delete Single Application (Direct Permanent Delete - No Trash)
        if (isset($_GET['delete'])) {
            $did = (int)$_GET['delete'];
            if ($did > 0) {
                // Remove stored resume file from server disk if present
                $fStmt = $db->prepare("SELECT resume_filename FROM job_applications WHERE id = :id");
                $fStmt->execute([':id' => $did]);
                $rFile = $fStmt->fetchColumn();
                if (!empty($rFile)) {
                    $rPath = UPLOADS_PATH . '/resumes/' . basename($rFile);
                    if (file_exists($rPath) && is_file($rPath)) @unlink($rPath);
                }
                $db->prepare("DELETE FROM job_applications WHERE id = :id")->execute([':id' => $did]);
                if (function_exists('log_admin_activity')) {
                    log_admin_activity('DELETE_APPLICATION', "Permanently deleted application #{$did}");
                }
            }
            header('Location: applications.php?msg=deleted');
            exit;
        }

        // Bulk / Batch Delete Applications (Direct Permanent Deletion - No Trash)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_delete') {
            if (!verify_csrf()) {
                header('Location: applications.php?err=csrf');
                exit;
            }

            $delete_scope  = sanitize($_POST['delete_scope'] ?? 'selected');
            $deleted_count = 0;

            if ($delete_scope === 'selected') {
                $raw_ids = $_POST['selected_ids'] ?? [];
                $ids = array_values(array_filter(array_map('intval', (array)$raw_ids), fn($i) => $i > 0));

                if (empty($ids)) {
                    header('Location: applications.php?err=no_selection');
                    exit;
                }

                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                // Remove resume files from storage
                $fStmt = $db->prepare("SELECT resume_filename FROM job_applications WHERE id IN ($placeholders)");
                $fStmt->execute($ids);
                $rFiles = $fStmt->fetchAll(PDO::FETCH_COLUMN);
                foreach ($rFiles as $rf) {
                    if (!empty($rf)) {
                        $p = UPLOADS_PATH . '/resumes/' . basename($rf);
                        if (file_exists($p) && is_file($p)) @unlink($p);
                    }
                }

                $delStmt = $db->prepare("DELETE FROM job_applications WHERE id IN ($placeholders)");
                $delStmt->execute($ids);
                $deleted_count = $delStmt->rowCount();

                if (function_exists('log_admin_activity')) {
                    log_admin_activity('BULK_DELETE_APPLICATIONS', "Directly deleted {$deleted_count} candidate applications (IDs: " . implode(',', array_slice($ids, 0, 15)) . (count($ids) > 15 ? '...' : '') . ")");
                }
            } elseif ($delete_scope === 'filtered') {
                $f_status = sanitize($_POST['filter_status'] ?? '');
                $f_search = sanitize($_POST['filter_search'] ?? '');
                $f_where  = "WHERE 1=1";
                $f_params = [];
                if ($f_status) { $f_where .= " AND status=:st"; $f_params[':st'] = $f_status; }
                if ($f_search) {
                    $f_where .= " AND (applicant_name LIKE :q OR email LIKE :q2 OR job_title LIKE :q3 OR current_location LIKE :q4)";
                    $f_params[':q'] = $f_params[':q2'] = $f_params[':q3'] = $f_params[':q4'] = '%' . $f_search . '%';
                }

                // Remove resume files
                $fStmt = $db->prepare("SELECT resume_filename FROM job_applications $f_where");
                $fStmt->execute($f_params);
                $rFiles = $fStmt->fetchAll(PDO::FETCH_COLUMN);
                foreach ($rFiles as $rf) {
                    if (!empty($rf)) {
                        $p = UPLOADS_PATH . '/resumes/' . basename($rf);
                        if (file_exists($p) && is_file($p)) @unlink($p);
                    }
                }

                $delStmt = $db->prepare("DELETE FROM job_applications $f_where");
                $delStmt->execute($f_params);
                $deleted_count = $delStmt->rowCount();

                if (function_exists('log_admin_activity')) {
                    log_admin_activity('FILTERED_DELETE_APPLICATIONS', "Directly deleted {$deleted_count} applications matching filter (Status: '{$f_status}', Search: '{$f_search}')");
                }
            } elseif ($delete_scope === 'all') {
                $confirm_text = strtoupper(trim($_POST['confirm_all_text'] ?? ''));
                if ($confirm_text !== 'DELETE') {
                    header('Location: applications.php?err=confirm_text');
                    exit;
                }

                $fStmt = $db->query("SELECT resume_filename FROM job_applications");
                if ($fStmt) {
                    $rFiles = $fStmt->fetchAll(PDO::FETCH_COLUMN);
                    foreach ($rFiles as $rf) {
                        if (!empty($rf)) {
                            $p = UPLOADS_PATH . '/resumes/' . basename($rf);
                            if (file_exists($p) && is_file($p)) @unlink($p);
                        }
                    }
                }

                $delStmt = $db->query("DELETE FROM job_applications");
                $deleted_count = $delStmt ? $delStmt->rowCount() : 0;

                if (function_exists('log_admin_activity')) {
                    log_admin_activity('PURGE_ALL_APPLICATIONS', "Directly purged all {$deleted_count} candidate applications from database");
                }
            }

            header("Location: applications.php?msg=bulk_deleted&count={$deleted_count}&scope={$delete_scope}");
            exit;
        }

        // Bulk / Batch Status Update Applications (Multi-candidate status change, batch rejection, shortlist, etc.)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_status_update') {
            if (!verify_csrf()) {
                header('Location: applications.php?err=csrf');
                exit;
            }

            $status_scope     = sanitize($_POST['status_scope'] ?? 'selected');
            $target_status    = sanitize($_POST['target_status'] ?? '');
            $allowed_statuses = ['new', 'reviewed', 'shortlisted', 'interview', 'offered', 'rejected', 'withdrawn'];

            if (!in_array($target_status, $allowed_statuses, true)) {
                header('Location: applications.php?err=invalid_status');
                exit;
            }

            $admin_note_append = trim($_POST['bulk_admin_notes'] ?? '');
            $send_emails       = !empty($_POST['send_bulk_emails']);
            $email_subject     = trim($_POST['bulk_email_subject'] ?? '');
            $email_message     = trim($_POST['bulk_email_message'] ?? '');

            $updated_count     = 0;
            $emails_sent_count = 0;

            // Helper closure to send batch notification emails
            $send_bulk_email = function(array $candidate) use ($target_status, $email_subject, $email_message): bool {
                if (empty($candidate['email'])) return false;
                $candidate_name = htmlspecialchars($candidate['applicant_name'] ?? 'Candidate');
                $job_title      = htmlspecialchars($candidate['job_title'] ?? 'Open Position');
                $status_label   = ucfirst($target_status);
                $app_id         = (int)($candidate['id'] ?? 0);

                $subj = $email_subject;
                if (empty($subj)) {
                    if ($target_status === 'rejected') {
                        $subj = "Update regarding your application for {$job_title} — " . SITE_NAME;
                    } elseif ($target_status === 'shortlisted') {
                        $subj = "Application Shortlisted: {$job_title} — " . SITE_NAME;
                    } elseif ($target_status === 'interview') {
                        $subj = "Interview Invitation: {$job_title} — " . SITE_NAME;
                    } else {
                        $subj = "Application Status Update: {$job_title} — " . SITE_NAME;
                    }
                }

                $msg = $email_message;
                if (empty($msg)) {
                    if ($target_status === 'rejected') {
                        $msg = "Thank you for your interest in " . SITE_NAME . " and for taking the time to apply for the {$job_title} position.\n\nAfter careful evaluation, we regret to inform you that we have decided to move forward with other candidates whose qualifications more closely align with our current role requirements.\n\nWe will keep your resume on file for future openings that match your skills. We wish you the very best in your professional endeavors.";
                    } elseif ($target_status === 'shortlisted') {
                        $msg = "We are pleased to inform you that your profile has been shortlisted for the {$job_title} position at " . SITE_NAME . ".\n\nOur recruitment team will contact you shortly regarding the next steps in our hiring process.";
                    } elseif ($target_status === 'interview') {
                        $msg = "We would like to invite you for an interview round for the {$job_title} position at " . SITE_NAME . ".\n\nOur recruitment team will contact you shortly with your scheduled interview slot and details.";
                    } else {
                        $msg = "Your application status for {$job_title} at " . SITE_NAME . " has been updated to: " . $status_label . ".";
                    }
                }

                $formatted_msg = nl2br(htmlspecialchars($msg));
                $status_colors = [
                    'new'         => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                    'reviewed'    => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
                    'shortlisted' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                    'interview'   => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                    'offered'     => ['bg' => '#d1fae5', 'text' => '#065f46'],
                    'rejected'    => ['bg' => '#f3f4f6', 'text' => '#4b5563'],
                    'withdrawn'   => ['bg' => '#f1f5f9', 'text' => '#475569'],
                ];
                $badge_style = $status_colors[$target_status] ?? ['bg' => '#e2e8f0', 'text' => '#1e293b'];

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
        This is an update regarding your application for the <strong>{$job_title}</strong> position at <strong>" . htmlspecialchars(SITE_NAME) . "</strong>" . ($app_id ? " (Application Ref <strong>#{$app_id}</strong>)" : "") . ".
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
                $careers_addr = function_exists('get_careers_email') ? get_careers_email() : EMAIL_CAREERS;
                return @send_notification_email($candidate['email'], $subj, $html_body, $from_name, $careers_addr, EMAIL_NO_REPLY, $careers_addr);
            };

            if ($status_scope === 'selected') {
                $raw_ids = $_POST['selected_ids'] ?? [];
                $ids = array_values(array_filter(array_map('intval', (array)$raw_ids), fn($i) => $i > 0));

                if (empty($ids)) {
                    header('Location: applications.php?err=no_selection');
                    exit;
                }

                $placeholders = implode(',', array_fill(0, count($ids), '?'));

                if ($send_emails) {
                    $cStmt = $db->prepare("SELECT id, applicant_name, email, job_title FROM job_applications WHERE id IN ($placeholders)");
                    $cStmt->execute($ids);
                    $candidates = $cStmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($candidates as $cand) {
                        if ($send_bulk_email($cand)) $emails_sent_count++;
                    }
                }

                if (!empty($admin_note_append)) {
                    $note_line = "\n[" . date('d M Y, h:i A') . "] " . $admin_note_append . " (Batch update: " . ucfirst($target_status) . ")";
                    $upStmt = $db->prepare("UPDATE job_applications SET status = ?, admin_notes = CONCAT(COALESCE(admin_notes, ''), ?) WHERE id IN ($placeholders)");
                    $upStmt->execute(array_merge([$target_status, $note_line], $ids));
                } else {
                    $upStmt = $db->prepare("UPDATE job_applications SET status = ? WHERE id IN ($placeholders)");
                    $upStmt->execute(array_merge([$target_status], $ids));
                }
                $updated_count = $upStmt->rowCount();

                if (function_exists('log_admin_activity')) {
                    log_admin_activity('BULK_STATUS_UPDATE', "Batch updated status to '{$target_status}' for {$updated_count} applications (IDs: " . implode(',', array_slice($ids, 0, 15)) . (count($ids) > 15 ? '...' : '') . ")");
                }

            } elseif ($status_scope === 'filtered') {
                $f_status = sanitize($_POST['filter_status'] ?? '');
                $f_search = sanitize($_POST['filter_search'] ?? '');
                $f_where  = "WHERE 1=1";
                $f_params = [];
                if ($f_status) { $f_where .= " AND status=:st"; $f_params[':st'] = $f_status; }
                if ($f_search) {
                    $f_where .= " AND (applicant_name LIKE :q OR email LIKE :q2 OR job_title LIKE :q3 OR current_location LIKE :q4)";
                    $f_params[':q'] = $f_params[':q2'] = $f_params[':q3'] = $f_params[':q4'] = '%' . $f_search . '%';
                }

                if ($send_emails) {
                    $cStmt = $db->prepare("SELECT id, applicant_name, email, job_title FROM job_applications $f_where");
                    $cStmt->execute($f_params);
                    $candidates = $cStmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($candidates as $cand) {
                        if ($send_bulk_email($cand)) $emails_sent_count++;
                    }
                }

                if (!empty($admin_note_append)) {
                    $note_line = "\n[" . date('d M Y, h:i A') . "] " . $admin_note_append . " (Batch update: " . ucfirst($target_status) . ")";
                    $f_params[':t_status']  = $target_status;
                    $f_params[':note_line'] = $note_line;
                    $upStmt = $db->prepare("UPDATE job_applications SET status = :t_status, admin_notes = CONCAT(COALESCE(admin_notes, ''), :note_line) $f_where");
                    $upStmt->execute($f_params);
                } else {
                    $f_params[':t_status'] = $target_status;
                    $upStmt = $db->prepare("UPDATE job_applications SET status = :t_status $f_where");
                    $upStmt->execute($f_params);
                }
                $updated_count = $upStmt->rowCount();

                if (function_exists('log_admin_activity')) {
                    log_admin_activity('FILTERED_STATUS_UPDATE', "Batch updated status to '{$target_status}' for {$updated_count} applications matching filter");
                }

            } elseif ($status_scope === 'all') {
                if ($send_emails) {
                    $cStmt = $db->query("SELECT id, applicant_name, email, job_title FROM job_applications");
                    $candidates = $cStmt ? $cStmt->fetchAll(PDO::FETCH_ASSOC) : [];
                    foreach ($candidates as $cand) {
                        if ($send_bulk_email($cand)) $emails_sent_count++;
                    }
                }

                if (!empty($admin_note_append)) {
                    $note_line = "\n[" . date('d M Y, h:i A') . "] " . $admin_note_append . " (Batch update: " . ucfirst($target_status) . ")";
                    $upStmt = $db->prepare("UPDATE job_applications SET status = :t_status, admin_notes = CONCAT(COALESCE(admin_notes, ''), :note_line)");
                    $upStmt->execute([':t_status' => $target_status, ':note_line' => $note_line]);
                } else {
                    $upStmt = $db->prepare("UPDATE job_applications SET status = :t_status");
                    $upStmt->execute([':t_status' => $target_status]);
                }
                $updated_count = $upStmt ? $upStmt->rowCount() : 0;

                if (function_exists('log_admin_activity')) {
                    log_admin_activity('ALL_STATUS_UPDATE', "Batch updated status to '{$target_status}' for all {$updated_count} applications");
                }
            }

            $mail_info = $send_emails ? "&emails_sent={$emails_sent_count}" : "";
            header("Location: applications.php?msg=bulk_status_updated&count={$updated_count}&status={$target_status}&scope={$status_scope}{$mail_info}");
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
                    $careers_addr = function_exists('get_careers_email') ? get_careers_email() : EMAIL_CAREERS;
                    $sent = send_notification_email($current_app['email'], $email_subject, $html_body, $from_name, $careers_addr, EMAIL_NO_REPLY, $careers_addr);

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
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $per_page = min(200, max(10, (int)($_GET['per_page'] ?? ITEMS_PER_PAGE)));

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
        $all_count   = (int)$db->query("SELECT COUNT(*) FROM job_applications")->fetchColumn();

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

        $pg   = paginate($total_count, $per_page, $page);
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
.form-check-input{cursor:pointer;border-color:#cbd5e1}
.form-check-input:checked{background-color:#CC2228;border-color:#CC2228}
tr.selected-row{background-color:#eef2ff !important}
tr.selected-row td{background-color:#eef2ff !important}
.bulk-action-bar{background:#080B1A;border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:14px 22px;color:#fff;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;box-shadow:0 10px 25px rgba(8,11,26,.18);transition:.3s}
.btn-purge{background:rgba(204,34,40,.12);color:#CC2228;border:1px solid rgba(204,34,40,.3);border-radius:10px;font-weight:700;font-size:13px;padding:10px 16px;transition:.2s}
.status-choice-card{border:2px solid #e2e8f0;background:#fff;transition:.2s;user-select:none;}
.status-choice-card:hover{border-color:#93c5fd;background:#f8faff;}
.status-choice-card.active{border-color:#1C2280;background:#f0f4ff;box-shadow:0 0 0 3px rgba(28,34,128,.15);}
.status-choice-card[data-status="rejected"].active{border-color:#ef4444;background:#fef2f2;box-shadow:0 0 0 3px rgba(239,68,68,.15);}
.status-choice-card[data-status="shortlisted"].active{border-color:#f59e0b;background:#fffbeb;box-shadow:0 0 0 3px rgba(245,158,11,.15);}
.status-choice-card[data-status="interview"].active{border-color:#6366f1;background:#eef2ff;box-shadow:0 0 0 3px rgba(99,102,241,.15);}
.status-choice-card[data-status="reviewed"].active{border-color:#10b981;background:#ecfdf5;box-shadow:0 0 0 3px rgba(16,185,129,.15);}
.status-choice-card[data-status="offered"].active{border-color:#0284c7;background:#f0f9ff;box-shadow:0 0 0 3px rgba(2,132,199,.15);}
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
    <a href="blog/generate.php" class="sidebar-link"><span class="icon"><i class="fas fa-robot"></i></span> AI Blog Generator</a>
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
    <div class="d-flex gap-2 align-items-center flex-wrap">
      <!-- Batch Status Update Options -->
      <div class="dropdown">
        <button class="btn dropdown-toggle" type="button" id="headerStatusDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background:#1C2280;color:#fff;border-radius:10px;font-weight:700;font-size:13px;padding:10px 18px;border:none;box-shadow:0 2px 8px rgba(28,34,128,.25);">
          <i class="fas fa-tasks me-1"></i> Update Status
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="border-radius:12px;font-size:13px;min-width:300px;padding:8px;border:1px solid #e2e8f0;">
          <li><div class="dropdown-header text-uppercase" style="font-size:10px;font-weight:700;letter-spacing:.5px;color:#1C2280;"><i class="fas fa-sliders-h me-1"></i> Batch Status Update</div></li>
          <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" style="border-radius:8px;font-weight:600;" onclick="triggerStatusScope('selected')">
              <i class="fas fa-check-square text-primary" style="font-size:15px;"></i>
              <div>
                <div>Update Selected Profiles</div>
                <small class="text-muted" style="font-size:11px;">Update checked candidates (<span class="selectedCountText">0</span>)</small>
              </div>
            </button>
          </li>
          <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" style="border-radius:8px;font-weight:600;" onclick="triggerStatusScopeWithPreselect('selected', 'rejected')">
              <i class="fas fa-times-circle text-danger" style="font-size:15px;"></i>
              <div>
                <div>Quick Reject Selected (<span class="selectedCountText">0</span>)</div>
                <small class="text-muted" style="font-size:11px;">Batch reject selected with notification</small>
              </div>
            </button>
          </li>
          <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" style="border-radius:8px;font-weight:600;color:#b45309;" onclick="triggerStatusScopeWithPreselect('selected', 'shortlisted')">
              <i class="fas fa-star text-warning" style="font-size:15px;"></i>
              <div>
                <div>Quick Shortlist Selected (<span class="selectedCountText">0</span>)</div>
                <small class="text-muted" style="font-size:11px;">Batch shortlist selected candidates</small>
              </div>
            </button>
          </li>
          <li><hr class="dropdown-divider my-2"></li>
          <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" style="border-radius:8px;font-weight:600;" onclick="triggerStatusScope('filtered')">
              <i class="fas fa-filter text-info" style="font-size:15px;"></i>
              <div>
                <div>Update All Filtered Profiles</div>
                <small class="text-muted" style="font-size:11px;">Update all <?= $total_count ?? 0 ?> applications matching filter</small>
              </div>
            </button>
          </li>
          <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" style="border-radius:8px;font-weight:600;" onclick="triggerStatusScope('all')">
              <i class="fas fa-database text-secondary" style="font-size:15px;"></i>
              <div>
                <div>Update All in Entire Database</div>
                <small class="text-muted" style="font-size:11px;">Batch update all <?= $all_count ?? 0 ?> profiles</small>
              </div>
            </button>
          </li>
        </ul>
      </div>

      <!-- Direct Multi-Profile Deletion Options (No Trash) -->
      <div class="dropdown">
        <button class="btn dropdown-toggle" type="button" id="headerDeleteDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background:#CC2228;color:#fff;border-radius:10px;font-weight:700;font-size:13px;padding:10px 18px;border:none;box-shadow:0 2px 8px rgba(204,34,40,.25);">
          <i class="fas fa-trash-alt me-1"></i> Delete Options
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="border-radius:12px;font-size:13px;min-width:280px;padding:8px;border:1px solid #fee2e2;">
          <li><div class="dropdown-header text-uppercase" style="font-size:10px;font-weight:700;letter-spacing:.5px;color:#991b1b;"><i class="fas fa-bolt me-1"></i> Direct Permanent Delete (No Trash)</div></li>
          <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" style="border-radius:8px;font-weight:600;" onclick="triggerDeleteScope('selected')">
              <i class="fas fa-check-square text-danger" style="font-size:15px;"></i>
              <div>
                <div>Delete Selected Profiles</div>
                <small class="text-muted" style="font-size:11px;">Delete profiles checked with checkboxes (<span class="selectedCountText">0</span>)</small>
              </div>
            </button>
          </li>
          <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" style="border-radius:8px;font-weight:600;" onclick="triggerDeleteScope('filtered')">
              <i class="fas fa-filter text-danger" style="font-size:15px;"></i>
              <div>
                <div>Delete All Filtered Profiles</div>
                <small class="text-muted" style="font-size:11px;">Delete all <?= $total_count ?? 0 ?> applications matching current filter</small>
              </div>
            </button>
          </li>
          <li><hr class="dropdown-divider my-2"></li>
          <li>
            <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" style="border-radius:8px;font-weight:700;" onclick="triggerDeleteScope('all')">
              <i class="fas fa-radiation-alt" style="font-size:15px;"></i>
              <div>
                <div>Purge Entire Applications Database</div>
                <small class="text-danger" style="font-size:11px;">Directly delete all <?= $all_count ?? 0 ?> profiles &amp; resumes</small>
              </div>
            </button>
          </li>
        </ul>
      </div>

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
  <div class="alert alert-success mb-4" style="border-radius:12px;"><i class="fas fa-check-circle me-2"></i> Application deleted permanently (no trash).</div>
  <?php endif; ?>

  <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'bulk_deleted'): ?>
  <div class="alert alert-success mb-4 shadow-sm" style="border-radius:12px;border:1px solid #bbf7d0;background:#f0fdf4;color:#166534;">
    <i class="fas fa-check-circle me-2 text-success"></i> <strong>Permanent Deletion Successful:</strong> <?= (int)($_GET['count'] ?? 0) ?> application profile(s) and their attached resume files were deleted directly from the database (No trash).
  </div>
  <?php endif; ?>

  <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'bulk_status_updated'): ?>
  <div class="alert alert-success mb-4 shadow-sm" style="border-radius:12px;border:1px solid #bbf7d0;background:#f0fdf4;color:#166534;">
    <i class="fas fa-check-circle me-2 text-success"></i> <strong>Bulk Status Update Successful:</strong> 
    Updated <strong><?= (int)($_GET['count'] ?? 0) ?></strong> application profile(s) to 
    <span class="status-badge status-<?= htmlspecialchars($_GET['new_status'] ?? '') ?>"><?= ucfirst(htmlspecialchars($_GET['new_status'] ?? '')) ?></span>
    <?php if (isset($_GET['emails_sent'])): ?>
      &bull; <strong><?= (int)$_GET['emails_sent'] ?></strong> candidate notification email(s) dispatched.
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($_GET['err'])): ?>
    <?php if ($_GET['err'] === 'no_selection'): ?>
    <div class="alert alert-warning mb-4 shadow-sm" style="border-radius:12px;border:1px solid #fed7aa;background:#fffbeb;color:#9a3412;">
      <i class="fas fa-exclamation-triangle me-2 text-warning"></i> <strong>No profiles selected:</strong> Please check at least one application profile to proceed.
    </div>
    <?php elseif ($_GET['err'] === 'invalid_status'): ?>
    <div class="alert alert-danger mb-4 shadow-sm" style="border-radius:12px;border:1px solid #fecaca;background:#fef2f2;color:#991b1b;">
      <i class="fas fa-exclamation-triangle me-2 text-danger"></i> <strong>Invalid Status:</strong> The status chosen for the bulk update is not recognized.
    </div>
    <?php elseif ($_GET['err'] === 'confirm_text'): ?>
    <div class="alert alert-danger mb-4 shadow-sm" style="border-radius:12px;border:1px solid #fecaca;background:#fef2f2;color:#991b1b;">
      <i class="fas fa-exclamation-triangle me-2 text-danger"></i> <strong>Confirmation mismatch:</strong> You must type <code>DELETE</code> into the confirmation box to purge all applications.
    </div>
    <?php elseif ($_GET['err'] === 'csrf'): ?>
    <div class="alert alert-danger mb-4 shadow-sm" style="border-radius:12px;border:1px solid #fecaca;background:#fef2f2;color:#991b1b;">
      <i class="fas fa-shield-alt me-2 text-danger"></i> <strong>Security Token Expired:</strong> Invalid CSRF validation. Please refresh and try again.
    </div>
    <?php endif; ?>
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
  <!-- Floating / Sticky Selection Action Bar -->
  <!-- Floating / Sticky Selection Action Bar -->
  <div id="bulkActionBar" class="bulk-action-bar" style="display:none;">
    <div class="d-flex align-items-center gap-3">
      <span class="badge rounded-pill px-3 py-2" style="font-size:13px;font-weight:700;background:#1C2280;color:#fff;">
        <span id="bulkSelectedCount">0</span> Selected
      </span>
      <span style="font-size:13.5px;font-weight:500;">Batch actions for selected candidates:</span>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <button type="button" class="btn btn-sm btn-outline-light" style="border-radius:8px;font-weight:600;" id="selectAllPageBtn">
        <i class="fas fa-check-double me-1"></i> Select All on Page
      </button>
      <button type="button" class="btn btn-sm btn-outline-light" style="border-radius:8px;font-weight:600;" id="clearSelectionBtn">
        <i class="fas fa-times me-1"></i> Deselect
      </button>

      <!-- Quick Reject button -->
      <button type="button" class="btn btn-sm" style="border-radius:8px;font-weight:700;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;padding:6px 14px;" onclick="triggerStatusScopeWithPreselect('selected', 'rejected')">
        <i class="fas fa-times-circle me-1"></i> Reject Selected (<span class="selectedCountText">0</span>)
      </button>

      <!-- Quick Shortlist button -->
      <button type="button" class="btn btn-sm" style="border-radius:8px;font-weight:700;background:#fef3c7;color:#92400e;border:1px solid #fcd34d;padding:6px 14px;" onclick="triggerStatusScopeWithPreselect('selected', 'shortlisted')">
        <i class="fas fa-star me-1"></i> Shortlist Selected (<span class="selectedCountText">0</span>)
      </button>

      <!-- Full Status Update Modal Button -->
      <button type="button" class="btn btn-sm btn-primary" style="border-radius:8px;font-weight:700;background:#2563eb;border-color:#2563eb;padding:6px 16px;" onclick="triggerStatusScope('selected')">
        <i class="fas fa-sliders-h me-1"></i> Update Status...
      </button>

      <!-- Delete Selected Directly -->
      <button type="button" class="btn btn-sm btn-danger ms-lg-2" style="border-radius:8px;font-weight:700;background:#CC2228;border-color:#CC2228;padding:6px 16px;" onclick="triggerDeleteScope('selected')">
        <i class="fas fa-trash-alt me-1"></i> Delete Selected (<span class="selectedCountText">0</span>)
      </button>
    </div>
  </div>

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
        <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()" style="border-radius:8px;width:auto;" title="Candidates per page">
          <option value="15" <?= ($per_page ?? 15)==15?'selected':'' ?>>15 / page</option>
          <option value="25" <?= ($per_page ?? 15)==25?'selected':'' ?>>25 / page</option>
          <option value="50" <?= ($per_page ?? 15)==50?'selected':'' ?>>50 / page</option>
          <option value="100" <?= ($per_page ?? 15)==100?'selected':'' ?>>100 / page</option>
        </select>
        <button type="submit" class="btn btn-sm btn-primary" style="border-radius:8px;background:#1C2280;border-color:#1C2280;font-weight:600;"><i class="fas fa-filter me-1"></i> Filter</button>
        <?php if ($filter || $search || ($per_page ?? 15) != 15): ?>
        <a href="applications.php" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;" title="Clear Filters"><i class="fas fa-times"></i></a>
        <?php endif; ?>
      </form>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th style="width:40px;text-align:center;">
              <input type="checkbox" id="masterCheckbox" class="form-check-input" title="Select / Deselect all on current page">
            </th>
            <th>#</th>
            <th>Applicant</th>
            <th>Position</th>
            <th>Experience</th>
            <th>Resume</th>
            <th>Applied</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if(empty($apps)): ?>
          <tr><td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;"><i class="fas fa-briefcase" style="font-size:32px;display:block;margin-bottom:12px;opacity:.3;"></i> No applications found.</td></tr>
          <?php else: ?>
          <?php foreach($apps as $a): ?>
          <tr data-id="<?= (int)$a['id'] ?>" class="app-row">
            <td style="text-align:center;">
              <input type="checkbox" class="form-check-input app-checkbox" value="<?= (int)$a['id'] ?>" data-name="<?= htmlspecialchars($a['applicant_name']) ?>">
            </td>
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
              <a href="applications.php?delete=<?= $a['id'] ?>" class="action-btn action-btn-delete" onclick="return confirm('Directly delete this application permanently? Resume file will be removed.');"><i class="fas fa-trash"></i></a>
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

  <!-- Permanent Direct Delete Confirmation Modal -->
  <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,.2);">
        <div class="modal-header" style="background:#fee2e2;border-bottom:1px solid #fecaca;padding:18px 24px;">
          <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2" id="bulkDeleteModalLabel">
            <i class="fas fa-exclamation-triangle"></i> <span id="modalTitleText">Direct Permanent Deletion</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="applications.php" id="bulkDeleteForm">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <input type="hidden" name="action" value="bulk_delete">
          <input type="hidden" name="delete_scope" id="modalDeleteScope" value="selected">
          <input type="hidden" name="filter_status" value="<?= htmlspecialchars($filter ?? '') ?>">
          <input type="hidden" name="filter_search" value="<?= htmlspecialchars($search ?? '') ?>">
          <div id="modalSelectedIdsContainer"></div>

          <div class="modal-body p-4">
            <div class="alert alert-danger d-flex align-items-start gap-2 mb-3" style="border-radius:10px;font-size:13px;">
              <i class="fas fa-info-circle mt-1" style="font-size:16px;"></i>
              <div>
                <strong>No Trash Bin:</strong> Applications and attached resume documents are deleted directly and permanently from database and server disk. This cannot be undone.
              </div>
            </div>

            <div id="modalWarningMessage" style="font-size:14px;color:#334155;line-height:1.6;margin-bottom:16px;">
              <!-- Dynamic prompt inserted here -->
            </div>

            <!-- Purge All Confirmation Input -->
            <div id="modalPurgeConfirmBlock" style="display:none;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:14px;margin-top:10px;">
              <label for="confirmAllTextInput" style="font-size:12px;font-weight:700;color:#991b1b;display:block;margin-bottom:6px;">
                TYPE <span class="badge bg-danger">DELETE</span> TO CONFIRM PURGING ALL:
              </label>
              <input type="text" name="confirm_all_text" id="confirmAllTextInput" class="form-control" placeholder="Type DELETE here" autocomplete="off" style="font-weight:700;letter-spacing:1px;border-color:#f87171;">
              <small class="text-danger mt-1 d-block" style="font-size:11.5px;">All applications across the entire portal will be removed permanently.</small>
            </div>
          </div>
          <div class="modal-footer" style="background:#f8fafc;border-top:1px solid #f1f5f9;padding:14px 24px;">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px;font-weight:600;font-size:13px;">Cancel</button>
            <button type="submit" id="modalSubmitDeleteBtn" class="btn btn-danger" style="border-radius:8px;font-weight:700;font-size:13px;background:#CC2228;border-color:#CC2228;padding:8px 20px;">
              <i class="fas fa-trash-alt me-1"></i> <span id="modalSubmitBtnText">Permanently Delete</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bulk Status Update Modal -->
  <div class="modal fade" id="bulkStatusModal" tabindex="-1" aria-labelledby="bulkStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,.2);">
        <div class="modal-header" style="background:#f0f4ff;border-bottom:1px solid #e0e7ff;padding:18px 24px;">
          <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="bulkStatusModalLabel" style="color:#1C2280;">
            <i class="fas fa-tasks"></i> <span id="statusModalTitleText">Bulk Update Application Status</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="applications.php" id="bulkStatusForm">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <input type="hidden" name="action" value="bulk_status_update">
          <input type="hidden" name="status_scope" id="modalStatusScope" value="selected">
          <input type="hidden" name="filter_status" value="<?= htmlspecialchars($filter ?? '') ?>">
          <input type="hidden" name="filter_search" value="<?= htmlspecialchars($search ?? '') ?>">
          <div id="modalStatusSelectedIdsContainer"></div>

          <div class="modal-body p-4">
            <div id="statusModalScopeDesc" class="alert alert-info d-flex align-items-start gap-2 mb-3" style="border-radius:10px;font-size:13px;background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;">
              <i class="fas fa-info-circle mt-1" style="font-size:16px;"></i>
              <div id="statusModalScopeText">
                Updating status for selected candidates.
              </div>
            </div>

            <!-- Choose New Status -->
            <label class="form-label fw-bold text-uppercase" style="font-size:12px;letter-spacing:.5px;color:#475569;">
              Choose New Status <span class="text-danger">*</span>
            </label>
            <div class="row g-2 mb-3" id="statusChoiceContainer">
              <div class="col-sm-6 col-md-4">
                <div class="status-choice-card d-block p-3 rounded-3" style="cursor:pointer;" data-status="rejected">
                  <input type="radio" name="new_status" value="rejected" class="d-none">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="badge bg-danger">Rejected</span>
                    <i class="fas fa-times-circle text-danger"></i>
                  </div>
                  <div style="font-size:12px;color:#64748b;">Decline profile politely</div>
                </div>
              </div>
              <div class="col-sm-6 col-md-4">
                <div class="status-choice-card d-block p-3 rounded-3" style="cursor:pointer;" data-status="shortlisted">
                  <input type="radio" name="new_status" value="shortlisted" class="d-none">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="badge" style="background:#f59e0b;color:#fff;">Shortlisted</span>
                    <i class="fas fa-star text-warning"></i>
                  </div>
                  <div style="font-size:12px;color:#64748b;">Qualified for next round</div>
                </div>
              </div>
              <div class="col-sm-6 col-md-4">
                <div class="status-choice-card d-block p-3 rounded-3" style="cursor:pointer;" data-status="interview">
                  <input type="radio" name="new_status" value="interview" class="d-none">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="badge" style="background:#6366f1;color:#fff;">Interview</span>
                    <i class="fas fa-calendar-check text-primary"></i>
                  </div>
                  <div style="font-size:12px;color:#64748b;">Schedule interview round</div>
                </div>
              </div>
              <div class="col-sm-6 col-md-4">
                <div class="status-choice-card d-block p-3 rounded-3" style="cursor:pointer;" data-status="reviewed">
                  <input type="radio" name="new_status" value="reviewed" class="d-none">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="badge bg-success">Reviewed</span>
                    <i class="fas fa-check text-success"></i>
                  </div>
                  <div style="font-size:12px;color:#64748b;">Screened &amp; under eval</div>
                </div>
              </div>
              <div class="col-sm-6 col-md-4">
                <div class="status-choice-card d-block p-3 rounded-3" style="cursor:pointer;" data-status="offered">
                  <input type="radio" name="new_status" value="offered" class="d-none">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="badge bg-info text-white">Offered</span>
                    <i class="fas fa-award text-info"></i>
                  </div>
                  <div style="font-size:12px;color:#64748b;">Formal job offer extended</div>
                </div>
              </div>
              <div class="col-sm-6 col-md-4">
                <div class="status-choice-card d-block p-3 rounded-3" style="cursor:pointer;" data-status="new">
                  <input type="radio" name="new_status" value="new" class="d-none">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="badge bg-secondary">New</span>
                    <i class="fas fa-undo text-secondary"></i>
                  </div>
                  <div style="font-size:12px;color:#64748b;">Reset to unreviewed</div>
                </div>
              </div>
            </div>

            <!-- Internal Admin Note (Optional) -->
            <div class="mb-3">
              <label for="bulkAdminNote" class="form-label fw-bold text-uppercase" style="font-size:12px;letter-spacing:.5px;color:#475569;">
                Internal HR Note <small class="text-muted text-lowercase font-monospace">(optional — appended with timestamp)</small>
              </label>
              <input type="text" name="admin_notes" id="bulkAdminNote" class="form-control" style="border-radius:10px;font-size:13.5px;" placeholder="e.g. Batch rejected after initial review / Shortlisted for round 1 screening">
            </div>

            <!-- Email Notification Options -->
            <div class="card p-3 mb-2" style="background:#f8faff;border:1px solid #dbeafe;border-radius:12px;">
              <div class="form-check form-switch d-flex align-items-center gap-2">
                <input class="form-check-input" type="checkbox" role="switch" id="bulkSendEmailToggle" name="send_email" value="1" style="width:2.5em;height:1.3em;cursor:pointer;">
                <label class="form-check-label fw-bold" for="bulkSendEmailToggle" style="color:#1e293b;cursor:pointer;font-size:13.5px;">
                  <i class="fas fa-envelope-open-text me-1" style="color:#1C2280;"></i> Notify candidate(s) via branded email
                </label>
              </div>
              <div id="bulkEmailComposerSection" style="margin-top:12px;display:none;">
                <div class="mb-2">
                  <label class="form-label fw-bold text-uppercase" style="font-size:11px;color:#64748b;">Email Subject</label>
                  <input type="text" name="email_subject" id="bulkEmailSubject" class="form-control" style="border-radius:8px;font-size:13px;" value="Update regarding your application at <?= htmlspecialchars(SITE_NAME) ?>">
                </div>
                <div class="mb-1">
                  <label class="form-label fw-bold text-uppercase" style="font-size:11px;color:#64748b;">Email Message Body</label>
                  <textarea name="email_message" id="bulkEmailMessage" rows="5" class="form-control" style="border-radius:8px;font-size:13px;line-height:1.6;" placeholder="Custom email body..."></textarea>
                  <small class="text-muted d-block mt-1" style="font-size:11px;">
                    <i class="fas fa-info-circle me-1"></i> Placeholders <code>[Candidate Name]</code> and <code>[Position]</code> are automatically replaced with each candidate's real details.
                  </small>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="background:#f8fafc;border-top:1px solid #f1f5f9;padding:14px 24px;">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px;font-weight:600;font-size:13px;">Cancel</button>
            <button type="submit" id="bulkStatusSubmitBtn" class="btn" style="border-radius:8px;font-weight:700;font-size:13px;background:#1C2280;color:#fff;padding:8px 22px;">
              <i class="fas fa-check me-1"></i> <span id="statusModalSubmitBtnText">Apply Status Update</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
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

// Bulk Selection & Flexible Deletion Management
(function() {
  const masterCheckbox   = document.getElementById('masterCheckbox');
  const appCheckboxes    = document.querySelectorAll('.app-checkbox');
  const bulkActionBar     = document.getElementById('bulkActionBar');
  const bulkSelectedCount = document.getElementById('bulkSelectedCount');
  const selectedCountTexts= document.querySelectorAll('.selectedCountText');
  const selectAllPageBtn  = document.getElementById('selectAllPageBtn');
  const clearSelectionBtn = document.getElementById('clearSelectionBtn');
  const bulkDeleteModalEl = document.getElementById('bulkDeleteModal');
  const bulkDeleteForm    = document.getElementById('bulkDeleteForm');
  const modalScopeInput   = document.getElementById('modalDeleteScope');
  const modalIdsContainer = document.getElementById('modalSelectedIdsContainer');
  const modalTitleText    = document.getElementById('modalTitleText');
  const modalWarningMsg   = document.getElementById('modalWarningMessage');
  const modalPurgeBlock   = document.getElementById('modalPurgeConfirmBlock');
  const confirmAllInput   = document.getElementById('confirmAllTextInput');
  const modalSubmitBtnTxt = document.getElementById('modalSubmitBtnText');

  const filteredCount = <?= (int)($total_count ?? 0) ?>;
  const allCount      = <?= (int)($all_count ?? 0) ?>;
  const filterStatus  = <?= json_encode($filter ?? '') ?>;
  const searchKeyword = <?= json_encode($search ?? '') ?>;

  function updateSelectionState() {
    const checked = Array.from(appCheckboxes).filter(cb => cb.checked);
    const count = checked.length;

    // Update count labels
    if (bulkSelectedCount) bulkSelectedCount.textContent = count;
    selectedCountTexts.forEach(el => el.textContent = count);

    // Show/hide floating bulk bar
    if (bulkActionBar) {
      bulkActionBar.style.display = count > 0 ? 'flex' : 'none';
    }

    // Highlight row
    appCheckboxes.forEach(cb => {
      const row = cb.closest('tr');
      if (row) {
        if (cb.checked) {
          row.classList.add('selected-row');
        } else {
          row.classList.remove('selected-row');
        }
      }
    });

    // Update master checkbox indeterminate state
    if (masterCheckbox && appCheckboxes.length > 0) {
      if (count === 0) {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = false;
      } else if (count === appCheckboxes.length) {
        masterCheckbox.checked = true;
        masterCheckbox.indeterminate = false;
      } else {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = true;
      }
    }
  }

  if (masterCheckbox) {
    masterCheckbox.addEventListener('change', function() {
      const isChecked = this.checked;
      appCheckboxes.forEach(cb => {
        cb.checked = isChecked;
      });
      updateSelectionState();
    });
  }

  appCheckboxes.forEach(cb => {
    cb.addEventListener('change', updateSelectionState);
  });

  if (selectAllPageBtn) {
    selectAllPageBtn.addEventListener('click', function() {
      appCheckboxes.forEach(cb => { cb.checked = true; });
      updateSelectionState();
    });
  }

  if (clearSelectionBtn) {
    clearSelectionBtn.addEventListener('click', function() {
      appCheckboxes.forEach(cb => { cb.checked = false; });
      updateSelectionState();
    });
  }

  // Trigger modal with appropriate scope ('selected' | 'filtered' | 'all')
  window.triggerDeleteScope = function(scope) {
    if (!bulkDeleteModalEl) return;
    const modal = bootstrap.Modal.getOrCreateInstance(bulkDeleteModalEl);
    modalScopeInput.value = scope;
    modalIdsContainer.innerHTML = '';
    if (confirmAllInput) confirmAllInput.value = '';

    if (scope === 'selected') {
      const checked = Array.from(appCheckboxes).filter(cb => cb.checked);
      if (checked.length === 0) {
        alert('Please select at least one application profile using the checkboxes to delete.');
        return;
      }
      checked.forEach(cb => {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'selected_ids[]';
        hidden.value = cb.value;
        modalIdsContainer.appendChild(hidden);
      });

      modalTitleText.textContent = `Delete ${checked.length} Selected Profile(s)`;
      modalWarningMsg.innerHTML = `You are about to permanently delete <strong>${checked.length}</strong> selected candidate profile(s) and their attached resume files.<br><br>This is a <strong>direct deletion</strong> (no trash/recycle bin). Are you sure you want to proceed?`;
      modalPurgeBlock.style.display = 'none';
      if (confirmAllInput) confirmAllInput.removeAttribute('required');
      modalSubmitBtnTxt.textContent = `Delete ${checked.length} Profile(s) Directly`;
      modal.show();

    } else if (scope === 'filtered') {
      if (filteredCount === 0) {
        alert('There are no applications matching the current filter to delete.');
        return;
      }
      modalTitleText.textContent = `Delete All ${filteredCount} Filtered Applications`;
      let filterDesc = [];
      if (filterStatus) filterDesc.push(`Status: <strong>${filterStatus}</strong>`);
      if (searchKeyword) filterDesc.push(`Search: <strong>${searchKeyword}</strong>`);
      let filterDetails = filterDesc.length > 0 ? ` matching (${filterDesc.join(', ')})` : '';

      modalWarningMsg.innerHTML = `You are about to permanently delete all <strong>${filteredCount}</strong> applications${filterDetails} along with their stored resumes.<br><br>They will be <strong>permanently deleted directly</strong> with no trash retention.`;
      modalPurgeBlock.style.display = 'none';
      if (confirmAllInput) confirmAllInput.removeAttribute('required');
      modalSubmitBtnTxt.textContent = `Delete All ${filteredCount} Filtered Directly`;
      modal.show();

    } else if (scope === 'all') {
      if (allCount === 0) {
        alert('There are no applications in the database to delete.');
        return;
      }
      modalTitleText.textContent = `⚠️ PURGE ALL ${allCount} APPLICATIONS`;
      modalWarningMsg.innerHTML = `<strong>DANGER:</strong> You are about to permanently erase <strong>ALL ${allCount}</strong> job applications and all attached resume documents from the entire system.<br><br>No trash or recovery option is available. To prevent accidental deletion, please type <strong>DELETE</strong> below:`;
      modalPurgeBlock.style.display = 'block';
      if (confirmAllInput) {
        confirmAllInput.setAttribute('required', 'required');
        setTimeout(() => confirmAllInput.focus(), 300);
      }
      modalSubmitBtnTxt.textContent = `PURGE ALL ${allCount} APPLICATIONS`;
      modal.show();
    }
  };

  if (bulkDeleteForm) {
    bulkDeleteForm.addEventListener('submit', function(e) {
      const scope = modalScopeInput.value;
      if (scope === 'all') {
        const val = (confirmAllInput.value || '').trim().toUpperCase();
        if (val !== 'DELETE') {
          e.preventDefault();
          alert('You must type DELETE exactly into the confirmation box to purge all applications.');
          confirmAllInput.focus();
          return false;
        }
      }
    });
  }

  // --- Bulk Status Update Modal & Logic ---
  const bulkStatusModalEl   = document.getElementById('bulkStatusModal');
  const bulkStatusForm      = document.getElementById('bulkStatusForm');
  const modalStatusScopeInput= document.getElementById('modalStatusScope');
  const modalStatusIdsCont  = document.getElementById('modalStatusSelectedIdsContainer');
  const statusModalTitleTxt = document.getElementById('statusModalTitleText');
  const statusModalScopeTxt = document.getElementById('statusModalScopeText');
  const statusSubmitBtnTxt  = document.getElementById('statusModalSubmitBtnText');
  const bulkSendEmailToggle = document.getElementById('bulkSendEmailToggle');
  const bulkEmailComposerSec= document.getElementById('bulkEmailComposerSection');
  const bulkEmailSubject    = document.getElementById('bulkEmailSubject');
  const bulkEmailMessage    = document.getElementById('bulkEmailMessage');
  const statusCards         = document.querySelectorAll('.status-choice-card');

  const defaultTemplates = {
    rejected: {
      subject: `Update regarding your application — <?= addslashes(SITE_NAME) ?>`,
      message: `Dear [Candidate Name],\n\nThank you for your interest in <?= addslashes(SITE_NAME) ?> and for taking the time to apply for the [Position] role.\n\nAfter careful consideration of all applications, we regret to inform you that we have decided to move forward with other candidates whose profiles more closely match our current requirements.\n\nWe will keep your resume in our talent network and will gladly contact you should a suitable opportunity arise in the future.\n\nWe wish you the very best in your career pursuits.\n\nSincerely,\n<?= addslashes(SITE_NAME) ?> Talent Acquisition`
    },
    shortlisted: {
      subject: `Application Shortlisted: [Position] — <?= addslashes(SITE_NAME) ?>`,
      message: `Dear [Candidate Name],\n\nWe are pleased to inform you that your profile has been shortlisted for the [Position] role at <?= addslashes(SITE_NAME) ?>.\n\nOur recruitment team reviewed your qualifications and experience, and we would like to proceed with the next round of our hiring process. Our team will contact you shortly to coordinate your schedule.\n\nPlease keep your phone and email accessible.\n\nBest regards,\n<?= addslashes(SITE_NAME) ?> Recruitment Team`
    },
    interview: {
      subject: `Interview Invitation: [Position] — <?= addslashes(SITE_NAME) ?>`,
      message: `Dear [Candidate Name],\n\nWe would like to invite you for an interview for the [Position] role at <?= addslashes(SITE_NAME) ?>.\n\nOur team will share the schedule and meeting details shortly. Please reply to this email if you have specific availability constraints.\n\nBest regards,\n<?= addslashes(SITE_NAME) ?> Recruitment Team`
    },
    reviewed: {
      subject: `Application Under Review: [Position] — <?= addslashes(SITE_NAME) ?>`,
      message: `Dear [Candidate Name],\n\nThank you for applying for the [Position] position at <?= addslashes(SITE_NAME) ?>.\n\nWe wanted to let you know that your application is actively under review by our hiring team. We appreciate your patience while we evaluate candidates.\n\nBest regards,\n<?= addslashes(SITE_NAME) ?> Recruitment Team`
    },
    offered: {
      subject: `Job Offer: [Position] — <?= addslashes(SITE_NAME) ?>`,
      message: `Dear [Candidate Name],\n\nCongratulations! We are delighted to extend a job offer for the position of [Position] at <?= addslashes(SITE_NAME) ?>.\n\nOur HR team will reach out with details regarding the formal offer letter and compensation package.\n\nWarm regards,\n<?= addslashes(SITE_NAME) ?> HR Team`
    },
    new: {
      subject: `Application Received: [Position] — <?= addslashes(SITE_NAME) ?>`,
      message: `Dear [Candidate Name],\n\nThank you for your application for the [Position] position at <?= addslashes(SITE_NAME) ?>. We will review your profile shortly.\n\nBest regards,\n<?= addslashes(SITE_NAME) ?> Team`
    }
  };

  function selectStatusCard(status) {
    statusCards.forEach(card => {
      const radio = card.querySelector('input[type=radio]');
      if (card.getAttribute('data-status') === status) {
        card.classList.add('active');
        if (radio) radio.checked = true;
      } else {
        card.classList.remove('active');
        if (radio) radio.checked = false;
      }
    });
    if (defaultTemplates[status]) {
      if (bulkEmailSubject) bulkEmailSubject.value = defaultTemplates[status].subject;
      if (bulkEmailMessage) bulkEmailMessage.value = defaultTemplates[status].message;
    }
  }

  statusCards.forEach(card => {
    card.addEventListener('click', function() {
      const status = this.getAttribute('data-status');
      selectStatusCard(status);
    });
  });

  if (bulkSendEmailToggle && bulkEmailComposerSec) {
    bulkSendEmailToggle.addEventListener('change', function() {
      bulkEmailComposerSec.style.display = this.checked ? 'block' : 'none';
    });
  }

  window.triggerStatusScopeWithPreselect = function(scope, status) {
    window.triggerStatusScope(scope, status);
  };

  window.triggerStatusScope = function(scope, preselectedStatus = null) {
    if (!bulkStatusModalEl) return;
    const modal = bootstrap.Modal.getOrCreateInstance(bulkStatusModalEl);
    modalStatusScopeInput.value = scope;
    modalStatusIdsCont.innerHTML = '';

    if (scope === 'selected') {
      const checked = Array.from(appCheckboxes).filter(cb => cb.checked);
      if (checked.length === 0) {
        alert('Please select at least one application profile using the checkboxes to update status.');
        return;
      }
      checked.forEach(cb => {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'selected_ids[]';
        hidden.value = cb.value;
        modalStatusIdsCont.appendChild(hidden);
      });

      statusModalTitleTxt.textContent = `Update Status for ${checked.length} Selected Application(s)`;
      statusModalScopeTxt.innerHTML = `You are updating the status of <strong>${checked.length}</strong> selected candidate profile(s).`;
      statusSubmitBtnTxt.textContent = `Update ${checked.length} Profile(s)`;

    } else if (scope === 'filtered') {
      if (filteredCount === 0) {
        alert('There are no applications matching the current filter to update.');
        return;
      }
      let filterDesc = [];
      if (filterStatus) filterDesc.push(`Status: <strong>${filterStatus}</strong>`);
      if (searchKeyword) filterDesc.push(`Search: <strong>${searchKeyword}</strong>`);
      let filterDetails = filterDesc.length > 0 ? ` matching (${filterDesc.join(', ')})` : '';

      statusModalTitleTxt.textContent = `Update All ${filteredCount} Filtered Applications`;
      statusModalScopeTxt.innerHTML = `You are updating all <strong>${filteredCount}</strong> applications${filterDetails}.`;
      statusSubmitBtnTxt.textContent = `Update All ${filteredCount} Filtered`;

    } else if (scope === 'all') {
      if (allCount === 0) {
        alert('There are no applications in the database to update.');
        return;
      }
      statusModalTitleTxt.textContent = `Update All ${allCount} Applications in Database`;
      statusModalScopeTxt.innerHTML = `You are about to update all <strong>${allCount}</strong> applications across the entire portal.`;
      statusSubmitBtnTxt.textContent = `Update All ${allCount} Applications`;
    }

    if (preselectedStatus) {
      selectStatusCard(preselectedStatus);
    } else {
      const activeCard = document.querySelector('.status-choice-card.active');
      if (!activeCard) {
        selectStatusCard('rejected');
      }
    }

    modal.show();
  };

  if (bulkStatusForm) {
    bulkStatusForm.addEventListener('submit', function(e) {
      const checkedRadio = bulkStatusForm.querySelector('input[name="new_status"]:checked');
      if (!checkedRadio) {
        e.preventDefault();
        alert('Please choose a status from the status cards.');
        return false;
      }
    });
  }
})();
</script>
</body>
</html>
