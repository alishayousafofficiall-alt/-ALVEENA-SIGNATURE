<?php
session_start();
require_once 'db/conn.php';

if (!isset($_GET['id'])) {
    header("Location: cart.php");
    exit;
}

$id = (int)$_GET['id'];
$session_id = session_id();

// Delete item from cart for this session
$stmt = $pdo->prepare("DELETE FROM addcart WHERE id = ? AND session_id = ?");
$stmt->execute([$id, $session_id]);

header("Location: cart.php");
exit;
