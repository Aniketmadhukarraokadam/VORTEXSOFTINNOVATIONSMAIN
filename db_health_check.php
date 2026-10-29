<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

admin_require_role(['super_admin']);
header('Content-Type: text/plain; charset=UTF-8');

try {
    $pdo = getDB();
    if (!$pdo) {
        die("Database connection failed.\n");
    }

    echo "--- Full Schema of job_applications ---\n";
    $cols = $pdo->query("DESCRIBE `job_applications`")->fetchAll();
    foreach ($cols as $c) {
        $default = $c['Default'] === null ? 'NULL' : "'{$c['Default']}'";
        echo sprintf("%-20s | %-25s | Null: %-3s | Def: %s\n", $c['Field'], $c['Type'], $c['Null'], $default);
    }

    echo "\n--- Recent Rows in job_applications ---\n";
    $rows = $pdo->query("SELECT id, applicant_name, email, job_title, created_at FROM job_applications ORDER BY id DESC LIMIT 5")->fetchAll();
    foreach ($rows as $r) {
        echo "#{$r['id']}: {$r['applicant_name']} | {$r['email']} | {$r['job_title']} | {$r['created_at']}\n";
    }

    echo "\n--- Test Inserting using api/apply.php EXACT Query ---\n";
    $stmt = $pdo->prepare("INSERT INTO job_applications
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
        ':job_title' => 'Test Position',
        ':dept'      => 'IT',
        ':name'      => 'Insert Test',
        ':email'     => 'insert_test@example.com',
        ':phone'     => '1234567890',
        ':location'  => 'Bangalore',
        ':exp'       => 2.0,
        ':curr_co'   => 'Test Co',
        ':notice'    => 'Immediate',
        ':ctc'       => '5 LPA',
        ':rf'        => null,
        ':rp'        => null,
        ':cover'     => 'Test cover',
        ':linkedin'  => '',
        ':portfolio' => '',
        ':ip'        => '127.0.0.1',
    ]);
    $id = $pdo->lastInsertId();
    echo " -> INSERT SUCCESSFUL! Generated ID: $id\n";
    
    // Clean it up
    $pdo->exec("DELETE FROM job_applications WHERE id = $id");
    echo " -> Cleaned up test row.\n";

} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
