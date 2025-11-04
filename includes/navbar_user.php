<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand fw-bold" href="../products/home.php">MyShop</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <!-- ✅ Home -->
        <li class="nav-item">
          <a class="nav-link" href="../products/home.php">Home</a>
        </li>

        <!-- ✅ Cart -->
        <li class="nav-item">
          <a class="nav-link" href="../products/cart.php">
            <i class="bi bi-cart-fill"></i> Cart
          </a>
        </li>

        <!-- ✅ My Orders (currently checkout) -->
        <li class="nav-item">
          <a class="nav-link" href="../products/my_orders.php">My Orders</a>
        </li>

        <!-- ✅ User Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userMenu" data-bs-toggle="dropdown">
            <?= $_SESSION['user_name'] ?? 'User' ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="../user/profile.php">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="../user/logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
