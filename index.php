<?php
$title = "Home";
require_once 'include/header.php';
require_once 'db/conn.php';


?>
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
  /* === General Reset & Body === */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  html,
  body {
    width: 100%;
    overflow-x: hidden;
    /* Prevent horizontal scroll */
    height: 100%;
    font-family: Arial, sans-serif;
    background: #F3E8FF !important;
  }

  /* === Section Wrapper for Equal Spacing === */
  section {
    padding: 60px 20px;
    margin: 0 auto;
  }

  section+section {
    margin-top: 60px;
  }

  /* === Section Headings Consistency === */
  section h2,
  .title,
  .subcategory-title,
  #alvinaBanner {
    font-family: Arial, sans-serif;
    font-weight: 700;
    text-align: center;
    color: #4B0082;
    margin-bottom: 30px;
    text-transform: capitalize;
  }

  /* === Swiper Slider === */
  .mainSwiper {
    width: 100%;
    max-width: 100vw;
    /* full width, prevent overflow */
    margin: 0 auto;
  }

  .swiper-wrapper {
    display: flex;
  }

  .swiper-slide {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0 8px;
  }

  .slide-image-wrapper {
    width: 100%;
    height: auto;
    overflow: hidden;
  }

  .slide-image-wrapper img {
    width: 100%;
    height: auto;
    display: block;
    object-fit: contain;
    transition: transform 0.3s ease;
  }

  .slide-image-wrapper img:hover {
    transform: scale(1.05);
  }

  .swiper-pagination-bullet {
    background: #7B2CBF;
    opacity: 1;
  }

  .swiper-pagination-bullet-active {
    background: #3A0CA3;
  }

  /* === General Container === */
  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
  }

  /* === Grid Circles / Categories === */
  .grid {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    justify-content: center;
  }

  .circle-wrapper {
    text-align: center;
    width: 180px;
  }

  .circle-link {
    display: inline-block;
    width: 160px;
    height: 160px;
    overflow: hidden;
    border-radius: 50%;
    position: relative;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .circle-link:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  }

  .circle-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
  }

  .circle-link:hover .circle-img {
    transform: scale(1.1);
  }

  .circle-title {
    margin-top: 10px;
    font-size: 16px;
    font-weight: 600;
  }

  .circle-desc {
    font-size: 14px;
    color: #555;
    margin-top: 4px;
  }

  /* === Eid Grid === */
  .eid-grid {
    display: grid;
    grid-template-columns: 2.5fr 1fr 1fr;
    grid-template-rows: 250px 250px;
    grid-template-areas:
      "tall1 small1 small2"
      "tall1 small3 small3";
    gap: 20px 25px;
    width: 100%;
    margin: 0;
    padding: 0;
  }

  .eid-grid>div:nth-child(1) {
    grid-area: tall1;
  }

  .eid-grid>div:nth-child(2) {
    grid-area: small1;
  }

  .eid-grid>div:nth-child(3) {
    grid-area: small2;
  }

  .eid-grid>div:nth-child(4) {
    grid-area: small3;
  }

  .eid-grid>div {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .eid-grid>div:hover {
    transform: translateY(-6px);
    box-shadow: 0 14px 42px rgba(0, 0, 0, 0.15);
  }

  .eid-grid img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* === Product Cards / Trending === */
  .product-card {
    width: 100%;
    max-width: 400px;
    height: 450px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow: hidden;
    position: relative;
    box-shadow:
      8px 8px 25px rgba(0, 0, 0, 0.15),
      -8px -8px 25px rgba(255, 255, 255, 0.7);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .product-card:hover {
    transform: translateY(-8px);
    box-shadow:
      12px 12px 30px rgba(0, 0, 0, 0.2),
      -12px -12px 30px rgba(255, 255, 255, 0.8);
  }

  .image-container {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
  }

  .image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 20px;
    transition: all 0.3s ease;
  }

  .product-card:hover .image-container img {
    transform: scale(1.06);
    filter: brightness(0.75);
  }

  /* --- Overlay --- */
  .overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.85);
    opacity: 0;
    z-index: 2;
    text-align: center;
    color: #fff;
    transition: all 0.3s ease;
    width: 90%;
  }

  /* show overlay on hover (yeh line missing thi) */
  .product-card:hover .overlay {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }

  /* --- Overlay Button --- */
  .overlay .view-btn {
    display: inline-block;
    background: #6f42c1 !important;
    /* force if global <a> override kare */
    color: #fff !important;
    padding: 10px 20px;
    margin-top: 12px;
    font-size: 14px;
    font-weight: 700;
    border: none;
    border-radius: 8px;
    text-decoration: none !important;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.25);
  }

  .overlay .view-btn:hover {
    background: #5a328f !important;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.35);
    transform: translateY(-3px);
  }

  /* Mobile: hover nahi hota, overlay hamesha visible rakho */
  @media (hover: none) {
    .overlay {
      opacity: 1;
      transform: translate(-50%, -50%) scale(1);
    }

    .image-container img {
      filter: brightness(0.85);
    }
  }


  .a1vin-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 15px;
  }

  .a1vin-grid>div {
    position: relative;
    height: 200px;
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease-out;
  }

  .a1vin-grid>div.show {
    opacity: 1;
    transform: translateY(0);
  }

  .a1vin-grid>div:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 40px rgba(0, 0, 0, 0.15);
  }

  .a1vin-grid img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 12px;
  }

  /* --- Overlay text --- */
  .tag,
  .content {
    position: absolute;
    left: 15px;
    color: #fff;
    font-weight: 600;
    text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
  }

  .tag {
    top: 15px;
    font-size: 14px;
    background: rgba(0, 0, 0, 0.5);
    padding: 3px 8px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .content {
    bottom: 15px;
    font-size: 18px;
  }

  /* === Aims Section === */
  .aims-section {
    padding: 60px 0;
    text-align: center;
  }

  .aims-row {
    display: flex;
    justify-content: center;
    gap: 40px;
    /* thoda zyada gap for premium look */
    flex-wrap: wrap;
    padding: 20px 0;
  }

  .aim-circle {
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    padding: 20px;
  }

  .aim-circle:hover {
    transform: translateY(-8px) scale(1.05);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
  }

  /* Icon / Image inside circle */
  .aim-circle img {
    width: 70px;
    height: 70px;
    object-fit: contain;
    margin-bottom: 15px;
  }

  /* Title */
  .aim-circle h3 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
  }

  /* Subtitle or description */
  .aim-circle p {
    font-size: 14px;
    color: #666;
    line-height: 1.4;
  }

  /* === Features Section === */
  .feature-card {
    background: #fff;
    transition: transform 0.3s, box-shadow 0.3s;
  }

  .feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  }

  .feature-icon {
    font-size: 2.5rem;
    color: #6f42c1;
  }

  .feature-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
  }

  .feature-text {
    font-size: 0.95rem;
    color: #555;
  }

  /* === Newsletter === */
  #newsletter input {
    border-radius: 50px 0 0 50px;
  }

  #newsletter button {
    border-radius: 0 50px 50px 0;
    background-color: #6f42c1;
    /* purple button */
    color: #fff;
    /* text white for contrast */
    border: none;
    /* remove default border */
    transition: background 0.3s ease;
  }

  #newsletter button:hover {
    background-color: #5a32a3;
    /* darker shade on hover */
  }

  #newsletter input:focus {
    box-shadow: none;
  }

  /* === Responsive Tweaks === */
  @media (max-width: 1024px) {
    .eid-grid {
      grid-template-columns: 1fr 1fr;
      grid-template-rows: auto auto;
      grid-template-areas:
        "tall1 tall1"
        "small1 small2"
        "small3 small3";
    }
  }

  @media (max-width: 768px) {
    section {
      padding: 40px 15px;
    }

    .circle-wrapper {
      width: 140px;
    }

    .circle-link {
      width: 120px;
      height: 120px;
    }

    .aim-circle {
      width: 160px;
      height: 160px;
      padding: 15px;
    }

    .aim-circle img {
      width: 50px;
      height: 50px;
    }

    section h2,
    .title {
      font-size: 1.6rem;
      margin-bottom: 20px;
    }
  }

  @media (max-width: 480px) {

    section h2,
    .title {
      font-size: 1.4rem;
      margin-bottom: 15px;
    }

    #newsletter {
      flex-direction: column;
    }

    #newsletter input,
    #newsletter button {
      width: 100%;
      border-radius: 50px;
      margin-bottom: 8px;
    }
  }
