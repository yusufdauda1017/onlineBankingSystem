<?php

require_once '../../db/db_connect.php';
require '../../includes/functions.php';
require '../../includes/log_activity.php';
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

session_start(); // Start the session once at the beginning

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Parse incoming JSON data or fallback to $_POST
    $data = [];
    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        $jsonInput = file_get_contents('php://input');
        $data = json_decode($jsonInput, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'message' => 'Invalid JSON format.']);
            exit;
        }
    } else {
        $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING) ?? [];
    }

    // Extract type and email from data
    $type = $data['type'] ?? null;
    $email = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : null;
    $phoneNumber = $data['phoneNumber'] ?? null;

    if (!$type) {
        echo json_encode(['success' => false, 'message' => 'Request type is missing.']);
        exit;
    }

    // Handle request types
    switch ($type) {
        case 'generate_otp':
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
                exit;
            }
            if (empty($phoneNumber)) {
                echo json_encode(['success' => false, 'message' => 'Phone number is required.']);
                exit;
            }

            $response = generateOTP($email, $phoneNumber, $conn);
            echo json_encode($response);
            break;

        case 'verify_otp':
            $inputOtp = htmlspecialchars($data['otp'] ?? null);
            if ($email && $inputOtp) {
                $response = verifyOTP($email, $inputOtp, $conn, $data['formData'] ?? null);
                echo json_encode($response);
            } else {
                echo json_encode(['success' => false, 'message' => 'Email or OTP is missing.']);
            }
            break;

        case 'resend_otp':
                $email = htmlspecialchars($data['email'] ?? null);
                if ($email) {
                    $response = generateOTP($email, null, $conn); // Assuming phoneNumber is not required for email OTP
                    echo json_encode($response);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Email is missing.']);
                }
                break;

                case 'create_pass':
                    // Retrieve user details from session
                    $email = $_SESSION['email'] ?? null;
                    $fName = $_SESSION['fName'] ?? null;
                    $sName = $_SESSION['sName'] ?? null;
                    $othername = $_SESSION['othername'] ?? null;
                    $phoneNumber = $_SESSION['phoneNumber'] ?? null;
                    $dob = $_SESSION['dob'] ?? null;
                    $gender = $_SESSION['gender'] ?? null;
                    $inputPass = htmlspecialchars($data['password'] ?? null);

                    // Validate required fields
                    if (!$email || !$inputPass || !$fName || !$sName || !$phoneNumber || !$dob || !$gender) {
                        error_log("Missing fields: " . json_encode([
                            'email' => $email,
                            'fName' => $fName,
                            'sName' => $sName,
                            'phoneNumber' => $phoneNumber,
                            'dob' => $dob,
                            'gender' => $gender,
                        ]));
                        echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
                        exit;
                    }

                    // Hash password securely
                    $hashedPassword = password_hash($inputPass, PASSWORD_DEFAULT);

                    // Insert user into database
                    $stmt = $conn->prepare("
                        INSERT INTO users (fname, sname, othername, phone_number, email, dob, gender, password, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    if (!$stmt) {
                        echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare statement.', 'error' => $conn->error]);
                        exit;
                    }

                    $stmt->bind_param("ssssssss", $fName, $sName, $othername, $phoneNumber, $email, $dob, $gender, $hashedPassword);

                    if ($stmt->execute()) {
                        session_regenerate_id(true); // Regenerate session ID for security
                        $id = $stmt->insert_id;
                        $_SESSION['user_id'] = $id;
                        $_SESSION['user_name'] = $fName . ' ' . $sName;
                        $_SESSION['user_name_full'] = $fName . ' ' . $sName . ' ' . $othername;
                        $_SESSION['user'] = $fName;
                        $_SESSION['email'] = $email;
                        $_SESSION['phone_number'] = $phoneNumber;

                        // Log activity
                        logActivity($id, "User Signup");

                        // Generate account number
                        $phoneNumberDigits = preg_replace('/\D/', '', $phoneNumber);
                        if (strlen($phoneNumberDigits) > 10) {
                            $account_number = substr($phoneNumberDigits, -10);
                        } elseif (strlen($phoneNumberDigits) < 10) {
                            $account_number = str_pad($phoneNumberDigits, 10, '0', STR_PAD_LEFT);
                        } else {
                            $account_number = $phoneNumberDigits;
                        }

                        // Insert account into database
                        $stmt2 = $conn->prepare("INSERT INTO accounts (id, account_number, balance) VALUES (?, ?, ?)");
                        if ($stmt2) {
                            $balance = 0.00;
                            $stmt2->bind_param("isd", $id, $account_number, $balance);
                            if ($stmt2->execute()) {
                                $_SESSION['account_number'] = $account_number;
                                $_SESSION['balance'] = $balance;
                                echo json_encode([
                                    'success' => true,
                                    'message' => 'Signup successful!',
                                    'user_id' => $id,
                                    'account_number' => $account_number
                                ]);
                            } else {
                                echo json_encode(['success' => false, 'message' => 'Error creating account', 'error' => $stmt2->error]);
                            }
                            $stmt2->close();
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare account statement.', 'error' => $conn->error]);
                        }
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Database insert failed.',
                            'error' => $stmt->error
                        ]);
                    }

                    $stmt->close();
                    break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid request type.']);
            break;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}