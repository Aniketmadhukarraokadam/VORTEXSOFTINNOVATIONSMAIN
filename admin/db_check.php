<?php
/**
 * Vortexsoft — Live DB Connection Diagnostic
 * Access: https://vortexsoftinnovations.com/admin/db_check.php?token=VortexDeploy6498286f401141b8
 * DELETE THIS FILE after fixing the DB password.
 */

$token = $_GET["token"] ?? "";
if (!hash_equals("VortexDeploy6498286f401141b8", $token)) {
    http_response_code(403);
    die("Unauthorized");
}

require_once __DIR__ . "/../config/constants.php";
require_once __DIR__ . "/../config/database.php";

header("Content-Type: text/plain; charset=utf-8");

echo "=== VORTEXSOFT DB DIAGNOSTIC ===\n\n";
echo "DB_HOST : " . DB_HOST . "\n";
echo "DB_NAME : " . DB_NAME . "\n";
echo "DB_USER : " . DB_USER . "\n";
echo "DB_PASS : " . (strlen(DB_PASS) > 0 ? str_repeat("*", strlen(DB_PASS)) : "*** EMPTY — THIS IS THE BUG ***") . "\n\n";

$db = getDB();

if (!$db) {
    echo "CONNECTION: FAILED — getDB() returned null\n";
    echo "Fix DB_PASS in config/.env on the server.\n";
    exit;
}

$driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
echo "CONNECTION: OK (driver: $driver)\n";

if ($driver === "sqlite") {
    echo "WARNING: Connected via SQLite fallback — NOT Hostinger MySQL!\n";
    echo "Fix DB_PASS in config/.env on the server.\n\n";
}

$tables = ["contact_inquiries", "job_applications", "blog_posts", "newsletter_subscribers", "admin_users"];
echo "\nTABLE ROW COUNTS:\n";
foreach ($tables as $table) {
    try {
        $count = $db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "  $table: $count rows\n";
    } catch (Exception $e) {
        echo "  $table: ERROR — " . $e->getMessage() . "\n";
    }
}

echo "\n=== END DIAGNOSTIC — DELETE THIS FILE AFTER USE ===\n";
