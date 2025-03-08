<?php
require_once __DIR__ . '/../db/db_connect.php';

require_once __DIR__ . '/log_activity.php'; // Logging functionality
require_once __DIR__ . '/check_remember.php'; // Remember me feature

// Set headers for JSON response
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';
    $remember = isset($input['rememberMe']) && $input['rememberMe'] == true ? true : false;

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and Password are required.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    $query = "SELECT id, role_id, fname, sname, othername, email, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role_id'];
            $_SESSION['user_name'] = $user['fname'] . ' ' . $user['sname'];
            $_SESSION['user_name_full'] = $user['fname'] . ' ' . $user['sname']. ' ' . $user['othername'];
            $_SESSION['user'] = $user['fname'];
            $_SESSION['email'] = $user['email'];

            // Fetch account details
            $user_id = $user['id'];
            $query = $conn->prepare("SELECT account_number, balance FROM accounts WHERE id = ?");
            $query->bind_param("i", $user_id);
            $query->execute();
            $query->bind_result($account_number, $balance);
            $query->fetch();
            $query->close();

            $_SESSION['account_number'] = $account_number;
            $_SESSION['balance'] = $balance;

            // Implement Remember Me (using token)
            if ($remember) {
                $remember_token = bin2hex(random_bytes(32));
                $expiry = time() + (30 * 24 * 60 * 60); // 30 days
                setcookie('remember_token', $remember_token, $expiry, "/", "", false, true);

                $update_token = $conn->prepare("UPDATE users SET remember_token = ?, last_login = NOW() WHERE id = ?");
                $update_token->bind_param("si", $remember_token, $user_id);
                $update_token->execute();
                $update_token->close();
            }

            logActivity($user_id, "User logged in");

            // Store user information in the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role_id'];

            // Determine redirect URL based on role_id
            switch ($user['role_id']) {
                case '1': // Admin role
                    $redirectUrl = './admin/index.php';
                    break;
                case '2': // Regular user role
                    $redirectUrl = '/User/index.php';
                    break;
                case '3': // Premium user role
                    $redirectUrl = '/userpremium/index.php';
                    break;
                default: // Default fallback for unrecognized roles
                    $redirectUrl = '/User/index.php';
                    break;
            }

            // Return success response with the redirect URL
            echo json_encode(['success' => true, 'redirect' => $redirectUrl]);
        } else {
            logActivity($user['id'], "Failed login attempt");
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }
    $stmt->close();
}
$conn->close();
?>
