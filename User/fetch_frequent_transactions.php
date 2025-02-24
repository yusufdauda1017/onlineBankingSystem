<?php
session_start();
require '../db/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['account_number'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$senderAccount = $_POST['sender_account'] ?? '';

if (empty($senderAccount)) {
    echo json_encode(["error" => "No account number provided"]);
    exit;
}

// Debugging: Check if sender's account is received
error_log("Sender Account: " . $senderAccount);

$sql = "SELECT u.fname, u.sname, t.receiver_account, t.bank_name, COUNT(t.transaction_id) as transfer_count
        FROM transactions t
        JOIN accounts a ON t.receiver_account = a.account_number
        JOIN users u ON a.id = u.id
        WHERE t.sender_account = ?
        GROUP BY t.receiver_account
        HAVING transfer_count >= 1
        ORDER BY transfer_count DESC
        LIMIT 5";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $senderAccount);
$stmt->execute();
$result = $stmt->get_result();

$recipients = [];
while ($row = $result->fetch_assoc()) {
    $recipients[] = [
        'name' => $row['fname'] . ' ' . $row['sname'],
        'receiver_account' => $row['receiver_account'],
        'bank_name' => $row['bank_name']
    ];
}

echo json_encode($recipients);
?>
