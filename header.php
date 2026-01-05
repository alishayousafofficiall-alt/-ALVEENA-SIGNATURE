<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db/conn.php'; // Adjust path according to location

// Get session info
$userId = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

$profileImage = 'default.png';
$username = 'Guest';

// Fetch user info if logged in
if ($userId) {
    $stmt = $pdo->prepare("SELECT name, profile_image FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $username = !empty($user['name']) ? $user['name'] : ($role === 'admin' ? 'Admin' : 'User');
        $profileImage = !empty($user['profile_image']) ? $user['profile_image'] : 'default.png';
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<header class="main-header">
    <div class="menu-icon" onclick="openSidebar()">&#9776;</div>
    <div class="header-title">
        <?php
        if ($role === 'admin') echo "Admin Dashboard";
        elseif ($role === 'user') echo "User Panel";
        else echo "Welcome";
        ?>
    </div>

    <!-- Profile Button -->
    <div class="profile-dropdown-toggle" id="profileBtn">
        <img src="uploads/profile_images/<?= htmlspecialchars($profileImage) ?>?t=<?= time() ?>" alt="Profile Image">
    </div>

    <!-- Profile Dropdown -->
    <div class="profile-dropdown" id="profileDropdown">
        <div class="dropdown-header">
            <img src="uploads/profile_images/<?= htmlspecialchars($profileImage) ?>?t=<?= time() ?>" alt="Profile Image">
            <p class="user-name"><?= htmlspecialchars($username) ?></p>
        </div>
        <div class="dropdown-actions">
            <?php if ($userId): ?>
                <a href="profile.php" class="btn profile-btn">Profile</a>
                <a href="logout.php" class="btn logout-btn">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn profile-btn">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Sidebar -->
<div id="mySidebar" class="sidebar">
    <span class="closebtn" onclick="closeSidebar()">&times;</span>
    <ul>
        <?php if ($role === 'admin'): ?>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="view_users.php">Manage Users</a></li>
            <li><a href="view_orders.php">View Orders</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="add_product.php">Add Product</a></li>
            <li><a href="manage-pages.php">Add Page</a></li>
            <li><a href="add-category.php">Add Categories</a></li>
            <li><a href="add-section.php">Add Section</a></li>
            <li><a href="reviews.php">Reviews</a></li>
            <li><a href="change_banner.php">🖼 Change Dashboard Banner</a></li>
        <?php elseif ($role === 'user'): ?>
            <li><a href="addcart.php">View Cart</a></li>
            <li><a href="orders.php">Your Orders</a></li>
            <li><a href="profile.php">Profile</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
        <?php endif; ?>
    </ul>
</div>

<style>
html, body {
    height: 100%; /* Ensure full viewport coverage */
    margin: 0;
    padding: 0;
    background: #F3E8FF  !important; /* Your desired light purple */
}

body::before {
    content: '';
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: #F3E8FF  !important;
    z-index: -1; /* Behind all content */
}


/* Header styling */
.main-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: linear-gradient(90deg, #6f42c1, #8a2be2);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    z-index: 1100;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Sidebar menu icon */
.menu-icon {
    font-size: 28px;
    cursor: pointer;
    transition: transform 0.3s ease;
}
.menu-icon:hover { transform: scale(1.1); }

/* Header title */
.header-title {
    font-size: 20px;
    font-weight: 600;
}

/* Profile dropdown toggle */
.profile-dropdown-toggle {
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.3s ease;
    border-radius: 8px;
    padding: 3px 5px;
}
.profile-dropdown-toggle:hover { background: rgba(255,255,255,0.1); }

/* Profile image */
.profile-dropdown-toggle img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid white;
    transition: transform 0.2s ease;
}
.profile-dropdown-toggle:hover img { transform: scale(1.1); }

/* Dropdown menu */
.profile-dropdown {
    display: none;
    position: absolute;
    top: 65px;
    right: 20px;
    background: rgba(255,255,255,0.95);
    border-radius: 12px;
    padding: 15px;
    width: 220px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    text-align: center;
    z-index: 1200;
}

/* Dropdown header (image + name) */
.dropdown-header img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #6f42c1;
    margin-bottom: 10px;
}
.dropdown-header .user-name {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
}

/* Dropdown action buttons */
.dropdown-actions a {
    display: block;
    padding: 10px 12px;
    margin: 6px 0;
    text-decoration: none;
    color: #333;
    border-radius: 8px;
    background: transparent;
    font-weight: 500;
    transition: all 0.3s ease;
}
.dropdown-actions a.profile-btn:hover { background: #6f42c1; color: #fff; transform: translateY(-2px); }
.dropdown-actions a.logout-btn:hover { background: #f39c12; color: #fff; transform: translateY(-2px); }

/* Show dropdown */
.profile-dropdown.show { display: block; }

/* Sidebar styling */
.sidebar {
    height: 100%;
    width: 0;
    position: fixed;
    top: 0;
    left: 0;
    background: #6f42c1;
    overflow-x: hidden;
    transition: 0.3s;
    padding-top: 60px;
    z-index: 1200;
}
.sidebar ul { list-style: none; padding: 0; margin: 0; }
.sidebar li a {
    padding: 12px 20px;
    color: #fff;
    display: block;
    font-weight: 500;
    text-decoration: none;
    transition: 0.3s;
}
.sidebar li a:hover { background: #F3E8FF; color: #6f42c1; }
.closebtn {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 30px;
    cursor: pointer;
    color: white;
}
</style>



<script>
function openSidebar() { document.getElementById("mySidebar").style.width = "250px"; }
function closeSidebar() { document.getElementById("mySidebar").style.width = "0"; }

const profileBtn = document.getElementById('profileBtn');
const profileDropdown = document.getElementById('profileDropdown');
if (profileBtn) {
    profileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        profileDropdown.classList.toggle('show');
    });
}
document.body.addEventListener('click', () => {
    if(profileDropdown) profileDropdown.classList.remove('show');
});
</script>

