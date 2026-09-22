<?php
session_start();
include '../../../config/db.php';

if(!isset($_SESSION['user'])){
    header("Location: ../../login/index.php");
    exit();
}

$user_id = intval($_SESSION['user']);

// Handle remove item from wishlist (self-contained GET action)
if (isset($_GET['remove_id'])) {
    $remove_id = intval($_GET['remove_id']);
    mysqli_query($conn, "DELETE FROM wishlist WHERE user_id=$user_id AND product_id=$remove_id");
    header("Location: index.php");
    exit();
}

include '../../../includes/header.php';

// Fetch user's wishlist joined with products details
$sql = "SELECT wishlist.product_id, products.name, products.price, products.mrp, products.discount_percent, products.image, products.category 
        FROM wishlist 
        INNER JOIN products ON wishlist.product_id = products.id 
        WHERE wishlist.user_id = $user_id 
        ORDER BY wishlist.created_at DESC";
$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>My Wishlist - Hani Nursery</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
.wishlist-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px 60px;
}
.wishlist-header {
    text-align: center;
    margin-bottom: 30px;
}
.wishlist-header h2 {
    font-size: 32px;
}
.empty-wishlist {
    text-align: center;
    background: white;
    padding: 50px;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    border: 1px solid #eee;
}
.remove-wishlist-btn {
    background: #ffebee;
    color: #c62828;
    border: none;
    padding: 8px 14px;
    border-radius: var(--radius-sm);
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition-smooth);
}
.remove-wishlist-btn:hover {
    background: #ffcdd2;
}
</style>
</head>
<body>

<div class="wishlist-container">
    <div class="wishlist-header">
        <h2 class="serif-font" style="display:flex; align-items:center; justify-content:center; gap:10px;"><i data-lucide="heart" style="width:28px; height:28px; fill:var(--accent-red); stroke:var(--accent-red);"></i> My Plant Wishlist</h2>
        <p>Your favorite green selections saved for your garden</p>
    </div>

    <?php if (mysqli_num_rows($res) > 0) { ?>
        <div class="product-grid">
            <?php while ($row = $res->fetch_assoc()) { ?>
                <div class="product-card">
                    <div class="product-image-container">
                        <a href="../../product/index.php?id=<?php echo $row['product_id']; ?>">
                            <img src="../../../uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                        </a>
                    </div>
                    
                    <div class="product-details-box">
                        <div class="product-category-tag"><?php echo htmlspecialchars($row['category']); ?></div>
                        <a href="../../product/index.php?id=<?php echo $row['product_id']; ?>" class="product-title"><?php echo htmlspecialchars($row['name']); ?></a>
                        
                        <div class="product-footer" style="margin-top: 10px; padding-top: 10px;">
                            <div class="product-price" style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                <?php if (!empty($row['mrp']) && $row['mrp'] > $row['price']) { ?>
                                    <span style="font-weight: 800; color: var(--primary-color);">₹<?php echo $row['price']; ?></span>
                                    <span style="text-decoration: line-through; color: #aaa; font-size: 12px; font-weight: 500;">₹<?php echo $row['mrp']; ?></span>
                                    <span style="background: #ffebee; color: #c62828; padding: 2px 5px; border-radius: 3px; font-size: 10px; font-weight: 700;"><?php echo $row['discount_percent']; ?>% OFF</span>
                                <?php } else { ?>
                                    <span>₹<?php echo $row['price']; ?></span>
                                <?php } ?>
                            </div>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <a href="?remove_id=<?php echo $row['product_id']; ?>" class="remove-wishlist-btn" style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px;" onclick="return confirm('Remove this plant from your wishlist?')">
                                    <i data-lucide="trash-2" style="width:16px; height:16px;"></i>
                                </a>
                                <div class="cart-control-wrapper" data-id="<?php echo $row['product_id']; ?>" data-name="<?php echo htmlspecialchars($row['name']); ?>" data-image="../../../uploads/<?php echo $row['image']; ?>" data-stock="10"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="empty-wishlist">
            <h3 style="margin-bottom: 15px; color: var(--primary-dark);">Your wishlist is empty</h3>
            <p style="margin-bottom: 25px; color: var(--text-muted); display:flex; align-items:center; justify-content:center; gap:6px;">Explore our shop to add plants to your wishlist! <i data-lucide="sprout" style="width:18px; height:18px;"></i></p>
            <a href="../../shop/" class="btn-premium" style="text-shadow:none;">Browse Shop</a>
        </div>
    <?php } ?>
</div>

<!-- Added to cart toast -->
<div id="toast-premium">
    <i data-lucide="check" style="width:20px; height:20px; color: var(--primary-light);"></i>
    <img id="toast-premium-img" src="" alt="Cart Product">
    <div id="toast-premium-text"></div>
</div>

<?php include '../../../includes/footer.php'; ?>

<script>
// AJAX Add to Cart from Wishlist
function addToCartWishlist(btn, name, img) {
    let form = btn.closest("form");
    let product_id = form.querySelector(".pid").value;
    let qty = form.querySelector(".qty").value;

    let toast = document.getElementById("toast-premium");
    let toastImg = document.getElementById("toast-premium-img");
    let toastText = document.getElementById("toast-premium-text");
    let cartCount = document.getElementById("cart-count");

    toastImg.src = img;
    toastText.innerText = name + " added to cart";
    
    toast.classList.add("show");

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../../cart/index.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if(this.status === 200 && this.responseText) {
            if(cartCount) {
                cartCount.innerText = this.responseText;
            }
        }
    };

    xhr.send("product_id=" + product_id + "&qty=" + qty);

    setTimeout(() => {
        toast.classList.remove("show");
    }, 2000);
}
</script>

</body>
</html>