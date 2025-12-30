<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$response = ['success' => true, 'data' => []];

try {
    // --- AUTO-DELETE LOGIC 1: Completed events older than 7 days ---
    $conn->query("DELETE FROM events WHERE event_date < DATE_SUB(NOW(), INTERVAL 7 DAY)");

    // --- AUTO-DELETE LOGIC 2: Cancelled bookings older than 1 hour ---
    $conn->query("DELETE FROM event_bookings WHERE booking_status = 'Cancelled' AND cancelled_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    
    // 2. Get Client ID
    $stmt = $conn->prepare("SELECT client_id FROM clients WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows === 0) throw new Exception("Client profile not found");
    $client_id = $res->fetch_assoc()['client_id'];

    // 3. Fetch Bookings
    $sql = "
        SELECT 
            b.booking_id, 
            b.booking_status, 
            b.agreed_budget, 
            e.event_type, 
            e.event_date, 
            e.venue_name,
            p.package_name,
            (SELECT COUNT(*) FROM feedbacks f WHERE f.booking_id = b.booking_id) as has_feedback
        FROM event_bookings b
        JOIN events e ON b.event_id = e.event_id
        LEFT JOIN packages p ON b.package_id = p.package_id
        WHERE e.client_id = ?
        ORDER BY e.event_date DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();

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