<?php
include('auth_check.php');
include('../config/db_connect.php');
include('../includes/functions.php');
$page_title = "Admin Dashboard";
include('../includes/header.php');
include('../includes/navbar_admin.php');
?>

<div class="container mt-4">
  <div class="card shadow p-4">
    <h4 class="fw-bold">Admin Panel</h4>
    <p class="text-muted">Manage users, products, and orders here.</p>
    <div class="mt-3">
      <a href="../user/register.php" class="btn btn-primary me-2">Add User</a>
      <a href="../user/shop.php" class="btn btn-success me-2">View Products</a>
      <a href="#" class="btn btn-warning">Manage Orders</a>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>
