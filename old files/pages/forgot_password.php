<?php
include '../config/db.php';

$message = "";

if(isset($_POST['reset'])){
    $mobile = $_POST['mobile'];
    $newpass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $check = $conn->query("SELECT * FROM users WHERE mobile='$mobile'");

    if($check->num_rows > 0){
        $conn->query("UPDATE users SET password='$newpass' WHERE mobile='$mobile'");
        header("Location: login.php?success=1");
        exit();
    } else {
        $message = "Mobile number not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background:#f1f3f6;   /* Flipkart light background */
}

/* CENTER BOX */
.container{
    width:360px;
    margin:80px auto;
    background:white;
    padding:25px;
    border-radius:8px;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
    text-align:center;
}

/* TITLE */
h2{
    margin-bottom:20px;
    color:#2874f0;   /* Flipkart blue */
}

/* INPUTS */
input{
    width:100%;
    padding:12px;
    margin:10px 0;
    border:1px solid #ccc;
    border-radius:4px;
    outline:none;
}

input:focus{
    border-color:#2874f0;
}

/* BUTTON */
button{
    width:100%;
    padding:12px;
    background:#fb641b; /* Flipkart orange button */
    color:white;
    border:none;
    border-radius:4px;
    font-weight:bold;
    cursor:pointer;
}

button:hover{
    background:#e85d17;
}

/* BACK LINK */
a{
    display:block;
    margin-top:15px;
    color:#2874f0;
    text-decoration:none;
}

/* ERROR */
.msg{
    color:red;
    font-size:14px;
}
</style>

</head>

<body>

<div class="container">

    <h2>Forgot Password</h2>

    <?php if($message!="") echo "<p class='msg'>$message</p>"; ?>

    <form method="POST">

        <input type="text" name="mobile" placeholder="Enter Mobile Number" required>

        <input type="password" name="new_password" placeholder="Enter New Password" required>

        <button type="submit" name="reset">Reset Password</button>

    </form>

    <a href="login.php">← Back to Login</a>

</div>

</body>