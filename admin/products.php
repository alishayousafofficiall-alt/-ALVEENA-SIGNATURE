<?php
session_start();
require_once '../db/conn.php';
require_once '../header.php';

if ($_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
  <title>Admin - Manage Products</title>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th,
    td {
      border: 1px solid #6f42c1;
      padding: 8px;
      text-align: center;
      vertical-align: middle;
    }

    th {
      background-color: #6f42c1;
      color: white;
    }

    a.button {
      background-color: #6f42c1;
      color: white;
      padding: 6px 12px;
      border-radius: 5px;
      text-decoration: none;
      margin: 0 4px;
      display: inline-block;
    }

    a.button:hover {
      background-color: #574b90;
    }

    img {
      max-width: 80px;
      height: auto;
    }

    td.desc {
      max-width: 200px;
      word-wrap: break-word;
      text-align: left;
    }
  </style>
</head>

<body>
  <h1>Admin Panel - Manage Products</h1>
  <a href="add_product.php" class="button">Add New Product</a>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Title</th>
        <th>Price</th>
        <th>SKU</th>
        <th>Description</th>
        <th>Fabric</th>
        <th>Color</th>
        <th>Category ID</th>
        <th>Section ID</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['id']) ?></td>
          <td><img src="../images/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>"></td>
          <td><?= htmlspecialchars($p['title']) ?></td>
          <td>Rs. <?= number_format($p['price'], 2) ?></td>
          <td><?= htmlspecialchars($p['sku']) ?></td>
          <td class="desc"><?= htmlspecialchars($p['description']) ?></td>
          <td><?= htmlspecialchars($p['fabric']) ?></td>
          <td><?= htmlspecialchars($p['color']) ?></td>
          <td><?= htmlspecialchars($p['category_id']) ?></td>
          <td><?= htmlspecialchars($p['section_id']) ?></td>
          <td>
            <a href="edit_product.php?id=<?= $p['id'] ?>" class="button">Edit</a>
            <a href="delete_product.php?id=<?= $p['id'] ?>" class="button"
              onclick="return confirm('Are you sure?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php include '../include/footer.php'; ?>
</body>

</html>