<?php
session_start();
include '../config/db.php';
include '../includes/header.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "SELECT * FROM products WHERE 1";

if(!empty($search)){
    $sql .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
}

if(!empty($category)){
    $sql .= " AND category='$category'";
}

$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>

<!-- Google tag -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1LSB0KPW3Z"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-1LSB0KPW3Z');
</script>

<title>Shop</title>

<style>
body {
    font-family: 'Segoe UI', Arial;
    background: #f5fff7;
    margin: 0;
}

/* BACK BUTTON */
.back-btn {
    padding:10px 20px;
}

/* GRID */
.container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 25px;
    padding: 20px 40px;
}

/* PRODUCT CARD */
.product {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: 0.3s;
    text-align: center;
}

.product:hover {
    transform: translateY(-5px);
}

/* IMAGE */
.product img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

/* BUTTON */
.product button {
    background: #2e7d32;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 6px;
    cursor: pointer;
}

.product button:hover {
    background: #1b5e20;
}

/* TOAST POPUP FIXED */
#toast {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.8);
    text-align: center;
    opacity: 0;
    transition: 0.4s ease;
    z-index: 9999;   /* FIXED */
    pointer-events: none;
}

#toast-img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
}

#toast-text {
    margin-top: 10px;
    font-weight: bold;
    color: #2e7d32;
    font-size: 16px;
}

#toast-icon {
    font-size: 28px;
    color: #2e7d32;
}
</style>

</head>
<body>

<div class="back-btn">
    <a href="../index.php" style="
        padding:8px 14px;
        background:#2e7d32;
        color:white;
        text-decoration:none;
        border-radius:6px;
        font-size:14px;
    ">
        ⬅ Back to Home
    </a>
</div>

<h2 style="text-align:center;">Shop Products</h2>

<div class="container">

<?php
if($res->num_rows > 0){
    while($row = $res->fetch_assoc()){
?>

<div class="product">

    <a href="product.php?id=<?php echo $row['id']; ?>">
        <img src="../uploads/<?php echo $row['image']; ?>">
    </a>

    <h3><?php echo $row['name']; ?></h3>

    <p style="color:#2e7d32; font-weight:bold;">
        ₹<?php echo $row['price']; ?>
    </p>

    <form onsubmit="return false;">
        <input type="hidden" class="pid" value="<?php echo $row['id']; ?>">
        <input type="hidden" class="qty" value="1">

        <button type="button"
            onclick="addToCart(this,'<?php echo $row['name']; ?>','../uploads/<?php echo $row['image']; ?>')">
            Add to Cart
        </button>
    </form>

</div>

<?php
    }
} else {
    echo "<h2 style='text-align:center;'>❌ No plants found</h2>";
}
?>

</div>

<!-- TOAST -->
<div id="toast">
    <div id="toast-icon">✔</div>
    <img id="toast-img" src="">
    <div id="toast-text"></div>
</div>

<script>
function addToCart(btn, name, img){

    let form = btn.closest("form");

    let product_id = form.querySelector(".pid").value;
    let qty = form.querySelector(".qty").value;

    let toast = document.getElementById("toast");
    let toastImg = document.getElementById("toast-img");
    let toastText = document.getElementById("toast-text");

    // reset image first (fix refresh issue)
    toastImg.src = "";
    setTimeout(() => {
        toastImg.src = img;
    }, 50);

    toastText.innerText = name + " added to cart";

    toast.style.opacity = "1";
    toast.style.transform = "translate(-50%, -50%) scale(1)";

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "cart.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function(){
        if(this.responseText){
            document.getElementById("cart-count").innerText = this.responseText;
        }
    };

    xhr.send("product_id=" + product_id + "&qty=" + qty);

    setTimeout(() => {
        toast.style.opacity = "0";
        toast.style.transform = "translate(-50%, -50%) scale(0.8)";
    }, 1200);
}
</script>

</body>
</html>