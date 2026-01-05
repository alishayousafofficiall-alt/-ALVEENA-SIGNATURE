<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "ecommerce");

if ($mysqli->connect_error) {
    die("DB connection failed: " . $mysqli->connect_error);
}

$error = "";

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // 👉 Role ke hisaab se redirect karo
        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
            exit;
        } elseif ($user['role'] === 'user') {
            header("Location: user/dashboard.php");
            exit;
        } else {
            $error = "Unknown role!";
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<?php require_once 'include/header.php'; ?>


<style>
    /* --- RESET --- */
    html,
    body {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: 'Poppins', sans-serif;
    }

    /* --- FLEX LAYOUT --- */
    body {
        display: flex;
        flex-direction: column;
    }

    /* Page content fills space between header and footer */
    .page-content {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #F3E8FF;
        padding: 0;
        /* no extra padding */
        margin: 0;
    }

    /* --- LOGIN FORM WRAPPER --- */
    .login-form-wrapper {
        width: 100%;
        max-width: 400px;
        background: #fff;
        padding: 30px 25px;
        /* slightly smaller padding */
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        text-align: center;
    }

    /* Heading */
    .login-form-wrapper h2 {
        margin-bottom: 20px;
        color: #333;
        font-weight: 600;
    }

    /* Inputs */
    .login-form-wrapper .form-control {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
    }

    .login-form-wrapper .form-control:focus {
        border-color: #6f42c1;
        outline: none;
        box-shadow: 0 0 5px rgba(111, 66, 193, 0.2);
    }

    /* Button */
    .login-form-wrapper .btn-primary {
        width: 100%;
        padding: 12px 0;
        font-size: 16px;
        font-weight: 500;
        border-radius: 8px;
        border: none;
        background-color: #6f42c1;
        color: #fff;
        cursor: pointer;
    }

    .login-form-wrapper .btn-primary:hover {
        background-color: #5930a0;
    }

    /* Footer links */
    .login-form-wrapper .form-footer {
        margin-top: 15px;
        font-size: 14px;
    }

    .login-form-wrapper .form-footer a {
        color: #6f42c1;
        text-decoration: none;
    }

    .login-form-wrapper .form-footer a:hover {
        text-decoration: underline;
    }

    /* Alert */
    .alert {
        margin-bottom: 10px;
    }

    /* --- Responsive --- */
    @media (max-width: 480px) {
        .login-form-wrapper {
            padding: 25px 20px;
        }

        .login-form-wrapper h2 {
            font-size: 22px;
        }
    }
</style>

<div class="page-content">
    <div class="login-form-wrapper">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php" novalidate>
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <div class="form-footer">
            <p>Don't have an account? <a href="registration.php">Register here</a></p>
        </div>
    </div>
</div>

<?php
include 'include/footer.php';
?>