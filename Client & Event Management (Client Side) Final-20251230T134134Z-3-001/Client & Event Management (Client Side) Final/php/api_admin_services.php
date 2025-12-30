<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

// Security: Admin Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- 1. FETCH ALL DATA (Services & Vendors) ---
if ($action === 'get_all') {
    $data = ['services' => [], 'vendors' => []];
    
    // Get Services (Join with Vendors to show Vendor Name)
    $svc = $conn->query("SELECT s.*, v.vendor_name FROM services s LEFT JOIN vendors v ON s.vendor_id = v.vendor_id ORDER BY s.category, s.service_name");
    while($row = $svc->fetch_assoc()) $data['services'][] = $row;

    // Get Vendors
    $vnd = $conn->query("SELECT * FROM vendors ORDER BY vendor_name");
    while($row = $vnd->fetch_assoc()) $data['vendors'][] = $row;

    echo json_encode(['success' => true, 'data' => $data]);
    exit();
}

// --- 2. ADD SERVICE ---
if ($action === 'add_service') {
    $name = $_POST['name'];
    $cat = $_POST['category'];
    $price = $_POST['price'];
    $vendor = $_POST['vendor_id'];
    $scope = $_POST['scope'];
    
    // Check if 'status' column exists (if you added it), default to 'Available'
    $stmt = $conn->prepare("INSERT INTO services (service_name, category, price, vendor_id, event_scope, status) VALUES (?, ?, ?, ?, ?, 'Available')");
    $stmt->bind_param("ssdis", $name, $cat, $price, $vendor, $scope);
    
    if ($stmt->execute()) echo json_encode(['success' => true]);
    else echo json_encode(['success' => false, 'message' => $conn->error]);
}

// --- 3. EDIT SERVICE ---
if ($action === 'update_service') {
    $id = $_POST['service_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE services SET service_name=?, price=?, status=? WHERE service_id=?");
    $stmt->bind_param("sdsi", $name, $price, $status, $id);
    
    if ($stmt->execute()) echo json_encode(['success' => true]);
    else echo json_encode(['success' => false, 'message' => $conn->error]);
}

// --- 4. DELETE SERVICE ---
if ($action === 'delete_service') {
    $id = $_POST['service_id'];
    $stmt = $conn->prepare("DELETE FROM services WHERE service_id=?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) echo json_encode(['success' => true]);
    else echo json_encode(['success' => false, 'message' => $conn->error]);
}

// --- 5. ADD VENDOR ---
if ($action === 'add_vendor') {
    $name = $_POST['name'];
    $cat = $_POST['category'];
    
    $stmt = $conn->prepare("INSERT INTO vendors (vendor_name, category, status) VALUES (?, ?, 'Active')");
    $stmt->bind_param("ss", $name, $cat);
    
    if ($stmt->execute()) echo json_encode(['success' => true]);
    else echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>