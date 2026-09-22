<?php
session_start();
include '../../../config/db.php';

if(!isset($_SESSION['user'])){
    header("Location: ../../login/");
    exit();
}

$user_id = intval($_SESSION['user']);
$msg = "";
$error = "";
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

// Handle delete address
if (isset($_GET['delete_address'])) {
    $addr_id = intval($_GET['delete_address']);
    mysqli_query($conn, "DELETE FROM addresses WHERE id=$addr_id AND user_id=$user_id");
    // Ensure at least one default address exists
    $check_default = mysqli_query($conn, "SELECT id FROM addresses WHERE user_id=$user_id AND is_default=1");
    if (mysqli_num_rows($check_default) == 0) {
        mysqli_query($conn, "UPDATE addresses SET is_default=1 WHERE user_id=$user_id LIMIT 1");
    }
    header("Location: index.php?tab=addresses&msg=Address deleted successfully!");
    exit();
}

// Handle set default address
if (isset($_GET['set_default'])) {
    $addr_id = intval($_GET['set_default']);
    mysqli_query($conn, "UPDATE addresses SET is_default=0 WHERE user_id=$user_id");
    mysqli_query($conn, "UPDATE addresses SET is_default=1 WHERE id=$addr_id AND user_id=$user_id");
    header("Location: index.php?tab=addresses&msg=Default address updated!");
    exit();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $mobile = mysqli_real_escape_string($conn, trim($_POST['mobile']));
    $new_password = $_POST['new_password'];
    
    if(!empty($name) && !empty($email) && !empty($mobile)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address. ❌";
        } else {
            $update_sql = "UPDATE users SET name='$name', email='$email', mobile='$mobile' WHERE id=$user_id";
            if(mysqli_query($conn, $update_sql)) {
                $_SESSION['username'] = $name;
                $_SESSION['email'] = $email;
                $msg = "Profile updated successfully! ✅";
                
                if(!empty($new_password)) {
                    if(strlen($new_password) < 6) {
                        $error = "Password must be at least 6 characters long. ❌";
                    } else {
                        $pass_hash = password_hash($new_password, PASSWORD_DEFAULT);
                        if(mysqli_query($conn, "UPDATE users SET password='$pass_hash' WHERE id=$user_id")) {
                            $msg = "Profile and password updated successfully! ✅";
                        } else {
                            $error = "Error updating password.";
                        }
                    }
                }
            } else {
                $error = "Error updating profile details.";
            }
        }
    } else {
        $error = "All fields except password are required. ❌";
    }
}

