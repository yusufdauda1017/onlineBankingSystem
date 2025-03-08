<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/db/db_connect.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();

// Mailgun API credentials
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
            // Mailgun API Endpoint
            $mailgunUrl = "https://api.mailgun.net/v3/$mailgunDomain/messages";

            // Prepare POST data
            $postData = http_build_query([
                'from'    => "$name <$email>",
                'to'      => $recipientEmail,
                'subject' => $subject,
                'html'    => $emailBody
            ]);

            // Mailgun API headers
            $authHeader = base64_encode("api:$mailgunApiKey");

            // HTTP context options
            $context = stream_context_create([
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Authorization: Basic $authHeader\r\n".
                                 "Content-Type: application/x-www-form-urlencoded\r\n",
                    'content' => $postData
                ]
            ]);

            // Send request using file_get_contents()
            $response = file_get_contents($mailgunUrl, false, $context);

            // Check response
            if ($response !== false) {
                echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    }
}
?>
