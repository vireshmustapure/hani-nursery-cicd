<?php
session_start();
include '../../../config/db.php';

if(!isset($_SESSION['user'])){
    header("Location: ../../login/");
    exit();
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = intval($_SESSION['user']);

$order_query = mysqli_query($conn,"
    SELECT * FROM orders
    WHERE id='$order_id' AND user_id='$user_id'
");

if(mysqli_num_rows($order_query) == 0){
    die("Order not found");
}

$order = mysqli_fetch_assoc($order_query);
$current = trim($order['order_status']);

$items = mysqli_query($conn,"
    SELECT order_items.*, products.image, products.name
    FROM order_items
    LEFT JOIN products ON order_items.product_id = products.id
    WHERE order_items.order_id='$order_id'
");

function activeStep($step, $current){
    $steps = [
        'Pending' => 1,
        'Confirmed' => 2,
        'Packed' => 3,
        'Shipped' => 4,
        'Out For Delivery' => 5,
        'Delivered' => 6
    ];
    
    // Safety check in case of status mismatch
    $step_val = isset($steps[$step]) ? $steps[$step] : 1;
    $curr_val = isset($steps[$current]) ? $steps[$current] : 1;
    
    return $step_val <= $curr_val;
}

$base = "../../../";
include '../../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Detail #<?php echo $order_id; ?> - Hani Nursery</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .ordview-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px 60px;
        }

        .back-btn-box {
            margin-bottom: 25px;
        }

        .ordview-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-sm);
            border: 1px solid #eee;
        }

        .ordview-title {
            font-size: 26px;
            color: var(--primary-dark);
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ordview-meta {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .product-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 20px;
        }

        .product-item {
            display: flex;
            gap: 20px;
            align-items: center;
            border-bottom: 1px solid #f9f9f9;
            padding-bottom: 15px;
        }

        .product-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .product-img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: var(--radius-sm);
            border: 1px solid #eee;
        }

        .product-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            text-decoration: none;
            transition: var(--transition-smooth);
        }

        .product-name:hover {
            color: var(--primary-color);
        }

        .product-info {
            flex: 1;
        }

        .product-price-qty {
            margin-top: 5px;
            color: var(--text-muted);
            font-size: 15px;
        }

        .product-price-qty span {
            color: var(--primary-color);
            font-weight: 700;
        }

        /* Timeline Tracker */
        .timeline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 40px 0 20px;
            position: relative;
            flex-wrap: wrap;
            gap: 20px;
        }

        @media(max-width: 768px) {
            .timeline {
                flex-direction: column;
                align-items: flex-start;
                padding-left: 30px;
                gap: 30px;
            }

            .timeline::before {
                content: '';
                position: absolute;
                left: 10px;
                top: 0;
                width: 4px;
                height: 100%;
                background: #e2e8f0;
            }

            .timeline-step {
                flex-direction: row !important;
                gap: 15px !important;
                text-align: left !important;
            }
        }

        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            z-index: 2;
            text-align: center;
            gap: 10px;
        }

        .timeline-circle {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-full);
            background: #e2e8f0;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: var(--transition-smooth);
            border: 3px solid white;
            box-shadow: 0 0 0 2px #e2e8f0;
        }

        .timeline-label {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            transition: var(--transition-smooth);
        }

        .timeline-step.active .timeline-circle {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 0 0 2px var(--primary-color);
        }

        .timeline-step.active .timeline-label {
            color: var(--primary-dark);
            font-weight: 700;
        }

        .address-box {
            line-height: 1.8;
            color: var(--text-dark);
            font-size: 15px;
        }
    </style>
</head>
<body>

<div class="ordview-container">

    <!-- Back Button -->
    <div class="back-btn-box">
        <a href="../orders/" class="btn-premium" style="padding: 8px 18px; font-size: 13px; text-shadow: none; box-shadow: var(--shadow-sm);">
            ⬅ Back to Orders
        </a>
    </div>

    <!-- Order Header Card -->
    <div class="ordview-card">
        <h2 class="ordview-title">Order #<?php echo $order['id']; ?></h2>
        <div class="ordview-meta">
            <span>📅 Placed on: <b><?php echo date("d M Y H:i", strtotime($order['created_at'])); ?></b></span>
            <span>💳 Payment: <b><?php echo htmlspecialchars($order['payment_method'] ?? 'Cash on Delivery'); ?></b></span>
            <span>💰 Status: <b><?php echo htmlspecialchars($order['order_status']); ?></b></span>
        </div>
    </div>

    <!-- Timeline Tracking -->
    <div class="ordview-card">
        <h3><i data-lucide="truck" style="vertical-align: middle; margin-right: 5px;"></i> Delivery Tracking</h3>
        
        <div class="timeline">
            <?php
            $steps = [
                ['Pending', 'shopping-bag', 'Placed'],
                ['Confirmed', 'check-circle', 'Confirmed'],
                ['Packed', 'box', 'Packed'],
                ['Shipped', 'truck', 'Shipped'],
                ['Out For Delivery', 'navigation', 'Out For Delivery'],
                ['Delivered', 'home', 'Delivered']
            ];
            foreach ($steps as $st) {
                $isAct = activeStep($st[0], $current);
            ?>
                <div class="timeline-step <?php echo $isAct ? 'active' : ''; ?>">
                    <div class="timeline-circle">
                        <i data-lucide="<?php echo $st[1]; ?>" style="width: 16px; height: 16px;"></i>
                    </div>
                    <span class="timeline-label"><?php echo $st[2]; ?></span>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Products Card -->
    <div class="ordview-card">
        <h3><i data-lucide="shopping-cart" style="vertical-align: middle; margin-right: 5px;"></i> Items Ordered</h3>
        <div class="product-list">
            <?php while($item = mysqli_fetch_assoc($items)){ ?>
                <div class="product-item">
                    <img src="../../../uploads/<?php echo htmlspecialchars($item['image']); ?>" onerror="this.src='https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=80&h=80&q=80'" class="product-img">
                    <div class="product-info">
                        <a href="../../product/?id=<?php echo $item['product_id']; ?>" class="product-name">
                            <?php echo htmlspecialchars($item['name']); ?>
                        </a>
                        <div class="product-price-qty">
                            Price: <span>₹<?php echo number_format($item['price']); ?></span> &nbsp;|&nbsp; Quantity: <span><?php echo $item['quantity']; ?></span>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div style="text-align: right; margin-top: 20px; font-size: 20px; font-weight: 800; border-top: 1px solid #eee; padding-top: 15px;">
            Total Paid: <span style="color: var(--primary-color);">₹<?php echo number_format($order['total_amount']); ?></span>
        </div>
    </div>

    <!-- Address Details Card -->
    <div class="ordview-card">
        <h3><i data-lucide="map-pin" style="vertical-align: middle; margin-right: 5px;"></i> Delivery Address</h3>
        <div class="address-box" style="margin-top: 15px;">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name'] ?? ''); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone'] ?? ''); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address'] ?? ''); ?></p>
            <p><strong>City/State:</strong> <?php echo htmlspecialchars($order['city'] ?? '') . ', ' . htmlspecialchars($order['state'] ?? '') . ' - ' . htmlspecialchars($order['pincode'] ?? ''); ?></p>
            <?php if(!empty($order['landmark'])){ ?>
                <p><strong>Landmark:</strong> <?php echo htmlspecialchars($order['landmark']); ?></p>
            <?php } ?>
        </div>
    </div>

</div>

<?php include '../../../includes/footer.php'; ?>

</body>
</html>