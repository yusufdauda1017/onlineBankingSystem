<?php
$paystackSecretKey = "sk_test_87f15c0781ff16439ee29e544250b46109aa478e";
$accountNumber = "1613411597";
$bankCode = "044";

$url = "https://api.paystack.co/bank/resolve?account_number=$accountNumber&bank_code=$bankCode";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $paystackSecretKey",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo "CURL Error: " . $curlError;
} else {
    echo "HTTP Code: " . $httpCode . "\n";
    echo "Response: " . $response;
}
?>
