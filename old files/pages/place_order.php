<?php
session_start();
include('../config/db.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../src/Exception.php';
require '../src/PHPMailer.php';
require '../src/SMTP.php';

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
'$total',
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

try{

    /* CUSTOMER MAIL */

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    /* YOUR GMAIL */
    $mail->Username   = 'kallurviresh39@gmail.com';

    /* APP PASSWORD */
    $mail->Password   = 'znamlbvnmkiysvkb';

    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('kallurviresh39@gmail.com', 'Hani Nursery');
    $mail->setFrom('maheshhbd439@gmail.com', 'mahesh');
   
    $mail->addAddress($email, $name);

    $mail->isHTML(true);

    $mail->Subject = "🌿 Order Placed Successfully";

    $mail->Body = "

    <div style='font-family:Arial;padding:25px;'>

        <h2 style='color:#2e7d32;'>
        Order Confirmed ✅
        </h2>

        <p>Hello <b>$name</b>,</p>

        <p>
        Your order <b>#$order_id</b>
        has been placed successfully.
        </p>

        <p>
        Total Amount: <b>₹$total</b>
        </p>

        <p>
        We will deliver your plants soon 🌱
        </p>

        <br>

        <p>
        Thank you for shopping with us.
        </p>

    </div>

    ";

    $mail->send();

    /* OWNER MAIL */

    $ownerMail = new PHPMailer(true);

    $ownerMail->isSMTP();
    $ownerMail->Host       = 'smtp.gmail.com';
    $ownerMail->SMTPAuth   = true;

    $ownerMail->Username   = 'yourgmail@gmail.com';
    $ownerMail->Password   = 'your_app_password';

    $ownerMail->SMTPSecure = 'tls';
    $ownerMail->Port       = 587;

    $ownerMail->setFrom('yourgmail@gmail.com', 'Hani Nursery');

    /* OWNER EMAIL */
    $ownerMail->addAddress('owner@gmail.com', 'Nursery Owner');

    $ownerMail->isHTML(true);

    $ownerMail->Subject = "🛒 New Order Received";

    $ownerMail->Body = "

    <div style='font-family:Arial;padding:25px;'>

        <h2 style='color:#1b5e20;'>
        New Order Received 🌿
        </h2>

        <hr>

        <p><b>Order ID:</b> #$order_id</p>

        <p><b>Customer:</b> $name</p>

        <p><b>Email:</b> $email</p>

        <p><b>Phone:</b> $phone</p>

        <p><b>Total:</b> ₹$total</p>

        <p><b>Payment:</b> $payment</p>

    </div>

    ";

    $ownerMail->send();

}catch(Exception $e){

}

/* =========================
   CLEAR CART
========================= */

unset($_SESSION['cart']);

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

<a href="shop.php" class="btn">
Continue Shopping
</a>

</div>

</body>
</html>