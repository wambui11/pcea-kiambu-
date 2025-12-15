<?php
session_start();
require_once 'config.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['student_name'] ?? '';
    $grade    = $_POST['grade'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($grade) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Sanitize / escape inputs
        $username_safe = mysqli_real_escape_string($conn, $username);
        $grade_safe    = mysqli_real_escape_string($conn, $grade);
        $password_safe = mysqli_real_escape_string($conn, $password);

        $sql = "SELECT * FROM students
                WHERE student_name = '$username_safe'
                  AND grade = '$grade_safe'";

        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) === 1) {
            $student_data = mysqli_fetch_assoc($result);
            if (password_verify($password,$student_data['password'])) {
                $_SESSION['username'] = $username;
                $_SESSION['student_name'] = $username;
                $_SESSION['grade'] = $grade;
                $_SESSION['student_id'] = $student_data['id'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Invalid username or grade.";
        }
    }
}

// If not post or error, show form + error
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Login</title>
  <link rel="stylesheet" href="student_portal.css">
</head>
<body>
  <main>
    <h1>Login</h1>
    <?php if ($error): ?>
      <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST">
      <label for="student_name">Student Name:</label>
      <input type="text" id="student_name" name="student_name" required><br><br>

      <label for="grade">Grade:</label>
      <input type="text" id="grade" name="grade" required><br><br>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required><br><br>

      <button type="submit">Login</button>
    </form>
  </main>
  <footer>
    <p>&copy; 2025 Student Academic Portal</p>
  </footer>
</body>
</html>
