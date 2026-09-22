<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: ../login.php");
    exit();
}
include("../config/db.php");
?>

<style>
body{font-family:Segoe UI;background:#f5f7f6;margin:0;}
.header{background:#1b5e20;color:white;padding:15px;text-align:center;}
.item{
    background:#fff;
    padding:15px;
    margin:10px;
    border-radius:10px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
}
</style>

<div class="header">🌱 Wishlist</div>

<div>

<?php
$user = $_SESSION['user'];
$sql = "SELECT * FROM wishlist WHERE user='$user'";
$res = $conn->query($sql);

while($row = $res->fetch_assoc()){
?>

<div class="item">
    🌿 <?php echo $row['product_name']; ?>
    <a href="remove_wishlist.php?id=<?php echo $row['id']; ?>">Remove</a>
</div>

<?php } ?>

</div>