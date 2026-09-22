<?php
session_start();
include '../../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    
    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit();
    }
    
    // Check if email already exists
    $email_esc = mysqli_real_escape_string($conn, $email);
    $check = mysqli_query($conn, "SELECT id FROM newsletter_subscriptions WHERE email='$email_esc'");
    
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['success' => true, 'message' => 'You are already subscribed to our newsletter!']);
        exit();
    }
    
    // Insert into database
    $q = "INSERT INTO newsletter_subscriptions (email) VALUES ('$email_esc')";
    if (mysqli_query($conn, $q)) {
        echo json_encode(['success' => true, 'message' => 'Thank you for subscribing to our newsletter! 🌿']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Subscription failed. Please try again later.']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
?>
