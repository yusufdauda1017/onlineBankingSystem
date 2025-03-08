<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/db/db_connect.php");

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?? filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit;
}

$action = $data['action'] ?? null;
if (!$action) {
    echo json_encode(['success' => false, 'message' => 'Action not specified.']);
    exit;
}

switch ($action) {
    case 'fetch':
        echo json_encode(fetchbeneficiary($conn));
        break;

    case 'add':
        if (isset($data['accountName'], $data['accountNumber'], $data['bank'], $data['nickname'])) {
            echo json_encode(addBeneficiary($conn, $_SESSION['user_id'], $data['accountName'], $data['accountNumber'], $data['bank'], $data['nickname']));
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        }
        break;

    case 'search':
        if (isset($data['searchTerm'])) {
            echo json_encode(searchbeneficiary($conn, $data['searchTerm']));
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing search term.']);
        }
        break;

    case 'edit':
        if (isset($data['id'], $data['accountName'], $data['accountNumber'], $data['bank'], $data['nickname'])) {
            echo json_encode(editBeneficiary($conn, $data['id'], $data['accountName'], $data['accountNumber'], $data['bank'], $data['nickname']));
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        }
        break;

    case 'remove':
        if (isset($data['id'])) {
            echo json_encode(removeBeneficiary($conn, intval($data['id'])));
        } else {
            echo json_encode(['success' => false, 'message' => 'Beneficiary ID is required.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        break;
}

function fetchbeneficiary($conn) {
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => 'User not logged in.'];
    }
    $stmt = $conn->prepare("SELECT id, bank, accountName, account_number, nickname FROM beneficiary WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    return ['success' => true, 'data' => $result->fetch_all(MYSQLI_ASSOC)];
}

function addBeneficiary($conn, $userId, $accountName, $accountNumber, $bank, $nickname) {
    if (!is_numeric($accountNumber)) {
        return ['success' => false, 'message' => 'Account number must be numeric.'];
    }

   // Check if beneficiary already exists with the same user_id, account_number, and (bank_name + account_name) OR nickname
$stmt = $conn->prepare("SELECT id FROM beneficiary WHERE user_id = ? AND account_number = ? AND ((bank = ? AND accountName = ?) OR nickname = ?)");
$stmt->bind_param("issss", $userId, $accountNumber, $bankName, $accountName, $nickname);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    return ['success' => false, 'message' => 'Beneficiary already exists.'];
}

    // Insert new beneficiary
    $stmt = $conn->prepare("INSERT INTO beneficiary (user_id, accountName, account_number, bank, nickname) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $userId, $accountName, $accountNumber, $bank, $nickname);
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Beneficiary added successfully.'];
    } else {
        return ['success' => false, 'message' => 'Failed to add beneficiary.'];
    }
}

function searchbeneficiary($conn, $searchTerm) {
    $searchTerm = "%$searchTerm%";
    $stmt = $conn->prepare("SELECT id, bank, accountName, account_number, nickname FROM beneficiary WHERE accountName LIKE ? OR account_number LIKE ? OR bank LIKE ? OR nickname LIKE ?");
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    return ['success' => true, 'data' => $result->fetch_all(MYSQLI_ASSOC)];
}

function editBeneficiary($conn, $id, $accountName, $accountNumber, $bank, $nickname) {
    if (!is_numeric($accountNumber)) {
        return ['success' => false, 'message' => 'Account number must be numeric.'];
    }
    $stmt = $conn->prepare("UPDATE beneficiary SET accountName = ?, account_number = ?, bank = ?, nickname = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $accountName, $accountNumber, $bank, $nickname, $id);
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Beneficiary updated successfully.'];
    } else {
        return ['success' => false, 'message' => 'Failed to update beneficiary.'];
    }
}

function removeBeneficiary($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM beneficiary WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Beneficiary removed successfully.'];
    } else {
        return ['success' => false, 'message' => 'Failed to remove beneficiary.'];
    }
}
?>