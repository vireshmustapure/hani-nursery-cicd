<?php
session_start();
include '../../config/db.php';

if(!isset($_SESSION['user'])){
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user'];

$orders = mysqli_query($conn,"
SELECT * FROM orders
WHERE user_id='$user_id'
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>

<title>My Orders</title>

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<style>

body{
    margin:0;
    padding:0;
    background:#f1f3f6;
    font-family:Arial,sans-serif;
}

.container{
    width:95%;
    max-width:1200px;
    margin:20px auto;
}

.title{
    font-size:28px;
    font-weight:bold;
    margin-bottom:20px;
}

.order-card{
    background:#fff;
    border-radius:10px;
    padding:20px;
    margin-bottom:18px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

.product-row{
    display:flex;
    gap:20px;
    align-items:center;
    flex-wrap:wrap;
}

.product-image{
    width:130px;
    height:130px;
    object-fit:cover;
    border-radius:10px;
    border:1px solid #ddd;
}

.product-details{
    flex:1;
}

.product-name{
    font-size:22px;
    font-weight:bold;
    color:#212121;
}

.price{
    margin-top:8px;
    font-size:20px;
    font-weight:bold;
}

.qty{
    margin-top:5px;
    color:#666;
}

.status{
    margin-top:10px;
    color:green;
    font-weight:bold;
}

.order-date{
    margin-top:8px;
    color:#777;
}

.view-btn{
    display:inline-block;
    margin-top:15px;
    padding:12px 20px;
    background:#2874f0;
    color:white;
    text-decoration:none;
    border-radius:6px;
    font-weight:bold;
}

.view-btn:hover{
    background:#0f5bd3;
}

.empty{
    background:#fff;
    padding:40px;
    border-radius:10px;
    text-align:center;
}

@media(max-width:768px){

    .product-row{
        flex-direction:column;
        align-items:flex-start;
    }

    .product-image{
        width:100%;
        height:240px;
    }

}

</style>

</head>

<body>

<div class="container">

<div class="title">
My Orders
</div>

<?php if(mysqli_num_rows($orders) > 0){ ?>

<?php while($order = mysqli_fetch_assoc($orders)){ ?>

<?php

$order_id = $order['id'];

$items = mysqli_query($conn,"
SELECT 
order_items.*,
products.image,
products.name

FROM order_items

LEFT JOIN products
ON order_items.product_id = products.id

WHERE order_items.order_id='$order_id'
");

?>

<?php while($item = mysqli_fetch_assoc($items)){ ?>

<div class="order-card">

<div class="product-row">

<img
src="../../uploads/<?php echo $item['image']; ?>"
class="product-image">

<div class="product-details">

<div class="product-name">
<?php echo $item['name']; ?>
</div>

<div class="price">
₹<?php echo number_format($item['price']); ?>
</div>

<div class="qty">
Quantity:
<?php echo $item['quantity']; ?>
</div>

<div class="status">
● <?php echo $order['order_status']; ?>
</div>

<div class="order-date">
Ordered on:
<?php echo date("d M Y", strtotime($order['created_at'])); ?>
</div>

<a class="view-btn" href="order_view.php?id=<?php echo $order_id; ?>">
View Details
</a>

</div>

</div>

</div>

<?php } ?>

<?php } ?>

<?php } else { ?>

<div class="empty">

<h2>No Orders Found</h2>

<p>You have not placed any orders yet.</p>

</div>

<?php } ?>

</div>

</body>
</html>