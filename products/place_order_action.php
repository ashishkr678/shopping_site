<?php
session_start();
include('../config/db_connect.php');

if (!isset($_SESSION['user_id'])) {
  die("❌ Unauthorized access!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id'];
  $address = trim($_POST['address']);
  $cart = isset($_POST['cart_data']) ? json_decode($_POST['cart_data'], true) : [];

  if (empty($cart)) {
    die("❌ Cart is empty!");
  }

  // Calculate total
  $total_amount = 0;
  foreach ($cart as $item) {
    $price = $item['discountedPrice'] ?? 0;
    $qty = $item['qty'] ?? 1;
    $total_amount += $price * $qty;
  }

  // Insert into orders table
  $stmt = $conn->prepare("INSERT INTO orders (user_id, address, total_amount, status) VALUES (?, ?, ?, 'PENDING')");
  $stmt->bind_param("isd", $user_id, $address, $total_amount);
  $stmt->execute();
  $order_id = $stmt->insert_id;
  $stmt->close();

  // Insert each order item
  $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_title, quantity, price) VALUES (?, ?, ?, ?, ?)");
  foreach ($cart as $item) {
    $product_id = $item['id'] ?? $item['product_id'] ?? 0;
    $title = $item['title'] ?? 'Unknown Product';
    $qty = $item['qty'] ?? 1;
    $price = $item['discountedPrice'] ?? 0;

    $stmt->bind_param("iisid", $order_id, $product_id, $title, $qty, $price);
    $stmt->execute();
  }
  $stmt->close();

  header("Location: my_orders.php?success=1");
  exit;
}
?>
