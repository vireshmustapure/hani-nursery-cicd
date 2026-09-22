<?php
include '../config/db.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Blogs</title>

<style>
body{
    font-family:Arial;
    background:#f5fff7;
    padding:20px;
}

h2{
    color:#2e7d32;
}

.top-btn{
    margin-bottom:20px;
}

.top-btn a{
    background:#2e7d32;
    color:white;
    padding:10px 15px;
    text-decoration:none;
    border-radius:8px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

table th, table td{
    padding:12px;
    border-bottom:1px solid #ddd;
}

table th{
    background:#e8f5e9;
}

img{
    border-radius:8px;
}

.action a{
    margin-right:10px;
    text-decoration:none;
    font-weight:bold;
}

.edit{
    color:blue;
}

.delete{
    color:red;
}
</style>

</head>
<body>

<h2>📝 Blog Management</h2>

<div class="top-btn">
    <a href="add-blog.php">➕ Add New Blog</a>
</div>

<table>
<tr>
    <th>ID</th>
    <th>Image</th>
    <th>Title</th>
    <th>Action</th>
</tr>

<?php
$result = mysqli_query($conn,"SELECT * FROM blogs ORDER BY id DESC");

while($row = mysqli_fetch_assoc($result)){
?>

<tr>
    <td><?php echo $row['id']; ?></td>

    <td>
        <img src="../uploads/<?php echo $row['image']; ?>" width="80">
    </td>

    <td><?php echo $row['title']; ?></td>

    <td class="action">
        <a href="edit-blog.php?id=<?php echo $row['id']; ?>" class="edit">Edit</a>
        <a href="delete-blog.php?id=<?php echo $row['id']; ?>" class="delete"
        onclick="return confirm('Delete this blog?')">Delete</a>
    </td>
</tr>

<?php } ?>

</table>

</body>
</html>