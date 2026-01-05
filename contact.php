<?php
require_once 'db/conn.php';
require_once 'include/header.php';

$slug = 'contact';
$errors = [];
$success_message = '';

$stmt = $pdo->prepare("SELECT title, content, image1 FROM pages WHERE slug = ?");
$stmt->execute([$slug]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$page) {
    echo "<h1>Page not found.</h1>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '')
        $errors[] = "Name is required.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Valid email required.";
    if ($subject === '')
        $errors[] = "Subject required.";
    if ($message === '')
        $errors[] = "Message required.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name,email,subject,message) VALUES (?,?,?,?)");
        $stmt->execute([$name, $email, $subject, $message]);
        $success_message = "Thank you for contacting us. We will get back to you soon.";
        $_POST = [];
    }
}
?>

<style>
    /* ==== BASIC RESET ==== */
    html,
    body {
        height: 100%;
        background-color: #F3E8FF !important;
    }

    /* ==== LAYOUT ==== */
    .full-page {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 40px 20px;
        box-sizing: border-box;
    }

    .content-wrapper {
        width: 100%;
        max-width: 800px;
        text-align: center;
    }

    /* ==== IMAGE ==== */
    .single-image-container {
        margin-bottom: 20px;
    }

    .single-image-container img {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

        opacity: 0;
        animation: fadeInUp 1s ease forwards;
        animation-delay: 0.2s;
    }

    /* ==== TITLE & CONTENT ==== */
    h1 {
        font-size: 2rem;
        color: #6f42c1;
        margin-bottom: 15px;

        opacity: 0;
        animation: fadeInUp 1s ease forwards;
        animation-delay: 0.4s;
    }

    .content {
        font-size: 1.1rem;
        line-height: 1.6;
        color: #333;
        margin-bottom: 25px;

        opacity: 0;
        animation: fadeInUp 1s ease forwards;
        animation-delay: 0.6s;
    }

    /* ==== FORM CARD ==== */
    form {
        max-width: 700px;
        margin: 0 auto 50px auto;
        padding: 25px;
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e0d1f7;
        box-shadow: 0 6px 15px rgba(111, 66, 193, 0.15);
        text-align: left;

        opacity: 0;
        animation: fadeInUp 1s ease forwards;
        animation-delay: 0.8s;
    }

    form label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #6f42c1;
    }

    form input,
    form textarea {
        width: 100%;
        padding: 12px;
        margin-bottom: 18px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 1rem;
        resize: vertical;
    }

    form input:focus,
    form textarea:focus {
        border-color: #6f42c1;
        outline: none;
        box-shadow: 0 0 5px rgba(111, 66, 193, 0.3);
    }

    form button {
        width: 100%;
        padding: 14px;
        background: #6f42c1;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: 0.3s;
    }

    form button:hover {
        background: #502c99;
    }

    .success-message {
        color: #6f42c1;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .error-list {
        color: red;
        list-style: disc;
        padding-left: 20px;
        max-width: 700px;
        margin: 0 auto 20px auto;
    }

    /* ==== ANIMATION ==== */
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

    /* ==== RESPONSIVE ==== */
    @media(max-width:768px) {
        .single-image-container img {
            width: 150px;
            height: 150px;
        }

        h1 {
            font-size: 1.6rem;
        }

        .content {
            font-size: 1rem;
        }

        form {
            padding: 20px;
        }
    }
</style>

<div class="full-page">
    <div class="content-wrapper">
        <div class="single-image-container">
            <?php if (!empty($page['image1'])): ?>
                <img src="images/<?= htmlspecialchars($page['image1']) ?>" alt="<?= htmlspecialchars($page['title']) ?>">
            <?php endif; ?>
        </div>

        <h1><?= htmlspecialchars($page['title']) ?></h1>

        <div class="content"><?= nl2br(htmlspecialchars($page['content'])) ?></div>

        <?php if ($success_message): ?>
            <p class="success-message"><?= $success_message ?></p>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <ul class="error-list">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

            <label>Subject:</label>
            <input type="text" name="subject" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" required>

            <label>Message:</label>
            <textarea name="message" rows="5" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>

            <button type="submit">Send Message</button>
        </form>
    </div>
</div>

<?php require_once 'include/footer.php'; ?>