<?php
session_start();
include '../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $uid = intval($_SESSION['user']);
    $pid = intval($_POST['product_id']);
    
    // Check if product exists
    $prod_check = mysqli_query($conn, "SELECT id FROM products WHERE id=$pid");
    if (mysqli_num_rows($prod_check) == 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit();
    }
    
    // Check if already in wishlist
    $wish_check = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id=$uid AND product_id=$pid");
    
    if (mysqli_num_rows($wish_check) > 0) {
        // Remove from wishlist
        if (mysqli_query($conn, "DELETE FROM wishlist WHERE user_id=$uid AND product_id=$pid")) {
            echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Removed from wishlist.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error.']);
        }
    } else {
        // Add to wishlist
        if (mysqli_query($conn, "INSERT INTO wishlist (user_id, product_id) VALUES ($uid, $pid)")) {
            echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Added to wishlist.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error.']);
        }
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
?>
