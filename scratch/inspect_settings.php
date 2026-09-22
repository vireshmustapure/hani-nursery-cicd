<?php
$conn = new mysqli('localhost', 'root', '', 'ivhymbbv_mahesh');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$res = $conn->query("SELECT * FROM settings");
while ($row = $res->fetch_assoc()) {
    echo $row['setting_key'] . " => " . substr($row['setting_value'], 0, 100) . "\n";
}
?>
