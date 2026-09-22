<?php
session_start();
include('../../config/db.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../src/Exception.php';
require '../../src/PHPMailer.php';
require '../../src/SMTP.php';

/* =========================
   LOGIN USER EMAIL
========================= */

$email = $_SESSION['email'];

$user_id = $_SESSION['user'];

/* =========================
   CHECK CART
========================= */

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    die("Cart is empty");
}

/* =========================
   GET FORM DATA
========================= */

$name      = mysqli_real_escape_string($conn, $_POST['name']);
$phone     = mysqli_real_escape_string($conn, $_POST['phone']);
$pincode   = mysqli_real_escape_string($conn, $_POST['pincode']);
$state     = mysqli_real_escape_string($conn, $_POST['state']);
$city      = mysqli_real_escape_string($conn, $_POST['city']);
$landmark  = mysqli_real_escape_string($conn, $_POST['landmark']);
$address   = mysqli_real_escape_string($conn, $_POST['address']);
$type      = mysqli_real_escape_string($conn, $_POST['type']);
$payment   = mysqli_real_escape_string($conn, $_POST['payment']);

$total = 0;

/* =========================
   CHECK STOCK + TOTAL
========================= */

foreach ($_SESSION['cart'] as $id => $qty) {

    $id = (int)$id;
    $qty = (int)$qty;

    $res = mysqli_query($conn,
    "SELECT * FROM products WHERE id='$id'");

    $row = mysqli_fetch_assoc($res);

    if (!$row) {
        die("Product not found.");
    }

    if ($row['stock'] < $qty) {
        die($row['name'] . " only " . $row['stock'] . " left in stock.");
    }

    $total += ($row['price'] * $qty);
}

/* =========================
   COUPON & DELIVERY CALCULATION
========================= */
$delivery = 0;
if ($total < 1000) {
    $delivery = 100;
}

$discount = 0;
$coupon_code = $_SESSION['coupon'] ?? '';
if (!empty($coupon_code)) {
    $cp_res = mysqli_query($conn, "SELECT * FROM coupons WHERE code='" . mysqli_real_escape_string($conn, $coupon_code) . "' AND status='active' LIMIT 1");
    if ($cp_res && $cp = mysqli_fetch_assoc($cp_res)) {
        if ($cp['discount_type'] === 'percentage') {
            $discount = $total * ($cp['discount_value'] / 100);
        } else {
            $discount = $cp['discount_value'];
        }
    }
}

$final_total = $total - $discount + $delivery;
if ($final_total < 0) {
    $final_total = 0;
}

