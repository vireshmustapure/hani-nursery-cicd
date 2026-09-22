<?php
include __DIR__ . '/../config.php';

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

/* ===========================
   LIVE COUNTS
=========================== */

// Total Products
$res = mysqli_query($conn,"SELECT COUNT(*) AS total FROM products");
$row = mysqli_fetch_assoc($res);
$total_products = $row['total'] ?? 0;

// Total Orders
$res = mysqli_query($conn,"SELECT COUNT(*) AS total FROM orders");
$row = mysqli_fetch_assoc($res);
$total_orders = $row['total'] ?? 0;

// Total Users
$res = mysqli_query($conn,"SELECT COUNT(*) AS total FROM users");
$row = mysqli_fetch_assoc($res);
$total_users = $row['total'] ?? 0;

// Revenue
$res = mysqli_query($conn,"SELECT SUM(total_amount) AS total FROM orders");
$row = mysqli_fetch_assoc($res);
$total_revenue = $row['total'] ?? 0;

// Pending Orders
$res = mysqli_query($conn,"SELECT COUNT(*) AS total FROM orders WHERE status='pending' OR order_status='Pending'");
$row = mysqli_fetch_assoc($res);
$pending_orders = $row['total'] ?? 0;

// Recent Orders
$recent_orders = mysqli_query($conn,"SELECT * FROM orders ORDER BY id DESC LIMIT 5");

// Low Stock Products
$low_stock = mysqli_query($conn,"SELECT name,stock FROM products WHERE stock <= 10 ORDER BY stock ASC LIMIT 5");

