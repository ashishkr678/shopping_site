<?php
// database/create_admin.php
include('../config/db_connect.php'); // ensure path is correct

$adminEmail = 'admin@shop.com';
$adminName  = 'Super Admin';
$rawPass    = 'admin123'; // change this

// check if admin exists
$check = $conn->prepare("SELECT id FROM admins WHERE email = ?");
$check->bind_param("s", $adminEmail);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "Admin already exists.";
    $check->close();
    exit;
}
$check->close();

// insert hashed password
$hashed = password_hash($rawPass, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO admins (full_name, email, password, role) VALUES (?, ?, ?, 'SUPERADMIN')");
$stmt->bind_param("sss", $adminName, $adminEmail, $hashed);

if ($stmt->execute()) {
    echo "Admin created: $adminEmail (password: $rawPass) — delete this script now for security.";
} else {
    echo "Error: " . $conn->error;
}
$stmt->close();
$conn->close();
