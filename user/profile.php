<?php
session_start();
include('../config/db_connect.php');
include('../includes/header.php');
include('../includes/navbar_user.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";

// ✅ Update user profile
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $full_name = trim($_POST['full_name']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);

  $stmt = $conn->prepare("UPDATE users SET full_name=?, phone=?, address=? WHERE id=?");
  $stmt->bind_param("sssi", $full_name, $phone, $address, $user_id);
  if ($stmt->execute()) {
    $_SESSION['user_name'] = $full_name;
    $msg = "<div class='alert alert-success text-center'>Profile updated successfully!</div>";
  } else {
    $msg = "<div class='alert alert-danger text-center'>Failed to update profile!</div>";
  }
  $stmt->close();
}

// ✅ Fetch current user data
$stmt = $conn->prepare("SELECT full_name, email, phone, address, role, created_at FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<div class="container py-5">
  <h2 class="text-center fw-bold text-primary mb-4">My Profile</h2>
  <?= $msg ?>

  <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
    <form method="POST">
      <div class="mb-3">
        <label class="form-label fw-semibold">Full Name</label>
        <div class="d-flex">
          <input type="text" name="full_name" id="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" readonly>
          <button type="button" class="btn btn-outline-primary ms-2" onclick="enableEdit('full_name')">Edit</button>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Phone</label>
        <div class="d-flex">
          <input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" readonly>
          <button type="button" class="btn btn-outline-primary ms-2" onclick="enableEdit('phone')">Edit</button>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Address</label>
        <div class="d-flex">
          <textarea name="address" id="address" class="form-control" rows="2" readonly><?= htmlspecialchars($user['address']) ?></textarea>
          <button type="button" class="btn btn-outline-primary ms-2" onclick="enableEdit('address')">Edit</button>
        </div>
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-success px-5">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<?php include('../includes/footer.php'); ?>

<script>
function enableEdit(id) {
  const field = document.getElementById(id);
  field.removeAttribute('readonly');
  field.focus();
  field.classList.add('border-primary');
}
</script>
