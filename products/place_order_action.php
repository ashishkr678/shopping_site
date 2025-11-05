<?php
session_start();
include('../config/db_connect.php');

if (!isset($_SESSION['user_id'])) {
  die("❌ Unauthorized access!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id'];
  $address = trim($_POST['address']);

  // ✅ Fetch cart items
  $stmt = $conn->prepare("SELECT c.product_id, c.quantity, p.discounted_price 
                          FROM cart c 
                          JOIN products p ON c.product_id = p.id 
                          WHERE c.user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    die("❌ Cart is empty!");
  }

  $cart_items = [];
  $total_amount = 0;

  while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_amount += $row['discounted_price'] * $row['quantity'];
  }

  $stmt->close();

  // ✅ Insert into orders table
  $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, address_line1, status) VALUES (?, ?, ?, 'PENDING')");
  $stmt->bind_param("ids", $user_id, $total_amount, $address);
  $stmt->execute();
  $order_id = $stmt->insert_id;
  $stmt->close();

  // ✅ Insert order_items
  $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
  foreach ($cart_items as $item) {
    $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['discounted_price']);
    $stmt->execute();
  }
  $stmt->close();

  // ✅ Clear cart after order placed
  $clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
  $clear->bind_param("i", $user_id);
  $clear->execute();
  $clear->close();

  header("Location: my_orders.php?success=1");
  exit;
}
?>
