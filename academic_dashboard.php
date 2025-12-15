<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: student portal.php");
    exit();
}

// Get student info from session or database
$username = $_SESSION['username'];

// Get student details from admissions table
$stmt = $conn->prepare("SELECT * FROM admissions WHERE student_name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$student_result = $stmt->get_result();
$student = $student_result->fetch_assoc();

if (!$student) {
    // Fallback: try to get from students table
    $stmt2 = $conn->prepare("SELECT * FROM students WHERE student_name = ?");
    $stmt2->bind_param("s", $username);
    $stmt2->execute();
    $student_result2 = $stmt2->get_result();
    $student = $student_result2->fetch_assoc();
    $stmt2->close();
}

$stmt->close();

if (!$student) {
    echo "Student record not found.";
    exit();
}

$student_name = $student['student_name'];
$grade = $student['grade'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Academic Dashboard - <?php echo htmlspecialchars($student_name); ?></title>
  <link rel="stylesheet" href="dashboard.css">
  <style>
    .academic-section {
      margin: 20px 0;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background-color: #f9f9f9;
    }
    .grade-summary {
      background-color: #e8f4fd;
      padding: 10px;
      border-radius: 5px;
      margin: 10px 0;
    }
    .subject-card {
      display: inline-block;
      margin: 10px;
      padding: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      background-color: white;
      min-width: 200px;
      text-align: center;
    }
    .attendance-status {
      padding: 5px 10px;
      border-radius: 15px;
      color: white;
      font-weight: bold;
    }
    .present { background-color: #28a745; }
    .absent { background-color: #dc3545; }
    .late { background-color: #ffc107; color: black; }
  </style>
</head>
<body>
  <header>
    <h1>🎓 Academic Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($student_name); ?> - Grade <?php echo htmlspecialchars($grade); ?></p>
    <nav>
      <a href="dashboard.php">🏠 Main Dashboard</a> |
      <a href="logout.php">🚪 Logout</a>
    </nav>
  </header>

  <main>
    <!-- Academic Overview -->
    <div class="academic-section">
      <h2>📊 Academic Overview</h2>
      <div class="grade-summary">
        <?php
        // Calculate overall academic performance
        $stmt = $conn->prepare("SELECT AVG(marks) as avg_marks, COUNT(*) as subject_count FROM report_cards WHERE student_name = ? AND grade = ?");
        $stmt->bind_param("ss", $student_name, $grade);
        $stmt->execute();
        $result = $stmt->get_result();
        $academic_summary = $result->fetch_assoc();
        $stmt->close();

        if ($academic_summary['subject_count'] > 0) {
            $avg_marks = round($academic_summary['avg_marks'], 2);
            $grade_letter = '';
            if ($avg_marks >= 90) $grade_letter = 'A+';
            elseif ($avg_marks >= 80) $grade_letter = 'A';
            elseif ($avg_marks >= 70) $grade_letter = 'B';
            elseif ($avg_marks >= 60) $grade_letter = 'C';
            elseif ($avg_marks >= 50) $grade_letter = 'D';
            else $grade_letter = 'F';

            echo "<h3>Overall Performance: {$avg_marks}% ({$grade_letter})</h3>";
            echo "<p>Subjects Enrolled: {$academic_summary['subject_count']}</p>";
        } else {
            echo "<p>No academic records found yet.</p>";
        }
        ?>
      </div>
    </div>

    <!-- Current Timetable -->
    <div class="academic-section">
      <h2>📅 Weekly Timetable</h2>
      <table border="1" cellpadding="8" style="width: 100%;">
        <tr style="background-color: #007bff; color: white;">
          <th>Day</th>
          <th>Time</th>
          <th>Subject</th>
          <th>Teacher</th>
        </tr>
        <?php
        $stmt = $conn->prepare("SELECT day_of_week, time, subject, teacher FROM timetable WHERE grade = ? ORDER BY FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday'), time");
        $stmt->bind_param("s", $grade);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['day_of_week']) . "</td>
                        <td>" . htmlspecialchars($row['time']) . "</td>
                        <td>" . htmlspecialchars($row['subject']) . "</td>
                        <td>" . htmlspecialchars($row['teacher']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4' style='text-align:center;'>No timetable found for your grade.</td></tr>";
        }
        $stmt->close();
        ?>
      </table>
    </div>

    <!-- Subject Performance -->
    <div class="academic-section">
      <h2>📚 Subject Performance</h2>
      <div style="display: flex; flex-wrap: wrap;">
        <?php
        $stmt = $conn->prepare("SELECT subject, marks FROM report_cards WHERE student_name = ? AND grade = ? ORDER BY subject");
        $stmt->bind_param("ss", $student_name, $grade);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $marks = $row['marks'];
                $subject = $row['subject'];
                
                // Determine grade color
                $color = '#dc3545'; // Red for F
                if ($marks >= 90) $color = '#28a745'; // Green for A+
                elseif ($marks >= 80) $color = '#20c997'; // Teal for A
                elseif ($marks >= 70) $color = '#17a2b8'; // Blue for B
                elseif ($marks >= 60) $color = '#ffc107'; // Yellow for C
                elseif ($marks >= 50) $color = '#fd7e14'; // Orange for D

                echo "<div class='subject-card' style='border-left: 5px solid {$color};'>
                        <h4>{$subject}</h4>
                        <p style='font-size: 24px; font-weight: bold; color: {$color};'>{$marks}%</p>
                      </div>";
            }
        } else {
            echo "<p>No grades recorded yet.</p>";
        }
        $stmt->close();
        ?>
      </div>
    </div>

    <!-- Attendance Summary -->
    <div class="academic-section">
      <h2>📋 Attendance Summary</h2>
      <?php
      // Check if attendance table exists and has data
      $attendance_query = "SELECT * FROM attendance WHERE student_name = ? AND grade = ? ORDER BY date DESC LIMIT 10";
      $stmt = $conn->prepare($attendance_query);
      
      if ($stmt) {
          $stmt->bind_param("ss", $student_name, $grade);
          $stmt->execute();
          $result = $stmt->get_result();
          
          if ($result->num_rows > 0) {
              echo "<table border='1' cellpadding='8' style='width: 100%;'>
                      <tr style='background-color: #007bff; color: white;'>
                        <th>Date</th>
                        <th>Subject</th>
                        <th>Status</th>
                      </tr>";
              
              while ($row = $result->fetch_assoc()) {
                  $status_class = strtolower($row['status']);
                  echo "<tr>
                          <td>" . htmlspecialchars($row['date']) . "</td>
                          <td>" . htmlspecialchars($row['subject']) . "</td>
                          <td><span class='attendance-status {$status_class}'>" . htmlspecialchars($row['status']) . "</span></td>
                        </tr>";
              }
              echo "</table>";
          } else {
              echo "<p>No attendance records found. <em>(Attendance tracking may not be implemented yet)</em></p>";
          }
          $stmt->close();
      } else {
          echo "<p>Attendance tracking not available yet.</p>";
      }
      ?>
    </div>

    <!-- Academic Calendar -->
    <div class="academic-section">
      <h2>📆 Upcoming Academic Events</h2>
      <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px;">
        <h4>📝 Upcoming Exams & Assignments</h4>
        <ul>
          <li><strong>Mid-term Exams:</strong> Next Month</li>
          <li><strong>Science Project:</strong> Due in 2 weeks</li>
          <li><strong>Math Quiz:</strong> This Friday</li>
        </ul>
        <p><em>Note: This is a placeholder. Academic calendar integration can be added later.</em></p>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 School Academic Management System</p>
  </footer>
</body>
</html>