/* =========================
   SAVE ADDRESS OPTION
========================= */
if (isset($_POST['save_new_address']) && $_POST['save_new_address'] == '1') {
    $check_dup = mysqli_query($conn, "SELECT id FROM addresses WHERE user_id=$user_id AND customer_name='$name' AND address='$address'");
    if (mysqli_num_rows($check_dup) == 0) {
        $check_first = mysqli_query($conn, "SELECT id FROM addresses WHERE user_id=$user_id");
        $is_default = (mysqli_num_rows($check_first) == 0) ? 1 : 0;
        mysqli_query($conn, "INSERT INTO addresses (user_id, customer_name, address, phone, pincode, state, city, landmark, address_type, is_default)
                             VALUES ($user_id, '$name', '$address', '$phone', '$pincode', '$state', '$city', '$landmark', '$type', $is_default)");
    }
}

/* =========================
   INSERT ORDER
========================= */

mysqli_query($conn, "INSERT INTO orders
(
customer_name,
email,
address,
phone,
pincode,
state,
city,
landmark,
address_type,
total_amount,
payment_method,
status,
order_status,
user_id
)

VALUES
(
'$name',
'$email',
'$address',
'$phone',
'$pincode',
'$state',
'$city',
'$landmark',
'$type',
'$final_total',
'$payment',
'pending',
'Pending',
'$user_id'
)
");

$order_id = mysqli_insert_id($conn);

/* =========================
   INSERT ITEMS + STOCK UPDATE
========================= */

foreach ($_SESSION['cart'] as $id => $qty) {

    $id = (int)$id;
    $qty = (int)$qty;

    $res = mysqli_query($conn,
    "SELECT * FROM products WHERE id='$id'");

    $row = mysqli_fetch_assoc($res);

    $price = $row['price'];

    $pname = mysqli_real_escape_string($conn,
    $row['name']);

    mysqli_query($conn, "INSERT INTO order_items
    (order_id, product_id, quantity, price, product_name)
    VALUES
    ('$order_id','$id','$qty','$price','$pname')
    ");

    mysqli_query($conn, "UPDATE products
    SET stock = stock - $qty
    WHERE id='$id'");
}

/* =========================
   SEND EMAILS
========================= */

include_once '../../config/mailer.php';

// Send confirmation email to customer
send_nursery_email($email, 'placed', [
    '{user_name}' => $name,
    '{order_id}' => $order_id,
    '{order_total}' => $final_total
]);

// Send new order alert email to owner
$settings_q = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key='smtp_from_email' LIMIT 1");
$settings_r = mysqli_fetch_assoc($settings_q);
$owner_email = $settings_r['setting_value'] ?? 'vireshmmustapure39@gmail.com';

// Build simple HTML layout listing products in cart for owner
$cart_items_html = "";
foreach ($_SESSION['cart'] as $pid => $qty) {
    $p_res = mysqli_query($conn, "SELECT name, price FROM products WHERE id=" . intval($pid));
    if ($p_row = mysqli_fetch_assoc($p_res)) {
        $cart_items_html .= "• " . htmlspecialchars($p_row['name']) . " x " . $qty . " (₹" . ($p_row['price'] * $qty) . ")<br>";
    }
}

send_nursery_email($owner_email, 'placed', [
    '{user_name}' => 'Admin',
    '{order_id}' => $order_id . " (Admin Notice - New Order)",
    '{order_total}' => $final_total . " Details:<br>" . $cart_items_html
]);

/* =========================
   CLEAR CART & COUPON
========================= */

unset($_SESSION['cart']);
unset($_SESSION['coupon']);

?>

<!DOCTYPE html>
<html>

<head>
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1LSB0KPW3Z"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-1LSB0KPW3Z');
</script>

<title>Order Success</title>

<meta name="viewport"
content="width=device-width, initial-scale=1">

<style>

body{
    margin:0;
    padding:0;
    font-family:Arial,sans-serif;
    background:linear-gradient(135deg,#e8f5e9,#ffffff);
}

.box{
    width:90%;
    max-width:550px;
    margin:70px auto;
    background:white;
    padding:40px;
    border-radius:18px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    text-align:center;
}

.tick{
    width:90px;
    height:90px;
    line-height:90px;
    margin:auto;
    border-radius:50%;
    background:#2e7d32;
    color:white;
    font-size:50px;
    font-weight:bold;
}

h1{
    color:#1b5e20;
    margin-top:20px;
}

p{
    color:#555;
    font-size:17px;
    line-height:1.7;
}

.order-id{
    background:#f1f8e9;
    padding:12px;
    border-radius:10px;
    margin:20px 0;
    font-weight:bold;
    color:#2e7d32;
}

.btn{
    display:inline-block;
    margin-top:15px;
    padding:14px 28px;
    background:#2e7d32;
    color:white;
    text-decoration:none;
    border-radius:10px;
    font-weight:bold;
}

.btn:hover{
    background:#1b5e20;
}

</style>

</head>

<body>

<div class="box">

<div class="tick">✓</div>

<h1>Order Placed Successfully!</h1>

<div class="order-id">
Order ID: #<?php echo $order_id; ?>
</div>

<p>
Payment Method:
<b><?php echo $payment; ?></b>
</p>

<p>
Thank you for shopping with us 🌱<br>
Your plants will be delivered soon.
</p>

<a href="../shop/index.php" class="btn">
Continue Shopping
</a>

</div>

</body>
</html>