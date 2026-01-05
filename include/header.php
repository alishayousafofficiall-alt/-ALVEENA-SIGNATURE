<?php
if (session_status() === PHP_SESSION_NONE)
  session_start();
require 'db/conn.php';

// Get main categories
$mainCategories = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL")
  ->fetchAll(PDO::FETCH_ASSOC);

// Cart item count
$session_id = session_id();
$user_id = $_SESSION['user_id'] ?? null;
$owner_column = $user_id ? 'user_id' : 'session_id';
$owner_value = $user_id ?? $session_id;

$stmt = $pdo->prepare("SELECT SUM(quantity) AS total_items FROM addcart WHERE $owner_column=?");
$stmt->execute([$owner_value]);
$cartCount = (int) ($stmt->fetchColumn() ?? 0);
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alveena Signature</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


  <style>
    /* GLOBAL */
    a {
      text-decoration: none;
      color: inherit;
    }

    .top-navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #6f42c1;
      padding: 10px 20px;
      flex-wrap: wrap;
    }

    .top-navbar .logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .top-navbar .logo img {
      width: 40px;
      height: 40px;
      border-radius: 4px;
    }

    .top-navbar .logo span {
      font-weight: 700;
      font-size: 1.2rem;
      color: #fff;
    }

    .top-navbar .search-container {
      position: relative;
      flex: 1;
      max-width: 400px;
      margin: 5px 20px;
    }

    .top-navbar .search-container input {
      width: 100%;
      padding: 6px 30px 6px 10px;
      border-radius: 4px;
      border: none;
      font-size: 1rem;
    }

    .top-navbar .search-container .fa-search {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #666;
      cursor: pointer;
    }

    #searchResults {
      position: absolute;
      top: 110%;
      left: 0;
      width: 100%;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 6px;
      max-height: 300px;
      overflow-y: auto;
      display: none;
      z-index: 1000;
    }

    #searchResults .result-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px;
      cursor: pointer;
      border-bottom: 1px solid #f0f0f0;
      transition: 0.3s;
    }

    #searchResults .result-item:hover {
      background: #e9f0ff;
    }

    #searchResults .result-item img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 6px;
    }

    #searchResults .result-item span {
      font-weight: 600;
      color: #333;
    }

    /* NAV LINKS */
    .top-navbar .nav-links {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }

    .top-navbar .nav-links a {
      display: flex;
      align-items: center;
      gap: 5px;
      color: #fff;
      font-weight: 600;
      padding: 6px 12px;
      border-radius: 4px;
      transition: 0.3s;
    }

    .top-navbar .nav-links a:hover {
      background: #fbe9e7;
      color: #333;
    }

    /* CATEGORIES NAVBAR */
    .navbar-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #F3E8FF;
      padding: 10px 20px;
      flex-wrap: wrap;
      color: #333;
    }

    .categories-navbar {
      list-style: none;
      display: flex;
      gap: 20px;
      justify-content: center;
      flex: 1;
      margin: 0;
      padding: 0;
      flex-wrap: wrap;
    }

    .categories-navbar>li {
      position: relative;
    }

    .categories-navbar>li>a {
      color: #333;
      font-weight: 600;
      padding: 8px 12px;
      display: block;
      border-radius: 4px;
      transition: 0.3s;
    }

    .categories-navbar>li:hover>a {
      background: #d1c4ff;
    }

    /* DROPDOWN */
    .dropdown-menu {
      display: none;
      position: absolute;
      top: 100%;
      left: 50%;
      transform: translateX(-50%);
      background: #d1c4ff;
      color: #333;
      padding: 15px;
      width: 600px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      border-radius: 6px;
      z-index: 1000;
      flex-direction: column;
    }

    .categories-navbar>li.active>.dropdown-menu {
      display: flex;
    }

    .dropdown-content {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    .main-image img {
      width: 200px;
      height: 200px;
      object-fit: cover;
      border-radius: 10px;
    }

    .subcategories {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .subcategory-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 5px;
      border-radius: 6px;
      transition: 0.3s;
      color: #333;
    }

    .subcategory-item img {
      width: 50px;
      height: 50px;
      border-radius: 6px;
      object-fit: cover;
    }

    .subcategory-item:hover {
      background: #f0f0f0;
    }

    .category-description {
      margin-top: 10px;
      font-size: 13px;
      color: #666;
      border-top: 1px solid #ddd;
      padding-top: 10px;
    }

    /* AUTH LINKS */
    .auth-links {
      display: flex;
      gap: 15px;
      flex-shrink: 0;
    }

    .auth-links a {
      color: #333;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 5px;
      padding: 5px 8px;
      border-radius: 4px;
      transition: 0.3s;
    }

    .auth-links a:hover {
      background: #d1c4ff;
    }

    /* RESPONSIVE */
    @media(max-width:768px) {

      .top-navbar,
      .navbar-container {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
      }

      .top-navbar .search-container {
        max-width: 100%;
        margin: 0;
        width: 100%;
      }

      .nav-links,
      .auth-links {
        justify-content: space-around;
        flex-wrap: wrap;
      }

      .dropdown-menu {
        position: static;
        width: 100%;
        box-shadow: none;
        display: none;
      }

      .categories-navbar>li.active>.dropdown-menu {
        display: flex;
        flex-direction: column;
      }
    }

    @media(max-width:480px) {
      .top-navbar .logo span {
        font-size: 1rem;
      }

      .top-navbar .nav-links a,
      .auth-links a {
        font-size: 14px;
        padding: 5px 8px;
      }

      .top-navbar .search-container input {
        font-size: 14px;
      }

      .main-image img {
        width: 150px;
        height: 150px;
      }

      .subcategory-item img {
        width: 40px;
        height: 40px;
      }
    }

    /* Show dropdown on hover for desktop */
    @media(min-width:769px) {
      .categories-navbar>li:hover>.dropdown-menu {
        display: flex;
      }
    }
  </style>
</head>

<body>

  <?php
  $stmt = $pdo->prepare("SELECT image FROM sliders WHERE image LIKE 'logo.%' LIMIT 1");
  $stmt->execute();
  $logo = $stmt->fetchColumn() ?: 'default-logo.jpg';
  ?>

  <!-- TOP NAVBAR -->
  <div class="top-navbar">
    <div class="logo">
      <a href="index.php"><img src="images/<?php echo htmlspecialchars($logo); ?>" alt="Logo"></a>
      <span>ALVEENA SIGNATURE</span>
    </div>
    <div class="search-container">
      <input type="text" id="searchInput" placeholder="Search categories...">
      <i class="fa fa-search"></i>
      <div id="searchResults"></div>
    </div>
    <div class="nav-links">
      <a href="home.php"><i class="fa fa-home"></i> Home</a>
      <a href="page.php?slug=about"><i class="fa fa-info-circle"></i> About</a>
      <a href="contact.php?slug=contact"><i class="fa fa-envelope"></i> Contact</a>
    </div>
  </div>

  <!-- CATEGORIES NAVBAR -->
  <div class="navbar-container">
    <ul class="categories-navbar">
      <?php foreach ($mainCategories as $mainCat):
        $childStmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id=?");
        $childStmt->execute([$mainCat['id']]);
        $children = $childStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <li>
          <a href="#"><?= htmlspecialchars($mainCat['name']) ?></a>
          <?php if ($children): ?>
            <div class="dropdown-menu">
              <div class="dropdown-content">
                <div class="main-image">
                  <?php if (!empty($mainCat['image'])): ?>
                    <img src="images/<?= htmlspecialchars($mainCat['image']) ?>"
                      alt="<?= htmlspecialchars($mainCat['name']) ?>">
                  <?php endif; ?>
                </div>
                <div class="subcategories">
                  <?php foreach ($children as $child): ?>
                    <a href="category.php?category_id=<?= $child['id'] ?>" class="subcategory-item">
                      <?php if (!empty($child['image'])): ?>
                        <img src="images/<?= htmlspecialchars($child['image']) ?>"
                          alt="<?= htmlspecialchars($child['name']) ?>">
                      <?php endif; ?>
                      <span><?= htmlspecialchars($child['name']) ?></span>
                    </a>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="category-description"><?= htmlspecialchars($mainCat['description']) ?></div>
            </div>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="auth-links">
      <a href="login.php"><i class="fa fa-user"></i> Login</a>
      <a href="cart.php">
        Cart (<span id="nav-cart-count"><?php echo $cartCount; ?></span>)
      </a>

    </div>
  </div>

  <script>
    // MOBILE DROPDOWN TOGGLE
    document.addEventListener('DOMContentLoaded', () => {
      const categoryLinks = document.querySelectorAll('.categories-navbar>li>a');
      categoryLinks.forEach(link => {
        link.addEventListener('click', e => {
          if (window.innerWidth <= 768) {
            e.preventDefault();
            const parent = link.parentElement;
            // close other dropdowns
            document.querySelectorAll('.categories-navbar>li').forEach(li => {
              if (li !== parent) li.classList.remove('active');
            });
            parent.classList.toggle('active');
          }
        });
      });
    });


    // SEARCH FUNCTIONALITY
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    let debounceTimer;
    searchInput.addEventListener('input', () => {
      clearTimeout(debounceTimer);
      const query = searchInput.value.trim();
      if (!query) { searchResults.style.display = 'none'; searchResults.innerHTML = ''; return; }
      debounceTimer = setTimeout(() => {
        fetch(`search_categories.php?q=${encodeURIComponent(query)}`)
          .then(res => res.json())
          .then(data => {
            if (!data.length) {
              searchResults.innerHTML = '<div style="padding:10px;text-align:center;color:#666;">No categories found.</div>';
              searchResults.style.display = 'block'; return;
            }
            searchResults.innerHTML = data.map(cat => `
                    <div class="result-item" data-id="${cat.id}">
                        <img src="images/${cat.image || 'default.jpg'}" alt="${cat.name}">
                        <span>${cat.name}</span>
                    </div>
                `).join('');
            searchResults.style.display = 'block';
            document.querySelectorAll('#searchResults .result-item').forEach(item => {
              item.addEventListener('click', () => {
                const id = item.getAttribute('data-id');
                window.location.href = `category.php?category_id=${id}`;
              });
            });
          }).catch(err => console.error(err));
      }, 300);
    });
    document.addEventListener('click', e => {
      if (!searchResults.contains(e.target) && e.target !== searchInput) {
        searchResults.style.display = 'none';
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>