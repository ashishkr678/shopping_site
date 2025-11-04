<?php
session_start();
include('../includes/functions.php');
if (!isset($_SESSION['user_id'])) redirect('login.php');
$page_title = "User Dashboard";
include('../includes/header.php');
include('../includes/navbar_user.php');
?>
<div class="container mt-5 text-center">
  <h2 class="fw-bold text-primary">Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h2>
  <p class="mt-3">You are now logged in successfully.</p>
  <a href="../shop.php" class="btn btn-success mt-3">Go to Shop</a>
  <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
</div>
<?php include('../includes/footer.php'); ?>
