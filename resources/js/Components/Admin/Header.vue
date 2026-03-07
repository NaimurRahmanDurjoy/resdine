<template>
  <header class="bg-white dark:bg-gray-900 shadow-sm px-4 h-16 flex justify-between items-center border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
    <div class="flex items-center space-x-2 flex-1 min-w-0">
      <!-- Mobile Menu Button -->
      <button class="lg:hidden p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 flex-shrink-0" @click="$emit('toggleSidebar')">
        <span class="material-symbols-outlined">menu</span>
      </button>

      <!-- Logo (Mobile Only) -->
      <div class="lg:hidden flex-shrink-0">
        <Logo />
      </div>

      <!-- Page Title -->
      <h1 class="text-lg md:text-xl font-semibold text-gray-800 dark:text-white truncate ml-2">
        {{ pageTitle }}
      </h1>
    </div>

    <!-- Right Section -->
    <div class="flex items-center space-x-2 md:space-x-4 flex-shrink-0">
      <!-- Notifications -->
      <button class="relative p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors" v-if="notifications.length">
        <span class="material-symbols-outlined">notifications</span>
        <span class="absolute top-1.5 right-1.5 bg-red-500 text-white rounded-full text-[10px] w-4 h-4 flex items-center justify-center">
          {{ notifications.length }}
        </span>
      </button>

      <!-- User Info -->
      <div class="flex items-center space-x-2 border-l border-gray-200 dark:border-gray-700 pl-4">
        <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center flex-shrink-0">
          <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400 text-sm">person</span>
        </div>
        <span class="text-gray-700 dark:text-gray-300 font-medium truncate max-w-[100px] hidden sm:inline-block">
          {{ user.name }}
        </span>
      </div>

      <!-- Logout -->
      <button
        @click="logout"
        class="p-2 md:px-4 md:py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 flex items-center transition-colors"
        title="Logout"
      >
        <span class="material-symbols-outlined text-sm">logout</span>
        <span class="ml-1 hidden md:inline-block">Logout</span>
      </button>
    </div>
  </header>
</template>

<script setup>
  import { router } from '@inertiajs/vue3'
  import Logo from './Logo.vue'

  const props = defineProps({
    pageTitle: { type: String, default: 'Dashboard' },
    user: { type: Object, required: true },
    notifications: { type: Array, default: () => [] }
  })

  function logout() {
    router.post('/admin/logout')
  }
</script>
