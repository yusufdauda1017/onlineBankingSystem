<?php
header('Content-Type: application/json');

$accountNumber = $_POST['account_number'] ?? '';
$bankCode = $_POST['bank_code'] ?? '';
$paystackSecretKey = "sk_test_87f15c0781ff16439ee29e544250b46109aa478e";

$url = "https://api.paystack.co/bank/resolve?account_number=$accountNumber&bank_code=$bankCode";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $paystackSecretKey"]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
