<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: admin_login.html");
  exit();
}

// 🟩 Add new timetable entry
if (isset($_POST['add'])) {
  $grade = trim($_POST['grade']);
  $day = trim($_POST['day_of_week']);
  $time = trim($_POST['time']);
  $subject = trim($_POST['subject']);
  $teacher = trim($_POST['teacher']);

  $stmt = $conn->prepare("INSERT INTO timetable (grade, day_of_week, time, subject, teacher) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $grade, $day, $time, $subject, $teacher);
  $stmt->execute();
  echo "<script>alert('✅ Timetable entry added successfully!'); window.location.href='admin_timetable.php';</script>";
  exit();
}

// 🟦 Update existing record
if (isset($_POST['update'])) {
  $id = intval($_POST['id']);
  $grade = trim($_POST['grade']);
  $day = trim($_POST['day_of_week']);
  $time = trim($_POST['time']);
  $subject = trim($_POST['subject']);
  $teacher = trim($_POST['teacher']);

  $stmt = $conn->prepare("UPDATE timetable SET grade=?, day_of_week=?, time=?, subject=?, teacher=? WHERE id=?");
  $stmt->bind_param("sssssi", $grade, $day, $time, $subject, $teacher, $id);
  $stmt->execute();
  echo "<script>alert('📝 Timetable updated successfully!'); window.location.href='admin_timetable.php';</script>";
  exit();
}

// 🟥 Delete timetable record (safe)
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $stmt = $conn->prepare("DELETE FROM timetable WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  echo "<script>alert('🗑️ Entry deleted successfully!'); window.location.href='admin_timetable.php';</script>";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Manage Timetable</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <h1>📅 Timetable Management</h1>
  <p><a href="admin_dashboard.php">⬅ Back to Dashboard</a></p>
  <hr>

  <!-- Add/Edit Form -->
  <form method="POST" id="timetableForm">
    <input type="hidden" name="id" id="id">
    <label>Grade:</label>
    <input type="text" name="grade" id="grade" required>
    <label>Day:</label>
    <input type="text" name="day_of_week" id="day_of_week" required>
    <label>Time:</label>
    <input type="time" name="time" id="time" required>
    <label>Subject:</label>
    <input type="text" name="subject" id="subject" required>
    <label>Teacher:</label>
    <input type="text" name="teacher" id="teacher" required>
    <button type="submit" name="add" id="addBtn">➕ Add Entry</button>
    <button type="submit" name="update" id="updateBtn" style="display:none;">💾 Update Entry</button>
    <button type="button" onclick="resetForm()">↩ Cancel</button>
  </form>

  <h2>📖 Current Timetable Records</h2>
  <table border="1" cellpadding="8" width="90%">
    <tr style="background:#007bff;color:white;">
      <th>ID</th>
      <th>Grade</th>
      <th>Day</th>
      <th>Time</th>
      <th>Subject</th>
      <th>Teacher</th>
      <th>Action</th>
    </tr>
    <?php
    $res = $conn->query("SELECT * FROM timetable ORDER BY grade, day_of_week");
    if ($res->num_rows > 0) {
      while ($row = $res->fetch_assoc()) {
        echo "<tr>
          <td>" . htmlspecialchars($row['id']) . "</td>
          <td>" . htmlspecialchars($row['grade']) . "</td>
          <td>" . htmlspecialchars($row['day_of_week']) . "</td>
          <td>" . htmlspecialchars($row['time']) . "</td>
          <td>" . htmlspecialchars($row['subject']) . "</td>
          <td>" . htmlspecialchars($row['teacher']) . "</td>
          <td>
            <button onclick=\"editRow('".htmlspecialchars($row['id'])."', '".htmlspecialchars($row['grade'])."', '".htmlspecialchars($row['day_of_week'])."', '".htmlspecialchars($row['time'])."', '".htmlspecialchars($row['subject'])."', '".htmlspecialchars($row['teacher'])."')\">✏ Edit</button>
            <a href='?delete=" . urlencode($row['id']) . "' onclick='return confirm(\"Delete this entry?\")'>🗑 Delete</a>
          </td>
        </tr>";
      }
    } else {
      echo "<tr><td colspan='7' style='text-align:center;'>No timetable entries found.</td></tr>";
    }
    ?>
  </table>

  <script>
    function editRow(id, grade, day, time, subject, teacher) {
      document.getElementById('id').value = id;
      document.getElementById('grade').value = grade;
      document.getElementById('day_of_week').value = day;
      document.getElementById('time').value = time;
      document.getElementById('subject').value = subject;
      document.getElementById('teacher').value = teacher;
      document.getElementById('addBtn').style.display = 'none';
      document.getElementById('updateBtn').style.display = 'inline';
    }
    function resetForm() {
      document.getElementById('id').value = '';
      document.getElementById('grade').value = '';
      document.getElementById('day_of_week').value = '';
      document.getElementById('time').value = '';
      document.getElementById('subject').value = '';
      document.getElementById('teacher').value = '';
      document.getElementById('addBtn').style.display = 'inline';
      document.getElementById('updateBtn').style.display = 'none';
    }
  </script>
</body>
</html>