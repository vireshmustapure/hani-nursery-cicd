<?php
include '../auth_check.php';
include '../../config/db.php';

$msg = "";

// Handle Actions (Approve/Delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] === 'approve') {
        if (mysqli_query($conn, "UPDATE reviews SET status='approved' WHERE id=$id")) {
            $msg = "Review approved successfully! ✅";
        }
    } elseif ($_GET['action'] === 'delete') {
        if (mysqli_query($conn, "DELETE FROM reviews WHERE id=$id")) {
            $msg = "Review deleted successfully! ✅";
        }
    }
}

// Handle Add Review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    $pid = intval($_POST['product_id']);
    $user_name = mysqli_real_escape_string($conn, trim($_POST['user_name']));
    $rating = intval($_POST['rating']);
    $review_text = mysqli_real_escape_string($conn, trim($_POST['review_text']));
    
    if ($pid > 0 && !empty($user_name) && $rating >= 1 && $rating <= 5 && !empty($review_text)) {
        $q = "INSERT INTO reviews (product_id, user_name, rating, review_text, status) 
              VALUES ($pid, '$user_name', $rating, '$review_text', 'approved')";
        if (mysqli_query($conn, $q)) {
            $msg = "Review added successfully! ✅";
        } else {
            $msg = "Error adding review: " . mysqli_error($conn);
        }
    } else {
        $msg = "Please fill in all fields correctly! ❌";
    }
}

// Fetch all reviews joined with products
$result_reviews = mysqli_query($conn, "
    SELECT reviews.*, products.name AS product_name 
    FROM reviews 
    LEFT JOIN products ON reviews.product_id = products.id 
    ORDER BY reviews.created_at DESC
");

// Fetch all products for dropdown
$result_products = mysqli_query($conn, "SELECT id, name FROM products ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Reviews</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
    font-family: Arial, sans-serif;
    background: #eef7ee;
    margin: 0;
    padding: 0;
}

.main {
    margin-left: 240px;
    padding: 30px;
    min-height: 100vh;
    background: white;
}

.topbar {
    background: #f4faf4;
    padding: 20px;
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.04);
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.topbar h1 {
    color: #1b5e20;
    font-size: 28px;
    margin: 0;
}

.msg {
    background: #d4edda;
    color: #155724;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-weight: bold;
}

.flex-container {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.flex-left {
    flex: 2;
    min-width: 500px;
}

.flex-right {
    flex: 1;
    min-width: 300px;
}

.card {
    background: #ffffff;
    border-radius: 14px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border: 1px solid #eee;
    margin-bottom: 25px;
}

.card h3 {
    margin-top: 0;
    color: #1b5e20;
    border-bottom: 1px solid #eee;
    padding-bottom: 12px;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

table th, table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    text-align: left;
    font-size: 14px;
}

table th {
    background: #e8f5e9;
    color: #1b5e20;
    font-weight: bold;
}

table tr:hover {
    background: #fafafa;
}

.approve-btn {
    background: #d4edda;
    color: #155724;
    padding: 5px 10px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    font-size: 12px;
    margin-right: 5px;
}

.delete-btn {
    background: #f8d7da;
    color: #721c24;
    padding: 5px 10px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    font-size: 12px;
}

.status-badge {
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: bold;
}

.status-approved {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-weight: bold;
    color: #1b5e20;
    margin-bottom: 5px;
    font-size: 14px;
}

.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    outline: none;
}

.submit-btn {
    background: #2e7d32;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 15px;
    cursor: pointer;
    width: 100%;
    transition: 0.2s;
}

.submit-btn:hover {
    background: #1b5e20;
}
</style>
</head>
<body>

<?php include __DIR__ . '/../sidebar.php'; ?>

<div class="main">
    <div class="topbar">
        <h1>⭐ Customer Reviews Manager</h1>
    </div>

    <?php if ($msg != "") { ?>
        <div class="msg"><?php echo $msg; ?></div>
    <?php } ?>

    <div class="flex-container">
        
        <!-- Left: Reviews Table -->
        <div class="flex-left">
            <div class="card">
                <h3>📝 Submitted Reviews</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Reviewer</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($result_reviews) > 0) {
                            while($row = mysqli_fetch_assoc($result_reviews)) { 
                        ?>
                            <tr>
                                <td><b><?php echo htmlspecialchars($row['product_name'] ?? 'Unknown'); ?></b></td>
                                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td style="color: #ffd700; font-weight: bold;">
                                    <?php echo str_repeat("⭐", $row['rating']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['review_text']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo ($row['status'] === 'approved') ? 'status-approved' : 'status-pending'; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'pending') { ?>
                                        <a href="?action=approve&id=<?php echo $row['id']; ?>" class="approve-btn">Approve</a>
                                    <?php } ?>
                                    <a href="?action=delete&id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Delete this review?')">Delete</a>
                                </td>
                            </tr>
                        <?php 
                            } 
                        } else {
                        ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #777; padding: 20px;">No reviews found.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Right: Add Good Review Form -->
        <div class="flex-right">
            <div class="card">
                <h3>✍️ Add Direct Review</h3>
                <form method="POST">
                    
                    <div class="form-group">
                        <label for="product_id">Select Product:</label>
                        <select id="product_id" name="product_id" required>
                            <option value="">-- Choose Plant --</option>
                            <?php 
                            mysqli_data_seek($result_products, 0);
                            while ($prod = mysqli_fetch_assoc($result_products)) { 
                            ?>
                                <option value="<?php echo $prod['id']; ?>"><?php echo htmlspecialchars($prod['name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="user_name">Reviewer Name:</label>
                        <input type="text" id="user_name" name="user_name" placeholder="e.g., Ramesh K." required>
                    </div>

                    <div class="form-group">
                        <label for="rating">Rating Star Count:</label>
                        <select id="rating" name="rating" required>
                            <option value="5">⭐⭐⭐⭐⭐ (5 Stars)</option>
                            <option value="4">⭐⭐⭐⭐ (4 Stars)</option>
                            <option value="3">⭐⭐⭐ (3 Stars)</option>
                            <option value="2">⭐⭐ (2 Stars)</option>
                            <option value="1">⭐ (1 Star)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="review_text">Review Comment:</label>
                        <textarea id="review_text" name="review_text" rows="4" placeholder="Write the positive comment..." required></textarea>
                    </div>

                    <button type="submit" name="add_review" class="submit-btn">Add Review</button>

                </form>
            </div>
        </div>

    </div>
</div>

</body>
</html>
