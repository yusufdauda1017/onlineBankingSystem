<?php
session_start();
require_once '../db/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "uploads/";
    $fileName = "profile_" . $user_id . "_" . time() . "." . pathinfo($_FILES["profilePic"]["name"], PATHINFO_EXTENSION);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES["profilePic"]["tmp_name"], $filePath)) {
        $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $stmt->bind_param("si", $filePath, $user_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(["success" => true, "filepath" => $filePath]);
    } else {
        echo json_encode(["success" => false, "message" => "Error saving file."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Upload error."]);
}
?>
