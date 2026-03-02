<template>
  <header class="bg-slate-100 dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between h-16 px-6">
      <!-- Left: Mobile menu + Search -->
      <div class="flex items-center">
        <button @click="$emit('toggleSidebar')" class="p-2 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 dark:hover:bg-gray-700">
          <i class="fas fa-bars"></i>
        </button>

        <div class="hidden md:block ml-4">
          <div class="relative">
            <input type="text" v-model="searchQuery" placeholder="Search..." class="w-64 pl-10 pr-4 py-2 rounded-lg border bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
          </div>
        </div>
      </div>

      <!-- Right: Dark mode, notifications, user menu -->
      <div class="flex items-center space-x-4">
        <button @click="darkMode = !darkMode" class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
          <i class="fas fa-moon" v-if="!darkMode"></i>
          <i class="fas fa-sun" v-else></i>
        </button>

        <!-- Notifications -->
        <div class="relative">
          <button @click="notificationsOpen = !notificationsOpen" class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 relative">
            <i class="fas fa-bell"></i>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
          </button>
          <div v-if="notificationsOpen" class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="font-semibold text-gray-900 dark:text-white">Notifications</h3>
            </div>
            <div class="max-h-96 overflow-y-auto">
              <div v-for="(item,index) in notifications" :key="index" class="p-4 border-b border-gray-100 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                <p class="text-sm text-gray-900 dark:text-white">{{ item.title }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ item.time }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- User Menu -->
        <div class="relative">
          <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
              <span class="text-white text-sm font-semibold">{{ userInitials }}</span>
            </div>
            <div class="hidden md:block text-left">
              <div class="text-sm font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
              <div class="text-xs text-gray-500">{{ user.role }}</div>
            </div>
            <i class="fas fa-chevron-down text-xs text-gray-500"></i>
          </button>

          <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
            <a href="#" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
              <i class="fas fa-user w-4 mr-3"></i> Profile
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
              <i class="fas fa-cog w-4 mr-3"></i> Settings
            </a>
            <div class="border-t border-gray-200 dark:border-gray-700"></div>
            <button @click="logout" class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-gray-50 dark:hover:bg-gray-700">
              <i class="fas fa-sign-out-alt w-4 mr-3"></i> Logout
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
  user: { type: Object, required: true },
  notifications: { type: Array, default: () => [] }
})

const darkMode = ref(false)
const notificationsOpen = ref(false)
const userMenuOpen = ref(false)
const searchQuery = ref('')

const userInitials = computed(() => {
  return props.user.name.split(' ').map(n => n[0]).join('').toUpperCase()
})

function logout() {
  Inertia.post('/admin/logout')
}
</script>
