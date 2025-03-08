<?php
require '../../db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$userId = $_SESSION['user_id'];
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=activity_log.csv");

$output = fopen("php://output", "w");
fputcsv($output, ["Date", "Time", "Activity", "Device"]); // Add Device column

$stmt = $conn->prepare("SELECT action, timestamp, device_name FROM activity_log WHERE user_id = ? ORDER BY timestamp DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [$row['timestamp'], $row['action'], $row['device_name']]); // Add device name
}

fclose($output);
?>
