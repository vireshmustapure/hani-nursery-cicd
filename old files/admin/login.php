<?php
include '../config.php';

$msg = "";

if(isset($_POST['login'])){

    $u = $_POST['username'];
    $p = md5($_POST['password']);

    $q = mysqli_query($conn, "SELECT * FROM admins WHERE username='$u' AND password='$p'");

    if(mysqli_num_rows($q) > 0){
        $_SESSION['admin'] = $u;
        header("Location: dashboard.php");
        exit();
    } else {
        $msg = "Invalid Username or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<style>
body{
    font-family: Arial;
    background:#e8f5e9;
}
.login-box{
    width:350px;
    margin:100px auto;
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0 0 10px gray;
}
h2{
    text-align:center;
    color:green;
}
input{
    width:100%;
    padding:10px;
    margin:10px 0;
}
button{
    width:100%;
    padding:10px;
    background:green;
    color:white;
    border:none;
}
.error{
    color:red;
    text-align:center;
}
</style>
</head>
<body>

<div class="login-box">
<h2>Nursery Admin Login</h2>

<p class="error"><?php echo $msg; ?></p>

<form method="post">
<input type="text" name="username" placeholder="Enter Username" required>
<input type="password" name="password" placeholder="Enter Password" required>
<button type="submit" name="login">Login</button>
</form>

</div>

</body>
</html>