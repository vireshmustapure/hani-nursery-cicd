<?php
session_start();
include 'config/db.php';

// Fetch dynamic settings
$settings_res = mysqli_query($conn, "SELECT * FROM settings");
$settings = [];
if ($settings_res) {
    while ($row = mysqli_fetch_assoc($settings_res)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-1LSB0KPW3Z"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-1LSB0KPW3Z');
    </script>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Best Plant Nursery in Kalaburagi | Hani Nursery and Gardening</title>

    <style>
    html {
        scroll-behavior: smooth;
    }
    


    /* SEARCH BAR */
    .search-container {
        display: flex;
        justify-content: center;
        margin: 30px 20px;
    }
    .search-box {
        display: flex;
        width: 650px;
        border-radius: 50px;
        overflow: hidden;
        border: 2px solid rgba(46, 125, 50, 0.15);
        background: #fff;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-smooth);
    }
    .search-box:focus-within {
        border-color: var(--primary-light);
        box-shadow: var(--shadow-md);
    }
    .search-box select {
        padding: 10px 20px;
        border: none;
        background: #f4faf4;
        color: var(--primary-dark);
        font-weight: 600;
        outline: none;
        cursor: pointer;
    }
    .search-box input {
        flex: 1;
        padding: 12px 20px;
        border: none;
        outline: none;
        font-size: 15px;
    }
    .search-box button {
        background: var(--secondary-color);
        border: none;
        padding: 0 25px;
        cursor: pointer;
        font-size: 18px;
        color: var(--primary-dark);
        transition: var(--transition-smooth);
    }
    .search-box button:hover {
        background: #b58915;
    }

    /* PREMIUM HERO SECTION WITH SLIDESHOW */
    .hero {
        position: relative;
        height: 500px;
        overflow: hidden;
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        box-shadow: var(--shadow-lg);
    }
    .hero-slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        opacity: 0;
        transition: opacity 1.5s ease-in-out;
        z-index: 1;
    }
    .hero-slide.active {
        opacity: 1;
        z-index: 2;
    }
    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.45);
        z-index: 3;
    }
    .hero-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        text-shadow: 2px 2px 12px rgba(0,0,0,0.6);
        z-index: 4;
        width: 90%;
        max-width: 800px;
    }
    .hero-content h2 {
        font-size: 48px;
        font-weight: 800;
        color: white;
        margin-bottom: 15px;
        line-height: 1.2;
    }
    .hero-content h1 {
        font-size: 24px;
        font-weight: 500;
        letter-spacing: 1px;
        margin-top: 15px;
        color: #e8f5e9;
    }
    .blink-btn {
        margin-top: 20px;
    }

    /* CATEGORIES SECTION */
    .categories {
        display: flex;
        justify-content: center;
        gap: 15px;
        padding: 40px 20px;
        flex-wrap: wrap;
    }
    .cat {
        background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
        color: white;
        padding: 14px 28px;
        border-radius: var(--radius-full);
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 6px 15px rgba(27,94,32,0.15);
        transition: var(--transition-smooth);
        font-size: 15px;
    }
    .cat:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(27,94,32,0.25);
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    }

    .highlight-text {
        font-size: 32px;
        font-weight: 600;
        color: var(--primary-color);
        text-align: center;
        padding: 50px 20px;
        background: linear-gradient(to right, transparent, #e8f5e9, transparent);
        margin: 20px 0;
    }

    /* BLOG HIGHLIGHT */
    .blog-section-home {
        background: #e8f5e9;
        padding: 60px 20px;
        border-radius: var(--radius-lg);
        margin: 40px 20px;
    }
    .blog-grid {
        display: flex;
        gap: 25px;
        overflow-x: auto;
        padding: 20px 10px;
        scrollbar-width: thin;
        scrollbar-color: var(--primary-light) #f0f0f0;
    }
    .blog-card {
        min-width: 300px;
        background: #fff;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        padding: 20px;
        transition: var(--transition-smooth);
        border: 1px solid rgba(0,0,0,0.02);
    }
    .blog-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }
    .blog-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: var(--radius-sm);
    }
    .blog-card h3 {
        font-size: 19px;
        margin: 15px 0 10px;
        color: var(--primary-dark);
        line-height: 1.4;
    }
    .blog-card a {
        text-decoration: none;
        color: var(--primary-light);
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: var(--transition-smooth);
    }
    .blog-card a:hover {
        color: var(--primary-dark);
        padding-left: 5px;
    }

    /* VISIT NURSERY SECTION */
    .nursery-section {
        padding: 60px 20px;
        background: #f1f8f4;
        margin: 40px 20px;
        border-radius: var(--radius-lg);
    }
    .nursery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 30px;
    }
    .nursery-card {
        background: white;
        border-radius: var(--radius-md);
        padding: 30px;
        box-shadow: var(--shadow-sm);
        display: flex;
        gap: 20px;
        align-items: center;
        border: 1px solid rgba(0,0,0,0.02);
    }
    .nursery-card img {
        width: 180px;
        height: 200px;
        object-fit: cover;
        border-radius: var(--radius-md);
    }
    .nursery-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .nursery-content h3 {
        color: var(--primary-color);
        margin: 0;
        font-size: 22px;
    }
    .nursery-content p {
        font-size: 14px;
        color: var(--text-muted);
    }

    /* Wholesale Image Slider */
    .slider-box {
        width: 180px;
        height: 200px;
        overflow: hidden;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        background: #fff;
        position: relative;
        flex-shrink: 0;
    }
    .slider {
        display: flex;
        width: 500%;
        height: 100%;
        animation: slide 25s infinite;
    }
    .slide {
        width: 20%;
        height: 100%;
        flex-shrink: 0;
    }
    .slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    @keyframes slide {
        0%, 16% { transform: translateX(0%); }
        20%, 36% { transform: translateX(-20%); }
        40%, 56% { transform: translateX(-40%); }
        60%, 76% { transform: translateX(-60%); }
        80%, 96% { transform: translateX(-80%); }
        100% { transform: translateX(0%); }
    }

    /* WHY VISIT LIST */
    .why-section {
        margin-top: 50px;
        text-align: center;
    }
    .why-box {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 25px;
    }
    .why-item {
        background: white;
        padding: 16px 25px;
        border-radius: var(--radius-full);
        box-shadow: var(--shadow-sm);
        font-weight: 600;
        color: var(--primary-dark);
        border: 1px solid rgba(46, 125, 50, 0.05);
    }

    /* CONTACT + MAP SECTION */
    .visit-section {
        padding: 70px 20px;
        background: linear-gradient(135deg, #eef8f0, #dff3e3);
        margin-top: 50px;
    }
    .visit-heading {
        text-align: center;
        margin-bottom: 50px;
    }
    .visit-heading h2 {
        font-size: 36px;
        color: #1b5e20;
        margin-bottom: 10px;
    }
    .visit-heading p {
        color: var(--text-muted);
        font-size: 17px;
    }
    .visit-wrapper {
        max-width: 1200px;
        margin: auto;
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 40px;
    }
    .visit-info, .visit-map {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border-radius: var(--radius-lg);
        padding: 40px;
        box-shadow: var(--shadow-lg);
        border: 1px solid rgba(255,255,255,0.4);
    }
    .visit-info h3 {
        color: #1b5e20;
        margin-bottom: 25px;
        font-size: 24px;
    }
    .visit-info p {
        margin: 12px 0;
        font-size: 16px;
        color: var(--text-dark);
    }
    .visit-buttons {
        margin-top: 30px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    .visit-buttons a {
        padding: 14px 25px;
        border-radius: var(--radius-full);
        text-decoration: none;
        color: white;
        font-weight: bold;
        transition: var(--transition-smooth);
        box-shadow: var(--shadow-sm);
    }
    .dir-btn {
        background: var(--primary-color);
    }
    .dir-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-3px);
    }
    .wa-btn {
        background: var(--accent-color);
    }
    .wa-btn:hover {
        background: #1ebd56;
        transform: translateY(-3px);
    }
    .visit-map iframe {
        width: 100%;
        height: 100%;
        min-height: 400px;
        border: 0;
        border-radius: var(--radius-md);
    }

    /* MINI STARS REVIEW */
    .mini-review {
        text-align: center;
        margin-top: 30px;
        border-top: 1px solid rgba(0,0,0,0.05);
        padding-top: 20px;
    }
    .mini-stars a {
        font-size: 28px;
        text-decoration: none;
        margin: 0 4px;
    }
    .mini-review p {
        margin: 10px 0;
        color: #1b5e20;
        font-size: 16px;
        font-weight: 700;
    }
    .mini-btn {
        display: inline-block;
        background: #FFD700;
        color: #222;
        padding: 10px 24px;
        border-radius: var(--radius-full);
        text-decoration: none;
        font-size: 14px;
        font-weight: bold;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-smooth);
    }
    .mini-btn:hover {
        background: #f5c400;
        transform: translateY(-2px);
    }

    /* RESPONSIVE MEDIA FOR SECTIONS */
    @media(max-width:992px) {
        .visit-wrapper {
            grid-template-columns: 1fr;
        }
        .nursery-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media(max-width:768px) {
        .hero {
            height: 400px;
        }
        .hero-content h2 {
            font-size: 34px;
        }
        .hero-content h1 {
            font-size: 18px;
        }
        .highlight-text {
            font-size: 24px;
        }
        .nursery-card {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }
        .nursery-card img, .slider-box {
            width: 100%;
            height: 220px;
        }
        .why-item {
            width: calc(50% - 10px);
            font-size: 14px;
        }
        .visit-info, .visit-map {
            padding: 25px;
        }
        .visit-map iframe {
            min-height: 300px;
        }
    }
    </style>
</head>
<body>



<!-- INCLUDE DYNAMIC HEADER -->
<?php include "includes/header.php"; ?>

<!-- SEARCH SECTION -->
<div class="search-container">
    <form method="GET" action="pages/shop/" class="search-box">
        <select name="category">
            <option value="">All Categories</option>
            <option value="Indoor Plants">Indoor Plants</option>
            <option value="Outdoor Plants">Outdoor Plants</option>
            <option value="Farmer Plants">Farmer Plants</option>
            <option value="Flowering Plants">Flower Plants</option>
        </select>
        <input type="text" name="search" placeholder="Search plants by name or features..." />
        <button type="submit" style="display:flex; align-items:center; justify-content:center;"><i data-lucide="search" style="width:20px; height:20px;"></i></button>
    </form>
</div>

<!-- PREMIUM HERO SLIDESHOW SECTION -->
<section class="hero">
    <div class="hero-slide active" style="background-image: url('https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=1200&q=80');"></div>
    <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1490750967868-88aa4486c946?auto=format&fit=crop&w=1200&q=80');"></div>
    <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1604762524889-3e2fcc145683?auto=format&fit=crop&w=1200&q=80');"></div>
    <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1591857177580-dc82b9ac4e1e?auto=format&fit=crop&w=1200&q=80');"></div>
    <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=1200&q=80');"></div>
    
    <div class="hero-content">
        <h2 class="serif-font">Welcome to Hani Nursery 2 <i data-lucide="sprout" style="width:36px; height:36px; vertical-align:middle; display:inline-block;"></i></h2>
        <a href="pages/shop/" class="btn-premium blink-btn">Shop Premium Plants</a>
        <h1>Best Plant Nursery and Gardening Center in Kalaburagi</h1>
    </div>
</section>

<!-- CATEGORY NAVIGATION SHORTCUTS -->
<section class="categories">
    <a href="pages/shop/" class="cat" style="display:inline-flex; align-items:center; gap:8px;"><i data-lucide="leaf" style="width:18px; height:18px;"></i> All Collections</a>
    <a href="pages/shop/?category=Indoor Plants" class="cat" style="display:inline-flex; align-items:center; gap:8px;"><i data-lucide="sprout" style="width:18px; height:18px;"></i> Indoor Plants</a>
    <a href="pages/shop/?category=Outdoor Plants" class="cat" style="display:inline-flex; align-items:center; gap:8px;"><i data-lucide="trees" style="width:18px; height:18px;"></i> Outdoor Plants</a>
    <a href="pages/shop/?category=Farmer Plants" class="cat" style="display:inline-flex; align-items:center; gap:8px;"><i data-lucide="tractor" style="width:18px; height:18px;"></i> Farmer Plants</a>
    <a href="pages/shop/?category=Flowering Plants" class="cat" style="display:inline-flex; align-items:center; gap:8px;"><i data-lucide="flower-2" style="width:18px; height:18px;"></i> Flower Plants</a>
</section>

<!-- HIGHLIGHT -->
<section class="highlight-text serif-font" style="display:flex; align-items:center; justify-content:center; gap:10px;">
    "Happiness is creating a garden in your own space" <i data-lucide="heart" style="width:28px; height:28px; fill:var(--primary-color); stroke:var(--primary-color);"></i>
</section>

<!-- DYNAMIC TOP SOLD PLANTS SECTION -->
<section style="max-width: 1200px; margin: auto; padding: 40px 20px;">
    <h2 style="text-align: center; margin-bottom: 10px; font-size: 32px; display:flex; align-items:center; justify-content:center; gap:10px;" class="serif-font"><i data-lucide="award" style="width:32px; height:32px; color:var(--secondary-color);"></i> Top Sold Plants</h2>
    <p style="text-align: center; color: #666; margin-bottom: 40px;">Our most loved and highly rated houseplants this season</p>
    
    <div class="product-grid">
        <?php
        $top_sold = mysqli_query($conn, "SELECT * FROM products WHERE is_top_sold = 1 LIMIT 4");
        
        if (mysqli_num_rows($top_sold) > 0) {
            while ($row = mysqli_fetch_assoc($top_sold)) {
                // Count product rating averages
                $pid = $row['id'];
                $rating_res = mysqli_query($conn, "SELECT AVG(rating) as avg, COUNT(*) as cnt FROM reviews WHERE product_id=$pid AND status='approved'");
                $rating_row = mysqli_fetch_assoc($rating_res);
                $avg_rating = round($rating_row['avg'] ?? 5);
                $rating_cnt = $rating_row['cnt'] ?? 0;
        ?>
            <div class="product-card">
                <div class="product-image-container">
                    <a href="pages/product/index.php?id=<?php echo $row['id']; ?>">
                        <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                    </a>
                    
                    <!-- Like/Wishlist Heart Toggle -->
                    <?php
                    $isLiked = false;
                    if (isset($_SESSION['user'])) {
                        $uid = $_SESSION['user'];
                        $check_like = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id=$uid AND product_id=$pid");
                        if (mysqli_num_rows($check_like) > 0) {
                            $isLiked = true;
                        }
                    }
                    ?>
                    <button class="wishlist-heart-btn <?php echo $isLiked ? 'liked' : ''; ?>" onclick="toggleWishlist(this, <?php echo $row['id']; ?>)">
                        <i data-lucide="heart" class="heart-icon-svg" style="width: 20px; height: 20px; fill: <?php echo $isLiked ? 'var(--accent-red)' : 'none'; ?>; stroke: <?php echo $isLiked ? 'var(--accent-red)' : 'currentColor'; ?>;"></i>
                    </button>
                </div>
                
                <div class="product-details-box">
                    <div class="product-category-tag"><?php echo htmlspecialchars($row['category']); ?></div>
                    <a href="pages/product/index.php?id=<?php echo $row['id']; ?>" class="product-title"><?php echo htmlspecialchars($row['name']); ?></a>
                    
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
                        <div class="cart-control-wrapper" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['name']); ?>" data-image="uploads/<?php echo $row['image']; ?>" data-stock="<?php echo $row['stock']; ?>"></div>
                    </div>
                </div>
            </div>
        <?php
            }
        } else {
            // Fallback to top 4 products if none are explicitly selected as top sold
            $fallback = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC LIMIT 4");
            while ($row = mysqli_fetch_assoc($fallback)) {
                $pid = $row['id'];
        ?>
            <div class="product-card">
                <div class="product-image-container">
                    <a href="pages/product/index.php?id=<?php echo $row['id']; ?>">
                        <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                    </a>
                    <button class="wishlist-heart-btn" onclick="toggleWishlist(this, <?php echo $row['id']; ?>)">
                        <i data-lucide="heart" class="heart-icon-svg" style="width: 20px; height: 20px; fill: none; stroke: currentColor;"></i>
                    </button>
                </div>
                <div class="product-details-box">
                    <div class="product-category-tag"><?php echo htmlspecialchars($row['category']); ?></div>
                    <a href="pages/product/index.php?id=<?php echo $row['id']; ?>" class="product-title"><?php echo htmlspecialchars($row['name']); ?></a>
                    <div class="product-rating-box">
                        <span class="star-icon">★</span><span class="star-icon">★</span><span class="star-icon">★</span><span class="star-icon">★</span><span class="star-icon">★</span>
                        <span class="rating-count-text">(0)</span>
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
                        <div class="cart-control-wrapper" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['name']); ?>" data-image="uploads/<?php echo $row['image']; ?>" data-stock="<?php echo $row['stock']; ?>"></div>
                    </div>
                </div>
            </div>
        <?php
            }
        }
        ?>
    </div>
