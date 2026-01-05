<?php
// cart.php
session_start();
require 'db/conn.php';

$session_id = session_id();
$user_id = $_SESSION['user_id'] ?? null;

// ---------- AJAX: handle first ----------
if (isset($_POST['action'], $_POST['id'])) {
  header('Content-Type: application/json; charset=utf-8');

  $id = (int) $_POST['id'];
  $action = $_POST['action'];
  $owner_column = $user_id ? 'user_id' : 'session_id';
  $owner_value = $user_id ?? $session_id;

  // find item for this owner
  $stmt = $pdo->prepare("SELECT quantity FROM addcart WHERE id=? AND $owner_column=?");
  $stmt->execute([$id, $owner_value]);
  $item = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$item) {
    echo json_encode(['status' => 'error', 'msg' => 'Item not found']);
    exit;
  }

  if ($action === 'delete') {
    $pdo->prepare("DELETE FROM addcart WHERE id=? AND $owner_column=?")
      ->execute([$id, $owner_value]);

    $left = $pdo->prepare("SELECT COUNT(*) FROM addcart WHERE $owner_column=?");
    $left->execute([$owner_value]);
    $cartCount = (int) $left->fetchColumn();

    echo json_encode([
      'status' => 'deleted',
      'id' => $id,
      'rows_left' => $cartCount,
      'cart_count' => $cartCount
    ]);
    exit;
  }

  if ($action === 'increase') {
    $new_qty = (int) $item['quantity'] + 1;
  } elseif ($action === 'decrease') {
    $new_qty = max(1, (int) $item['quantity'] - 1);
  } else {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid action']);
    exit;
  }

  $pdo->prepare("UPDATE addcart SET quantity=? WHERE id=? AND $owner_column=?")
    ->execute([$new_qty, $id, $owner_value]);

  // updated count for navbar
  $countStmt = $pdo->prepare("SELECT COUNT(*) FROM addcart WHERE $owner_column=?");
  $countStmt->execute([$owner_value]);
  $cartCount = (int) $countStmt->fetchColumn();

  echo json_encode([
    'status' => 'success',
    'id' => $id,
    'new_quantity' => $new_qty,
    'cart_count' => $cartCount
  ]);
  exit;
}

// ---------- Normal page load ----------
require_once 'include/header.php';

// fetch cart items for page render
$stmt = $user_id
  ? $pdo->prepare("SELECT * FROM addcart WHERE user_id = ?")
  : $pdo->prepare("SELECT * FROM addcart WHERE session_id = ?");

