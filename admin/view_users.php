<?php
session_start();
require_once '../db/conn.php';
require_once '../header.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch all users
$stmt = $pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE role = 'user'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Users</title>
    <style>
        /* Ensure body takes full height and footer is at bottom */
        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1; /* main content grows, pushing footer down */
            padding: 70px 20px 20px; /* top space for navbar */
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #6f42c1;
        }

        table {
            width: 80%;
            max-width: 1000px;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #6f42c1;
            text-align: left;
        }

        th {
            background-color: #6f42c1;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Footer styling */
        footer {
            background: #6f42c1;
            color: white;
            text-align: center;
            padding: 15px 0;
        }
    </style>
</head>
<body>

<main>
    <h2>Registered Users</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Registered On</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= htmlspecialchars($user['created_at'] ?? 'N/A') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php include '../include/footer.php'; ?>

</body>
</html>
