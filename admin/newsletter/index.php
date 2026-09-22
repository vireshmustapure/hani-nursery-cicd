<?php
include '../auth_check.php';
include '../../config/db.php';

$msg = "";

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (mysqli_query($conn, "DELETE FROM newsletter_subscriptions WHERE id=$id")) {
        $msg = "Subscription deleted successfully ✅";
    } else {
        $msg = "Error deleting subscription ❌";
    }
}

// Fetch all subscriptions
$result = mysqli_query($conn, "SELECT * FROM newsletter_subscriptions ORDER BY email DESC");
$total_subscribers = $result ? mysqli_num_rows($result) : 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Newsletter Subscriptions</title>
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

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    background: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border-radius: 10px;
    overflow: hidden;
}

table th, table td {
    padding: 14px 18px;
    border-bottom: 1px solid #eee;
    text-align: left;
}

table th {
    background: #e8f5e9;
    color: #1b5e20;
    font-weight: bold;
}

table tr:hover {
    background: #fafafa;
}

.delete-btn {
    background: #f8d7da;
    color: #721c24;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    font-size: 13px;
    transition: 0.2s;
}

.delete-btn:hover {
    background: #f5c6cb;
}
</style>
</head>
<body>

<?php include __DIR__ . '/../sidebar.php'; ?>

<div class="main">
    <div class="topbar">
        <h1>📧 Newsletter Subscriptions</h1>
        <p>Total Subscriptions: <b><?php echo $total_subscribers; ?></b></p>
    </div>

    <?php if ($msg != "") { ?>
        <div class="msg"><?php echo $msg; ?></div>
    <?php } ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Email Address</th>
                <th>Subscribed At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($total_subscribers > 0) {
                while($row = mysqli_fetch_assoc($result)) { 
            ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><b><?php echo htmlspecialchars($row['email']); ?></b></td>
                    <td><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                    <td>
                        <a href="?action=delete&id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Remove this email from subscriptions?')">Delete</a>
                    </td>
                </tr>
            <?php 
                } 
            } else {
            ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: #777; padding: 30px;">No subscribers found yet.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
