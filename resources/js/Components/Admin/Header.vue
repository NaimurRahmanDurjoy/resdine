<template>
  <header
    class="bg-white dark:bg-gray-900 shadow-sm px-4 h-16 flex justify-between items-center border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
    <div class="flex items-center space-x-2 flex-1 min-w-0">
      <!-- Mobile Menu Button -->
      <button class="lg:hidden p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 flex-shrink-0"
        @click="$emit('toggleSidebar')">
        <span class="material-symbols-outlined">menu</span>
      </button>

      <!-- Logo (Mobile Only) -->
      <div class="lg:hidden border-r pr-2 flex-shrink-0">
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
      <button class="relative p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"
        v-if="notifications?.total"
        @click="router.get(route('admin.stock.index'))"
        :title="Object.values(notifications.groups).map(g => g.label).join(', ')">
        <span class="material-symbols-outlined">notifications</span>
        <span
          class="absolute top-1.5 right-1.5 bg-red-500 text-white rounded-full text-[10px] w-4 h-4 flex items-center justify-center">
          {{ notifications.total }}
        </span>
      </button>

      <!-- User Info -->
      <div class="relative dropdown">
        <button @click="toggleDropdown" class="flex items-center space-x-2">
          <div class="flex items-center space-x-2 border-l border-gray-200 dark:border-gray-700 pl-4 pr-4">
            <div
              class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center flex-shrink-0">
              <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400 text-sm">person</span>
            </div>
            <span class="text-gray-700 dark:text-gray-300 font-medium truncate max-w-[100px] hidden sm:inline-block">
              {{ user.name }}
            </span>
          </div>
        </button>

        <div v-if="open"
          class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded shadow-lg border dark:border-gray-700">

          <Link :href="route('admin.profile')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
            <span class="material-symbols-outlined text-sm">person</span> <span class="ml-1">Profile</span>
          </Link>

          <button @click="logout" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
            <span class="material-symbols-outlined text-sm">logout</span> <span
              class="ml-1">Logout</span>
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { Link, router} from '@inertiajs/vue3'
import Logo from './Logo.vue'
import { ref, onMounted, onBeforeUnmount } from 'vue'

const open = ref(false)

const props = defineProps({
  pageTitle: { type: String, default: 'Dashboard' },
  user: { type: Object, required: true },
  notifications: { type: Object, default: () => ({ total: 0, groups: {} }) }
})
// Toggle dropdown
function toggleDropdown() {
  open.value = !open.value
}

// Close dropdown on outside click
function handleClickOutside(event) {
  if (!event.target.closest('.dropdown')) {
    open.value = false
  }
}
// Add event listener for clicks outside the dropdown
onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))

// Logout function
function logout() {
  router.post('/admin/logout')
}
</script>
