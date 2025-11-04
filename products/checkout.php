<?php
session_start();
include('../includes/header.php');
include('../includes/navbar_user.php');
?>

<div class="container text-center py-5">
  <h2 class="fw-bold text-success mb-3">Order Confirmed!</h2>
  <p class="fs-5 text-muted">Thank you for shopping with us. Your order has been placed successfully.</p>
  <a href="home.php" class="btn btn-primary mt-3">Continue Shopping</a>
</div>

<?php include('../includes/footer.php'); ?>

<script>
  localStorage.removeItem("cart");
</script>
