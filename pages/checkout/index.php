<?php
session_start();

/* ================= LOGIN CHECK ================= */
if(!isset($_SESSION['user'])) {
    header("Location: ../login/index.php?redirect=checkout");
    exit();
}

include('../../config/db.php');
include('../../includes/header.php');

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

        <form method="POST" action="../place_order/index.php">

        <?php
        $user_id = intval($_SESSION['user']);
        $addr_q = mysqli_query($conn, "SELECT * FROM addresses WHERE user_id=$user_id ORDER BY is_default DESC");
        if (mysqli_num_rows($addr_q) > 0) {
            $addresses = [];
        ?>
            <div style="margin-bottom: 20px;">
                <label style="font-weight:600; font-size:14px; color:#2e7d32; display:block; margin-bottom:5px;">Select Saved Address:</label>
                <select id="saved-address-selector" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ccc; font-size:14px; outline:none; background:white;" onchange="populateAddress(this)">
                    <option value="">-- Enter new address --</option>
                    <?php 
                    while ($addr = mysqli_fetch_assoc($addr_q)) { 
                        $addresses[$addr['id']] = $addr;
                        $sel = $addr['is_default'] ? 'selected' : '';
                    ?>
                        <option value="<?php echo $addr['id']; ?>" <?php echo $sel; ?>>
                            <?php echo htmlspecialchars($addr['customer_name'] . ' (' . $addr['address_type'] . ') - ' . $addr['address'] . ', ' . $addr['city']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            
            <script>
            const savedAddresses = <?php echo json_encode($addresses); ?>;
            function populateAddress(select) {
                const id = select.value;
                if (id && savedAddresses[id]) {
                    const addr = savedAddresses[id];
                    document.querySelector("input[name='name']").value = addr.customer_name;
                    document.querySelector("input[name='phone']").value = addr.phone;
                    document.querySelector("input[name='pincode']").value = addr.pincode;
                    document.querySelector("input[name='state']").value = addr.state;
                    document.querySelector("input[name='city']").value = addr.city;
                    document.querySelector("input[name='landmark']").value = addr.landmark || '';
                    document.querySelector("textarea[name='address']").value = addr.address;
                    
                    const typeRadios = document.querySelectorAll("input[name='type']");
                    typeRadios.forEach(radio => {
                        if (radio.value === addr.address_type) {
                            radio.checked = true;
                        }
                    });
                } else {
                    document.querySelector("input[name='name']").value = '';
                    document.querySelector("input[name='phone']").value = '';
                    document.querySelector("input[name='pincode']").value = '';
                    document.querySelector("input[name='state']").value = '';
                    document.querySelector("input[name='city']").value = '';
                    document.querySelector("input[name='landmark']").value = '';
                    document.querySelector("textarea[name='address']").value = '';
                }
            }
            
            document.addEventListener("DOMContentLoaded", () => {
                const select = document.getElementById("saved-address-selector");
                if (select) populateAddress(select);
            });
            </script>
        <?php } ?>

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

            <button id="submit-btn" style="
            background:#fb641b;
            color:white;
            padding:12px 20px;
            border:none;
            border-radius:5px;
            cursor:pointer;
            font-weight: bold;
            ">
            Save & Deliver Here
            </button>

            <br><br>

            <label style="line-height:1.8; display: block; margin-bottom: 10px;">
                <input type="checkbox" name="save_new_address" value="1">
                Save this address for future purchases
            </label>

            <!-- NEW CHECKBOX -->
            <label style="line-height:1.8; display: block;">
                <input type="checkbox" required>
                I understand delivery charges and wholesale order conditions.
            </label>

            <label style="line-height:1.8; display: block; margin-bottom: 15px;">
                <input type="checkbox" required>
                Live plants may vary slightly in size and color naturally.
            </label>

        </form>

        <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.getElementById("submit-btn").closest("form");
            form.addEventListener("submit", (e) => {
                const btn = document.getElementById("submit-btn");
                btn.disabled = true;
                btn.style.background = "#ccc";
                btn.style.cursor = "not-allowed";
                btn.innerHTML = "⏳ Processing Order, Please Wait...";
            });
        });
        </script>

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

        /* COUPON DISCOUNT */
        $discount = 0;
        $coupon_code = $_SESSION['coupon'] ?? '';
        if (!empty($coupon_code)) {
            $cp_res = mysqli_query($conn, "SELECT * FROM coupons WHERE code='" . mysqli_real_escape_string($conn, $coupon_code) . "' AND status='active' LIMIT 1");
            if ($cp_res && $cp = mysqli_fetch_assoc($cp_res)) {
                if ($cp['discount_type'] === 'percentage') {
                    $discount = $total * ($cp['discount_value'] / 100);
                } else {
                    $discount = $cp['discount_value'];
                }
            } else {
                unset($_SESSION['coupon']);
                $coupon_code = '';
            }
        }

        $final_total = $total - $discount + $delivery;
        if ($final_total < 0) {
            $final_total = 0;
        }
        ?>

        <!-- Coupon Input Section -->
        <div style="margin-top:20px; margin-bottom:20px; border-top:1px solid #eee; padding-top:15px;">
            <label style="font-weight:bold; font-size:13px; color:#333; display:block; margin-bottom:5px;">Have a Promo Coupon?</label>
            <div style="display:flex; gap:8px;">
                <input type="text" id="coupon-input" placeholder="Enter Coupon Code" value="<?php echo htmlspecialchars($coupon_code); ?>" style="flex:1; padding:8px; border:1px solid #ccc; border-radius:6px; font-size:13px; outline:none; text-transform: uppercase;">
                <button type="button" onclick="applyCoupon()" style="background:#2e7d32; color:white; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; font-weight:bold; font-size:13px;">Apply</button>
            </div>
            <div id="coupon-message" style="font-size:12px; margin-top:5px; font-weight:600;"></div>
            <?php if (!empty($coupon_code)) { ?>
                <a href="#" onclick="removeCoupon(event)" style="color:#d32f2f; font-size:12px; text-decoration:none; display:inline-block; margin-top:5px; font-weight:bold;">Remove Coupon</a>
            <?php } ?>
        </div>

        <hr><br>

        <div style="line-height:2;">

            <b>Subtotal:</b>
            ₹<?php echo $total; ?><br>

            <?php if ($discount > 0) { ?>
                <b style="color:green;">Discount:</b>
                <span style="color:green;">-₹<?php echo $discount; ?></span><br>
            <?php } ?>

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

