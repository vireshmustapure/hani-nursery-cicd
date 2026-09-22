<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base = "/";

/* ================= CART COUNT FIX ================= */
$count = (!empty($_SESSION['cart']) && is_array($_SESSION['cart']))
    ? array_sum($_SESSION['cart'])
    : 0;
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

/* ================= HEADER ================= */
.header {
    background: #1b5e20;
    color: white;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.header-inner {
    max-width: 1200px;
    margin: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 18px;
    font-family: Arial, sans-serif;
}

/* LOGO */
.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: white;
}

.logo img {
    width: 38px;
    height: 38px;
    border-radius: 50%;
}

.logo h2 {
    font-size: 18px;
    margin: 0;
}

/* RIGHT SIDE */
.right-group {
    display: flex;
    align-items: center;
    gap: 15px;
}

/* CART */
.cart {
    background: rgba(255,255,255,0.15);
    padding: 6px 10px;
    border-radius: 6px;
    font-weight: 600;
}

/* PROFILE */
.profile {
    position: relative;
}

.profile-btn {
    background: #2e7d32;
    padding: 7px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}

/* DROPDOWN */
.dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 45px;
    width: 220px;
    background: #fff;
    color: #333;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.dropdown a {
    display: block;
    padding: 12px;
    text-decoration: none;
    color: #333;
}

.dropdown a:hover {
    background: #f1f8e9;
}

.dropdown .logout {
    color: red;
    font-weight: bold;
}

/* HAMBURGER */
.menu-toggle {
    font-size: 26px;
    cursor: pointer;
    display: none;
}

/* MOBILE */
@media(max-width:768px){

    .menu-toggle {
        display: block;
    }

    .header-inner {
        padding: 10px 12px;
    }

    .right-group {
        gap: 10px;
    }
}

</style>

<div class="header">
    <div class="header-inner">

        <!-- LOGO -->
        <a href="<?= $base ?>index.php" class="logo">
            <img src="<?= $base ?>assets/images/logo.png">
            <h2>🌿 Hani Nursery and Gardening</h2>
        </a>

        <!-- RIGHT -->
        <div class="right-group">

            <!-- CART (FIXED COUNT) -->
            <a class="cart" href="<?= $base ?>pages/cart.php">
                🛒 <?= $count ?>
            </a>

            <!-- PROFILE -->
            <?php if(isset($_SESSION['user'])){ ?>

            <div class="profile">
                <div class="profile-btn" onclick="toggleProfile()">
                    👤
                </div>

                <div class="dropdown" id="dropdown">
                    <a href="<?= $base ?>pages/account/dashboard.php">My Account</a>
                    <a href="<?= $base ?>pages/account/orders.php">My Orders</a>
                    <a href="<?= $base ?>pages/account/wishlist.php">Wishlist</a>
                    <a href="<?= $base ?>pages/account/settings.php">Settings</a>
                    <a href="<?= $base ?>logout.php" class="logout">Logout</a>
                </div>
            </div>

            <?php } else { ?>

            <a class="cart" href="<?= $base ?>pages/login.php">👤 Login</a>

            <?php } ?>

            <!-- MENU -->
            <div class="menu-toggle" onclick="toggleMenu()">☰</div>

        </div>

    </div>
</div>

<script>

function toggleMenu(){
    alert("Menu can be upgraded to sidebar later 👍");
}

function toggleProfile(){
    let d = document.getElementById("dropdown");
    d.style.display = (d.style.display === "block") ? "none" : "block";
}

document.addEventListener("click", function(e){
    let box = document.querySelector(".profile");
    let drop = document.getElementById("dropdown");

    if(box && !box.contains(e.target)){
        if(drop) drop.style.display = "none";
    }
});

</script>