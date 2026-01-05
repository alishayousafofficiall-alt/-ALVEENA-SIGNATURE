<?php
session_start();
require_once 'db/conn.php'; // DB connection
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!$name || !$email || !$password) {
        $error = "Please fill all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            if ($stmt->execute([$name, $email, $hashedPassword])) {
                $success = "Registration successful. You can now login.";
            } else {
                $error = "Error during registration.";
            }
        }
    }
}

// Include header with navbar
include 'include/header.php';
?>

<style>
html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: 'Poppins', sans-serif;
}

/* Flex layout to stick footer */
body {
    display: flex;
    flex-direction: column;
}

.page-content {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #F3E8FF;
    padding: 0;
    margin: 0;
}

/* Form Wrapper */
.form-wrapper {
    width: 100%;
    max-width: 400px;
    background: #fff;
    padding: 35px 30px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    text-align: center;
}

.form-wrapper h2 {
    margin-bottom: 20px;
    color: #333;
    font-weight: 600;
}

.form-wrapper .form-control {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
}

.form-wrapper .form-control:focus {
    border-color: #6f42c1;
    outline: none;
    box-shadow: 0 0 5px rgba(111,66,193,0.2);
}

.form-wrapper .btn-primary {
    width: 100%;
    padding: 14px 0;
    font-size: 16px;
    font-weight: 500;
    border-radius: 8px;
    border: none;
    background-color: #6f42c1;
    color: #fff;
    cursor: pointer;
}

.form-wrapper .btn-primary:hover {
    background-color: #5930a0;
}

.alert { margin-bottom: 10px; }

.form-footer {
    margin-top: 15px;
    font-size: 14px;
}

.form-footer a {
    color: #6f42c1;
    text-decoration: none;
}

.form-footer a:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 480px) {
    .form-wrapper { padding: 25px 20px; }
    .form-wrapper h2 { font-size: 22px; }
}
</style>

<div class="page-content">
    <div class="form-wrapper">
        <h2>Register</h2>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="registration.php" novalidate>
            <input type="text" name="name" class="form-control" placeholder="Full Name" required />
            <input type="email" name="email" class="form-control" placeholder="Email" required />
            <input type="password" name="password" class="form-control" placeholder="Password" required />
            <button type="submit" class="btn btn-primary">Register</button>
        </form>

        <div class="form-footer">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>
