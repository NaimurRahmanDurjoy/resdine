<template>
  <div
    class="font-outfit bg-slate-50 min-h-screen text-slate-800 transition-colors duration-300 antialiased selection:bg-amber-300 selection:text-amber-900">
    <!-- Navbar -->
    <nav
      class="fixed w-full z-50 transition-all duration-300 bg-white/80 backdrop-blur-md shadow-sm border-b border-slate-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14 md:h-16 items-center">

          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center space-x-2 cursor-pointer" @click="$inertia.visit('/')">
            <div
              class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-tr from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
              <span class="material-symbols-outlined text-white font-bold text-xl md:text-2xl">
                restaurant
              </span>
            </div>

            <span class="font-black text-lg md:text-2xl tracking-tighter text-slate-900">
              RESDINE<span class="text-amber-500">.</span>
            </span>
          </div>

          <!-- Desktop -->
          <div class=" hidden md:flex space-x-8 items-center">
            <Link :href="route('web.menu')" class="text-sm font-bold">
              Menu
            </Link>

            <button @click="promptTrackOrder">
              Track Order
            </button>

            <button @click="showAboutModal = true">
              About
            </button>

            <button @click="showContactModal = true">
              Contact
            </button>

            <slot name="nav-actions"></slot>
          </div>

          <!-- Mobile -->
          <div class="flex md:hidden items-center gap-2">

            <slot name="nav-actions"></slot>

            <button @click="mobileMenuOpen = true" class="p-2 rounded-xl bg-slate-100 active:bg-slate-200">
              <span class="material-symbols-outlined">
                menu
              </span>
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main class="pt-14 md:pt-16 min-h-[calc(100vh-80px)] mt-0 relative">
      <slot></slot>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-8 border-t border-slate-800">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <p class="font-medium text-sm">© {{ new Date().getFullYear() }} ResDine. All rights reserved.</p>
        <p class="text-xs mt-2 opacity-50">Built with modern tech for an exceptional experience.</p>
      </div>
    </footer>

    <!-- Mobile Menu: now slides in/out instead of just appearing -->
    <Transition name="fade">
      <div v-if="mobileMenuOpen" class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/40" @click="mobileMenuOpen = false"></div>

        <Transition name="slide-in" appear>
          <div class="absolute right-0 top-0 h-full w-72 max-w-[85vw] bg-white shadow-2xl flex flex-col">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
              <span class="font-black text-lg text-slate-900">Menu</span>
              <button @click="mobileMenuOpen = false" class="p-2 rounded-full bg-slate-50 active:bg-slate-100">
                <span class="material-symbols-outlined text-slate-500">close</span>
              </button>
            </div>

            <div class="p-4 space-y-2 overflow-y-auto flex-1">

              <Link :href="route('web.menu')" @click="mobileMenuOpen = false"
                class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 active:bg-amber-50 border border-slate-100 transition">
                <span class="material-symbols-outlined text-amber-500">
                  restaurant_menu
                </span>
                <span class="font-semibold">Browse Menu</span>
              </Link>

              <button @click="promptTrackOrder"
                class="w-full flex items-center gap-3 p-4 rounded-2xl bg-slate-50 active:bg-amber-50 border border-slate-100 transition">
                <span class="material-symbols-outlined text-amber-500">
                  receipt_long
                </span>
                <span class="font-semibold">Track Order</span>
              </button>

              <button @click="showAboutModal = true; mobileMenuOpen = false"
                class="w-full flex items-center gap-3 p-4 rounded-2xl bg-slate-50 active:bg-amber-50 border border-slate-100 transition">
                <span class="material-symbols-outlined text-amber-500">
                  info
                </span>
                <span class="font-semibold">About Us</span>
              </button>

              <button @click="showContactModal = true; mobileMenuOpen = false"
                class="w-full flex items-center gap-3 p-4 rounded-2xl bg-slate-50 active:bg-amber-50 border border-slate-100 transition">
                <span class="material-symbols-outlined text-amber-500">
                  call
                </span>
                <span class="font-semibold">Contact Us</span>
              </button>

            </div>
          </div>
        </Transition>
      </div>
    </Transition>

    <!-- About Us Modal -->
    <Transition name="fade">
      <div v-if="showAboutModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="showAboutModal = false"></div>
        <div
          class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-105 dark:border-slate-800 max-w-lg w-full max-h-[90vh] overflow-y-auto overflow-hidden relative z-10 transform scale-100 transition-all duration-300">
          <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-5 md:p-6 text-white relative sticky top-0">
            <button @click="showAboutModal = false"
              class="absolute top-4 right-4 text-white/80 hover:text-white font-bold bg-white/10 hover:bg-white/20 p-2 rounded-full transition flex items-center justify-center">
              <span class="material-symbols-outlined text-lg">close</span>
            </button>
            <span class="text-xs uppercase tracking-wider font-bold opacity-75">Our Story</span>
            <h3 class="text-xl md:text-2xl font-black mt-1">About ResDine</h3>
          </div>
          <div class="p-5 md:p-6 space-y-4">
            <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed font-medium">
              Welcome to ResDine, where culinary passion meets exceptional service. Our chefs craft every dish with
              precision and creativity, sourcing only the finest fresh ingredients to tell a unique culinary story.
            </p>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-3 md:gap-4">
              <div
                class="bg-slate-50 dark:bg-slate-950/50 p-3 md:p-4 rounded-2xl border border-slate-100/50 dark:border-slate-800">
                <span class="material-symbols-outlined text-amber-500 mb-1 block">schedule</span>
                <h4 class="font-bold text-slate-800 dark:text-slate-200 text-xs uppercase tracking-wide">Opening Hours
                </h4>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Daily: 11:00 AM - 10:00 PM</p>
              </div>
              <div
                class="bg-slate-50 dark:bg-slate-950/50 p-3 md:p-4 rounded-2xl border border-slate-100/50 dark:border-slate-800">
                <span class="material-symbols-outlined text-amber-500 mb-1 block">restaurant</span>
                <h4 class="font-bold text-slate-800 dark:text-slate-200 text-xs uppercase tracking-wide">Ambiance</h4>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Gourmet Fine Dining & Delivery</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Contact Us Modal -->
    <Transition name="fade">
      <div v-if="showContactModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="showContactModal = false"></div>
        <div
          class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-105 dark:border-slate-800 max-w-lg w-full max-h-[90vh] overflow-y-auto overflow-hidden relative z-10 transform scale-100 transition-all duration-300">
          <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-5 md:p-6 text-white relative sticky top-0">
            <button @click="showContactModal = false"
              class="absolute top-4 right-4 text-white/80 hover:text-white font-bold bg-white/10 hover:bg-white/20 p-2 rounded-full transition flex items-center justify-center">
              <span class="material-symbols-outlined text-lg">close</span>
            </button>
            <span class="text-xs uppercase tracking-wider font-bold opacity-75">Get In Touch</span>
            <h3 class="text-xl md:text-2xl font-black mt-1">Contact Us</h3>
          </div>
          <div class="p-5 md:p-6 space-y-4">
            <div class="space-y-3">
              <a href="tel:+88012345678"
                class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-950/50 rounded-2xl border border-slate-100/50 dark:border-slate-800 active:bg-amber-50">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                  <span class="material-symbols-outlined text-lg">call</span>
                </div>
                <div>
                  <h4 class="font-bold text-xs uppercase text-slate-400 tracking-wider">Hotline</h4>
                  <p class="text-sm font-bold text-slate-700 dark:text-slate-300">+880 1234 5678</p>
                </div>
              </a>
              <a href="mailto:support@resdine.com"
                class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-950/50 rounded-2xl border border-slate-100/50 dark:border-slate-800 active:bg-amber-50">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                  <span class="material-symbols-outlined text-lg">mail</span>
                </div>
                <div>
                  <h4 class="font-bold text-xs uppercase text-slate-400 tracking-wider">Email</h4>
                  <p class="text-sm font-bold text-slate-700 dark:text-slate-300">support@resdine.com</p>
                </div>
              </a>
              <div
                class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-950/50 rounded-2xl border border-slate-100/55 dark:border-slate-800">
                <div
                  class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                  <span class="material-symbols-outlined text-lg">pin_drop</span>
                </div>
                <div>
                  <h4 class="font-bold text-xs uppercase text-slate-400 tracking-wider">Address</h4>
                  <p class="text-sm font-bold text-slate-700 dark:text-slate-300 leading-relaxed">123 Gourmet Boulevard,
                    Food District, Dhaka.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import Swal from 'sweetalert2'

