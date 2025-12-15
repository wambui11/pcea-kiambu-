<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: admin_login.html");
  exit();
}

// Add new student (use prepared statement)
if (isset($_POST['add'])) {
  $name = trim($_POST['student_name']);
  $grade = trim($_POST['grade']);
  $phone = trim($_POST['parent_phone']);
  // Basic validation (optional: more rigorous checks)
  if ($name && $grade) {
    $stmt = $conn->prepare("INSERT INTO admissions (student_name, grade, parent_phone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $grade, $phone);
    $stmt->execute();
    $stmt->close();
  }
}

// Approve student (use POST and prepared statement)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve'])) {
  $id = intval($_POST['approve']);
  $stmt = $conn->prepare("UPDATE admissions SET status = 'Approved' WHERE admission_id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
}

// Delete student (use POST and prepared statement)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
  $id = intval($_POST['delete']);
  $stmt = $conn->prepare("DELETE FROM admissions WHERE admission_id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Admissions</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <h1>📋 Admissions Management</h1>
  <form method="POST" autocomplete="off">
    <label>Student Name:</label>
    <input type="text" name="student_name" required>
    <label>Grade:</label>
    <input type="text" name="grade" required>
    <label>Parent Phone:</label>
    <input type="text" name="parent_phone">
    <button type="submit" name="add">Add Student</button>
  </form>

  <h2>Current Admissions</h2>
  <table border="1" cellpadding="6">
    <tr><th>ID</th><th>Name</th><th>Grade</th><th>Parent Phone</th><th>Action</th></tr>
    <?php
    $res = $conn->query("SELECT * FROM admissions");
    while($row = $res->fetch_assoc()){
      echo "<tr>
        <td>{$row['admission_id']}</td>
        <td>" . htmlspecialchars($row['student_name']) . "</td>
        <td>" . htmlspecialchars($row['grade']) . "</td>
        <td>" . htmlspecialchars($row['parent_phone']) . "</td>
        <td>
          <form method='POST' style='display:inline;' onsubmit='return confirm(\"Approve this student?\");'>
            <input type='hidden' name='approve' value='{$row['admission_id']}'>
            <button type='submit'>Approve</button>
          </form>
          <form method='POST' style='display:inline;' onsubmit='return confirm(\"Delete this student?\");'>
            <input type='hidden' name='delete' value='{$row['admission_id']}'>
            <button type='submit'>Delete</button>
          </form>
        </td>
      </tr>";
    }
    ?>
  </table>
  <p><a href="admin_dashboard.php">⬅ Back to Dashboard</a></p>
</body>
</html>
