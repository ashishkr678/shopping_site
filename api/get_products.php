<?php
header('Content-Type: application/json');
include('../config/db_connect.php');

// Fetch all products
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$products = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            "id" => $row["id"],
            "brand" => $row["brand"],
            "title" => $row["title"],
            "color" => $row["color"],
            "description" => $row["description"],
            "price" => (float)$row["price"],
            "discounted_price" => (float)$row["discounted_price"],
            "discount_percent" => (int)$row["discount_percent"],
            "image_url" => $row["image_url"],
            "top_level_category" => $row["top_level_category"],
            "second_level_category" => $row["second_level_category"],
            "third_level_category" => $row["third_level_category"],
            "quantity" => (int)$row["quantity"]
        ];
    }
}

echo json_encode($products);
$conn->close();
?>
