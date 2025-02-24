
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Mailgun\Mailgun;
use Dotenv\Dotenv;
// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Get Mailgun credentials
$mailgunApiKey = $_ENV['MAILGUN_API_KEY'];
$mailgunDomain = $_ENV['MAILGUN_DOMAIN'];

function sendEmail($email, $otp) {
    global $mailgunApiKey, $mailgunDomain;

    // Initialize Mailgun
    $mg = Mailgun::create($mailgunApiKey);

    try {
        $mg->messages()->send($mailgunDomain, [
            'from'    => 'noreply@trustp.me',
            'to'      => $email,
            'subject' => ' Trustpoint OTP Code',
            'html'    => "
                <div style='background-color: #f4f4f4; padding: 20px; font-family: Arial, sans-serif; text-align: center;'>
                    <div style='background-color: #ffffff; border-radius: 8px; padding: 20px; max-width: 500px; margin: auto;'>
                        <h1 style='color: #333;'>Your OTP Code</h1>
                        <p style='color: #555; font-size: 16px;'>Your OTP is: <strong style='color: #007BFF;'>$otp</strong></p>
                        <p style='color: #555; font-size: 14px;'>It will expire in <strong>2 minutes</strong>.</p>
                        <p style='font-size: 12px; color: #999;'>Please do not share this code with anyone.</p>
                    </div>
                </div>"
        ]);

        return true;
    } catch (\Mailgun\Exception\HttpClientException $e) {
        error_log("Mailgun Error: " . $e->getMessage());
        return false;
    } catch (\Exception $e) {
        error_log("General Error: " . $e->getMessage());
        return false;
    }
}

function generateOTP($email, $phoneNumber, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM users WHERE email = ? OR phone_number = ?");
    if (!$stmt) {
        return [
            'success' => false,
            'message' => 'Database error: Failed to prepare statement.'
        ];
    }

    $stmt->bind_param("ss", $email, $phoneNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row['count'] > 0) {
        return [
            'success' => false,
            'message' => 'This email or phone number is already registered.'
        ];
    }

    $otp = rand(1000, 9999);
    $hashedOtp = password_hash($otp, PASSWORD_DEFAULT);
    $createdAt = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO otps (email, otp, created_at) VALUES (?, ?, ?)");
    if (!$stmt) {
        return [
            'success' => false,
            'message' => 'Database error: Failed to prepare insert statement.'
        ];
    }

    $stmt->bind_param("sss", $email, $hashedOtp, $createdAt);

    if ($stmt->execute()) {
        $stmt->close();
        $emailSent = sendEmail($email, $otp);

        return [
            'success' => $emailSent,
            'message' => $emailSent ? 'OTP generated and sent successfully.' : 'Failed to send OTP.',
            'email_status' => $emailSent ? 'Sent' : 'Failed'
        ];
    } else {
        $stmt->close();
        return [
            'success' => false,
            'message' => 'Failed to generate OTP. Please try again.'
        ];
    }
}

function verifyOTP($email, $inputOtp, $conn, $formData = null) {
    $otpExpiryTime = 2 * 60;

    $stmt = $conn->prepare("SELECT otp, created_at FROM otps WHERE email = ? ORDER BY created_at DESC LIMIT 1");
    if (!$stmt) {
        return [
            'success' => false,
            'message' => 'Database error: Failed to prepare statement.'
        ];
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row) {
        $storedHashedOtp = $row['otp'];
        $createdAt = strtotime($row['created_at']);
        $currentTime = time();

        if (($currentTime - $createdAt) > $otpExpiryTime) {
            return [
                'success' => false,
                'message' => 'OTP has expired. Please request a new one.'
            ];
        }

        if (password_verify($inputOtp, $storedHashedOtp)) {
            // Store form data in session after successful OTP verification
            if ($formData) {
                $_SESSION['fName'] = $formData['firstName'];
                $_SESSION['sName'] = $formData['surname'];
                $_SESSION['phoneNumber'] = $formData['phoneNumber'];
                $_SESSION['email'] = $formData['email'];
                $_SESSION['dob'] = $formData['dob'];
                $_SESSION['gender'] = $formData['gender'];

                // Log session data for debugging
                error_log("Session data set: " . json_encode([
                    'fName' => $_SESSION['fName'],
                    'sName' => $_SESSION['sName'],
                    'phoneNumber' => $_SESSION['phoneNumber'],
                    'email' => $_SESSION['email'],
                    'dob' => $_SESSION['dob'],
                    'gender' => $_SESSION['gender'],
                ]));
            }

            $deleteStmt = $conn->prepare("DELETE FROM otps WHERE email = ?");
            if ($deleteStmt) {
                $deleteStmt->bind_param("s", $email);
                $deleteStmt->execute();
                $deleteStmt->close();
            }

            return [
                'success' => true,
                'message' => 'OTP verified successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Invalid OTP. Please try again.'
            ];
        }
    } else {
        return [
            'success' => false,
            'message' => 'No OTP found for this email. Please request one first.'
        ];
    }
}

