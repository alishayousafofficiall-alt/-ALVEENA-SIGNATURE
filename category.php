<?php
require_once 'db/conn.php';
require_once 'include/header.php';
$category_id = $_GET['category_id'] ?? null;

if (!$category_id) {
    echo "Category ID not found.";
    exit;
}

// Get main category (e.g., Girls)
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    echo "Category not found.";
    exit;
}

// Get subcategories (e.g., 1-5 Years, 5-10 Years, etc.)
$sub_stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ?");
$sub_stmt->execute([$category_id]);
$subcategories = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($category['name']) ?> - Subcategories</title>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            font-family: Arial, sans-serif;
            background-color: #F3E8FF !important;
            /* Force purple everywhere */
            background-image: none !important;
            /* Remove any other background overrides */
        }


        /* Page heading */
        h1 {
            text-align: center;
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 40px;
            text-transform: uppercase;
            color: #333;
        }

        .subcategory-title {
            font-size: 24px;
            text-align: center;
            margin: 40px 0 20px;
            color: #444;
            text-transform: capitalize;
        }

        /* Section spacing */
        .subcategory-section {
            margin-bottom: 60px;
        }

        /* Products grid */
        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        /* Product card */
        .product-card {
            width: calc(25% - 20px);
            /* 4 per row */
            background-color: transparent;
            border: none;
            margin: 0;
            padding: 0;
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-6px);
        }

        /* Image container */
        .image-container {
            width: 100%;
            aspect-ratio: 1 / 1;
            /* square */
            border: 1px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: transparent;
            box-sizing: border-box;
            overflow: hidden;
            position: relative;
        }

        .image-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
            background-color: transparent;
            transition: all 0.3s ease;
        }

        /* Hover effect */
        .product-card:hover .image-container img {
            filter: blur(3px) brightness(0.6);
            transform: scale(1.05);
        }

        /* Overlay */
        .overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            opacity: 0;
            z-index: 2;
            text-align: center;
            color: #fff;
            transition: all 0.3s ease;
            width: 90%;
        }

        .product-card:hover .overlay {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }

        .overlay p {
            font-size: 13px;
            margin: 6px 0 12px;
            color: #ddd;
        }

        .view-btn {
            display: inline-block;
            padding: 8px 14px;
            background-color: #fff;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background 0.2s ease;
        }

        .view-btn:hover {
            background-color: #eee;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .product-card {
                width: calc(33.33% - 20px);
            }
        }

        @media (max-width: 768px) {
            .product-card {
                width: calc(50% - 20px);
            }
        }

        @media (max-width: 480px) {
            .product-card {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div style="text-align: center; padding: 20px 10px; margin: 0;">
        <h1 style="font-size: 36px; color: #4B0082; margin: 0 0 15px 0;">
            <?= htmlspecialchars($category['name']) ?>
        </h1>

        <?php if (!empty($category['description'])): ?>
            <p style="font-size: 18px; color: #444; line-height: 1.6; max-width: 800px; margin: 0 auto;">
                <?= htmlspecialchars($category['description']) ?>
            </p>
        <?php endif; ?>
    </div>



    <!-- Show products in main category itself -->
    <div class="subcategory-section">
        <div class="products">
            <?php
            $mainProdStmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ?");
            $mainProdStmt->execute([$category['id']]);
            $mainProducts = $mainProdStmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($mainProducts) > 0):
                foreach ($mainProducts as $product):
                    ?>
                    <div class="product-card">
                        <div class="image-container">
                            <img src="images/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                            <div class="overlay">
                                <a href="product-detail.php?id=<?= $product['id'] ?>" class="view-btn">View Detail</a>
                            </div>
                        </div>
                    </div>


                    <?php
                endforeach;

            endif;
            ?>
        </div>
    </div>

    <!-- Show subcategory products -->
    <?php foreach ($subcategories as $subcat): ?>
        <div class="subcategory-section">
            <h2 class="subcategory-title"><?= htmlspecialchars($subcat['name']) ?></h2>

            <div class="products">
                <?php
                $prod_stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ?");
                $prod_stmt->execute([$subcat['id']]);
                $products = $prod_stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($products) > 0):
                    foreach ($products as $product):
                        ?>
                        <div class="product-card">
                            <div class="image-container">
                                <img src="images/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                                <div class="overlay">

                                    <p><?= substr($product['description'], 0, 50) ?>...</p>
                                    <a href="product-detail.php?id=<?= $product['id'] ?>" class="view-btn">View Detail</a>
                                </div>
                            </div>
                        </div>


                        <?php
                    endforeach;
                else:
                    echo "<p>No products in this subcategory.</p>";
                endif;
                ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php
    require_once 'include/footer.php';
    ?>