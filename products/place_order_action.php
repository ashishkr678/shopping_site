<?php
session_start();
include('../config/db_connect.php');

if (!isset($_SESSION['user_id'])) {
  die("❌ Unauthorized access!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id'];
  $address = trim($_POST['address']);
  $cart = json_decode($_POST['cart_data'], true);

  if (empty($cart)) {
    die("❌ Cart is empty!");
  }

  // 🧮 Calculate total amount
  $total_amount = 0;
  foreach ($cart as $item) {
    $price = isset($item['discountedPrice']) ? $item['discountedPrice'] : 0;
    $qty = isset($item['qty']) ? (int)$item['qty'] : 1;
    $total_amount += $price * $qty;
  }

  // 🧾 Insert into orders table
  $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'PENDING')");
  $stmt->bind_param("id", $user_id, $total_amount);
  $stmt->execute();
  $order_id = $stmt->insert_id;
  $stmt->close();

  // 📦 Insert into order_items table
  $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
  foreach ($cart as $item) {
    // Handle cases where id may differ (id / product_id)
    $product_id = $item['id'] ?? $item['product_id'] ?? null;
    $qty = $item['qty'] ?? 1;
    $price = $item['discountedPrice'] ?? 0;

    if ($product_id === null) {
      // Skip items with missing product_id to avoid database error
      continue;
    }

    $stmt->bind_param("iiid", $order_id, $product_id, $qty, $price);
    $stmt->execute();
  }
  $stmt->close();

  // 🧹 Clear cart from localStorage (handled in frontend)
  header("Location: my_orders.php?success=1");
  exit;
}
?>
