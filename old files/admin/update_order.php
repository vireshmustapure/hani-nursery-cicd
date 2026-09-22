<?php
include '../config.php';

session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
$status = $_GET['status'] ?? '';

if($id == 0 || $status == ''){
    die("Invalid Request");
}

$allowed = ['Dispatched','Delivered'];

if(!in_array($status,$allowed)){
    die("Invalid Status");
}

// STEP 1: GET CURRENT STATUS
$order = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT order_status FROM orders WHERE id='$id'")
);

if(!$order){
    die("Order not found");
}

$current = $order['order_status'];

/*
WORKFLOW RULE:
Pending → Dispatched → Delivered only
*/

if($status == "Dispatched" && $current != "Pending"){
    die("Not allowed");
}

if($status == "Delivered" && $current != "Dispatched"){
    die("Not allowed");
}

// STEP 2: UPDATE STATUS
mysqli_query($conn,"UPDATE orders SET order_status='$status' WHERE id='$id'");

// STEP 3: REDIRECT BACK
header("Location: view-order.php?id=$id");
exit();
?>