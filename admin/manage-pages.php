<?php
session_start();
require_once '../db/conn.php';
require_once '../header.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Default blank page data
$page = [
    'title' => '',
    'slug' => '',
    'content' => '',
    'image1' => '',
    'image2' => ''
];

// Edit mode check
$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->execute([$id]);
    $page = $stmt->fetch(PDO::FETCH_ASSOC) ?? $page;
}

// Handle form submission
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
            $update = $pdo->prepare("UPDATE pages SET title = ?, slug = ?, content = ?, image1 = ?, image2 = ? WHERE id = ?");
            $update->execute([$title, $slug, $content, $image1, $image2, $id]);
            header("Location: manage-pages.php?msg=Page updated successfully");
        } else {
            $insert = $pdo->prepare("INSERT INTO pages (title, slug, content, image1, image2) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([$title, $slug, $content, $image1, $image2]);
            header("Location: manage-pages.php?msg=Page added successfully");
        }
        exit();
    }
}

// Fetch all pages for listing
$pages = $pdo->query("SELECT * FROM pages ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Pages - Admin</title>
    <style>
        /* Push content down from navbar */
        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
        }

        h2, h3 {
            color: #222;
            margin-bottom: 20px;
            font-weight: 600;
        }
/* Form container */
form {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    background-color: #f9f5ff;
    border: 1px solid #6f42c1;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    font-family: Arial, sans-serif;
}

/* Form heading */
form h2 {
    color: #6f42c1;
    text-align: center;
    margin-bottom: 15px;
}

/* Input fields */
form input[type="text"],
form input[type="email"],
form input[type="number"],
form textarea,
form select {
    width: 100%;
    padding: 10px 12px;
    margin: 8px 0 16px 0;
    border: 1px solid #6f42c1;
    border-radius: 6px;
    box-sizing: border-box;
    font-size: 14px;
}

/* Textarea height */
form textarea {
    min-height: 100px;
    resize: vertical;
}

/* Submit button */
form button[type="submit"] {
    background-color: #6f42c1;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    transition: 0.3s;
}

/* Button hover */
form button[type="submit"]:hover {
    background-color: #532a91;
}

/* Labels */
form label {
    font-weight: bold;
    color: #4b2a7b;
}

/* Small notes or hints */
form small {
    display: block;
    color: #6f42c1;
    font-size: 12px;
    margin-top: -12px;
    margin-bottom: 8px;
}


        label {
            font-weight: 500;
            display: block;
            margin-bottom: 6px;
        }

        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 18px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            transition: border 0.3s ease;
        }

        input[type="text"]:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        textarea {
            resize: vertical;
        }

        button {
            background: #007bff;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #0056b3;
        }

        .msg {
            padding: 10px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .error {
            padding: 10px;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background: #F3E8FF;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #6f42c1;
            color: white;
            font-weight: 500;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        img {
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ddd;
            max-height: 120px;
        }

        /* Footer style */
        footer {
            margin-top: 50px;
            padding: 20px;
            background: #f1f1f1;
            text-align: left; /* Ensure left alignment */
            border-top: 1px solid #ccc;
        }
    </style>
</head>
<body>

<div class="container">

    <h2><?= $id ? 'Edit Page: ' . htmlspecialchars($page['title']) : 'Add New Page' ?></h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['msg'])): ?>
        <div class="msg"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <!-- Add/Edit Form -->
    <form method="post" enctype="multipart/form-data">
        <label>Title:
            <input type="text" name="title" value="<?= htmlspecialchars($page['title']) ?>" required>
        </label>

        <label>Slug:
            <input type="text" name="slug" value="<?= htmlspecialchars($page['slug']) ?>" required>
        </label>

        <label>Content:
            <textarea name="content" rows="5" required><?= htmlspecialchars($page['content']) ?></textarea>
        </label>

        <?php if ($id && $page['image1'] && file_exists('../' . $page['image1'])): ?>
            <label>Current Image 1:
                <img src="../<?= htmlspecialchars($page['image1']) ?>" alt="">
            </label>
        <?php endif; ?>
        <label>Upload Image 1:
            <input type="file" name="image1" accept="image/*">
        </label>

        <?php if ($id && $page['image2'] && file_exists('../' . $page['image2'])): ?>
            <label>Current Image 2:
                <img src="../<?= htmlspecialchars($page['image2']) ?>" alt="">
            </label>
        <?php endif; ?>
        <label>Upload Image 2:
            <input type="file" name="image2" accept="image/*">
        </label>

        <button type="submit"><?= $id ? 'Update Page' : 'Add Page' ?></button>
    </form>

    <h3>Existing Pages</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Slug</th>
            <th>Title</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($pages as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['slug']) ?></td>
            <td><?= htmlspecialchars($p['title']) ?></td>
            <td>
                <a href="manage-pages.php?id=<?= $p['id'] ?>">Edit</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

</div>

<!-- Include footer OUTSIDE container -->
<?php include '../include/footer.php'; ?>

</body>
</html>
