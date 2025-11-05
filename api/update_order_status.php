<?php
session_start();
include('../config/db_connect.php');
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
  echo json_encode(["success" => false, "message" => "Unauthorized access"]);
  exit;
}

// ✅ Support both form POST or JSON body
$data = $_POST;
if (empty($data)) {
  $data = json_decode(file_get_contents("php://input"), true);
}

$order_id = $data['order_id'] ?? null;
$status = strtoupper(trim($data['status'] ?? ''));

if (!$order_id || !$status) {
  echo json_encode(["success" => false, "message" => "Missing order ID or status"]);
  exit;
}

// ✅ Check if order exists
$check = $conn->prepare("SELECT id FROM orders WHERE id = ?");
$check->bind_param("i", $order_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 0) {
  echo json_encode(["success" => false, "message" => "Order not found"]);
  exit;
}

// ✅ Update order status
$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);
if ($stmt->execute()) {
  // Optional: You can notify users by email here
  echo json_encode(["success" => true, "message" => "Order status updated successfully"]);
} else {
  echo json_encode(["success" => false, "message" => "Failed to update order"]);
}

$stmt->close();
$conn->close();
?>
