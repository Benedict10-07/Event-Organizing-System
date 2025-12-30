<?php
include 'db_connect.php';
header('Content-Type: application/json');

// LOGIC: 
// 1. rating >= 4 (Prioritize high ratings)
// 2. ORDER BY date_posted DESC (Show recent first)
// 3. LIMIT 6 (Display max 6 cards for layout balance)

$sql = "SELECT client_name, rating, comment, date_posted 
        FROM feedbacks 
        WHERE rating >= 4 
        ORDER BY date_posted DESC 
        LIMIT 6";

$result = $conn->query($sql);

$feedbacks = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}

echo json_encode($feedbacks);
$conn->close();
?>