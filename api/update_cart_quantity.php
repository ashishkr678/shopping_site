<?php
session_start();
header('Content-Type: application/json');
error_reporting(0); // Prevent PHP warnings from breaking JSON
include('../config/db_connect.php');

$response = ["success" => false, "message" => "Unknown error"];

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

if (empty($_POST['cart_id']) || empty($_POST['quantity'])) {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
    exit;
}

$cartId = intval($_POST['cart_id']);
$quantity = intval($_POST['quantity']);
$userId = $_SESSION['user_id'];

if ($quantity < 1) {
    echo json_encode(["success" => false, "message" => "Invalid quantity"]);
    exit;
}

try {
    $query = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    if (!$query) throw new Exception("Query prepare failed: " . $conn->error);

    $query->bind_param("iii", $quantity, $cartId, $userId);
    if (!$query->execute()) throw new Exception("Execute failed: " . $query->error);

    if ($query->affected_rows > 0) {
        $response = ["success" => true, "message" => "Quantity updated"];
    } else {
        $response = ["success" => false, "message" => "No change made"];
    }
} catch (Exception $e) {
    $response = ["success" => false, "message" => $e->getMessage()];
}

echo json_encode($response);
$conn->close();
exit;
?>
