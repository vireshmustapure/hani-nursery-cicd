<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "ivhymbbv_mahesh";

echo "Connecting to MySQL database $database...\n";
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$tables = [
    'addresses', 'admins', 'authors', 'blogs', 'coupons',
    'newsletter_subscriptions', 'order_items', 'orders',
    'posts', 'products', 'reviews', 'seo_metadata',
    'settings', 'users', 'wishlist'
];

echo "Dropping tables individually...\n";
foreach ($tables as $table) {
    if ($conn->query("DROP TABLE IF EXISTS `$table`")) {
        echo "Dropped table $table (or it didn't exist).\n";
    } else {
        echo "Failed to drop table $table: " . $conn->error . "\n";
    }
}

echo "Creating tables...\n";

function run_q($conn, $sql, $tbl) {
    if ($conn->query($sql)) {
        echo "Created table $tbl\n";
    } else {
        echo "FAILED to create table $tbl: " . $conn->error . "\n";
    }
}

// 1. settings
run_q($conn, "CREATE TABLE `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(255) NOT NULL UNIQUE,
    `setting_value` LONGTEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'settings');

// 2. users
run_q($conn, "CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `mobile` VARCHAR(20) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'users');

// 3. products
run_q($conn, "CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10,2) NOT NULL,
    `mrp` DECIMAL(10,2) DEFAULT NULL,
    `discount_percent` INT DEFAULT NULL,
    `tags` TEXT,
    `category` VARCHAR(100),
    `image` VARCHAR(255),
    `stock` INT NOT NULL DEFAULT 0,
    `is_top_sold` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'products');

// 4. orders
run_q($conn, "CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `address` TEXT NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `pincode` VARCHAR(10) NOT NULL,
    `state` VARCHAR(255) NOT NULL,
    `city` VARCHAR(255) NOT NULL,
    `landmark` VARCHAR(255) DEFAULT NULL,
    `address_type` VARCHAR(50) DEFAULT 'Home',
    `total_amount` DECIMAL(10,2) NOT NULL,
    `payment_method` VARCHAR(50) DEFAULT 'COD',
    `status` VARCHAR(50) DEFAULT 'pending',
    `order_status` VARCHAR(50) DEFAULT 'Pending',
    `user_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'orders');

// 5. order_items
run_q($conn, "CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'order_items');

// 6. wishlist
run_q($conn, "CREATE TABLE `wishlist` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'wishlist');

// 7. reviews
run_q($conn, "CREATE TABLE `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `user_name` VARCHAR(255) NOT NULL,
    `rating` INT NOT NULL,
    `review_text` TEXT,
    `status` VARCHAR(50) DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'reviews');

// 8. newsletter_subscriptions
run_q($conn, "CREATE TABLE `newsletter_subscriptions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'newsletter_subscriptions');

// 9. seo_metadata
run_q($conn, "CREATE TABLE `seo_metadata` (
    `uri` VARCHAR(255) NOT NULL PRIMARY KEY,
    `title` VARCHAR(255) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `published_at` VARCHAR(50) DEFAULT NULL,
    `updated_at` VARCHAR(50) DEFAULT NULL,
    `author_id` INT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'seo_metadata');

// 10. admins
run_q($conn, "CREATE TABLE `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'admins');

// 11. authors
run_q($conn, "CREATE TABLE `authors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    `image` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'authors');

// 12. blogs
run_q($conn, "CREATE TABLE `blogs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT,
    `image` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'blogs');

// 13. coupons
run_q($conn, "CREATE TABLE `coupons` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `discount_type` VARCHAR(20) NOT NULL,
    `discount_value` DECIMAL(10,2) NOT NULL,
    `target_type` VARCHAR(50) NULL,
    `status` VARCHAR(20) DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'coupons');

// 14. addresses
run_q($conn, "CREATE TABLE `addresses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `customer_name` VARCHAR(100) NOT NULL,
    `address` TEXT NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `pincode` VARCHAR(10) NOT NULL,
    `state` VARCHAR(100) NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `landmark` VARCHAR(255) NULL,
    `address_type` VARCHAR(20) DEFAULT 'Home',
    `is_default` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'addresses');

// 15. posts
run_q($conn, "CREATE TABLE `posts` (
    `slug` VARCHAR(255) NOT NULL PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `meta_title` VARCHAR(255) DEFAULT NULL,
    `meta_description` TEXT DEFAULT NULL,
    `intro_content` TEXT DEFAULT NULL,
    `body_sections` LONGTEXT DEFAULT NULL,
    `authorId` INT DEFAULT NULL,
    `author_name` VARCHAR(255) DEFAULT NULL,
    `published_at` DATETIME DEFAULT NULL,
    `category` VARCHAR(100) DEFAULT 'General',
    `status` VARCHAR(50) DEFAULT 'published',
    `is_pinned` INT DEFAULT 0,
    `image` VARCHAR(255) DEFAULT NULL,
    `content_type` VARCHAR(50) DEFAULT 'form',
    `raw_html` LONGTEXT DEFAULT NULL,
    `content_html` LONGTEXT DEFAULT NULL,
    `s1_h` VARCHAR(255) DEFAULT NULL, `s1_t` VARCHAR(50) DEFAULT 'h2', `s1_c` TEXT DEFAULT NULL,
    `s2_h` VARCHAR(255) DEFAULT NULL, `s2_t` VARCHAR(50) DEFAULT 'h2', `s2_c` TEXT DEFAULT NULL,
    `s3_h` VARCHAR(255) DEFAULT NULL, `s3_t` VARCHAR(50) DEFAULT 'h2', `s3_c` TEXT DEFAULT NULL,
    `s4_h` VARCHAR(255) DEFAULT NULL, `s4_t` VARCHAR(50) DEFAULT 'h2', `s4_c` TEXT DEFAULT NULL,
    `s5_h` VARCHAR(255) DEFAULT NULL, `s5_t` VARCHAR(50) DEFAULT 'h2', `s5_c` TEXT DEFAULT NULL,
    `s6_h` VARCHAR(255) DEFAULT NULL, `s6_t` VARCHAR(50) DEFAULT 'h2', `s6_c` TEXT DEFAULT NULL,
    `s7_h` VARCHAR(255) DEFAULT NULL, `s7_t` VARCHAR(50) DEFAULT 'h2', `s7_c` TEXT DEFAULT NULL,
    `s8_h` VARCHAR(255) DEFAULT NULL, `s8_t` VARCHAR(50) DEFAULT 'h2', `s8_c` TEXT DEFAULT NULL,
    `s9_h` VARCHAR(255) DEFAULT NULL, `s9_t` VARCHAR(50) DEFAULT 'h2', `s9_c` TEXT DEFAULT NULL,
    `s10_h` VARCHAR(255) DEFAULT NULL, `s10_t` VARCHAR(50) DEFAULT 'h2', `s10_c` TEXT DEFAULT NULL,
    `s11_h` VARCHAR(255) DEFAULT NULL, `s11_t` VARCHAR(50) DEFAULT 'h2', `s11_c` TEXT DEFAULT NULL,
    `s12_h` VARCHAR(255) DEFAULT NULL, `s12_t` VARCHAR(50) DEFAULT 'h2', `s12_c` TEXT DEFAULT NULL,
    `s13_h` VARCHAR(255) DEFAULT NULL, `s13_t` VARCHAR(50) DEFAULT 'h2', `s13_c` TEXT DEFAULT NULL,
    `s14_h` VARCHAR(255) DEFAULT NULL, `s14_t` VARCHAR(50) DEFAULT 'h2', `s14_c` TEXT DEFAULT NULL,
    `s15_h` VARCHAR(255) DEFAULT NULL, `s15_t` VARCHAR(50) DEFAULT 'h2', `s15_c` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", 'posts');

echo "Inserting default admin account...\n";
$admin_pass = md5("admin");
if ($conn->query("INSERT INTO admins (username, password) VALUES ('admin', '$admin_pass')")) {
    echo "Inserted admin successfully\n";
} else {
    echo "FAILED to insert admin: " . $conn->error . "\n";
}

echo "Inserting default products...\n";
if ($conn->query("INSERT INTO products (name, description, price, mrp, discount_percent, tags, category, image, stock, is_top_sold)
VALUES ('Tulsi Plant (Tulasi)', 'Tulsi plant is a sacred and beneficial herb.', 50.00, 50.00, 0, 'fragrance, outdoor', 'Outdoor Plants', 'tulsi.jpg', 10, 1)")) {
    echo "Inserted product successfully\n";
} else {
    echo "FAILED to insert product: " . $conn->error . "\n";
}

echo "Database tables recreated and initialized successfully!\n";
?>
