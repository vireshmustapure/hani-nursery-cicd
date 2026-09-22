<?php
session_start();
include '../../config/db.php';

/* GET PRODUCT ID */
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

$product_item = mysqli_fetch_assoc($result);

if(!$product_item){
    echo "<h2 style='text-align:center;'>Product not found</h2>";
    exit();
}

/* RECORD RECENTLY VIEWED PRODUCT IN SESSION */
if(!isset($_SESSION['recently_viewed'])){
    $_SESSION['recently_viewed'] = [];
}
// Remove existing instance of this product ID to re-insert it at the top
if(($key = array_search($id, $_SESSION['recently_viewed'])) !== false) {
    unset($_SESSION['recently_viewed'][$key]);
}
array_unshift($_SESSION['recently_viewed'], $id);
$_SESSION['recently_viewed'] = array_slice($_SESSION['recently_viewed'], 0, 5);


/* ADD REVIEW POST HANDLER */
$review_msg = "";
if(isset($_POST['submit_review'])) {
    $rating = intval($_POST['rating']);
    $review_text = mysqli_real_escape_string($conn, trim($_POST['review_text']));
    
    // Determine user name
    if (isset($_SESSION['username'])) {
        $user_name = $_SESSION['username'];
    } else {
        $user_name = mysqli_real_escape_string($conn, trim($_POST['guest_name'] ?? 'Anonymous'));
    }
    
    if($rating >= 1 && $rating <= 5 && !empty($review_text) && !empty($user_name)) {
        // Insert review as approved by default
        $q_review = "INSERT INTO reviews (product_id, user_name, rating, review_text, status) 
                     VALUES ($id, '$user_name', $rating, '$review_text', 'approved')";
        if(mysqli_query($conn, $q_review)) {
            $review_msg = "Your review has been added! Thank you. 🌟";
        } else {
            $review_msg = "Error submitting review. Please try again.";
        }
    } else {
        $review_msg = "Please fill in all fields.";
    }
}


/* ADD TO CART POST HANDLER (UNCHANGED CORE) */
if(isset($_POST['product_id']) && !isset($_POST['submit_review'])){
    $pid = intval($_POST['product_id']);
    $qty = intval($_POST['qty']);

    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = [];
    }

    if(isset($_SESSION['cart'][$pid])){
        $_SESSION['cart'][$pid] += $qty;
    } else {
        $_SESSION['cart'][$pid] = $qty;
    }

    header("Location: ../cart/index.php");
    exit();
}

include '../../includes/header.php';

// Check if current product is in user's wishlist
$isLiked = false;
if (isset($_SESSION['user'])) {
    $uid = $_SESSION['user'];
    $check_like = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id=$uid AND product_id=$id");
    if (mysqli_num_rows($check_like) > 0) {
        $isLiked = true;
    }
}

