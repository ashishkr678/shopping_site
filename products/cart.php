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
  document.addEventListener("DOMContentLoaded", loadCart);

  async function loadCart() {
    const cartContainer = document.getElementById("cart-items");
    const checkoutBtn = document.getElementById("checkout-btn");

    try {
      const res = await fetch("../api/get_cart.php");
      const cart = await res.json();

      if (cart.length === 0) {
        cartContainer.innerHTML = `<h5 class='text-center text-muted'>Your cart is empty!</h5>`;
        checkoutBtn.classList.add("d-none");
        return;
      }

      let total = 0;
      const rows = cart.map(item => {
        const itemTotal = item.discounted_price * item.quantity;
        total += itemTotal;
        return `
        <tr data-cart-id="${item.cart_id}">
          <td><img src="${item.image_url}" width="80" class="rounded shadow-sm"></td>
          <td>${item.title}</td>
          <td>${item.selected_size || "Free Size"}</td>
          <td>₹${item.discounted_price}</td>
          <td>
            <div class="d-flex justify-content-center align-items-center gap-2">
              <button class="btn btn-outline-secondary btn-sm qty-btn" data-action="decrease">−</button>
              <span class="fw-semibold">${item.quantity}</span>
              <button class="btn btn-outline-secondary btn-sm qty-btn" data-action="increase">+</button>
            </div>
          </td>
          <td class="item-total">₹${itemTotal}</td>
          <td>
            <button class="btn btn-outline-danger btn-sm" onclick="removeItem(${item.cart_id})">
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
              <th>Qty</th>
              <th>Total</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>${rows}</tbody>
        </table>
      </div>
      <div class="d-flex justify-content-end mt-3">
        <h4 class="fw-bold text-success">Grand Total: ₹<span id="grandTotal">${total}</span></h4>
      </div>
    `;
      checkoutBtn.classList.remove("d-none");

      attachQuantityListeners();
    } catch (err) {
      console.error("Error loading cart:", err);
    }
  }

  function attachQuantityListeners() {
    document.querySelectorAll(".qty-btn").forEach(btn => {
      btn.addEventListener("click", async () => {
        const row = btn.closest("tr");
        const cartId = row.dataset.cartId;
        const qtySpan = row.querySelector("span");
        let qty = parseInt(qtySpan.innerText);

        if (btn.dataset.action === "increase") qty++;
        else if (btn.dataset.action === "decrease" && qty > 1) qty--;

        qtySpan.innerText = qty;
        await updateQuantity(cartId, qty);
        updateTotals(row, qty);
      });
    });
  }

  async function updateQuantity(cartId, qty) {
    try {
      const formData = new FormData();
      formData.append("cart_id", cartId);
      formData.append("quantity", qty);

      const res = await fetch("../api/update_cart_quantity.php", {
        method: "POST",
        body: formData
      });

      const text = await res.text();
      try {
        const data = JSON.parse(text);
        if (!data.success) alert(data.message || "Failed to update quantity");
      } catch {
        console.error("Invalid JSON response:", text);
        alert("⚠️ Server error while updating quantity.");
      }
    } catch (err) {
      console.error("Quantity update error:", err);
      alert("❌ Failed to update quantity. Try again!");
    }
  }


  function updateTotals(row, qty) {
    const price = parseFloat(row.children[3].innerText.replace("₹", ""));
    const itemTotal = price * qty;
    row.querySelector(".item-total").innerText = "₹" + itemTotal;

    let total = 0;
    document.querySelectorAll(".item-total").forEach(td => {
      total += parseFloat(td.innerText.replace("₹", ""));
    });
    document.getElementById("grandTotal").innerText = total;
  }

  async function removeItem(cartId) {
    if (!confirm("Remove this item?")) return;
    const formData = new FormData();
    formData.append("cart_id", cartId);
    const res = await fetch("../api/remove_from_cart.php", {
      method: "POST",
      body: formData
    });
    const data = await res.json();
    alert(data.message);
    loadCart();
  }
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
    width: 32px;
    height: 32px;
    padding: 0;
    font-weight: bold;
  }

  .fw-semibold {
    font-weight: 600;
  }
</style>