<?php
session_start();
header('Content-Type: application/json');
include('../config/db_connect.php');

// Ensure user logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$userId = $_SESSION['user_id'];

// Handle both JSON and form data
$input = file_get_contents("php://input");
if (!empty($input)) {
    $data = json_decode($input, true);
} else {
    $data = $_POST;
}

if (!isset($data['product_id']) || !isset($data['quantity'])) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

$productId = (int)$data['product_id'];
$quantity = (int)$data['quantity'];
$size = isset($data['selected_size']) ? $data['selected_size'] : null;

// ✅ Check if product exists in cart (handle NULL size correctly)
if ($size === null) {
    $sql = "SELECT id FROM cart WHERE user_id = ? AND product_id = ? AND selected_size IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $productId);
} else {
    $sql = "SELECT id FROM cart WHERE user_id = ? AND product_id = ? AND selected_size = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $userId, $productId, $size);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // ✅ Update existing cart item
    $row = $result->fetch_assoc();
    $cartId = $row['id'];
    $update = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
    $update->bind_param("ii", $quantity, $cartId);
    $update->execute();
    echo json_encode(["success" => true, "message" => "Cart updated successfully!"]);
} else {
    // ✅ Insert new cart item
    $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, selected_size) VALUES (?, ?, ?, ?)");
    $insert->bind_param("iiis", $userId, $productId, $quantity, $size);
    $insert->execute();
    echo json_encode(["success" => true, "message" => "Item added to cart successfully!"]);
}

$stmt->close();
$conn->close();
?>
