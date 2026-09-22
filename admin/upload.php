<?php
session_start();
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $img = $_FILES['image'];
    $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
    
    // Validate extension
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file extension']);
        exit();
    }
    
    $newName = md5(time() . rand()) . '.' . $ext;
    
    $uploadDir = '../../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    if (move_uploaded_file($img['tmp_name'], $uploadDir . $newName)) {
        // Return absolute URL from domain root
        echo json_encode(['success' => true, 'url' => '/uploads/' . $newName]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
    }
    exit();
}
echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
