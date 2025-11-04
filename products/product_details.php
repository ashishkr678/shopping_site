<?php
session_start();
include('../includes/header.php');
include('../includes/navbar_user.php');
?>

<div class="container py-5" id="product-detail"></div>

<?php include('../includes/footer.php'); ?>

<script type="module">
  import { mens_kurta } from "../assets/mens_kurta.js";

  const params = new URLSearchParams(window.location.search);
  const id = params.get('id');
  const product = mens_kurta[id];
  const container = document.getElementById('product-detail');

  if (product) {
    container.innerHTML = `
      <div class="row gy-4">
        <div class="col-md-5 text-center">
          <img src="${product.imageUrl}" class="img-fluid rounded shadow-sm border" alt="${product.title}" style="max-height: 500px; object-fit: cover;">
        </div>
        <div class="col-md-7">
          <h3 class="fw-bold mb-1">${product.title}</h3>
          <h6 class="text-muted mb-3">${product.brand}</h6>
          <h4 class="text-success fw-bold mb-2">₹${product.discountedPrice}
            <span class="text-decoration-line-through text-muted fs-6 ms-1">₹${product.price}</span>
            <span class="text-danger fs-6 ms-1">(${product.discountedPercent}% OFF)</span>
          </h4>
          <p class="text-secondary">${product.description}</p>

          <!-- Size Buttons -->
          <div class="mt-3">
            <label class="fw-semibold mb-2 d-block">Select Size:</label>
            <div id="size-options" class="d-flex flex-wrap gap-2">
              ${product.sizes.map(s => `
                <button type="button" class="btn btn-outline-primary size-btn" data-size="${s.name}">${s.name}</button>
              `).join('')}
            </div>
          </div>

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
            <button id="add-to-cart" class="btn btn-primary me-2 px-4"><i class="bi bi-cart-plus"></i> Add to Cart</button>
            <a href="cart.php" class="btn btn-success px-4"><i class="bi bi-bag-check"></i> Go to Cart</a>
          </div>
        </div>
      </div>
    `;

    // ✅ Handle size selection
    const sizeButtons = document.querySelectorAll('.size-btn');
    let selectedSize = null;

    sizeButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        sizeButtons.forEach(b => b.classList.remove('active', 'btn-primary'));
        btn.classList.add('active', 'btn-primary');
        btn.classList.remove('btn-outline-primary');
        selectedSize = btn.dataset.size;
      });
    });

    // ✅ Handle quantity change
    const qtyInput = document.getElementById('quantity');
    document.getElementById('increase').addEventListener('click', () => {
      qtyInput.value = parseInt(qtyInput.value) + 1;
    });
    document.getElementById('decrease').addEventListener('click', () => {
      if (parseInt(qtyInput.value) > 1) qtyInput.value = parseInt(qtyInput.value) - 1;
    });

    // ✅ Add to Cart Logic
    document.getElementById("add-to-cart").addEventListener("click", () => {
      if (!selectedSize) {
        alert("⚠️ Please select a size first!");
        return;
      }

      const qty = parseInt(qtyInput.value);
      const cart = JSON.parse(localStorage.getItem("cart")) || [];
      const existing = cart.find(i => i.title === product.title && i.selectedSize === selectedSize);

      if (existing) {
        existing.qty += qty;
      } else {
        cart.push({ ...product, selectedSize, qty });
      }

      localStorage.setItem("cart", JSON.stringify(cart));
      alert(`✅ ${qty} item(s) added to cart!`);
    });
  } else {
    container.innerHTML = `<h4 class="text-center text-danger">Product not found!</h4>`;
  }
</script>

<style>
  .size-btn.active {
    color: #fff !important;
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
  }
  .size-btn {
    min-width: 50px;
    border-radius: 8px;
  }
</style>
