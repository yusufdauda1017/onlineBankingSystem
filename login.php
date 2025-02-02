<?php

// Start the session
session_start();

// Include database connection
require_once './db/db_connect.php';

// Set headers for JSON response
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the JSON payload
    $input = json_decode(file_get_contents('php://input'), true);

    // Collect user inputs
    $email = isset($input['email']) ? trim($input['email']) : '';
    $password = isset($input['password']) ? $input['password'] : '';

    // Validate inputs
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and Password are required.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    // Query to fetch user details
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare query.']);
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Regenerate session ID for security
            session_regenerate_id(true);

            // Store user information in the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role_id'];

            // Determine redirect URL based on role_id
            switch ($user['role_id']) {
                case '1': // Admin role
                    $redirectUrl = '../admin/index.php';
                    break;
                case '2': // Regular user role
                    $redirectUrl = '../user/index.php';
                    break;
                case '3': // Premium user role
                    $redirectUrl = '../userpremium/index.php';
                    break;
                default: // Default fallback for unrecognized roles
                    $redirectUrl = '../user/index.php';
                    break;
            }

            // Return success response with the redirect URL
            echo json_encode(['success' => true, 'redirect' => $redirectUrl]);
        } else {
            // Return failure response for incorrect password
            echo json_encode(['success' => false, 'message' => 'Invalid password.']);
        }
    } else {
        // Return failure response for invalid email
        echo json_encode(['success' => false, 'message' => 'No user found with this email.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
