<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../db/db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized: Session user_id is missing']);
    exit();
}

$userId = $_SESSION['user_id'];

if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$stmt = $conn->prepare("SELECT action, ip_address, user_agent, device_name, timestamp FROM activity_log WHERE user_id = ? ORDER BY timestamp DESC");

if (!$stmt) {
    echo json_encode(["error" => "SQL error: " . $conn->error]);
    exit();
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

$stmt->close();
echo json_encode($logs);
?>
