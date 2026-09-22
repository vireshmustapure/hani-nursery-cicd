<?php
include __DIR__ . '/../config.php';

$msg = "";

if(isset($_POST['save'])){

$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$category = $_POST['category'];

$image = $_FILES['image']['name'];
$tmp = $_FILES['image']['tmp_name'];

move_uploaded_file($tmp, "../uploads/".$image);

mysqli_query($conn,"INSERT INTO products(name,description,price,category,image)
VALUES('$name','$description','$price','$category','$image')");

$msg = "Product Added Successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Product</title>

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
color:#333;
}

input, textarea, select{
width:100%;
padding:12px;
border:1px solid #ccc;
border-radius:10px;
font-size:15px;
}

textarea{
height:100px;
resize:none;
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

.msg{
background:#e8f5e9;
color:green;
padding:12px;
text-align:center;
border-radius:8px;
margin-bottom:15px;
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

<h2>🌿 Add New Plant Product</h2>

<?php if($msg!=""){ ?>
<div class="msg"><?php echo $msg; ?></div>
<?php } ?>

<form method="post" enctype="multipart/form-data">

<label>Product Name</label>
<input type="text" name="name" required>

<label>Description</label>
<textarea name="description" required></textarea>

<label>Price</label>
<input type="text" name="price" required>

<label>Category</label>
<select name="category" required>
<option value="">Select Category</option>
<option>Indoor Plants</option>
<option>Outdoor Plants</option>
<option>Flower Plants</option>
<option>Medicinal Plants</option>
<option>Seeds</option>
<option>Pots</option>
</select>

<label>Upload Image</label>
<input type="file" name="image" required>

<button type="submit" name="save">Add Product</button>

</form>

<a href="products.php" class="back">← Back to Products</a>

</div>

</body>
</html>