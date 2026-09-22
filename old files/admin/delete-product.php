<?php
include __DIR__ . '/../config.php';

if(!isset($_SESSION['admin'])){
header("Location: login.php");
exit();
}

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM products WHERE id='$id'");

header("Location: products.php");
?>