</style>


<?php
// Fetch the 3 latest images
$stmt = $pdo->prepare("SELECT image FROM sliders ORDER BY id ASC LIMIT 3");
$stmt->execute();
$slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="swiper mainSwiper">
  <div class="swiper-wrapper">
    <?php foreach ($slides as $slide): ?>
      <div class="swiper-slide">
        <div class="slide-image-wrapper">
          <img src="images/<?= htmlspecialchars($slide['image']) ?>" alt="" class="slide-image">

        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="swiper-pagination"></div>
</div>



<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Initialize mainSwiper -->
<script>
  const mainSwiper = new Swiper('.mainSwiper', {
    loop: true,
    speed: 800,
    effect: 'slide',
    grabCursor: true,
    autoplay: {
      delay: 4000,
      disableOnInteraction: false,
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    observer: true,
    observeParents: true
  });

</script>
<?php
require 'db/conn.php';

$stmt = $pdo->prepare("SELECT * FROM products_sections WHERE section_key LIKE 'what-new%'");
$stmt->execute();
$sections = $stmt->fetchAll();
?>

<div class="container">
  <h1 class="title">🆕 What's New</h1>
  <?php if (empty($sections)): ?>
    <p style="text-align:center;">No sections found.</p>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($sections as $section): ?>
        <div class="circle-wrapper">
          <a href="view_section.php?section_id=<?= (int) $section['id'] ?>" class="circle-link"
            aria-label="<?= htmlspecialchars($section['title']) ?>">
            <img src="images/<?= htmlspecialchars($section['image']) ?>" alt="<?= htmlspecialchars($section['title']) ?>"
              class="circle-img">
          </a>
          <div class="circle-title"><?= htmlspecialchars($section['title']) ?></div>
          <div class="circle-desc"><?= htmlspecialchars($section['description']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>


<?php
require 'db/conn.php';

$stmt = $pdo->prepare("SELECT * FROM products_sections WHERE section_key LIKE '%eid%'");
$stmt->execute();
$sections = $stmt->fetchAll();
?>
<h1 class="title">🎉 Eid Sales</h1>

<div class="eid-grid">
  <?php foreach ($sections as $index => $section): ?>
    <div>
      <a href="view_section.php?section_id=<?= $section['id'] ?>"
        style="color: inherit; text-decoration: none; position: relative; display: block; height: 100%;">
        <img src="images/<?= htmlspecialchars($section['image']) ?>" alt="<?= htmlspecialchars($section['title']) ?>">
        <div class="tag">SALE</div>
        <div class="content"><?= htmlspecialchars($section['title']) ?></div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

<?php
// Database connection
require 'db/conn.php';

// Get Trending Section ID by section_key
$trendingSection = $pdo->prepare("
    SELECT id, title 
    FROM products_sections 
    WHERE section_key = 'trending' 
    LIMIT 1
");
$trendingSection->execute();
$section = $trendingSection->fetch(PDO::FETCH_ASSOC);

$trendingProducts = [];
if ($section) {
  $stmt = $pdo->prepare("
        SELECT *
        FROM products
        WHERE section_id = ?
        ORDER BY id DESC
        LIMIT 10
    ");
  $stmt->execute([$section['id']]);
  $trendingProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<section id="trending">
  <div class="trending-fullwidth">
    <h1 class="subcategory-title">Trending Products</h1>

    <div class="swiper trendingSwiper">
      <div class="swiper-wrapper">
        <?php foreach ($trendingProducts as $product): ?>
          <div class="swiper-slide">
            <div class="product-card">
              <div class="image-container">
                <img src="images/<?= htmlspecialchars($product['image']) ?>"
                  alt="<?= htmlspecialchars($product['title']) ?>">
                <div class="overlay">
                  <p>Rs <?= number_format($product['price']) ?></p>
                  <a href="product-detail.php?id=<?= (int) $product['id'] ?>&section_key=trending" class="view-btn">View
                    Detail</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Navigation -->
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>
  </div>
</section>




<script>
  const swiper = new Swiper('.trendingSwiper', {
    slidesPerView: 4,
    spaceBetween: 12,  // gap kam kiya hai
    loop: false,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      320: { slidesPerView: 1, spaceBetween: 10 },
      576: { slidesPerView: 2, spaceBetween: 10 },
      768: { slidesPerView: 3, spaceBetween: 12 },
      992: { slidesPerView: 4, spaceBetween: 12 }
    }
  })
</script>

<?php
require 'db/conn.php';

// Fetch parent category (alvina_collection)
$parent = $pdo->prepare("SELECT * FROM categories WHERE name = 'alvina_collection' LIMIT 1");
$parent->execute();
$collection = $parent->fetch(PDO::FETCH_ASSOC);

if (!$collection) {
  echo "<p style='color:red;text-align:center;'>Alvina Collection category not found.</p>";
  return;
}

// Fetch subcategories (Watches, Shoes, etc.)
$sub_stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? LIMIT 4");
$sub_stmt->execute([$collection['id']]);
$categories = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1 id="alvinaBanner" class="banner-title">✨ ALVINA COLLECTION</h1>


<div class="a1vin-grid">
  <?php foreach ($categories as $cat): ?>
    <div>
      <a href="category.php?category_id=<?= $cat['id'] ?>"
        style="display:block; height:100%; text-decoration:none; color:inherit;">
        <img src="images/<?= htmlspecialchars($cat['image']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
        <div class="tag">ALVINA</div>
        <div class="content"><?= htmlspecialchars($cat['name']) ?></div>
      </a>
    </div>
  <?php endforeach; ?>
</div>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const banner = document.getElementById("alvinaBanner");
    const cards = document.querySelectorAll(".a1vin-grid > div");

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          banner.classList.add("show");

          // Thoda delay se cards animate hon
          cards.forEach((card, index) => {
            setTimeout(() => {
              card.classList.add("show");
            }, index * 150); // staggered animation
          });

          observer.unobserve(banner);
        }
      });
    }, { threshold: 0.3 });

    observer.observe(banner);
  });
