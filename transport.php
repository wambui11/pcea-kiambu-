<?php
require_once 'config.php';
// --- Disable error display for production ---
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
echo "<h2>PHP RUNNING!</h2>";

// --- Database connection is now handled by config.php ---
// $conn is available globally

// --- Handle form submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data safely
    $studentName = trim($_POST['studentName']);
    $zone_id = $_POST['Zone_id']; // Fix: match the form field name
    $parent_phone = trim($_POST['parent_Phone']); // Fix: match the form field name
    $isOneWay = isset($_POST['is_one_way']) ? 1 : 0;

    // Validate required fields
    if (empty($studentName) || empty($zone_id) || empty($parent_phone)) {
        die("⚠ Please fill in all required fields!");
    }

    // Convert zone name to ID
    $zoneMap = [
        'Zone A' => 1,
        'Zone B' => 2,
        'Zone C' => 3,
        'Zone D' => 4,
        'Zone E' => 5,
        'Zone F' => 6
    ];
    if (!isset($zoneMap[$zone_id])) {
        die("⚠ Invalid zone selected!");
    }
    $zone_id = $zoneMap[$zone_id];

    // --- Insert student into admissions table ---
    $sqlInsertAdmission = "INSERT INTO admissions (student_name) VALUES (?)";
    $stmt = $conn->prepare($sqlInsertAdmission);
    $stmt->bind_param("s", $studentName);
    if (!$stmt->execute()) {
        die("Error inserting student: " . $stmt->error);
    }
    $admission_id = $stmt->insert_id;
    $stmt->close();

    // --- Get transport cost from zone ---
    $sqlCost = "SELECT full_cost FROM transport_zones WHERE zone_id = ?";
    $stmt2 = $conn->prepare($sqlCost);
    $stmt2->bind_param("i", $zone_id);
    $stmt2->execute();
    $result = $stmt2->get_result();
    if ($row = $result->fetch_assoc()) {
        $fullCost = $row['full_cost'];
    } else {
        die("Invalid zone selected.");
    }
    $stmt2->close();

    // --- Calculate cost ---
    $costCharged = $isOneWay ? round(0.75 * $fullCost, 2) : $fullCost;

    // --- Insert into transport_bookings ---
    $sqlBooking = "INSERT INTO transport_bookings (admission_id, zone_id, is_one_way, cost_charged, parent_phone)
                   VALUES (?, ?, ?, ?, ?)";
    $stmt3 = $conn->prepare($sqlBooking);
    $stmt3->bind_param("iiids", $admission_id, $zone_id, $isOneWay, $costCharged, $parent_phone);
    if (!$stmt3->execute()) {
        die("Error inserting booking: " . $stmt3->error);
    }
    $stmt3->close();

    // --- Display confirmation ---
    echo "<h2>✅ Transport Booking Successful!</h2>";
    echo "<p><strong>Student Name:</strong> $studentName</p>";
    echo "<p><strong>Zone Selected:</strong> $zone_id</p>";
    echo "<p><strong>Parent Phone:</strong> $parent_phone</p>";
    echo "<p><strong>Transport Type:</strong> " . ($isOneWay ? "One-way" : "Full") . "</p>";
    echo "<p><strong>Total Cost:</strong> Ksh $costCharged</p>";
    echo "<p><em>Your booking is pending approval. You will be contacted soon.</em></p>";
}
?>