// Top Selling Products
$top_products = mysqli_query($conn,"
SELECT products.name, SUM(order_items.quantity) AS total_qty
FROM order_items
INNER JOIN products ON products.id = order_items.product_id
GROUP BY order_items.product_id
ORDER BY total_qty DESC
LIMIT 5
");

/* ===============================
   LIVE MONTHLY REVENUE CHART
=============================== */

$chart_data = [];

for($m=1; $m<=12; $m++){

    $sql = "SELECT SUM(total_amount) AS total
            FROM orders
            WHERE YEAR(order_date)=YEAR(CURDATE())
            AND MONTH(order_date)='$m'";

    $res = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($res);

    $chart_data[$m] = $row['total'] ? $row['total'] : 0;
}

$max_value = max($chart_data);
if($max_value == 0){
    $max_value = 1;
}

$months = [
1=>"Jan",2=>"Feb",3=>"Mar",4=>"Apr",5=>"May",6=>"Jun",
7=>"Jul",8=>"Aug",9=>"Sep",10=>"Oct",11=>"Nov",12=>"Dec"
];
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
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

.sidebar{
width:240px;
height:100vh;
position:fixed;
left:0;
top:0;
background:linear-gradient(180deg,#1b5e20,#43a047);
padding:25px 15px;
color:white;
overflow:auto;
}

.logo{
font-size:28px;
text-align:center;
margin-bottom:30px;
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

.main{
margin-left:240px;
padding:30px;
min-height:100vh;
background:
linear-gradient(rgba(255,255,255,0.88),rgba(255,255,255,0.88)),
url('https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=1600&q=80');
background-size:cover;
background-position:center;
}

.topbar{
background:white;
padding:20px;
border-radius:14px;
box-shadow:0 8px 20px rgba(0,0,0,0.08);
margin-bottom:25px;
}

.topbar h1{
color:#1b5e20;
font-size:28px;
}

.topbar p{
color:#666;
margin-top:5px;
}

.cards{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
}

.card-link{
text-decoration:none;
}

.card{
background:white;
padding:25px;
border-radius:16px;
box-shadow:0 10px 25px rgba(0,0,0,0.08);
transition:0.3s;
}

.card:hover{
transform:translateY(-6px);
}

.card h3{
color:#2e7d32;
margin-bottom:12px;
font-size:18px;
}

.num{
font-size:34px;
font-weight:bold;
color:#1b5e20;
}

.card p{
color:#777;
margin-top:8px;
}

.section{
margin-top:30px;
background:white;
padding:25px;
border-radius:16px;
box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

.section h2{
color:#1b5e20;
margin-bottom:15px;
}

.alert{
padding:15px;
border-radius:10px;
background:#fff3cd;
color:#856404;
font-weight:bold;
}

table{
width:100%;
border-collapse:collapse;
margin-top:10px;
}

table th, table td{
padding:12px;
border-bottom:1px solid #eee;
text-align:left;
font-size:14px;
}

table th{
background:#f4faf4;
color:#1b5e20;
}

.status{
padding:6px 10px;
border-radius:20px;
font-size:12px;
font-weight:bold;
display:inline-block;
}

.pending{
background:#fff3cd;
color:#856404;
}

.delivered{
background:#d4edda;
color:#155724;
}

.cancelled{
background:#f8d7da;
color:#721c24;
}

.chart{
display:flex;
align-items:flex-end;
gap:12px;
height:230px;
margin-top:20px;
}

.bar-wrap{
text-align:center;
flex:1;
}

.bar{
background:linear-gradient(#66bb6a,#1b5e20);
border-radius:8px 8px 0 0;
color:white;
font-size:11px;
padding-top:5px;
}

.bar-label{
margin-top:8px;
font-size:13px;
color:#555;
}

ul li, ol li{
margin-bottom:10px;
color:#555;
}
</style>
</head>

<body>

<div class="sidebar">
<div class="logo">🌿</div>
<h2>Nursery Admin</h2>

<a href="dashboard.php">🏠 Dashboard</a>
<a href="products.php">🪴 Products</a>
<a href="orders.php">📦 Orders</a>
<a href="manage-blog.php">📝 Blogs</a> 
<a href="logout.php">🚪 Logout</a>
</div>

<div class="main">

<div class="topbar">
<h1>Welcome, <?php echo $_SESSION['admin']; ?> 🌱</h1>
<p>Manage your nursery store with greenery and growth.</p>
</div>

<div class="cards">

<a href="products.php" class="card-link">
<div class="card">
<h3>Total Products</h3>
<div class="num"><?php echo $total_products; ?></div>
<p>Indoor & outdoor plants</p>
</div>
</a>

<a href="orders.php" class="card-link">
<div class="card">
<h3>Total Orders</h3>
<div class="num"><?php echo $total_orders; ?></div>
<p>Customer purchases</p>
</div>
</a>

<a href="users.php" class="card-link">
<div class="card">
<h3>Customers</h3>
<div class="num"><?php echo $total_users; ?></div>
<p>Registered users</p>
</div>
</a>

<a href="orders.php" class="card-link">
<div class="card">
<h3>Revenue</h3>
<div class="num">₹<?php echo number_format($total_revenue); ?></div>
<p>Total sales</p>
</div>
</a>

</div>

<div class="section">
<h2>🔔 Pending Orders Alert</h2>
<div class="alert">
You have <?php echo $pending_orders; ?> pending orders.
</div>
</div>

<div class="section">
<h2>🌿 Low Stock Plants</h2>

<?php if(mysqli_num_rows($low_stock)>0){ ?>
<ul>
<?php while($row=mysqli_fetch_assoc($low_stock)){ ?>
<li><?php echo $row['name']; ?> - Only <?php echo $row['stock']; ?> left</li>
<?php } ?>
</ul>
<?php } else { ?>
<p>All products are in good stock.</p>
<?php } ?>

</div>

<div class="section">
<h2>🌱 Top Selling Plants</h2>

<ol>
<?php while($row=mysqli_fetch_assoc($top_products)){ ?>
<li><?php echo $row['name']; ?> (<?php echo $row['total_qty']; ?> sold)</li>
<?php } ?>
</ol>

</div>

<div class="section">
<h2>📦 Recent Orders</h2>

<table>
<tr>
<th>ID</th>
<th>Customer</th>
<th>Amount</th>
<th>Status</th>
</tr>

<?php while($order=mysqli_fetch_assoc($recent_orders)){

$status = strtolower($order['status']);
$class = "pending";

if($status=="delivered"){ $class="delivered"; }
if($status=="cancelled"){ $class="cancelled"; }

?>
<tr>
<td>#<?php echo $order['id']; ?></td>
<td><?php echo $order['customer_name']; ?></td>
<td>₹<?php echo number_format($order['total_amount']); ?></td>
<td><span class="status <?php echo $class; ?>"><?php echo ucfirst($status); ?></span></td>
</tr>
<?php } ?>

</table>
</div>

<div class="section">
<h2>📈 Monthly Revenue Overview</h2>

<div class="chart">

<?php foreach($chart_data as $num=>$amount):

$height = ($amount / $max_value) * 220;

if($height < 25 && $amount > 0){
$height = 25;
}
?>

<div class="bar-wrap">
<div class="bar" style="height:<?php echo $height; ?>px;">
₹<?php echo number_format($amount); ?>
</div>
<div class="bar-label"><?php echo $months[$num]; ?></div>
</div>

<?php endforeach; ?>

</div>
</div>

<div class="section">
<h2>🌿 Nature Brings Growth</h2>
<p style="color:#666; line-height:1.8;">
Keep your nursery organized, update products, track orders, and grow your business every day.
Plants bring freshness to homes, and your dashboard brings control to your store.
</p>
<p style="font-size:22px; margin-top:10px;">🍀 🌱 🌿 🪴</p>
</div>

</div>

</body>
</html>