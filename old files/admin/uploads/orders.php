<?php include '../config.php';
if(isset($_POST['save'])){
$name=$_POST['name']; $price=$_POST['price']; $desc=$_POST['description'];
$img=$_FILES['image']['name']; move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/'.$img);
mysqli_query($conn,"INSERT INTO products(name,price,image,description) VALUES('$name','$price','$img','$desc')");
header('Location: products.php'); }
?>
<form method='post' enctype='multipart/form-data'>
<input name='name' placeholder='Plant Name'>
<input name='price' placeholder='Price'>
<input type='file' name='image'>
<textarea name='description'></textarea>
<button name='save'>Save</button>
</form>