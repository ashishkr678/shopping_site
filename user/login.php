<?php
session_start();
include('../config/db_connect.php');
include('../includes/functions.php');

$page_title = "User Login";
$msg = "";

// If already logged in → redirect to home
if (isset($_SESSION['user_id'])) {
  redirect('/SHOPPING_SITE/products/home.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  if (empty($email) || empty($password)) {
    $msg = "<div class='alert alert-danger'>All fields are required.</div>";
  } else {
    $stmt = $conn->prepare("SELECT id, full_name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();

      if (password_verify($password, $user['password'])) {
        // Set session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];

        // Redirect to home page
        redirect('/SHOPPING_SITE/products/home.php');
        exit;
      } else {
        $msg = "<div class='alert alert-danger'>Invalid password! Please try again.</div>";
      }
    } else {
      $msg = "<div class='alert alert-danger'>No user found with this email address!</div>";
    }

    $stmt->close();
  }
}

include('../includes/header.php');
?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow p-4 border-0 rounded-4">
        <h3 class="text-center mb-3 text-primary fw-bold">User Login</h3>
        <?= $msg ?>
        <form method="POST" autocomplete="off">
          <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control rounded-3" placeholder="Enter your email" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control rounded-3" placeholder="Enter your password" required>
          </div>
          <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-3">Login</button>
        </form>
        <p class="mt-3 text-center">
          New user? <a href="register.php" class="text-decoration-none text-primary fw-semibold">Register here</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>

<style>
body {
  background-color: #f8f9fa;
}
.card {
  background: #ffffff;
}
.btn-primary {
  transition: 0.3s ease;
}
.btn-primary:hover {
  background-color: #0d6efd;
  transform: translateY(-1px);
}
</style>
