<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$booking_id = $_GET['id'] ?? '';

// 1. Fetch Booking Info (Added b.package_id to select list)
$sql = "
    SELECT 
        b.booking_id, 
        b.package_id, 
        b.agreed_budget, 
        e.venue_name, 
        e.event_type, 
        e.event_date,
        p.package_name, 
        p.base_price,
        c.full_name as client_name
    FROM event_bookings b
    JOIN events e ON b.event_id = e.event_id
    JOIN clients c ON e.client_id = c.client_id
    LEFT JOIN packages p ON b.package_id = p.package_id
    WHERE b.booking_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if ($booking) {
    $currentEventType = $booking['event_type'];
    $packageId = $booking['package_id'];

    // 2. Fetch Services ALREADY BOOKED (Add-ons)
    $bookedIds = [];
    $bookedDetails = [];
    
    $svcSql = "
        SELECT bs.service_id, s.service_name, s.price 
        FROM booking_services bs
        JOIN services s ON bs.service_id = s.service_id
        WHERE bs.booking_id = ?
    ";
    $stmt2 = $conn->prepare($svcSql);
    $stmt2->bind_param("s", $booking_id);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    
    while ($row = $res2->fetch_assoc()) {
        $bookedIds[] = $row['service_id'];
        $bookedDetails[] = $row;
    }
    $booking['booked_service_ids'] = $bookedIds;
    $booking['booked_details'] = $bookedDetails;

    // 3. NEW: Fetch Standard PACKAGE INCLUSIONS
    $inclusions = [];
    if ($packageId) {
        $incSql = "
            SELECT s.service_name 
            FROM package_definitions pd
            JOIN services s ON pd.service_id = s.service_id
            WHERE pd.package_id = ? AND pd.event_type = ?
        ";
        $stmtInc = $conn->prepare($incSql);
        $stmtInc->bind_param("is", $packageId, $currentEventType);
        $stmtInc->execute();
        $resInc = $stmtInc->get_result();
        
        while ($row = $resInc->fetch_assoc()) {
            $inclusions[] = $row['service_name'];
        }
    }
    $booking['inclusions'] = $inclusions;

    // 4. Fetch AVAILABLE SERVICES for this Event Type (including 'All' scope)
    $allSvcSql = "
        SELECT service_id, service_name, price, category, status 
        FROM services 
        WHERE (event_scope = 'All' OR event_scope = ?)
        AND category != 'Venue'
        ORDER BY category, service_name
    ";

    $stmt3 = $conn->prepare($allSvcSql);
    $stmt3->bind_param("s", $currentEventType);
    $stmt3->execute();
    $res3 = $stmt3->get_result();
    
    $availableServices = [];
    while ($row = $res3->fetch_assoc()) {
        $availableServices[] = $row;
    }
    $booking['available_services'] = $availableServices;
    
    echo json_encode($booking);
} else {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
}
?>