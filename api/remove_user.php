<?php
session_start();
header('Content-Type: application/json');
include('../config/db_connect.php');

if (!isset($_SESSION['admin_id'])) {
  echo json_encode(["success" => false, "message" => "Unauthorized access"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['user_id']) || empty($data['user_id'])) {
  echo json_encode(["success" => false, "message" => "Missing user ID"]);
  exit;
}

$user_id = (int)$data['user_id'];

$check = $conn->prepare("SELECT id FROM users WHERE id = ?");
$check->bind_param("i", $user_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
  echo json_encode(["success" => false, "message" => "User not found"]);
  exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
  echo json_encode(["success" => true, "message" => "User deleted successfully"]);
} else {
  echo json_encode(["success" => false, "message" => "Failed to delete user"]);
}

$stmt->close();
$conn->close();
?>
