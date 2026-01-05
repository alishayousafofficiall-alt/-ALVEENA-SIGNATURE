<?php
session_start();
require_once '../db/conn.php';
require_once '../header.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle form submit for status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $orderId = intval($_POST['order_id']);
    $status = $_POST['status'];
    $allowedStatuses = ['pending', 'shipped', 'delivered', 'cancelled'];

    if (in_array($status, $allowedStatuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
        $message = "Order #$orderId updated successfully.";
    } else {
        $message = "Invalid status selected.";
    }
}

// Fetch orders after possible update
$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <style>
      body {
        margin: 0;
        font-family: Arial, sans-serif;
     
      }
      .table-container {
        background: #fff;
        padding: 20px;
        margin: 80px auto 30px auto; /* navbar से gap देने के लिए margin-top */
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
        width: fit-content;
      }
      h2 {
        margin-bottom: 15px;
        color: #333;
      }
      table {
        border-collapse: collapse;
        width: 1000px;
        margin: auto;
      }
      th, td {
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
      .message { 
        margin: 15px 0; 
        padding: 10px; 
        background-color: #d4edda; 
        color: #155724; 
        border-radius: 5px; 
      }
    </style>
</head>
<body>
  <div class="table-container">
    <h2>Manage Orders</h2>

    <?php if (!empty($message)) : ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Order Details</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Update Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order) : ?>
            <tr>
                <td><?= htmlspecialchars($order['id']) ?></td>
                <td><?= htmlspecialchars($order['name']) ?></td>
                <td><?= htmlspecialchars($order['email']) ?></td>
                <td><?= htmlspecialchars($order['phone']) ?></td>
                <td><?= htmlspecialchars($order['address']) ?></td>
                <td><?= htmlspecialchars($order['order_details']) ?></td>
                <td><?= htmlspecialchars($order['total_price']) ?></td>
                <td><?= htmlspecialchars($order['status']) ?></td>
                <td><?= htmlspecialchars($order['created_at']) ?></td>
                <td>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <select name="status">
                            <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
  </div>
  <?php include '../include/footer.php'; ?>

</body>
</html>
