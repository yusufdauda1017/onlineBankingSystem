<?php
// Connect to database
require '../db/db_connect.php'; // Update with your database connection file

// Fetch total users
$totalUsersQuery = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = $totalUsersQuery->fetch_assoc()['total_users'];

// Fetch total transactions
$totalTransactionsQuery = $conn->query("SELECT SUM(amount) AS total_transactions FROM transactions");
$totalTransactions = $totalTransactionsQuery->fetch_assoc()['total_transactions'] ?? 0;

// Fetch new signups in the last 24 hours
$newSignupsQuery = $conn->query("SELECT COUNT(*) AS new_signups FROM users WHERE created_at >= NOW() - INTERVAL 1 DAY");
$newSignups = $newSignupsQuery->fetch_assoc()['new_signups'];

// Return JSON response
echo json_encode([
    "total_users" => $totalUsers,
    "total_transactions" => $totalTransactions,
    "new_signups" => $newSignups
]);

$conn->close();
?>
