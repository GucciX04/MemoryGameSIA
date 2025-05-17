<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
  try {
    $stmt->execute([$username, $password]);
    header("Location: login.php");
    exit;
  } catch (PDOException $e) {
    $error = "Username already exists.";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Sign Up</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="auth-container">
    <h2>Sign Up</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="submit" value="Sign Up" />
      <div class="link">
        Already have an account? <a href="login.php">Login</a>
      </div>
    </form>
  </div>
</body>
</html>
