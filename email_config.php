<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// // Email Configuration
// define('SMTP_HOST', 'smtp.gmail.com');  // Gmail SMTP server
// define('SMTP_PORT', 465);               // Gmail SMTP port (TLS)
// define('SMTP_USERNAME', '27aarav.singh04@gmail.com');  // Your Gmail address
// define('SMTP_PASSWORD', 'oprmarkhigksbulj');  // Your app password
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure this loads PHPMailer classes

// ✅ Create the mail object
$mail = new PHPMailer(true);

// ✅ Set SMTP settings for GoDaddy
$mail->isSMTP();
$mail->Host = 'smtpout.secureserver.net';
$mail->Port = 465;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->SMTPAuth = true;
$mail->Username = 'customersupport@rejuvenateganga.com';
$mail->Password = 'AaravAayansh@1';

$mail->setFrom('customersupport@rejuvenateganga.com', 'Rejuvenate Ganga');

// Email Settings
define('SMTP_FROM_EMAIL', 'customersupport@rejuvenateganga.com');
define('SMTP_FROM_NAME', 'Rejuvenate Ganga');
?>