$stmt->execute([$user_id ?? $session_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalItems = count($cartItems);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <style>
    html,
    body {
      margin: 0;
      padding: 0;
      height: 100%;
      background: #F3E8FF !important;
      /* background color poore page ke liye */
      font-family: Arial, sans-serif;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }


    /* ✅ yeh main wrapper footer ko neeche chipkane ke liye */
    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }

    .cart-container {
      width: auto;
      max-width: 100%;
      margin: 40px;
      padding: 40px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      display: inline-block;
    }




    h1 {
      text-align: center;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    th,
    td {
      text-align: center;
      padding: 12px;
      border-bottom: 1px solid #ddd;
    }

    th {
      background: #f3f3f3;
    }

    img {
      width: 70px;
      height: 70px;
      object-fit: cover;
      border-radius: 6px;
    }

    .delete-btn {
      color: #6f42c1;
      cursor: pointer;
      font-weight: bold;
    }

    .delete-btn:hover {
      color: #5a328f;
    }

    .checkout-btn {
      display: inline-block;
      background: #6f42c1;
      color: white;
      padding: 12px 25px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
    }

    .checkout-btn:hover {
      background: #5a328f;
    }

    .empty-msg {
      text-align: center;
      padding: 40px;
      font-size: 18px;
      color: #555;
    }

    .qty-btn {
      padding: 4px 10px;
      margin: 0 5px;
      background: #6f42c1;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .qty-btn[disabled] {
      opacity: .5;
      cursor: not-allowed;
    }

    @media (max-width: 480px) {
      th:nth-child(1),
      td:nth-child(1),
      /* Hide Image */
      th:nth-child(5),
      td:nth-child(5)

      /* Hide Size */
        {
        display: none;
      }

      /* Responsive: Mobile view */
      @media (max-width: 768px) {
        .cart-container {
          padding: 10px;
          margin: 10px;
        }
  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
  <main>
    <div class="cart-container">
      <h1>Your Cart (<span id="item-count"><?php echo $totalItems; ?></span> items)</h1>

      <?php if ($totalItems > 0): ?>
        <table>
          <thead>
            <tr>
              <th>Image</th>
              <th>Product</th>
              <th>Price</th>
              <th>Qty</th>
              <th>Size</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cartItems as $item): ?>
              <tr id="row-<?php echo $item['id']; ?>">
                <td><img src="images/<?php echo htmlspecialchars($item['product_image']); ?>" alt=""></td>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td>₹<?php echo htmlspecialchars($item['product_price']); ?></td>
                <td>
                  <button class="qty-btn dec" data-id="<?php echo $item['id']; ?>" data-action="decrease" <?php echo ($item['quantity'] <= 1 ? ' disabled' : ''); ?>>-</button>
                  <span id="qty-<?php echo $item['id']; ?>"><?php echo (int) $item['quantity']; ?></span>
                  <button class="qty-btn inc" data-id="<?php echo $item['id']; ?>" data-action="increase">+</button>
                </td>
                <td><?php echo htmlspecialchars($item['size']); ?></td>
                <td><span class="delete-btn" data-id="<?php echo $item['id']; ?>">Delete</span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <a class="checkout-btn" href="checkout.php">Proceed to Checkout</a>
      <?php else: ?>
        <p class="empty-msg">Your cart is empty.</p>
      <?php endif; ?>
    </div>
  </main>
  <script>
    $(function () {
      $(document).on('click', '.qty-btn', function () {
        const $btn = $(this);
        const id = $btn.data('id');
        const action = $btn.data('action');

        $btn.prop('disabled', true);

        $.ajax({
          url: '', type: 'POST', dataType: 'json',
          data: { id: id, action: action },
          success: function (res) {
            if (res.status === 'success') {
              $('#qty-' + id).text(res.new_quantity);
              $('#nav-cart-count').text(res.cart_count); // ✅ update navbar
              const $dec = $('#row-' + id + ' .dec');
              if (res.new_quantity <= 1) $dec.prop('disabled', true);
              else $dec.prop('disabled', false);
            }
            else if (res.status === 'deleted') {
              $('#row-' + id).fadeOut(200, function () {
                $(this).remove();
                $('#item-count').text($('tbody tr').length);
                $('#nav-cart-count').text(res.cart_count); // ✅ update navbar
                if ($('tbody tr').length === 0) {
                  $('.cart-container').html('<p class="empty-msg">Your cart is empty.</p>');
                }
              });
            } else {
              alert(res.msg || 'Error updating cart');
            }
          },
          complete: function () { $btn.prop('disabled', false); }
        });
      });

      $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        $.ajax({
          url: '', type: 'POST', dataType: 'json',
          data: { id: id, action: 'delete' }
        }).done(function (res) {
          if (res.status === 'deleted') {
            $('#row-' + id).fadeOut(200, function () {
              $(this).remove();
              $('#item-count').text($('tbody tr').length);
              $('#nav-cart-count').text(res.cart_count); // ✅ update navbar
              if ($('tbody tr').length === 0) {
                $('.cart-container').html('<p class="empty-msg">Your cart is empty.</p>');
              }
            });
          } else {
            alert(res.msg || 'Delete failed');
          }
        });
      });
    });
  </script>

  <?php include 'include/footer.php'; ?>
</body>

</html>