<?php
session_start();
header('Content-Type: application/json');

$response = [
    'logged_in' => false,
    'user' => null
];

if (isset($_SESSION['user_id'])) {
    include 'db_connect.php';
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT user_id, username, full_name, email, contact_number FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $response['logged_in'] = true;
        $response['user'] = $row;
    }
}

echo json_encode($response);
?>