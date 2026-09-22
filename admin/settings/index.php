<?php
include '../auth_check.php';
include '../../config/db.php';

$msg = "";

// Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    foreach ($_POST['settings'] as $key => $val) {
        $key_esc = mysqli_real_escape_string($conn, $key);
        $val_esc = mysqli_real_escape_string($conn, $val);
        
        // Update or insert setting (Upsert)
        mysqli_query($conn, "INSERT INTO settings (setting_key, setting_value) VALUES ('$key_esc', '$val_esc') ON DUPLICATE KEY UPDATE setting_value='$val_esc'");
    }
    $msg = "Settings updated successfully! ✅";
}

// Handle Add Coupon
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_coupon'])) {
    $code = mysqli_real_escape_string($conn, trim($_POST['coupon_code']));
    $discount_type = mysqli_real_escape_string($conn, $_POST['discount_type']);
    $discount_value = floatval($_POST['discount_value']);
    $target_type = mysqli_real_escape_string($conn, $_POST['target_type']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    if (!empty($code) && $discount_value > 0) {
        $q = "INSERT INTO coupons (code, discount_type, discount_value, target_type, status) 
              VALUES ('$code', '$discount_type', $discount_value, '$target_type', '$status')";
        if (mysqli_query($conn, $q)) {
            $msg = "Coupon added successfully! ✅";
        } else {
            $msg = "Error adding coupon: " . mysqli_error($conn);
        }
    } else {
        $msg = "Please enter a valid coupon code and value! ❌";
    }
}

// Handle Delete Coupon
if (isset($_GET['delete_coupon'])) {
    $coupon_id = intval($_GET['delete_coupon']);
    if (mysqli_query($conn, "DELETE FROM coupons WHERE id=$coupon_id")) {
        $msg = "Coupon deleted successfully! ✅";
    } else {
        $msg = "Error deleting coupon: " . mysqli_error($conn);
    }
}

// Fetch settings
$settings = [];
$res = mysqli_query($conn, "SELECT * FROM settings");
while ($row = mysqli_fetch_assoc($res)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Fetch coupons
$coupons_res = mysqli_query($conn, "SELECT * FROM coupons ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Settings</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
    font-family: Arial, sans-serif;
    background: #eef7ee;
    margin: 0;
    padding: 0;
}

.main {
    margin-left: 240px;
    padding: 30px;
    min-height: 100vh;
    background: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.topbar {
    background: #f4faf4;
    padding: 20px;
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.04);
    margin-bottom: 25px;
}

.topbar h1 {
    color: #1b5e20;
    font-size: 28px;
    margin: 0;
}

.msg {
    background: #d4edda;
    color: #155724;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-weight: bold;
}

.tabs-container {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 10px;
    flex-wrap: wrap;
}

.tab-btn {
    background: transparent;
    border: none;
    padding: 12px 20px;
    font-size: 15px;
    font-weight: 600;
    color: #4a5568;
    cursor: pointer;
    border-radius: 8px;
    transition: 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
    outline: none;
}

.tab-btn:hover {
    background: rgba(46, 125, 50, 0.08);
    color: #2e7d32;
}

.tab-btn.active {
    background: #2e7d32;
    color: white;
}

.settings-tab-content {
    display: none;
}

.settings-tab-content.active {
    display: block;
}

.card {
    background: #ffffff;
    border-radius: 14px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border: 1px solid #eee;
    margin-bottom: 25px;
}

.card h3 {
    margin-top: 0;
    color: #1b5e20;
    border-bottom: 1px solid #eee;
    padding-bottom: 12px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: bold;
    color: #1b5e20;
    margin-bottom: 8px;
    font-size: 15px;
}

.form-group input, .form-group textarea, .form-group select {
    width: 100%;
    padding: 12px 15px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
    outline: none;
    transition: 0.2s;
    box-sizing: border-box;
}

.form-group input:focus, .form-group textarea:focus, .form-group select:focus {
    border-color: #2e7d32;
    box-shadow: 0 0 5px rgba(46, 125, 50, 0.3);
}

.submit-btn {
    background: #2e7d32;
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    transition: 0.2s;
}

.submit-btn:hover {
    background: #1b5e20;
}

/* Coupon Table styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

table th, table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    text-align: left;
    font-size: 14px;
}

table th {
    background: #e8f5e9;
    color: #1b5e20;
}

/* Day Badges */
.day-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 20px;
    background: #e2e8f0;
    color: #4a5568;
    font-size: 11px;
    font-weight: bold;
    margin-right: 4px;
}

