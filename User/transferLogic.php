<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

require_once '../db/db_connect.php';
require '../includes/log_activity.php';
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

// Define transaction charge (e.g., 1% fee, minimum 10 currency units)
$charge_percentage = 0.01; // 1% charge
$min_charge = 0.00;
$charge = max($amount * $charge_percentage, $min_charge);

$total_deduction = $amount + $charge;

if (empty($recipient_account) || $total_deduction <= 0 || empty($entered_pin)) {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit;
}

// Check if the user is locked out
$max_attempts = 5;  // Maximum failed attempts
$lockout_time = 300; // Lockout duration in seconds (5 minutes)

if (isset($_SESSION['lockout_time']) && time() < $_SESSION['lockout_time']) {
    echo json_encode(["success" => false, "message" => "Too many failed attempts. Try again later."]);
    exit;
}

// Initialize failed attempts if not set
if (!isset($_SESSION['failed_attempts'])) {
    $_SESSION['failed_attempts'] = 0;
}

// Fetch sender details
$sql = "SELECT account_id, account_number, balance, currency, transaction_pin FROM accounts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$sender = $result->fetch_assoc();

if (!$sender) {
    echo json_encode(["success" => false, "message" => "Sender account not found."]);
    exit;
}

// Verify PIN
if (!password_verify($entered_pin, $sender['transaction_pin'])) {
    $_SESSION['failed_attempts']++;

    if ($_SESSION['failed_attempts'] >= $max_attempts) {
        $_SESSION['lockout_time'] = time() + $lockout_time; // Set lockout time
        echo json_encode(["success" => false, "message" => "Too many failed attempts. Try again in 5 minutes."]);
    } else {
        $attempts_left = $max_attempts - $_SESSION['failed_attempts'];
        echo json_encode(["success" => false, "message" => "Invalid transaction PIN. Attempts left: $attempts_left"]);
    }
    exit;
}

// Reset failed attempts on success
$_SESSION['failed_attempts'] = 0;
unset($_SESSION['lockout_time']);

// Check balance
if ($sender['balance'] < $total_deduction) {
    echo json_encode(["success" => false, "message" => "Insufficient balance."]);
    exit;
}

// Fetch recipient details
$sql = "SELECT account_id, account_number, balance, currency FROM accounts WHERE account_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $recipient_account);
$stmt->execute();
$result = $stmt->get_result();
$recipient = $result->fetch_assoc();

if (!$recipient) {
    echo json_encode(["success" => false, "message" => "Recipient account not found."]);
    exit;
}

if ($sender['currency'] !== $recipient['currency']) {
    echo json_encode(["success" => false, "message" => "Currency mismatch. Cannot transfer."]);
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    // 🔒 Lock sender row
    $sql = "SELECT account_id, account_number, balance, currency, transaction_pin FROM accounts WHERE id = ? FOR UPDATE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sender = $result->fetch_assoc();

    if (!$sender || $sender['balance'] < $total_deduction) {
        throw new Exception("Insufficient balance or sender not found.");
    }

    // 🔒 Lock recipient row
    $sql = "SELECT account_id, account_number, balance, currency FROM accounts WHERE account_number = ? FOR UPDATE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $recipient_account);
    $stmt->execute();
    $result = $stmt->get_result();
    $recipient = $result->fetch_assoc();

    if (!$recipient) {
        throw new Exception("Recipient account not found.");
    }

    // Deduct from sender
    $sql = "UPDATE accounts SET balance = balance - ?, updated_at = NOW() WHERE account_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $total_deduction, $sender['account_id']);
    $stmt->execute();

    // Credit recipient
    $sql = "UPDATE accounts SET balance = balance + ?, updated_at = NOW() WHERE account_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $amount, $recipient['account_id']);
    $stmt->execute();

    // Insert transaction record
    $sql = "INSERT INTO transactions (account_number, transaction_type, amount, transaction_date, transaction_no, status, narration, sender_account, receiver_account, balance_after, reference)
            VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssssssds",
        $sender['account_number'], "Transfer", $amount, $transaction_no, "Successful",
        "Funds transfer", $sender['account_number'], $recipient['account_number'],
        $sender['balance'] - $total_deduction, $reference
    );
    $stmt->execute();

    // Insert charge record
    $charge_sql = "INSERT INTO charges (transaction_id, account_id, charge_type, amount, currency)
                   VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($charge_sql);
    $stmt->bind_param("sisds", $transaction_no, $sender['account_id'], "transfer_fee", $charge, $sender['currency']);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    logActivity($user_id, "User transfer");

    echo json_encode([
        'success' => true,
        'message' => "Transaction successful.",
        'transaction_id' => $transaction_no,
        'charge' => $charge,
        'total_deducted' => $total_deduction,
        'status' => "Successful"
    ]);
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    error_log("Transaction failed: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Transaction failed: " . $e->getMessage()]);
}

?>
