<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit;
}

include('../includes/header.php');
include('admin_navbar.php');
include('../config/db_connect.php');

// ✅ Fetch all orders with user info
$sql = "
  SELECT o.id, o.user_id, u.full_name, o.total_amount, o.status, o.created_at
  FROM orders o
  JOIN users u ON o.user_id = u.id
  ORDER BY o.created_at DESC
";
$result = $conn->query($sql);
?>

<div class="container mt-4">
  <h3 class="text-center fw-bold mb-4 text-primary">Manage Orders 📦</h3>

  <div id="messageBox" class="text-center mb-3"></div>

  <table class="table table-bordered table-hover align-middle text-center">
    <thead class="table-dark">
      <tr>
        <th>Order ID</th>
        <th>User</th>
        <th>Total Amount</th>
        <th>Status</th>
        <th>Placed On</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr id="order-<?= $row['id'] ?>">
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td>₹<?= number_format($row['total_amount'], 2) ?></td>
            <td class="status-cell">
              <span class="badge bg-<?=
                $row['status'] === 'DELIVERED' ? 'success' :
                ($row['status'] === 'CANCELLED' ? 'danger' :
                ($row['status'] === 'SHIPPED' ? 'info' :
                ($row['status'] === 'CONFIRMED' ? 'primary' : 'secondary')))
              ?>">
                <?= strtoupper($row['status']) ?>
              </span>
            </td>
            <td><?= date("d M Y", strtotime($row['created_at'])) ?></td>
            <td>
              <form onsubmit="updateStatus(event, <?= $row['id'] ?>)" class="d-flex justify-content-center align-items-center gap-2">
                <select name="status" class="form-select form-select-sm" style="width: 140px;">
                  <?php
                  $statuses = ['PENDING', 'CONFIRMED', 'SHIPPED', 'DELIVERED', 'CANCELLED'];
                  foreach ($statuses as $s) {
                    $selected = ($s === strtoupper($row['status'])) ? 'selected' : '';
                    echo "<option value='$s' $selected>$s</option>";
                  }
                  ?>
                </select>
                <button type="submit" class="btn btn-success btn-sm">Update</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6" class="text-muted">No orders found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
function updateStatus(e, orderId) {
  e.preventDefault();
  const form = e.target;
  const status = form.status.value;

  fetch('../api/update_order_status.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'order_id=' + orderId + '&status=' + status
  })
  .then(res => res.json())
  .then(data => {
    const msgBox = document.getElementById('messageBox');
    msgBox.innerHTML = `<div class="alert alert-${data.success ? 'success' : 'danger'} py-2">${data.message}</div>`;
    setTimeout(() => msgBox.innerHTML = '', 2500);

    if (data.success) {
      const row = document.getElementById('order-' + orderId);
      const cell = row.querySelector('.status-cell');
      const badgeClass = status === 'DELIVERED' ? 'success'
                      : status === 'CANCELLED' ? 'danger'
                      : status === 'SHIPPED' ? 'info'
                      : status === 'CONFIRMED' ? 'primary'
                      : 'secondary';
      cell.innerHTML = `<span class="badge bg-${badgeClass}">${status}</span>`;
    }
  })
  .catch(() => {
    alert('Something went wrong. Try again.');
  });
}
</script>

<?php include('../includes/footer.php'); ?>
