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
$user_id = $_SESSION['user_id'];

// 1. Verify ownership AND that status is 'Pending'
$check = $conn->prepare("
    SELECT b.booking_id 
    FROM event_bookings b
    JOIN events e ON b.event_id = e.event_id
    JOIN clients c ON e.client_id = c.client_id
    WHERE b.booking_id = ? AND c.user_id = ? AND b.booking_status = 'Pending'
");
$check->bind_param("si", $booking_id, $user_id);
$check->execute();

if($check->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Cannot cancel. Booking not found or already processed.']);
    exit();
}

// 2. Update Status AND Set Timestamp (Crucial for 1-hour deletion)
$stmt = $conn->prepare("UPDATE event_bookings SET booking_status = 'Cancelled', cancelled_at = NOW() WHERE booking_id = ?");
$stmt->bind_param("s", $booking_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}
?>