</section>

<!-- GARDENING TIPS BLOG SECTION -->
<section class="blog-section-home">
    <h2 style="text-align:center; font-size:32px; margin-bottom: 40px; display:flex; align-items:center; justify-content:center; gap:10px;" class="serif-font"><i data-lucide="book-open" style="width:32px; height:32px;"></i> Fresh Gardening Tips</h2>
    
    <div class="blog-grid">
        <?php
        // Fetch published posts from the active posts table
        $blog_res = mysqli_query($conn, "SELECT * FROM posts WHERE status='published' ORDER BY is_pinned DESC, published_at DESC LIMIT 5");
        
        if (mysqli_num_rows($blog_res) > 0) {
            while($row = mysqli_fetch_assoc($blog_res)){
                $img_src = $row['image'] ?: 'https://images.unsplash.com/photo-1501004318641-b39e6451bec6';
                if (strpos($img_src, 'http') === false) {
                    $img_src = ltrim($img_src, '/');
                }
        ?>
            <div class="blog-card">
                <img src="<?php echo htmlspecialchars($img_src); ?>">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <a href="blog/<?php echo $row['slug']; ?>/" style="display:inline-flex; align-items:center; gap:6px;">Read Article <i data-lucide="arrow-right" style="width:16px; height:16px;"></i></a>
            </div>
        <?php 
            }
        } else {
            // Fallback to blogs table in case no posts table entries exist yet
            $old_blog_res = mysqli_query($conn, "SELECT * FROM blogs ORDER BY id DESC LIMIT 5");
            while ($row = mysqli_fetch_assoc($old_blog_res)) {
        ?>
            <div class="blog-card">
                <img src="uploads/<?php echo $row['image']; ?>">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <a href="blog-single/?id=<?php echo $row['id']; ?>" style="display:inline-flex; align-items:center; gap:6px;">Read Article <i data-lucide="arrow-right" style="width:16px; height:16px;"></i></a>
            </div>
        <?php
            }
        }
        ?>
    </div>
