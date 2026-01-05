<?php
require 'db/conn.php';

// Eid ke 4 sections fetch karna
$sections = [
    'Woman Eid Sales' => 'eid_section',
    'Men Eid Sales' => 'men_eid_section',
    'Macthing Eid Sales' => 'matching_eid_section',
    'Childs Eid Sales' => 'Childs_eid_section',
];

$all_sections = [];
foreach ($sections as $label => $key) {
    $stmt = $pdo->prepare("SELECT * FROM products_sections WHERE section_key = ?");
    $stmt->execute([$key]);
    $section = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($section) {
        // Get products for this section
        $product_stmt = $pdo->prepare("
            SELECT p.* FROM products p
            JOIN products_section_items psi ON psi.product_id = p.id
            WHERE psi.section_id = ?
        ");
        $product_stmt->execute([$section['id']]);
        $products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);
        $section['products'] = $products;
        $all_sections[] = $section;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Eid Collection</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; }
        .section { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .section h2 { font-size: 26px; margin-bottom: 6px; }
        .section p { color: #555; margin-bottom: 20px; }
        .banner img {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .products-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .product-card {
            width: 230px;
            border: 1px solid #eee;
            border-radius: 6px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            text-align: center;
            transition: 0.2s;
        }
        .product-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .product-card img {
            width: 100%;
            height: 240px;
            object-fit: cover;
        }
        .product-card h4 {
            margin: 10px 0 4px;
            font-size: 16px;
        }
        .price {
            font-weight: bold;
            color: #333;
        }
        .product-card .offer {
            color: red;
            font-size: 13px;
            margin-top: 2px;
        }
        .view-btn {
            display: inline-block;
            margin: 10px 0 15px;
            background: #007bff;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>

<?php if (empty($all_sections)): ?>
    <div class="section"><h2>No Eid Sections Found</h2></div>
<?php else: ?>
    <?php foreach ($all_sections as $section): ?>
        <div class="section">
            <h2><?= htmlspecialchars($section['title']) ?></h2>
            <p><?= htmlspecialchars($section['description']) ?></p>

            <?php if (!empty($section['image'])): ?>
                <div class="banner">
                    <img src="images/<?= htmlspecialchars($section['image']) ?>" alt="<?= htmlspecialchars($section['title']) ?>">
                </div>
            <?php endif; ?>

            <?php if (empty($section['products'])): ?>
                <p style="color:#999;">No products in this section yet.</p>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($section['products'] as $product): ?>
                        <div class="product-card">
                            <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                            <h4><?= htmlspecialchars($product['title']) ?></h4>
                            <div class="price">Rs <?= number_format($product['price']) ?></div>
                            <div class="offer">Up to 50% OFF</div>
                            <a class="view-btn" href="product-detail.php?id=<?= $product['id'] ?>">View Detail</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>

