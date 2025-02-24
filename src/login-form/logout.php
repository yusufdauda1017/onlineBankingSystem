<?php
session_start();

// Destroy the session
session_destroy();

// Remove the remember_me cookie
setcookie('remember_token', '', time() - 3600, "/", "", false, true); // Expire cookie immediately

// Optionally, remove token from the database
require_once __DIR__ . '/../../db/db_connect.php';
$update_token = $conn->prepare("UPDATE users SET remember_token = NULL WHERE remember_token IS NOT NULL");
$update_token->execute();
$update_token->close();
$conn->close();

echo json_encode(["success" => true, "message" => "Logged out successfully"]);
exit();
?>
