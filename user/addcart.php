<?php
session_start();
require_once '../db/conn.php';
require_once '../header.php';

// Redirect if not user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

// Current session id
$session_id = session_id();

// Fetch cart items: user cart if logged in, otherwise guest cart
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM addcart WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$_SESSION['user_id']]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM addcart WHERE session_id = ? ORDER BY id DESC");
    $stmt->execute([$session_id]);
}

$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f9f9f9;
        }
        /* Add top padding equal to navbar height (adjust 70px to match your navbar) */
        main {
            flex: 1;
            padding-top: 70px;
        }
        h2 { 
            text-align: center; 
            margin: 30px 0;
            color: #333;
        }
        table { 
            border-collapse: collapse; 
            width: 90%; 
            margin: 0 auto 30px auto; 
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td { 
            border: 1px solid #ccc; 
            padding: 10px; 
            text-align: center; 
        }
        th { 
            background: #6f42c1; 
            color: white; 
        }
        img { 
            max-width: 80px; 
            height: auto; 
            object-fit: cover;
        }
        p.empty-cart {
            text-align: center;
            font-size: 18px;
            margin: 20px 0;
            color: #555;
        }
        /* Footer styling */
        footer {
            background: #222;
            color: #fff;
            text-align: center;
            padding: 20px 0;
        }
    </style>
</head>
<body>

<main>
    <h2>Your Cart</h2>

    <?php if (count($cartItems) === 0): ?>
        <p class="empty-cart">Your cart is empty.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Image</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Size</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cartItems as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td>
                    <img src="../images/<?= htmlspecialchars($row['product_image']) ?>" 
                         alt="<?= htmlspecialchars($row['product_name']) ?>">
                </td>
                <td>$<?= htmlspecialchars($row['product_price']) ?></td>
                <td><?= htmlspecialchars($row['quantity']) ?></td>
                <td><?= htmlspecialchars($row['size']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</main>

<!-- Footer -->
<?php include '../include/footer.php'; ?>

</body>
</html>
