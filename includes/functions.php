
<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();

// Get Mailgun credentials
$mailgunApiKey = $_ENV['MAILGUN_API_KEY'];
$mailgunDomain = $_ENV['MAILGUN_DOMAIN'];

function sendEmail($email, $otp) {
    global $mailgunApiKey, $mailgunDomain;

    // Mailgun API Endpoint
    $mailgunUrl = "https://api.mailgun.net/v3/$mailgunDomain/messages";

    // Prepare POST data
    $postData = http_build_query([
        'from'    => 'noreply@trustp.me',
        'to'      => $email,
        'subject' => 'Trustpoint OTP Code',
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
            return true;
        } else {
            error_log("Mailgun Error: " . json_encode($responseData));
            return false;
        }
    } else {
        error_log("Mailgun Request Failed: " . json_encode(error_get_last()));
        return false;
    }
}
function generateOTP($email, $phoneNumber, $conn) { 
    // Set the timezone to Nigerian Time (GMT+1)
    date_default_timezone_set('Africa/Lagos');
    
    $currentTime = time(); // Current Unix timestamp

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


    // ✅ Check OTP requests in the last 1 hour
    $stmt = $conn->prepare("SELECT COUNT(*) AS request_count, MIN(created_at) AS first_request_time 
                            FROM otps WHERE email = ? 
                            AND created_at > NOW() - INTERVAL 1 HOUR");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['request_count'] >= 3) {
        $remainingTime = (strtotime($row['first_request_time']) + 3600) - $currentTime;
        return [
            'success' => false,
            'message' => 'Too many OTP requests. Please try again after 1 hour.',
            'remaining_time' => max(0, $remainingTime)
        ];
    }

    // ✅ Check if the last OTP was sent less than 2 minutes ago
    $stmt = $conn->prepare("SELECT created_at FROM otps WHERE email = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingOtp = $result->fetch_assoc();

    if ($existingOtp) {
        $lastOtpTime = strtotime($existingOtp["created_at"]);
        if ($currentTime - $lastOtpTime < 120) {
            return [
                'success' => false,
                'message' => "Please wait " . (120 - ($currentTime - $lastOtpTime)) . " seconds before requesting a new OTP."
            ];
        }
    }

    // ✅ Generate and hash OTP
    $otp = rand(1000, 9999);
    $hashedOtp = password_hash($otp, PASSWORD_DEFAULT);
    $createdAt = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO otps (email, otp, created_at) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $hashedOtp, $createdAt);
    
    if (!$stmt->execute()) {
        return ['success' => false, 'message' => 'Failed to save OTP.', 'error' => $stmt->error];
    }

    // ✅ Send OTP via Email
    $emailSent = sendEmail($email, $otp);
    return [
        'success' => $emailSent,
        'message' => $emailSent ? 'OTP sent successfully.' : 'Failed to send OTP.',
        'otpSentTime' => $createdAt
    ];
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

