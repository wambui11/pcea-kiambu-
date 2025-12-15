<?php
// config.php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$servername = getenv('DB_HOST') ?: 'localhost';
$dbUsername = getenv('DB_USER') ?: 'root';
$dbPassword     = getenv('DB_PASS') ?: '';
$dbName     = getenv('DB_NAME') ?: 'schooldb';

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

$conn->set_charset('utf8mb4');
?>
