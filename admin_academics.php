<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: admin_login.html");
  exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_subject'])) {
            $subject_name = trim($_POST['subject_name']);
            $subject_code = trim($_POST['subject_code']);
            $description = trim($_POST['description']);
            $grade_level = trim($_POST['grade_level']);
            $teacher = trim($_POST['teacher']);
            $credits = intval($_POST['credits']);

            $stmt = $conn->prepare("INSERT INTO subjects (subject_name, subject_code, description, grade_level, teacher, credits) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $subject_name, $subject_code, $description, $grade_level, $teacher, $credits);
            $stmt->execute();
            $stmt->close();
            echo "<script>alert('✅ Subject added successfully!'); window.location.href='admin_academics.php';</script>";
            exit();
        }

        if (isset($_POST['add_calendar_event'])) {
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $event_date = $_POST['event_date'];

            $stmt = $conn->prepare("INSERT INTO academic_calendar (event_title, event_description, event_date) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $description, $event_date);
            $stmt->execute();
            $stmt->close();
            echo "<script>alert('✅ Calendar event added successfully!'); window.location.href='admin_academics.php';</script>";
            exit();
        }

        if (isset($_POST['mark_attendance'])) {
            $student_name = trim($_POST['student_name']);
            $grade = trim($_POST['grade']);
            $subject = trim($_POST['subject']);
            $date = $_POST['date'];
            $status = $_POST['status'];
            $teacher = trim($_POST['teacher']);
            $notes = trim($_POST['notes']);

            $stmt = $conn->prepare("INSERT INTO attendance (student_name, grade, subject, date, status, teacher, notes) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE status=?, teacher=?, notes=?");
            $stmt->bind_param("ssssssssss", $student_name, $grade, $subject, $date, $status, $teacher, $notes, $status, $teacher, $notes);
            $stmt->execute();
            $stmt->close();
            echo "<script>alert('✅ Attendance marked successfully!'); window.location.href='admin_academics.php';</script>";
            exit();
        }
    } catch (Exception $e) {
        echo "<script>alert('❌ Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        exit();
    }
}

