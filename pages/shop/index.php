<?php
session_start();
include '../../config/db.php';
include '../../includes/header.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "SELECT * FROM products WHERE 1";

if(!empty($search)){
    $search_esc = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (name LIKE '%$search_esc%' OR description LIKE '%$search_esc%')";
}

if(!empty($category)){
    $cat_esc = mysqli_real_escape_string($conn, $category);
    $sql .= " AND category='$cat_esc'";
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
    
    <title>Shop Plants - Hani Nursery</title>
    
    <style>
    .back-btn {
        padding: 30px 40px 10px;
    }
    
    .shop-title-box {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .shop-title-box h2 {
        font-size: 36px;
        margin-bottom: 10px;
    }
    
    .shop-title-box p {
        color: var(--text-muted);
    }
    
    .shop-grid-container {
        max-width: 1200px;
        margin: auto;
        padding: 0 20px 60px;
    }
    </style>
</head>
<body>

<div class="back-btn">
    <a href="../../index.php" class="btn-premium" style="padding: 8px 20px; font-size: 13px; text-shadow: none; box-shadow: var(--shadow-sm); display:inline-flex; align-items:center; gap:6px;">
        <i data-lucide="arrow-left" style="width:14px; height:14px;"></i> Back to Home
    </a>
</div>

<div class="shop-title-box">
    <h2 class="serif-font">Our Plant Collection</h2>
    <p>Discover healthy, handpicked greens cultivated for growth and aesthetics</p>
</div>

<div class="shop-filter-bar" style="max-width: 1200px; margin: 0 auto 30px; padding: 0 20px;">
    <form method="GET" action="" style="display: flex; flex-wrap: wrap; gap: 15px; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); padding: 20px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid rgba(46, 125, 50, 0.1); align-items: center; justify-content: space-between;">
        
        <!-- Search Field -->
        <div style="flex: 1; min-width: 280px; position: relative;">
            <input type="text" name="search" placeholder="Search plants..." value="<?php echo htmlspecialchars($search); ?>" style="width: 100%; padding: 12px 20px; border-radius: var(--radius-sm); border: 1px solid #ddd; outline: none; font-size: 14px; font-weight: 500; transition: var(--transition-smooth);">
        </div>

        <!-- Category Dropdown -->
        <div style="min-width: 200px;">
            <select name="category" style="width: 100%; padding: 12px 20px; border-radius: var(--radius-sm); border: 1px solid #ddd; outline: none; font-size: 14px; font-weight: 500; cursor: pointer; transition: var(--transition-smooth); background-color: white;">
                <option value="">All Categories</option>
                <?php
                $cat_query = mysqli_query($conn, "SELECT DISTINCT category FROM products WHERE category != ''");
                if ($cat_query) {
                    while ($cat_row = mysqli_fetch_assoc($cat_query)) {
                        $selected = ($category === $cat_row['category']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($cat_row['category']) . "' $selected>" . htmlspecialchars($cat_row['category']) . "</option>";
                    }
                }
                ?>
            </select>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 10px; min-width: 200px;">
            <button type="submit" class="btn-premium" style="padding: 12px 24px; box-shadow: none; font-size: 14px; flex: 1; display:inline-flex; align-items:center; justify-content:center; gap:6px;">
                Filter <i data-lucide="search" style="width:16px; height:16px;"></i>
            </button>
            <?php if (!empty($search) || !empty($category)) { ?>
                <a href="index.php" class="btn-premium" style="padding: 12px 24px; box-shadow: none; font-size: 14px; background: var(--accent-red); color: white !important; text-decoration: none; text-align: center; text-shadow: none; flex: 1; display:inline-flex; align-items:center; justify-content:center; gap:6px;">
                    Clear <i data-lucide="x" style="width:16px; height:16px;"></i>
                </a>
            <?php } ?>
        </div>
    </form>
</div>

<div class="shop-grid-container">
    <div class="product-grid">

    <?php
    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            $pid = $row['id'];
            
            // Count average rating
            $rating_res = mysqli_query($conn, "SELECT AVG(rating) as avg, COUNT(*) as cnt FROM reviews WHERE product_id=$pid AND status='approved'");
            $rating_row = mysqli_fetch_assoc($rating_res);
            $avg_rating = round($rating_row['avg'] ?? 5);
            $rating_cnt = $rating_row['cnt'] ?? 0;
            
            // Wishlist Check
            $isLiked = false;
            if (isset($_SESSION['user'])) {
                $uid = $_SESSION['user'];
                $check_like = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id=$uid AND product_id=$pid");
                if (mysqli_num_rows($check_like) > 0) {
                    $isLiked = true;
                }
            }
    ?>

    <div class="product-card">
        <div class="product-image-container">
            <a href="../product/index.php?id=<?php echo $row['id']; ?>">
                <img src="../../uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
            </a>
            
            <!-- Wishlist Heart Toggle -->
            <button class="wishlist-heart-btn <?php echo $isLiked ? 'liked' : ''; ?>" onclick="toggleWishlist(this, <?php echo $row['id']; ?>)">
                <i data-lucide="heart" class="heart-icon-svg" style="width: 20px; height: 20px; fill: <?php echo $isLiked ? 'var(--accent-red)' : 'none'; ?>; stroke: <?php echo $isLiked ? 'var(--accent-red)' : 'currentColor'; ?>;"></i>
            </button>
        </div>
        
        <div class="product-details-box">
            <div class="product-category-tag"><?php echo htmlspecialchars($row['category']); ?></div>
            <a href="../product/index.php?id=<?php echo $row['id']; ?>" class="product-title"><?php echo htmlspecialchars($row['name']); ?></a>
            
            <div class="product-rating-box">
                <?php for($star=1; $star<=5; $star++) { ?>
                    <span class="star-icon"><?php echo ($star <= $avg_rating) ? '★' : '☆'; ?></span>
                <?php } ?>
                <span class="rating-count-text">(<?php echo $rating_cnt; ?>)</span>
            </div>
            
            <div class="product-footer">
                <div class="product-price" style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                    <?php if (!empty($row['mrp']) && $row['mrp'] > $row['price']) { ?>
                        <span style="font-weight: 800; color: var(--primary-color);">₹<?php echo $row['price']; ?></span>
                        <span style="text-decoration: line-through; color: #aaa; font-size: 12px; font-weight: 500;">₹<?php echo $row['mrp']; ?></span>
                        <span style="background: #ffebee; color: #c62828; padding: 2px 5px; border-radius: 3px; font-size: 10px; font-weight: 700;"><?php echo $row['discount_percent']; ?>% OFF</span>
                    <?php } else { ?>
                        <span>₹<?php echo $row['price']; ?></span>
                    <?php } ?>
                </div>
                <div class="cart-control-wrapper" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['name']); ?>" data-image="../../uploads/<?php echo $row['image']; ?>" data-stock="<?php echo $row['stock']; ?>"></div>
            </div>
        </div>
    </div>

    <?php
        }
    } else {
        echo "<div style='grid-column: 1/-1; text-align:center; padding: 50px 0;'><h2 style='display:flex; align-items:center; justify-content:center; gap:8px;'><i data-lucide=\"alert-circle\" style='width:24px; height:24px; color:var(--accent-red);'></i> No plants found matching your search.</h2></div>";
    }
    ?>

    </div>
