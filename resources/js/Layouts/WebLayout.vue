<template>
  <div class="font-outfit bg-slate-50 min-h-screen text-slate-800 transition-colors duration-300 antialiased selection:bg-amber-300 selection:text-amber-900">
    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300 bg-white/80 backdrop-blur-md shadow-sm border-b border-slate-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <div class="flex-shrink-0 flex items-center space-x-2 cursor-pointer" @click="$inertia.visit('/')">
            <div class="w-10 h-10 bg-gradient-to-tr from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
              <span class="material-symbols-outlined text-white font-bold text-2xl">restaurant</span>
            </div>
            <span class="font-black text-2xl tracking-tighter text-slate-900">RESDINE<span class="text-amber-500">.</span></span>
          </div>

          <!-- Desktop menu -->
          <div class="hidden md:flex space-x-8 items-center">
             <Link :href="route('web.menu')" class="text-sm font-bold text-slate-600 hover:text-amber-600 transition tracking-wide uppercase">Menu</Link>
             <a href="#about" class="text-sm font-bold text-slate-600 hover:text-amber-600 transition tracking-wide uppercase">About</a>
             <a href="#contact" class="text-sm font-bold text-slate-600 hover:text-amber-600 transition tracking-wide uppercase">Contact</a>
             
             <!-- Slot for cart button usually -->
             <slot name="nav-actions"></slot>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main class="pt-16 min-h-[calc(100vh-80px)] mt-0 relative">
      <slot></slot>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-8 border-t border-slate-800">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <p class="font-medium text-sm">© {{ new Date().getFullYear() }} ResDine. All rights reserved.</p>
        <p class="text-xs mt-2 opacity-50">Built with modern tech for an exceptional experience.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

// Standard import for Google Font
import { onMounted } from 'vue'

onMounted(() => {
  // Inject Google Font if not present
  if (!document.getElementById('font-outfit')) {
    const link = document.createElement('link')
    link.id = 'font-outfit'
    link.rel = 'stylesheet'
    link.href = 'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap'
    document.head.appendChild(link)
  }
})
</script>

<style>
/* Base Styles injected here for the web layout */
body {
  font-family: 'Outfit', sans-serif;
  margin: 0;
}
.font-outfit {
  font-family: 'Outfit', sans-serif;
}
</style>
