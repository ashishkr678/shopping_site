<?php
session_start();
include('../includes/header.php');
include('../includes/navbar_user.php');
?>

<div class="container py-5">
  <h2 class="fw-bold text-center text-primary mb-4">Your Shopping Cart</h2>
  <div id="cart-items"></div>
  <div class="text-center mt-4">
    <a href="checkout.php" class="btn btn-success px-4">Proceed to Checkout</a>
  </div>
</div>

<?php include('../includes/footer.php'); ?>

<script>
  const cartContainer = document.getElementById("cart-items");
  const cart = JSON.parse(localStorage.getItem("cart")) || [];

  if (cart.length === 0) {
    cartContainer.innerHTML = `<h5 class="text-center text-muted">Your cart is empty!</h5>`;
  } else {
    let total = 0;
    cartContainer.innerHTML = `
      <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
          <thead class="table-dark">
            <tr>
              <th>Image</th>
              <th>Product</th>
              <th>Size</th>
              <th>Price</th>
              <th>Qty</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            ${cart.map(item => {
              const itemTotal = item.discountedPrice * item.qty;
              total += itemTotal;
              return `
                <tr>
                  <td><img src="${item.imageUrl}" width="80"></td>
                  <td>${item.title}</td>
                  <td>${item.selectedSize}</td>
                  <td>₹${item.discountedPrice}</td>
                  <td>${item.qty}</td>
                  <td>₹${itemTotal}</td>
                </tr>`;
            }).join("")}
          </tbody>
        </table>
        <h4 class="text-end fw-bold">Grand Total: ₹${total}</h4>
      </div>`;
  }
</script>
