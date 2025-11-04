<?php
session_start();
include('../config/db_connect.php');
include('../includes/functions.php');

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  $stmt = $conn->prepare("SELECT id, full_name, password, role FROM admins WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $full_name, $hashed, $role);
    $stmt->fetch();

    if (password_verify($password, $hashed)) {
      $_SESSION['admin_id'] = $id;
      $_SESSION['admin_name'] = $full_name;
      $_SESSION['admin_role'] = $role;
      header("Location: dashboard.php");
      exit;
    } else {
      $msg = "<div class='alert alert-danger'>Invalid password!</div>";
    }
  } else {
    $msg = "<div class='alert alert-danger'>No admin found with that email!</div>";
  }
  $stmt->close();
}

$page_title = "Admin Login";
include('../includes/header.php');
?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card shadow p-4">
        <h3 class="text-center mb-3 text-danger fw-bold">Admin Login</h3>
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
          <button type="submit" class="btn btn-danger w-100">Login</button>
        </form>
        <p class="mt-3 text-center"><a href="../user/login.php">Back to User Login</a></p>
      </div>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>
