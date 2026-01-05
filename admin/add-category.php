<?php
session_start();
require_once '../db/conn.php';
require_once '../header.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch parent categories for dropdown
$parents = $pdo->query("SELECT id, name FROM categories WHERE parent_id IS NULL")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
    $description = trim($_POST['description']);
    $sizes = trim($_POST['sizes']);
    $imagePath = null;
    $uploadDir = '../uploads/categories/';

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    if (!empty($_FILES['image']['name'])) {
        $imgName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $imgName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = 'uploads/categories/' . $imgName;
        } else {
            $errors[] = "Failed to upload image.";
        }
    }

    if (empty($name)) $errors[] = "Category name is required.";

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, parent_id, image, description, sizes) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $parent_id, $imagePath, $description, $sizes]);
        $successMsg = "Category added successfully.";
    }
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $delId = intval($_GET['delete_id']);
    $stmtDel = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmtDel->execute([$delId]);
    header("Location: manage-categories.php");
    exit();
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories - Admin</title>
    <style>
        body {
    margin: 0;
    padding-top: 60px;
    background-color: #F3E8FF; /* This will apply page background */
    font-family: Arial, sans-serif;
}
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        form {
            background: white;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 100%;
            max-width: 600px;
            margin-bottom: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #F3E8FF;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #6f42c1;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color:#6f42c1;
        }
        img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            padding: 6px 12px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .btn-edit {
    background-color: #007bff;
    color: white;
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    text-decoration: none;
}
.btn-edit:hover {
    background-color: #0056b3;
}

        .success { color: green; margin-bottom: 15px; }
        .error { color: red; margin-bottom: 15px; }
    </style>
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this category?')) {
                window.location.href = 'manage-categories.php?delete_id=' + id;
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h1>Manage Categories</h1>

    <?php if ($successMsg): ?>
        <div class="success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Add Category Form -->
    <form method="post" enctype="multipart/form-data">
        <h2>Add New Category</h2>
        <input type="hidden" name="add_category" value="1">

        <label>Name:<br>
            <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </label><br><br>

        <label>Parent Category (optional):<br>
            <select name="parent_id">
                <option value="">-- None --</option>
                <?php foreach ($parents as $parent): ?>
                    <option value="<?= $parent['id'] ?>" <?= (isset($_POST['parent_id']) && $_POST['parent_id'] == $parent['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($parent['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <label>Description:<br>
            <textarea name="description" rows="4" cols="50"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </label><br><br>

        <label>Sizes (comma separated):<br>
            <input type="text" name="sizes" value="<?= htmlspecialchars($_POST['sizes'] ?? '') ?>">
        </label><br><br>

        <label>Image:<br>
            <input type="file" name="image" accept="image/*">
        </label><br><br>

        <button type="submit">Add Category</button>
    </form>

    <!-- Categories List -->
    <h2>Existing Categories</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Parent ID</th>
                <th>Description</th>
                <th>Sizes</th>
                <th>Image</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td><?= $cat['parent_id'] ?: 'None' ?></td>
                    <td><?= htmlspecialchars($cat['description']) ?></td>
                    <td><?= htmlspecialchars($cat['sizes']) ?></td>
                    <td>
                        <?php if ($cat['image']): ?>
                          <img src="../images/<?= htmlspecialchars($cat['image']) ?>" alt="Category Image" width="80" height="80">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                   <td>
    <a href="edit-category.php?id=<?= $cat['id'] ?>" class="btn-edit">Edit</a>
    <button class="btn-delete" onclick="confirmDelete(<?= $cat['id'] ?>)">Delete</button>
</td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../include/footer.php'; ?>

</body>
</html>
