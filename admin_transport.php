<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: admin_login.html");
  exit();
}

// Approve booking
if (isset($_GET['approve'])) {
  $id = intval($_GET['approve']);
  $stmt = $conn->prepare("UPDATE transport_bookings SET status='Approved' WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  header("Location: admin_transport.php");
  exit();
}

// Decline booking
if (isset($_GET['decline'])) {
  $id = intval($_GET['decline']);
  $stmt = $conn->prepare("UPDATE transport_bookings SET status='Declined' WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  header("Location: admin_transport.php");
  exit();
}

// Delete booking
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $stmt = $conn->prepare("DELETE FROM transport_bookings WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  header("Location: admin_transport.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Transport Bookings</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <h1>🚌 Transport Bookings Management</h1>
  <p><a href="admin_dashboard.php">⬅ Back to Dashboard</a></p>
  <hr>

  <table border="1" cellpadding="8" width="90%">
    <tr style="background:#007bff; color:white;">
      <th>ID</th>
      <th>Student</th>
      <th>Zone</th>
      <th>One Way?</th>
      <th>Cost</th>
      <th>Phone</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>

    <?php
    $sql = "SELECT tb.*, a.student_name, tz.zone_name as zone
            FROM transport_bookings tb
            JOIN admissions a ON tb.admission_id = a.admission_id
            JOIN transport_zones tz ON tb.zone_id = tz.zone_id
            ORDER BY tb.id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $statusColor = $row['status'] == 'Approved' ? 'green' : ($row['status'] == 'Declined' ? 'red' : 'orange');
        echo "<tr>
          <td>" . htmlspecialchars($row['id']) . "</td>
          <td>" . htmlspecialchars($row['student_name']) . "</td>
          <td>" . htmlspecialchars($row['zone']) . "</td>
          <td>" . ($row['is_one_way'] ? 'Yes' : 'No') . "</td>
          <td>Ksh " . htmlspecialchars($row['cost_charged']) . "</td>
          <td>" . htmlspecialchars($row['parent_phone']) . "</td>
          <td style='color:$statusColor;'>" . htmlspecialchars($row['status']) . "</td>
          <td>
            <a href='?approve=" . urlencode($row['id']) . "'>✅ Approve</a> |
            <a href='?decline=" . urlencode($row['id']) . "'>❌ Decline</a> |
            <a href='?delete=" . urlencode($row['id']) . "' onclick='return confirm(\"Delete this booking?\")'>🗑 Delete</a>
          </td>
        </tr>";
      }
    } else {
      echo "<tr><td colspan='8' style='text-align:center;'>No bookings found.</td></tr>";
    }
    ?>
  </table>
</body>
</html>