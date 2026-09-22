<?php
include '../config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

$orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Orders</title>

<style>
body{
    font-family:Arial;
    background:#f4f6f9;
}

.container{
    width:95%;
    margin:20px auto;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
}

h2{
    color:#2e7d32;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #ddd;
}

th{
    background:#2e7d32;
    color:white;
}

.btn{
    padding:6px 12px;
    background:#2e7d32;
    color:white;
    text-decoration:none;
    border-radius:5px;
}

/* STATUS BADGE */
.badge{
    padding:5px 10px;
    border-radius:6px;
    color:black;
    font-weight:bold;
}

.back-btn{
    display:inline-block;
    margin-bottom:10px;
    padding:8px 12px;
    background:#444;
    color:white;
    text-decoration:none;
    border-radius:5px;
}
</style>

</head>

<body>

<div class="container">

<a href="dashboard.php" class="back-btn">⬅ Back</a>

<h2>Orders List</h2>

<table>
<tr>
    <th>Order ID</th>
    <th>Customer</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($orders)){

$status = $row['order_status'];
?>

<tr>
    <td>#<?php echo $row['id']; ?></td>

    <td>
        <?php echo $row['customer_name'] ?? 'Guest'; ?><br>
        <?php echo $row['phone'] ?? ''; ?>
    </td>

    <td>₹<?php echo $row['total_amount']; ?></td>

    <td>
        <span class="badge"
        style="background:
        <?php 
       if($status=='Pending'){
    echo '#ffcc00';
}
elseif($status=='Confirmed'){
    echo '#28a745';
}
elseif($status=='Dispatched'){
    echo '#ff9800';
}
elseif($status=='Delivered'){
    echo '#00c851';
}
else{
    echo '#cccccc';
}
        ?>">
        <?php echo $status; ?>
        </span>
    </td>

    <td><?php echo $row['order_date'] ?? ''; ?></td>

    <td>
        <a class="btn"
           href="view-order.php?id=<?php echo $row['id']; ?>">
            View
        </a>
    </td>
</tr>

<?php } ?>

</table>

</div>

</body>
</html>