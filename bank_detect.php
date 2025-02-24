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
$digitalBanksFile = './digital_banks.json';
$commercialBanksFile = './file.json';

// Function to load bank data with caching
function loadBanks($filePath) {
    static $cache = [];
    if (!isset($cache[$filePath])) {
        $cache[$filePath] = file_exists($filePath) ? json_decode(file_get_contents($filePath), true)['data'] ?? [] : [];
    }
    return $cache[$filePath];
}

function verifyAccount($accountNumber, $banks, $paystackSecretKey) {
    $mh = curl_multi_init();
    $curlHandles = [];
    $responses = [];

    foreach ($banks as $bank) {
        $bankCode = $bank['code'] ?? null;
        if ($bankCode) {
            $ch = curl_init("https://api.paystack.co/bank/resolve?account_number=$accountNumber&bank_code=$bankCode");
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ["Authorization: Bearer $paystackSecretKey"],
                CURLOPT_TIMEOUT => 3,
            ]);
            curl_multi_add_handle($mh, $ch);
            $curlHandles[$bank['name']] = $ch;
        }
    }

    do {
        $status = curl_multi_exec($mh, $running);
    } while ($running && $status === CURLM_OK);

    foreach ($curlHandles as $bankName => $ch) {
        $response = json_decode(curl_multi_getcontent($ch), true);
        if ($response['status'] ?? false) {
            // Fetch correct bank details
            foreach ($banks as $bank) {
                if ($bank['name'] === $bankName) {
                    $responses[] = [
                        "account_name" => $response['data']['account_name'],
                        "bank" => $bankName,
                        "bank_code" => $response['data']['bank_code'] ?? null,
                        "logo" => $bank['logo'] ?? 'default-logo.png', // Ensure logo is set
                    ];
                    break;
                }
            }
        }
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    curl_multi_close($mh);
    return $responses;
}

// Step 1: Check Digital Banks First (For Phone Numbers)
if ($isPhoneNumber) {
    $digitalBanks = loadBanks($digitalBanksFile);
    $foundAccounts = verifyAccount($accountNumber, $digitalBanks, $paystackSecretKey);

    if (!empty($foundAccounts)) {
        echo json_encode(["status" => true, "data" => $foundAccounts]);
        exit;
    }
}

// Step 2: Check Commercial Banks if Not Found
$commercialBanks = loadBanks($commercialBanksFile);
$foundAccounts = verifyAccount($accountNumber, $commercialBanks, $paystackSecretKey);

echo json_encode($foundAccounts ? ["status" => true, "data" => $foundAccounts] : ["status" => false, "message" => "❌ Account not found."]);
?>
