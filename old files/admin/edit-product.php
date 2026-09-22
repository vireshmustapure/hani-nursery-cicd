<?php
include __DIR__ . '/../config.php';

if(!isset($_SESSION['admin'])){
header("Location: login.php");
exit();
}

$id = $_GET['id'];

$get = mysqli_query($conn,"SELECT * FROM products WHERE id='$id'");
$row = mysqli_fetch_assoc($get);

if(isset($_POST['update'])){

$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$category = $_POST['category'];

$image = $row['image'];

if($_FILES['image']['name']!=""){
$image = $_FILES['image']['name'];
$tmp = $_FILES['image']['tmp_name'];
move_uploaded_file($tmp,"../uploads/".$image);
}

mysqli_query($conn,"UPDATE products SET
name='$name',
description='$description',
price='$price',
stock='$stock',
category='$category',
image='$image'
WHERE id='$id'");

header("Location: products.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Product</title>

<style>
*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial;
}

body{
background:#eef7ee;
padding:40px;
}

.box{
width:650px;
margin:auto;
background:white;
padding:35px;
border-radius:18px;
box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

h2{
text-align:center;
color:#1b5e20;
margin-bottom:25px;
}

label{
display:block;
margin-top:15px;
margin-bottom:6px;
font-weight:bold;
}

input, textarea, select{
width:100%;
padding:12px;
border:1px solid #ccc;
border-radius:10px;
}

textarea{
height:100px;
resize:none;
}

img{
width:100px;
margin-top:10px;
border-radius:10px;
}

button{
margin-top:25px;
width:100%;
padding:14px;
background:#2e7d32;
color:white;
border:none;
border-radius:10px;
font-size:16px;
cursor:pointer;
}

button:hover{
background:#1b5e20;
}

.back{
display:block;
text-align:center;
margin-top:18px;
text-decoration:none;
color:#2e7d32;
font-weight:bold;
}
</style>
</head>

<body>

<div class="box">

<h2>🌿 Edit Product</h2>

<form method="post" enctype="multipart/form-data">

<label>Name</label>
<input type="text" name="name" value="<?php echo $row['name']; ?>" required>

<label>Description</label>
<textarea name="description" required><?php echo $row['description']; ?></textarea>

<label>Price</label>
<input type="text" name="price" value="<?php echo $row['price']; ?>" required>

<label>Stock Quantity</label>
<input type="number" name="stock" value="<?php echo $row['stock']; ?>" required>

<label>Category</label>
<input type="text" name="category" value="<?php echo $row['category']; ?>" required>

<label>Current Image</label>
<img src="../uploads/<?php echo $row['image']; ?>">

<label>Change Image</label>
<input type="file" name="image">

<button type="submit" name="update">Update Product</button>

</form>

<a href="products.php" class="back">← Back to Products</a>

</div>

</body>
</html>