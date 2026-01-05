<?php
session_start();
require_once 'db/conn.php';
require_once 'include/header.php';
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$product_id)
    die("Product not specified.");

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product)
    die("Product not found.");



// Get sizes from the category OR section table
$stmt = $pdo->prepare("
    SELECT 
        c.sizes AS category_sizes,
        ps.sizes AS section_sizes
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN products_sections ps ON p.section_id = ps.id
    WHERE p.id = ?
");
$stmt->execute([$product_id]);
$sizesData = $stmt->fetch(PDO::FETCH_ASSOC);

// Priority: category sizes first, otherwise section sizes
$sizesToShow = '';
if (!empty($sizesData['category_sizes'])) {
    $sizesToShow = $sizesData['category_sizes'];
} elseif (!empty($sizesData['section_sizes'])) {
    $sizesToShow = $sizesData['section_sizes'];
}

// Parse sizes into multiple groups (adult/kids or custom labels)
$sizeGroups = [];
if (!empty($sizesToShow)) {
    $allSizes = array_map('trim', explode(',', $sizesToShow));
    $adultSizes = [];
    $kidSizes = [];

    foreach ($allSizes as $size) {
        if (preg_match('/^\d+(-\d+)?$/', $size)) {
            $kidSizes[] = $size; // e.g. 1-2, 3-4
        } else {
            $adultSizes[] = $size; // e.g. S, M, L, XL
        }
    }

    if (!empty($adultSizes)) {
        $sizeGroups[] = ['label' => 'Adult Sizes', 'sizes' => $adultSizes];
    }
    if (!empty($kidSizes)) {
        $sizeGroups[] = ['label' => 'Kids Sizes', 'sizes' => $kidSizes];
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($product['title']) ?></title>
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

        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            padding: 40px 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 60px;
            /* ✅ Footer se gap banane ke liye */
        }

        /* Optional: footer upar se thoda gap le */
        footer {
            margin-top: 40px;
        }


        .product-image {
            flex: 1 1 45%;
        }

        .product-image img {
            width: 100%;
            border-radius: 10px;
            object-fit: cover;
        }

        .product-details {
            flex: 1 1 50%;
        }

        .product-details h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .price {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #000;
        }

        .size-options {
            margin: 15px 0;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .size-option input {
            display: none;
        }

        .size-option label {
            padding: 10px 18px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            background: #f1f1f1;
            transition: all 0.3s ease;
        }

        .size-option input:checked+label {
            background: #000;
            color: #fff;
            border-color: #000;
        }

        .buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn-addcart,
        .btn-buynow {
            background-color: #6f42c1;
            color: #000;
            border: none;
            padding: 12px 28px;
            font-weight: bold;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            text-align: center;
        }

        .btn-addcart:hover,
        .btn-buynow:hover {
            background-color: #6f42c1;
        }

        @media screen and (max-width: 768px) {
            .product-container {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="product-container">
        <div class="product-image">
            <img src="images/<?= htmlspecialchars($product['image']) ?>"
                alt="<?= htmlspecialchars($product['title']) ?>">
        </div>

        <div class="product-details">
            <h1><?= htmlspecialchars($product['title']) ?></h1>
            <p class="price">Rs <?= number_format($product['price']) ?></p>
            <p><strong>SKU:</strong> <?= htmlspecialchars($product['sku']) ?></p>

            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <p><strong>Fabric:</strong> <?= htmlspecialchars($product['fabric']) ?></p>
            <p><strong>Color:</strong> <?= htmlspecialchars($product['color']) ?></p>

            <?php if (count($sizeGroups) > 0): ?>
                <?php foreach ($sizeGroups as $i => $group): ?>
                    <div>
                        <strong><?= $group['label'] ?>:</strong>
                        <div class="size-options">
                            <?php foreach ($group['sizes'] as $j => $size): ?>
                                <div class="size-option">
                                    <input type="radio" name="size_group_<?= $i ?>" id="size_<?= $i ?>_<?= $j ?>"
                                        value="<?= htmlspecialchars($size) ?>">
                                    <label for="size_<?= $i ?>_<?= $j ?>"><?= htmlspecialchars($size) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <input type="hidden" id="no_size" value="No Size">
            <?php endif; ?>

            <div class="buttons">
                <button class="btn-addcart" data-id="<?= $product['id'] ?>">Add to Cart</button>
                <button class="btn-buynow" data-id="<?= $product['id'] ?>">BUY NOW</button>
            </div>
        </div>
    </div>

    <script>
        // Common function for both buttons
        function handleCartOrBuy(productId, isBuyNow) {
            const groupCount = <?= count($sizeGroups) ?>;
            let selectedSizes = [];

            if (groupCount > 0) {
                for (let i = 0; i < groupCount; i++) {
                    const sel = document.querySelector(`input[name="size_group_${i}"]:checked`);
                    if (!sel) {
                        alert('Please select a size.');
                        return;
                    }
                    selectedSizes.push(sel.value);
                }
            } else {
                selectedSizes.push("No Size");
            }

            const sizeString = selectedSizes.join(", ");

            if (isBuyNow) {
                window.location.href = `checkout.php?buy_now=${productId}&size=${encodeURIComponent(sizeString)}`;
            } else {
                fetch('add-to-cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${productId}&size=${encodeURIComponent(sizeString)}`
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const cartCountSpan = document.querySelector('#nav-cart-count');
                            if (cartCountSpan) cartCountSpan.textContent = data.total;
                            alert('Product added to cart!');
                        } else {
                            alert(data.msg || 'Error adding to cart.');
                        }
                    })
                    .catch(err => console.error(err));
            }
        }

        // Event listeners
        document.querySelector('.btn-addcart').addEventListener('click', function () {
            const productId = this.getAttribute('data-id');
            handleCartOrBuy(productId, false);
        });

        document.querySelector('.btn-buynow').addEventListener('click', function () {
            const productId = this.getAttribute('data-id');
            handleCartOrBuy(productId, true);
        });
    </script>