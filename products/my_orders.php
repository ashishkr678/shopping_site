<?php
session_start();
include('../config/db_connect.php');
include('../includes/header.php');
include('../includes/navbar_user.php');

if (!isset($_SESSION['user_id'])) {
  echo "<script>window.location.href='../login.php';</script>";
  exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders & items
$query = "
    SELECT o.id AS order_id, o.total_amount, o.status, o.created_at, 
           oi.product_id, oi.product_title, oi.quantity, oi.price
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $order_id = $row['order_id'];
    if (!isset($orders[$order_id])) {
        $orders[$order_id] = [
            'order_id' => $row['order_id'],
            'total_amount' => $row['total_amount'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'items' => []
        ];
    }
    $orders[$order_id]['items'][] = [
        'product_id' => $row['product_id'],
        'product_title' => $row['product_title'],
        'quantity' => $row['quantity'],
        'price' => $row['price']
    ];
}
?>

<div class="container py-5">
  <h2 class="mb-4 fw-bold text-primary">My Orders</h2>
  <div id="ordersContainer"></div>
</div>

<?php include('../includes/footer.php'); ?>

<script type="module">
  import { mens_kurta } from "../assets/mens_kurta.js";

  const orders = <?php echo json_encode(array_values($orders)); ?>;
  const container = document.getElementById("ordersContainer");

  if (orders.length === 0) {
    container.innerHTML = `<p class="text-center text-muted">You have no orders yet.</p>`;
  } else {
    orders.forEach(order => {
      let itemsHTML = "";

      order.items.forEach(item => {
        // Find matching product in JS file
        const product = mens_kurta.find(p => p.id == item.product_id);

        // ✅ FIX: Correct image property
        const imageUrl = product ? product.imageUrl : "../assets/placeholder.png";
        const title = product ? product.title : item.product_title;

        itemsHTML += `
          <tr>
            <td><img src="${imageUrl}" alt="${title}" width="60" height="60" style="object-fit: cover; border-radius:6px;"></td>
            <td>${title}</td>
            <td>${item.quantity}</td>
            <td>₹${parseFloat(item.price).toFixed(2)}</td>
          </tr>
        `;
      });

      // ✅ Allow cancel for everything except Delivered
      const lowerStatus = order.status.toLowerCase();
      const cancelBtn = lowerStatus !== "delivered"
        ? `<button class="btn btn-danger btn-sm mt-2 cancel-order" data-id="${order.order_id}">Cancel Order</button>`
        : "";

      container.innerHTML += `
        <div class="card mb-4 shadow-sm" id="order-${order.order_id}">
          <div class="card-body">
            <h5 class="card-title">Order #${order.order_id}</h5>
            <p><strong>Status:</strong> 
              <span class="badge status-badge bg-${
                order.status === 'Pending' ? 'warning text-dark' :
                order.status === 'Cancelled' ? 'danger' :
                order.status === 'Delivered' ? 'success' : 'secondary'
              }">${order.status}</span>
            </p>
            <p><strong>Date:</strong> ${order.created_at}</p>
            <p><strong>Total:</strong> ₹${parseFloat(order.total_amount).toFixed(2)}</p>

            <div class="table-responsive">
              <table class="table table-striped align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                  </tr>
                </thead>
                <tbody>${itemsHTML}</tbody>
              </table>
            </div>
            ${cancelBtn}
          </div>
        </div>
      `;
    });
  }

  // ✅ Cancel order live update
  document.addEventListener("click", async (e) => {
    if (e.target.classList.contains("cancel-order")) {
      const button = e.target;
      const orderId = button.getAttribute("data-id");

      if (confirm("Are you sure you want to cancel this order?")) {
        button.disabled = true;
        button.textContent = "Cancelling...";

        const res = await fetch("../products/cancel_order.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ order_id: orderId })
        });

        const data = await res.json();

        if (data.success) {
          const orderCard = document.getElementById(`order-${orderId}`);
          const badge = orderCard.querySelector(".status-badge");

          badge.className = "badge status-badge bg-danger";
          badge.textContent = "Cancelled";

          button.remove();
          alert("Order cancelled successfully!");
        } else {
          button.disabled = false;
          button.textContent = "Cancel Order";
          alert(data.message || "Failed to cancel order.");
        }
      }
    }
  });
</script>
