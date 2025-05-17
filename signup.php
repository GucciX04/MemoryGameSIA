<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

  $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
  $stmt->execute([$username]);
  if ($stmt->fetch()) {
    die("Username already exists.");
  }

  $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
  $stmt->execute([$username, $password]);

  header("Location: login.php");
  exit;
}
?>

<h2>Sign Up</h2>
<form method="POST">
  <input type="text" name="username" placeholder="Username" required><br>
  <input type="password" name="password" placeholder="Password" required><br>
  <button type="submit">Sign Up</button>
</form>
<a href="login.php">Already have an account? Log in</a>
