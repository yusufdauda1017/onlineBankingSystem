<?php
header('Content-Type: application/json'); // Set header for JSON response

// Path to your local JSON file
$banksFile = $_SERVER['DOCUMENT_ROOT'] . "/banks.json";

// Check if file exists
if (!file_exists($banksFile)) {
    echo json_encode(['success' => false, 'message' => 'banks.json not found!']);
    exit;
}

// Read and decode the JSON file
$banksData = file_get_contents($banksFile);
$banks = json_decode($banksData, true); // Convert JSON to PHP array

// Validate JSON format
if ($banks === null) {
    echo json_encode(['success' => false, 'message' => 'Failed to decode JSON']);
    exit;
}

// Check if the JSON follows expected structure
if (isset($banks['status']) && $banks['status'] && isset($banks['data'])) {
    echo json_encode(['success' => true, 'banks' => $banks['data']]);
} else {
    echo json_encode(['success' => true, 'banks' => $banks]); // Adjust if no 'status' key
}
?>