</script>





<div class="container aims-section text-center">
  <h1 class="title"><i class="bi bi-book"></i>OUR AIMS</h1>
  <div class="aims-row">
    <?php
    $aims = [
      ["icon" => "bi-bookmark", "title" => "Our Target", "desc" => "Keeping generations in love with books."],
      ["icon" => "bi-emoji-smile", "title" => "Excellent Support", "desc" => "Can't find a book? Contact us via WhatsApp or Instagram."],
      ["icon" => "bi-truck", "title" => "Flat Shipping", "desc" => "Rs. 200 across all orders."],
      ["icon" => "bi-shield-lock", "title" => "Secure Shopping", "desc" => "100% secure checkout guaranteed."]
    ];

    foreach ($aims as $aim) {
      echo '
        <div class="aim-circle">
          <i class="bi ' . $aim["icon"] . '"></i>
          <h5>' . $aim["title"] . '</h5>
          <p>' . $aim["desc"] . '</p>
        </div>';
    }
    ?>
  </div>
</div>
<div class="container my-5" id="features">
  <h1 class="title"></i>Features</h1>
  <div class="row g-4">
    <!-- Feature 1 -->
    <div class="col-md-6 col-lg-4">
      <div class="feature-card h-100 text-center p-4 shadow-sm rounded">
        <i class="bi bi-patch-check-fill feature-icon mb-3"></i>
        <h5 class="feature-title">Premium Fabric Quality</h5>
        <p class="feature-text">High-quality, breathable, and long-lasting fabrics for ultimate comfort.</p>
      </div>
    </div>

    <!-- Feature 2 -->
    <div class="col-md-6 col-lg-4">
      <div class="feature-card h-100 text-center p-4 shadow-sm rounded">
        <i class="bi bi-calendar2-week-fill feature-icon mb-3"></i>
        <h5 class="feature-title">Seasonal Collections</h5>
        <p class="feature-text">New trendy designs launched every season to keep your wardrobe fresh.</p>
      </div>
    </div>

    <!-- Feature 3 -->
    <div class="col-md-6 col-lg-4">
      <div class="feature-card h-100 text-center p-4 shadow-sm rounded">
        <i class="bi bi-rulers feature-icon mb-3"></i>
        <h5 class="feature-title">Size & Fit Guide</h5>
        <p class="feature-text">Detailed size charts and virtual try-on to ensure perfect fit every time.</p>
      </div>
    </div>

    <!-- feature 4 -->
    <div class="col-md-6 col-lg-4">
      <div class="feature-card h-100 text-center p-4 shadow-sm rounded">
        <i class="bi bi-scissors feature-icon mb-3"></i>
        <h5 class="feature-title">Free Alteration Service</h5>
        <p class="feature-text">Complimentary alterations available on selected clothing items.</p>
      </div>
    </div>

    <!-- Feature 5 -->
    <div class="col-md-6 col-lg-4">
      <div class="feature-card h-100 text-center p-4 shadow-sm rounded">
        <i class="bi bi-arrow-repeat feature-icon mb-3"></i>
        <h5 class="feature-title">Easy Exchange & Return</h5>
        <p class="feature-text">Hassle-free return or exchange within 7 days for unused items.</p>
      </div>
    </div>

    <!-- Feature 6 -->
    <div class="col-md-6 col-lg-4">
      <div class="feature-card h-100 text-center p-4 shadow-sm rounded">
        <i class="bi bi-truck feature-icon mb-3"></i>
        <h5 class="feature-title">Fast Delivery</h5>
        <p class="feature-text">Quick and reliable delivery service to your doorstep across Pakistan.</p>
      </div>
    </div>
  </div>
</div>
<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<div class="container my-5" id="newsletter">
  <div class="row justify-content-center">
    <div class="col-md-6 text-center p-4 shadow rounded" style="background-color: #F3E8FF;">
      <h3 class="mb-3">Subscribe to Our Newsletter</h3>
      <p class="mb-4">Get updates on new products, offers, and more!</p>

      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" style="background-color: #6f42c1; color: #fff;">
          <?= $_SESSION['success']; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
          <?= $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <form action="newsletter-save.php" method="POST" class="d-flex">
        <input type="email" name="email" class="form-control me-2" placeholder="Enter your email" required>
        <button type="submit" name="subscribe" class="btn btn-purple">Subscribe</button>
      </form>
    </div>
  </div>
</div>



<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">




<?php
require_once 'include/footer.php';
?>