</section>

<!-- NURSERY HIGHLIGHT SECTION -->
<section class="nursery-section">
    <div class="nursery-grid">
        
        <!-- Left Card -->
        <div class="nursery-card">
            <div class="nursery-content">
                <h3 style="display:flex; align-items:center; gap:8px;"><i data-lucide="map-pin" style="width:24px; height:24px; color:var(--primary-light);"></i> Visit Our Physical Nursery</h3>
                <p>Experience the beauty of nature firsthand! Wander through our leafy greenhouse filled with fresh, vibrant plant collections and garden accessories.</p>
                <a href="https://share.google/7YePEfVIKtmIGDyxn" target="_blank" class="btn-premium" style="gap:6px;">
                    <i data-lucide="navigation" style="width:16px; height:16px;"></i> Open Google Map
                </a>
            </div>
            <img src="uploads/nursury_front.jpg" onerror="this.src='https://images.unsplash.com/photo-1592150621744-aca64f48394a?auto=format&fit=crop&w=400&q=80'" alt="Nursery Image">
        </div>
        
        <!-- Right Card -->
        <div class="nursery-card">
            <div class="nursery-content">
                <h3 style="display:flex; align-items:center; gap:8px;"><i data-lucide="briefcase" style="width:24px; height:24px; color:var(--primary-light);"></i> Wholesale Discounts</h3>
                <p>Order in bulk and unlock special wholesale prices. Ideal for offices, apartments, landscaped gardens, and decorators. Available for pick-up at our physical shop counter.</p>
                <p style="color:orange; font-weight:700; display:flex; align-items:center; gap:6px;"><i data-lucide="star" style="width:16px; height:16px; fill:orange; stroke:orange;"></i> Offers available on in-person visits</p>
            </div>
            
            <!-- Wholesale image slider -->
            <div class="slider-box">
                <div class="slider">
                    <div class="slide"><img src="uploads/IMG-20260409-WA0041.webp" onerror="this.src='https://images.unsplash.com/photo-1598902108854-10e335adac99?auto=format&fit=crop&w=200&h=220&q=80'"></div>
                    <div class="slide"><img src="uploads/IMG-20260409-WA0021.webp" onerror="this.src='https://images.unsplash.com/photo-1533038590840-1cde6e668a91?auto=format&fit=crop&w=200&h=220&q=80'"></div>
                    <div class="slide"><img src="uploads/IMG-20260409-WA0013.webp" onerror="this.src='https://images.unsplash.com/photo-1463936575829-25148e1db1b8?auto=format&fit=crop&w=200&h=220&q=80'"></div>
                    <div class="slide"><img src="uploads/IMG-20260409-WA0036.webp" onerror="this.src='https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=200&h=220&q=80'"></div>
                    <div class="slide"><img src="uploads/IMG-20260426-WA00001.webp" onerror="this.src='https://images.unsplash.com/photo-1592150621744-aca64f48394a?auto=format&fit=crop&w=200&h=220&q=80'"></div>
                </div>
            </div>
        </div>

    </div>

    <style>
        .why-shop-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .why-shop-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 25px;
            box-shadow: var(--shadow-sm);
            border: 1px solid #f0fdf4;
            transition: var(--transition-smooth);
            text-align: left;
        }
        .why-shop-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-light);
        }
        .why-card-icon {
            color: var(--primary-color);
            margin-bottom: 15px;
            display: inline-block;
            background: #e8f5e9;
            padding: 12px;
            border-radius: var(--radius-md);
        }
        .why-shop-card h3 {
            font-size: 18px;
            color: var(--primary-dark);
            margin: 0 0 10px 0;
            font-weight: 700;
        }
        .why-shop-card p {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.6;
            margin: 0;
        }
    </style>
    
    <div class="why-section" style="background: #f7fdf8; padding: 60px 0; border-radius: var(--radius-lg); margin: 50px 20px;">
        <h2 class="serif-font" style="font-size:36px; text-align: center; color: var(--primary-dark); margin-bottom: 10px; display:flex; align-items:center; justify-content:center; gap:10px;"><i data-lucide="help-circle" style="width:36px; height:36px; color:var(--primary-light);"></i> Why Shop with Hani Nursery?</h2>
        <p style="text-align: center; color: var(--text-muted); max-width: 600px; margin: 0 auto 40px;">We are dedicated to bringing high-quality plant varieties, wholesale pricing, and expert guidance directly to your gardening space.</p>
        
        <div class="why-shop-grid">
            <div class="why-shop-card">
                <div class="why-card-icon"><i data-lucide="sprout" style="width:28px; height:28px;"></i></div>
                <h3>Direct Farm Sourced & Fresh</h3>
                <p>All our plants are grown in our local farms and greenhouses under strict quality controls, arriving fresh and shock-free.</p>
            </div>
            <div class="why-shop-card">
                <div class="why-card-icon"><i data-lucide="badge-percent" style="width:28px; height:28px;"></i></div>
                <h3>Transparent Wholesale Pricing</h3>
                <p>Skip the middlemen! Get nursery-direct prices on all items, whether you are buying a single flowering pot or landscaping an entire project.</p>
            </div>
            <div class="why-shop-card">
                <div class="why-card-icon"><i data-lucide="heart-handshake" style="width:28px; height:28px;"></i></div>
                <h3>Expert Horticultural Support</h3>
                <p>Receive lifetime support from our professional botanists and gardening experts on soil mixtures, sunlight, and watering schedules.</p>
            </div>
            <div class="why-shop-card">
                <div class="why-card-icon"><i data-lucide="leaf" style="width:28px; height:28px;"></i></div>
                <h3>Massive Plant Diversity</h3>
                <p>Choose from over 500+ healthy plant varieties including indoor foliage, exotics, agricultural seedlings, and designer clay pots.</p>
            </div>
            <div class="why-shop-card">
                <div class="why-card-icon"><i data-lucide="truck" style="width:28px; height:28px;"></i></div>
                <h3>Care-Packed Safe Delivery</h3>
                <p>Our custom eco-friendly packaging keeps moisture intact and shields roots from travel shock, ensuring safe door-step arrival.</p>
            </div>
            <div class="why-shop-card">
                <div class="why-card-icon"><i data-lucide="smile" style="width:28px; height:28px;"></i></div>
                <h3>Eco-Conscious Gardening</h3>
                <p>We use biodegradable coir pots, organic composts, and sustainable pest controls to protect both your garden and the local ecosystem.</p>
            </div>
        </div>
    </div>
