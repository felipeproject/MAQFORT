function updateCartCount() {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  const cartCountElement = document.getElementById('cart-count');
  if (cartCountElement) cartCountElement.textContent = cart.length;
}

document.addEventListener('DOMContentLoaded', () => {
  // Monta a navbar
  document.getElementById("navbar").innerHTML = `
    <header class="fixed top-0 w-full bg-[#802A31] z-50 shadow-md">
      <div class="container mx-auto flex flex-col md:flex-row md:items-center justify-between p-4 gap-y-2">
        <div class="flex justify-between items-center w-full md:w-auto">
          <a href="index.html" class="text-white text-2xl font-semibold">MAQFORT</a>
          <button id="nav-toggle" class="text-white text-2xl md:hidden focus:outline-none">
            <i class="fas fa-bars"></i>
          </button>
        </div>
        <nav id="nav-menu" class="hidden flex-col md:flex md:flex-row md:space-x-6 text-white text-base">
          <a href="index.html" class="block hover:underline py-1">Início</a>
          <a href="produtos.html" class="block hover:underline py-1">Produtos</a>
          <a href="sobre.html" class="block hover:underline py-1">Sobre</a>
          <a href="contato.html" class="block hover:underline py-1">Contato</a>
          <a href="checkout.html" class="relative flex items-center gap-1 hover:underline py-1">
            <i class="fas fa-shopping-cart text-xl"></i>
            <span class="md:hidden">Carrinho</span>
            <span id="cart-count" class="absolute -top-2 -right-3 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center">0</span>
          </a>
        </nav>
      </div>
    </header>
  `;

  // Mobile toggle
  document.getElementById('nav-toggle').addEventListener('click', () => {
    document.getElementById('nav-menu').classList.toggle('hidden');
  });

  // Footer
  document.getElementById("footer").innerHTML = `
    <footer class="bg-[#802A31] text-white text-center py-4 mt-auto">
      &copy; 2025 Maqfort. Todos os direitos reservados.
    </footer>
  `;

  updateCartCount();
});
