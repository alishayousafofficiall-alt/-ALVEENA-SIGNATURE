<?php

session_start();
require_once '../db/conn.php';
 require_once '../header.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}


$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $sku = trim($_POST['sku']);
    $details = trim($_POST['details']);
    $color = trim($_POST['color']);
    $fabric = trim($_POST['fabric']);

    // Image upload handling
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imgTmpPath = $_FILES['image']['tmp_name'];
        $imgName = basename($_FILES['image']['name']);
        $imgExtension = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imgExtension, $allowed)) {
            $errors[] = "Invalid image file type.";
        } else {
            $uploadDir = '../images/';
            $newImageName = uniqid('prod_', true) . '.' . $imgExtension;
            $destPath = $uploadDir . $newImageName;

            if (!move_uploaded_file($imgTmpPath, $destPath)) {
                $errors[] = "Failed to upload image.";
            }
        }
    } else {
        $errors[] = "Image file is required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO products (title, description, price, image, category_id, sku, details, color, fabric) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $price, $newImageName, $category_id, $sku, $details, $color, $fabric]);
        header("Location: products.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Add New Product</title>
<style>

.page-content {
    margin-top: 60px; /* heading neeche move ho jayegi */
}


  * {
    box-sizing: border-box;
  }
  
  h1 {
    text-align: center;
    color:#6f42c1;
    margin-bottom: 25px;
    font-weight: 700;
    letter-spacing: 1px;
  }
  form {
    max-width: 700px;
    background: #fff;
    margin: 0 auto;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    transition: box-shadow 0.3s ease;
    display: grid;
    grid-template-columns: 150px 1fr;
    grid-gap: 15px 30px;
    align-items: center;
  }
  form:hover {
    box-shadow: 0 12px 28px rgba(0,0,0,0.15);
  }
  label {
    font-weight: 600;
    color:black;
    user-select: none;
    justify-self: end;
  }
  input[type="text"],
  input[type="number"],
  input[type="file"],
  textarea {
    padding: 10px 14px;
    border: 1.5px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    font-family: inherit;
    resize: vertical;
    width: 100%;
  }
  input[type="text"]:focus,
  input[type="number"]:focus,
  input[type="file"]:focus,
  textarea:focus {
    outline: none;
    border-color:#6f42c1;
    box-shadow: #6f42c1;
    background: #f0f8ff;
  }
  textarea {
    min-height: 70px;
  }
  button {
    grid-column: 1 / span 2;
    margin-top: 20px;
    background-color:#6f42c1;
    color: white;
    border: none;
    padding: 14px 0;
    font-size: 16px;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 700;
    letter-spacing: 1px;
    transition: background-color 0.3s ease;
    box-shadow: #6f42c1;
  }
  button:hover {
    background-color:#6f42c1;
    box-shadow:#6f42c1;
  }
  ul.errors {
    max-width: 700px;
    margin: 0 auto 25px auto;
    padding: 12px 20px;
    background: #ffe6e6;
    border: 1.5px solid #ff4d4d;
    border-radius: 8px;
    color: #b30000;
    font-weight: 600;
    list-style-type: disc;
  }
  p.back-link {
    max-width: 700px;
    margin: 20px auto 0 auto;
    text-align: center;
    font-size: 15px;
    user-select: none;
  }
  p.back-link a {
    color:#6f42c1;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
  }
  p.back-link a:hover {
    text-decoration: underline;
    color:#6f42c1;
  }
  /* Image Preview Styles */
  #imgPreview {
    grid-column: 2 / 3;
    max-width: 250px;
    max-height: 250px;
    border-radius: 12px;
    box-shadow: 0 0 12px rgba(0,0,0,0.12);
    display: none;
    object-fit: contain;
    user-select: none;
    justify-self: start;
    margin-top: 10px;
  }
</style>
</head>
<body>
  <div class="main-content">
<div class="page-content">
    <h1>Add New Product</h1>
    <form method="post" enctype="multipart/form-data" novalidate>
  <label for="title">Title</label>
  <input id="title" type="text" name="title" required>

  <label for="description">Description</label>
  <textarea id="description" name="description" rows="3"></textarea>

  <label for="price">Price</label>
  <input id="price" type="number" step="0.01" name="price" required>

  <label for="category_id">Category ID</label>
  <input id="category_id" type="number" name="category_id" required>

  <label for="sku">SKU</label>
  <input id="sku" type="text" name="sku">

  <label for="details">Details</label>
  <textarea id="details" name="details" rows="3"></textarea>

  <label for="color">Color</label>
  <input id="color" type="text" name="color">

  <label for="fabric">Fabric</label>
  <input id="fabric" type="text" name="fabric">

  <label for="image">Image</label>
  <input id="image" type="file" name="image" required accept="image/*" onchange="previewImage(event)">

  <img id="imgPreview" src="#" alt="Image Preview" />

  <button type="submit">Add Product</button>
</form>
</div>
<p class="back-link"><a href="products.php">← Back to Products</a></p>
</div>
<script>
  function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('imgPreview');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      }
      reader.readAsDataURL(input.files[0]);
    } else {
      preview.src = '#';
      preview.style.display = 'none';
    }
  }
</script>
<?php include '../include/footer.php'; ?>

</body>
</html>
