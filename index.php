<?php
include('includes/header.php');
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">🛒 ShopEase</a>
  </div>
</nav>

<div class="container text-center mt-5">
  <h1 class="text-primary mb-3 fw-bold">Welcome to ShopEase</h1>
  <p class="lead">Please choose your login panel to continue</p>

  <div class="mt-4 d-flex justify-content-center gap-4">
    <a href="user/login.php" class="btn btn-success btn-lg px-4">User Login</a>
    <a href="admin/login.php" class="btn btn-dark btn-lg px-4">Admin Login</a>
  </div>
</div>
<?php include('includes/footer.php'); ?>
