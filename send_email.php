<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the PHPMailer class
require 'vendor/autoload.php';

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';                         // Set the SMTP server to Gmail
    $mail->SMTPAuth = true;                                // Enable SMTP authentication
    $mail->Username = 'yusufyakub023@gmail.com';           // SMTP username (your Gmail address)
    $mail->Password = 'inip zyir ffaq zjjw';                    // SMTP password (use the App Password if using 2FA)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     // Enable TLS encryption
    $mail->Port = 587;                                     // TCP port to connect to

    //Recipients
    $mail->setFrom('yusufyakub023@gmail.comu', 'Admin');    // Sender's email address
    $mail->addAddress('g20sccs1017@gsu.edu.ng', 'Yusuf Yakubu'); // Recipient's email address

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Test Email from PHP Mailer';
    $mail->Body    = 'This is a test email sent using PHP Mailer with Gmail SMTP!';
    $mail->AltBody = 'This is the plain text version of the email';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
