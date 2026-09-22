<?php
include __DIR__ . '/../config.php';

if(!isset($_SESSION['admin'])){
header("Location: http://localhost/nursery/admin/login.php");
exit();
}

/* SEARCH */
$search = "";

if(isset($_GET['search'])){
$search = $_GET['search'];

$result = mysqli_query($conn,
"SELECT * FROM products
WHERE name LIKE '%$search%'
ORDER BY id DESC");

}else{

$result = mysqli_query($conn,
"SELECT * FROM products
ORDER BY id DESC");

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Products Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial, sans-serif;
}

body{
background:#eef7ee;
}

/* Sidebar */
.sidebar{
width:240px;
height:100vh;
position:fixed;
left:0;
top:0;
background:linear-gradient(180deg,#1b5e20,#43a047);
padding:25px 15px;
color:white;
}

.logo{
font-size:32px;
text-align:center;
margin-bottom:18px;
}

.sidebar h2{
text-align:center;
font-size:22px;
margin-bottom:25px;
}

.sidebar a{
display:block;
color:white;
text-decoration:none;
padding:14px;
margin-bottom:10px;
border-radius:10px;
background:rgba(255,255,255,0.08);
transition:0.3s;
}

.sidebar a:hover{
background:rgba(255,255,255,0.2);
transform:translateX(5px);
}

/* Main */
.main{
margin-left:240px;
padding:30px;
min-height:100vh;
background:
linear-gradient(rgba(255,255,255,0.90),rgba(255,255,255,0.90)),
url('https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=1600&q=80');
background-size:cover;
background-position:center;
}

.top{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
background:white;
padding:20px;
border-radius:16px;
box-shadow:0 10px 25px rgba(0,0,0,0.08);
flex-wrap:wrap;
gap:15px;
}

.top h1{
color:#1b5e20;
}

.add-btn{
background:#2e7d32;
color:white;
padding:12px 18px;
text-decoration:none;
border-radius:10px;
font-weight:bold;
}

.add-btn:hover{
background:#1b5e20;
}

/* SEARCH BAR */
.search-box{
display:flex;
gap:10px;
width:100%;
margin-top:10px;
}

.search-box input{
flex:1;
padding:12px;
border:1px solid #ccc;
border-radius:10px;
font-size:15px;
}

.search-box button{
padding:12px 18px;
border:none;
background:#2e7d32;
color:white;
border-radius:10px;
cursor:pointer;
font-weight:bold;
}

.search-box button:hover{
background:#1b5e20;
}

.table-box{
background:white;
padding:20px;
border-radius:16px;
box-shadow:0 10px 25px rgba(0,0,0,0.08);
overflow:auto;
}

table{
width:100%;
border-collapse:collapse;
}

table th{
background:#43a047;
color:white;
padding:14px;
text-align:left;
}

table td{
padding:12px;
border-bottom:1px solid #eee;
vertical-align:middle;
}

table tr:hover{
background:#f8fff8;
}

img{
width:65px;
height:65px;
object-fit:cover;
border-radius:10px;
}

.action{
padding:8px 12px;
text-decoration:none;
border-radius:8px;
color:white;
font-size:14px;
margin-right:5px;
display:inline-block;
margin-top:5px;
}

.edit{
background:#1976d2;
}

.delete{
background:#d32f2f;
}

</style>
</head>

<body>

<div class="sidebar">

<div class="logo">🌿</div>

<h2>Nursery Admin</h2>

<a href="dashboard.php">🏠 Dashboard</a>
<a href="products.php">🪴 Products</a>
<a href="add-product.php">➕ Add Product</a>
<a href="orders.php">📦 Orders</a>
<a href="logout.php">🚪 Logout</a>

</div>

<div class="main">

<div class="top">

<h1>🪴 Products Management</h1>

<a href="add-product.php" class="add-btn">
+ Add Product
</a>

<form method="GET" class="search-box">

<input type="text"
name="search"
placeholder="Search Plant Name..."
value="<?php echo $search; ?>">

<button type="submit">
🔍 Search
</button>

</form>

</div>

<div class="table-box">

<table>

<tr>
<th>ID</th>
<th>Image</th>
<th>Name</th>
<th>Description</th>
<th>Price</th>
<th>Category</th>
<th>Action</th>
</tr>

<?php
$i = 1;

while($row=mysqli_fetch_assoc($result)){
?>

<tr>

<td><?php echo $i++; ?></td>

<td>
<?php echo $row['image']; ?><br>

<img src="../uploads/<?php echo $row['image']; ?>" width="70">
</td>

<td><?php echo $row['name']; ?></td>

<td><?php echo $row['description']; ?></td>

<td>₹<?php echo $row['price']; ?></td>

<td><?php echo $row['category']; ?></td>

<td>

<a class="action edit"
href="edit-product.php?id=<?php echo $row['id']; ?>">
Edit
</a>

<a class="action delete"
href="delete-product.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this product?')">
Delete
</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>