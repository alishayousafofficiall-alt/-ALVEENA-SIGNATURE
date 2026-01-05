<?php
session_start();
require_once '../db/conn.php';
require_once '../header.php';

// only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// initialize
$errors = [];
$success = '';
$sections = [];

// handle POST (add section)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_key = trim($_POST['section_key'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sizes = trim($_POST['sizes'] ?? '');
    $imagePath = null;

    // upload directory (absolute path)
    $uploadDir = __DIR__ . '/../imagess/sections/';

    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        $errors[] = "Unable to create upload directory.";
    }

    // file upload
    if (!empty($_FILES['image']['name'])) {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $origName = basename($_FILES['image']['name']);
            $safeName = time() . '_' . bin2hex(random_bytes(6)) . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $origName);
            $targetFile = $uploadDir . $safeName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // sirf filename save karenge DB me (same jaise aapke records me hai: 29.jpg, wp2.jpg etc.)
                $imagePath = $safeName;
            } else {
                $errors[] = "Failed to move uploaded file.";
            }
        } else {
            $errors[] = "Error uploading image (error code: " . ($_FILES['image']['error'] ?? 'unknown') . ").";
        }
    }

    // validation
    if ($section_key === '')
        $errors[] = "Section key is required.";
    if ($title === '')
        $errors[] = "Title is required.";

    // insert
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products_sections (section_key, title, description, image, sizes, created_at) 
                                   VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$section_key, $title, $description, $imagePath, $sizes]);
            $success = "Section added successfully.";
            $_POST = []; // reset form
        } catch (PDOException $e) {
            $errors[] = "Database error: could not save section.";
        }
    }
}

// fetch existing sections
try {
    $sections = $pdo->query("SELECT * FROM products_sections ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    if (!is_array($sections))
        $sections = [];
} catch (PDOException $e) {
    $sections = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Manage Sections - Admin</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <style>
        :root {
            --bg: #F3E8FF;
            --card: #ffffff;
            --accent: #6f42c1;
            --text: #102a43;
            --text-secondary: #18303b;
            --success: #045d3b;
            --error: #9b2c2c;
        }

        body {
            font-family: Inter, Arial, sans-serif;
            margin: 0;
            padding: 80px 24px 24px;
            background: var(--bg);
            color: var(--text);
        }

        .wrap {
            max-width: 1100px;
            margin: 0 auto;
        }

        h1,
        h2 {
            margin-bottom: 16px;
            text-align: center;
            color: var(--text);
        }

        .card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .08);
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 14px;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .actions {
            margin-top: 8px;
        }

        button.primary {
            background: var(--accent);
            color: #fff;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
        }

        .msg-success {
            background: #d1fae5;
            color: var(--success);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .msg-error {
            background: #fee2e2;
            color: var(--error);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: var(--accent);
            color: #fff;
        }

        img.thumb {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
        }
    </style>
</head>

<body>
    <div class="wrap">

        <div class="card">
            <h2>Add Section</h2>
            <?php if (!empty($errors)): ?>
                <div class="msg-error">
                    <ul style="margin:0; padding-left:18px;">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif ($success): ?>
                <div class="msg-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div>
                        <label for="section_key">Section Key</label>
                        <input type="text" id="section_key" name="section_key"
                            value="<?= htmlspecialchars($_POST['section_key'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title"
                            value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                    </div>
                </div>
                <label for="description">Description</label>
                <textarea id="description"
                    name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                <label for="sizes">Sizes (comma separated)</label>
                <input type="text" id="sizes" name="sizes" value="<?= htmlspecialchars($_POST['sizes'] ?? '') ?>">
                <label for="image">Image</label>
                <input type="file" id="image" name="image" accept="image/*">
                <div class="actions"><button type="submit" class="primary">Add Section</button></div>
            </form>
        </div>

        <div class="card">
            <h2>Existing Sections (<?= count($sections) ?>)</h2>
            <?php if (empty($sections)): ?>
                <p>No sections found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Section Key</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Sizes</th>
                            <th>Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sections as $sec): ?>
                            <tr>
                                <td><?= htmlspecialchars($sec['id']) ?></td>
                                <td><?= htmlspecialchars($sec['section_key']) ?></td>
                                <td><?= htmlspecialchars($sec['title']) ?></td>
                                <td><?= nl2br(htmlspecialchars($sec['description'])) ?></td>
                                <td><?= htmlspecialchars($sec['sizes']) ?></td>
                                <td>
                                    <?php if (!empty($sec['image'])): ?>
                                        <img src="../images/sections/<?= htmlspecialchars($sec['image']) ?>" class="thumb"
                                            alt="Image">
                                    <?php else: ?>
                                        <span>No Image</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>
    <?php include '../include/footer.php'; ?>
</body>

</html>