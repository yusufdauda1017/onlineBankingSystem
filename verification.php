<?php
header('Content-Type: application/json'); // Set JSON header

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect input data from POST request
    $firstName = $_POST['fName'] ?? 'N/A';
    $surname = $_POST['sName'] ?? 'N/A';
    $otherName = $_POST['otherName'] ?? 'N/A';
    $phoneNumber = $_POST['phoneNumber'] ?? 'N/A';
    $email = $_POST['email'] ?? 'N/A';
    $dob = $_POST['dob'] ?? 'N/A';
    $gender = $_POST['gender'] ?? 'N/A';

    // Abstract API key (replace with your actual API key)
    $apiKey = '7ab4236e294e4116b15ee4211989f053'; // Replace with your Abstract API key
    $apiUrl = "https://emailvalidation.abstractapi.com/v1/?api_key=$apiKey&email=" . urlencode($email);

    // Perform the API request
    $response = file_get_contents($apiUrl);
    if ($response === false) {
        echo json_encode(['success' => false, 'message' => 'Error contacting the email validation service.']);
        exit;
    }

    // Decode the API response
    $result = json_decode($response, true);

    // Check email validity with additional logic (checking role-based emails)
    if (isset($result['deliverability']) && $result['deliverability'] === 'DELIVERABLE' &&
        isset($result['is_disposable_email']) && !$result['is_disposable_email']['value'] &&
        isset($result['is_mx_found']) && $result['is_mx_found']['value'] === true &&
        isset($result['is_smtp_valid']) && $result['is_smtp_valid']['value'] === true) {

        echo json_encode(['success' => true, 'message' => 'Email is valid.']);
    } else {

        echo json_encode(['success' => false, 'message' => 'invalid try']);
    }
} else {

    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
