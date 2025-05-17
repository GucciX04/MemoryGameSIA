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

if ($attempts > 0 && $category) {
  try {
    $stmt = $pdo->prepare("INSERT INTO attempts (user_id, attempts, category) VALUES (:user_id, :attempts, :category)");
    $stmt->execute([
      ':user_id' => $user_id,
      ':attempts' => $attempts,
      ':category' => $category
    ]);
    echo "Attempt saved";
  } catch (PDOException $e) {
    http_response_code(500);
    echo "Database error: " . $e->getMessage();
  }
} else {
  http_response_code(400);
  echo "Invalid input";
}
?>
