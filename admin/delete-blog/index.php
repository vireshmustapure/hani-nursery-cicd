<?php
include '../../config/db.php';
require_once '../auth_check.php';

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM blogs WHERE id=$id");

header("Location: ../blogs.php");
exit();
?>