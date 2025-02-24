<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method. Use POST.']);
    exit;
}

// Read raw input
$rawData = file_get_contents('php://input');
error_log("Raw Input Data: " . $rawData);

$input = json_decode($rawData, true);

if (!is_array($input)) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input.']);
    exit;
}

// Validate input
if (!isset($input['accountNumber']) || !isset($input['bankCode'])) {
    echo json_encode(['success' => false, 'message' => 'Account number and bank code are required.']);
    exit;
}

$accountNumber = trim($input['accountNumber']);
$bankCode = trim($input['bankCode']);

if (empty($accountNumber) || empty($bankCode)) {
    echo json_encode(['success' => false, 'message' => 'Account number and bank code cannot be empty.']);
    exit;
}

// Use an environment variable for security
$paystackSecretKey = getenv('PAYSTACK_SECRET_KEY') ?: 'sk_test_87f15c0781ff16439ee29e544250b46109aa478e';
error_log("Using Paystack API Key: " . substr($paystackSecretKey, 0, 8) . "********");

// Paystack API URL
$url = "https://api.paystack.co/bank/resolve?account_number=$accountNumber&bank_code=$bankCode";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $paystackSecretKey",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Handle cURL errors
if ($curlError) {
    echo json_encode(['success' => false, 'message' => 'CURL Error: ' . $curlError]);
    exit;
}

// Debugging: Log Paystack API response
error_log("Paystack API Response: " . $response);

$result = json_decode($response, true);

if ($httpCode !== 200 || !$result || !isset($result['status']) || !$result['status']) {
    echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Invalid response from Paystack']);
    exit;
}

// Success Response
echo json_encode([
    'success' => true,
    'accountNumber' => $result['data']['account_number'],
    'accountName' => $result['data']['account_name']
]);
?>
