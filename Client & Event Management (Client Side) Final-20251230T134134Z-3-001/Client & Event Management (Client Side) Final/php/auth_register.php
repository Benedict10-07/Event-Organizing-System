<?php
require_once 'db_connect.php';

// 1. Get data
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';
$full_name = $_POST['full_name'] ?? '';
$email     = $_POST['email'] ?? '';
$contact   = $_POST['contact_number'] ?? '';

// 2. Basic Validation
if (empty($username) || empty($password) || empty($full_name)) {
    header("Location: ../register.html?error=empty_fields");
    exit();
}

if ($password !== $confirm) {
    header("Location: ../register.html?error=password_mismatch");
    exit();
}

// 3. Check if User Exists
$check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    header("Location: ../register.html?error=username_taken");
    exit();
}

// 4. Register the User
$hashed_pass = password_hash($password, PASSWORD_DEFAULT);
$role = 'client';

$stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, contact_number, role) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $username, $hashed_pass, $full_name, $email, $contact, $role);

if ($stmt->execute()) {
    $new_user_id = $conn->insert_id; 

    // IMPROVED ID GENERATION HERE
    $client_id = 'CL-' . bin2hex(random_bytes(4));

    $stmt2 = $conn->prepare("INSERT INTO clients (client_id, user_id, full_name) VALUES (?, ?, ?)");
    $stmt2->bind_param("sis", $client_id, $new_user_id, $full_name);
    $stmt2->execute();

    header("Location: ../login.html?success=registered");
} else {
    header("Location: ../register.html?error=sql_error");
}

$stmt->close();
$conn->close();
?>