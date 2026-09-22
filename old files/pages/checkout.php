<?php
session_start();

/* ================= LOGIN CHECK ================= */
if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include('../config/db.php');
include('../includes/header.php');

/* ================= CART CHECK ================= */
if(!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0){
    echo "<h3 style='text-align:center;'>🛒 Your cart is empty</h3>";
    exit();
}
?>

<div style="width:80%; margin:auto; margin-top:30px; display:flex; gap:30px;">

    <!-- LEFT SIDE FORM -->
    <div style="flex:1; background:white; padding:20px; border-radius:10px; box-shadow:0 0 10px #ccc;">

        <!-- NEW WHOLESALE NOTICE -->
        <div style="
        background:#fff8e1;
        border:1px solid #ffcc80;
        padding:15px;
        border-radius:8px;
        margin-bottom:20px;
        line-height:1.7;
        color:#e65100;
        ">

        <b>⚠ Wholesale & Delivery Information</b><br>

        We mainly accept bulk / wholesale plant orders.<br>


        🚚 Orders above ₹1000 = FREE Delivery

        </div>

        <h2>📦 Delivery Address</h2>

        <form method="POST" action="place_order.php">

            <div style="display:flex; gap:10px;">
                <input type="text" name="name" placeholder="Full Name" required style="flex:1; padding:10px;">

                <input type="text" name="phone" placeholder="Mobile Number" required style="flex:1; padding:10px;">
            </div><br>

            <div style="display:flex; gap:10px;">
                <input type="text" name="pincode" placeholder="Pincode" required style="flex:1; padding:10px;">

                <input type="text" name="state" placeholder="State" required style="flex:1; padding:10px;">
            </div><br>

            <div style="display:flex; gap:10px;">
                <input type="text" name="city" placeholder="City" required style="flex:1; padding:10px;">

                <input type="text" name="landmark" placeholder="Landmark (Optional)" style="flex:1; padding:10px;">
            </div><br>

            <textarea name="address"
            placeholder="House No, Building, Street, Area"
            required
            style="width:100%; padding:10px;"></textarea>

            <br><br>

            <!-- ADDRESS TYPE -->
            <label><b>Address Type:</b></label><br>

            <input type="radio" name="type" value="Home" checked> Home

            <input type="radio" name="type" value="Work"> Work

            <br><br>

            <!-- PAYMENT METHOD -->
            <h3>💳 Payment Method</h3>

            <label>
                <input type="radio" name="payment" value="COD" checked>
                Cash on Delivery
            </label><br>

            <label>
                <input type="radio" name="payment" value="ONLINE">
                Online Payment (Demo)
            </label>

            <br><br>

            <!-- NEW CHECKBOX -->
            <label style="line-height:1.8;">
                <input type="checkbox" required>

                I understand delivery charges and wholesale order conditions.
            </label>

            <br>

            <label style="line-height:1.8;">
                <input type="checkbox" required>

                Live plants may vary slightly in size and color naturally.
            </label>

            <br><br>

            <button style="
            background:#fb641b;
            color:white;
            padding:12px 20px;
            border:none;
            border-radius:5px;
            cursor:pointer;
            ">

            Save & Deliver Here

            </button>

        </form>

    </div>

    <!-- RIGHT SIDE SUMMARY -->
    <div style="
    width:300px;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 0 10px #ccc;
    ">

        <h3>🛒 Order Summary</h3>

        <?php
        $total = 0;

        foreach($_SESSION['cart'] as $id => $qty){

            $res = $conn->query("SELECT * FROM products WHERE id=$id");
            $row = $res->fetch_assoc();

            if(!$row) continue;

            $subtotal = $row['price'] * $qty;
            $total += $subtotal;
        ?>

        <div style="margin-bottom:10px; line-height:1.6;">

            <?php echo $row['name']; ?> × <?php echo $qty; ?><br>

            ₹<?php echo $subtotal; ?>

        </div>

        <?php } ?>

        <?php

        /* DELIVERY CHARGE */
        $delivery = 0;

        if($total < 1000){
            $delivery = 100;
        }

        $final_total = $total + $delivery;

        ?>

        <hr><br>

        <div style="line-height:2;">

            <b>Subtotal:</b>
            ₹<?php echo $total; ?><br>

            <b>Delivery Charge:</b>

            <?php
            if($delivery > 0){
                echo "₹100";
            }else{
                echo "<span style='color:green;'>FREE</span>";
            }
            ?>

            <br>

            <?php if($total < 1000){ ?>

            <div style="
            background:#e8f5e9;
            color:#2e7d32;
            padding:10px;
            border-radius:6px;
            margin-top:10px;
            font-size:14px;
            ">

            🪴 Add ₹<?php echo 1000 - $total; ?>
            more plants to get FREE delivery 🚚

            </div>

            <?php } ?>

        </div>

        <hr><br>

        <h3 style="color:green;">

        Total: ₹<?php echo $final_total; ?>

        </h3>

    </div>

</div>