<?php
include '../../config.php';
include '../../config/mailer.php';

session_start();

if(!isset($_SESSION['admin'])){
    header("Location: ../login/index.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);

if($id <= 0){
    die("Invalid Order ID");
}

/* UPDATE STATUS + SEND MAIL */

if(isset($_GET['status'])){

    $new_status = $_GET['status'];

    mysqli_query($conn,
    "UPDATE orders
    SET order_status='$new_status'
    WHERE id='$id'");

    /* GET ORDER DETAILS */

    $orderData = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM orders WHERE id='$id'"));

    if(!$orderData){
        die("Order not found");
    }

    $customer_name  = $orderData['customer_name'];
    $customer_email = $orderData['email'];

    // Map status to template key
    $tpl_key = '';
    if($new_status == "Confirmed"){
        $tpl_key = 'placed';
    }
    elseif($new_status == "Packed"){
        $tpl_key = 'packed';
    }
    elseif($new_status == "Shipped" || $new_status == "Out For Delivery"){
        $tpl_key = 'shipped';
    }
    elseif($new_status == "Delivered"){
        $tpl_key = 'delivered';
    }

    if ($tpl_key !== '') {
        $variables = [
            '{user_name}' => $customer_name,
            '{order_id}' => $id,
            '{order_total}' => $orderData['total_amount']
        ];
        send_nursery_email($customer_email, $tpl_key, $variables);
    }

    header("Location: ../view-order/index.php?id=$id");
    exit();
}

/* GET ORDER */

$order = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT * FROM orders WHERE id='$id'"));

if(!$order){
    die("Order not found");
}

$current = $order['order_status'];

?>

<!DOCTYPE html>
<html>

<head>
<title>Order Control</title>

<style>

body{
    font-family:Arial;
    background:#f4f6f9;
}

.box{
    width:500px;
    margin:40px auto;
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
}

.back{
    display:inline-block;
    background:#444;
    color:white;
    padding:8px 12px;
    text-decoration:none;
    border-radius:5px;
    margin-bottom:20px;
}

.status{
    background:#fff3cd;
    padding:15px;
    border-radius:8px;
    margin-bottom:25px;
    font-size:18px;
}

.btn{
    display:flex;
    justify-content:space-between;
    align-items:center;
    text-decoration:none;
    padding:18px;
    border-radius:10px;
    margin-bottom:15px;
    font-size:18px;
    font-weight:bold;
}

.confirm{
    background:#eaf8ee;
    border:1px solid #28a745;
    color:#28a745;
}

.packed{
    background:#fff3cd;
    border:1px solid #ff9800;
    color:#ff9800;
}

.dispatch{
    background:#eef5ff;
    border:1px solid #007bff;
    color:#007bff;
}

.out{
    background:#ede7f6;
    border:1px solid #673ab7;
    color:#673ab7;
}

.deliver{
    background:#eaf8ee;
    border:1px solid #00c851;
    color:#00c851;
}

.rate{
    background:#fff8e1;
    border:1px solid #fbc02d;
    color:#f57f17;
}

</style>

</head>

<body>

<?php include __DIR__ . '/../sidebar.php'; ?>

<div class="main">
<div class="box">

<a class="back"
href="../view-order/index.php?id=<?php echo $id; ?>">

⬅ Back

</a>

<h2>Order Control - Order #<?php echo $id; ?></h2>

<div class="status">

Current Status:
<b><?php echo $current; ?></b>

</div>

<a class="btn confirm"
href="../order_control/index.php?id=<?php echo $id; ?>&status=Confirmed">

✅ Confirm Order
➜

</a>

<a class="btn packed"
href="../order_control/index.php?id=<?php echo $id; ?>&status=Packed">

📦 Mark Packed
➜

</a>

<a class="btn dispatch"
href="../order_control/index.php?id=<?php echo $id; ?>&status=Shipped">

🚚 Mark Shipped
➜

</a>

<a class="btn out"
href="../order_control/index.php?id=<?php echo $id; ?>&status=Out For Delivery">

🛵 Out For Delivery
➜

</a>

<a class="btn deliver"
href="../order_control/index.php?id=<?php echo $id; ?>&status=Delivered">

✅ Mark Delivered
➜

</a>

<?php if($current == "Confirmed"){ ?>

<a class="btn packed"
href="../order_control/index.php?id=<?php echo $id; ?>&status=Packed">

📦 Mark Packed
➜

</a>

<?php } ?>

<?php if($current == "Packed"){ ?>

<a class="btn dispatch"
href="../order_control/index.php?id=<?php echo $id; ?>&status=Shipped">

🚚 Mark Shipped
➜

</a>

<?php } ?>

<?php if($current == "Shipped"){ ?>

<a class="btn out"
href="../order_control/index.php?id=<?php echo $id; ?>&status=Out For Delivery">

🛵 Out For Delivery
➜

</a>

<?php } ?>

<?php if($current == "Out For Delivery"){ ?>

<a class="btn deliver"
href="../order_control/index.php?id=<?php echo $id; ?>&status=Delivered">

✅ Mark Delivered
➜

</a>

<?php } ?>

<?php if($current == "Delivered"){ ?>

<a class="btn rate" href="#">

⭐ Delivered Successfully
✓

</a>

<?php } ?>

</div>
</div>

</body>
</html>