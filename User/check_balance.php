<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

require_once '../db/db_connect.php';

header('Content-Type: application/json');

if (isset($_POST["amount"])) {
    $amount = floatval($_POST["amount"]);
    $user_id = $_SESSION['user_id'];

    if ($amount <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid amount"]);
        exit;
    }

    // Query to get the balance of the logged-in user
    $query = "SELECT balance FROM accounts WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Database error"]);
        exit;
    }

    $stmt->bind_param("i", $user_id); // Bind user_id instead of amount
    $stmt->execute();
    $stmt->bind_result($balance);
    $stmt->fetch();
    $stmt->close();

    if (!isset($balance)) {
        echo json_encode(["success" => false, "message" => "Balance not found"]);
        exit;
    }

    if ($balance >= $amount) {
        echo json_encode(["success" => true, "message" => "Transaction Successful"]);
    } else {
        echo json_encode(["success" => false, "message" => "Insufficient balance"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Amount is required"]);
}

$conn->close();
?>
