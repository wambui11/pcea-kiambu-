<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: admin_login.php");
  exit();
}

// Add new report card entry
if (isset($_POST['add'])) {
  $student_name = trim($_POST['student_name']);
  $grade = trim($_POST['grade']);
  $subject = trim($_POST['subject']);
  $marks = floatval($_POST['marks']);

  if ($student_name && $grade && $subject && $marks >= 0) {
    $stmt = $conn->prepare("INSERT INTO report_cards (student_name, grade, subject, marks) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $student_name, $grade, $subject, $marks);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('✅ Report card entry added successfully!'); window.location.href='admin_reports.php';</script>";
    exit();
  }
}

// Update existing report card entry
if (isset($_POST['update'])) {
  $id = intval($_POST['id']);
  $student_name = trim($_POST['student_name']);
  $grade = trim($_POST['grade']);
  $subject = trim($_POST['subject']);
  $marks = floatval($_POST['marks']);

  if ($student_name && $grade && $subject && $marks >= 0) {
    $stmt = $conn->prepare("UPDATE report_cards SET student_name=?, grade=?, subject=?, marks=? WHERE id=?");
    $stmt->bind_param("sssdi", $student_name, $grade, $subject, $marks, $id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('📝 Report card updated successfully!'); window.location.href='admin_reports.php';</script>";
    exit();
  }
}

// Delete report card entry
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $stmt = $conn->prepare("DELETE FROM report_cards WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
  echo "<script>alert('🗑️ Entry deleted successfully!'); window.location.href='admin_reports.php';</script>";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Report Cards</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <h1>📊 Report Cards Management</h1>
  <p><a href="admin_dashboard.php">⬅ Back to Dashboard</a></p>
  <hr>

  <!-- Add/Edit Form -->
  <form method="POST" id="reportForm">
    <input type="hidden" name="id" id="id">
    <label>Student Name:</label>
    <input type="text" name="student_name" id="student_name" required>
    <label>Grade:</label>
    <input type="text" name="grade" id="grade" required>
    <label>Subject:</label>
    <input type="text" name="subject" id="subject" required>
    <label>Marks:</label>
    <input type="number" name="marks" id="marks" step="0.01" min="0" max="100" required>
    <button type="submit" name="add" id="addBtn">➕ Add Entry</button>
    <button type="submit" name="update" id="updateBtn" style="display:none;">💾 Update Entry</button>
    <button type="button" onclick="resetForm()">↩ Cancel</button>
  </form>

  <h2>📖 Current Report Card Entries</h2>
  <table border="1" cellpadding="8" width="90%">
    <tr style="background:#007bff;color:white;">
      <th>ID</th>
      <th>Student Name</th>
      <th>Grade</th>
      <th>Subject</th>
      <th>Marks</th>
      <th>Action</th>
    </tr>
    <?php
    $res = $conn->query("SELECT * FROM report_cards ORDER BY student_name, grade, subject");
    if ($res->num_rows > 0) {
      while ($row = $res->fetch_assoc()) {
        echo "<tr>
          <td>" . htmlspecialchars($row['id']) . "</td>
          <td>" . htmlspecialchars($row['student_name']) . "</td>
          <td>" . htmlspecialchars($row['grade']) . "</td>
          <td>" . htmlspecialchars($row['subject']) . "</td>
          <td>" . htmlspecialchars($row['marks']) . "</td>
          <td>
            <button onclick=\"editRow('".htmlspecialchars($row['id'])."', '".htmlspecialchars($row['student_name'])."', '".htmlspecialchars($row['grade'])."', '".htmlspecialchars($row['subject'])."', '".htmlspecialchars($row['marks'])."')\">✏ Edit</button>
            <a href='?delete=" . urlencode($row['id']) . "' onclick='return confirm(\"Delete this entry?\")'>🗑 Delete</a>
          </td>
        </tr>";
      }
    } else {
      echo "<tr><td colspan='6' style='text-align:center;'>No report card entries found.</td></tr>";
    }
    ?>
  </table>

  <script>
    function editRow(id, student_name, grade, subject, marks) {
      document.getElementById('id').value = id;
      document.getElementById('student_name').value = student_name;
      document.getElementById('grade').value = grade;
      document.getElementById('subject').value = subject;
      document.getElementById('marks').value = marks;
      document.getElementById('addBtn').style.display = 'none';
      document.getElementById('updateBtn').style.display = 'inline';
    }
    function resetForm() {
      document.getElementById('id').value = '';
      document.getElementById('student_name').value = '';
      document.getElementById('grade').value = '';
      document.getElementById('subject').value = '';
      document.getElementById('marks').value = '';
      document.getElementById('addBtn').style.display = 'inline';
      document.getElementById('updateBtn').style.display = 'none';
    }
  </script>
</body>
</html>
