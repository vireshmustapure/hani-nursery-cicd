<?php
include __DIR__ . '/../../config.php';
require_once __DIR__ . '/../auth_check.php';

/* AJAX UPDATE STOCK */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_update_stock'])) {
    $pid = intval($_POST['product_id']);
    $stock = intval($_POST['stock']);
    mysqli_query($conn, "UPDATE products SET stock = $stock WHERE id=$pid");
    echo "success";
    exit();
}

/* TOGGLE TOP SOLD ACTION */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_top_sold_id'])) {
    $pid = intval($_POST['toggle_top_sold_id']);
    $new_val = intval($_POST['current_status']) ? 0 : 1;
    mysqli_query($conn, "UPDATE products SET is_top_sold = $new_val WHERE id=$pid");
    header("Location: index.php");
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

<?php include __DIR__ . '/../sidebar.php'; ?>

<div class="main">

<div class="top">

<h1>🪴 Products Management</h1>

<a href="../add-product/index.php" class="add-btn">
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
<th>Stock</th>
<th>Category</th>
<th>Featured Top Sold</th>
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

<img src="../../uploads/<?php echo $row['image']; ?>" width="70">
</td>

<td><?php echo $row['name']; ?></td>

<td><?php echo $row['description']; ?></td>

<td>₹<?php echo $row['price']; ?></td>

<td>
    <div style="display:flex; align-items:center; gap:5px;">
        <input type="number" value="<?php echo $row['stock']; ?>" style="width:70px; padding:6px; border-radius:6px; border:1px solid #ccc; text-align:center;" onchange="updateStockInline(this, <?php echo $row['id']; ?>)">
        <span style="font-size:11px; color:green; display:none; font-weight:bold;">✔</span>
    </div>
</td>

<td><?php echo $row['category']; ?></td>

<td>
    <form method="POST" style="margin:0;">
        <input type="hidden" name="toggle_top_sold_id" value="<?php echo $row['id']; ?>">
        <input type="hidden" name="current_status" value="<?php echo $row['is_top_sold']; ?>">
        <button type="submit" style="
            padding: 6px 12px; 
            border-radius: 6px; 
            border: none; 
            cursor: pointer; 
            font-weight: bold;
            background: <?php echo $row['is_top_sold'] ? '#d4edda' : '#e2e8f0'; ?>;
            color: <?php echo $row['is_top_sold'] ? '#155724' : '#4a5568'; ?>;
        ">
            <?php echo $row['is_top_sold'] ? '⭐ Yes' : '☆ No'; ?>
        </button>
    </form>
</td>

<td>

<a class="action edit"
href="../edit-product/index.php?id=<?php echo $row['id']; ?>">
Edit
</a>

<a class="action delete"
href="../delete-product/index.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this product?')">
Delete
</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

<script>
function updateStockInline(input, productId) {
    let stockVal = input.value;
    let indicator = input.nextElementSibling;
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (this.status === 200 && this.responseText.trim() === 'success') {
            indicator.style.display = 'inline';
            setTimeout(() => {
                indicator.style.display = 'none';
            }, 1500);
        }
    };
    xhr.send("ajax_update_stock=1&product_id=" + productId + "&stock=" + stockVal);
}
</script>

</body>
</html>