// Handle deletions
if (isset($_GET['delete_subject'])) {
    try {
        $id = intval($_GET['delete_subject']);
        $stmt = $conn->prepare("DELETE FROM subjects WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('🗑️ Subject deleted successfully!'); window.location.href='admin_academics.php';</script>";
        exit();
    } catch (Exception $e) {
        echo "<script>alert('❌ Error deleting subject: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        exit();
    }
}

if (isset($_GET['delete_event'])) {
    try {
        $id = intval($_GET['delete_event']);
        $stmt = $conn->prepare("DELETE FROM academic_calendar WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('🗑️ Event deleted successfully!'); window.location.href='admin_academics.php';</script>";
        exit();
    } catch (Exception $e) {
        echo "<script>alert('❌ Error deleting event: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Academic Management</title>
  <link rel="stylesheet" href="admin.css">
  <style>
    .tab-container {
      margin: 20px 0;
    }
    .tab-buttons {
      display: flex;
      background-color: #f1f1f1;
      border-radius: 8px 8px 0 0;
    }
    .tab-button {
      flex: 1;
      padding: 12px;
      background-color: #f1f1f1;
      border: none;
      cursor: pointer;
      font-size: 16px;
      border-radius: 8px 8px 0 0;
    }
    .tab-button.active {
      background-color: #007bff;
      color: white;
    }
    .tab-content {
      display: none;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 0 0 8px 8px;
      background-color: white;
    }
    .tab-content.active {
      display: block;
    }
    .form-group {
      margin: 15px 0;
    }
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }
    .form-group input, .form-group select, .form-group textarea {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
    }
    .form-group textarea {
      height: 80px;
      resize: vertical;
    }
    .btn {
      padding: 10px 20px;
      margin: 5px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }
    .btn-primary { background-color: #007bff; color: white; }
    .btn-success { background-color: #28a745; color: white; }
    .btn-danger { background-color: #dc3545; color: white; }
  </style>
</head>
<body>
  <h1>🎓 Academic Management System</h1>
  <p><a href="admin_dashboard.php">⬅ Back to Dashboard</a></p>
  <hr>

  <div class="tab-container">
    <div class="tab-buttons">
      <button class="tab-button active" onclick="showTab('subjects')">📚 Subjects</button>
      <button class="tab-button" onclick="showTab('calendar')">📅 Calendar</button>
      <button class="tab-button" onclick="showTab('attendance')">📋 Attendance</button>
      <button class="tab-button" onclick="showTab('overview')">📊 Overview</button>
    </div>

    <!-- Subjects Tab -->
    <div id="subjects" class="tab-content active">
      <h2>📚 Subject Management</h2>
      
      <form method="POST" style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3>Add New Subject</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
          <div class="form-group">
            <label>Subject Name:</label>
            <input type="text" name="subject_name" required>
          </div>
          <div class="form-group">
            <label>Subject Code:</label>
            <input type="text" name="subject_code" required>
          </div>
          <div class="form-group">
            <label>Grade Level:</label>
            <input type="text" name="grade_level" placeholder="e.g., Grade 1, All" required>
          </div>
          <div class="form-group">
            <label>Teacher:</label>
            <input type="text" name="teacher" required>
          </div>
          <div class="form-group">
            <label>Credits:</label>
            <input type="number" name="credits" min="1" max="10" value="3" required>
          </div>
        </div>
        <div class="form-group">
          <label>Description:</label>
          <textarea name="description" placeholder="Subject description..."></textarea>
        </div>
        <button type="submit" name="add_subject" class="btn btn-primary">➕ Add Subject</button>
      </form>

      <h3>Current Subjects</h3>
      <table border="1" cellpadding="8" style="width: 100%;">
        <tr style="background-color: #007bff; color: white;">
          <th>Code</th>
          <th>Subject Name</th>
          <th>Grade Level</th>
          <th>Teacher</th>
          <th>Credits</th>
          <th>Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM subjects ORDER BY subject_name");
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
              <td>" . htmlspecialchars($row['subject_code']) . "</td>
              <td>" . htmlspecialchars($row['subject_name']) . "</td>
              <td>" . htmlspecialchars($row['grade_level']) . "</td>
              <td>" . htmlspecialchars($row['teacher']) . "</td>
              <td>" . htmlspecialchars($row['credits']) . "</td>
              <td>
                <a href='?delete_subject=" . urlencode($row['id']) . "' onclick='return confirm(\"Delete this subject?\")' class='btn btn-danger'>🗑 Delete</a>
              </td>
            </tr>";
          }
        } else {
          echo "<tr><td colspan='6' style='text-align:center;'>No subjects found.</td></tr>";
        }
        ?>
      </table>
    </div>

    <!-- Calendar Tab -->
    <div id="calendar" class="tab-content">
      <h2>📅 Academic Calendar</h2>
      
      <form method="POST" style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3>Add Calendar Event</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
          <div class="form-group">
            <label>Event Title:</label>
            <input type="text" name="title" required>
          </div>
          <div class="form-group">
            <label>Event Date:</label>
            <input type="date" name="event_date" required>
          </div>
        </div>
        <div class="form-group">
          <label>Description:</label>
          <textarea name="description" placeholder="Event description..."></textarea>
        </div>
        <button type="submit" name="add_calendar_event" class="btn btn-primary">➕ Add Event</button>
      </form>

      <h3>Upcoming Events</h3>
      <table border="1" cellpadding="8" style="width: 100%;">
        <tr style="background-color: #007bff; color: white;">
          <th>Date</th>
          <th>Title</th>
          <th>Description</th>
          <th>Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM academic_calendar ORDER BY event_date ASC");
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
              <td>" . htmlspecialchars($row['event_date']) . "</td>
              <td>" . htmlspecialchars($row['event_title']) . "</td>
              <td>" . htmlspecialchars($row['event_description']) . "</td>
              <td>
                <a href='?delete_event=" . urlencode($row['id']) . "' onclick='return confirm(\"Delete this event?\")' class='btn btn-danger'>🗑 Delete</a>
              </td>
            </tr>";
          }
        } else {
          echo "<tr><td colspan='4' style='text-align:center;'>No events found.</td></tr>";
        }
        ?>
      </table>
    </div>

    <!-- Attendance Tab -->
    <div id="attendance" class="tab-content">
      <h2>📋 Attendance Management</h2>
      
      <form method="POST" style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3>Mark Attendance</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
          <div class="form-group">
            <label>Student Name:</label>
            <input type="text" name="student_name" required>
          </div>
          <div class="form-group">
            <label>Grade:</label>
            <input type="text" name="grade" required>
          </div>
          <div class="form-group">
            <label>Subject:</label>
            <input type="text" name="subject" required>
          </div>
          <div class="form-group">
            <label>Date:</label>
            <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
          </div>
          <div class="form-group">
            <label>Status:</label>
            <select name="status" required>
              <option value="Present">Present</option>
              <option value="Absent">Absent</option>
              <option value="Late">Late</option>
              <option value="Excused">Excused</option>
            </select>
          </div>
          <div class="form-group">
            <label>Teacher:</label>
            <input type="text" name="teacher" required>
          </div>
        </div>
        <div class="form-group">
          <label>Notes (optional):</label>
          <textarea name="notes" placeholder="Additional notes..."></textarea>
        </div>
        <button type="submit" name="mark_attendance" class="btn btn-success">✅ Mark Attendance</button>
      </form>

      <h3>Recent Attendance Records</h3>
      <table border="1" cellpadding="8" style="width: 100%;">
        <tr style="background-color: #007bff; color: white;">
          <th>Date</th>
          <th>Student</th>
          <th>Grade</th>
          <th>Subject</th>
          <th>Status</th>
          <th>Teacher</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM attendance ORDER BY date DESC, student_name LIMIT 20");
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $status_color = '';
            switch($row['status']) {
              case 'Present': $status_color = 'color: green; font-weight: bold;'; break;
              case 'Absent': $status_color = 'color: red; font-weight: bold;'; break;
              case 'Late': $status_color = 'color: orange; font-weight: bold;'; break;
              case 'Excused': $status_color = 'color: blue; font-weight: bold;'; break;
            }
            
            echo "<tr>
              <td>" . htmlspecialchars($row['date']) . "</td>
              <td>" . htmlspecialchars($row['student_name']) . "</td>
              <td>" . htmlspecialchars($row['grade']) . "</td>
              <td>" . htmlspecialchars($row['subject']) . "</td>
              <td style='{$status_color}'>" . htmlspecialchars($row['status']) . "</td>
              <td>" . htmlspecialchars($row['teacher']) . "</td>
            </tr>";
          }
        } else {
          echo "<tr><td colspan='6' style='text-align:center;'>No attendance records found.</td></tr>";
        }
        ?>
      </table>
    </div>

    <!-- Overview Tab -->
    <div id="overview" class="tab-content">
      <h2>📊 Academic Overview</h2>
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <div style="background-color: #e8f4fd; padding: 20px; border-radius: 8px;">
          <h3>📚 Total Subjects</h3>
          <?php
          $result = $conn->query("SELECT COUNT(*) as count FROM subjects");
          $count = $result->fetch_assoc()['count'];
          echo "<p style='font-size: 24px; font-weight: bold; color: #007bff;'>{$count}</p>";
          ?>
        </div>
        
        <div style="background-color: #d4edda; padding: 20px; border-radius: 8px;">
          <h3>🎓 Total Students with Grades</h3>
          <?php
          $result = $conn->query("SELECT COUNT(DISTINCT student_name) as count FROM report_cards");
          $count = $result->fetch_assoc()['count'];
          echo "<p style='font-size: 24px; font-weight: bold; color: #28a745;'>{$count}</p>";
          ?>
        </div>
      </div>

      <div style="background-color: #fff3cd; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3>📅 Upcoming Events (Next 7 Days)</h3>
        <?php
        $next_week = date('Y-m-d', strtotime('+7 days'));
        $today = date('Y-m-d');
        $result = $conn->query("SELECT * FROM academic_calendar WHERE event_date BETWEEN '$today' AND '$next_week' ORDER BY event_date");
        
        if ($result && $result->num_rows > 0) {
          echo "<ul>";
          while ($row = $result->fetch_assoc()) {
            echo "<li><strong>" . htmlspecialchars($row['event_date']) . "</strong> - " . htmlspecialchars($row['event_title']) . "</li>";
          }
          echo "</ul>";
        } else {
          echo "<p>No upcoming events in the next 7 days.</p>";
        }
        ?>
      </div>

      <div style="background-color: #f8d7da; padding: 20px; border-radius: 8px;">
        <h3>⚠️ Academic Alerts</h3>
        <ul>
          <li>Review attendance records for accuracy</li>
          <li>Update timetables for next semester</li>
          <li>Prepare mid-term exam schedules</li>
          <li>Check for missing grade entries</li>
        </ul>
      </div>
    </div>
  </div>

  <script>
    function showTab(tabName) {
      // Hide all tab contents
      const contents = document.querySelectorAll('.tab-content');
      contents.forEach(content => content.classList.remove('active'));
      
      // Remove active class from all buttons
      const buttons = document.querySelectorAll('.tab-button');
      buttons.forEach(button => button.classList.remove('active'));
      
      // Show selected tab and activate button
      document.getElementById(tabName).classList.add('active');
      event.target.classList.add('active');
    }
  </script>
</body>
</html>