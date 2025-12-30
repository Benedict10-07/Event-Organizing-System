<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

// 1. Security: Admin Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$response = ['success' => true, 'data' => []];

try {
    // 2. Fetch All Bookings with Client Info
    $sql = "
        SELECT 
            b.booking_id, 
            b.booking_status, 
            b.agreed_budget, 
            e.event_type, 
            e.event_date, 
            e.venue_name,
            c.full_name,
            u.contact_number
        FROM event_bookings b
        JOIN events e ON b.event_id = e.event_id
        JOIN clients c ON e.client_id = c.client_id
        JOIN users u ON c.user_id = u.user_id
        ORDER BY 
            CASE WHEN b.booking_status = 'Pending' THEN 1 ELSE 2 END, 
            e.event_date ASC
    ";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $response['data'][] = $row;
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>