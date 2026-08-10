<?php
/**
 * Vortexsoft Innovations — Admin: Secure File Download
 * /admin/download.php?id=X
 */

session_start();
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Verify admin session
admin_check();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    http_response_code(400);
    die('Invalid application ID.');
}

$db = getDB();
if (!$db) {
    http_response_code(500);
    die('Database connection failed.');
}

try {
    $stmt = $db->prepare("SELECT resume_filename, resume_path FROM job_applications WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $app = $stmt->fetch();

    if (!$app || empty($app['resume_filename'])) {
        http_response_code(404);
        die('Resume file not found for this application.');
    }

    $upload_dir = UPLOADS_PATH . '/resumes/';
    $filepath   = $upload_dir . basename($app['resume_filename']);

    if (!file_exists($filepath)) {
        http_response_code(404);
        die('Resume file does not exist on server.');
    }

    $ext   = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    $mimes = [
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    $mime = $mimes[$ext] ?? 'application/octet-stream';

    // Clear buffer & disable compression
    if (ob_get_level()) ob_end_clean();

    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . basename($app['resume_filename']) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));

    log_admin_activity('Resume Downloaded', "Downloaded resume for application ID: {$id}");

    readfile($filepath);
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    die('Error retrieving file.');
}
