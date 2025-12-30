<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) exit(json_encode(['success'=>false, 'message'=>'Unauthorized']));

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = $data['booking_id'];
$venue = $data['venue_name'];
$service_ids = $data['service_ids']; // Array of Service IDs the user kept/added

$conn->begin_transaction();

try {
    // 1. Update Venue
    $stmt = $conn->prepare("UPDATE events e JOIN event_bookings b ON e.event_id = b.event_id SET e.venue_name = ? WHERE b.booking_id = ?");
    $stmt->bind_param("ss", $venue, $booking_id);
    $stmt->execute();
    $stmt->close();

    // 2. Sync Services (Delete old -> Insert new list)
    $del = $conn->prepare("DELETE FROM booking_services WHERE booking_id = ?");
    $del->bind_param("s", $booking_id);
    $del->execute();
    $del->close();

    if(!empty($service_ids)) {
        $ins = $conn->prepare("INSERT INTO booking_services (booking_id, service_id) VALUES (?, ?)");
        foreach($service_ids as $sid) {
            $ins->bind_param("si", $booking_id, $sid);
            $ins->execute();
        }
        $ins->close();
    }

    // 3. RECALCULATE & UPDATE TOTAL BUDGET (The Missing Logic)
    
    // A. Get Base Package Price
    $basePrice = 0;
    $pkgSql = "SELECT p.base_price 
               FROM event_bookings b 
               JOIN packages p ON b.package_id = p.package_id 
               WHERE b.booking_id = ?";
    $stmtPkg = $conn->prepare($pkgSql);
    $stmtPkg->bind_param("s", $booking_id);
    $stmtPkg->execute();
    $pkgRes = $stmtPkg->get_result();
    if($row = $pkgRes->fetch_assoc()) {
        $basePrice = (float)$row['base_price'];
    }
    $stmtPkg->close();

    // B. Calculate Total of Selected Services
    $svcTotal = 0;
    if(!empty($service_ids)) {
        // We sum the prices of the services currently linked to this booking
        // (This ensures we get the correct prices from the DB)
        $sumSql = "SELECT SUM(price) as total FROM services WHERE service_id IN (" . implode(',', array_map('intval', $service_ids)) . ")";
        $resultSum = $conn->query($sumSql);
        if($r = $resultSum->fetch_assoc()) {
            $svcTotal = (float)$r['total'];
        }
    }

    $newTotal = $basePrice + $svcTotal;

    // C. Update the Agreed Budget in Database
    $upd = $conn->prepare("UPDATE event_bookings SET agreed_budget = ? WHERE booking_id = ?");
    $upd->bind_param("ds", $newTotal, $booking_id);
    $upd->execute();
    $upd->close();

    $conn->commit();
    echo json_encode(['success'=>true, 'new_total'=>$newTotal]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
}

$conn->close();
?>