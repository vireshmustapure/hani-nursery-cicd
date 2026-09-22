<?php
session_start();
include '../../config/db.php';
header('Content-Type: application/json');

if (isset($_POST['action'])) {
    if ($_POST['action'] === 'apply') {
        $code = mysqli_real_escape_string($conn, strtoupper(trim($_POST['code'])));
        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a coupon code.']);
            exit();
        }
        $res = mysqli_query($conn, "SELECT * FROM coupons WHERE code='$code' AND status='active' LIMIT 1");
        if ($res && $cp = mysqli_fetch_assoc($res)) {
            $_SESSION['coupon'] = $cp['code'];
            echo json_encode(['success' => true, 'message' => 'Coupon applied successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid or inactive coupon code.']);
        }
        exit();
    }
    if ($_POST['action'] === 'remove') {
        unset($_SESSION['coupon']);
        echo json_encode(['success' => true, 'message' => 'Coupon removed successfully.']);
        exit();
    }
}
echo json_encode(['success' => false, 'message' => 'Invalid action.']);
?>