const showAboutModal = ref(false)
const showContactModal = ref(false)
const mobileMenuOpen = ref(false)

const promptTrackOrder = () => {
  Swal.fire({
    title: 'Track Your Order',
    text: 'Please enter your unique Order Number:',
    input: 'text',
    inputPlaceholder: 'e.g. ORD-2026-00027',
    showCancelButton: true,
    confirmButtonColor: '#f59e0b',
    cancelButtonColor: '#475569',
    confirmButtonText: 'Track Now',
    background: '#ffffff',
    customClass: {
      confirmButton: 'px-5 py-3 rounded-xl font-bold uppercase tracking-wider text-sm',
      cancelButton: 'px-5 py-3 rounded-xl font-bold uppercase tracking-wider text-sm'
    }
  }).then((result) => {
    if (result.isConfirmed && result.value && result.value.trim()) {
      window.location.href = `/track-order/${result.value.trim()}`
    }
  })
}

onMounted(() => {
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
body {
  font-family: 'Outfit', sans-serif;
  margin: 0;
}

.font-outfit {
  font-family: 'Outfit', sans-serif;
}

/* Modal/menu transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-in-enter-active,
.slide-in-leave-active {
  transition: transform 0.3s ease;
}
.slide-in-enter-from,
.slide-in-leave-to {
  transform: translateX(100%);
}
</style>