</section>

<!-- RECENTLY VIEWED PRODUCTS SECTION -->
<?php
if (!empty($_SESSION['recently_viewed']) && is_array($_SESSION['recently_viewed'])):
    // Filter array to ensure values are numeric
    $recent_ids = array_filter(array_map('intval', $_SESSION['recently_viewed']));
    if (!empty($recent_ids)):
        $ids_str = implode(',', $recent_ids);
        // Maintain original order of array IDs via FIELD() query
        $recent_res = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids_str) ORDER BY FIELD(id, $ids_str) LIMIT 5");
?>
    <section style="max-width: 1200px; margin: 60px auto 40px; padding: 0 20px;">
        <h2 style="font-size: 28px; margin-bottom: 10px; display:flex; align-items:center; gap:8px;" class="serif-font"><i data-lucide="eye" style="width:24px; height:24px;"></i> Recently Viewed</h2>
        <p style="color: #666; margin-bottom: 25px;">Pick up right where you left off</p>
        
        <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));">
            <?php 
            while ($row = mysqli_fetch_assoc($recent_res)) {
                $pid = $row['id'];
            ?>
                <div class="product-card">
                    <div class="product-image-container" style="height: 170px;">
                        <a href="pages/product/index.php?id=<?php echo $row['id']; ?>">
                            <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                        </a>
                    </div>
                    <div class="product-details-box" style="padding: 15px;">
                        <a href="pages/product/index.php?id=<?php echo $row['id']; ?>" class="product-title" style="font-size: 15px;"><?php echo htmlspecialchars($row['name']); ?></a>
                        <div class="product-price" style="font-size: 17px; margin-top: 5px;">₹<?php echo $row['price']; ?></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
<?php 
    endif;
endif; 
?>

<!-- VISIT AND CONTACT MAP -->
<section class="visit-section">
    <div class="visit-heading">
        <h2 class="serif-font" style="display:flex; align-items:center; justify-content:center; gap:10px;"><i data-lucide="sprout" style="width:32px; height:32px;"></i> Visit Hani Nursery & Gardens Today <i data-lucide="sprout" style="width:32px; height:32px;"></i></h2>
        <p>Fresh Healthy Plants • Affordable Wholesale Prices • Trusted Kalaburagi Nursery</p>
    </div>
    
    <div class="visit-wrapper">
        <!-- Info Column -->
        <div class="visit-info">
            <h3 style="display:flex; align-items:center; gap:8px;"><i data-lucide="map-pin" style="width:24px; height:24px;"></i> Contact Details & Directions</h3>
            <p><strong>Hani Nursery & Gardening Center</strong></p>
            <p>P&T Cross, Old Jewargi Rd</p>
            <p>Near Zudio, Opp Sai Bazar</p>
            <p>Kalaburagi, Karnataka - 585102</p>
            <p style="margin-top: 15px; display:flex; align-items:center; gap:6px;"><i data-lucide="phone" style="width:16px; height:16px; color:var(--primary-light);"></i> <strong>Phone Support:</strong> +91 7676626666</p>
            <p style="display:flex; align-items:center; gap:6px;"><i data-lucide="clock" style="width:16px; height:16px; color:var(--primary-light);"></i> <strong>Operating Hours:</strong> 8:00 AM - 8:00 PM (Daily)</p>
            <p style="color: #ffd700; font-size: 18px; margin-top: 15px; display:flex; align-items:center; gap:4px;"><i data-lucide="star" style="width:16px; height:16px; fill:#ffd700; stroke:#ffd700;"></i><i data-lucide="star" style="width:16px; height:16px; fill:#ffd700; stroke:#ffd700;"></i><i data-lucide="star" style="width:16px; height:16px; fill:#ffd700; stroke:#ffd700;"></i><i data-lucide="star" style="width:16px; height:16px; fill:#ffd700; stroke:#ffd700;"></i><i data-lucide="star" style="width:16px; height:16px; fill:#ffd700; stroke:#ffd700;"></i> <span style="color: #333; font-size:14px; font-weight:bold; margin-left:6px;">Trusted local nursery</span></p>
            
            <div class="visit-buttons">
                <a href="https://maps.app.goo.gl/zZooo2P3QFCKZiSi7" target="_blank" class="dir-btn" style="display:inline-flex; align-items:center; gap:6px;">
                    <i data-lucide="navigation" style="width:16px; height:16px;"></i> Get Driving Directions
                </a>
                <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? '917676626666'; ?>?text=<?php echo urlencode($settings['whatsapp_message'] ?? 'Hello Hani Nursery'); ?>" target="_blank" class="wa-btn" style="display:inline-flex; align-items:center; gap:6px;">
                    <i data-lucide="message-circle" style="width:18px; height:18px;"></i> WhatsApp Inquiry
                </a>
            </div>
            
            <div class="mini-review">
                <div class="mini-stars">
                    <a href="https://search.google.com/local/writereview?placeid=ChIJJ8iWIu2_yDsR-7mBJGELuaE" target="_blank">⭐</a>
                    <a href="https://search.google.com/local/writereview?placeid=ChIJJ8iWIu2_yDsR-7mBJGELuaE" target="_blank">⭐</a>
                    <a href="https://search.google.com/local/writereview?placeid=ChIJJ8iWIu2_yDsR-7mBJGELuaE" target="_blank">⭐</a>
                    <a href="https://search.google.com/local/writereview?placeid=ChIJJ8iWIu2_yDsR-7mBJGELuaE" target="_blank">⭐</a>
                    <a href="https://search.google.com/local/writereview?placeid=ChIJJ8iWIu2_yDsR-7mBJGELuaE" target="_blank">⭐</a>
                </div>
                <p>Love Our Collection? Share Your Feedback</p>
                <a href="https://search.google.com/local/writereview?placeid=ChIJJ8iWIu2_yDsR-7mBJGELuaE" target="_blank" class="mini-btn">
                    ⭐ Leave Google Review
                </a>
            </div>
        </div>

        <!-- Google Maps Embed -->
        <div class="visit-map">
            <iframe
                src="https://www.google.com/maps?q=Hani+Nursery+Kalaburagi&output=embed"
                loading="lazy"
                allowfullscreen>
            </iframe>
        </div>
    </div>
</section>

<!-- STICKY FLOATING WHATSAPP BUBBLE -->
<div class="whatsapp-bubble-container" id="wa-bubble-container">
    <div class="whatsapp-tooltip">Chat with us on WhatsApp!</div>
    <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? '917676626666'; ?>?text=<?php echo urlencode($settings['whatsapp_message'] ?? 'Hello Hani Nursery'); ?>" target="_blank" class="whatsapp-bubble">
        <svg viewBox="0 0 24 24" width="32" height="32" fill="currentColor">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.262 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.457L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.863-9.755.002-2.61-1.01-5.063-2.85-6.907C16.643 2.097 14.195.84 11.59.84c-5.442 0-9.866 4.372-9.87 9.76-.002 1.802.483 3.565 1.407 5.12L2.129 21.73l6.518-1.777zM17.12 14.4c-.303-.152-1.793-.883-2.047-.975-.254-.092-.44-.138-.625.138-.185.277-.71.883-.87 1.068-.16.184-.32.207-.624.055-.304-.152-1.282-.472-2.443-1.507-.903-.805-1.512-1.8-1.69-2.107-.177-.307-.02-.473.133-.625.138-.137.303-.354.456-.53.15-.177.2-.303.3-.506.1-.202.05-.38-.026-.53-.076-.153-.625-1.507-.856-2.07-.225-.544-.452-.47-.624-.479-.16-.008-.346-.01-.53-.01-.185 0-.485.07-.74.354-.253.285-1.016.992-1.016 2.422 0 1.43 1.04 2.81 1.185 3.003.145.19 2.05 3.13 4.97 4.387.693.3 1.235.478 1.66.612.697.22 1.33.19 1.83.115.558-.083 1.794-.733 2.048-1.44.254-.707.254-1.314.177-1.44-.077-.127-.253-.203-.556-.356z"/>
        </svg>
    </a>
</div>

<!-- DYNAMIC PRODUCT ADDED TO CART TOAST -->
<div id="toast-premium">
    <span style="font-size: 20px;">✔</span>
    <img id="toast-premium-img" src="" alt="Cart Product">
    <div id="toast-premium-text"></div>
</div>

<!-- INCLUDE FOOTER -->
<?php include "includes/footer.php"; ?>

<!-- JAVASCRIPT LOGIC -->
<script>
// 1. Premium Hero Slideshow Transition
const slides = document.querySelectorAll(".hero-slide");
let currentSlide = 0;

setInterval(() => {
    slides[currentSlide].classList.remove("active");
    currentSlide = (currentSlide + 1) % slides.length;
    slides[currentSlide].classList.add("active");
}, 4500);

// 2. WhatsApp Bubble Shrink on scroll
window.addEventListener('scroll', function() {
    let bubbleContainer = document.getElementById('wa-bubble-container');
    if (bubbleContainer) {
        if (window.scrollY > 150) {
            bubbleContainer.classList.add('folded');
        } else {
            bubbleContainer.classList.remove('folded');
        }
    }
});

// 3. AJAX Toggle Wishlist
function toggleWishlist(btn, productId) {
    <?php if(!isset($_SESSION['user'])) { ?>
        alert("Please login to add plants to your wishlist! ");
        window.location.href = "pages/login/";
        return;
    <?php } ?>

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "pages/wishlist_toggle/index.php", true);
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

// 4. AJAX add to cart from Homepage
function addToCartHome(btn, name, img) {
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
    xhr.open("POST", "pages/cart/index.php", true);
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