<?php
function redirect($url) {
  header("Location: $url");
  exit;
}

function formatPrice($price) {
  return "₹" . number_format($price, 2);
}

function isLoggedIn() {
  return isset($_SESSION['user_id']);
}

function isAdmin() {
  return isset($_SESSION['admin_id']);
}
?>
