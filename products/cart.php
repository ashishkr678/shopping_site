<?php
session_start();
include('../includes/header.php');
include('../includes/navbar_user.php');
?>

<div class="container py-5">
  <h2 class="fw-bold text-center text-primary mb-4">Your Shopping Cart</h2>
  <div id="cart-items"></div>
  <div class="text-center mt-4">
    <a href="place_order.php" id="checkout-btn" class="btn btn-success px-4 d-none">Proceed to Checkout</a>
  </div>
</div>

<?php include('../includes/footer.php'); ?>

<script>
  const cartContainer = document.getElementById("cart-items");
  const checkoutBtn = document.getElementById("checkout-btn");

  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  function renderCart() {
    if (cart.length === 0) {
      cartContainer.innerHTML = `<h5 class="text-center text-muted">Your cart is empty!</h5>`;
      checkoutBtn.classList.add("d-none");
      return;
    }

    let total = 0;
    const rows = cart.map((item, index) => {
      const itemTotal = item.discountedPrice * item.qty;
      total += itemTotal;

      const sizeOptions = item.sizes?.map(s => 
        `<option value="${s.name}" ${s.name === item.selectedSize ? "selected" : ""}>${s.name}</option>`
      ).join('') || `<option value="Free Size">Free Size</option>`;

      return `
        <tr>
          <td><img src="${item.imageUrl}" width="80" class="rounded shadow-sm"></td>
          <td class="fw-semibold">${item.title}</td>
          <td>
            <select class="form-select form-select-sm" onchange="updateSize(${index}, this.value)">
              ${sizeOptions}
            </select>
          </td>
          <td>₹${item.discountedPrice}</td>
          <td>
            <div class="d-flex justify-content-center align-items-center">
              <button class="btn btn-outline-secondary btn-sm me-2" onclick="changeQty(${index}, -1)">-</button>
              <span class="fw-bold">${item.qty}</span>
              <button class="btn btn-outline-secondary btn-sm ms-2" onclick="changeQty(${index}, 1)">+</button>
            </div>
          </td>
          <td>₹${itemTotal}</td>
          <td>
            <button class="btn btn-outline-danger btn-sm" onclick="removeItem(${index})">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>`;
    }).join("");

    cartContainer.innerHTML = `
      <div class="table-responsive">
        <table class="table table-bordered text-center align-middle shadow-sm">
          <thead class="table-dark">
            <tr>
              <th>Image</th>
              <th>Product</th>
              <th>Size</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Total</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>${rows}</tbody>
        </table>
      </div>
      <div class="d-flex justify-content-end mt-3">
        <h4 class="fw-bold text-success">Grand Total: ₹${total}</h4>
      </div>
    `;

    checkoutBtn.classList.remove("d-none");
  }

  function changeQty(index, delta) {
    const item = cart[index];
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
      removeItem(index);
      return;
    }
    saveCart();
  }

  function updateSize(index, newSize) {
    cart[index].selectedSize = newSize;
    saveCart();
  }

  function removeItem(index) {
    if (confirm("Remove this item from cart?")) {
      cart.splice(index, 1);
      saveCart();
    }
  }

  function saveCart() {
    localStorage.setItem("cart", JSON.stringify(cart));
    renderCart();
  }

  renderCart();
</script>

<style>
  table {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
  }
  th {
    font-size: 1rem;
    text-transform: uppercase;
  }
  td {
    vertical-align: middle !important;
  }
  .btn-outline-secondary {
    border-radius: 50%;
    width: 30px;
    height: 30px;
    padding: 0;
  }
  .fw-semibold {
    font-weight: 600;
  }
  select.form-select-sm {
    width: 100px;
    margin: 0 auto;
    cursor: pointer;
  }
</style>
