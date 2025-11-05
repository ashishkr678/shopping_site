<?php
header("Content-Type: application/json");
include('../config/db_connect.php');

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Product ID missing"]);
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if ($product) {
    echo json_encode($product);
} else {
    echo json_encode([]);
}

$stmt->close();
$conn->close();
?>
