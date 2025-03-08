

<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/db/db_connect.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/log_activity.php");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['recipient_account']) || !isset($data['amount']) || !isset($data['pin'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$recipient_account = trim($data['recipient_account']);
$amount = floatval($data['amount']);
$entered_pin = trim($data['pin']);

$charge_percentage = 0.01;
$min_charge = 0.00;
$charge = max($amount * $charge_percentage, $min_charge);
$total_deduction = $amount + $charge;

if (empty($recipient_account) || $total_deduction <= 0 || empty($entered_pin)) {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit;
}

$sql = "SELECT account_id, account_number, balance, currency, transaction_pin FROM accounts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$sender = $result->fetch_assoc();

if (!$sender || !password_verify($entered_pin, $sender['transaction_pin'])) {
    echo json_encode(["success" => false, "message" => "Invalid transaction PIN or account not found."]);
    exit;
}

if ($sender['balance'] < $total_deduction) {
    echo json_encode(["success" => false, "message" => "Insufficient balance."]);
    exit;
}


$sql = "SELECT * FROM accounts WHERE account_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $recipient_account); // Account numbers are usually strings
$stmt->execute();
$result = $stmt->get_result();
$recipient = $result->fetch_assoc();

if (!$recipient || $sender['currency'] !== $recipient['currency']) {
    echo json_encode(["success" => false, "message" => "Recipient not found or currency mismatch."]);
    exit;
}

$conn->begin_transaction();

$sql = "UPDATE accounts SET balance = balance - ?, updated_at = NOW() WHERE account_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("di", $total_deduction, $sender['account_id']);
$stmt->execute();

$sql = "UPDATE accounts SET balance = balance + ?, updated_at = NOW() WHERE account_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("di", $amount, $recipient['account_id']);
$stmt->execute();

$account_number = $sender['account_number'];
$transaction_type = "Transfer";
$transaction_amount = $amount;
$transaction_date = date("Y-m-d H:i:s");
$transaction_no = uniqid("TXN_");
$status = "Successful";
$narration = "Funds transfer";
$sender_account = $sender['account_number'];
$receiver_account = $recipient['account_number'];
$balance_after = $sender['balance'] - $total_deduction;
$reference = uniqid("REF_");

$sql = "INSERT INTO transactions (account_number, transaction_type, amount, transaction_date, transaction_no, status, narration, sender_account, receiver_account, balance_after, reference) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssdssssssds",
    $account_number, $transaction_type, $transaction_amount, $transaction_date,
    $transaction_no, $status, $narration, $sender_account, 
    $receiver_account, $balance_after, $reference
);
$stmt->execute();
$transaction_id = $transaction_no;
$account_id = $sender['account_id'];
$charge_type = "transfer_fee";
$charge_amount = $charge;
$currency = $sender['currency'];

$sql = "INSERT INTO charges (transaction_id, account_id, charge_type, amount, currency) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sisds", $transaction_id, $account_id, $charge_type, $charge_amount, $currency);
$stmt->execute();

if ($conn->commit()) {
    logActivity($user_id, "User transfer");

    echo json_encode([
        "success" => true,
        "message" => "Transaction successful.",
        "transaction_id" => $transaction_no,
        "charge" => $charge,
        "total_deducted" => $total_deduction,
        "status" => "Successful"
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Transaction failed."]);
}

exit;

?>