<?php
session_start();
include('../includes/header.php');
include('../includes/navbar_user.php');
?>

<div class="container py-5">
  <h2 class="fw-bold text-center text-primary mb-4">My Orders</h2>
  <div id="orders-container"></div>
</div>

<?php include('../includes/footer.php'); ?>

<script>
document.addEventListener("DOMContentLoaded", loadOrders);

async function loadOrders() {
  const container = document.getElementById("orders-container");
  try {
    const res = await fetch("../api/get_my_orders.php");
    const orders = await res.json();

    if (orders.length === 0) {
      container.innerHTML = `<h5 class='text-center text-muted'>No orders found.</h5>`;
      return;
    }

    // Group orders
    const grouped = {};
    orders.forEach(o => {
      if (!grouped[o.order_id]) grouped[o.order_id] = { ...o, items: [] };
      grouped[o.order_id].items.push(o);
    });

    container.innerHTML = Object.values(grouped).map(order => {
      const itemsHTML = order.items.map(item => `
        <div class="d-flex align-items-center mb-3 border-bottom pb-2">
          <img src="${item.image_url}" width="80" class="rounded shadow-sm me-3">
          <div>
            <h6 class="fw-bold mb-1">${item.title}</h6>
            <p class="text-muted mb-0">Qty: ${item.quantity}</p>
            <p class="fw-semibold text-success mb-0">₹${item.price}</p>
          </div>
        </div>
      `).join('');

      const cancelBtn =
        order.status === 'DELIVERED' ? `<button class="btn btn-secondary btn-sm" disabled>Delivered</button>` :
        order.status === 'CANCELLED' ? `<button class="btn btn-danger btn-sm" disabled>Cancelled</button>` :
        `<button class="btn btn-outline-danger btn-sm" onclick="cancelOrder(${order.order_id})">Cancel Order</button>`;

      return `
        <div class="card shadow-sm mb-4">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-bold text-primary mb-0">Order #${order.order_id}</h5>
              <span class="badge bg-${order.status === 'DELIVERED' ? 'success' : order.status === 'CANCELLED' ? 'danger' : 'warning'}">${order.status}</span>
            </div>
            ${itemsHTML}
            <div class="d-flex justify-content-between mt-3">
              <h6 class="fw-bold text-success">Total: ₹${order.total_amount}</h6>
              ${cancelBtn}
            </div>
          </div>
        </div>`;
    }).join('');
  } catch (err) {
    console.error("Error loading orders:", err);
  }
}

async function cancelOrder(orderId) {
  if (!confirm("Cancel this order?")) return;
  const res = await fetch("../api/cancel_order.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ order_id: orderId })
  });
  const data = await res.json();
  alert(data.message);
  loadOrders();
}
</script>

<style>
.card { border-radius: 10px; }
.badge { font-size: 0.9rem; padding: 0.5em 0.8em; }
</style>