</div>

<!-- Footer -->
<?php include '../../includes/footer.php'; ?>

<script>
// AJAX Add to Cart
function addToCart(btn, name, img){
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
    xhr.open("POST", "../cart/index.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function(){
        if(this.status === 200 && this.responseText){
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

// AJAX Toggle Wishlist
function toggleWishlist(btn, productId) {
    <?php if(!isset($_SESSION['user'])) { ?>
        alert("Please login to add plants to your wishlist! ");
        window.location.href = "../login/";
        return;
    <?php } ?>

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../wishlist_toggle/index.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    xhr.onload = function() {
        if (this.status === 200) {
            let res = JSON.parse(this.responseText);
            if (res.success) {
                let heartIcon = btn.querySelector('.heart-icon-svg');
                if (res.action === 'added') {
                    btn.classList.add('liked');
                    if (heartIcon) {
                        heartIcon.setAttribute('fill', 'var(--accent-red)');
                        heartIcon.setAttribute('stroke', 'var(--accent-red)');
                    }
                } else {
                    btn.classList.remove('liked');
                    if (heartIcon) {
                        heartIcon.setAttribute('fill', 'none');
                        heartIcon.setAttribute('stroke', 'currentColor');
                    }
                }
            }
        }
    };
    xhr.send("product_id=" + productId);
}
</script>

</body>
</html>