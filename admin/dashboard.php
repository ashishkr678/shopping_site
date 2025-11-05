<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit;
}

include('../includes/header.php');
include('admin_navbar.php');
include('../config/db_connect.php');
?>

<div class="container py-4">
  <h2 class="fw-bold text-center text-primary mb-4">Welcome, <?= htmlspecialchars($_SESSION['admin_name']) ?> 👋</h2>

  <div class="row g-4 text-center">
    <div class="col-md-4">
      <a href="manage_users.php" class="text-decoration-none text-dark">
        <div class="card shadow-sm p-3 border-0">
          <h5 class="fw-semibold text-secondary">Total Users</h5>
          <?php
          $res = $conn->query("SELECT COUNT(*) AS count FROM users");
          $count = $res->fetch_assoc()['count'] ?? 0;
          ?>
          <h3 class="fw-bold text-success"><?= $count ?></h3>
          <small class="text-muted">View All ➜</small>
        </div>
      </a>
    </div>


    <div class="col-md-4">
      <a href="manage_orders.php" class="text-decoration-none text-dark">
      <div class="card shadow-sm p-3 border-0">
        <h5 class="fw-semibold text-secondary">Total Orders</h5>
        <?php
        $res = $conn->query("SELECT COUNT(*) AS count FROM orders");
        $count = $res->fetch_assoc()['count'] ?? 0;
        ?>
        <h3 class="fw-bold text-primary"><?= $count ?></h3>
        <small class="text-muted">View All ➜</small>
      </div>
      </a>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm p-3 border-0">
        <h5 class="fw-semibold text-secondary">Total Products</h5>
        <?php
        $res = $conn->query("SELECT COUNT(*) AS count FROM products");
        $count = $res->fetch_assoc()['count'] ?? 0;
        ?>
        <h3 class="fw-bold text-danger"><?= $count ?></h3>
      </div>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>