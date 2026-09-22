if(isset($_POST['product_id'])){

    $pid = (int)$_POST['product_id'];
    $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

    if($qty < 1){
        $qty = 1;
    }

    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = [];
    }

    if(!isset($_SESSION['cart'][$pid])){
        $_SESSION['cart'][$pid] = 0;
    }

    $_SESSION['cart'][$pid] += $qty;

    // IMPORTANT: go back to same page (NOT ../cart/index.php)
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}