<?php
// database/setup_database.php
$servername = "localhost";
$username = "root";
$password = ""; // change if you set one in XAMPP
$port = 3307; // your MySQL port

try {
    // Step 1: Connect to MySQL
    $conn = new PDO("mysql:host=$servername;port=$port", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Step 2: Create database if not exists
    $dbName = "shopping_db";
    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database '$dbName' created or already exists.<br>";

    // Step 3: Use the created DB
    $conn->exec("USE $dbName");

    // Step 4: Create tables
    $tables = [

        // USERS
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            phone VARCHAR(15),
            address TEXT,
            role ENUM('USER', 'ADMIN') DEFAULT 'USER',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        // ADMINS
        "CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('SUPERADMIN','ADMIN') DEFAULT 'ADMIN',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        // PRODUCTS
        "CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            brand VARCHAR(100),
            color VARCHAR(50),
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            discounted_price DECIMAL(10,2),
            discount_percent INT,
            image_url VARCHAR(500),
            top_level_category VARCHAR(100),
            second_level_category VARCHAR(100),
            third_level_category VARCHAR(100),
            quantity INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        // ORDERS
        "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            total_amount DECIMAL(10,2) NOT NULL,
            status ENUM('PENDING','SHIPPED','DELIVERED','CANCELLED') DEFAULT 'PENDING',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )",

        // ORDER ITEMS
        "CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id),
            FOREIGN KEY (product_id) REFERENCES products(id)
        )"
    ];

    foreach ($tables as $sql) {
        $conn->exec($sql);
    }

    echo "✅ All tables created successfully!<br>";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
