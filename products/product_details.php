<?php
session_start();
include('../includes/header.php');
include('../includes/navbar_user.php');
?>

<div class="container py-5" id="product-detail"></div>

<?php include('../includes/footer.php'); ?>

<script type="module">
  import { mens_kurta } from "../assets/products/mens_kurta.js";

  const params = new URLSearchParams(window.location.search);
  const id = params.get('id');
  const product = mens_kurta[id];
  const container = document.getElementById('product-detail');

  if (product) {
    container.innerHTML = `
      <div class="row">
        <div class="col-md-5">
          <img src="${product.imageUrl}" class="img-fluid rounded shadow" alt="${product.title}">
        </div>
        <div class="col-md-7">
          <h3 class="fw-bold">${product.title}</h3>
          <h5 class="text-muted">${product.brand}</h5>
          <p class="text-success fw-bold fs-5 mt-2">₹${product.discountedPrice}
            <span class="text-decoration-line-through text-muted fs-6">₹${product.price}</span>
            <span class="text-danger fs-6">(${product.discountedPercent}% OFF)</span>
          </p>
          <p class="mt-3">${product.description}</p>
          <label class="fw-semibold">Select Size:</label>
          <select id="size" class="form-select w-50 mb-3">
            ${product.sizes.map(s => `<option value="${s.name}">${s.name}</option>`).join('')}
          </select>
          <button id="add-to-cart" class="btn btn-primary me-2">Add to Cart</button>
          <a href="cart.php" class="btn btn-success">Go to Cart</a>
        </div>
      </div>`;

    // ✅ Attach event after HTML loads
    document.getElementById("add-to-cart").addEventListener("click", () => {
      const size = document.getElementById("size").value;
      const cart = JSON.parse(localStorage.getItem("cart")) || [];

      // Check if already in cart with same size
      const existing = cart.find(i => i.title === product.title && i.selectedSize === size);
      if (existing) {
        existing.qty += 1;
      } else {
        cart.push({ ...product, selectedSize: size, qty: 1 });
      }

      localStorage.setItem("cart", JSON.stringify(cart));
      alert("✅ Product added to cart!");
    });
  } else {
    container.innerHTML = `<h4 class="text-center text-danger">Product not found!</h4>`;
  }
</script>
