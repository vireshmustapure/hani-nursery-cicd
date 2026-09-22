<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: ../login.php");
    exit();
}
$base = "../";
?>

<style>
body{font-family:Segoe UI;margin:0;background:#f5f7f6;}
.container{padding:20px;}

.card-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:15px;
}

.card{
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
    text-align:center;
    text-decoration:none;
    color:#333;
    transition:0.2s;
}

.card:hover{transform:scale(1.03);}

.header{
    background:#1b5e20;
    color:white;
    padding:15px;
    text-align:center;
}
</style>

<div class="header">
    👤 My Account
</div>

<div class="container">

<h3>Welcome, <?php echo $_SESSION['username']; ?></h3>

<div class="card-grid">

<a class="card" href="orders.php">📦 My Orders</a>
<a class="card" href="wishlist.php">🌱 Wishlist</a>
<a class="card" href="settings.php">⚙️ Settings</a>
<a class="card" href="../logout.php">🚪 Logout</a>

</div>
</div>