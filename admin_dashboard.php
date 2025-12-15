<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: admin_login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <h1>🏫 Admin Dashboard - PCEA Kiambu Academy</h1>
  <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin']); ?>!</p>

  <div class="dashboard-grid">
    <div class="dashboard-card">
      <h3>📋 Manage Admissions</h3>
      <p>View and manage student admissions.</p>
      <a href="admin_admissions.php">Go to Admissions</a>
    </div>

    <div class="dashboard-card">
      <h3>📅 Manage Timetable</h3>
      <p>Add, edit, and delete timetable entries.</p>
      <a href="admin_timetable.php">Go to Timetable</a>
    </div>

    <div class="dashboard-card">
      <h3>🚌 Manage Transport</h3>
      <p>Approve or decline transport bookings.</p>
      <a href="admin_transport.php">Go to Transport</a>
    </div>

    <div class="dashboard-card">
      <h3>📊 View Reports</h3>
      <p>View student reports and analytics.</p>
      <a href="admin_reports.php">Go to Reports</a>
    </div>

    <div class="dashboard-card">
      <h3>🎓 Academic Management</h3>
      <p>Manage subjects, calendar, and attendance.</p>
      <a href="admin_academics.php">Go to Academics</a>
    </div>
  </div>

  <p style="margin-top: 40px;"><a href="logout.php">🚪 Logout</a></p>
</body>
</html>
