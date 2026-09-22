<?php
include '../config/db.php';

if(isset($_POST['update'])){

    $id = $_POST['order_id'];
    $status = $_POST['status'];

    mysqli_query($conn,"
    UPDATE orders
    SET order_status='$status'
    WHERE id='$id'
    ");

    header("Location: orders.php");
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Update Order</title>

<style>

body{
    font-family:Arial;
    background:#f1f3f6;
    padding:40px;
}

.box{
    width:400px;
    background:white;
    padding:30px;
    margin:auto;
    border-radius:10px;
}

select,
input,
button{
    width:100%;
    padding:12px;
    margin-top:15px;
}

button{
    background:#2874f0;
    color:white;
    border:none;
    cursor:pointer;
}

</style>

</head>

<body>

<div class="box">

<h2>Update Order Status</h2>

<form method="POST">

<input type="number"
name="order_id"
placeholder="Enter Order ID"
required>

<select name="status">

<option value="Pending">
Pending
</option>

<option value="Confirmed">
Confirmed
</option>

<option value="Packed">
Packed
</option>

<option value="Shipped">
Shipped
</option>

<option value="Out For Delivery">
Out For Delivery
</option>

<option value="Delivered">
Delivered
</option>

<option value="Cancelled">
Cancelled
</option>

</select>

<button type="submit"
name="update">
Update Order
</button>

</form>

</div>

</body>
</html>