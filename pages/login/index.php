<?php
session_start();
include '../../config/db.php';

$message = "";

// SUCCESS MESSAGE AFTER PASSWORD RESET
if(isset($_GET['success'])){
    $message = "Password updated successfully! Please login.";
}

// LOGIN PROCESS
if(isset($_POST['login'])){
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE mobile='$mobile'";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();

        if(password_verify($password, $user['password'])){
            $_SESSION['user'] = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            
            // Send login success notification email
            include_once '../../config/mailer.php';
            send_nursery_email($user['email'], 'login', ['{user_name}' => $user['name']]);

            // Check for active (abandoned) cart
            if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                $pids = array_keys($_SESSION['cart']);
                if (!empty($pids)) {
                    $pids_str = implode(',', array_map('intval', $pids));
                    $prod_res = mysqli_query($conn, "SELECT id, name, price FROM products WHERE id IN ($pids_str)");
                    if ($prod_res && mysqli_num_rows($prod_res) > 0) {
                        $cart_html = "<ul>";
                        while ($p = mysqli_fetch_assoc($prod_res)) {
                            $qty = $_SESSION['cart'][$p['id']] ?? 1;
                            $cart_html .= "<li>" . htmlspecialchars($p['name']) . " x " . $qty . " (₹" . ($p['price'] * $qty) . ")</li>";
                        }
                        $cart_html .= "</ul>";
                        
                        send_nursery_email($user['email'], 'abandoned', [
                            '{user_name}' => $user['name'],
                            '{cart_items}' => $cart_html
                        ]);
                    }
                }
            }

            if (isset($_GET['redirect']) && $_GET['redirect'] === 'checkout') {
                header("Location: ../checkout/");
            } else {
                header("Location: ../../");
            }
            exit();
        } else {
            $message = "Wrong password!";
        }
    } else {
        $message = "Mobile not registered!";
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

<title>Login</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body {
    font-family:Arial;
    background:url('https://images.unsplash.com/photo-1501004318641-b39e6451bec6') no-repeat center/cover;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:15px;
}

body::before {
    content:"";
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    z-index:-1;
}

.container {
    width:100%;
    max-width:350px;
    background:rgba(255,255,255,0.1);
    backdrop-filter:blur(12px);
    -webkit-backdrop-filter:blur(12px);
    padding:25px;
    border-radius:15px;
    text-align:center;
    color:white;
}

/* inputs */
input {
    width:100%;
    padding:12px;
    margin:10px 0;
    border:none;
    border-radius:8px;
    background:rgba(255,255,255,0.2);
    color:white;
    font-size:16px;
    outline:none;
}

input::placeholder {
    color:#eee;
}

/* password */
.password-box {
    position:relative;
}

.password-box span {
    position:absolute;
    right:12px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    font-size:18px;
}

/* button */
button {
    width:100%;
    padding:12px;
    background:#2e7d32;
    color:white;
    border:none;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
    margin-top:10px;
}

/* links */
a {
    color:#a5d6a7;
    display:block;
    margin-top:10px;
    text-decoration:none;
    font-size:14px;
}

.msg {
    color:#ffcccb;
    margin-bottom:10px;
}

/* Mobile */
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
        padding:13px;
        font-size:16px;
    }

    a{
        font-size:13px;
    }
}
</style>
</head>
<body>

<div class="container" style="margin-top:50px; background:rgba(255,255,255,0.1);
">

<h2>🌿 Login</h2>

<?php if($message!="") echo "<p class='msg'>$message</p>"; ?>

<form method="POST" autocomplete="off">

<input type="text" name="mobile" placeholder="Mobile Number" autocomplete="off" required>

<div class="password-box">
<input type="password" id="pass" name="password" placeholder="Password" required>
<span onclick="toggle()">👁</span>
</div>

<button name="login">Login</button>

</form>

<a href="../forgot_password/">Forgot Password?</a>
<a href="../register/">Create Account</a>

</div>

<script>
function toggle(){
    var p = document.getElementById("pass");
    p.type = (p.type === "password") ? "text" : "password";
}
</script>

</body>
</html>