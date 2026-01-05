<?php
session_start();
require_once '../db/conn.php';
require_once '../header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle banner upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['banner'])) {
    $fileName = time() . '_' . basename($_FILES['banner']['name']);
    $target = '../images/' . $fileName;

    if (move_uploaded_file($_FILES['banner']['tmp_name'], $target)) {
        $stmt = $pdo->prepare("UPDATE dashboard_banner SET image_path = ? WHERE id = 1");
        $stmt->execute([$target]);
        $successMsg = "Banner updated successfully!";
    } else {
        $errorMsg = "Failed to upload image.";
    }
}

// Fetch banner image
$bannerStmt = $pdo->query("SELECT image_path FROM dashboard_banner LIMIT 1");
$bannerPath = $bannerStmt->fetchColumn(); // Use $bannerPath


// Stats
$totalOrders    = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders  = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$totalUsers     = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalMessages  = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
          body {
            margin: 0;
            padding: 0;
        }
        /* Full-width banner */
        .dashboard-banner {
    width: 100%;
    height: auto;       /* Keep original height ratio */
    display: block;
    margin-top: 56px;   /* For fixed navbar gap */
}
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        .card:hover { transform: translateY(-4px); }
        .card-icon { font-size: 2.5rem; opacity: 0.8; }
    </style>
</head>
<body>

<!-- FULL-WIDTH Banner -->
<?php if ($bannerPath): ?>
    <div class="dashboard-banner">
        <img src="<?= htmlspecialchars($bannerPath) ?>?t=<?= time() ?>" alt="Banner" style="width:100%; height:auto;">
    </div>
<?php endif; ?>

<!-- Stats Section -->
<div class="container py-5">
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4>Total Orders</h4>
                        <h2><?= $totalOrders ?></h2>
                    </div>
                    <div class="card-icon">📦</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4>Pending Orders</h4>
                        <h2><?= $pendingOrders ?></h2>
                    </div>
                    <div class="card-icon">⏳</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4>Total Users</h4>
                        <h2><?= $totalUsers ?></h2>
                    </div>
                    <div class="card-icon">👤</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4>Messages</h4>
                        <h2><?= $totalMessages ?></h2>
                    </div>
                    <div class="card-icon">💬</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../include/footer.php'; ?>

</body>
</html>
