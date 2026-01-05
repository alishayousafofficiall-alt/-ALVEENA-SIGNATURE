<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Successful</title>
    <style>
        html, body {
    height: 100%;
    background-color: #F3E8FF !important;
}

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #F3E8FF;
            margin: 0;
            padding: 0;

            display: flex;
            flex-direction: column; /* because header + footer + content */
            min-height: 100vh;
        }

        main {
            flex: 1; /* take remaining space */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .success-box {
            text-align: center;
            border: 1px solid #fbe9e7;
            padding: 40px 60px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .success-box h2 {
            color: #d84315;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .success-box a {
            text-decoration: none;
            background-color:#6f42c1;
            color: #000;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .success-box a:hover {
            background-color:#6f42c1;
        }
    </style>
</head>
<body>
    <?php require_once 'include/header.php'; ?>  

    <main>
        <div class="success-box">
            <h2>✅ Thank you! Your order has been placed successfully.</h2>
            <a href="home.php">Back to Home</a>
        </div>
    </main>

    <?php require_once 'include/footer.php'; ?>
</body>
</html>
