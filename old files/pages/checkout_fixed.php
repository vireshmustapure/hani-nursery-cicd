<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])){
    die("Cart empty");
}

$total = 0;

/* STOCK CHECK */
foreach($_SESSION['cart'] as $id => $qty){

$id = (int)$id;
$qty = (int)$qty;

$res = mysqli_query($conn,"SELECT * FROM products WHERE id=$id");
$row = mysqli_fetch_assoc($res);

if(!$row){
    die("Product missing");
}

if($row['stock'] < $qty){
    die($row['name']." only ".$row['stock']." left");
}

$total += $row['price'] * $qty;
}

/* INSERT ORDER */
mysqli_query($conn,"INSERT INTO orders
(customer_name,total_amount,status)
VALUES
('Customer','$total','pending')
");

$order_id = mysqli_insert_id($conn);

/* INSERT ITEMS + STOCK UPDATE */
foreach($_SESSION['cart'] as $id => $qty){

$id = (int)$id;
$qty = (int)$qty;

$res = mysqli_query($conn,"SELECT * FROM products WHERE id=$id");
$row = mysqli_fetch_assoc($res);

/* ORDER ITEMS */
mysqli_query($conn,"INSERT INTO order_items
(order_id,product_id,quantity,price,product_name)
VALUES
('$order_id','$id','$qty','".$row['price']."','".$row['name']."')
");

/* SAFE STOCK UPDATE */
mysqli_query($conn,"UPDATE products 
SET stock = stock - $qty 
WHERE id=$id AND stock >= $qty");

}

/* CLEAR CART */
unset($_SESSION['cart']);
?>

<h2>Order Placed Successfully ✔</h2>
<p>Order ID: <?php echo $order_id; ?></p>

<a href="shop.php">Continue Shopping</a>