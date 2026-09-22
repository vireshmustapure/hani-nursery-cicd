<?php
session_start();
include '../../../config/db.php';

if(!isset($_SESSION['user'])){
    header("Location: ../../login/");
    exit();
}

$user_id = intval($_SESSION['user']);

// Fetch user info
$user_q = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$user = mysqli_fetch_assoc($user_q);

// Fetch stats
$orders_cnt_q = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE user_id=$user_id");
$orders_cnt = mysqli_fetch_assoc($orders_cnt_q)['count'] ?? 0;

$wishlist_cnt_q = mysqli_query($conn, "SELECT COUNT(*) as count FROM wishlist WHERE user_id=$user_id");
$wishlist_cnt = mysqli_fetch_assoc($wishlist_cnt_q)['count'] ?? 0;

// Fetch addresses count
$address_cnt_q = mysqli_query($conn, "SELECT COUNT(*) as count FROM addresses WHERE user_id=$user_id");
$address_cnt = mysqli_fetch_assoc($address_cnt_q)['count'] ?? 0;

$base = "../../../";
include '../../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Account Dashboard - Hani Nursery</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px 60px;
        }

        .welcome-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 30px;
            border-radius: var(--radius-md);
            margin-bottom: 30px;
            box-shadow: var(--shadow-md);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .welcome-text h2 {
            font-size: 28px;
            margin: 0 0 10px 0;
        }

        .welcome-text p {
            margin: 0;
            color: #e8f5e9;
            font-size: 15px;
        }

        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: var(--transition-smooth);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: var(--radius-full);
            background: #e8f5e9;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-info h4 {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
        }

        .stat-info p {
            margin: 5px 0 0 0;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        @media(max-width: 992px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        .dashboard-card {
            background: white;
            padding: 30px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid #eee;
            margin-bottom: 30px;
        }

        .dashboard-card h3 {
            margin: 0 0 20px 0;
            font-size: 20px;
            color: var(--primary-dark);
            border-bottom: 2px solid #f4faf4;
            padding-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .action-btn {
            background: white;
            padding: 25px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid #eee;
            text-align: center;
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 600;
            transition: var(--transition-smooth);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .action-btn i {
            font-size: 28px;
            color: var(--primary-color);
            transition: var(--transition-smooth);
        }

        .action-btn:hover {
            border-color: var(--primary-light);
            background: #fcfdfe;
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .action-btn:hover i {
            transform: scale(1.1);
        }

        .profile-detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f9f9f9;
        }

        .profile-detail-row:last-child {
            border-bottom: none;
        }

        .profile-detail-label {
            font-weight: 600;
            color: var(--text-dark);
        }

        .profile-detail-value {
            color: var(--text-muted);
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    
    <!-- Welcome Header -->
    <div class="welcome-header">
        <div class="welcome-text">
            <h2 style="display:flex; align-items:center; gap:8px;">Welcome back, <?php echo htmlspecialchars($user['name'] ?? $_SESSION['username']); ?>! <i data-lucide="sprout" style="width:28px; height:28px;"></i></h2>
            <p>Manage your account, track orders, and update your delivery addresses.</p>
        </div>
        <a href="../settings/" class="btn-premium" style="background: white; color: var(--primary-dark) !important; text-shadow: none; border-color: white;">
            Edit Profile
        </a>
    </div>

    <!-- Stats Summary Row -->
    <div class="stats-summary">
        <div class="stat-card">
            <div class="stat-icon"><i data-lucide="package"></i></div>
            <div class="stat-info">
                <h4>Total Orders</h4>
                <p><?php echo $orders_cnt; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ffebee; color:#e53935;"><i data-lucide="heart"></i></div>
            <div class="stat-info">
                <h4>Wishlist Items</h4>
                <p><?php echo $wishlist_cnt; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8eaf6; color:#3f51b5;"><i data-lucide="map-pin"></i></div>
            <div class="stat-info">
                <h4>Saved Addresses</h4>
                <p><?php echo $address_cnt; ?></p>
            </div>
        </div>
    </div>

    <!-- Grid Layout -->
    <div class="dashboard-grid">
        
        <!-- Left: Quick Actions -->
        <div>
            <div class="dashboard-card">
                <h3><i data-lucide="layout-dashboard"></i> Account Panel</h3>
                <div class="quick-actions">
                    <a class="action-btn" href="../orders/">
                        <i data-lucide="package"></i>
                        <span>My Orders</span>
                    </a>
                    <a class="action-btn" href="../wishlist/">
                        <i data-lucide="heart"></i>
                        <span>Wishlist</span>
                    </a>
                    <a class="action-btn" href="../settings/">
                        <i data-lucide="settings"></i>
                        <span>Account Settings</span>
                    </a>
                    <a class="action-btn" href="../../../logout/" style="color: var(--accent-red);">
                        <i data-lucide="log-out" style="color: var(--accent-red);"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right: Profile Info -->
        <div>
            <div class="dashboard-card">
                <h3><i data-lucide="user"></i> Profile Information</h3>
                <div class="profile-detail-row">
                    <span class="profile-detail-label">Name:</span>
                    <span class="profile-detail-value"><?php echo htmlspecialchars($user['name'] ?? ''); ?></span>
                </div>
                <div class="profile-detail-row">
                    <span class="profile-detail-label">Email:</span>
                    <span class="profile-detail-value"><?php echo htmlspecialchars($user['email'] ?? ''); ?></span>
                </div>
                <div class="profile-detail-row">
                    <span class="profile-detail-label">Mobile:</span>
                    <span class="profile-detail-value"><?php echo htmlspecialchars($user['mobile'] ?? ''); ?></span>
                </div>
            </div>
        </div>

    </div>

</div>

<?php include '../../../includes/footer.php'; ?>

</body>
</html>