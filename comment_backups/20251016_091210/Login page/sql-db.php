<?php
// Database connection helper for login endpoints
// Force TCP to avoid macOS UNIX socket issues ("No such file or directory")
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_PORT = (int)(getenv('DB_PORT') ?: 3306); // adjust if using MAMP/XAMPP
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'EduVerse';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = null;
$db_connect_error = null;
try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    // Do not throw to avoid fatal; let caller respond with JSON error
    $db_connect_error = $e->getMessage();
    $conn = null;
}
?>