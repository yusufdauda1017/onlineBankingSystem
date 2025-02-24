<?php
session_start();
require '../db/db_connect.php'; // Include your database connection
require '../includes/log_activity.php';
$response = ["status" => "error", "message" => "Something went wrong."];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION["user_id"])) {
        $response["message"] = "Session expired. Please log in again.";
        echo json_encode($response);
        exit;
    }

    $user_id = $_SESSION["user_id"];
    $oldPassword = trim($_POST["oldPassword"]);
    $newPassword = trim($_POST["newPassword"]);

    // Password length check (additional validation)
    if (strlen($newPassword) < 4) {
        $response["message"] = "New password must be at least 6 characters long.";
        echo json_encode($response);
        exit;
    }

    // Fetch the user's current password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();

    if (!$hashedPassword || !password_verify($oldPassword, $hashedPassword)) {
        $response["message"] = "Old password is incorrect.";
    } else {
        // Hash new password
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update the password in the database
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $newHashedPassword, $user_id);
        
        if ($stmt->execute()) {
            $response = ["status" => "success", "message" => "Password updated successfully."];
        } else {
            $response["message"] = "Failed to update password.";
        }
        $stmt->close();
    }
}

echo json_encode($response);
?>
