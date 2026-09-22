<?php
session_start();
$host = "localhost";
$user = "ivhymbbv_Hani";
$password = "Vibha@2123";
$database = "ivhymbbv_Mahesh";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional
$conn->set_charset("utf8");
?>