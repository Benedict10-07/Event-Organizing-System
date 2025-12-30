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

// Check if booking belongs to this user before deleting
$user_id = $_SESSION['user_id'];
$check = $conn->prepare("
    SELECT b.booking_id 
    FROM event_bookings b
    JOIN events e ON b.event_id = e.event_id
    JOIN clients c ON e.client_id = c.client_id
    WHERE b.booking_id = ? AND c.user_id = ?
");
$check->bind_param("si", $booking_id, $user_id);
$check->execute();
if($check->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Booking not found or access denied']);
    exit();
}

// Perform Delete (This cascades to services due to your DB setup)
$del = $conn->prepare("DELETE FROM event_bookings WHERE booking_id = ?");
$del->bind_param("s", $booking_id);

if ($del->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Delete failed']);
}
?>