<?php
session_start();
require_once '../db/conn.php';

// Get session id
$session_id = session_id();

// Check if user is logged in
$userEmail = $_SESSION['email'] ?? null;

// Fetch orders
if ($userEmail) {
  // Logged-in user
  $stmt = $pdo->prepare("SELECT * FROM orders WHERE email = ? ORDER BY created_at DESC");
  $stmt->execute([$userEmail]);
} else {
  // Guest user
  $stmt = $pdo->prepare("SELECT * FROM orders WHERE session_id = ? ORDER BY created_at DESC");
  $stmt->execute([$session_id]);
}

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>My Orders</title>
  <style>
    html,
    body {
      height: 100%;
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      flex-direction: column;
    }

    .content {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      padding: 20px;
    }

    h2 {
      margin-bottom: 15px;
      color: #333;
      text-align: center;
    }

    table {
      border-collapse: collapse;
      width: 900px;
      background: #fff;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      overflow: hidden;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }

    th {
      background-color: #9370DB;
      color: #fff;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    p {
      text-align: center;
      font-size: 16px;
      color: #555;
    }
  </style>
</head>

<body>

  <div class="content">
    <h2>My Orders</h2>

    <?php if (empty($orders)): ?>
      <p>You have no orders yet.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Order Details</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Ordered On</th>
            <th>Email/Guest</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $order): ?>
            <tr>
              <td><?= htmlspecialchars($order['id']) ?></td>
              <td><?= htmlspecialchars($order['order_details']) ?></td>
              <td>Rs <?= number_format($order['total_price'], 2) ?></td>
              <td><strong><?= htmlspecialchars(ucfirst($order['status'])) ?></strong></td>
              <td><?= htmlspecialchars($order['created_at']) ?></td>
              <td><?= htmlspecialchars($order['email'] ?? 'Guest') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <?php include '../include/footer.php'; ?>

</body>

</html>