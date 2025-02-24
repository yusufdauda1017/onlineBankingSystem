<?php
require '../db/db_connect.php'; // Database connection
require_once __DIR__ . '/../vendor/autoload.php';

use Mailgun\Mailgun;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Get Mailgun credentials
$mailgunApiKey = $_ENV['MAILGUN_API_KEY'];
$mailgunDomain = $_ENV['MAILGUN_DOMAIN'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {
    $email = trim($_POST["email"]);
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    // ✅ RATE LIMITING: Prevent excessive requests
    $stmt = $conn->prepare("SELECT COUNT(*) AS attempts FROM password_reset_attempts WHERE ip_address = ? AND timestamp > NOW() - INTERVAL 10 MINUTE");
    $stmt->bind_param("s", $ipAddress);
    $stmt->execute();
    $result = $stmt->get_result();
    $attempts = $result->fetch_assoc()['attempts'];
    $stmt->close();

    if ($attempts >= 3) { // Allow only 3 attempts in 10 minutes
        echo json_encode(["status" => "error", "message" => "Too many requests. Try again later."]);
        exit();
    }

    // Log this request (prevent spam attacks)
    $stmt = $conn->prepare("INSERT INTO password_reset_attempts (ip_address, timestamp) VALUES (?, NOW())");
    $stmt->bind_param("s", $ipAddress);
    $stmt->execute();
    $stmt->close();

    // ✅ CHECK IF EMAIL EXISTS
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // ✅ SECURITY: Prevent email enumeration
    $message = "If your email exists, a reset link will be sent.";

    if ($user) {
        // Generate a secure token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store token & expiry in DB
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();
        $stmt->close();

        // Create reset link
        $resetLink = "http://localhost/onlineBankingSystem/forgotPass/reset_pass.php?token=$token";

        // Initialize Mailgun
        $mg = Mailgun::create($mailgunApiKey);

        try {
            $mg->messages()->send($mailgunDomain, [
                'from'    => 'Trustpoint@trustp.me',
                'to'      => $email,
                'subject' => 'Trustpoint Reset Password',
                'html'    => "
                    <div style='background-color: #f4f4f4; padding: 20px; font-family: Arial, sans-serif; text-align: center;'>
                        <div style='background-color: #ffffff; border-radius: 8px; padding: 20px; max-width: 500px; margin: auto;'>
                            <h1 style='color: #333;'>Reset Your Password</h1>
                            <p style='color: #555; font-size: 16px;'>Click the link below to reset your password:</p>
                            <p><a href='$resetLink' style='color: #007BFF; font-size: 18px;'>Reset Password</a></p>
                            <p style='color: #555; font-size: 14px;'>This link is valid for <strong>1 hour</strong>.</p>
                            <p style='font-size: 12px; color: #999;'>Please do not share this link with anyone.</p>
                        </div>
                    </div>"
            ]);
        } catch (\Exception $e) {
            error_log("Mailgun Error: " . $e->getMessage());
        }
    }

    echo json_encode(["status" => "success", "message" => $message]);
}
?>
