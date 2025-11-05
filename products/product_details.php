<?php
session_start();
include('../includes/header.php');
include('../includes/navbar_user.php');
include('../config/db_connect.php');
?>

<div class="container py-5" id="product-detail"></div>

<?php include('../includes/footer.php'); ?>

<script>
document.addEventListener("DOMContentLoaded", async () => {
  const params = new URLSearchParams(window.location.search);
  const id = params.get('id');
  const container = document.getElementById('product-detail');

  if (!id) {
    container.innerHTML = `<h4 class="text-center text-danger">Invalid Product ID!</h4>`;
    return;
  }

  try {
    const response = await fetch(`../api/get_product_details.php?id=${id}`);
    const product = await response.json();

    if (!product || !product.id) {
      container.innerHTML = `<h4 class="text-center text-danger">Product not found!</h4>`;
      return;
    }

    container.innerHTML = `
      <div class="row gy-4">
        <div class="col-md-5 text-center">
          <img src="${product.image_url}" class="img-fluid rounded shadow-sm border"
               alt="${product.title}" style="max-height: 500px; object-fit: cover;">
        </div>
        <div class="col-md-7">
          <h3 class="fw-bold mb-1">${product.title}</h3>
          <h6 class="text-muted mb-3">${product.brand || ''}</h6>
          <h4 class="text-success fw-bold mb-2">
            ₹${product.discounted_price}
            <span class="text-decoration-line-through text-muted fs-6 ms-1">₹${product.price}</span>
            <span class="text-danger fs-6 ms-1">(${product.discount_percent}% OFF)</span>
          </h4>
          <p class="text-secondary">${product.description || ''}</p>

          <!-- Quantity Selector -->
          <div class="mt-4">
            <label class="fw-semibold mb-2 d-block">Quantity:</label>
            <div class="input-group w-25">
              <button class="btn btn-outline-secondary" id="decrease">-</button>
              <input type="text" id="quantity" class="form-control text-center" value="1" readonly>
              <button class="btn btn-outline-secondary" id="increase">+</button>
            </div>
          </div>

          <!-- Buttons -->
          <div class="mt-4">
            <button id="add-to-cart" class="btn btn-primary me-2 px-4">
              <i class="bi bi-cart-plus"></i> Add to Cart
            </button>
            <a href="cart.php" class="btn btn-success px-4">
              <i class="bi bi-bag-check"></i> Go to Cart
            </a>
          </div>
        </div>
      </div>
    `;

    const qtyInput = document.getElementById('quantity');
    document.getElementById('increase').addEventListener('click', () => {
      qtyInput.value = parseInt(qtyInput.value) + 1;
    });
    document.getElementById('decrease').addEventListener('click', () => {
      if (parseInt(qtyInput.value) > 1) qtyInput.value = parseInt(qtyInput.value) - 1;
    });

    // ✅ Add to Cart (DB + toast)
    document.getElementById("add-to-cart").addEventListener("click", async () => {
      const quantity = parseInt(qtyInput.value);

      try {
        const res = await fetch("../api/add_to_cart.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            product_id: product.id,
            quantity: quantity
          })
        });
        const data = await res.json();

        if (data.success) {
          showToast("items added to cart successfully");
        } else if (data.message === "User not logged in") {
          window.location.href = "../user/login.php";
        } else {
          showToast(`⚠️ ${data.message}`, true);
        }
      } catch (err) {
        console.error(err);
        showToast("Failed to add to cart!", true);
      }
    });

  } catch (error) {
    console.error(error);
    container.innerHTML = `<p class="text-center text-danger">Error loading product details.</p>`;
  }
});

function showToast(msg, error = false) {
  const toast = document.createElement('div');
  toast.className = `toast-msg position-fixed top-0 end-0 m-3 ${error ? 'bg-danger' : 'bg-success'} text-white px-3 py-2 rounded shadow`;
  toast.style.zIndex = "1055";
  toast.innerHTML = msg;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 2000);
}
</script>

<style>
.toast-msg {
  animation: fadeInOut 2s ease;
}
@keyframes fadeInOut {
  0% {opacity: 0; transform: translateY(-10px);}
  10%,90% {opacity: 1; transform: translateY(0);}
  100% {opacity: 0; transform: translateY(-10px);}
}
</style>
