<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

// Security: Admin Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = $data['booking_id'];

// Update status to Completed
$stmt = $conn->prepare("UPDATE event_bookings SET booking_status = 'Completed' WHERE booking_id = ?");
$stmt->bind_param("s", $booking_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>