<script>
function applyCoupon() {
    const code = document.getElementById("coupon-input").value.trim();
    const msgDiv = document.getElementById("coupon-message");
    if (!code) {
        msgDiv.style.color = "red";
        msgDiv.innerText = "Please enter a coupon code.";
        return;
    }
    msgDiv.style.color = "blue";
    msgDiv.innerText = "Applying...";

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "apply_coupon.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        try {
            const res = JSON.parse(this.responseText);
            if (res.success) {
                msgDiv.style.color = "green";
                msgDiv.innerText = res.message;
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                msgDiv.style.color = "red";
                msgDiv.innerText = res.message;
            }
        } catch(e) {
            msgDiv.style.color = "red";
            msgDiv.innerText = "An error occurred. Please try again.";
        }
    };
    xhr.send("action=apply&code=" + encodeURIComponent(code));
}

function removeCoupon(e) {
    e.preventDefault();
    const msgDiv = document.getElementById("coupon-message");
    msgDiv.style.color = "blue";
    msgDiv.innerText = "Removing...";

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "apply_coupon.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        try {
            const res = JSON.parse(this.responseText);
            if (res.success) {
                msgDiv.style.color = "green";
                msgDiv.innerText = res.message;
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch(e) {}
    };
    xhr.send("action=remove");
}
</script>