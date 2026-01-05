<?php
session_start();
require_once 'db/conn.php';

// Get session id
$session_id = session_id();

// Check if user is logged in
$userEmail = $_SESSION['email'] ?? null;

// Order details from POST
$name = $_POST['name'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$total_price = $_POST['total_price'];
$order_details = $_POST['order_details'];

// Insert order
$stmt = $pdo->prepare("
    INSERT INTO orders 
    (session_id, name, email, phone, address, total_price, order_details, status, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
");
$stmt->execute([$session_id, $name, $userEmail, $phone, $address, $total_price, $order_details]);

// Redirect to order success page
header("Location: order_success.php");
exit;
?>