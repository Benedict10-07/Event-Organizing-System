<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

// 1. Security: Admin Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = $data['booking_id'] ?? '';
$action = $data['action'] ?? 'update'; // Default to update
$status = $data['status'] ?? '';

if (empty($booking_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit();
}

if ($action === 'delete') {
    // --- DELETE LOGIC ---
    // We delete the booking. The Database "ON DELETE CASCADE" should handle the services.
    $stmt = $conn->prepare("DELETE FROM event_bookings WHERE booking_id = ?");
    $stmt->bind_param("s", $booking_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Booking deleted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Delete failed']);
    }
    $stmt->close();

} else {
    // --- UPDATE STATUS LOGIC (Approve/Reject) ---
    if (empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Status required']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE event_bookings SET booking_status = ? WHERE booking_id = ?");
    $stmt->bind_param("ss", $status, $booking_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
    $stmt->close();
}

$conn->close();
?>