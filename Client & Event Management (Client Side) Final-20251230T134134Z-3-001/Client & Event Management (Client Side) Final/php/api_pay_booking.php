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
$ref_num = $data['ref_num'];

// 1. Verify Booking Exists
$check = $conn->prepare("SELECT booking_id FROM event_bookings WHERE booking_id = ?");
$check->bind_param("s", $booking_id);
$check->execute();
if($check->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit();
}

// 2. Update Status to 'Ongoing' (Instead of Completed)
// This signifies that payment is received and services are now in preparation.
$stmt = $conn->prepare("UPDATE event_bookings SET booking_status = 'Ongoing' WHERE booking_id = ?");
$stmt->bind_param("s", $booking_id);

if ($stmt->execute()) {
    // Optional: You could save the $ref_num to a 'payments' table here if you had one.
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}
?>