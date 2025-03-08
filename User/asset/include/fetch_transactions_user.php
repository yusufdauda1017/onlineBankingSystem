<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/db/db_connect.php");

// Ensure the user is logged in
if (!isset($_SESSION['account_number'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$account_number = $_SESSION['account_number'];

// Get filters
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$viewAll = isset($_GET['view']) && $_GET['view'] === "all"; // Check if viewing all transactions
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = $viewAll ? null : 3; // Show all transactions if 'view=all', else limit to 3
$offset = ($page - 1) * ($limit ?? 1); // Only needed if limit is applied

if ($page < 1) {
    $page = 1;
}

// Base SQL query
$sql = "SELECT transaction_no, transaction_type, transaction_date, amount, status
        FROM transactions WHERE account_number = ?";

$conditions = [];
$params = [$account_number];
$types = "s"; // 's' because account_number is a string

// Apply filters
if (!empty($dateFilter)) {
    $conditions[] = "transaction_date = ?";
    $params[] = $dateFilter;
    $types .= "s";
}

if (!empty($searchQuery)) {
    $conditions[] = "transaction_no LIKE ?";
    $params[] = "%$searchQuery%";
    $types .= "s";
}

if (!empty($statusFilter)) {
    $conditions[] = "status = ?";
    $params[] = $statusFilter;
    $types .= "s";
}

// Append conditions to SQL query
if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

// Count total transactions for pagination (only if not viewing all)
$totalPages = 1;
if (!$viewAll) {
    $totalQuery = "SELECT COUNT(*) as total FROM transactions WHERE account_number = ?";
    if (!empty($conditions)) {
        $totalQuery .= " AND " . implode(" AND ", $conditions);
    }

    $totalStmt = $conn->prepare($totalQuery);
    $totalStmt->bind_param($types, ...$params);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
    $totalRow = $totalResult->fetch_assoc();
    $totalPages = ceil($totalRow['total'] / $limit);
    $totalStmt->close();
}

// Fetch transactions with pagination or all records
$sql .= " ORDER BY transaction_date DESC";
if (!$viewAll) {
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii"; // 'ii' for limit and offset
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

$stmt->close();
$conn->close();

// Return JSON response
echo json_encode(["transactions" => $transactions, "totalPages" => $totalPages]);
?>
