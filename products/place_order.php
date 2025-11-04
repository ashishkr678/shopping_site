<?php
session_start();
include('../includes/header.php');
include('../includes/navbar_user.php');
include('../config/db_connect.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: ../user/login.php");
  exit;
}
?>

<div class="container py-5">
  <h2 class="fw-bold text-center text-primary mb-4">Place Your Order</h2>

  <!-- Address Input -->
  <form id="orderForm" method="POST" action="place_order_action.php" class="shadow p-4 bg-light rounded">
    <div class="mb-3">
      <label for="address" class="form-label fw-semibold">Delivery Address</label>
      <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter your full delivery address" required></textarea>
    </div>

    <h4 class="mt-4 mb-3 text-secondary">Order Summary</h4>
    <div id="order-summary"></div>

    <div class="d-flex justify-content-end mt-3">
      <h5 id="grand-total" class="fw-bold text-success"></h5>
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-4">Place Order</button>
      <a href="cart.php" class="btn btn-outline-secondary ms-2">Back to Cart</a>
    </div>
  </form>
</div>

<?php include('../includes/footer.php'); ?>

<script>
  const summaryDiv = document.getElementById("order-summary");
  const grandTotal = document.getElementById("grand-total");
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  function renderSummary() {
    if (cart.length === 0) {
      summaryDiv.innerHTML = `<p class="text-center text-muted">No items in your cart!</p>`;
      grandTotal.textContent = "";
      return;
    }

    let total = 0;
    const rows = cart.map(item => {
      const itemTotal = item.discountedPrice * item.qty;
      total += itemTotal;
      return `
        <tr>
          <td><img src="${item.imageUrl}" width="70" class="rounded shadow-sm"></td>
          <td>${item.title}</td>
          <td>${item.selectedSize || "Free Size"}</td>
          <td>₹${item.discountedPrice}</td>
          <td>${item.qty}</td>
          <td>₹${itemTotal}</td>
        </tr>`;
    }).join("");

    summaryDiv.innerHTML = `
      <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
          <thead class="table-dark">
            <tr>
              <th>Image</th><th>Product</th><th>Size</th><th>Price</th><th>Qty</th><th>Total</th>
            </tr>
          </thead>
          <tbody>${rows}</tbody>
        </table>
      </div>`;
    grandTotal.textContent = `Grand Total: ₹${total.toFixed(2)}`;
  }

  // On form submit, attach cart data
  document.getElementById("orderForm").addEventListener("submit", function(e) {
    const hidden = document.createElement("input");
    hidden.type = "hidden";
    hidden.name = "cart_data";
    hidden.value = JSON.stringify(cart);
    this.appendChild(hidden);

    localStorage.removeItem("cart"); // clear cart after order
  });

  renderSummary();
</script>
