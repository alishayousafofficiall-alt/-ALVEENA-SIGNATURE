<?php
session_start();
require_once '../db/conn.php';
require_once '../header.php';

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle banner uploads
$successMsg = $errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['admin_banner']) && $_FILES['admin_banner']['tmp_name']) {
        $fileName = time() . 'admin' . basename($_FILES['admin_banner']['name']);
        $target = '../images/' . $fileName;
        if (move_uploaded_file($_FILES['admin_banner']['tmp_name'], $target)) {
            $stmt = $pdo->prepare("UPDATE dashboard_banner SET image_path = ? WHERE type = 'admin'");
            $stmt->execute([$target]);
            $successMsg .= "Admin banner updated successfully!<br>";
        } else {
            $errorMsg .= "Failed to upload admin banner.<br>";
        }
    }

    if (isset($_FILES['user_banner']) && $_FILES['user_banner']['tmp_name']) {
        $fileName = time() . 'user' . basename($_FILES['user_banner']['name']);
        $target = '../images/' . $fileName;
        if (move_uploaded_file($_FILES['user_banner']['tmp_name'], $target)) {
            $stmt = $pdo->prepare("UPDATE dashboard_banner SET image_path = ? WHERE type = 'user'");
            $stmt->execute([$target]);
            $successMsg .= "User banner updated successfully!";
        } else {
            $errorMsg .= "Failed to upload user banner.";
        }
    }
}

// Fetch current banners
$adminBannerStmt = $pdo->prepare("SELECT image_path FROM dashboard_banner WHERE type='admin' LIMIT 1");
$adminBannerStmt->execute();
$adminBanner = $adminBannerStmt->fetchColumn();

$userBannerStmt = $pdo->prepare("SELECT image_path FROM dashboard_banner WHERE type='user' LIMIT 1");
$userBannerStmt->execute();
$userBanner = $userBannerStmt->fetchColumn();
?>

<style>   body {
    margin: 0;
    padding-top: 60px;
    background-color:#F3E8FF; /* This will apply page background */
    font-family: Arial, sans-serif;
}

h2 {
    text-align:center;
    margin-bottom:20px;
    color: #333;
}

.current-banner {
    margin-bottom: 25px;
    width: 100%;
    display: flex;
    justify-content: center; /* center the image */
}

.current-banner img {
    max-width: 80%;   /* responsive width */
    height: auto;
    border-radius: 10px;
    display: block;
}

input[type="file"] {
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ced4da;
    margin-bottom: 15px;
    width: 300px;      /* fixed width for inputs */
    background: #fff;
}

button {
    padding: 10px 20px;
    background:#6f42c1;
    border: none;
    border-radius: 6px;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background:#6f42c1;
}

.alert {
    border-radius: 8px;
    padding: 10px 15px;
    font-weight: 500;
    margin-bottom: 15px;
    width: 300px;
    text-align: center;
    box-sizing: border-box;
    background: #e0f7fa;
    color:#6f42c1;
}

#site-footer {
    background: none; 
    color: #0c9bd7; 
    text-align: center;
    padding: 10px 0;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    margin-top: 30px; 
}

#site-footer a {
    color: #0c9bd7;
    text-decoration: none;
    margin: 0 8px;
    transition: 0.3s;
}

#site-footer a:hover {
    text-decoration: underline;
}
</style>

<h2>Change Dashboard Banner</h2>

<!-- Current Banner Preview -->
<div class="current-banner">
    <img src="<?= htmlspecialchars($adminBanner) ?>?t=<?= time() ?>" alt="Admin Banner">
</div>

<div class="current-banner">
    <img src="<?= htmlspecialchars($userBanner) ?>?t=<?= time() ?>" alt="User Banner">
</div>

<!-- Success/Error Messages -->
<?php if (!empty($successMsg)): ?>
    <div class="alert alert-success"><?= $successMsg ?></div>
<?php elseif (!empty($errorMsg)): ?>
    <div class="alert alert-danger"><?= $errorMsg ?></div>
<?php endif; ?>

<!-- Upload Form -->
<form method="POST" enctype="multipart/form-data" style="display:flex; flex-direction:column; align-items:center;">
    <input type="file" name="admin_banner">
    <input type="file" name="user_banner">
    <button type="submit">Upload</button>
</form>

  <?php include '../include/footer.php'; ?>
