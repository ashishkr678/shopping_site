<?php
session_start();
header('Content-Type: application/json');
include('../config/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

if (!isset($_POST['cart_id'])) {
    echo json_encode(["success" => false, "message" => "Missing cart_id"]);
    exit;
}

$cartId = $_POST['cart_id'];
$userId = $_SESSION['user_id'];

$sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cartId, $userId);
$stmt->execute();

echo json_encode(["success" => true, "message" => "Item removed from cart"]);
$stmt->close();
$conn->close();
?>
