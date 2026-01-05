<?php
session_start();
require_once '../db/conn.php';  // Adjust path
 require_once '../header.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$error = '';
$success = '';
$userId = $_SESSION['user_id'];

// Fetch current user info including profile_image
$stmt = $pdo->prepare("SELECT name, email, profile_image FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$name || !$email) {
        $error = "Name and Email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email is changing and is unique
        if ($email !== $user['email']) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                $error = "Email is already taken.";
            }
        }
    }

    if (!$error && ($new_password || $confirm_password)) {
        if (!$current_password) {
            $error = "Enter current password to change password.";
        } else {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row || !password_verify($current_password, $row['password'])) {
                $error = "Current password is incorrect.";
            } elseif ($new_password !== $confirm_password) {
                $error = "New password and confirmation do not match.";
            }
        }
    }

    // Image upload handling
    if (!$error && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_image']['tmp_name'];
        $fileName = $_FILES['profile_image']['name'];
        $fileSize = $_FILES['profile_image']['size'];
        $fileType = $_FILES['profile_image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            // Generate new unique filename
            $newFileName = $userId . '_' . time() . '.' . $fileExtension;

            $uploadFileDir = '../uploads/profile_images/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $destPath = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $destPath)) {
                // Update DB profile_image
                $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                $stmt->execute([$newFileName, $userId]);
                $user['profile_image'] = $newFileName; // update for immediate display
            } else {
                $error = "There was an error moving the uploaded file.";
            }
        } else {
            $error = "Upload failed. Allowed file types: " . implode(', ', $allowedExtensions);
        }
    }

    if (!$error) {
        // Update name and email
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $userId]);

        // Update password if needed
        if ($new_password) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $userId]);
        }

        $success = "Profile updated successfully.";

        // Refresh user data
        $stmt = $pdo->prepare("SELECT name, email, profile_image FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Update Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { background:#F3E8FF;  font-family: Arial, sans-serif; }
        .container { max-width: 500px; margin-top: 50px; }
        .form-wrapper { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #6f42c1; border-color: y#6f42c1; color: black; width: 100%; padding: 12px; border-radius: 8px; }
        .btn-primary:hover { background-color:#6f42c1; border-color: #6f42c1; }
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #F3E8FF;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="form-wrapper">
        <h2>Update Profile</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Show current profile image or default -->
        <?php
        $profileImagePath = $user['profile_image'] ? "../uploads/profile_images/" . htmlspecialchars($user['profile_image']) : "../images/default-user.png";
        ?>
        <img src="<?= $profileImagePath ?>" alt="Profile Image" class="profile-img" />

        <form method="POST" action="" enctype="multipart/form-data" novalidate>
            <label for="profile_image" class="form-label">Change Profile Image</label>
            <input type="file" name="profile_image" class="form-control mb-3" accept="image/*" />

            <label>Name</label>
            <input type="text" name="name" class="form-control mb-3" value="<?= htmlspecialchars($user['name']) ?>" required />

            <label>Email</label>
            <input type="email" name="email" class="form-control mb-3" value="<?= htmlspecialchars($user['email']) ?>" required />

            <hr>
            <p><strong>Change Password</strong> (leave blank if you don't want to change)</p>

            <label>Current Password</label>
            <input type="password" name="current_password" class="form-control mb-3" />

            <label>New Password</label>
            <input type="password" name="new_password" class="form-control mb-3" />

            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control mb-3" />

            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
</div>
<!-- Include footer OUTSIDE container -->
<?php include '../include/footer.php'; ?>

</body>
</html>
