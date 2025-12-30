<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];

// Generate IDs
$booking_id = "BK-" . strtoupper(uniqid());
$event_id = "EV-" . strtoupper(uniqid());

$conn->begin_transaction();

try {
    // 2. Get Client ID (Fixed: Uses user_id instead of email)
    $stmt = $conn->prepare("SELECT client_id FROM clients WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Client profile not found for this user.");
    }
    
    $client_id = $result->fetch_assoc()['client_id'];
    $stmt->close();

    // 3. Insert Event
    $stmt = $conn->prepare("INSERT INTO events (event_id, client_id, event_type, event_date, venue_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $event_id, $client_id, $data['event_type'], $data['event_date'], $data['venue_name']);
    $stmt->execute();
    $stmt->close();

    // 4. Insert Booking
    $stmt = $conn->prepare("INSERT INTO event_bookings (booking_id, event_id, package_id, agreed_budget, booking_status) VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("ssid", $booking_id, $event_id, $data['package_id'], $data['total_budget']);
    $stmt->execute();
    $stmt->close();

    // 5. Insert Custom Services (Add-ons)
    if (isset($data['services']) && is_array($data['services'])) {
        $stmt = $conn->prepare("INSERT INTO booking_services (booking_id, service_id) VALUES (?, ?)");
        foreach ($data['services'] as $service_id) {
            $stmt->bind_param("si", $booking_id, $service_id);
            $stmt->execute();
        }
        $stmt->close();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'booking_id' => $booking_id]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>