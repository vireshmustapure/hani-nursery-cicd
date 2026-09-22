<?php
include '../../config.php';

session_start();

if(!isset($_SESSION['admin'])){
    header("Location: ../login/index.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);

$order = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT * FROM orders WHERE id='$id'"));

if(!$order){
    die("Order not found");
}

$status = $order['order_status'];

/* UPDATED QUERY WITH PRODUCT NAME + IMAGE */

$items = mysqli_query($conn,
"SELECT order_items.*, products.name, products.image
FROM order_items
LEFT JOIN products
ON order_items.product_id = products.id
WHERE order_items.order_id='$id'");
?>

<!DOCTYPE html>
<html>

<head>
<title>Order Details</title>

<style>

body{
    font-family:Arial;
    background:#f4f6f9;
}

.container{
    width:85%;
    margin:20px auto;
    background:white;
    padding:20px;
    border-radius:10px;
}

/* BACK BUTTON */
.back{
    display:inline-block;
    padding:8px 12px;
    background:#444;
    color:white;
    text-decoration:none;
    border-radius:5px;
    margin-bottom:10px;
}

/* BOX */
.box{
    padding:10px;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:12px;
    border-bottom:1px solid #ddd;
    text-align:center;
}

th{
    background:#2e7d32;
    color:white;
}

/* STATUS */
.status{
    padding:5px 12px;
    border-radius:20px;
    font-weight:bold;
    color:white;
    font-size:14px;
}

/* ORDER CONTROL BUTTON */
.control-btn{
    background:#28a745;
    color:white;
    padding:10px 15px;
    border-radius:6px;
    text-decoration:none;
    font-weight:bold;
    margin-left:15px;
}

.top-flex{
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:15px;
}

/* PRODUCT */
.product-box{
    display:flex;
    align-items:center;
    gap:12px;
}

.product-img{
    width:70px;
    height:70px;
    object-fit:cover;
    border-radius:8px;
    border:1px solid #ddd;
}

.product-name{
    font-weight:bold;
    text-align:left;
}

</style>

</head>

<body>

<?php include __DIR__ . '/../sidebar.php'; ?>

<div class="main">
<div class="container">

<!-- BACK -->
<a class="back" href="../orders/index.php">⬅ Back</a>

<div class="box">

<h2>📦 Order #<?php echo $id; ?></h2>

<p><b>Total:</b> ₹<?php echo $order['total_amount']; ?></p>

<div class="top-flex">

<p>

<b>Status:</b>

<?php
$bg = "#999";

if($status == 'Pending'){
    $bg = "#ffcc00";
}
elseif($status == 'Confirmed'){
    $bg = "#28a745";
}
elseif($status == 'Packed'){
    $bg = "#ff9800";
}
elseif($status == 'Shipped'){
    $bg = "#2196f3";
}
elseif($status == 'Out For Delivery'){
    $bg = "#673ab7";
}
elseif($status == 'Delivered'){
    $bg = "#00c851";
}
?>

<span class="status"
style="background:<?php echo $bg; ?>">

<?php echo $status; ?>

</span>

</p>

<a class="control-btn"
href="../order_control/index.php?id=<?php echo $id; ?>">

⚙ Order Control

</a>

</div>

<hr>

<h3>👤 Customer Details</h3>

<p>
<?php echo $order['customer_name']; ?>
|
<?php echo $order['phone']; ?>
</p>

<h3>📍 Address</h3>

<p>
<?php echo $order['address']; ?>,
<?php echo $order['city']; ?> -
<?php echo $order['pincode']; ?>
</p>

<p>
<b>Payment:</b>
<?php echo $order['payment_method']; ?>
</p>

<hr>

<h3>🛒 Products</h3>

<table>

<tr>
<th>Plant</th>
<th>Price</th>
<th>Qty</th>
<th>Total</th>
</tr>

<?php while($row = mysqli_fetch_assoc($items)){ ?>

<tr>

<td>

<div class="product-box">

<img
src="../../uploads/<?php echo $row['image']; ?>"
class="product-img">

<div class="product-name">
<?php echo $row['name']; ?>
</div>

</div>

</td>

<td>
₹<?php echo $row['price']; ?>
</td>

<td>
<?php echo $row['quantity']; ?>
</td>

<td>
₹<?php echo $row['price'] * $row['quantity']; ?>
</td>

</tr>

<?php } ?>

</table>

</div>

</div>
</div>

</body>
</html>