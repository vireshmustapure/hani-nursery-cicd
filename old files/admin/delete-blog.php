<?php
include '../config/db.php';

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM blogs WHERE id=$id");

header("Location: manage-blog.php");
exit();
?>