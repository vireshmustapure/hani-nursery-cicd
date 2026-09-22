<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_depth = substr_count(str_replace('\\', '/', $_SERVER['SCRIPT_NAME']), '/') - 1;
$base = str_repeat('../', $base_depth);

/* ================= CART COUNT FIX ================= */
$count = (!empty($_SESSION['cart']) && is_array($_SESSION['cart']))
    ? array_sum($_SESSION['cart'])
    : 0;

/* ================= DYNAMIC BANNER LOAD ================= */
if (!isset($conn)) {
    include_once __DIR__ . '/../config/db.php';
}
if (!isset($settings)) {
    $settings = [];
    $settings_res = mysqli_query($conn, "SELECT * FROM settings");
    if ($settings_res) {
        while ($settings_row = mysqli_fetch_assoc($settings_res)) {
            $settings[$settings_row['setting_key']] = $settings_row['setting_value'];
        }
    }
}

$day_of_week = date('N'); // 1 = Monday, 7 = Sunday
$matching_banner_text = '';
$header_banners_json = $settings['header_banners_json'] ?? '';
if (!empty($header_banners_json)) {
    $banners_arr = json_decode($header_banners_json, true);
    if (is_array($banners_arr)) {
        foreach ($banners_arr as $b) {
            if (isset($b['days']) && is_array($b['days']) && in_array($day_of_week, $b['days'])) {
                $matching_banner_text = $b['text'];
                break;
            }
        }
    }
}
if (empty($matching_banner_text)) {
    if ($day_of_week == 6 || $day_of_week == 7) {
        $matching_banner_text = $settings['top_banner_weekend'] ?? '🎉 WEEKEND OFFER 🎉 10% OFF on all flowering plants!';
    } else {
        $matching_banner_text = $settings['top_banner_weekday'] ?? '🌿 WEEKDAY SPECIAL 🌿 Explore agricultural and indoor plants!';
    }
}
$is_weekend_day = ($day_of_week == 6 || $day_of_week == 7);
$strip_class = $is_weekend_day ? 'offer-strip' : 'offer-strip-1';
$move_class = $is_weekend_day ? 'offer-move' : 'offer-move-1';
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Link to Premium Global Stylesheet -->
<link rel="stylesheet" href="<?= $base ?>assets/css/global.css">

