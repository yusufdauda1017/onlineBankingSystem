<?php
require '../db/db_connect.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();

// Mailgun credentials
$mailgunApiKey = $_ENV['MAILGUN_API_KEY'];
$mailgunDomain = $_ENV['MAILGUN_DOMAIN'];
$secretKey = $_ENV['SECRET_KEY']; // Security key for HMAC

global $conn; // Ensure $conn is available

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["token"], $_POST["password"])) {
    try {
        $token = trim($_POST["token"]);
        $newPassword = trim($_POST["password"]);
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        // Hash new password securely
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        if (!$hashedPassword) {
            echo json_encode(["status" => "error", "message" => "Password hashing failed."]);
            exit();
        }

        // Validate Token (Check Expiry & Match User)
        $stmt = $conn->prepare("SELECT email, reset_expiry FROM users WHERE reset_token = ? AND reset_expiry IS NOT NULL");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            echo json_encode(["status" => "error", "message" => "Invalid or expired reset link!"]);
            exit();
        }

        $email = $user['email'];
        $expiryTime = strtotime($user['reset_expiry']);

        // Check if the reset link has expired
        if (time() > $expiryTime) {
            $stmt = $conn->prepare("UPDATE users SET reset_token = NULL, reset_expiry = NULL WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->close();

            echo json_encode(["status" => "error", "message" => "Reset link has expired! Request a new one."]);
            exit();
        }

        // Update password and remove token
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);
        if (!$stmt->execute()) {
            error_log("Database Error: " . $stmt->error);
            echo json_encode(["status" => "error", "message" => "Database update failed!"]);
            exit();
        }
        $stmt->close();

        // Log reset attempt
        $stmt = $conn->prepare("INSERT INTO password_reset_logs (email, ip_address, timestamp) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $email, $ipAddress);
        if (!$stmt->execute()) {
            error_log("Database Error: " . $stmt->error);
        }
        $stmt->close();

        // Mailgun API Endpoint
        $mailgunUrl = "https://api.mailgun.net/v3/$mailgunDomain/messages";

        // Prepare POST data
        $postData = http_build_query([
            'from'    => 'Trustpoint@trustp.me',
            'to'      => $email,
            'subject' => 'Your Password Has Been Changed',
            'html'    => "
                <div style='background-color: #f4f4f4; padding: 20px; font-family: Arial, sans-serif; text-align: center;'>
                    <div style='background-color: #ffffff; border-radius: 8px; padding: 20px; max-width: 500px; margin: auto;'>
                        <h1 style='color: #333;'>Password Changed Successfully</h1>
                        <p style='color: #555; font-size: 16px;'>Your password has been updated.</p>
                        <p style='color: red; font-size: 14px;'>If you did not make this change, please reset your password immediately.</p>
                    </div>
                </div>"
        ]);

        // Mailgun API headers
        $authHeader = base64_encode("api:$mailgunApiKey");

        // HTTP context options
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Authorization: Basic $authHeader\r\n" .
                             "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $postData
            ]
        ]);

        // Send request using file_get_contents()
        $response = file_get_contents($mailgunUrl, false, $context);

        if ($response !== false) {
            $responseData = json_decode($response, true);

            if (isset($responseData['message']) && strpos($responseData['message'], "Queued") !== false) {
                echo json_encode(["status" => "success", "message" => "Password reset successfully. Email notification sent."]);
            } else {
                error_log("Mailgun Error: " . json_encode($responseData));
                echo json_encode(["status" => "warning", "message" => "Password reset successful, but email notification failed."]);
            }
        } else {
            error_log("Mailgun Request Failed: " . json_encode(error_get_last()));
            echo json_encode(["status" => "warning", "message" => "Password reset successful, but email sending failed."]);
        }

    } catch (Exception $e) {
        error_log("Exception: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "An unexpected error occurred. Please try again."]);
    }
}
?>
