<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RestoNest</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans text-gray-800">

<!-- Navbar -->
<nav class="fixed top-0 left-0 w-full bg-black bg-opacity-80 text-white z-50">
  <div class="container mx-auto flex items-center justify-between py-4 px-6">
    <a href="#" class="text-2xl font-bold">🍴 RestoNest</a>
    <ul class="hidden md:flex space-x-6">
      <li><a href="#" class="hover:text-yellow-400">Home</a></li>
      <li><a href="#about" class="hover:text-yellow-400">About</a></li>
      <li><a href="#menu" class="hover:text-yellow-400">Menu</a></li>
      <li><a href="#contact" class="hover:text-yellow-400">Contact</a></li>
    </ul>
  </div>
</nav>

<!-- Hero Section -->
<header class="h-screen bg-cover bg-center flex flex-col items-center justify-center text-center text-white relative" style="background-image: url('https://source.unsplash.com/1600x800/?restaurant,food');">
  <div class="absolute inset-0 bg-black bg-opacity-50"></div>
  <div class="relative z-10">
    <h1 class="text-5xl md:text-6xl font-extrabold drop-shadow-lg">Welcome to RestoNest</h1>
    <p class="mt-4 text-xl md:text-2xl">Where Taste Meets Elegance</p>
    <a href="#menu" class="mt-6 inline-block bg-yellow-400 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-500 transition">Explore Menu</a>
  </div>
</header>

<!-- About Section -->
<section id="about" class="py-16 px-6 bg-white">
  <div class="container mx-auto text-center">
    <h2 class="text-3xl font-bold mb-4">About Us</h2>
    <p class="text-lg max-w-3xl mx-auto">At <span class="font-semibold">RestoNest</span>, we serve freshly prepared dishes made with love. Our chefs bring a fusion of flavors to give you the perfect dining experience. Whether it’s a casual meal or a celebration, we make it memorable.</p>
  </div>
</section>

<!-- Specials / Menu Section -->
<section id="menu" class="py-16 px-6 bg-gray-100">
  <div class="container mx-auto">
    <h2 class="text-3xl font-bold text-center mb-10">Our Specials</h2>
    <div class="grid gap-8 md:grid-cols-3">
      
      <!-- Card -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <img src="https://source.unsplash.com/400x300/?pizza" class="w-full h-48 object-cover" alt="Pizza">
        <div class="p-5 text-center">
          <h3 class="text-xl font-semibold">Classic Pizza</h3>
          <p class="mt-2 text-gray-600">Freshly baked with Italian flavors.</p>
          <p class="mt-3 text-lg font-bold">$12.99</p>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <img src="https://source.unsplash.com/400x300/?burger" class="w-full h-48 object-cover" alt="Burger">
        <div class="p-5 text-center">
          <h3 class="text-xl font-semibold">Cheese Burger</h3>
          <p class="mt-2 text-gray-600">Juicy grilled beef with melted cheese.</p>
          <p class="mt-3 text-lg font-bold">$9.99</p>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <img src="https://source.unsplash.com/400x300/?pasta" class="w-full h-48 object-cover" alt="Pasta">
        <div class="p-5 text-center">
          <h3 class="text-xl font-semibold">Italian Pasta</h3>
          <p class="mt-2 text-gray-600">Rich creamy pasta with authentic flavors.</p>
          <p class="mt-3 text-lg font-bold">$11.49</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Footer -->
<footer id="contact" class="bg-black text-white py-8">
  <div class="container mx-auto text-center">
    <p class="mb-2">📍 123 Food Street, Dhaka</p>
    <p>📞 +880 1234 567890 | ✉ info@restonest.com</p>
    <p class="mt-4 text-gray-400">&copy; 2025 RestoNest. All Rights Reserved.</p>
  </div>
</footer>

</body>
</html>
