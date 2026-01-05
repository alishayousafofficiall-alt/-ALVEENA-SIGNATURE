<?php
session_start();
require_once '../db/conn.php';
require_once '../header.php';
// Agar login na ho aur admin role na ho
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

// Product ID le rahe hain
if (!isset($_GET['id'])) {
  die("Product ID not provided!");
}
$id = $_GET['id'];

// Product fetch
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  die("Product not found!");
}

// Categories fetch
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Sections fetch
$sections = $pdo->query("SELECT id, title FROM products_sections ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);

// Update product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = $_POST['title'] ?? '';
  $description = $_POST['description'] ?? '';
  $fabric = $_POST['fabric'] ?? '';
  $color = $_POST['color'] ?? '';
  $price = $_POST['price'] ?? '';
  $sku = $_POST['sku'] ?? '';
  $category_id = $_POST['category_id'] ?? null;
  $section_id = $_POST['section_id'] ?? null;

  // Agar image update hui hai
  $image = $product['image'];
  if (!empty($_FILES['image']['name'])) {
    $targetDir = "../uploads/";
    $image = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $image;
    move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
  }

  $sql = "UPDATE products SET 
                title=?, description=?, fabric=?, color=?, price=?, sku=?, image=?, category_id=?, section_id=? 
            WHERE id=?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$title, $description, $fabric, $color, $price, $sku, $image, $category_id, $section_id, $id]);

  header("Location: products.php?success=1");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Edit Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #6a0dad;
      /* Purple background */
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .edit-form {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
      width: 600px;
    }

    .edit-form h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #6a0dad;
    }
  </style>
</head>

<body>
  <div class="edit-form">
    <h2>Edit Product</h2>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($product['title']) ?>"
          required>
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"
          required><?= htmlspecialchars($product['description']) ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Fabric</label>
        <input type="text" name="fabric" class="form-control" value="<?= htmlspecialchars($product['fabric']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Color</label>
        <input type="text" name="color" class="form-control" value="<?= htmlspecialchars($product['color']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" name="price" class="form-control"
          value="<?= htmlspecialchars($product['price']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">SKU</label>
        <input type="text" name="sku" class="form-control" value="<?= htmlspecialchars($product['sku']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
          <option value="">-- Select Category --</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Section</label>
        <select name="section_id" class="form-select">
          <option value="">-- Select Section --</option>
          <?php foreach ($sections as $sec): ?>
            <option value="<?= $sec['id'] ?>" <?= $sec['id'] == $product['section_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($sec['title']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Image</label><br>
        <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" width="120" class="mb-2">
        <input type="file" name="image" class="form-control">
      </div>
      <button type="submit" class="btn btn-success w-100">Update Product</button>
    </form>
  </div>
  <?php include '../include/footer.php'; ?>
</body>

</html>

<style>
  body {
    font-family: Arial, sans-serif;
    background: #F3E8FF;
    /* Light Purple */
    margin: 0;
    padding: 0;
  }

  .content-wrapper {
    min-height: calc(100vh - 100px);
    /* Header + Footer ke liye jagah chhod do */
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 0;
  }

  .form-container {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
    width: 450px;
  }

  .form-container h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #6a11cb;
  }

  .form-container label {
    display: block;
    margin: 10px 0 5px;
    font-weight: bold;
  }

  .form-container input,
  .form-container textarea,
  .form-container select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 15px;
  }

  .form-container button {
    width: 100%;
    padding: 12px;
    background: #6a11cb;
    border: none;
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
  }

  .form-container button:hover {
    background: #2575fc;
  }

  .preview {
    text-align: center;
    margin-bottom: 15px;
  }

  .preview img {
    max-width: 100px;
    border-radius: 8px;
  }
</style>