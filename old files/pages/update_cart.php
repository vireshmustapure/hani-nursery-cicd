<?php
session_start();

/* SAFE CHECKS */
if(!isset($_POST['id']) || !isset($_POST['action'])){
    header("Location: cart.php");
    exit();
}

$id = (int)$_POST['id'];
$action = $_POST['action'];

/* INIT CART IF NOT EXISTS */
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

/* INIT PRODUCT IF NOT SET */
if(!isset($_SESSION['cart'][$id])){
    $_SESSION['cart'][$id] = 1;
}

/* INCREASE */
if($action == "plus"){
    $_SESSION['cart'][$id] += 1;
}

/* DECREASE */
if($action == "minus"){
    if($_SESSION['cart'][$id] > 1){
        $_SESSION['cart'][$id] -= 1;
    } else {
        unset($_SESSION['cart'][$id]);
    }
}

header("Location: cart.php");
exit();
?>