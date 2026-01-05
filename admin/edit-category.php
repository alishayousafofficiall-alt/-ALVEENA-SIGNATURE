<?php
session_start();
require_once '../db/conn.php';
require_once '../header.php';

// only admin allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Initialize to avoid "undefined variable" warnings
$errors = [];
$successMsg = '';

// Validate id
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("Invalid category ID.");
}

// Fetch category
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    die("Category not found.");
}

// Fetch parent categories (exclude current category)
$parentsStmt = $pdo->prepare("SELECT id, name FROM categories WHERE id != ? ORDER BY name ASC");
$parentsStmt->execute([$id]);
$parents = $parentsStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle POST (update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $parent_id = $_POST['parent_id'] ?? '';
    $parent_id = ($parent_id === '') ? null : intval($parent_id);
    // Prevent selecting self as parent
    if ($parent_id === $id) {
        $parent_id = null;
    }
    $description = trim($_POST['description'] ?? '');
    $sizes = trim($_POST['sizes'] ?? '');
    $imagePath = $category['image']; // keep old image by default

    // File upload dir (absolute path)
    $uploadDir = __DIR__ . '/../uploads/categories/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // If new image uploaded, process it
    if (!empty($_FILES['image']['name'])) {
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['image']['name']));
        $targetFile = $uploadDir . $safeName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // delete old image file if exists (safe unlink)
            if (!empty($category['image'])) {
                $oldPath = __DIR__ . '/../' . $category['image'];
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $imagePath = 'uploads/categories/' . $safeName;
        } else {
            $errors[] = "Failed to upload new image.";
        }
    }

    // Validation
    if ($name === '') {
        $errors[] = "Category name is required.";
    }

    // Update DB if no errors
    if (empty($errors)) {
        $update = $pdo->prepare("UPDATE categories SET name = ?, parent_id = ?, image = ?, description = ?, sizes = ? WHERE id = ?");
        $update->execute([$name, $parent_id, $imagePath, $description, $sizes, $id]);

        $successMsg = "Category updated successfully.";

        // Refresh category data
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Edit Category</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <style>
        .card { max-width:720px; margin:30px auto; background:#fff; border-radius:10px; padding:24px; box-shadow:0 8px 24px rgba(12,38,63,0.08); }
        h1 { margin:0 0 18px; color:#17394a; text-align:center; }
        label { display:block; font-weight:600; margin-bottom:6px; color:#233645; }
        input[type="text"], textarea, select { width:100%; padding:10px 12px; border:1px solid #d8e1e8; border-radius:8px; font-size:14px; margin-bottom:14px; background:#fbfeff; }
        textarea { min-height:110px; resize:vertical; }
        .row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .center { text-align:center; }
        .img-preview { width:140px; height:110px; object-fit:cover; border-radius:8px; border:1px solid #e1e7eb; display:block; margin:8px auto; }
        .actions { display:flex; gap:12px; justify-content:center; margin-top:18px; }
        .btn { padding:10px 18px; border-radius:8px; border:none; cursor:pointer; font-weight:700; }
        .btn-primary { background:#0c9bd7; color:#fff; }
        .btn-primary:hover { background:#0978a8; }
        .btn-secondary { background:#f1f5f8; color:#16323a; border:1px solid #e2e9ef; }
        .msg-success { background:#e6fbf0; color:#0a7a3b; padding:10px; border-radius:8px; margin-bottom:12px; border:1px solid #c8f0d5; text-align:center; }
        .msg-error { background:#fff0f0; color:#a33; padding:10px; border-radius:8px; margin-bottom:12px; border:1px solid #f1c6c6; }
        @media (max-width:600px){ .row { grid-template-columns:1fr; } .img-preview{ width:120px; height:90px; } }
    </style>
</head>
<body>
    <div class="card">
        <h1>Edit Category</h1>

        <!-- messages -->
        <?php if (!empty($successMsg)): ?>
            <div class="msg-success"><?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="msg-error">
                <ul style="margin:0; padding-left:18px;">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" novalidate>
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="<?= htmlspecialchars($category['name'] ?? '') ?>" required>

            <label for="parent_id">Parent Category</label>
            <select id="parent_id" name="parent_id">
                <option value="">-- None --</option>
                <?php foreach ($parents as $p): ?>
                    <option value="<?= (int)$p['id'] ?>" <?= ($category['parent_id'] == $p['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="description">Description</label>
            <textarea id="description" name="description"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>

            <label for="sizes">Sizes (comma separated)</label>
            <input id="sizes" type="text" name="sizes" value="<?= htmlspecialchars($category['sizes'] ?? '') ?>">

            <label>Current Image</label>
            <div class="center">
                <?php if (!empty($category['image'])): ?>
                    <img class="img-preview" src="../<?= htmlspecialchars($category['image']) ?>" alt="Category image">
                <?php else: ?>
                    <div style="color:#777; padding:10px 0;">No image</div>
                <?php endif; ?>
            </div>

            <label for="image">Replace Image (optional)</label>
            <input id="image" type="file" name="image" accept="image/*">

            <div class="actions">
                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="manage-categories.php" class="btn btn-secondary" style="text-decoration:none; display:inline-flex; align-items:center;">Cancel</a>
            </div>
        </form>
    </div>
    <?php include '../include/footer.php'; ?>

</body>
</html>
