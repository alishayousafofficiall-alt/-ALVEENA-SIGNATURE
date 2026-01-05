<?php
session_start();
require_once 'db/conn.php';

$session_id = session_id();
$buy_now_product_id = isset($_GET['buy_now']) ? intval($_GET['buy_now']) : null;

/** -----------------------------
 *  Fetch cart/buy-now items
 *  ----------------------------- */
$cartItems = [];

if ($buy_now_product_id) {
    $stmt = $pdo->prepare("SELECT id, title, price, image FROM products WHERE id = ?");
    $stmt->execute([$buy_now_product_id]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cartItems[] = [
            'title'    => $row['title'],
            'price'    => (float)$row['price'],
            'image'    => $row['image'],
            'quantity' => 1
        ];
    }
} else {
    $stmt = $pdo->prepare("SELECT id, product_name AS title, product_price AS price, product_image AS image, quantity
                           FROM addcart WHERE session_id = ?");
    $stmt->execute([$session_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/** -----------------------------
 *  Place Order
 *  ----------------------------- */
if (isset($_POST['order_btn'])) {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $method  = $_POST['method'] ?? 'COD';
    $created_at = date('Y-m-d H:i:s');

    $total = 0;
    $details = [];

    foreach ($cartItems as $item) {
        $quantity = isset($item['quantity']) && (int)$item['quantity'] > 0 ? (int)$item['quantity'] : 1;
        $title    = (string)$item['title'];
        $price    = (float)$item['price'];

        $t = mb_strtolower($title);
        if ((strpos($t, 'mom') !== false && strpos($t, 'daughter') !== false) ||
            (strpos($t, 'father') !== false && strpos($t, 'son') !== false)) {
            $price *= 2;
        }

        $subtotal = $price * $quantity;
        $total   += $subtotal;
        $details[] = "{$title} (Qty: {$quantity})";
    }

    $orderDetails = implode(', ', $details);

    $stmt = $pdo->prepare("INSERT INTO orders
        (session_id, name, email, phone, address, total_price, order_details, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$session_id, $name, $email, $phone, $address, $total, $orderDetails, $created_at]);

    if (!$buy_now_product_id) {
        $pdo->prepare("DELETE FROM addcart WHERE session_id = ?")->execute([$session_id]);
    }

    header("Location: success.php");
    exit;
}

// 👉 Only include header AFTER logic
require_once 'include/header.php';
?>


<style>
  html, body {
  margin: 0;
  padding: 0;
  height: 100%;
  background:  #F3E8FF!important;
}
.page-flex {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 40px 20px;
}
.checkout-container {
  display: flex;
  flex-wrap: wrap;
  gap: 40px;
  background: #fff;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
  width: 90%;
  max-width: 1200px;
  margin: 40px auto;
}

.form-section {
  flex: 1.2;
}

.summary-section {
  flex: 0.8;
  border-left: 1px solid #eee;
  padding-left: 40px;
}

h2, h3 {
  font-size: 24px;
  font-weight: 600;
  margin-bottom: 25px;
  color: #222;
}

input, select, textarea {
  width: 100%;
  padding: 14px 16px;
  margin-bottom: 18px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 15px;
  background: #fafafa;
  transition: all 0.3s ease;
}

input:focus, select:focus, textarea:focus {
  border-color: #6c63ff;
  background: #fff;
  outline: none;
  box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.1);
}

button {
  width: 100%;
  padding: 14px;
  background:#6f42c1;
  color: #fff;
  font-size: 16px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s ease, background 0.3s ease;
}

button:hover {
  transform: translateY(-2px);
  background: #6f42c1;
}

.order-item {
  display: flex;
  margin-bottom: 20px;
  background: #fdfdfd;
  border: 1px solid #eee;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 0 5px rgba(0,0,0,0.05);
}

.order-item img {
  width: 90px;
  height: 110px;
  object-fit: cover;
  border-right: 1px solid #eee;
}

.order-details {
  padding: 10px 15px;
  flex: 1;
}

.order-details p {
  margin: 4px 0;
  font-size: 14px;
  color: #333;
}

.total {
  text-align: right;
  margin-top: 20px;
  font-size: 18px;
  font-weight: bold;
  color: #000;
  border-top: 1px solid #eee;
  padding-top: 10px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .checkout-container {
    flex-direction: column;
    padding: 20px;
    gap: 20px;
  }
  .summary-section {
    border-left: none;
    padding-left: 0;
    border-top: 1px solid #eee;
    padding-top: 20px;
  }
}


</style>

<div class="page-flex">
  <div class="checkout-container">
    <!-- Checkout Form -->
    <div class="form-section">
      <h2>Checkout</h2>
      <form method="post">
        <input type="text" name="name" required placeholder="Your Name">
        <input type="email" name="email" required placeholder="Your Email">
        <input type="text" name="phone" required placeholder="Your Phone">
        <input type="text" name="address" required placeholder="Shipping Address">
        <select name="method" required>
          <option value="COD">Cash on Delivery</option>
          <option value="Card">Credit Card</option>
        </select>
        <button type="submit" name="order_btn">Place Order</button>
      </form>
    </div>

    <!-- Order Summary -->
    <div class="summary-section">
      <h3>Order Summary</h3>
      <?php
      $grandTotal = 0.0;

      if (empty($cartItems)) {
          echo '<p>Your cart is empty.</p>';
      } else {
          foreach ($cartItems as $item):
              $qty   = isset($item['quantity']) && (int)$item['quantity'] > 0 ? (int)$item['quantity'] : 1;
              $title = (string)$item['title'];
              $price = (float)$item['price'];

              $t = mb_strtolower($title);
              if ((strpos($t, 'mom') !== false && strpos($t, 'daughter') !== false) ||
                  (strpos($t, 'father') !== false && strpos($t, 'son') !== false)) {
                  $price *= 2;
              }

              $subtotal   = $price * $qty;
              $grandTotal += $subtotal;
      ?>
        <div class="order-item">
          <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($title) ?>">
          <div class="order-details">
            <p><strong><?= htmlspecialchars($title) ?></strong></p>
            <p>Quantity: <?= $qty ?></p>
            <p>Price: PKR <?= number_format($price) ?></p>
            <p>Subtotal: PKR <?= number_format($subtotal) ?></p>
          </div>
        </div>
      <?php
          endforeach;
      }
      ?>

      <div class="total">
        Total: PKR <?= number_format($grandTotal) ?>
      </div>
    </div>
  </div> <!-- /checkout-container -->
</div> <!-- /page-flex -->

<?php require_once 'include/footer.php'; // footer outside container, uses site-wide styles ?>