// Fetch approved reviews
$reviews_res = mysqli_query($conn, "SELECT * FROM reviews WHERE product_id=$id AND status='approved' ORDER BY created_at DESC");
$reviews_count = mysqli_num_rows($reviews_res);
$rating_res = mysqli_query($conn, "SELECT AVG(rating) as avg FROM reviews WHERE product_id=$id AND status='approved'");
$rating_row = mysqli_fetch_assoc($rating_res);
$avg_rating = round($rating_row['avg'] ?? 5);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($product_item['name']); ?> - Hani Nursery</title>
    <style>
    .product-detail-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
    }
    .back-btn-box {
        margin-bottom: 25px;
    }
    .product-flex-layout {
        display: flex;
        gap: 50px;
        align-items: flex-start;
        background: var(--bg-white);
        padding: 40px;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-md);
    }
    .product-left-img {
        flex: 1;
        max-width: 400px;
        border-radius: var(--radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }
    .product-left-img img {
        width: 100%;
        height: auto;
        object-fit: cover;
        display: block;
    }
    .product-right-details {
        flex: 1.2;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .details-category {
        font-size: 13px;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--primary-light);
        letter-spacing: 1px;
    }
    .details-title {
        font-size: 32px;
        font-weight: 800;
        color: var(--primary-dark);
        margin: 0;
    }
    .details-price {
        font-size: 26px;
        font-weight: 800;
        color: var(--primary-color);
    }
    .details-desc {
        color: var(--text-muted);
        font-size: 15px;
        line-height: 1.8;
    }
    .stock-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: var(--radius-full);
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .stock-in { background: #e8f5e9; color: #2e7d32; }
    .stock-out { background: #ffebee; color: #c62828; }
    
    .actions-row {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 15px;
    }
    
    /* Reviews panel */
    .reviews-section {
        background: var(--bg-white);
        border-radius: var(--radius-md);
        padding: 40px;
        margin-top: 40px;
        box-shadow: var(--shadow-md);
    }
    .reviews-list {
        margin-top: 25px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .review-item {
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 20px;
    }
    .review-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .review-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .reviewer-name {
        font-weight: 700;
        color: var(--text-dark);
    }
    .review-date {
        font-size: 12px;
        color: var(--text-muted);
    }
    .review-comment {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.6;
    }
    
    /* Write review card */
    .write-review-card {
        background: #fcfdfe;
        border: 1px solid rgba(46, 125, 50, 0.1);
        border-radius: var(--radius-md);
        padding: 25px;
        margin-top: 35px;
    }
    .write-review-card h4 {
        color: var(--primary-dark);
        margin-bottom: 15px;
        font-size: 18px;
    }
    .form-group-review {
        margin-bottom: 15px;
    }
    .form-group-review label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
        font-size: 14px;
        color: var(--text-dark);
    }
    .form-group-review input, .form-group-review select, .form-group-review textarea {
        width: 100%;
        padding: 10px;
        border-radius: var(--radius-sm);
        border: 1px solid #ddd;
        outline: none;
    }
    
    @media(max-width: 768px) {
        .product-flex-layout {
            flex-direction: column;
            padding: 20px;
        }
        .product-left-img {
            max-width: 100%;
        }
    }
    </style>
</head>
<body>

<div class="product-detail-container">
    
    <!-- BACK BUTTON -->
    <div class="back-btn-box">
        <a href="../shop/index.php" class="btn-premium" style="padding: 8px 18px; font-size: 13px; text-shadow: none; box-shadow: var(--shadow-sm);">
            ⬅ Back to Shop
        </a>
    </div>
    
    <!-- PRODUCT CORE DETAILS -->
    <div class="product-flex-layout">
        
        <!-- Left: Image Box -->
        <div class="product-left-img">
            <img src="../../uploads/<?php echo $product_item['image']; ?>" alt="<?php echo $product_item['name']; ?>">
        </div>
        
        <!-- Right: Detail Box -->
        <div class="product-right-details">
            <span class="details-category"><?php echo htmlspecialchars($product_item['category']); ?></span>
            <h2 class="details-title serif-font" style="margin-bottom:5px;"><?php echo htmlspecialchars($product_item['name']); ?></h2>
            
            <?php if (!empty($product_item['tags'])) { 
                $tags = explode(',', $product_item['tags']);
            ?>
                <div style="display:flex; flex-wrap:wrap; gap:6px; margin-top:5px; margin-bottom: 15px;">
                    <?php foreach ($tags as $t) { if (empty(trim($t))) continue; ?>
                        <span style="background: #e8f5e9; color: #2e7d32; font-size:12px; font-weight:700; padding:4px 10px; border-radius:12px;">#<?php echo htmlspecialchars(trim($t)); ?></span>
                    <?php } ?>
                </div>
            <?php } ?>
            
            <!-- Rating stars summary -->
            <div class="product-rating-box" style="margin-bottom: 5px;">
                <?php for($star=1; $star<=5; $star++) { ?>
                    <span class="star-icon" style="font-size: 15px;"><?php echo ($star <= $avg_rating) ? '★' : '☆'; ?></span>
                <?php } ?>
                <span class="rating-count-text" style="font-size: 13px;">(<?php echo $reviews_count; ?> reviews)</span>
            </div>

            <!-- Price -->
            <div class="details-price" style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                <?php if (!empty($product_item['mrp']) && $product_item['mrp'] > $product_item['price']) { ?>
                    <span style="font-size: 28px; color: var(--primary-color); font-weight:800;">₹<?php echo $product_item['price']; ?></span>
                    <span style="text-decoration: line-through; color: #aaa; font-size: 18px; font-weight:500;">₹<?php echo $product_item['mrp']; ?></span>
                    <span style="background: #ffebee; color: #c62828; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight:700;"><?php echo $product_item['discount_percent']; ?>% OFF</span>
                <?php } else { ?>
                    <span>₹<?php echo $product_item['price']; ?></span>
                <?php } ?>
            </div>
            
            <!-- Stock status -->
            <div>
                <?php if ($product_item['stock'] > 0) { ?>
                    <span class="stock-badge stock-in">In Stock (<?php echo $product_item['stock']; ?> available)</span>
                <?php } else { ?>
                    <span class="stock-badge stock-out">Out of Stock</span>
                <?php } ?>
            </div>
            
            <!-- Description -->
            <p class="details-desc"><?php echo nl2br(htmlspecialchars($product_item['description'])); ?></p>
            
            <!-- Cart & Wishlist Forms -->
            <div class="actions-row">
                <?php if ($product_item['stock'] > 0) { ?>
                    <div class="cart-control-wrapper" data-product-page="1" data-id="<?php echo $product_item['id']; ?>" data-name="<?php echo htmlspecialchars($product_item['name']); ?>" data-image="../../uploads/<?php echo $product_item['image']; ?>" data-stock="<?php echo $product_item['stock']; ?>"></div>
                <?php } ?>
                
                <!-- Wishlist Heart Toggle Button -->
                <button class="wishlist-heart-btn <?php echo $isLiked ? 'liked' : ''; ?>" 
                        style="position: static; box-shadow: var(--shadow-sm); width: 44px; height: 44px; display:flex; align-items:center; justify-content:center;" 
                        onclick="toggleWishlistProduct(this, <?php echo $product_item['id']; ?>)">
                    <i data-lucide="heart" class="heart-icon-svg" style="width: 22px; height: 22px; fill: <?php echo $isLiked ? 'var(--accent-red)' : 'none'; ?>; stroke: <?php echo $isLiked ? 'var(--accent-red)' : 'currentColor'; ?>;"></i>
                </button>
            </div>
            
        </div>
        
    </div>

    <!-- RECOMMENDATIONS SECTIONS -->
    <?php
    // Section 1: Related Products (sharing tags)
    $related_products = [];
    if (!empty($product_item['tags'])) {
        $tags_arr = array_map('trim', explode(',', $product_item['tags']));
        $tag_likes = [];
        foreach ($tags_arr as $tag) {
            $tag_esc = mysqli_real_escape_string($conn, $tag);
            if (!empty($tag_esc)) {
                $tag_likes[] = "tags LIKE '%$tag_esc%'";
            }
        }
        if (!empty($tag_likes)) {
            $tag_sql = implode(' OR ', $tag_likes);
            $related_q = mysqli_query($conn, "SELECT * FROM products WHERE ($tag_sql) AND id != $id LIMIT 2");
            if ($related_q) {
                while ($r_row = mysqli_fetch_assoc($related_q)) {
                    $related_products[] = $r_row;
                }
            }
        }
    }

    // Fill to 2 with random if not enough
    if (count($related_products) < 2) {
        $needed = 2 - count($related_products);
        $exclude_ids = array_merge([$id], array_column($related_products, 'id'));
        $exclude_str = implode(',', $exclude_ids);
        $fill_q = mysqli_query($conn, "SELECT * FROM products WHERE id NOT IN ($exclude_str) ORDER BY RAND() LIMIT $needed");
        if ($fill_q) {
            while ($r_row = mysqli_fetch_assoc($fill_q)) {
                $related_products[] = $r_row;
            }
        }
    }

    // Section 2: People Also Shopped For (top sold or random)
    $also_shopped = [];
    $also_q = mysqli_query($conn, "SELECT * FROM products WHERE id != $id ORDER BY is_top_sold DESC, RAND() LIMIT 2");
    if ($also_q) {
        while ($a_row = mysqli_fetch_assoc($also_q)) {
            $also_shopped[] = $a_row;
        }
    }
    ?>

    <!-- Related Products -->
    <div class="reviews-section" style="margin-top: 30px;">
        <h3 class="serif-font" style="font-size: 24px; border-bottom: 2px solid #f0f4f1; padding-bottom: 15px; margin-bottom: 20px; display:flex; align-items:center; gap:8px;">
            <i data-lucide="leaf"></i> Related Products
        </h3>
        <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
            <?php foreach ($related_products as $p) { 
                $pid = $p['id'];
                $r_res = mysqli_query($conn, "SELECT AVG(rating) as avg FROM reviews WHERE product_id=$pid AND status='approved'");
                $r_row = mysqli_fetch_assoc($r_res);
                $p_avg = round($r_row['avg'] ?? 5);
            ?>
                <div class="product-card" style="box-shadow:none; border:1px solid #eee;">
                    <div class="product-image-container" style="height:180px;">
                        <a href="index.php?id=<?php echo $p['id']; ?>">
                            <img src="../../uploads/<?php echo $p['image']; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                        </a>
                    </div>
                    <div class="product-details-box" style="padding:15px;">
                        <div class="product-category-tag"><?php echo htmlspecialchars($p['category']); ?></div>
                        <a href="index.php?id=<?php echo $p['id']; ?>" class="product-title" style="font-size:16px;"><?php echo htmlspecialchars($p['name']); ?></a>
                        <div class="product-rating-box">
                            <?php for($star=1; $star<=5; $star++) { ?>
                                <span class="star-icon" style="font-size:12px;"><?php echo ($star <= $p_avg) ? '★' : '☆'; ?></span>
                            <?php } ?>
                        </div>
                        <div class="product-price" style="font-size:18px; margin-top:5px;">₹<?php echo $p['price']; ?></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- People Also Shopped For -->
    <div class="reviews-section" style="margin-top: 30px;">
        <h3 class="serif-font" style="font-size: 24px; border-bottom: 2px solid #f0f4f1; padding-bottom: 15px; margin-bottom: 20px; display:flex; align-items:center; gap:8px;">
            <i data-lucide="sparkles"></i> People Also Shopped For
        </h3>
        <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
            <?php foreach ($also_shopped as $p) { 
                $pid = $p['id'];
                $r_res = mysqli_query($conn, "SELECT AVG(rating) as avg FROM reviews WHERE product_id=$pid AND status='approved'");
                $r_row = mysqli_fetch_assoc($r_res);
                $p_avg = round($r_row['avg'] ?? 5);
            ?>
                <div class="product-card" style="box-shadow:none; border:1px solid #eee;">
                    <div class="product-image-container" style="height:180px;">
                        <a href="index.php?id=<?php echo $p['id']; ?>">
                            <img src="../../uploads/<?php echo $p['image']; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                        </a>
                    </div>
                    <div class="product-details-box" style="padding:15px;">
                        <div class="product-category-tag"><?php echo htmlspecialchars($p['category']); ?></div>
                        <a href="index.php?id=<?php echo $p['id']; ?>" class="product-title" style="font-size:16px;"><?php echo htmlspecialchars($p['name']); ?></a>
                        <div class="product-rating-box">
                            <?php for($star=1; $star<=5; $star++) { ?>
                                <span class="star-icon" style="font-size:12px;"><?php echo ($star <= $p_avg) ? '★' : '☆'; ?></span>
                            <?php } ?>
                        </div>
                        <div class="product-price" style="font-size:18px; margin-top:5px;">₹<?php echo $p['price']; ?></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    
    <!-- RECENTLY VIEWED PRODUCTS SECTION -->
    <?php
    if (!empty($_SESSION['recently_viewed']) && is_array($_SESSION['recently_viewed'])):
        // Exclude the current product ID
        $recent_ids = array_filter(array_map('intval', $_SESSION['recently_viewed']), function($val) use ($id) {
            return $val !== $id;
        });
        if (!empty($recent_ids)):
            $ids_str = implode(',', $recent_ids);
            $recent_res = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids_str) ORDER BY FIELD(id, $ids_str) LIMIT 4");
            if ($recent_res && mysqli_num_rows($recent_res) > 0):
    ?>
        <div class="reviews-section" style="margin-top: 30px;">
            <h3 class="serif-font" style="font-size: 24px; border-bottom: 2px solid #f0f4f1; padding-bottom: 15px; margin-bottom: 20px; display:flex; align-items:center; gap:8px;">
                <i data-lucide="eye"></i> Recently Viewed Products
            </h3>
            <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));">
                <?php 
                while ($p_recent = mysqli_fetch_assoc($recent_res)) {
                    $pid_rec = $p_recent['id'];
                    $r_rec_res = mysqli_query($conn, "SELECT AVG(rating) as avg FROM reviews WHERE product_id=$pid_rec AND status='approved'");
                    $r_rec_row = mysqli_fetch_assoc($r_rec_res);
                    $p_rec_avg = round($r_rec_row['avg'] ?? 5);
                ?>
                    <div class="product-card" style="box-shadow:none; border:1px solid #eee;">
                        <div class="product-image-container" style="height:170px;">
                            <a href="index.php?id=<?php echo $p_recent['id']; ?>">
                                <img src="../../uploads/<?php echo $p_recent['image']; ?>" alt="<?php echo htmlspecialchars($p_recent['name']); ?>">
                            </a>
                        </div>
                        <div class="product-details-box" style="padding:15px;">
                            <div class="product-category-tag"><?php echo htmlspecialchars($p_recent['category']); ?></div>
                            <a href="index.php?id=<?php echo $p_recent['id']; ?>" class="product-title" style="font-size:15px;"><?php echo htmlspecialchars($p_recent['name']); ?></a>
                            <div class="product-rating-box">
                                <?php for($star=1; $star<=5; $star++) { ?>
                                    <span class="star-icon" style="font-size:12px;"><?php echo ($star <= $p_rec_avg) ? '★' : '☆'; ?></span>
                                <?php } ?>
                            </div>
                            <div class="product-price" style="font-size:17px; margin-top:5px;">₹<?php echo $p_recent['price']; ?></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php 
            endif;
        endif;
    endif; 
    ?>

    <!-- REVIEWS SECTION -->
    <div class="reviews-section">
        <h3 class="serif-font" style="font-size: 24px; border-bottom: 2px solid #f0f4f1; padding-bottom: 15px; margin-bottom: 20px; display:flex; align-items:center; gap:8px;">
            <i data-lucide="message-square" style="width:24px; height:24px;"></i> Customer Reviews (<?php echo $reviews_count; ?>)
        </h3>
        
        <?php if ($review_msg != "") { ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 8px; font-weight: bold; margin-bottom: 20px;">
                <?php echo $review_msg; ?>
            </div>
        <?php } ?>
        
        <div class="reviews-list">
            <?php 
            if ($reviews_count > 0) {
                mysqli_data_seek($reviews_res, 0);
                while ($rev = mysqli_fetch_assoc($reviews_res)) {
            ?>
                <div class="review-item">
                    <div class="review-header">
                        <span class="reviewer-name"><?php echo htmlspecialchars($rev['user_name']); ?></span>
                        <span class="review-date"><?php echo date('d M Y', strtotime($rev['created_at'])); ?></span>
                    </div>
                    <div class="product-rating-box" style="margin-bottom: 10px;">
                        <?php for($star=1; $star<=5; $star++) { ?>
                            <span class="star-icon" style="font-size: 12px;"><?php echo ($star <= $rev['rating']) ? '★' : '☆'; ?></span>
                        <?php } ?>
                    </div>
                    <p class="review-comment"><?php echo nl2br(htmlspecialchars($rev['review_text'])); ?></p>
                </div>
            <?php 
                }
            } else {
                echo "<p style='color: #777; font-style: italic; padding: 15px 0;'>No reviews yet. Be the first to share your experience with this plant!</p>";
            } 
            ?>
        </div>
        
        <!-- Write Review Form -->
        <div class="write-review-card">
            <h4 style="display:flex; align-items:center; gap:8px;"><i data-lucide="edit-3" style="width:20px; height:20px;"></i> Leave a Review</h4>
            <form method="POST">
                <input type="hidden" name="submit_review" value="1">
                
                <?php if (!isset($_SESSION['username'])) { ?>
                    <div class="form-group-review">
                        <label for="guest_name">Your Name:</label>
                        <input type="text" id="guest_name" name="guest_name" placeholder="Enter your name" required>
                    </div>
                <?php } else { ?>
                    <p style="font-size: 14px; margin-bottom: 15px; color: var(--text-muted);">
                        Reviewing as: <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>
                    </p>
                <?php } ?>
                
                <div class="form-group-review">
                    <label for="rating">Star Rating:</label>
                    <select id="rating" name="rating" required>
                        <option value="5">⭐⭐⭐⭐⭐ (5 - Excellent)</option>
                        <option value="4">⭐⭐⭐⭐ (4 - Very Good)</option>
                        <option value="3">⭐⭐⭐ (3 - Average)</option>
                        <option value="2">⭐⭐ (2 - Poor)</option>
                        <option value="1">⭐ (1 - Terrible)</option>
                    </select>
                </div>
                
                <div class="form-group-review">
                    <label for="review_text">Your Review:</label>
                    <textarea id="review_text" name="review_text" rows="4" placeholder="Tell us how you liked the plant, health of roots, shipping care..." required></textarea>
                </div>
                
                <button type="submit" class="btn-premium" style="width: auto; padding: 12px 30px; box-shadow: none; display:inline-flex; align-items:center; gap:6px;">
                    Submit Review <i data-lucide="arrow-right" style="width:16px; height:16px;"></i>
                </button>
            </form>
        </div>
    </div>
    
</div>

<!-- Footer -->
<?php include '../../includes/footer.php'; ?>

<script>
// AJAX Toggle Wishlist on Product Details Page
function toggleWishlistProduct(btn, productId) {
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

// AJAX Add to Cart for Product details page
function addToCartProduct(btn, name, img) {
    let form = btn.closest("form");
    let product_id = form.querySelector("[name='product_id']").value;
    let qty = form.querySelector("[name='qty']").value;

    let toast = document.getElementById("toast-premium");
    let toastImg = document.getElementById("toast-premium-img");
    let toastText = document.getElementById("toast-premium-text");
    let cartCount = document.getElementById("cart-count");

    if (toastImg) toastImg.src = img;
    if (toastText) toastText.innerText = name + " added to cart";
    if (toast) toast.classList.add("show");

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
        if (toast) toast.classList.remove("show");
    }, 2000);
}
</script>

</body>
</html>