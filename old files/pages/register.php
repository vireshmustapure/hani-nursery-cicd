<?php
session_start();
include '../config/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../src/Exception.php';
require '../src/PHPMailer.php';
require '../src/SMTP.php';

$message = "";
$showOtpBox = false;

/* ---------------- SEND OTP ---------------- */
if (isset($_POST['send_otp'])) {

    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($name) && !empty($mobile) && !empty($email) && !empty($password)) {
      $check = $conn->query("SELECT * FROM users WHERE email='$email' OR mobile='$mobile'");

if ($check->num_rows > 0) {

    session_destroy();

    echo "<script>
        alert('User already exists! Please login.');
        window.location.href = 'login.php';
    </script>";
    exit();
}
        $otp = rand(1000, 9999);

        $_SESSION['otp'] = $otp;
        $_SESSION['name'] = $name;
        $_SESSION['mobile'] = $mobile;
        $_SESSION['email'] = $email;

        // store hashed password
        $_SESSION['password'] = password_hash($password, PASSWORD_DEFAULT);

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            $mail->Username = 'kallurviresh39@gmail.com';
            $mail->Password = 'znamlbvnmkiysvkb';

            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('vireshmmustapure39@gmail.com', 'Hani Nursery');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "OTP Verification";
            $mail->Body = "<h2>Your OTP is</h2><h1>$otp</h1>";

            $mail->send();

            $message = "OTP sent to your email!";
            $showOtpBox = true;

        } catch (Exception $e) {
            $message = "Email failed: " . $mail->ErrorInfo;
        }

    } else {
        $message = "Fill all fields!";
    }
}

/* ---------------- VERIFY OTP ---------------- */
if (isset($_POST['verify_otp'])) {

    $otp_input = $_POST['otp'];

    if (isset($_SESSION['otp']) && $otp_input == $_SESSION['otp']) {

        $name = $_SESSION['name'];
        $mobile = $_SESSION['mobile'];
        $email = $_SESSION['email'];
        $password = $_SESSION['password'];

        $check = $conn->query("SELECT * FROM users WHERE email='$email' OR mobile='$mobile'");

       if ($check->num_rows > 0) {

    session_destroy();

    echo "<script>
        alert('User already exists! Redirecting to login page...');
        window.location.href = 'login.php';
    </script>";
    exit();

}else {

            $conn->query("INSERT INTO users (name,email,mobile,password)
                          VALUES ('$name','$email','$mobile','$password')");

            session_destroy();

            header("Location: login.php?registered=1");
            exit();
        }

    } else {
        $message = "Invalid OTP!";
        $showOtpBox = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1LSB0KPW3Z"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-1LSB0KPW3Z');
</script>

<title>Register</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body {
    font-family: Arial;
    background: linear-gradient(to right, #2e7d32, #66bb6a);
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:15px;
}

.container {
    width:100%;
    max-width:350px;
    background: white;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow:0 5px 20px rgba(0,0,0,0.2);
}

h2 {
    color: #2e7d32;
    margin-bottom:15px;
}

input {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border:1px solid #ccc;
    border-radius:8px;
    font-size:16px;
    outline:none;
}

button {
    width: 100%;
    padding: 12px;
    background: #2e7d32;
    color: white;
    border: none;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
    margin-top:10px;
}

.msg {
    color: red;
    font-weight: bold;
    margin-bottom:10px;
}

/* eye icon */
.password-box {
    position: relative;
}

.password-box span {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 18px;
}

/* mobile */
@media(max-width:480px){

    .container{
        padding:20px;
        border-radius:12px;
    }

    h2{
        font-size:24px;
    }

    input,
    button{
        font-size:16px;
        padding:13px;
    }
}

</style>
</head>

<body>

<div class="container">

<h2>🌿 Register</h2>

<?php if($message){ ?>
<p class="msg"><?php echo $message; ?></p>
<?php } ?>

<!-- STEP 1 -->
<?php if(!$showOtpBox){ ?>

<form method="POST">

    <input type="text" name="name" placeholder="Full Name" required>
    <input type="text" name="mobile" placeholder="Mobile Number" required>

    <!-- PASSWORD FIELD WITH EYE TOGGLE -->
    <div class="password-box">
        <input type="password" name="password" id="password"
               placeholder="Create Password" required>

        <span onclick="togglePassword()">👁️</span>
    </div>

    <input type="email" name="email" placeholder="Email ID" required>

    <button type="submit" name="send_otp">Send OTP</button>

</form>

<?php } ?>

<!-- STEP 2 -->
<?php if($showOtpBox){ ?>

<form method="POST">

    <p>OTP sent to: <b><?php echo $_SESSION['email']; ?></b></p>

    <input type="text" name="otp" placeholder="Enter OTP" required>

    <button type="submit" name="verify_otp">Verify OTP</button>

</form>

<?php } ?>

</div>

<script>
function togglePassword() {
    let pass = document.getElementById("password");

    if (pass.type === "password") {
        pass.type = "text";
    } else {
        pass.type = "password";
    }
}
</script>

</body>
</html>