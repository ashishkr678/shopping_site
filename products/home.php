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

<script>
  document.addEventListener("DOMContentLoaded", async () => {
    const productList = document.getElementById('product-list');

    try {
      const response = await fetch("../api/get_products.php");
      const products = await response.json();

      if (products.length === 0) {
        productList.innerHTML = "<p class='text-center text-muted'>No products available.</p>";
        return;
      }

      products.forEach((item) => {
        const card = document.createElement('div');
        card.className = "col-md-3 col-sm-6 mb-4";

        card.innerHTML = `
          <div class="card product-card h-100 shadow-sm border-0 position-relative">
            <div class="card-img-wrapper">
              <img src="${item.image_url}" class="card-img-top" alt="${item.title}">
            </div>
            <div class="card-body d-flex flex-column text-center">
              <h6 class="text-muted mb-1">${item.brand}</h6>
              <h5 class="fw-semibold mb-2">${item.title}</h5>
              <p class="text-success fw-bold mb-2">
                ₹${item.discounted_price}
                <span class="text-decoration-line-through text-muted">₹${item.price}</span>
                <span class="text-danger">(${item.discount_percent}% OFF)</span>
              </p>
              <div class="d-flex justify-content-center gap-2 mt-auto">
                <a href="product_details.php?id=${item.id}" class="btn btn-outline-primary btn-sm w-50 fw-semibold">
                  View
                </a>
                <button class="btn btn-primary btn-sm w-50 fw-semibold add-cart-btn" data-id="${item.id}">
                  Add to Cart
                </button>
              </div>
            </div>
          </div>
        `;

        productList.appendChild(card);
      });
    } catch (error) {
      console.error("Error loading products:", error);
      productList.innerHTML = "<p class='text-center text-danger'>Failed to load products.</p>";
    }
  });

  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('add-cart-btn')) {
      e.stopPropagation();
      const id = e.target.dataset.id;
      const productCard = e.target.closest('.product-card');

      const product = {
        id,
        title: productCard.querySelector('h5').innerText,
        brand: productCard.querySelector('h6').innerText,
        price: productCard.querySelector('.text-success').innerText,
        imageUrl: productCard.querySelector('img').src,
        qty: 1
      };

      const cart = JSON.parse(localStorage.getItem("cart")) || [];
      const existing = cart.find(p => p.id == id);
      if (existing) existing.qty += 1;
      else cart.push(product);

      localStorage.setItem("cart", JSON.stringify(cart));
      showToast(`${product.title} added to cart 🛒`);
    }
  });

  function showToast(msg) {
    const toast = document.createElement('div');
    toast.className = "toast-msg position-fixed top-0 end-0 m-3 bg-success text-white px-3 py-2 rounded shadow";
    toast.style.zIndex = "1055";
    toast.innerHTML = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2000);
  }
</script>


<style>
  .product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
    border-radius: 12px;
  }

  .product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
  }

  .card-img-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 300px;
    /* consistent height */
    background: #f8f9fa;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
  }

  .card-img-top {
    max-height: 100%;
    max-width: 100%;
    object-fit: contain;
  }

  .product-card:hover .card-img-top {
    transform: scale(1.08);
  }

  .add-cart-btn {
    transition: all 0.2s ease;
  }

  .add-cart-btn:hover {
    background-color: #0b5ed7;
  }

  .toast-msg {
    animation: fadeInOut 2s ease;
  }

  @keyframes fadeInOut {
    0% {
      opacity: 0;
      transform: translateY(-10px);
    }

    10%,
    90% {
      opacity: 1;
      transform: translateY(0);
    }

    100% {
      opacity: 0;
      transform: translateY(-10px);
    }
  }

  @media (max-width: 576px) {
    .card-img-top {
      height: 240px;
    }
  }
</style>