<?php
session_start();
include('../config/db_connect.php');
include('../includes/functions.php');
$page_title = "User Registration";

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $full_name = trim($_POST['full_name']);
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);
  $confirm = trim($_POST['confirm']);

  if ($password !== $confirm) {
    $msg = "<div class='alert alert-danger'>Passwords do not match!</div>";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $msg = "<div class='alert alert-danger'>Invalid email format!</div>";
  } elseif (strlen($password) < 6) {
    $msg = "<div class='alert alert-danger'>Password must be at least 6 characters!</div>";
  } else {
    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
      $msg = "<div class='alert alert-danger'>Email already exists!</div>";
    } else {
      $hashed = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $full_name, $email, $hashed);
      if ($stmt->execute()) {
        $msg = "<div class='alert alert-success text-center'>
                  Registration successful! <a href='login.php' class='alert-link'>Login now</a>
                </div>";
      } else {
        $msg = "<div class='alert alert-danger'>Error: " . htmlspecialchars($conn->error) . "</div>";
      }
      $stmt->close();
    }
    $check->close();
  }
}

include('../includes/header.php');
?>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow p-4">
        <h3 class="text-center mb-3 text-primary fw-bold">Create an Account</h3>
        <?= $msg ?>
        <form method="POST">
          <div class="mb-3">
            <label class="form-label fw-semibold">Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Confirm Password</label>
            <input type="password" name="confirm" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <p class="mt-3 text-center">Already registered? <a href="login.php">Login here</a></p>
      </div>
    </div>
  </div>
</div>
<?php include('../includes/footer.php'); ?>
