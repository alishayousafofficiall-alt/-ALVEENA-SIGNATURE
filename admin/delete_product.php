<?php

session_start();
require_once '../db/conn.php';

 require_once '../header.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}


$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: products.php");
    exit();
}

// Pehle product ka image name fetch karo taake image bhi delete kar sakein
$stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product) {
    // Image file delete karo agar exist karti ho
    $imagePath = '../images/' . $product['image'];
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }

    // Phir product delete karo
    $delStmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $delStmt->execute([$id]);
}

header("Location: products.php");
exit();
