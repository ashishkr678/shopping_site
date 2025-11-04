<?php
session_start();
include('../includes/header.php');
include('../includes/navbar_user.php');
?>

<div class="container py-5">
  <h2 class="text-center mb-4 fw-bold text-primary">Men's Kurtas Collection</h2>
  <div class="row" id="product-list"></div>
</div>

<?php include('../includes/footer.php'); ?>

<script type="module">
  import { mens_kurta } from "../assets/products/mens_kurta.js";

  const productList = document.getElementById('product-list');
  mens_kurta.forEach((item, index) => {
    const card = document.createElement('div');
    card.className = "col-md-3 mb-4";
    card.innerHTML = `
      <div class="card h-100 shadow-sm">
        <img src="${item.imageUrl}" class="card-img-top" alt="${item.title}" style="height:280px; object-fit:cover;">
        <div class="card-body d-flex flex-column">
          <h6 class="text-muted mb-1">${item.brand}</h6>
          <h5 class="fw-semibold">${item.title}</h5>
          <p class="text-success fw-bold mb-1">₹${item.discountedPrice} <span class="text-decoration-line-through text-muted">₹${item.price}</span> <span class="text-danger">(${item.discountedPercent}% OFF)</span></p>
          <a href="product_details.php?id=${index}" class="btn btn-primary mt-auto w-100">View Details</a>

        </div>
      </div>`;
    productList.appendChild(card);
  });
</script>
