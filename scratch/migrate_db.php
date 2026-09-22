<?php
$conn = new mysqli('localhost', 'root', '', 'ivhymbbv_mahesh');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Starting database migrations...\n";

// 1. Update products table
$cols = [];
$res = $conn->query("SHOW COLUMNS FROM products");
while ($row = $res->fetch_assoc()) {
    $cols[] = $row['Field'];
}

if (!in_array('mrp', $cols)) {
    $conn->query("ALTER TABLE products ADD COLUMN mrp DECIMAL(10,2) NULL DEFAULT NULL");
    echo "Added 'mrp' to products table.\n";
}
if (!in_array('discount_percent', $cols)) {
    $conn->query("ALTER TABLE products ADD COLUMN discount_percent INT NULL DEFAULT NULL");
    echo "Added 'discount_percent' to products table.\n";
}
if (!in_array('tags', $cols)) {
    $conn->query("ALTER TABLE products ADD COLUMN tags TEXT NULL DEFAULT NULL");
    echo "Added 'tags' to products table.\n";
}

// 2. Create coupons table
$conn->query("CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount_type VARCHAR(20) NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    target_type VARCHAR(50) NULL,
    status VARCHAR(20) DEFAULT 'active'
)");
echo "Ensured 'coupons' table exists.\n";

// 3. Create addresses table
$conn->query("CREATE TABLE IF NOT EXISTS addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    state VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    landmark VARCHAR(255) NULL,
    address_type VARCHAR(20) DEFAULT 'Home',
    is_default INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "Ensured 'addresses' table exists.\n";

// 4. Helper function to insert default setting row if not exists
function insert_setting_if_not_exists($conn, $key, $value) {
    $key_esc = $conn->real_escape_string($key);
    $res = $conn->query("SELECT id FROM settings WHERE setting_key='$key_esc'");
    if ($res->num_rows == 0) {
        $val_esc = $conn->real_escape_string($value);
        $conn->query("INSERT INTO settings (setting_key, setting_value, created_at) VALUES ('$key_esc', '$val_esc', NOW())");
        echo "Inserted default setting: $key\n";
    }
}

// 5. Insert default SMTP Settings
insert_setting_if_not_exists($conn, 'smtp_host', 'smtp.gmail.com');
insert_setting_if_not_exists($conn, 'smtp_port', '587');
insert_setting_if_not_exists($conn, 'smtp_user', 'kallurviresh39@gmail.com');
insert_setting_if_not_exists($conn, 'smtp_pass', 'znamlbvnmkiysvkb');
insert_setting_if_not_exists($conn, 'smtp_secure', 'tls');
insert_setting_if_not_exists($conn, 'smtp_from_email', 'vireshmmustapure39@gmail.com');
insert_setting_if_not_exists($conn, 'smtp_from_name', 'Hani Nursery');

// 6. Insert default Offer codes/values
insert_setting_if_not_exists($conn, 'global_offer_code', 'HANI10');
insert_setting_if_not_exists($conn, 'global_offer_value', '10%');

// 7. Insert default mail templates
insert_setting_if_not_exists($conn, 'mail_tpl_otp_sub', 'Hani Nursery - OTP Verification');
insert_setting_if_not_exists($conn, 'mail_tpl_otp_body', '<h2>Your OTP is</h2><h1>{otp_code}</h1><p>Dear {user_name}, use the code above to verify your account.</p>');

insert_setting_if_not_exists($conn, 'mail_tpl_login_sub', 'Successful Login - Hani Nursery');
insert_setting_if_not_exists($conn, 'mail_tpl_login_body', '<p>Hello {user_name},</p><p>You have successfully logged into your account. If this was not you, please secure your credentials immediately.</p><p>Use offer code: <b>{offer_code}</b> to get <b>{offer_value}</b> off on your next purchase!</p>');

insert_setting_if_not_exists($conn, 'mail_tpl_abandoned_sub', 'Did you leave something behind? - Hani Nursery');
insert_setting_if_not_exists($conn, 'mail_tpl_abandoned_body', '<p>Hello {user_name},</p><p>You left some beautiful green plants in your cart! Complete your purchase today using coupon <b>{offer_code}</b> for an exclusive <b>{offer_value}</b> discount!</p><p>Your items: <br>{cart_items}</p>');

insert_setting_if_not_exists($conn, 'mail_tpl_placed_sub', 'Order Placed Successfully! - Hani Nursery');
insert_setting_if_not_exists($conn, 'mail_tpl_placed_body', '<p>Dear {user_name},</p><p>Thank you for shopping with Hani Nursery! Your order <b>#{order_id}</b> has been placed successfully.</p><p>Total Amount: <b>₹{order_total}</b></p>');

insert_setting_if_not_exists($conn, 'mail_tpl_packed_sub', 'Your order #{order_id} is Packed - Hani Nursery');
insert_setting_if_not_exists($conn, 'mail_tpl_packed_body', '<p>Dear {user_name},</p><p>Great news! Your order <b>#{order_id}</b> has been packed carefully and is ready for shipment.</p>');

insert_setting_if_not_exists($conn, 'mail_tpl_shipped_sub', 'Your order #{order_id} is Out for Shipping - Hani Nursery');
insert_setting_if_not_exists($conn, 'mail_tpl_shipped_body', '<p>Dear {user_name},</p><p>Your order <b>#{order_id}</b> has been shipped. It is in transit and will reach you soon.</p>');

insert_setting_if_not_exists($conn, 'mail_tpl_delivered_sub', 'Order Delivered! - Hani Nursery');
insert_setting_if_not_exists($conn, 'mail_tpl_delivered_body', '<p>Dear {user_name},</p><p>Your order <b>#{order_id}</b> has been delivered successfully. We hope you enjoy your new green friends! 🌿</p>');

// 8. Insert default banner settings JSON
insert_setting_if_not_exists($conn, 'header_banners_json', json_encode([
    [
        'text' => '🌿 WEEKDAY SPECIAL 🌿 Buy any plants & get exciting offers on fruit plants! 🚜',
        'days' => [1, 2, 3, 4, 5]
    ],
    [
        'text' => '🎉 WEEKEND OFFER 🎉 Show your Google rating/review at shop counter and get <span>10% OFF</span>!',
        'days' => [6, 7]
    ]
]));

echo "Database migrations complete!\n";
?>
