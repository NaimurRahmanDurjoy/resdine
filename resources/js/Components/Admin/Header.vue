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
      <!-- Notifications Dropdown -->
      <div class="relative notif-dropdown">
        <button class="relative p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors focus:outline-none"
          @click="toggleNotifDropdown">
          <span class="material-symbols-outlined">notifications</span>
          <span v-if="notifications?.total"
            class="absolute top-1.5 right-1.5 bg-red-500 text-white rounded-full text-[10px] w-4 h-4 flex items-center justify-center font-bold">
            {{ notifications.total > 9 ? '9+' : notifications.total }}
          </span>
        </button>

        <!-- Dropdown Menu -->
        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
          <div v-if="notifOpen"
            class="absolute right-0 mt-2 w-80 sm:w-96 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">
            
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/80 flex justify-between items-center">
              <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                Notifications 
                <span v-if="notifications?.total" class="bg-indigo-100 text-indigo-800 text-xs font-black px-2 py-0.5 rounded-full dark:bg-indigo-900 dark:text-indigo-300">{{ notifications.total }}</span>
              </h3>
            </div>

            <div class="max-h-[60vh] overflow-y-auto custom-scrollbar">
               <div v-if="!notifications?.items || notifications.items.length === 0" class="p-8 text-center text-gray-400 dark:text-gray-500">
                 <span class="material-symbols-outlined text-5xl mb-3 opacity-30 block">notifications_paused</span>
                 <p class="text-sm font-medium">All caught up!</p>
               </div>
               
               <template v-else>
                  <Link 
                    v-for="item in notifications.items" 
                    :key="item.id" 
                    :href="item.url"
                    class="flex items-start p-4 hover:bg-indigo-50/50 dark:hover:bg-gray-700/50 border-b border-gray-50 dark:border-gray-700/50 transition-all last:border-0 group"
                  >
                    <div class="flex-shrink-0 mt-0.5">
                      <span v-if="item.type === 'low_stock'" class="material-symbols-outlined text-red-500 bg-red-50 dark:bg-red-500/10 p-2.5 rounded-xl text-sm shadow-sm">warning</span>
                      <span v-else-if="item.type === 'expiring'" class="material-symbols-outlined text-amber-500 bg-amber-50 dark:bg-amber-500/10 p-2.5 rounded-xl text-sm shadow-sm">timer</span>
                      <span v-else-if="item.type === 'pending_order'" class="material-symbols-outlined text-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 p-2.5 rounded-xl text-sm shadow-sm">receipt</span>
                      <span v-else class="material-symbols-outlined text-blue-500 bg-blue-50 dark:bg-blue-500/10 p-2.5 rounded-xl text-sm shadow-sm">info</span>
                    </div>
                    <div class="ml-4 flex-1">
                      <p class="text-sm rounded text-gray-800 dark:text-gray-200 leading-snug font-medium group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        {{ item.message }}
                      </p>
                    </div>
                  </Link>
               </template>
            </div>
          </div>
        </transition>
      </div>

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
const notifOpen = ref(false)

const props = defineProps({
  pageTitle: { type: String, default: 'Dashboard' },
  user: { type: Object, required: true },
  notifications: { type: Object, default: () => ({ total: 0, groups: {}, items: [] }) }
})

// Toggle dropdowns
function toggleDropdown() {
  open.value = !open.value
  if (open.value) notifOpen.value = false
}

function toggleNotifDropdown() {
  notifOpen.value = !notifOpen.value
  if (notifOpen.value) open.value = false
}

// Close dropdowns on outside click
function handleClickOutside(event) {
  if (!event.target.closest('.dropdown')) {
    open.value = false
  }
  if (!event.target.closest('.notif-dropdown')) {
    notifOpen.value = false
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
