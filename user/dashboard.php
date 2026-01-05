<?php
session_start();
require_once '../db/conn.php';
require_once '../header.php';

// Only allow users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

// Initialize bannerPath to avoid undefined variable warning
$bannerPath = '';

// Fetch user banner
$stmt = $pdo->prepare("SELECT image_path FROM dashboard_banner WHERE type='user' LIMIT 1");
$stmt->execute();
$result = $stmt->fetchColumn();
if ($result) {
    $bannerPath = $result;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Ensure full height layout */
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        /* Banner flush to top */
        .dashboard-banner img {
            width: 100%;
            height: auto;
            display: block; /* remove gap below image */
        }

        /* Main content will grow and push footer down */
        main {
            flex: 1;
        }

        /* Footer flush to bottom */
        footer {
            background: #222;
            color: #fff;
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>
<body>

<?php if (!empty($bannerPath)): ?>
    <div class="dashboard-banner">
        <img src="<?= htmlspecialchars($bannerPath) ?>?t=<?= time() ?>" alt="User Banner">
    </div>
<?php endif; ?>

<main>
    <!-- Any additional dashboard content can go here -->
</main>

<!-- Footer -->
<?php include '../include/footer.php'; ?>

</body>
</html>
