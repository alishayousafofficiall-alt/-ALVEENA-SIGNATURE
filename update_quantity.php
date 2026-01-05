<?php
session_start();
require_once 'db/conn.php';

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: cart.php");
    exit;
}

$id = (int)$_GET['id'];
$type = $_GET['type'];

$session_id = session_id();

// Pehle item fetch karo
$stmt = $pdo->prepare("SELECT quantity FROM addcart WHERE id = ? AND session_id = ?");
$stmt->execute([$id, $session_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    // Item nahi mila, cart page redirect
    header("Location: cart.php");
    exit;
}

$current_qty = (int)$item['quantity'];

if ($type === 'increase') {
    $new_qty = $current_qty + 1;
} elseif ($type === 'decrease') {
    $new_qty = max(1, $current_qty - 1); // quantity 1 se kam nahi
} else {
    // Invalid action
    header("Location: cart.php");
    exit;
}

// Update quantity
$update = $pdo->prepare("UPDATE addcart SET quantity = ? WHERE id = ? AND session_id = ?");
$update->execute([$new_qty, $id, $session_id]);

header("Location: cart.php");
exit;
