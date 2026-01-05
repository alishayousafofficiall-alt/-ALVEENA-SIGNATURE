<?php
require_once '../db/conn.php';
require_once '../header.php'; // header include karega (isme <body> aur nav bar hoga)

// Approve or Reject
if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id = (int) $_GET['id'];

    if (in_array($action, ['approve', 'reject'])) {
        $stmt = $pdo->prepare("UPDATE reviews SET status = ? WHERE id = ?");
        $stmt->execute([$action === 'approve' ? 'approved' : 'rejected', $id]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Fetch pending reviews
$reviews = $pdo->query("SELECT * FROM reviews WHERE status='pending' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- ================= CSS ================= -->
<style>
    html,
    body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    main {
        flex: 1;
        /* ye footer ko neeche push karega */
        padding: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        padding: 12px 10px;
        text-align: left;
        vertical-align: middle;
        border-bottom: 1px solid #6f42c1;
    }

    th {
        background-color: #6f42c1;
        color: #fff;
        font-weight: 700;
    }

    tr:nth-child(even) {
        background-color: #f3e8ff;
    }

    tr:hover {
        background-color: #e0d4f7;
    }

    img.review-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
    }

    .action-links a {
        text-decoration: none;
        color: #fff;
        padding: 5px 10px;
        border-radius: 4px;
        margin-right: 5px;
        font-weight: 600;
    }

    .action-links a.approve {
        background-color: #28a745;
    }

    .action-links a.reject {
        background-color: #dc3545;
    }

    .action-links a:hover {
        opacity: 0.8;
    }

    footer {
        background-color: #6f42c1;
        color: #fff;
        text-align: center;
        padding: 15px 0;
    }
</style>

<!-- ================= Content (header ke niche start hoga) ================= -->
<main>
    <h1>Pending Reviews</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Rating</th>
            <th>Review</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($reviews as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
                <td>
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= $r['rating'] ? "★" : "☆";
                    }
                    ?>
                </td>
                <td><?= nl2br(htmlspecialchars($r['review'])) ?></td>
                <td>
                    <?php if ($r['image']): ?>
                        <img class="review-img" src="../images/reviews/<?= htmlspecialchars($r['image']) ?>" alt="Review Image">
                    <?php endif; ?>
                </td>
                <td class="action-links">
                    <a class="approve" href="?action=approve&id=<?= $r['id'] ?>">Approve</a>
                    <a class="reject" href="?action=reject&id=<?= $r['id'] ?>">Reject</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>

<?php require_once '../include/footer.php'; ?>