<?php
session_start();
require_once '../db/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT balance FROM accounts WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database error"]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($balance);
$stmt->fetch();
$stmt->close();
$conn->close();

if (isset($balance)) {
    echo json_encode(["success" => true, "balance" => $balance]);
} else {
    echo json_encode(["success" => false, "message" => "Balance not found"]);
}
?>
