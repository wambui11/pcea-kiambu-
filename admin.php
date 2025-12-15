<?php
require_once 'config.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - PCEA Kiambu Academy</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <h1>🏫 Admin Panel - PCEA Kiambu Academy</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin']); ?>!</p>

    <div class="admin-options">
        <h2>Quick Actions</h2>
        <ul>
            <li><a href="admin_dashboard.php">Go to Dashboard</a></li>
            <li><a href="admin_admissions.php">Manage Admissions</a></li>
            <li><a href="admin_timetable.php">Manage Timetable</a></li>
            <li><a href="admin_transport.php">Manage Transport</a></li>
            <li><a href="admin_reports.php">View Reports</a></li>
            <li><a href="admin_academics.php">Academic Management</a></li>
        </ul>
    </div>

    <div class="admin-info">
        <h3>System Information</h3>
        <p>Database: Connected to schooldb</p>
        <p>Server Time: <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>

    <p style="margin-top: 40px;"><a href="logout.php">🚪 Logout</a></p>
</body>
</html>
