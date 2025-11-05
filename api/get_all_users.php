<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
session_start();

include('../config/db_connect.php');

// ✅ Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

try {
    // ✅ Fetch all users
    $sql = "SELECT id, full_name, email, phone, role, created_at 
            FROM users 
            ORDER BY created_at DESC";
    $result = $conn->query($sql);

    $users = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    echo json_encode(["success" => true, "users" => $users]);
} catch (Throwable $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
