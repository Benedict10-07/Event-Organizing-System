<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = $data['booking_id'];
$rating = (int)$data['rating'];
$comment = $data['comment'];

// Get Client Info
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT client_id, full_name FROM clients WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$clientRes = $stmt->get_result();

if($clientRes->num_rows > 0) {
    $client = $clientRes->fetch_assoc();
    $client_id = $client['client_id'];
    $client_name = $client['full_name'];

    // Insert Feedback
    $ins = $conn->prepare("INSERT INTO feedbacks (booking_id, client_id, client_name, rating, comment) VALUES (?, ?, ?, ?, ?)");
    $ins->bind_param("sssis", $booking_id, $client_id, $client_name, $rating, $comment);
    
    if($ins->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'DB Error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Client not found']);
}
?>