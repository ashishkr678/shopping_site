<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit;
}
include('../includes/header.php');
include('admin_navbar.php');
include('../config/db_connect.php');
?>

<div class="container py-4">
  <h2 class="fw-bold text-center text-primary mb-4">Manage Users 👥</h2>
  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle text-center">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Role</th>
          <th>Registered On</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="userTable">
        <tr><td colspan="7" class="text-muted">Loading users...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<script>
// ✅ Load all users
fetch('../api/get_all_users.php')
  .then(res => res.json())
  .then(data => {
    const table = document.getElementById('userTable');
    table.innerHTML = ''; // clear "Loading users..."
    
    if (data.success && data.users.length > 0) {
      data.users.forEach(user => {
        const row = `
          <tr id="user-${user.id}">
            <td>${user.id}</td>
            <td>${user.full_name}</td>
            <td>${user.email}</td>
            <td>${user.phone ?? '-'}</td>
            <td>${user.role}</td>
            <td>${user.created_at}</td>
            <td>
              <button class="btn btn-sm btn-danger" onclick="removeUser(${user.id})">
                <i class="bi bi-trash"></i> Delete
              </button>
            </td>
          </tr>`;
        table.insertAdjacentHTML('beforeend', row);
      });
    } else {
      table.innerHTML = `<tr><td colspan="7" class="text-muted">No users found.</td></tr>`;
    }
  })
  .catch(err => {
    console.error('Error loading users:', err);
    document.getElementById('userTable').innerHTML =
      `<tr><td colspan="7" class="text-danger">Failed to load users.</td></tr>`;
  });

// ✅ Remove user (POST JSON)
function removeUser(id) {
  if (!confirm("Are you sure you want to delete this user?")) return;

  fetch('../api/remove_user.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ user_id: id })
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message);
    if (data.success) {
      const row = document.getElementById('user-' + id);
      row.style.transition = 'opacity 0.4s';
      row.style.opacity = '0';
      setTimeout(() => row.remove(), 400);
    }
  })
  .catch(err => {
    console.error('Delete failed:', err);
    alert('Error deleting user.');
  });
}
</script>

<?php include('../includes/footer.php'); ?>
