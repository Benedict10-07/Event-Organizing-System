<?php
// File: php/reset_admin.php
require_once 'db_connect.php';

$new_password = 'admin123'; // This will be your new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->bind_param("s", $hashed_password);

if ($stmt->execute()) {
    echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'>";
    echo "<h1 style='color:green;'>Success!</h1>";
    echo "<p>Admin password has been reset to: <strong>" . $new_password . "</strong></p>";
    echo "<br><a href='../login.html' style='background:#5A1F2E; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Go to Login</a>";
    echo "</div>";
} else {
    echo "Error: " . $conn->error;
}
?>