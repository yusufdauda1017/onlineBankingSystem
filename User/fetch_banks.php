<?php
// Path to your local JSON file
$jsonFile = "../banks.json";

// Read and decode the JSON file
$jsonData = file_get_contents($jsonFile);
$result = json_decode($jsonData, true);

// Check if the file is properly formatted and contains the data
if ($result && isset($result['status']) && $result['status']) {
    echo json_encode(['success' => true, 'banks' => $result['data']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch banks']);
}
?>
