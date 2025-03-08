<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json'); // 👈 Ensure JSON output

require_once($_SERVER['DOCUMENT_ROOT'] . "/db/db_connect.php");

$response = ["success" => false, "message" => "Unknown error."];

if (!isset($_SESSION['user_id'])) {
    $response["message"] = "User not logged in.";
    echo json_encode($response);
    exit();
}

if (!isset($_FILES['profilePic'])) {
    $response["message"] = "No file uploaded.";
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user_id'];
$file = $_FILES["profilePic"];

if ($file["error"] !== UPLOAD_ERR_OK) {
    $response["message"] = "Upload error: " . $file["error"];
    echo json_encode($response);
    exit();
}

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/"; // Full path
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true); // Ensure directory exists

$fileName = "profile_" . $user_id . "_" . time() . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
$filePath = $uploadDir . $fileName;
$dbFilePath = "/uploads/" . $fileName; // Path for DB (relative)

if (move_uploaded_file($file["tmp_name"], $filePath)) {
    $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $dbFilePath, $user_id);
        $stmt->execute();
        $stmt->close();
        $response = ["success" => true, "filepath" => $dbFilePath];
    } else {
        $response["message"] = "Database error: " . $conn->error;
    }
} else {
    $response["message"] = "Error moving uploaded file.";
}

echo json_encode($response);
?>
