function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const navbar = document.getElementById('navbar');
    if (navbar && !document.getElementById('cart-icon')) {
      const cartLink = document.createElement('a');
      cartLink.href = "checkout.html";
      cartLink.className = "relative text-white ml-4";
      cartLink.innerHTML = `
        <i class="fas fa-shopping-cart text-lg"></i>
        <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-xs rounded-full px-1.5">${cart.length}</span>
      `;
      navbar.appendChild(cartLink);
    } else {
      const countSpan = document.getElementById('cart-count');
      if (countSpan) countSpan.textContent = cart.length;
    }
  }
  document.addEventListener('DOMContentLoaded', updateCartCount);
  