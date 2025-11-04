<?php
session_start();
include('../config/db_connect.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(["success" => false, "message" => "Not logged in"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$order_id = $data['order_id'] ?? null;

if (!$order_id) {
  echo json_encode(["success" => false, "message" => "Invalid request"]);
  exit;
}

$user_id = $_SESSION['user_id'];

$check = $conn->prepare("SELECT status FROM orders WHERE id = ? AND user_id = ?");
$check->bind_param("ii", $order_id, $user_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 0) {
  echo json_encode(["success" => false, "message" => "Order not found"]);
  exit;
}

$row = $res->fetch_assoc();

// ✅ Block only delivered orders
if ($row['status'] === 'Delivered') {
  echo json_encode(["success" => false, "message" => "Delivered orders cannot be cancelled."]);
  exit;
}

// ✅ Cancel all other statuses
$update = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ? AND user_id = ?");
$update->bind_param("ii", $order_id, $user_id);
$update->execute();

echo json_encode(["success" => true]);
?>
