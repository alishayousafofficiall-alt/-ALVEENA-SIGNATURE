<?php
require_once 'db/conn.php';
require_once 'include/header.php';

$section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;
if (!$section_id) { echo "Section not specified."; exit; }

$secStmt = $pdo->prepare("SELECT * FROM products_sections WHERE id = ?");
$secStmt->execute([$section_id]);
$section = $secStmt->fetch(PDO::FETCH_ASSOC);
if (!$section) { echo "Section not found."; exit; }

$prodStmt = $pdo->prepare("SELECT * FROM products WHERE section_id = ? ORDER BY id DESC");
$prodStmt->execute([$section_id]);
$products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
html, body {
    margin: 0;
    padding: 0;
    min-height: 100%;
    font-family: Arial, sans-serif;
    background-color: #F3E8FF !important;
}

/* Section heading similar to category page */
.section-header {
    text-align: center;
    padding: 20px 10px;
}
.section-header h1 {
    font-size: 36px;
    font-weight: bold;
    margin-bottom: 15px;
    text-transform: uppercase;
    color: #4B0082;
}
.section-header p {
    font-size: 18px;
    color: #444;
    line-height: 1.6;
    max-width: 800px;
    margin: 0 auto;
}

/* Products grid (same as category page) */
.products {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    padding-bottom: 60px; 
}
.product-card {
    width: calc(25% - 20px);
    background-color: transparent;
    position: relative;
    transition: transform 0.3s ease;
}
.product-card:hover { transform: translateY(-6px); }
.image-container {
    width: 100%;
    aspect-ratio: 1 / 1;
    border: 1px solid #ccc;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
    background: transparent;
      position: relative;
}


/* Generic tag style */
.tag {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
    border-radius: 4px;
    color: #fff;
    z-index: 5;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

/* Sale tag style */
.sale-tag {
    background: linear-gradient(45deg, #ff416c, #ff4b2b);
}

/* New tag style */
.new-tag {
    background: linear-gradient(45deg, #6a11cb, #2575fc);
}

/* Optional: adjust for smaller screens */
@media (max-width: 480px) {
    .tag {
        font-size: 10px;
        padding: 4px 8px;
    }
}

.image-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: all 0.3s ease;
}
.product-card:hover .image-container img {
    filter: blur(3px) brightness(0.6);
    transform: scale(1.05);
}
.overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.8);
    opacity: 0;
    text-align: center;
    color: #fff;
    transition: all 0.3s ease;
    width: 90%;
    z-index: 2;
}
.product-card:hover .overlay {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}
.overlay h4 { font-size: 16px; font-weight: bold; color: #fff; }
.overlay p { font-size: 13px; margin: 6px 0 12px; color: #ddd; }
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
.view-btn:hover { background-color: #eee; }

@media (max-width: 1024px) { .product-card { width: calc(33.33% - 20px); } }
@media (max-width: 768px) { .product-card { width: calc(50% - 20px); } }
@media (max-width: 480px) { .product-card { width: 100%; } }
</style>

<div class="section-header">
    <h1><?= htmlspecialchars($section['title']) ?></h1>
    <?php if (!empty($section['description'])): ?>
        <p><?= htmlspecialchars($section['description']) ?></p>
    <?php endif; ?>
</div>

<?php if (empty($products)): ?>
    <p style="text-align:center;">No products found.</p>
<?php else: ?>
<div class="products">
    <?php foreach ($products as $p): ?>
        <div class="product-card">
           <div class="image-container">
    <?php if (stripos($section['section_key'], 'eid') !== false): ?>
        <span class="tag sale-tag">SALE<br>Up to 60% OFF</span>
    <?php endif; ?>
    <?php if (stripos($section['section_key'], 'what-new') !== false): ?>
        <span class="tag new-tag">NEW</span>
    <?php endif; ?>
    <img src="images/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
    <div class="overlay">
        <h4><?= htmlspecialchars($p['title']) ?></h4>
        <p>Rs <?= number_format($p['price']) ?></p>
        <a class="view-btn" href="product-detail.php?id=<?= (int)$p['id'] ?>&section_id=<?= $section_id ?>">View Detail</a>
    </div>
</div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php
require_once 'include/footer.php';
?>
