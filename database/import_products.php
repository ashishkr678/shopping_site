<?php
// import_products.php
include('../config/db_connect.php');

// ✅ Correct path to the JS file
$filePath = realpath(__DIR__ . '/../assets/mens_kurta.js');

if (!$filePath || !file_exists($filePath)) {
    die("❌ Error: mens_kurta.js file not found. Expected at: " . __DIR__ . '/../assets/mens_kurta.js');
}

// Step 1: Read the JS file
$content = file_get_contents($filePath);

// Step 2: Extract JSON content between the brackets [ ... ]
// It removes the "export const mens_kurta =" part and isolates only the array
if (preg_match('/\[\s*{.*}\s*\]/s', $content, $matches)) {
    $jsonData = $matches[0];
} else {
    die("❌ Error: Could not extract JSON array from JS file. Please check formatting.");
}

// Step 3: Decode the JSON safely
$data = json_decode($jsonData, true);
if ($data === null) {
    die("❌ Error: Failed to parse JSON — check your file syntax.");
}

// Step 4: Prepare insert query
$stmt = $conn->prepare("
    INSERT INTO products 
    (title, brand, color, description, price, discounted_price, discount_percent, image_url, top_level_category, second_level_category, third_level_category, quantity)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

// Step 5: Loop through and insert each product
$count = 0;
foreach ($data as $item) {
    $title = $item['title'] ?? '';
    $brand = $item['brand'] ?? '';
    $color = $item['color'] ?? '';
    $description = $item['description'] ?? '';
    $price = $item['price'] ?? 0;
    $discounted_price = $item['discountedPrice'] ?? 0;
    $discount_percent = $item['discountedPercent'] ?? 0;
    $image_url = $item['imageUrl'] ?? '';
    $top = $item['topLevelcategory'] ?? '';
    $second = $item['secondLevelcategory'] ?? '';
    $third = $item['thirdLevelcategory'] ?? '';
    $quantity = $item['quantity'] ?? 0;

    $stmt->bind_param(
        "ssssddissssi",
        $title, $brand, $color, $description,
        $price, $discounted_price, $discount_percent,
        $image_url, $top, $second, $third, $quantity
    );
    $stmt->execute();
    $count++;
}

echo "✅ Successfully inserted $count products into the database from mens_kurta.js!";
?>
