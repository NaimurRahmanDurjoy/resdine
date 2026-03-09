<template>
  <header
    class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 h-16 flex items-center justify-between px-6 sticky top-0 z-20">
    <!-- Left Section -->
    <div class="flex items-center">
      <!-- Mobile menu button -->
      <button @click="$emit('toggleSidebar')"
        class="p-2 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-cyan-500">
        <span class="material-symbols-outlined font-icon">menu</span>
      </button>

      <!-- Search -->
      <div class="hidden md:block ml-4">
        <div class="relative group">
          <input type="text" placeholder="Global system search..."
            class="w-64 pl-10 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all group-hover:bg-white dark:group-hover:bg-gray-800">
          <span class="material-symbols-outlined absolute left-3 top-2 text-gray-400 text-xl font-icon">search</span>
        </div>
      </div>
    </div>

    <!-- Right Section -->
    <div class="flex items-center space-x-2">
      <!-- Theme Toggle -->
      <button
        class="p-2 text-gray-400 hover:text-cyan-500 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 rounded-lg transition-colors">
        <span class="material-symbols-outlined font-icon">dark_mode</span>
      </button>

      <!-- Notifications -->
      <div class="relative">
        <button
          class="p-2 text-gray-400 hover:text-cyan-500 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 rounded-lg transition-colors relative">
          <span class="material-symbols-outlined font-icon">notifications</span>
          <span
            class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-gray-800"></span>
        </button>
      </div>

      <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

      <!-- User Menu -->
      <div class="relative group">
        <button
          class="flex items-center space-x-3 p-1.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all border border-transparent hover:border-gray-200 dark:hover:border-gray-600">
          <div
            class="w-8 h-8 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-sm">
            {{ userInitials }}
          </div>
          <div class="hidden md:block text-left">
            <div class="text-xs font-bold text-gray-900 dark:text-white leading-none mb-0.5">DEV: {{ user.name }}</div>
            <div class="text-[10px] text-gray-500 font-medium">System Developer</div>
          </div>
          <span class="material-symbols-outlined text-gray-400 text-lg font-icon">expand_more</span>
        </button>

        <!-- Dropdown Menu (Hidden by default, shown on group hover for simplicity or could be clicked) -->
        <div
          class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
          <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 mb-1">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Signed in as</p>
            <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ user.email }}</p>
          </div>
          <Link :href="route('devAdmin.settings')"
            class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <span class="material-symbols-outlined mr-3 text-lg font-icon">settings</span>
            System Settings
          </Link>
          <div class="h-px bg-gray-100 dark:bg-gray-700 my-1"></div>
          <button @click="logout"
            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
            <span class="material-symbols-outlined mr-3 text-lg font-icon">logout</span>
            Exit Panel
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  user: { type: Object, required: true },
  notifications: { type: Array, default: () => [] },
  pageTitle: { type: String, default: '' }
})

const userInitials = computed(() => {
  return props.user.name ? props.user.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) : 'DV'
})

const logout = () => {
  router.post(route('devAdmin.logout'))
}
</script>

<style scoped>
.font-icon {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
}
</style>
