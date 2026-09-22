<?php
include '../auth_check.php';
include '../../config/db.php';

$msg = "";

// Delete Inquiry
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {

    $id = intval($_GET['id']);

    if(mysqli_query($conn,"DELETE FROM contact_inquiries WHERE id='$id'")){
        $msg = "Inquiry deleted successfully ✅";
    }else{
        $msg = "Failed to delete inquiry ❌";
    }
}

// Fetch inquiries
$result = mysqli_query($conn,"SELECT * FROM contact_inquiries ORDER BY created_at DESC");

$total_inquiries = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html>
<head>

<title>Contact Inquiries</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<style>

*{
box-sizing:border-box;
}

body{
margin:0;
font-family:Arial,sans-serif;
background:#eef7ee;
}

.main{
margin-left:240px;
padding:30px;
min-height:100vh;
}

.topbar{

background:white;
padding:25px;
border-radius:15px;
display:flex;
justify-content:space-between;
align-items:center;
box-shadow:0 10px 30px rgba(0,0,0,.05);
margin-bottom:25px;

}

.topbar h1{

margin:0;
font-size:30px;
color:#1b5e20;

}

.topbar p{

margin-top:8px;
color:#666;

}

.stats{

display:grid;
grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
gap:20px;
margin-bottom:25px;

}

.card{

background:white;
padding:25px;
border-radius:15px;
box-shadow:0 8px 20px rgba(0,0,0,.05);

}

.card h2{

margin:0;
font-size:38px;
color:#2e7d32;

}

.card span{

color:#666;
font-size:15px;

}

.msg{

background:#d4edda;
padding:15px;
border-radius:10px;
margin-bottom:20px;
color:#155724;
font-weight:bold;

}

.table-box{

background:white;
border-radius:15px;
overflow:auto;
box-shadow:0 8px 20px rgba(0,0,0,.05);

}

table{

width:100%;
border-collapse:collapse;
min-width:1100px;

}

table th{

background:#e8f5e9;
padding:16px;
text-align:left;
color:#1b5e20;
font-size:14px;

}

table td{

padding:16px;
border-bottom:1px solid #eee;
font-size:14px;
vertical-align:top;

}

table tr:hover{

background:#fafafa;

}

.badge{

background:#e8f5e9;
color:#2e7d32;
padding:5px 10px;
border-radius:20px;
font-size:12px;
font-weight:bold;

}

.view-btn{

background:#2e7d32;
color:white;
padding:8px 14px;
text-decoration:none;
border-radius:6px;
font-size:13px;
margin-right:5px;
display:inline-block;

}

.delete-btn{

background:#e53935;
color:white;
padding:8px 14px;
text-decoration:none;
border-radius:6px;
font-size:13px;
display:inline-block;

}

.view-btn:hover{

background:#1b5e20;

}

.delete-btn:hover{

background:#c62828;

}

.message{

max-width:300px;
white-space:nowrap;
overflow:hidden;
text-overflow:ellipsis;

}

@media(max-width:900px){

.main{

margin-left:0;
padding:20px;

}

}

</style>

</head>

<body>

<?php include __DIR__.'/../sidebar.php'; ?>

<div class="main">

<div class="topbar">

<div>

<h1>Contact Inquiries</h1>

<p>View and manage customer inquiries.</p>

</div>

</div>

<?php if($msg!=""){ ?>

<div class="msg"><?php echo $msg; ?></div>

<?php } ?>

<div class="stats">

<div class="card">

<h2><?php echo $total_inquiries; ?></h2>

<span>Total Inquiries</span>

</div>

</div>

<div class="table-box">

<table>

<thead>

<tr>

<th>ID</th>

<th>Name</th>

<th>Email</th>

<th>Mobile</th>

<th>Subject</th>

<th>Message</th>

<th>Date</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if($total_inquiries>0){

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo htmlspecialchars($row['name']); ?></td>

<td><?php echo htmlspecialchars($row['email']); ?></td>

<td><?php echo htmlspecialchars($row['mobile']); ?></td>

<td><?php echo htmlspecialchars($row['subject']); ?></td>

<td class="message">

<?php echo htmlspecialchars($row['message']); ?>

</td>

<td>

<?php echo date("d M Y<br>h:i A",strtotime($row['created_at'])); ?>

</td>

<td>

<span class="badge">New</span>

</td>

<td>

<a class="view-btn"

href="#"

onclick="alert(<?php echo htmlspecialchars(json_encode($row['message']), ENT_QUOTES, 'UTF-8'); ?>); return false;">

View

</a>

<a

class="delete-btn"

href="?action=delete&id=<?php echo $row['id']; ?>"

onclick="return confirm('Delete this inquiry?')">

Delete

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="9" style="text-align:center;padding:40px;color:#888;">

No Contact Inquiries Found

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</body>

</html>