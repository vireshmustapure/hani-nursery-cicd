    <?php
include 'config/db.php';

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM blogs WHERE id=$id"));
?>

<h1><?php echo $data['title']; ?></h1>

<img src="uploads/<?php echo $data['image']; ?>">

<p><?php echo $data['content']; ?></p>