<?php 
session_start();
include 'db_connect.php'; 

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Go up one level to root
    exit();
}

// 2. Fetch User Info
$user_id = $_SESSION['user_id'];
$user_query = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_data = $user_query->get_result()->fetch_assoc();

// 3. Fetch Packages
$pkg_sql = "SELECT * FROM packages WHERE status='Available' 
            ORDER BY CASE WHEN base_price = 0 THEN 1 ELSE 0 END, base_price ASC";
$pkg_result = $conn->query($pkg_sql);

// 4. Fetch Venues
$venue_sql = "SELECT * FROM services WHERE category = 'Venue'";
$venue_result = $conn->query($venue_sql);
$venues = [];
while($row = $venue_result->fetch_assoc()) {
    $venues[] = $row;
}

// 5. Fetch Add-ons (Non-Venue services)
$addon_sql = "SELECT * FROM services WHERE category != 'Venue' ORDER BY category, service_name";
$addon_result = $conn->query($addon_sql);
$addons = [];
while($row = $addon_result->fetch_assoc()) {
    $addons[$row['category']][] = $row;
}

// 6. Fetch Package Definitions (For JS logic)
$definitions_sql = "
    SELECT pd.package_id, pd.event_type, s.service_name 
    FROM package_definitions pd
    JOIN services s ON pd.service_id = s.service_id
    ORDER BY pd.package_id, pd.event_type
";
$def_result = $conn->query($definitions_sql);
$package_services = [];
if ($def_result->num_rows > 0) {
    while($row = $def_result->fetch_assoc()) {
        $pid = $row['package_id'];
        $type = $row['event_type'];
        $package_services[$pid][$type][] = $row['service_name'];
    }
}
?>