<?php
session_start();
include('../config/db_connect.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode([]);
  exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
          o.id AS order_id,
          o.status,
          o.total_amount,
          o.created_at,
          oi.quantity,
          oi.price,
          p.title,
          p.image_url
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
  $orders[] = $row;
}

echo json_encode($orders);
$stmt->close();
$conn->close();
?>
