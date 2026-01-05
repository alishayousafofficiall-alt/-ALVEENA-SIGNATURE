<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli("localhost", "root", "", "ecommerce");
if ($mysqli->connect_error) {
    die("DB connection failed: " . $mysqli->connect_error);
}

// New admin password
$newPassword = "admin123";
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$email = "admin@example.com";

$stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashedPassword, $email);

if ($stmt->execute()) {
    echo "Admin password has been reset successfully!<br>";
    echo "Use email: $email <br> Password: $newPassword";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>