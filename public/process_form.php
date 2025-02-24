<?php
require './db/db_connect.php'; // Database connection
require_once __DIR__ . '/vendor/autoload.php';

use Mailgun\Mailgun;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/./');
$dotenv->load();

// Get Mailgun credentials
$mailgunApiKey = $_ENV['MAILGUN_API_KEY'];
$mailgunDomain = $_ENV['MAILGUN_DOMAIN'];
$recipientEmail = "support@trustp.me";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = nl2br(htmlspecialchars($_POST['message']));

    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {

        // Styled HTML Email Template
        $emailBody = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px; }
                .container { max-width: 600px; margin: auto; background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
                h2 { color: #333; }
                p { font-size: 16px; color: #555; line-height: 1.5; }
                .footer { margin-top: 20px; font-size: 12px; color: #999; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>New Contact Form Submission</h2>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Subject:</strong> $subject</p>
                <p><strong>Message:</strong><br> $message</p>
                <hr>
                <p class='footer'>This email was sent from your website's contact form.</p>
            </div>
        </body>
        </html>";

        try {
            // Initialize Mailgun
            $mg = Mailgun::create($mailgunApiKey);

            // Send the email
            $result = $mg->messages()->send($mailgunDomain, [
                'from'    => "$name <$email>",
                'to'      => $recipientEmail,
                'subject' => $subject,
                'html'    => $emailBody // Send as HTML
            ]);

            echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'There was an error sending your message: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    }
}
?>
