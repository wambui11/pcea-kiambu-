<?php
// admission.php
require_once 'config.php';

// 3. Collect form data (POST)
$studentName     = $_POST['student-name'] ?? "";
$fatherName      = $_POST["father_name"] ?? "";       // change form field name to "father_name"
$motherName      = $_POST["mother_name"] ?? "";
$guardianName    = $_POST["guardian_name"] ?? "";
$email           = $_POST['email'] ?? "";             // change form field name to "email"
$residence       = $_POST['residence'] ?? "";
$dob             = $_POST['date-of-birth'] ?? "";
$gender          = $_POST['gender'] ?? "";
$grade           = $_POST['student-grade'] ?? "";
$club            = $_POST['clubs_societies'] ?? "";   // change form field name to "clubs_societies"
$transportMethod = $_POST['student-transport'] ?? "";
$allergyDetails  = $_POST['allergy-details'] ?? "";
$isAsthma        = ($_POST['asthma'] ?? 'no') === 'yes' ? 1 : 0;
$isEpilepsy      = ($_POST['epilepsy'] ?? 'no') === 'yes' ? 1 : 0;
$isDiabetes      = ($_POST['diabetes'] ?? 'no') === 'yes' ? 1 : 0;
$otherMedical    = $_POST['medical-info'] ?? "";
$emergency1      = $_POST['emergency-contact1'] ?? "";

// 4. Validation (simple)
if (empty($studentName) || empty($fatherName) || empty($email)) {
    echo "Error: Please make sure required fields are filled.";
    exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Error: Invalid email format.";
    exit();
}

// 5. Prepare INSERT statement
$sql = "INSERT INTO admissions (
    student_name,
    father_name,
    mother_name,
    guardian_name,
    email,
    residence,
    date_of_birth,
    gender,
    grade,
    clubs,
    transport_method,
    allergies,
    asthma,
    epilepsy,
    diabetes,
    other_medical,
    emergency_contact1
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

// Bind parameters (17 strings, 3 ints)
$stmt->bind_param(
    "ssssssssssssiiiss",
    $studentName,     // s
    $fatherName,      // s
    $motherName,      // s
    $guardianName,    // s
    $email,           // s
    $residence,       // s
    $dob,             // s
    $gender,          // s
    $grade,           // s
    $club,            // s
    $transportMethod, // s
    $allergyDetails,  // s
    $isAsthma,        // i
    $isEpilepsy,      // i
    $isDiabetes,      // i
    $otherMedical,    // s
    $emergency1       // s
);

if ($stmt->execute()) {
    echo "✅ Admission submitted successfully! Your admission ID: " . htmlspecialchars($stmt->insert_id);
} else {
    echo "❌ Error: " . htmlspecialchars($stmt->error);
}

$stmt->close();
$conn->close();
?>




