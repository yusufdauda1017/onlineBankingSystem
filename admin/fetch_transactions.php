<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

require '../db/db_connect.php';

// Get filters
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Validate page number
if ($page < 1) {
    $page = 1;
}

// Base SQL query with placeholders
$sql = "SELECT  transaction_no, transaction_date, amount, status FROM transactions WHERE 1";

// Conditions array for filtering
$conditions = [];
$params = [];
$types = "";

if (!empty($dateFilter)) {
    $conditions[] = "transaction_date = ?";
    $params[] = $dateFilter;
    $types .= "s";
}


// Add date filter
if (!empty($dateFilter)) {
    $conditions[] = "transaction_date = ?";
    $params[] = $dateFilter;
    $types .= "s";
}

// Add search filter
if (!empty($searchQuery)) {
    $conditions[] = "transaction_no LIKE ?";
    $params[] = "%$searchQuery%";
    $types .= "s";
}

// Append conditions to SQL query
if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

// Count total transactions (for pagination)
$totalQuery = "SELECT COUNT(*) as total FROM transactions WHERE 1";
if (!empty($conditions)) {
    $totalQuery .= " AND " . implode(" AND ", $conditions);
}

$totalStmt = $conn->prepare($totalQuery);
if (!empty($params)) {
    $totalStmt->bind_param($types, ...$params);
}
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $limit);
$totalStmt->close();

// Apply ordering, limit, and offset
$sql .= " ORDER BY transaction_date DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

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
