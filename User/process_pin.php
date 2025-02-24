<?php
session_start();
include "../db/db_connect.php"; // Your database connection file
require '../includes/log_activity.php';
header("Content-Type: application/json");

// Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
    exit();
}

// Get action type
$action = $_POST["action"] ?? "";

// Function to hash PIN securely
function hashPin($pin) {
    return password_hash($pin, PASSWORD_DEFAULT);
}

// Function to verify hashed PIN
function verifyPin($pin, $hashedPin) {
    return password_verify($pin, $hashedPin);
}

// Get user ID from session (Assuming user is logged in)
$user_id = $_SESSION["user_id"] ?? null;
if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}

if ($action === "create_pin") {
    $pin = $_POST["pin"] ?? "";
    if (strlen($pin) !== 4 || !ctype_digit($pin)) {
        echo json_encode(["status" => "error", "message" => "Invalid PIN format."]);
        exit();
    }

    // Check if user already has a PIN
    $stmt = $conn->prepare("SELECT transaction_pin FROM accounts WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && !empty($user["transaction_pin"])) {
        echo json_encode(["status" => "error", "message" => "Transaction PIN already exists."]);
        exit();
    }

    // Store hashed PIN
    $hashedPin = hashPin($pin);
    $stmt = $conn->prepare("UPDATE accounts SET transaction_pin = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPin, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Transaction PIN created successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to create PIN."]);
    }
    exit();
}

if ($action === "change_pin") {
    $oldPin = $_POST["oldPin"] ?? "";
    $newPin = $_POST["newPin"] ?? "";

    if (strlen($newPin) !== 4 || !ctype_digit($newPin)) {
        echo json_encode(["status" => "error", "message" => "Invalid PIN format."]);
        exit();
    }

    // Fetch stored PIN
    $stmt = $conn->prepare("SELECT transaction_pin FROM accounts WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user || empty($user["transaction_pin"])) {
        echo json_encode(["status" => "error", "message" => "No PIN found."]);
        exit();
    }

    // Verify old PIN
    if (!verifyPin($oldPin, $user["transaction_pin"])) {
        echo json_encode(["status" => "error", "message" => "Old PIN is incorrect."]);
        exit();
    }

    // Update new hashed PIN
    $hashedNewPin = hashPin($newPin);
    $stmt = $conn->prepare("UPDATE accounts SET transaction_pin = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedNewPin, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Transaction PIN changed successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to change PIN."]);
    }
    exit();
}

echo json_encode(["status" => "error", "message" => "Invalid action."]);
