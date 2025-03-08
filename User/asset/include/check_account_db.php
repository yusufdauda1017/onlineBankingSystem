<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/db/db_connect.php");

header('Content-Type: application/json');

// Ensure the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['accountNumber']) || empty($data['accountNumber'])) {
    echo json_encode(["success" => false, "message" => "Account number is required"]);
    exit;
}

$accountNumber = trim($data['accountNumber']);
$response = ["success" => false, "exists" => false];

// Prepare SQL query
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

if ($stmt->fetch()) {
    $response["success"] = true;
    $response["exists"] = true;
    $response["account_name"] = $fname . " " . $sname;
} else {
    $response["message"] = "Account not found";
}

$stmt->close();
$conn->close();

// Return JSON response
echo json_encode($response);
?>
