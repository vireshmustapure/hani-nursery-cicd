<?php
session_start();
include '../../../config/db.php';

if(!isset($_SESSION['user'])){
    header("Location: ../../login/");
    exit();
}

$user_id = intval($_SESSION['user']);

$orders = mysqli_query($conn, "
    SELECT * FROM orders
    WHERE user_id='$user_id'
    ORDER BY id DESC
");

$base = "../../../";
include '../../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Orders - Hani Nursery</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .orders-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px 60px;
        }

        .orders-title {
            font-size: 32px;
            color: var(--primary-dark);
            margin-bottom: 30px;
            border-bottom: 2px solid #f4faf4;
            padding-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .order-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--shadow-sm);
            border: 1px solid #eee;
            transition: var(--transition-smooth);
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-light);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f5f5f5;
            padding-bottom: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .order-id {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .order-date {
            font-size: 14px;
            color: var(--text-muted);
        }

        .product-row {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: var(--radius-sm);
            border: 1px solid #eee;
        }

        .product-details {
            flex: 1;
            min-width: 200px;
        }

        .product-name {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
            text-decoration: none;
            transition: var(--transition-smooth);
        }

        .product-name:hover {
            color: var(--primary-color);
        }

        .price-qty {
            margin-top: 8px;
            font-size: 16px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .price-qty span {
            color: var(--primary-color);
            font-weight: 700;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f5f5f5;
            flex-wrap: wrap;
            gap: 15px;
        }

        .order-total {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
        }

        .order-total span {
            color: var(--primary-color);
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: var(--radius-full);
            font-size: 13px;
            font-weight: 700;
            text-transform: capitalize;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-pending { background: #fff8e1; color: #f57f17; }
        .status-confirmed { background: #e8eaf6; color: #3f51b5; }
        .status-packed { background: #e0f2f1; color: #004d40; }
        .status-shipped { background: #e1f5fe; color: #01579b; }
        .status-outfordelivery { background: #ede7f6; color: #4a148c; }
        .status-delivered { background: #e8f5e9; color: #1b5e20; }
        .status-cancelled { background: #ffebee; color: #b71c1c; }

        .view-btn {
            background: var(--primary-color);
            color: white !important;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-sm);
        }

        .view-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .empty-orders {
            background: white;
            padding: 50px;
            border-radius: var(--radius-md);
            text-align: center;
            box-shadow: var(--shadow-sm);
            border: 1px solid #eee;
        }
    </style>
</head>
<body>

<div class="orders-container">

    <div class="orders-title">
        <i data-lucide="package"></i> My Orders
    </div>

    <?php if(mysqli_num_rows($orders) > 0){ ?>

        <?php while($order = mysqli_fetch_assoc($orders)){ 
            $order_id = $order['id'];
            $items = mysqli_query($conn, "
                SELECT order_items.*, products.image, products.name
                FROM order_items
                LEFT JOIN products ON order_items.product_id = products.id
                WHERE order_items.order_id='$order_id'
            ");
            
            // Status CSS class mapping
            $status_clean = str_replace(' ', '', strtolower(trim($order['order_status'])));
            if (empty($status_clean)) $status_clean = 'pending';
        ?>
            <div class="order-card">
                <div class="order-header">
                    <span class="order-id">Order #<?php echo $order_id; ?></span>
                    <span class="order-date">Ordered on: <?php echo date("d M Y", strtotime($order['created_at'])); ?></span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php while($item = mysqli_fetch_assoc($items)){ ?>
                        <div class="product-row">
                            <img src="../../../uploads/<?php echo htmlspecialchars($item['image']); ?>" onerror="this.src='https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=100&h=100&q=80'" class="product-image">
                            <div class="product-details">
                                <a href="../../product/?id=<?php echo $item['product_id']; ?>" class="product-name">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </a>
                                <div class="price-qty">
                                    Price: <span>₹<?php echo number_format($item['price']); ?></span> &nbsp;|&nbsp; Qty: <span><?php echo $item['quantity']; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="order-footer">
                    <div class="order-total">
                        Total Paid: <span>₹<?php echo number_format($order['total_amount']); ?></span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <span class="status-badge status-<?php echo $status_clean; ?>">
                            <i data-lucide="circle-dot" style="width: 14px; height: 14px;"></i>
                            <?php echo htmlspecialchars($order['order_status']); ?>
                        </span>
                        <a class="view-btn" href="../order_view/?id=<?php echo $order_id; ?>">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php } else { ?>

        <div class="empty-orders">
            <i data-lucide="shopping-bag" style="font-size: 48px; color: var(--primary-color); margin-bottom: 15px;"></i>
            <h3>No Orders Found</h3>
            <p style="color:#777; margin-bottom: 25px;">You have not placed any orders yet. Grow your garden today!</p>
            <a href="../../shop/" class="btn-premium" style="text-shadow:none;">Browse Plants</a>
        </div>

    <?php } ?>

</div>

<?php include '../../../includes/footer.php'; ?>

</body>
</html>