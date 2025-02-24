<?php
require '../db/db_connect.php';
require_once __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Mailgun\Mailgun;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Mailgun credentials
$mailgunApiKey = $_ENV['MAILGUN_API_KEY'];
$mailgunDomain = $_ENV['MAILGUN_DOMAIN'];
$secretKey = $_ENV['SECRET_KEY']; // Security key for HMAC

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["token"]) && isset($_POST["password"])) {
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

        // Send Email Notification
        $mg = Mailgun::create($mailgunApiKey);
        try {
            $mg->messages()->send($mailgunDomain, [
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
        } catch (\Exception $e) {
            error_log("Mailgun Error: " . $e->getMessage());
            echo json_encode(["status" => "warning", "message" => "Password changed, but email notification failed."]);
            exit();
        }

        echo json_encode(["status" => "success", "message" => "Password reset successfully!"]);
    } catch (Exception $e) {
        error_log("Error in password reset: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "An error occurred. Try again later."]);
    }
}
?>