.day-badge.active-day {
    background: #c6f6d5;
    color: #22543d;
}

/* Dynamic Banners list card */
.banner-item {
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.delete-badge-btn {
    background: #fed7d7;
    color: #9b2c2c;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    font-size: 12px;
    transition: 0.2s;
}

.delete-badge-btn:hover {
    background: #feb2b2;
}
</style>
</head>
<body>

<?php include __DIR__ . '/../sidebar.php'; ?>

<div class="main">
    <div class="topbar">
        <h1>⚙️ Site Settings Dashboard</h1>
    </div>

    <?php if ($msg != "") { ?>
        <div class="msg"><?php echo $msg; ?></div>
    <?php } ?>

    <div class="tabs-container">
        <button class="tab-btn active" id="btn-banners" onclick="switchSettingsTab('banners')"><i data-lucide="megaphone"></i> Offer Banners</button>
        <button class="tab-btn" id="btn-coupons" onclick="switchSettingsTab('coupons')"><i data-lucide="ticket"></i> Promo Coupons</button>
        <button class="tab-btn" id="btn-smtp" onclick="switchSettingsTab('smtp')"><i data-lucide="server"></i> SMTP Config</button>
        <button class="tab-btn" id="btn-templates" onclick="switchSettingsTab('templates')"><i data-lucide="mail"></i> Mail Templates</button>
        <button class="tab-btn" id="btn-whatsapp" onclick="switchSettingsTab('whatsapp')"><i data-lucide="message-square"></i> WhatsApp Settings</button>
    </div>

    <!-- MAIN FORM FOR SAVING SETTINGS -->
    <form method="POST">

        <!-- TAB: BANNERS -->
        <div class="settings-tab-content active" id="tab-content-banners">
            <div class="card">
                <h3><i data-lucide="megaphone"></i> Dynamic Header Banners</h3>
                <p style="font-size: 13px; color: #666; margin-bottom: 20px;">
                    Configure targeted alert banners displayed at the top of Hani Nursery. Only one banner is displayed each day depending on the active days.
                </p>

                <!-- Hidden serialize field -->
                <input type="hidden" name="settings[header_banners_json]" id="header-banners-json-input" value="<?php echo htmlspecialchars($settings['header_banners_json'] ?? '[]'); ?>">

                <!-- Rendered banners list -->
                <div id="banners-list-container">
                    <!-- Loaded via JS -->
                </div>

                <button type="button" class="submit-btn" onclick="openBannerModal()" style="margin-top: 15px; display: inline-flex; align-items: center; gap: 8px;">
                    <i data-lucide="plus"></i> Add Targeted Banner
                </button>
            </div>

            <!-- Fallback old banners (kept in case, but hidden/optional) -->
            <div class="card" style="opacity: 0.8;">
                <h3><i data-lucide="alert-circle"></i> Static Fallbacks</h3>
                <div class="form-group">
                    <label>Fallback Weekday Text:</label>
                    <input type="text" name="settings[top_banner_weekday]" value="<?php echo htmlspecialchars($settings['top_banner_weekday'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Fallback Weekend Text:</label>
                    <input type="text" name="settings[top_banner_weekend]" value="<?php echo htmlspecialchars($settings['top_banner_weekend'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <!-- TAB: SMTP -->
        <div class="settings-tab-content" id="tab-content-smtp">
            <div class="card">
                <h3><i data-lucide="server"></i> SMTP Server Configuration</h3>
                <p style="font-size: 13px; color: #666; margin-bottom: 20px;">Configure outgoing SMTP relay servers to transmit dynamic OTPs and order status updates securely.</p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <div class="form-group">
                        <label for="smtp_host">SMTP Host / Server:</label>
                        <input type="text" id="smtp_host" name="settings[smtp_host]" value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>" placeholder="e.g. smtp.gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="smtp_port">SMTP Port:</label>
                        <input type="text" id="smtp_port" name="settings[smtp_port]" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>" placeholder="e.g. 587 or 465">
                    </div>
                    <div class="form-group">
                        <label for="smtp_secure">Security Protocol:</label>
                        <select id="smtp_secure" name="settings[smtp_secure]">
                            <option value="tls" <?php echo ($settings['smtp_secure'] ?? '') == 'tls' ? 'selected' : ''; ?>>TLS (Preferred)</option>
                            <option value="ssl" <?php echo ($settings['smtp_secure'] ?? '') == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                            <option value="none" <?php echo ($settings['smtp_secure'] ?? '') == 'none' ? 'selected' : ''; ?>>None</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="smtp_user">SMTP Username:</label>
                        <input type="text" id="smtp_user" name="settings[smtp_user]" value="<?php echo htmlspecialchars($settings['smtp_user'] ?? ''); ?>" placeholder="e.g. sender@gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="smtp_pass">SMTP Password:</label>
                        <input type="password" id="smtp_pass" name="settings[smtp_pass]" value="<?php echo htmlspecialchars($settings['smtp_pass'] ?? ''); ?>" placeholder="Enter SMTP password">
                    </div>
                    <div class="form-group">
                        <label for="smtp_from_email">Sender Email Address:</label>
                        <input type="email" id="smtp_from_email" name="settings[smtp_from_email]" value="<?php echo htmlspecialchars($settings['smtp_from_email'] ?? ''); ?>" placeholder="e.g. store@nursery.com">
                    </div>
                    <div class="form-group">
                        <label for="smtp_from_name">Sender Identity Name:</label>
                        <input type="text" id="smtp_from_name" name="settings[smtp_from_name]" value="<?php echo htmlspecialchars($settings['smtp_from_name'] ?? ''); ?>" placeholder="e.g. Hani Nursery Support">
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: EMAIL TEMPLATES -->
        <div class="settings-tab-content" id="tab-content-templates">
            <div class="card">
                <h3><i data-lucide="sparkles"></i> General Promo Variables</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div class="form-group">
                        <label>Active Offer Coupon Code:</label>
                        <input type="text" name="settings[global_offer_code]" value="<?php echo htmlspecialchars($settings['global_offer_code'] ?? 'HANI10'); ?>">
                        <small style="color:#666;">Mapped to placeholder {offer_code}</small>
                    </div>
                    <div class="form-group">
                        <label>Offer Discount Value:</label>
                        <input type="text" name="settings[global_offer_value]" value="<?php echo htmlspecialchars($settings['global_offer_value'] ?? '10%'); ?>">
                        <small style="color:#666;">Mapped to placeholder {offer_value}</small>
                    </div>
                </div>
            </div>

            <!-- OTP Template -->
            <div class="card">
                <h3><i data-lucide="shield-check"></i> Account Verification OTP Email</h3>
                <p style="font-size:12px; color:#555; margin-bottom:12px;">Variables: <b>{user_name}</b>, <b>{otp_code}</b></p>
                <div class="form-group">
                    <label>Subject line:</label>
                    <input type="text" name="settings[mail_tpl_otp_sub]" value="<?php echo htmlspecialchars($settings['mail_tpl_otp_sub'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Body Content (HTML allowed):</label>
                    <textarea name="settings[mail_tpl_otp_body]" rows="4"><?php echo htmlspecialchars($settings['mail_tpl_otp_body'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Login Confirmation Template -->
            <div class="card">
                <h3><i data-lucide="log-in"></i> Successful Customer Login Alert</h3>
                <p style="font-size:12px; color:#555; margin-bottom:12px;">Variables: <b>{user_name}</b>, <b>{offer_code}</b>, <b>{offer_value}</b></p>
                <div class="form-group">
                    <label>Subject line:</label>
                    <input type="text" name="settings[mail_tpl_login_sub]" value="<?php echo htmlspecialchars($settings['mail_tpl_login_sub'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Body Content (HTML allowed):</label>
                    <textarea name="settings[mail_tpl_login_body]" rows="4"><?php echo htmlspecialchars($settings['mail_tpl_login_body'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Abandoned Cart Template -->
            <div class="card">
                <h3><i data-lucide="shopping-cart"></i> Abandoned Cart Reminder</h3>
                <p style="font-size:12px; color:#555; margin-bottom:12px;">Variables: <b>{user_name}</b>, <b>{cart_items}</b>, <b>{offer_code}</b>, <b>{offer_value}</b></p>
                <div class="form-group">
                    <label>Subject line:</label>
                    <input type="text" name="settings[mail_tpl_abandoned_sub]" value="<?php echo htmlspecialchars($settings['mail_tpl_abandoned_sub'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Body Content (HTML allowed):</label>
                    <textarea name="settings[mail_tpl_abandoned_body]" rows="5"><?php echo htmlspecialchars($settings['mail_tpl_abandoned_body'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Order Placed Template -->
            <div class="card">
                <h3><i data-lucide="check-circle-2"></i> Order Placed & Confirmed</h3>
                <p style="font-size:12px; color:#555; margin-bottom:12px;">Variables: <b>{user_name}</b>, <b>{order_id}</b>, <b>{order_total}</b>, <b>{offer_code}</b>, <b>{offer_value}</b></p>
                <div class="form-group">
                    <label>Subject line:</label>
                    <input type="text" name="settings[mail_tpl_placed_sub]" value="<?php echo htmlspecialchars($settings['mail_tpl_placed_sub'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Body Content (HTML allowed):</label>
                    <textarea name="settings[mail_tpl_placed_body]" rows="4"><?php echo htmlspecialchars($settings['mail_tpl_placed_body'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Order Packed Template -->
            <div class="card">
                <h3><i data-lucide="package-open"></i> Order Status - Packed</h3>
                <p style="font-size:12px; color:#555; margin-bottom:12px;">Variables: <b>{user_name}</b>, <b>{order_id}</b></p>
                <div class="form-group">
                    <label>Subject line:</label>
                    <input type="text" name="settings[mail_tpl_packed_sub]" value="<?php echo htmlspecialchars($settings['mail_tpl_packed_sub'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Body Content (HTML allowed):</label>
                    <textarea name="settings[mail_tpl_packed_body]" rows="4"><?php echo htmlspecialchars($settings['mail_tpl_packed_body'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Order Shipped Template -->
            <div class="card">
                <h3><i data-lucide="truck"></i> Order Status - Shipped</h3>
                <p style="font-size:12px; color:#555; margin-bottom:12px;">Variables: <b>{user_name}</b>, <b>{order_id}</b></p>
                <div class="form-group">
                    <label>Subject line:</label>
                    <input type="text" name="settings[mail_tpl_shipped_sub]" value="<?php echo htmlspecialchars($settings['mail_tpl_shipped_sub'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Body Content (HTML allowed):</label>
                    <textarea name="settings[mail_tpl_shipped_body]" rows="4"><?php echo htmlspecialchars($settings['mail_tpl_shipped_body'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Order Delivered Template -->
            <div class="card">
                <h3><i data-lucide="home"></i> Order Status - Delivered</h3>
                <p style="font-size:12px; color:#555; margin-bottom:12px;">Variables: <b>{user_name}</b>, <b>{order_id}</b></p>
                <div class="form-group">
                    <label>Subject line:</label>
                    <input type="text" name="settings[mail_tpl_delivered_sub]" value="<?php echo htmlspecialchars($settings['mail_tpl_delivered_sub'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Body Content (HTML allowed):</label>
                    <textarea name="settings[mail_tpl_delivered_body]" rows="4"><?php echo htmlspecialchars($settings['mail_tpl_delivered_body'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <!-- TAB: WHATSAPP -->
        <div class="settings-tab-content" id="tab-content-whatsapp">
            <div class="card">
                <h3>💬 WhatsApp Floating Bubble Settings</h3>
                
                <div class="form-group">
                    <label for="whatsapp_number">WhatsApp Phone Number (with Country Code, no spacing/plus):</label>
                    <input type="text" id="whatsapp_number" name="settings[whatsapp_number]" value="<?php echo htmlspecialchars($settings['whatsapp_number'] ?? '917676626666'); ?>" required>
                    <small style="color: #666;">e.g., 917676626666 for India (+91).</small>
                </div>

                <div class="form-group">
                    <label for="whatsapp_message">Default Pre-filled Message:</label>
                    <input type="text" id="whatsapp_message" name="settings[whatsapp_message]" value="<?php echo htmlspecialchars($settings['whatsapp_message'] ?? 'Hello I want to buy plants'); ?>" required>
                </div>
            </div>
        </div>

        <!-- Save Button for Settings tabs -->
        <div id="save-settings-bar" style="margin-top: 15px; margin-bottom: 40px;">
            <button type="submit" name="save_settings" class="submit-btn"><i data-lucide="save"></i> Save All Settings</button>
        </div>

    </form>

    <!-- TAB: COUPON MANAGEMENT -->
    <div class="settings-tab-content" id="tab-content-coupons">
        
        <!-- Add Coupon Card -->
        <div class="card">
            <h3><i data-lucide="plus-circle"></i> Create Target Promotion Coupon</h3>
            <form method="POST">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div class="form-group">
                        <label for="coupon_code">Coupon Code:</label>
                        <input type="text" id="coupon_code" name="coupon_code" placeholder="e.g. WELCOME50" required style="text-transform: uppercase;">
                    </div>
                    <div class="form-group">
                        <label for="discount_type">Discount Type:</label>
                        <select id="discount_type" name="discount_type" required>
                            <option value="percentage">Percentage (%)</option>
                            <option value="flat">Flat Value (₹)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="discount_value">Value:</label>
                        <input type="number" step="0.01" id="discount_value" name="discount_value" placeholder="e.g. 10 or 150" required>
                    </div>
                    <div class="form-group">
                        <label for="target_type">Target Customer Group:</label>
                        <select id="target_type" name="target_type" required>
                            <option value="all">All Registered Customers</option>
                            <option value="login_offer">New Login Offer</option>
                            <option value="missing_customers">Inactive / Missing Customers</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Coupon Status:</label>
                        <select id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="add_coupon" class="submit-btn" style="width: auto; display: inline-flex; align-items: center; gap: 8px;"><i data-lucide="plus"></i> Create Coupon</button>
            </form>
        </div>

        <!-- Coupons Table -->
        <div class="card">
            <h3><i data-lucide="ticket"></i> Current Coupons List</h3>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Coupon Code</th>
                            <th>Discount</th>
                            <th>Target Group</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($coupons_res) > 0) {
                            while ($cp = mysqli_fetch_assoc($coupons_res)) {
                        ?>
                            <tr>
                                <td><?php echo $cp['id']; ?></td>
                                <td><b style="color: #2e7d32; font-family: monospace; font-size: 15px;"><?php echo htmlspecialchars($cp['code']); ?></b></td>
                                <td><?php echo ($cp['discount_type'] == 'percentage') ? $cp['discount_value'] . '%' : '₹' . $cp['discount_value']; ?></td>
                                <td>
                                    <?php 
                                        if ($cp['target_type'] == 'login_offer') echo 'New Login Welcome';
                                        elseif ($cp['target_type'] == 'missing_customers') echo 'Inactive Customers';
                                        else echo 'All Registered';
                                    ?>
                                </td>
                                <td>
                                    <span style="font-weight: bold; padding: 4px 10px; border-radius: 20px; font-size: 11px;
                                        background: <?php echo $cp['status'] == 'active' ? '#c6f6d5' : '#fed7d7'; ?>;
                                        color: <?php echo $cp['status'] == 'active' ? '#22543d' : '#9b2c2c'; ?>;">
                                        <?php echo ucfirst($cp['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="?delete_coupon=<?php echo $cp['id']; ?>" class="delete-badge-btn" onclick="return confirm('Delete this coupon permanently?')">Delete</a>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else { ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #777; padding: 20px;">No coupons added yet.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DYNAMIC BANNER DIALOG POPUP -->
<dialog id="banner-dialog" style="border: none; padding: 25px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); max-width: 500px; width: 90%; margin: auto;">
    <h3 style="color: #1b5e20; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;"><i data-lucide="megaphone"></i> Create Header Banner</h3>
    
    <div class="form-group">
        <label style="font-weight: bold; display: block; margin-bottom: 8px;">Banner Text Content</label>
        <textarea id="modal-banner-text" placeholder="e.g. 🌿 WEEKDAY SPECIAL 🌿 Buy any plants & get offers!" rows="3" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; outline:none; font-family:inherit; font-size:14px; box-sizing: border-box;"></textarea>
        <small style="color:#666; margin-top: 5px; display:block;">Use HTML tags like &lt;span&gt; for highlighted text.</small>
    </div>
    
    <div class="form-group" style="margin-top: 15px;">
        <label style="font-weight: bold; display: block; margin-bottom: 8px;">Display Days of Week</label>
        <div class="days-checkbox-container" id="modal-days-checkboxes" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
            <!-- Javascript will render checkboxes here, checking for claimed days -->
        </div>
    </div>
    
    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px; border-top: 1px solid #eee; padding-top: 15px;">
        <button type="button" onclick="closeBannerModal()" style="padding: 10px 18px; border: 1px solid #ccc; border-radius: 8px; background: #fff; cursor: pointer; font-weight: bold; color: #555;">Cancel</button>
        <button type="button" onclick="saveNewBanner()" style="padding: 10px 18px; border: none; border-radius: 8px; background: #2e7d32; color: white; cursor: pointer; font-weight: bold;">Add Banner</button>
    </div>
</dialog>

<script>
// Switch settings sections via tabs
function switchSettingsTab(tabName) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.settings-tab-content').forEach(content => content.classList.remove('active'));
    
    const targetBtn = document.getElementById('btn-' + tabName);
    const targetContent = document.getElementById('tab-content-' + tabName);
    const saveBar = document.getElementById('save-settings-bar');
    
    if (targetBtn && targetContent) {
        targetBtn.classList.add('active');
        targetContent.classList.add('active');
    }
    
    // Hide main save settings button on coupon tab since it uses inline forms
    if (saveBar) {
        if (tabName === 'coupons') {
            saveBar.style.display = 'none';
        } else {
            saveBar.style.display = 'block';
        }
    }
}

// Banners list array & mapping
const dayNames = {
    1: "Monday", 2: "Tuesday", 3: "Wednesday", 4: "Thursday",
    5: "Friday", 6: "Saturday", 7: "Sunday"
};

let banners = [];
try {
    const bannersJson = document.getElementById('header-banners-json-input').value;
    banners = JSON.parse(bannersJson || '[]');
} catch(e) {
    banners = [];
}

function renderBannersList() {
    const container = document.getElementById('banners-list-container');
    if (!container) return;
    
    if (banners.length === 0) {
        container.innerHTML = `
            <div style="padding: 30px; text-align: center; border: 2px dashed #ccc; border-radius: 12px; color: #777;">
                No dynamic weekly banners configured. Dynamic weekday/weekend fallback is used.
            </div>
        `;
        return;
    }
    
    let html = '';
    banners.forEach((b, index) => {
        let badgesHtml = '';
        // Draw badges for all 7 days
        for(let d=1; d<=7; d++) {
            let active = b.days.includes(d);
            let nameShort = dayNames[d].substring(0, 3);
            badgesHtml += `<span class="day-badge ${active ? 'active-day' : ''}" title="${dayNames[d]}">${nameShort}</span>`;
        }
        
        html += `
            <div class="banner-item">
                <div style="flex: 1;">
                    <div style="font-weight: bold; font-size: 14px; margin-bottom: 8px; color: #1b5e20;">${b.text}</div>
                    <div>${badgesHtml}</div>
                </div>
                <div>
                    <button type="button" class="delete-badge-btn" onclick="deleteBanner(${index})">Remove</button>
                </div>
            </div>
        `;
    });
    container.innerHTML = html;
}

function updateBannersInput() {
    document.getElementById('header-banners-json-input').value = JSON.stringify(banners);
}

function deleteBanner(index) {
    if(confirm("Remove this targeted banner configuration?")) {
        banners.splice(index, 1);
        updateBannersInput();
        renderBannersList();
    }
}

function openBannerModal() {
    const dialog = document.getElementById('banner-dialog');
    const container = document.getElementById('modal-days-checkboxes');
    
    // Find all day integers that have already been allocated
    let claimedDays = [];
    banners.forEach(b => {
        claimedDays = claimedDays.concat(b.days);
    });
    
    // Generate checkboxes
    let html = '';
    for(let d=1; d<=7; d++) {
        let isClaimed = claimedDays.includes(d);
        html += `
            <label style="display: flex; align-items: center; gap: 8px; cursor: ${isClaimed ? 'not-allowed' : 'pointer'}; opacity: ${isClaimed ? 0.5 : 1}; font-size: 13px;">
                <input type="checkbox" name="modal_days[]" value="${d}" ${isClaimed ? 'disabled' : ''}>
                <span>${dayNames[d]} ${isClaimed ? '<b style="font-size:10px; color:#c53030;">(claimed)</b>' : ''}</span>
            </label>
        `;
    }
    container.innerHTML = html;
    
    // Clear text
    document.getElementById('modal-banner-text').value = '';
    
    dialog.showModal();
}

function closeBannerModal() {
    document.getElementById('banner-dialog').close();
}

function saveNewBanner() {
    const textVal = document.getElementById('modal-banner-text').value.trim();
    if (!textVal) {
        alert("Please enter banner alert text content!");
        return;
    }
    
    // Get checked days
    const checkedBoxes = document.querySelectorAll('input[name="modal_days[]"]:checked');
    if (checkedBoxes.length === 0) {
        alert("Please select at least one active day of the week!");
        return;
    }
    
    let selectedDays = [];
    checkedBoxes.forEach(cb => {
        selectedDays.push(parseInt(cb.value));
    });
    
    banners.push({
        text: textVal,
        days: selectedDays
    });
    
    updateBannersInput();
    renderBannersList();
    closeBannerModal();
}

// Initial draw
document.addEventListener("DOMContentLoaded", () => {
    renderBannersList();
});
</script>

</body>
</html>
