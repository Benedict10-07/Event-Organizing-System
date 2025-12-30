<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = isset($_POST['username']) ? trim($_POST['username']) : '';
    $pass = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($user) || empty($pass)) {
        header("Location: ../login.html?error=empty_fields");
        exit();
    }

    $stmt = $conn->prepare("SELECT user_id, username, password, role, full_name, email, contact_number FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify Password
        if (password_verify($pass, $row['password'])) {
            // Set Session Variables
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['full_name'] = $row['full_name'];
            
            // --- FIX: REDIRECT BASED ON ROLE ---
            if ($row['role'] === 'admin') {
                header("Location: ../admin_dashboard.php");
            } else {
                header("Location: ../index.html");
            }
            exit();
            
        } else {
            header("Location: ../login.html?error=Incorrect_password");
            exit();
        }
    } else {
        header("Location: ../login.html?error=User_not_found");
        exit();
    }
    $stmt->close();
} else {
    header("Location: ../login.html");
    exit();
}
?>