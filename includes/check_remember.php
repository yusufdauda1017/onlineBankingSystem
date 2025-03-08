<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/db/db_connect.php';


if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $remember_token = $_COOKIE['remember_token'];

    // Fetch user details based on the remember token
    $query = "SELECT id, fname, sname, othername, role_id, email FROM users WHERE remember_token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $remember_token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Restore all session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['fname'] . ' ' . $user['sname'];
        $_SESSION['user_name_full'] = $user['fname'] . ' ' . $user['sname']. ' ' . $user['othername'];
        $_SESSION['user'] = $user['fname'];
        $_SESSION['user_role'] = $user['role_id'];
        $_SESSION['email'] = $user['email'];

        // Fetch account details (fixing column reference issue)
        $user_id = $user['id'];
        $query = $conn->prepare("SELECT account_number, balance FROM accounts WHERE id = ?");
        $query->bind_param("i", $user_id);
        $query->execute();
        $result = $query->get_result();

        if ($account = $result->fetch_assoc()) {
            $_SESSION['account_number'] = $account['account_number'] ?? null; // Ensure account_number exists
            $_SESSION['balance'] = $account['balance'];
        } else {
            $_SESSION['account_number'] = "No Account"; // Default if account is missing
        }

        $query->close();


        exit();

    } else {
        // If token is invalid, remove it and redirect to login
        setcookie("remember_token", "", time() - 3600, "/");
        header("Location: ../login.php");
        exit();
    }
    $stmt->close();
}
?>
