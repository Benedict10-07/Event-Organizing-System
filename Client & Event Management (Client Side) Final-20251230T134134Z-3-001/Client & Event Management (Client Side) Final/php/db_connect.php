<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "client_eos"; // Ensure this matches your new database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>