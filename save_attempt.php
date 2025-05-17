<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
  http_response_code(403);
  echo "Unauthorized";
  exit;
}

$user_id = $_SESSION['user_id'];
$attempts = isset($_POST['attempts']) ? intval($_POST['attempts']) : 0;
$category = isset($_POST['category']) ? $_POST['category'] : '';
$difficulty = isset($_POST['difficulty']) ? $_POST['difficulty'] : '';

if ($attempts > 0 && $category && $difficulty) {
  $stmt = $pdo->prepare("INSERT INTO attempts (user_id, attempts, category, difficulty) VALUES (?, ?, ?, ?)");
  $stmt->execute([$user_id, $attempts, $category, $difficulty]);
  echo "Attempt saved";
} else {
  http_response_code(400);
  echo "Invalid input";
}
?>
