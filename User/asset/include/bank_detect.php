<?php
header('Content-Type: application/json'); // Set JSON response type

$accountNumber = $_POST['account_number'] ?? ''; // Get user input
$paystackSecretKey = "sk_test_87f15c0781ff16439ee29e544250b46109aa478e"; // Paystack API Key

// Validate account number (must be exactly 10 digits)
if (!preg_match('/^\d{10}$/', $accountNumber)) {
    echo json_encode(["status" => false, "message" => "❌ Invalid account number. Must be 10 digits."]);
    exit;
}

// Identify if the input is a phone number (starts with 91, 81, 70, 80, or 90)
$isPhoneNumber = preg_match('/^(91|81|70|80|90)/', $accountNumber);

// Define bank files
$digitalBanksFile = '../digital_banks.json';
$commercialBanksFile = '../file.json';

// Function to load bank data
function loadBanks($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }
    $data = json_decode(file_get_contents($filePath), true);
    return $data['data'] ?? [];
}

// Function to verify the account number with Paystack
function verifyAccount($accountNumber, $banks, $paystackSecretKey) {
    $responses = [];

    foreach ($banks as $bank) {
        $bankCode = $bank['code'] ?? null;
        if ($bankCode) {
            $url = "https://api.paystack.co/bank/resolve?account_number=$accountNumber&bank_code=$bankCode";
            $context = stream_context_create([
                "http" => [
                    "method" => "GET",
                    "header" => "Authorization: Bearer $paystackSecretKey\r\n",
                    "timeout" => 5
                ]
            ]);

            $response = file_get_contents($url, false, $context);
            $data = json_decode($response, true);

            if ($data['status'] ?? false) {
                $responses[] = [
                    "account_name" => $data['data']['account_name'],
                    "bank" => $bank['name'],
                    "bank_code" => $bank['code'],
                    "logo" => $bank['logo'] ?? 'default-logo.png',
                ];
            }
        }
    }

    return $responses;
}

// Step 1: Check Digital Banks First (For Phone Numbers)
if ($isPhoneNumber) {
    $digitalBanks = loadBanks($digitalBanksFile);
    
    if (empty($digitalBanks)) {
        echo json_encode(["status" => false, "message" => "🚨 Digital bank file is missing or empty."]);
        exit;
    }

    $foundAccounts = verifyAccount($accountNumber, $digitalBanks, $paystackSecretKey);

    if (!empty($foundAccounts)) {
        echo json_encode(["status" => true, "data" => $foundAccounts]);
        exit;
    }
}

// Step 2: Check Commercial Banks if Not Found
$commercialBanks = loadBanks($commercialBanksFile);

if (empty($commercialBanks)) {
    echo json_encode(["status" => false, "message" => "🚨 Commercial bank file is missing or empty."]);
    exit;
}

$foundAccounts = verifyAccount($accountNumber, $commercialBanks, $paystackSecretKey);

echo json_encode($foundAccounts ? ["status" => true, "data" => $foundAccounts] : ["status" => false, "message" => "❌ Account not found."]);
?>
