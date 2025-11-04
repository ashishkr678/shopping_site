<?php
session_start();
include('../includes/header.php');
include('../includes/navbar_user.php');
include('../config/db_connect.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: ../user/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<div class="container py-5">
  <h2 class="fw-bold text-center text-primary mb-4">My Orders</h2>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success text-center">✅ Order placed successfully!</div>
  <?php endif; ?>

  <?php if ($orders->num_rows > 0): ?>
    <?php while ($order = $orders->fetch_assoc()): ?>
      <div class="card mb-4 shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between">
          <span>Order #<?= $order['id'] ?></span>
          <span>Status: <b><?= ucfirst($order['status']) ?></b></span>
        </div>
        <div class="card-body">
          <p><b>Placed on:</b> <?= date("d M Y, h:i A", strtotime($order['created_at'])) ?></p>
          <p><b>Total:</b> ₹<?= number_format($order['total_amount'], 2) ?></p>

          <!-- Fetch order items -->
          <?php
            $item_sql = "SELECT oi.*, p.name AS product_name, p.image AS product_image 
                         FROM order_items oi 
                         JOIN products p ON oi.product_id = p.id 
                         WHERE oi.order_id = ?";
            $item_stmt = $conn->prepare($item_sql);
            $item_stmt->bind_param("i", $order['id']);
            $item_stmt->execute();
            $items = $item_stmt->get_result();
          ?>

          <div class="table-responsive mt-3">
            <table class="table table-bordered text-center align-middle">
              <thead class="table-secondary">
                <tr>
                  <th>Image</th>
                  <th>Product</th>
                  <th>Qty</th>
                  <th>Price</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                  <tr>
                    <td><img src="<?= htmlspecialchars($item['product_image']) ?>" width="70" style="border-radius:8px;"></td>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₹<?= number_format($item['price'], 2) ?></td>
                    <td>₹<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <h5 class="text-center text-muted">You haven’t placed any orders yet.</h5>
  <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
