<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Adjust path based on execution location. mailer.php is in /config/
require_once __DIR__ . '/../src/Exception.php';
require_once __DIR__ . '/../src/PHPMailer.php';
require_once __DIR__ . '/../src/SMTP.php';

function send_nursery_email($to, $tpl_key, $variables = []) {
    // We need a DB connection. If $conn is not global, we can connect or fetch it.
    global $conn;
    if (!isset($conn)) {
        include __DIR__ . '/db.php';
    }
    
    // 1. Fetch SMTP settings
    $settings_res = mysqli_query($conn, "SELECT * FROM settings WHERE setting_key LIKE 'smtp_%' OR setting_key LIKE 'global_offer_%' OR setting_key LIKE 'mail_tpl_%'");
    $settings = [];
    while ($row = mysqli_fetch_assoc($settings_res)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    $host = $settings['smtp_host'] ?? 'smtp.gmail.com';
    $port = intval($settings['smtp_port'] ?? '587');
    $user = $settings['smtp_user'] ?? 'kallurviresh39@gmail.com';
    $pass = $settings['smtp_pass'] ?? 'znamlbvnmkiysvkb';
    $secure = $settings['smtp_secure'] ?? 'tls';
    $from_email = $settings['smtp_from_email'] ?? 'vireshmmustapure39@gmail.com';
    $from_name = $settings['smtp_from_name'] ?? 'Hani Nursery';
    
    $offer_code = $settings['global_offer_code'] ?? 'HANI10';
    $offer_value = $settings['global_offer_value'] ?? '10%';
    
    // 2. Fetch template
    $sub_key = "mail_tpl_" . $tpl_key . "_sub";
    $body_key = "mail_tpl_" . $tpl_key . "_body";
    
    $subject = $settings[$sub_key] ?? "Update from Hani Nursery";
    $body = $settings[$body_key] ?? "<p>This is an automated notification from Hani Nursery.</p>";
    
    // 3. Add default variables
    $variables['{offer_code}'] = $offer_code;
    $variables['{offer_value}'] = $offer_value;
    if (!isset($variables['{user_name}'])) {
        $variables['{user_name}'] = $_SESSION['username'] ?? 'Valued Customer';
    }
    
    // 4. Replace placeholders
    foreach ($variables as $placeholder => $val) {
        $subject = str_replace($placeholder, $val, $subject);
        $body = str_replace($placeholder, $val, $body);
    }
    
    // 5. Send mail via PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $user;
        $mail->Password   = $pass;
        $mail->SMTPSecure = $secure;
        $mail->Port       = $port;
        
        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($to);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error
        error_log("PHPMailer failed: " . $mail->ErrorInfo);
        return false;
    }
}
?>
