<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/db.php';

$msg = "";
$msg_type = "";

// Handle Form Submission
if (isset($_POST['submit_inquiry'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $mobile = mysqli_real_escape_string($conn, trim($_POST['mobile']));
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));

    if (!empty($name) && !empty($email) && !empty($mobile) && !empty($subject) && !empty($message)) {
        // Ensure table exists
        $create_table = "CREATE TABLE IF NOT EXISTS contact_inquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            mobile VARCHAR(20) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->query($create_table);

        $insert = "INSERT INTO contact_inquiries (name, email, mobile, subject, message) 
                   VALUES ('$name', '$email', '$mobile', '$subject', '$message')";
        
        if ($conn->query($insert)) {
            $msg = "Thank you! Your message has been received. We will get back to you shortly. 🌿";
            $msg_type = "success";
        } else {
            $msg = "Something went wrong. Please try again.";
            $msg_type = "error";
        }
    } else {
        $msg = "Please fill in all fields.";
        $msg_type = "error";
    }
}

// Fetch WhatsApp details from settings
$whatsapp_num = '917676626666';
$settings_query = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key='whatsapp_number' LIMIT 1");
if ($settings_query && $settings_row = mysqli_fetch_assoc($settings_query)) {
    $whatsapp_num = $settings_row['setting_value'];
}

$whatsapp_msg = 'Hello Hani Nursery';
$message_query = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key='whatsapp_message' LIMIT 1");
if ($message_query && $message_row = mysqli_fetch_assoc($message_query)) {
    $whatsapp_msg = $message_row['setting_value'];
}

include '../includes/header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Us - Hani Nursery</title>
    <style>
        .contact-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .contact-title-box {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .contact-title-box h2 {
            font-size: 38px;
            color: var(--primary-dark);
            margin-bottom: 10px;
        }
        
        .contact-title-box p {
            color: var(--text-muted);
            font-size: 16px;
        }

        .contact-layout {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            margin-bottom: 60px;
        }

        .contact-info-col {
            flex: 1;
            min-width: 320px;
            background: white;
            padding: 40px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(46, 125, 50, 0.1);
        }

        .contact-info-col h3 {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 25px;
            border-bottom: 2px solid #f1f8e9;
            padding-bottom: 10px;
        }

        .info-item {
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }

        .info-icon {
            font-size: 22px;
            color: var(--primary-color);
        }

        .info-text strong {
            display: block;
            font-size: 15px;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .info-text p {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .contact-form-col {
            flex: 1.2;
            min-width: 320px;
            background: white;
            padding: 40px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(46, 125, 50, 0.1);
        }

        .contact-form-col h3 {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 25px;
            border-bottom: 2px solid #f1f8e9;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            border: 1px solid #ddd;
            outline: none;
            font-size: 14px;
            transition: var(--transition-smooth);
        }

        .form-group input:focus, .form-group textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .contact-alert {
            padding: 15px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .contact-map-section {
            background: white;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(46, 125, 50, 0.1);
            margin-bottom: 60px;
        }

        .contact-map-section iframe {
            width: 100%;
            height: 450px;
            border: none;
        }

        @media(max-width: 768px) {
            .contact-layout {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="contact-container">
    
    <div class="contact-title-box">
        <h2 class="serif-font">Get in Touch with Hani Nursery 🌿</h2>
        <p>Have questions about plants, bulk pricing, or landscaping? Let's start a conversation.</p>
    </div>

    <div class="contact-layout">
        <!-- Info column -->
        <div class="contact-info-col">
            <h3>📍 Contact Details</h3>
            
            <div class="info-item">
                <div class="info-icon">🏢</div>
                <div class="info-text">
                    <strong>Nursery Address</strong>
                    <p>Hani Nursery & Gardening Center<br>
                    P&T Cross, Old Jewargi Rd<br>
                    Near Zudio, Opp Sai Bazar<br>
                    Kalaburagi, Karnataka - 585102</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">📞</div>
                <div class="info-text">
                    <strong>Phone Support</strong>
                    <p>+91 7676626666</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">✉</div>
                <div class="info-text">
                    <strong>Email Address</strong>
                    <p>support@haninursery.com</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">🕒</div>
                <div class="info-text">
                    <strong>Operating Hours</strong>
                    <p>8:00 AM - 8:00 PM (Daily)</p>
                </div>
            </div>

            <div style="margin-top: 40px; display: flex; flex-direction: column; gap: 10px;">
                <a href="https://maps.app.goo.gl/zZooo2P3QFCKZiSi7" target="_blank" class="btn-premium" style="text-align: center; text-decoration: none; text-shadow: none;">
                    📍 Get Driving Directions
                </a>
                <a href="https://wa.me/<?php echo $whatsapp_num; ?>?text=<?php echo urlencode($whatsapp_msg); ?>" target="_blank" class="btn-premium" style="background: #25D366; border-color: #25D366; text-align: center; text-decoration: none; text-shadow: none; color: white !important;">
                    💬 WhatsApp Inquiry
                </a>
            </div>
        </div>

        <!-- Form Column -->
        <div class="contact-form-col">
            <h3>✉ Send a Message</h3>
            
            <?php if (!empty($msg)) { ?>
                <div class="contact-alert alert-<?php echo $msg_type; ?>">
                    <?php echo $msg; ?>
                </div>
            <?php } ?>

            <form method="POST">
                <div class="form-group">
                    <label for="name">Your Name *</label>
                    <input type="text" id="name" name="name" required placeholder="Enter your full name">
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email address">
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile Number *</label>
                    <input type="text" id="mobile" name="mobile" required placeholder="Enter your phone number">
                </div>

                <div class="form-group">
                    <label for="subject">Subject *</label>
                    <input type="text" id="subject" name="subject" required placeholder="How can we help you?">
                </div>

                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" rows="5" required placeholder="Type your message here..."></textarea>
                </div>

                <button type="submit" name="submit_inquiry" class="btn-premium" style="width: auto; padding: 12px 35px; box-shadow: none;">
                    Submit Inquiry ➔
                </button>
            </form>
        </div>
    </div>

    <!-- Map Section -->
    <div class="contact-map-section">
        <iframe
            src="https://www.google.com/maps?q=Hani+Nursery+Kalaburagi&output=embed"
            loading="lazy"
            allowfullscreen>
        </iframe>
    </div>

</div>

<!-- Footer -->
<?php include '../includes/footer.php'; ?>

</body>
</html>
