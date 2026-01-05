<?php
require_once 'db/conn.php';
require_once 'include/header.php';

// Get review page data from `pages` table
$stmt = $pdo->prepare("SELECT title, content, image1 FROM pages WHERE slug = 'reviews'");
$stmt->execute();
$page = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
$errors = [];
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rating = (int) ($_POST['rating'] ?? 0);
    $review = trim($_POST['review'] ?? '');
    $image = $_FILES['image']['name'] ?? null;

    if ($name === '')
        $errors[] = "Name is required.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Valid email required.";
    if ($rating < 1 || $rating > 5)
        $errors[] = "Rating must be 1-5.";
    if ($review === '')
        $errors[] = "Review cannot be empty.";

    if (empty($errors)) {
        if ($image) {
            move_uploaded_file($_FILES['image']['tmp_name'], "images/reviews/$image");
        }
        $stmt = $pdo->prepare("INSERT INTO reviews (name,email,rating,review,image,status,created_at) VALUES (?,?,?,?,?, 'pending', NOW())");
        $stmt->execute([$name, $email, $rating, $review, $image]);
        $success_message = "Thank you! Your review is submitted for approval.";
        $_POST = [];
    }
}

// Fetch approved reviews
$reviews = $pdo->query("SELECT * FROM reviews WHERE status='approved' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    html,
    body {
        height: 100%;
        background-color: #F3E8FF !important;
    }

    /* Page container */
    .page-header {
        text-align: center;
        margin: 30px 0;
        opacity: 0;
        animation: fadeInUp 1s ease forwards;
        animation-delay: 0.2s;
    }

    .page-header img {
        width: 220px;
        height: 220px;
        border-radius: 50%;
        object-fit: cover;
        border: 6px solid #fff;
        box-shadow: 0 4px 15px rgba(111, 66, 193, 0.3);
    }

    .page-header h1 {
        color: #6f42c1;
        margin-top: 15px;
        font-size: 2rem;
    }

    .page-header p {
        max-width: 700px;
        margin: 10px auto;
        font-size: 1.1rem;
        color: #333;
        line-height: 1.6;
    }

    /* Form */
    form {
        max-width: 700px;
        margin: 30px auto 50px auto;
        padding: 25px;
        background: #fff;
        border: 2px solid #6f42c1;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        gap: 12px;

        /* fade effect */
        opacity: 0;
        animation: fadeInUp 1s ease forwards;
        animation-delay: 0.6s;
    }

    form input,
    form textarea,
    form select {
        padding: 12px;
        font-size: 1rem;
        border: 2px solid #6f42c1;
        border-radius: 8px;
        width: 100%;
        transition: 0.3s;
    }

    form input:focus,
    form textarea:focus {
        outline: none;
        border-color: #5a2fa0;
        box-shadow: 0 0 8px rgba(111, 66, 193, 0.4);
    }

    form button {
        background: #6f42c1;
        color: #fff;
        border: none;
        padding: 14px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: bold;
        transition: 0.3s;
    }

    form button:hover {
        background: #5a2fa0;
    }

    /* Success + Error */
    .success-message {
        color: green;
        text-align: center;
        font-weight: bold;
    }

    .error-list {
        color: red;
        text-align: center;
        list-style: none;
        padding: 0;
    }

    /* Reviews */
    .review-card {
        max-width: 700px;
        margin: 20px auto;
        padding: 20px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid #ddd;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

        /* fade effect */
        opacity: 0;
        animation: fadeInUp 1s ease forwards;
    }

    .review-card .name {
        font-weight: bold;
        font-size: 1.1rem;
        color: #6f42c1;
    }

    .review-card .stars {
        color: #f5b50a;
        margin: 5px 0;
    }

    .review-card img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        margin-top: 10px;
    }

    .review-card small {
        color: #666;
        display: block;
        margin-top: 8px;
    }

    /* Fade animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>


<div class="page-header">
    <?php if (!empty($page['image1'])): ?>
        <img src="images/<?= htmlspecialchars($page['image1']) ?>" alt="<?= htmlspecialchars($page['title']) ?>">
    <?php endif; ?>
    <h1><?= htmlspecialchars($page['title']) ?></h1>
    <p><?= nl2br(htmlspecialchars($page['content'])) ?></p>
</div>

<?php if ($success_message): ?>
    <p class="success-message"><?= htmlspecialchars($success_message) ?></p>
<?php endif; ?>

<?php if ($errors): ?>
    <ul class="error-list">
        <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
    <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
        required>
    <label>Rating:</label>
    <div>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>" <?= (($_POST['rating'] ?? 0) == $i) ? 'checked' : '' ?> required>
            <label for="star<?= $i ?>"><?= str_repeat('★', $i) ?></label>
        <?php endfor; ?>
    </div>
    <textarea name="review" placeholder="Your review"
        required><?= htmlspecialchars($_POST['review'] ?? '') ?></textarea>
    <input type="file" name="image" accept="image/*">
    <button type="submit">Submit Review</button>
</form>

<h2 style="text-align:center;">Customer Reviews</h2>
<?php foreach ($reviews as $rev): ?>
    <div class="review-card">
        <span class="name"><?= htmlspecialchars($rev['name']) ?></span>
        <div class="stars">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <?= ($i <= $rev['rating']) ? '★' : '☆'; ?>
            <?php endfor; ?>
        </div>
        <p><?= nl2br(htmlspecialchars($rev['review'])) ?></p>
        <?php if ($rev['image']): ?>
            <img src="images/reviews/<?= htmlspecialchars($rev['image']) ?>" alt="Review Image">
        <?php endif; ?>
        <small><?= $rev['created_at'] ?></small>
    </div>
<?php endforeach; ?>

<?php require_once 'include/footer.php'; ?>