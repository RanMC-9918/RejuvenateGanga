<?php
require 'email_config.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendVerificationEmail($to, $subject, $htmlMessage) {
    $mail = new PHPMailer(true);
    $logFile = '/Applications/XAMPP/xamppfiles/htdocs/email_debug.log';

    try {
        // Enable verbose debug output
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = function($str, $level) use ($logFile) {
            $timestamp = date('Y-m-d H:i:s');
            file_put_contents($logFile, "[$timestamp] PHPMailer Debug [$level]: $str\n", FILE_APPEND);
        };

        // Server settings
        // $mail->isSMTP();
        // $mail->Host = 'smtp.gmail.com';  // Gmail SMTP host
        // $mail->SMTPAuth = true;
        // $mail->Username = '27aarav.singh04@gmail.com';  // Gmail email
        // $mail->Password = 'oprmarkhigksbulj';  // Gmail app password
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        // $mail->Port = 465;
        $mail->Host = 'smtpout.secureserver.net';
        $mail->Port = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Username = 'customersupport@rejuvenateganga.com';
        $mail->Password = 'AaravAayansh@1';
        
        // Additional SMTP options
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Recipients
        $mail->setFrom('customersupport@rejuvenateganga.com', 'Rejuvenate Ganga');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlMessage;

        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] Attempting to send email to: $to\n", FILE_APPEND);
        $mail->send();
        file_put_contents($logFile, "[$timestamp] Email sent successfully to: $to\n", FILE_APPEND);
        return true;
    } catch (Exception $e) {
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] Failed to send email to: $to\n", FILE_APPEND);
        file_put_contents($logFile, "[$timestamp] PHPMailer Error: " . $mail->ErrorInfo . "\n", FILE_APPEND);
        file_put_contents($logFile, "[$timestamp] Exception Message: " . $e->getMessage() . "\n", FILE_APPEND);
        file_put_contents($logFile, "[$timestamp] Exception Trace: " . $e->getTraceAsString() . "\n", FILE_APPEND);
        return false;
    }
}

function sendDonationConfirmationEmail($to, $name, $amount, $paymentMethod, $transactionId, $date) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = function($str, $level) {
            error_log("PHPMailer Debug [$level]: $str");
        };
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Gmail SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = '27aarav.singh04@gmail.com';  // Gmail email
        $mail->Password = 'oprmarkhigksbulj';  // Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Additional SMTP options
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Recipients
        $mail->setFrom('27aarav.singh04@gmail.com', 'Rejuvenate Ganga');
        $mail->addAddress($to, $name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Donation Verification - Rejuvenate Ganga';
        
        // Email body with improved styling
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f8f9fa; }
                    .details { margin: 20px 0; }
                    .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
                    .amount { font-size: 24px; color: #28a745; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Donation Verified!</h2>
                    </div>
                    <div class='content'>
                        <p>Dear $name,</p>
                        <p>We are pleased to inform you that your donation to Rejuvenate Ganga has been verified and processed successfully.</p>
                        
                        <div class='details'>
                            <h3>Donation Details:</h3>
                            <p><strong>Name:</strong> $name</p>
                            <p><strong>Amount:</strong> <span class='amount'>$$amount</span></p>
                            <p><strong>Payment Method:</strong> $paymentMethod</p>
                            <p><strong>Transaction ID:</strong> $transactionId</p>
                            <p><strong>Date of Donation:</strong> $date</p>
                        </div>
                        
                        <p>Thank you for your generous contribution to our cause. Your support helps us in our mission to rejuvenate the Ganga River.</p>
                        
                        <p>If you have any questions, please don't hesitate to contact us.</p>
                        
                        <p>Best regards,<br>The Rejuvenate Ganga Team</p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated message. Please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        // Plain text version for email clients that don't support HTML
        $mail->AltBody = "Dear $name,\n\n" .
            "We are pleased to inform you that your donation to Rejuvenate Ganga has been verified and processed successfully.\n\n" .
            "Donation Details:\n" .
            "Name: $name\n" .
            "Amount: $$amount\n" .
            "Payment Method: $paymentMethod\n" .
            "Transaction ID: $transactionId\n" .
            "Date of Donation: $date\n\n" .
            "Thank you for your generous contribution to our cause. Your support helps us in our mission to rejuvenate the Ganga River.\n\n" .
            "If you have any questions, please don't hesitate to contact us.\n\n" .
            "Best regards,\nThe Rejuvenate Ganga Team";
        
        // Send the email
        $mail->send();
        error_log("Email sent successfully to: $to");
        return true;
    } catch (Exception $e) {
        error_log("Failed to send email to $to. Error: " . $mail->ErrorInfo);
        error_log("Exception message: " . $e->getMessage());
        error_log("Exception trace: " . $e->getTraceAsString());
        return false;
    }
}
?>