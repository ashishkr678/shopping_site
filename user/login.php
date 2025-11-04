<?php
session_start();
include('../config/db_connect.php');
include('../includes/functions.php');
$page_title = "User Login";

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_name'] = $user['full_name'];
      redirect('/SHOPPING_SITE/products/home.php');
    } else {
      $msg = "<div class='alert alert-danger'>Invalid password!</div>";
    }
  } else {
    $msg = "<div class='alert alert-danger'>No user found with this email!</div>";
  }
  $stmt->close();
}

include('../includes/header.php');
?>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow p-4">
        <h3 class="text-center mb-3 text-primary fw-bold">User Login</h3>
        <?= $msg ?>
        <form method="POST">
          <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="mt-3 text-center">New user? <a href="register.php">Register here</a></p>
      </div>
    </div>
  </div>
</div>
<?php include('../includes/footer.php'); ?>