<!-- Lucide Icons CDN -->
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    /* Dynamic banner strip styles */
    .offer-strip {
        width: 100%;
        background: linear-gradient(90deg, #1b5e20, #2e7d32, #1b5e20);
        color: #fff;
        overflow: hidden;
        white-space: nowrap;
        padding: 10px 0;
        font-weight: 700;
        font-size: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        position: relative;
        z-index: 999;
    }
    .offer-strip-1 {
        width: 100%;
        background: #ffffff;
        color: #2e7d32;
        overflow: hidden;
        white-space: nowrap;
        padding: 10px 0;
        font-weight: 700;
        font-size: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        position: relative;
        z-index: 999;
        border-bottom: 1px solid #e8f5e9;
    }
    .offer-move {
        display: inline-block;
        padding-left: 100%;
        animation: offerMove 20s linear infinite;
    }
    .offer-move span {
        color: #ffeb3b; /* Gold highlights for offers */
    }
    .offer-move-1 {
        display: inline-block;
        padding-left: 100%;
        animation: offerMove 20s linear infinite;
    }
    .offer-move-1 span {
        color: #1b5e20;
        font-weight: 800;
    }
    @keyframes offerMove {
        0% { transform: translateX(0); }
        100% { transform: translateX(-100%); }
    }

    /* ================= PREMIUM NAVBAR STYLES ================= */
    /* ================= PREMIUM NAVBAR STYLES ================= */
    /* ================= QUANTITY CONTROL STYLES ================= */
    .qty-selector-container {
        display: inline-flex;
        align-items: center;
        border: 2px solid var(--primary-color, #2e7d32);
        border-radius: var(--radius-full, 50px);
        overflow: hidden;
        background: white;
        height: 38px;
        box-shadow: var(--shadow-sm);
        vertical-align: middle;
    }
    .qty-btn {
        background: transparent;
        border: none;
        width: 32px;
        height: 100%;
        font-size: 18px;
        font-weight: 700;
        color: var(--primary-color, #2e7d32);
        cursor: pointer;
        transition: var(--transition-smooth, 0.2s);
        display: flex;
        align-items: center;
        justify-content: center;
        outline: none;
    }
    .qty-btn:hover:not([disabled]) {
        background: #f1f8e9;
        color: var(--primary-dark, #1b5e20);
    }
    .qty-btn[disabled] {
        opacity: 0.3;
        cursor: not-allowed;
    }
    .qty-val {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-dark, #333);
        padding: 0 8px;
        min-width: 24px;
        text-align: center;
    }
    .add-to-cart-main-btn {
        width: 100%;
    }

    .header-glass {
        background: rgba(27, 94, 32, 0.9);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        color: white;
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: var(--shadow-md);
    }

    .header-inner {
        max-width: 1200px;
        margin: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 20px;
    }

    /* LOGO */
    .logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: white;
        transition: var(--transition-smooth);
    }

    .logo:hover {
        opacity: 0.95;
        transform: scale(1.02);
    }

    .logo img {
        width: 42px;
        height: 42px;
        border-radius: var(--radius-full);
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .logo h2 {
        font-size: 19px;
        font-weight: 700;
        margin: 0;
        color: white;
    }

    /* DESKTOP NAV LINKS */
    .nav-links {
        display: flex;
        align-items: center;
        gap: 25px;
    }

    .nav-links a {
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        font-weight: 500;
        font-size: 15px;
        transition: var(--transition-smooth);
        position: relative;
        padding: 4px 0;
    }

    .nav-links a:hover {
        color: white;
    }

    .nav-links a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background-color: var(--secondary-color);
        transition: var(--transition-smooth);
    }

    .nav-links a:hover::after {
        width: 100%;
    }

    /* RIGHT GROUP */
    .right-group {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    /* CART & LOGIN BUTTONS */
    .nav-cart-btn {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white !important;
        padding: 8px 16px;
        border-radius: var(--radius-sm);
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition-smooth);
    }

    .nav-cart-btn:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
    }

    .nav-cart-btn span {
        background: var(--secondary-color);
        color: var(--primary-dark);
        padding: 2px 8px;
        border-radius: var(--radius-full);
        font-size: 12px;
        font-weight: 800;
    }

    /* PROFILE & DROPDOWN */
    .profile {
        position: relative;
    }

    .profile-btn {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        width: 38px;
        height: 38px;
        border-radius: var(--radius-full);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: var(--transition-smooth);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .profile-btn:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: scale(1.05);
    }

    .dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 48px;
        width: 200px;
        background: var(--bg-white);
        border-radius: var(--radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        border: 1px solid rgba(0, 0, 0, 0.05);
        z-index: 1100;
    }

    .dropdown a {
        display: block;
        padding: 12px 18px;
        text-decoration: none;
        color: var(--text-dark);
        font-size: 14px;
        font-weight: 500;
        transition: var(--transition-smooth);
        border-bottom: 1px solid #f5f5f5;
    }

    .dropdown a:last-child {
        border-bottom: none;
    }

    .dropdown a:hover {
        background: #f1f8e9;
        color: var(--primary-color);
    }

    .dropdown .logout {
        color: var(--accent-red);
        font-weight: 600;
    }

    /* HAMBURGER & MOBILE NAVIGATION */
    .menu-toggle {
        font-size: 24px;
        cursor: pointer;
        display: none;
        transition: var(--transition-smooth);
    }

    .menu-toggle:hover {
        color: var(--secondary-color);
    }

    /* MOBILE SLIDE DRAWER */
    .mobile-drawer-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 9998;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.4s ease;
    }

    .mobile-drawer-overlay.open {
        opacity: 1;
        pointer-events: auto;
    }

    .mobile-drawer {
        position: fixed;
        top: 0;
        right: -300px;
        width: 280px;
        height: 100vh;
        background: var(--bg-white);
        box-shadow: var(--shadow-xl);
        z-index: 9999;
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .mobile-drawer.open {
        right: 0;
    }

    .drawer-close {
        align-self: flex-end;
        font-size: 26px;
        cursor: pointer;
        color: var(--text-muted);
        transition: var(--transition-smooth);
    }

    .drawer-close:hover {
        color: var(--accent-red);
    }

    .drawer-title {
        font-size: 20px;
        font-weight: 800;
        color: var(--primary-color);
        margin-bottom: 10px;
        border-bottom: 2px solid var(--bg-light);
        padding-bottom: 10px;
    }

    .drawer-links {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .drawer-links a {
        text-decoration: none;
        color: var(--text-dark);
        font-size: 16px;
        font-weight: 600;
        padding: 8px 12px;
        border-radius: var(--radius-sm);
        transition: var(--transition-smooth);
    }

    .drawer-links a:hover {
        background: var(--bg-light);
        color: var(--primary-color);
        padding-left: 18px;
    }

    /* RESPONSIVE MEDIA QUERIES */
    @media(max-width:768px) {
        .nav-links {
            display: none;
        }

        .menu-toggle {
            display: block;
        }

        .logo h2 {
            font-size: 16px;
        }
    }
</style>

<div class="<?= $strip_class ?>">
    <div class="<?= $move_class ?>">
        <?= $matching_banner_text ?>
    </div>
</div>

<div class="header-glass">
    <div class="header-inner">

        <!-- LOGO -->
        <a href="<?= $base ?>" class="logo">
            <img src="<?= $base ?>assets/images/logo.png"
                onerror="this.src='https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=80&h=80&q=80'">
            <h2 style="display:flex; align-items:center; gap:6px;"><i data-lucide="sprout" style="width:20px; height:20px;"></i> Hani Nursery</h2>
        </a>

        <!-- DESKTOP NAV -->
        <div class="nav-links">
            <a href="<?= $base ?>">Home</a>
            <a href="<?= $base ?>pages/shop/">Shop</a>
            <a href="<?= $base ?>blog/">Gardening Tips</a>
            <a href="<?= $base ?>contact/">Visit Us</a>
        </div>

        <!-- RIGHT CONTROLS -->
        <div class="right-group">

            <!-- CART -->
            <a class="nav-cart-btn" href="<?= $base ?>pages/cart/">
                <i data-lucide="shopping-cart" style="width:16px; height:16px;"></i> Cart <span id="cart-count"><?= $count ?></span>
            </a>

            <!-- PROFILE / ACC dropdown -->
            <?php if (isset($_SESSION['user'])) { ?>

                <div class="profile">
                    <div class="profile-btn" onclick="toggleProfile()">
                        <i data-lucide="user" style="width:16px; height:16px;"></i>
                    </div>

                    <div class="dropdown" id="dropdown">
                        <a href="<?= $base ?>pages/account/dashboard/">My Account</a>
                        <a href="<?= $base ?>pages/account/orders/">My Orders</a>
                        <a href="<?= $base ?>pages/account/wishlist/">My Wishlist</a>
                        <a href="<?= $base ?>pages/account/settings/">Settings</a>
                        <a href="<?= $base ?>logout/" class="logout">Logout</a>
                    </div>
                </div>

            <?php } else { ?>

                <a class="nav-cart-btn" style="background:var(--secondary-color); color:var(--primary-dark) !important; gap:6px;"
                    href="<?= $base ?>pages/login/">
                    <i data-lucide="log-in" style="width:16px; height:16px;"></i> Login
                </a>

            <?php } ?>

            <!-- HAMBURGER MENU TOGGLE -->
            <div class="menu-toggle" onclick="toggleMobileDrawer()" style="display:flex; align-items:center; justify-content:center;"><i data-lucide="menu" style="width:24px; height:24px;"></i></div>

        </div>

    </div>
</div>

<!-- MOBILE SLIDE DRAWER OVERLAY -->
<div class="mobile-drawer-overlay" id="drawer-overlay" onclick="toggleMobileDrawer()"></div>

<!-- MOBILE SLIDE DRAWER -->
<div class="mobile-drawer" id="mobile-drawer">
    <div class="drawer-close" onclick="toggleMobileDrawer()"><i data-lucide="x" style="width:24px; height:24px;"></i></div>

    <div class="drawer-title" style="display:flex; align-items:center; gap:8px;"><i data-lucide="menu" style="width:20px; height:20px;"></i> Menu</div>

    <div class="drawer-links">
        <a href="<?= $base ?>index.php" onclick="toggleMobileDrawer()" style="display:flex; align-items:center; gap:8px;"><i data-lucide="home" style="width:18px; height:18px;"></i> Home</a>
        <a href="<?= $base ?>pages/shop/" onclick="toggleMobileDrawer()" style="display:flex; align-items:center; gap:8px;"><i data-lucide="sprout" style="width:18px; height:18px;"></i> Shop Plants</a>
        <a href="<?= $base ?>blog/" onclick="toggleMobileDrawer()" style="display:flex; align-items:center; gap:8px;"><i data-lucide="book-open" style="width:18px; height:18px;"></i> Blog & Tips</a>
        <a href="<?= $base ?>contact/" onclick="toggleMobileDrawer()" style="display:flex; align-items:center; gap:8px;"><i data-lucide="map-pin" style="width:18px; height:18px;"></i> Contact / Visit</a>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">

        <?php if (isset($_SESSION['user'])) { ?>
            <a href="<?= $base ?>pages/account/dashboard/" onclick="toggleMobileDrawer()" style="display:flex; align-items:center; gap:8px;"><i data-lucide="layout-dashboard" style="width:18px; height:18px;"></i> My Dashboard</a>
            <a href="<?= $base ?>pages/account/orders/" onclick="toggleMobileDrawer()" style="display:flex; align-items:center; gap:8px;"><i data-lucide="package" style="width:18px; height:18px;"></i> My Orders</a>
            <a href="<?= $base ?>pages/account/wishlist/" onclick="toggleMobileDrawer()" style="display:flex; align-items:center; gap:8px;"><i data-lucide="heart" style="width:18px; height:18px;"></i> Wishlist</a>
            <a href="<?= $base ?>pages/account/settings/" onclick="toggleMobileDrawer()" style="display:flex; align-items:center; gap:8px;"><i data-lucide="settings" style="width:18px; height:18px;"></i> Settings</a>
            <a href="<?= $base ?>logout/" style="color:var(--accent-red); display:flex; align-items:center; gap:8px;" onclick="toggleMobileDrawer()"><i data-lucide="log-out" style="width:18px; height:18px;"></i> Logout</a>
        <?php } else { ?>
            <a href="<?= $base ?>pages/login/" onclick="toggleMobileDrawer()" style="display:flex; align-items:center; gap:8px;"><i data-lucide="log-in" style="width:18px; height:18px;"></i> Login / Signup</a>
        <?php } ?>
    </div>
</div>

<!-- Dynamic cart added toast (Global) -->
<div id="toast-premium">
    <i data-lucide="check" style="width:20px; height:20px; color: var(--primary-light);"></i>
    <img id="toast-premium-img" src="" alt="Cart Product">
    <div id="toast-premium-text"></div>
</div>

<script>
    window.cartSession = <?php 
        $cartObj = !empty($_SESSION['cart']) ? $_SESSION['cart'] : (object)[];
        echo json_encode($cartObj); 
    ?>;
    window.cartBaseUrl = "<?= $base ?>";

    function initCartControls() {
        document.querySelectorAll(".cart-control-wrapper").forEach(wrapper => {
            renderCartControl(wrapper);
        });
    }

    function renderCartControl(wrapper) {
        let productId = wrapper.getAttribute("data-id");
        let name = wrapper.getAttribute("data-name");
        let img = wrapper.getAttribute("data-image");
        let stock = parseInt(wrapper.getAttribute("data-stock") || "10");
        let qty = window.cartSession[productId] ? parseInt(window.cartSession[productId]) : 0;
        
        let isProductPage = wrapper.hasAttribute("data-product-page");
        
        if (qty > 0) {
            wrapper.innerHTML = `
                <div class="qty-selector-container">
                    <button type="button" class="qty-btn minus-btn" onclick="updateCartQty(${productId}, ${qty - 1}, '${name.replace(/'/g, "\\'")}', '${img}')">−</button>
                    <span class="qty-val">${qty}</span>
                    <button type="button" class="qty-btn plus-btn" onclick="updateCartQty(${productId}, ${qty + 1}, '${name.replace(/'/g, "\\'")}', '${img}')" ${qty >= stock ? 'disabled' : ''}>+</button>
                </div>
            `;
        } else {
            if (isProductPage) {
                wrapper.innerHTML = `
                    <button type="button" class="btn-premium add-to-cart-main-btn" style="box-shadow:none; text-shadow:none;" onclick="updateCartQty(${productId}, 1, '${name.replace(/'/g, "\\'")}', '${img}')">
                        Add to Cart
                    </button>
                `;
            } else {
                wrapper.innerHTML = `
                    <button type="button" class="btn-add-cart-icon" style="display:flex; align-items:center; justify-content:center;" onclick="updateCartQty(${productId}, 1, '${name.replace(/'/g, "\\'")}', '${img}')">
                        <i data-lucide="shopping-cart" style="width:18px; height:18px;"></i>
                    </button>
                `;
            }
        }
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function updateCartQty(productId, newQty, name, img) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", window.cartBaseUrl + "pages/cart/index.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onload = function() {
            if(this.status === 200) {
                let cartCount = document.getElementById("cart-count");
                if(cartCount) {
                    cartCount.innerText = this.responseText;
                }
                
                // Show toast if increasing
                if (newQty > 0 && (!window.cartSession[productId] || newQty > window.cartSession[productId])) {
                    showCartToast(name, img);
                }
                
                // Update local session
                if (newQty <= 0) {
                    delete window.cartSession[productId];
                } else {
                    window.cartSession[productId] = newQty;
                }
                
                // Update all controls for this product on the page
                document.querySelectorAll(`.cart-control-wrapper[data-id="${productId}"]`).forEach(wrapper => {
                    renderCartControl(wrapper);
                });
            }
        };
        
        xhr.send("product_id=" + productId + "&set_qty=" + newQty);
    }

    function showCartToast(name, img) {
        let toast = document.getElementById("toast-premium");
        let toastImg = document.getElementById("toast-premium-img");
        let toastText = document.getElementById("toast-premium-text");
        if (toast && toastImg && toastText) {
            toastImg.src = img;
            toastText.innerText = name + " added to cart";
            toast.classList.add("show");
            setTimeout(() => {
                toast.classList.remove("show");
            }, 2000);
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        if (window.lucide) {
            window.lucide.createIcons();
        }
        initCartControls();
    });

    function toggleProfile() {
        let d = document.getElementById("dropdown");
        if (d) {
            d.style.display = (d.style.display === "block") ? "none" : "block";
        }
    }

    function toggleMobileDrawer() {
        let drawer = document.getElementById("mobile-drawer");
        let overlay = document.getElementById("drawer-overlay");
        if (drawer && overlay) {
            drawer.classList.toggle("open");
            overlay.classList.toggle("open");
        }
    }

    document.addEventListener("click", function (e) {
        let box = document.querySelector(".profile");
        let drop = document.getElementById("dropdown");

        if (box && !box.contains(e.target)) {
            if (drop) drop.style.display = "none";
        }
    });
</script>