<?php
session_start();
header('Content-Type: application/json');
include('../config/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT 
            c.id AS cart_id,
            c.quantity,
            c.selected_size,
            p.id AS product_id,
            p.title,
            p.brand,
            p.discounted_price,
            p.price,
            p.discount_percent,
            p.image_url
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}

echo json_encode($cartItems);
$stmt->close();
$conn->close();
?>
