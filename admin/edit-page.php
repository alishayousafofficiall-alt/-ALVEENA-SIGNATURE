<?php
session_start();
require_once '../db/conn.php';
require_once '../header.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Edit ya Add mode ka check
$id = intval($_GET['id'] ?? 0);
$page = [
    'title' => '',
    'slug' => '',
    'content' => '',
    'image1' => '',
    'image2' => ''
];

if ($id > 0) {
    // Edit mode - record fetch karo
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->execute([$id]);
    $page = $stmt->fetch(PDO::FETCH_ASSOC) ?? $page;

    if (!$page) {
        echo "Page not found.";
        exit();
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $content = $_POST['content'] ?? '';

    $uploadDir = '../uploads/pages/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Image1
    $image1 = $page['image1'];
    if (!empty($_FILES['image1']['name'])) {
        $img1Name = basename($_FILES['image1']['name']);
        $targetFile1 = $uploadDir . time() . '_img1_' . $img1Name;
        if (move_uploaded_file($_FILES['image1']['tmp_name'], $targetFile1)) {
            if ($id && $image1 && file_exists('../' . $image1)) {
                unlink('../' . $image1);
            }
            $image1 = 'uploads/pages/' . basename($targetFile1);
        } else {
            $errors[] = "Failed to upload Image 1.";
        }
    }

    // Image2
    $image2 = $page['image2'];
    if (!empty($_FILES['image2']['name'])) {
        $img2Name = basename($_FILES['image2']['name']);
        $targetFile2 = $uploadDir . time() . '_img2_' . $img2Name;
        if (move_uploaded_file($_FILES['image2']['tmp_name'], $targetFile2)) {
            if ($id && $image2 && file_exists('../' . $image2)) {
                unlink('../' . $image2);
            }
            $image2 = 'uploads/pages/' . basename($targetFile2);
        } else {
            $errors[] = "Failed to upload Image 2.";
        }
    }

    if (empty($errors)) {
        if ($id > 0) {
            // Update query
            $update = $pdo->prepare("UPDATE pages SET title = ?, slug = ?, content = ?, image1 = ?, image2 = ? WHERE id = ?");
            $update->execute([$title, $slug, $content, $image1, $image2, $id]);
            header("Location: manage-pages.php?msg=Page updated successfully");
        } else {
            // Insert query
            $insert = $pdo->prepare("INSERT INTO pages (title, slug, content, image1, image2) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([$title, $slug, $content, $image1, $image2]);
            header("Location: manage-pages.php?msg=Page added successfully");
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $id ? 'Edit' : 'Add' ?> Page - Admin</title>
</head>
<body>
    <h2><?= $id ? 'Edit Page: ' . htmlspecialchars($page['title']) : 'Add New Page' ?></h2>

    <?php if (!empty($errors)): ?>
        <div style="color:red;">
            <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Title:<br>
            <input type="text" name="title" value="<?= htmlspecialchars($page['title']) ?>" required>
        </label><br><br>

        <label>Slug:<br>
            <input type="text" name="slug" value="<?= htmlspecialchars($page['slug']) ?>" required>
        </label><br><br>

        <label>Content:<br>
            <textarea name="content" rows="10" cols="50" required><?= htmlspecialchars($page['content']) ?></textarea>
        </label><br><br>

        <?php if ($id && $page['image1'] && file_exists('../' . $page['image1'])): ?>
            <label>Current Image 1:<br>
                <img src="../<?= htmlspecialchars($page['image1']) ?>" alt="Image1" style="max-width:200px;"><br>
            </label><br>
        <?php endif; ?>
        <label>Upload Image 1:<br>
            <input type="file" name="image1" accept="image/*">
        </label><br><br>

        <?php if ($id && $page['image2'] && file_exists('../' . $page['image2'])): ?>
            <label>Current Image 2:<br>
                <img src="../<?= htmlspecialchars($page['image2']) ?>" alt="Image2" style="max-width:200px;"><br>
            </label><br>
        <?php endif; ?>
        <label>Upload Image 2:<br>
            <input type="file" name="image2" accept="image/*">
        </label><br><br>

        <button type="submit"><?= $id ? 'Update Page' : 'Add Page' ?></button>
    </form>
    <?php include '../include/footer.php'; ?>

</body>
</html>
