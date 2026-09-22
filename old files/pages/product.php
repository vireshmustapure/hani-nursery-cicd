<?php
session_start();
include '../config/db.php';

/* ADD TO CART + GO CART PAGE */
if(isset($_POST['product_id'])){

    $pid = $_POST['product_id'];
    $qty = $_POST['qty'];

    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = [];
    }

    if(isset($_SESSION['cart'][$pid])){
        $_SESSION['cart'][$pid] += $qty;
    } else {
        $_SESSION['cart'][$pid] = $qty;
    }

    header("Location: cart.php");
    exit();
}

include '../includes/header.php';

/* CHECK PRODUCT ID */
if(!isset($_GET['id'])){
    echo "<h2 style='text-align:center;'>No Product Selected</h2>";
    exit();
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM products WHERE id=$id";
$result = mysqli_query($conn, $sql);

if(!$result){
    die(mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);

if(!$row){
    echo "<h2 style='text-align:center;'>Product not found</h2>";
    exit();
}
?>

<div style="padding:20px 30px;">
    <a href="shop.php" style="
        background:#2e7d32;
        color:white;
        padding:8px 14px;
        text-decoration:none;
        border-radius:6px;
        font-size:14px;
    ">
        ⬅ Back to Shop
    </a>
</div>

<div style="width:60%; margin:auto; text-align:center;">

<img src="../uploads/<?php echo $row['image']; ?>" 
style="width:300px; height:auto; border-radius:10px; object-fit:cover;">

<h2><?php echo $row['name']; ?></h2>

<p><?php echo $row['description']; ?></p>

<h3>₹<?php echo $row['price']; ?></h3>

<form method="POST">
    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
    <input type="hidden" name="qty" value="1">

    <button type="submit" style="
        background:green;
        color:white;
        padding:10px 20px;
        border:none;
        cursor:pointer;
        border-radius:6px;
    ">
        Add to Cart
    </button>
</form>

</div>