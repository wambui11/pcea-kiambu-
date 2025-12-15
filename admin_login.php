<?php
require_once 'config.php';
require_once 'session_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Fetch admin user
    $stmt = $conn->prepare("SELECT password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $username;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid username.";
    }
    $stmt->close();
} else {
    // If not POST, check if admin is already logged in
    if (isset($_SESSION['admin'])) {
        header("Location: admin_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="admin_login.css">
</head>
<body>
  <h1>Admin Login</h1>
  <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <form action="admin_login.php" method="POST">
    <label>Username</label><br>
    <input type="text" name="username" required><br><br>
    <label>Password</label><br>
    <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
  </form>
  <!-- Default credentials removed for security -->
</body>
</html>
