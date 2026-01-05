<?php
session_start();
require 'db/conn.php';

$product_id = $_POST['id'] ?? null;
$size = $_POST['size'] ?? 'No Size';
if (!$product_id) exit(json_encode(['status'=>'error','msg'=>'No product selected']));

$session_id = session_id();
$user_id = $_SESSION['user_id'] ?? null;

// Get product details
$stmt = $pdo->prepare("SELECT id, title, image, price FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) exit(json_encode(['status'=>'error','msg'=>'Product not found']));

// Merge guest cart to user cart once after login
if ($user_id) {
    $transfer = $pdo->prepare("UPDATE addcart SET user_id = ? WHERE session_id = ? AND user_id IS NULL");
    $transfer->execute([$user_id, $session_id]);
}

// Determine owner
$owner_column = $user_id ? 'user_id' : 'session_id';
$owner_value = $user_id ?? $session_id;

// Check if product exists (with same size)
$stmt = $pdo->prepare("SELECT id, quantity FROM addcart WHERE $owner_column = ? AND product_name = ? AND size = ?");
$stmt->execute([$owner_value, $product['title'], $size]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    // Update quantity
    $update = $pdo->prepare("UPDATE addcart SET quantity = quantity + 1 WHERE id = ?");
    $update->execute([$existing['id']]);
} else {
    // Insert new product
    $insert = $pdo->prepare("INSERT INTO addcart 
        (session_id, user_id, product_name, product_image, product_price, quantity, size) 
        VALUES (?, ?, ?, ?, ?, 1, ?)");
    $insert->execute([$session_id, $user_id, $product['title'], $product['image'], $product['price'], $size]);
}

// Return updated cart info
$stmt = $pdo->prepare($user_id ? "SELECT * FROM addcart WHERE user_id = ?" : "SELECT * FROM addcart WHERE session_id = ?");
$stmt->execute([$owner_value]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalItems = count($cartItems);

echo json_encode([
    'status' => 'success',
    'total' => $totalItems,
    'items' => $cartItems
]);
?>