// Handle Add Address
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_address'])) {
    $customer_name = mysqli_real_escape_string($conn, trim($_POST['customer_name']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $pincode = mysqli_real_escape_string($conn, trim($_POST['pincode']));
    $state = mysqli_real_escape_string($conn, trim($_POST['state']));
    $city = mysqli_real_escape_string($conn, trim($_POST['city']));
    $landmark = mysqli_real_escape_string($conn, trim($_POST['landmark']));
    $address_type = mysqli_real_escape_string($conn, trim($_POST['address_type']));
    $is_default = isset($_POST['is_default']) ? 1 : 0;
    
    if(!empty($customer_name) && !empty($phone) && !empty($address) && !empty($pincode) && !empty($city) && !empty($state)) {
        if ($is_default) {
            mysqli_query($conn, "UPDATE addresses SET is_default = 0 WHERE user_id = $user_id");
        }
        
        $check_first = mysqli_query($conn, "SELECT id FROM addresses WHERE user_id = $user_id");
        if (mysqli_num_rows($check_first) == 0) {
            $is_default = 1;
        }
        
        $insert = "INSERT INTO addresses (user_id, customer_name, address, phone, pincode, state, city, landmark, address_type, is_default)
                   VALUES ($user_id, '$customer_name', '$address', '$phone', '$pincode', '$state', '$city', '$landmark', '$address_type', $is_default)";
        if(mysqli_query($conn, $insert)) {
            $msg = "Address added successfully! ✅";
            $active_tab = 'addresses';
        } else {
            $error = "Error adding address.";
        }
    } else {
        $error = "Please fill in all required fields for address.";
    }
}

// Handle Edit Address
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_address'])) {
    $addr_id = intval($_POST['address_id']);
    $customer_name = mysqli_real_escape_string($conn, trim($_POST['customer_name']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $pincode = mysqli_real_escape_string($conn, trim($_POST['pincode']));
    $state = mysqli_real_escape_string($conn, trim($_POST['state']));
    $city = mysqli_real_escape_string($conn, trim($_POST['city']));
    $landmark = mysqli_real_escape_string($conn, trim($_POST['landmark']));
    $address_type = mysqli_real_escape_string($conn, trim($_POST['address_type']));
    $is_default = isset($_POST['is_default']) ? 1 : 0;
    
    if(!empty($customer_name) && !empty($phone) && !empty($address) && !empty($pincode) && !empty($city) && !empty($state)) {
        if ($is_default) {
            mysqli_query($conn, "UPDATE addresses SET is_default = 0 WHERE user_id = $user_id");
        }
        
        $update = "UPDATE addresses SET 
                   customer_name='$customer_name', 
                   phone='$phone', 
                   address='$address', 
                   pincode='$pincode', 
                   state='$state', 
                   city='$city', 
                   landmark='$landmark', 
                   address_type='$address_type', 
                   is_default=$is_default 
                   WHERE id=$addr_id AND user_id=$user_id";
        if(mysqli_query($conn, $update)) {
            $msg = "Address updated successfully! ✅";
            $active_tab = 'addresses';
        } else {
            $error = "Error updating address.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

if(isset($_GET['msg'])) {
    $msg = $_GET['msg'];
}

// Fetch user data
$user_data = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

// Fetch addresses
$addresses_res = mysqli_query($conn, "SELECT * FROM addresses WHERE user_id=$user_id ORDER BY is_default DESC, id DESC");

// Fetch address for edit
$edit_address = null;
if (isset($_GET['edit_address_id'])) {
    $edit_id = intval($_GET['edit_address_id']);
    $edit_q = mysqli_query($conn, "SELECT * FROM addresses WHERE id=$edit_id AND user_id=$user_id");
    if (mysqli_num_rows($edit_q) > 0) {
        $edit_address = mysqli_fetch_assoc($edit_q);
        $active_tab = 'edit_address';
    }
}

$base = "../../../";
include '../../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Account Settings & Addresses - Hani Nursery</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .settings-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px 60px;
        }

        .tab-box {
            display: flex;
            gap: 15px;
            border-bottom: 2px solid #e8f5e9;
            margin-bottom: 30px;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            color: var(--text-muted);
            border-bottom: 3px solid transparent;
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab-btn.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .tab-btn:hover {
            color: var(--primary-light);
        }

        .settings-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 35px;
            box-shadow: var(--shadow-sm);
            border: 1px solid #eee;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--text-dark);
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border-radius: var(--radius-sm);
            border: 1px solid #ddd;
            font-size: 15px;
            outline: none;
            transition: var(--transition-smooth);
            background: white;
        }

        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .msg-alert {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px 18px;
            border-radius: var(--radius-sm);
            margin-bottom: 25px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .error-alert {
            background: #ffebee;
            color: #c62828;
            padding: 12px 18px;
            border-radius: var(--radius-sm);
            margin-bottom: 25px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Saved Addresses layout */
        .address-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .address-card {
            background: white;
            border: 1px solid #eee;
            border-radius: var(--radius-md);
            padding: 20px;
            position: relative;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-smooth);
        }

        .address-card.default {
            border-color: var(--primary-color);
            background: #fcfdfc;
        }

        .address-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .address-type-tag {
            background: #e8f5e9;
            color: var(--primary-dark);
            padding: 4px 10px;
            border-radius: var(--radius-full);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .default-badge {
            background: var(--primary-color);
            color: white;
            padding: 4px 10px;
            border-radius: var(--radius-full);
            font-size: 11px;
            font-weight: 700;
        }

        .address-card-body p {
            margin: 5px 0;
            font-size: 14px;
            line-height: 1.5;
            color: var(--text-dark);
        }

        .address-actions {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            border-top: 1px solid #f5f5f5;
            padding-top: 12px;
            font-size: 13px;
        }

        .address-actions a {
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .address-actions a.delete {
            color: var(--accent-red);
        }
    </style>
</head>
<body>

<div class="settings-container">
    
    <!-- Tab Controls -->
    <div class="tab-box">
        <button class="tab-btn <?php echo $active_tab === 'profile' ? 'active' : ''; ?>" onclick="switchTab('profile')">
            <i data-lucide="user"></i> Profile Settings
        </button>
        <button class="tab-btn <?php echo ($active_tab === 'addresses' || $active_tab === 'add_address' || $active_tab === 'edit_address') ? 'active' : ''; ?>" onclick="switchTab('addresses')">
            <i data-lucide="map-pin"></i> Saved Addresses
        </button>
    </div>

    <!-- Alert Messages -->
    <?php if ($msg != "") { ?>
        <div class="msg-alert"><i data-lucide="check-circle"></i> <?php echo $msg; ?></div>
    <?php } ?>
    <?php if ($error != "") { ?>
        <div class="error-alert"><i data-lucide="alert-circle"></i> <?php echo $error; ?></div>
    <?php } ?>

    <!-- TAB 1: Profile Settings -->
    <div id="tab-profile" style="display: <?php echo $active_tab === 'profile' ? 'block' : 'none'; ?>;">
        <div class="settings-card">
            <h2 class="serif-font" style="margin-top: 0; color: var(--primary-dark); font-size: 24px; margin-bottom: 20px;">
                👤 Edit Profile Settings
            </h2>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="save_profile" value="1">
                
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_data['name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile Number:</label>
                    <input type="text" id="mobile" name="mobile" value="<?php echo htmlspecialchars($user_data['mobile'] ?? ''); ?>" required>
                </div>

                <div class="form-group" style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                    <label for="new_password">Change Password (leave empty to keep current):</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password (min. 6 chars)">
                </div>

                <button type="submit" class="btn-premium" style="width: 100%; box-shadow: none; margin-top: 10px; text-shadow:none;">
                    Save Profile Changes
                </button>
            </form>
        </div>
    </div>

    <!-- TAB 2: Saved Addresses Listing -->
    <div id="tab-addresses" style="display: <?php echo $active_tab === 'addresses' ? 'block' : 'none'; ?>;">
        <div class="settings-card">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f4faf4; padding-bottom: 12px; margin-bottom: 20px;">
                <h2 class="serif-font" style="margin:0; color: var(--primary-dark); font-size: 24px;">
                    📍 Saved Delivery Addresses
                </h2>
                <button type="button" class="btn-premium" style="padding: 8px 16px; font-size: 13px; text-shadow: none; box-shadow: none;" onclick="showAddAddressForm()">
                    + Add New Address
                </button>
            </div>

            <?php if (mysqli_num_rows($addresses_res) > 0) { ?>
                <div class="address-grid">
                    <?php while ($addr = mysqli_fetch_assoc($addresses_res)) { ?>
                        <div class="address-card <?php echo $addr['is_default'] ? 'default' : ''; ?>">
                            <div class="address-card-header">
                                <span class="address-type-tag"><?php echo htmlspecialchars($addr['address_type']); ?></span>
                                <?php if ($addr['is_default']) { ?>
                                    <span class="default-badge">Default</span>
                                <?php } ?>
                            </div>
                            <div class="address-card-body">
                                <p><strong><?php echo htmlspecialchars($addr['customer_name']); ?></strong></p>
                                <p><?php echo htmlspecialchars($addr['address']); ?></p>
                                <p><?php echo htmlspecialchars($addr['city']) . ', ' . htmlspecialchars($addr['state']) . ' - ' . htmlspecialchars($addr['pincode']); ?></p>
                                <?php if (!empty($addr['landmark'])) { ?>
                                    <p style="font-size:12px; color:#666;">Landmark: <?php echo htmlspecialchars($addr['landmark']); ?></p>
                                <?php } ?>
                                <p>Phone: <?php echo htmlspecialchars($addr['phone']); ?></p>
                            </div>
                            <div class="address-actions">
                                <a href="?edit_address_id=<?php echo $addr['id']; ?>"><i data-lucide="edit-3" style="width:14px; height:14px;"></i> Edit</a>
                                <?php if (!$addr['is_default']) { ?>
                                    <a href="?set_default=<?php echo $addr['id']; ?>"><i data-lucide="check" style="width:14px; height:14px;"></i> Set Default</a>
                                    <a class="delete" href="?delete_address=<?php echo $addr['id']; ?>" onclick="return confirm('Delete this address?')"><i data-lucide="trash-2" style="width:14px; height:14px;"></i> Delete</a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p style="text-align: center; color: #777; font-style: italic; padding: 20px 0;">
                    You have not saved any addresses yet. Add one to checkout faster! 🏡
                </p>
            <?php } ?>
        </div>
    </div>

    <!-- TAB 3: Add Address Form -->
    <div id="tab-add_address" style="display: <?php echo $active_tab === 'add_address' ? 'block' : 'none'; ?>;">
        <div class="settings-card">
            <h2 class="serif-font" style="margin-top: 0; color: var(--primary-dark); font-size: 24px; margin-bottom: 20px;">
                🏡 Add New Delivery Address
            </h2>
            <form method="POST">
                <input type="hidden" name="add_address" value="1">
                
                <div class="form-group">
                    <label for="customer_name">Contact Person Name *</label>
                    <input type="text" id="customer_name" name="customer_name" required placeholder="Enter full name">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="text" id="phone" name="phone" required placeholder="10-digit mobile number">
                </div>

                <div class="form-group">
                    <label for="address">Full Address *</label>
                    <textarea id="address" name="address" rows="3" required placeholder="Flat, House no., Building, Company, Apartment, Street address"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="city">City / District *</label>
                        <input type="text" id="city" name="city" required placeholder="e.g. Kalaburagi">
                    </div>
                    <div class="form-group">
                        <label for="state">State *</label>
                        <input type="text" id="state" name="state" required placeholder="e.g. Karnataka">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="pincode">Pincode *</label>
                        <input type="text" id="pincode" name="pincode" required placeholder="6-digit postal code">
                    </div>
                    <div class="form-group">
                        <label for="landmark">Landmark (Optional)</label>
                        <input type="text" id="landmark" name="landmark" placeholder="e.g. Opp Zudio showroom">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; align-items: center;">
                    <div class="form-group">
                        <label for="address_type">Address Type</label>
                        <select id="address_type" name="address_type">
                            <option value="Home">🏡 Home (All day delivery)</option>
                            <option value="Work">🏢 Work (9 AM - 5 PM delivery)</option>
                            <option value="Other">📍 Other</option>
                        </select>
                    </div>
                    <div class="form-group" style="padding-top: 20px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: normal; cursor: pointer;">
                            <input type="checkbox" name="is_default" value="1" style="width: auto; margin:0;">
                            <span>Make this my default address</span>
                        </label>
                    </div>
                </div>

                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <button type="submit" class="btn-premium" style="flex:1; box-shadow: none; text-shadow:none;">
                        Save Address
                    </button>
                    <button type="button" class="btn-premium" style="background:#888; border-color:#888; color:white; flex:1; box-shadow: none; text-shadow:none;" onclick="switchTab('addresses')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 4: Edit Address Form -->
    <div id="tab-edit_address" style="display: <?php echo $active_tab === 'edit_address' ? 'block' : 'none'; ?>;">
        <?php if ($edit_address) { ?>
            <div class="settings-card">
                <h2 class="serif-font" style="margin-top: 0; color: var(--primary-dark); font-size: 24px; margin-bottom: 20px;">
                    🏡 Edit Delivery Address
                </h2>
                <form method="POST">
                    <input type="hidden" name="update_address" value="1">
                    <input type="hidden" name="address_id" value="<?php echo $edit_address['id']; ?>">
                    
                    <div class="form-group">
                        <label for="edit_customer_name">Contact Person Name *</label>
                        <input type="text" id="edit_customer_name" name="customer_name" value="<?php echo htmlspecialchars($edit_address['customer_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_phone">Phone Number *</label>
                        <input type="text" id="edit_phone" name="phone" value="<?php echo htmlspecialchars($edit_address['phone']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_address">Full Address *</label>
                        <textarea id="edit_address" name="address" rows="3" required><?php echo htmlspecialchars($edit_address['address']); ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="edit_city">City / District *</label>
                            <input type="text" id="edit_city" name="city" value="<?php echo htmlspecialchars($edit_address['city']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_state">State *</label>
                            <input type="text" id="edit_state" name="state" value="<?php echo htmlspecialchars($edit_address['state']); ?>" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="edit_pincode">Pincode *</label>
                            <input type="text" id="edit_pincode" name="pincode" value="<?php echo htmlspecialchars($edit_address['pincode']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_landmark">Landmark (Optional)</label>
                            <input type="text" id="edit_landmark" name="landmark" value="<?php echo htmlspecialchars($edit_address['landmark']); ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; align-items: center;">
                        <div class="form-group">
                            <label for="edit_address_type">Address Type</label>
                            <select id="edit_address_type" name="address_type">
                                <option value="Home" <?php echo $edit_address['address_type'] === 'Home' ? 'selected' : ''; ?>>🏡 Home (All day delivery)</option>
                                <option value="Work" <?php echo $edit_address['address_type'] === 'Work' ? 'selected' : ''; ?>>🏢 Work (9 AM - 5 PM delivery)</option>
                                <option value="Other" <?php echo $edit_address['address_type'] === 'Other' ? 'selected' : ''; ?>>📍 Other</option>
                            </select>
                        </div>
                        <div class="form-group" style="padding-top: 20px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: normal; cursor: pointer;">
                                <input type="checkbox" name="is_default" value="1" style="width: auto; margin:0;" <?php echo $edit_address['is_default'] ? 'checked' : ''; ?>>
                                <span>Make this my default address</span>
                            </label>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px; margin-top: 20px;">
                        <button type="submit" class="btn-premium" style="flex:1; box-shadow: none; text-shadow:none;">
                            Save Address
                        </button>
                        <button type="button" class="btn-premium" style="background:#888; border-color:#888; color:white; flex:1; box-shadow: none; text-shadow:none;" onclick="switchTab('addresses')">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        <?php } ?>
    </div>

</div>

<?php include '../../../includes/footer.php'; ?>

<script>
    function switchTab(tabId) {
        document.getElementById('tab-profile').style.display = 'none';
        document.getElementById('tab-addresses').style.display = 'none';
        document.getElementById('tab-add_address').style.display = 'none';
        document.getElementById('tab-edit_address').style.display = 'none';
        
        let profileBtn = document.querySelector('button[onclick="switchTab(\'profile\')"]');
        let addressesBtn = document.querySelector('button[onclick="switchTab(\'addresses\')"]');
        
        profileBtn.classList.remove('active');
        addressesBtn.classList.remove('active');
        
        if (tabId === 'profile') {
            document.getElementById('tab-profile').style.display = 'block';
            profileBtn.classList.add('active');
        } else if (tabId === 'addresses') {
            document.getElementById('tab-addresses').style.display = 'block';
            addressesBtn.classList.add('active');
        }
    }

    function showAddAddressForm() {
        document.getElementById('tab-profile').style.display = 'none';
        document.getElementById('tab-addresses').style.display = 'none';
        document.getElementById('tab-edit_address').style.display = 'none';
        document.getElementById('tab-add_address').style.display = 'block';
    }
</script>

</body>
</html>