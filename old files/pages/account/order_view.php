<?php
session_start();
include '../../config/db.php';

if(!isset($_SESSION['user'])){
    header("Location: ../login.php");
    exit();
}


$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user'];

$order_query = mysqli_query($conn,"
SELECT * FROM orders
WHERE id='$order_id'
AND user_id='$user_id'
");

if(mysqli_num_rows($order_query) == 0){
    die("Order not found");
}

$order = mysqli_fetch_assoc($order_query);

$current = trim($order['order_status']);

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

function activeStep($step, $current){

    $steps = [
        'Pending' => 1,
        'Confirmed' => 2,
        'Packed' => 3,
        'Shipped' => 4,
        'Out For Delivery' => 5,
        'Delivered' => 6
    ];

    return $steps[$step] <= $steps[$current];
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Order Details</title>

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
    max-width:1100px;
    margin:20px auto;
}

.card{
    background:#fff;
    border-radius:10px;
    padding:25px;
    margin-bottom:20px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

.title{
    font-size:28px;
    font-weight:bold;
}

.top-status{
    margin-top:10px;
    font-size:20px;
    color:green;
    font-weight:bold;
}

.product{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
    align-items:center;
}

.product img{
    width:140px;
    height:140px;
    object-fit:cover;
    border-radius:10px;
    border:1px solid #ddd;
}

.product-name{
    font-size:24px;
    font-weight:bold;
}

.price{
    margin-top:10px;
    font-size:20px;
    font-weight:bold;
}

.qty{
    margin-top:8px;
    color:#555;
}

.timeline{
    margin-top:30px;
    position:relative;
    padding-left:40px;
}

.timeline::before{
    content:'';
    position:absolute;
    left:13px;
    top:0;
    width:4px;
    height:100%;
    background:#4caf50;
}

.step{
    position:relative;
    margin-bottom:40px;
}

.circle{
    width:28px;
    height:28px;
    border-radius:50%;
    background:#ccc;
    position:absolute;
    left:-40px;
    top:0;
    border:3px solid #ccc;
}

.active .circle{
    background:#4caf50;
    border-color:#4caf50;
}
.step-title{
    font-size:20px;
    font-weight:bold;
}

.active .step-title{
    color:green;
}

.address{
    line-height:1.8;
    color:#555;
    font-size:17px;
}

@media(max-width:768px){

    .product{
        flex-direction:column;
        align-items:flex-start;
    }

    .product img{
        width:100%;
        height:240px;
    }

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="title">
Order #<?php echo $order['id']; ?>
</div>

<div class="top-status">
Current Status:
<?php echo $order['order_status']; ?>
</div>

</div>

<?php while($item = mysqli_fetch_assoc($items)){ ?>

<div class="card">

<div class="product">

<img src="../../uploads/<?php echo $item['image']; ?>">

<div>

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

</div>

</div>

</div>

<?php } ?>

<div class="card">

<h2>Delivery Tracking</h2>

<div class="timeline">

<div class="step <?php if(activeStep('Pending',$current)) echo 'active'; ?>">
<div class="circle"></div>
<div class="step-title">Order Placed</div>
</div>

<div class="step <?php if(activeStep('Confirmed',$current)) echo 'active'; ?>">
<div class="circle"></div>
<div class="step-title">Confirmed</div>
</div>

<div class="step <?php if(activeStep('Packed',$current)) echo 'active'; ?>">
<div class="circle"></div>
<div class="step-title">Packed</div>
</div>

<div class="step <?php if(activeStep('Shipped',$current)) echo 'active'; ?>">
<div class="circle"></div>
<div class="step-title">Shipped</div>
</div>

<div class="step <?php if(activeStep('Out For Delivery',$current)) echo 'active'; ?>">
<div class="circle"></div>
<div class="step-title">Out For Delivery</div>
</div>

<div class="step <?php if(activeStep('Delivered',$current)) echo 'active'; ?>">
<div class="circle"></div>
<div class="step-title">Delivered</div>
</div>

</div>

</div>

<div class="card">

<h2>Delivery Address</h2>

<div class="address">

<?php echo $order['customer_name']; ?><br>

<?php echo $order['address']; ?><br>

<?php echo $order['city']; ?>,
<?php echo $order['state']; ?> -
<?php echo $order['pincode']; ?><br>

Phone:
<?php echo $order['phone']; ?>

</div>

</div>

</div>

</body>
</html>