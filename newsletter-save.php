<?php
require 'db/conn.php'; // your PDO or mysqli connection

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['subscribe'])) {
    $email = trim($_POST['email']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
            $stmt->execute([$email]);

            $_SESSION['success'] = "Thank you for subscribing!";
            header("Location: index.php#newsletter");
            exit;

        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $_SESSION['error'] = "You are already subscribed.";
            } else {
                $_SESSION['error'] = "Something went wrong. Try again.";
            }
            header("Location: home.php#newsletter");
            exit;
        }
    } else {
        $_SESSION['error'] = "Invalid email address.";
        header("Location: home.php#newsletter");
        exit;
    }
}
