<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

require_once '../db/db_connect.php';

header('Content-Type: application/json');

if (isset($_POST["account_number"])) {
    $accountNumber = trim($_POST["account_number"]);

    if (empty($accountNumber)) {
        echo json_encode(["success" => false, "message" => "Account number is required"]);
        exit;
    }

    $query = "SELECT users.fname, users.sname 
              FROM users 
              INNER JOIN accounts ON users.id = accounts.id
              WHERE accounts.account_number = ?";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Database error"]);
        exit;
    }

    $stmt->bind_param("s", $accountNumber);
    $stmt->execute();
    $stmt->bind_result($fname, $sname);
    $stmt->fetch();
    $stmt->close();

    if ($fname && $sname) {
        echo json_encode(["success" => true, "account_name" => $fname . " " . $sname]);
    } else {
        echo json_encode(["success" => false, "message" => "Account not found"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

$conn->close();
?>