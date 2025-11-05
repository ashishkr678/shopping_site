<?php
session_start();
include('../config/db_connect.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(["success" => false, "message" => "User not logged in"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$order_id = $data['order_id'] ?? null;

if (!$order_id) {
  echo json_encode(["success" => false, "message" => "Invalid order ID"]);
  exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Verify order ownership & status
$check = $conn->prepare("SELECT status FROM orders WHERE id = ? AND user_id = ?");
$check->bind_param("ii", $order_id, $user_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 0) {
  echo json_encode(["success" => false, "message" => "Order not found"]);
  exit;
}

$order = $res->fetch_assoc();

if ($order['status'] === 'Delivered') {
  echo json_encode(["success" => false, "message" => "Delivered orders cannot be cancelled."]);
  exit;
}

// ✅ Update to Cancelled
$update = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ? AND user_id = ?");
$update->bind_param("ii", $order_id, $user_id);
$update->execute();

echo json_encode(["success" => true, "message" => "Order cancelled successfully"]);
?>
