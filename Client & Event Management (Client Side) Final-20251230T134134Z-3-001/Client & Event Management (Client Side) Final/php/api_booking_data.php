<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

$response = ['packages' => [], 'venues' => [], 'addons' => [], 'package_definitions' => []];

$pkgResult = $conn->query("SELECT * FROM packages");
while ($row = $pkgResult->fetch_assoc()) $response['packages'][] = $row;

$svcQuery = "SELECT s.*, v.vendor_name FROM services s LEFT JOIN vendors v ON s.vendor_id = v.vendor_id ORDER BY s.category, s.service_name";
$svcResult = $conn->query($svcQuery);
while ($row = $svcResult->fetch_assoc()) {
    $cat = $row['category'];
    if (stripos($cat, 'Venue') !== false && stripos($row['service_name'], 'Coordination') === false) {
        $response['venues'][] = $row;
    } else {
        if (!isset($response['addons'][$cat])) $response['addons'][$cat] = [];
        $response['addons'][$cat][] = $row;
    }
}

$defQuery = "SELECT pd.package_id, pd.event_type, s.service_name FROM package_definitions pd JOIN services s ON pd.service_id = s.service_id";
$defResult = $conn->query($defQuery);
while ($row = $defResult->fetch_assoc()) {
    $response['package_definitions'][$row['package_id']][$row['event_type']][] = $row['service_name'];
}

echo json_encode($response);
$conn->close();
?>