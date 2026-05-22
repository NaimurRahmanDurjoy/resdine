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
      <div class="relative notif-dropdown">
        <button
          class="p-2 text-gray-400 hover:text-cyan-500 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 rounded-lg transition-colors relative"
          @click="toggleNotifDropdown">
          <span class="material-symbols-outlined font-icon">notifications</span>
          <span v-if="localNotifications?.total"
            class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white rounded-full border-2 border-white dark:border-gray-800 text-[8px] font-bold flex items-center justify-center">
            {{ localNotifications.total > 9 ? '9+' : localNotifications.total }}
          </span>
        </button>

        <!-- Dropdown Menu -->
        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
          <div v-if="notifOpen"
            class="absolute right-0 mt-2 w-80 sm:w-96 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">
            
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/80 flex justify-between items-center">
              <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                Notifications 
                <span v-if="localNotifications?.total" class="bg-indigo-100 text-indigo-800 text-xs font-black px-2 py-0.5 rounded-full dark:bg-indigo-900 dark:text-indigo-300">{{ localNotifications.total }}</span>
              </h3>
            </div>

            <div class="max-h-[60vh] overflow-y-auto custom-scrollbar">
               <div v-if="!localNotifications?.items || localNotifications.items.length === 0" class="p-8 text-center text-gray-400 dark:text-gray-500">
                 <span class="material-symbols-outlined text-5xl mb-3 opacity-30 block">notifications_paused</span>
                 <p class="text-sm font-medium">All caught up!</p>
               </div>
               
               <template v-else>
                  <Link 
                    v-for="item in localNotifications.items" 
                    :key="item.id" 
                    :href="item.url"
                    class="flex items-start p-4 hover:bg-indigo-50/50 dark:hover:bg-gray-700/50 border-b border-gray-50 dark:border-gray-700/50 transition-all last:border-0 group"
                  >
                    <div class="flex-shrink-0 mt-0.5">
                      <span v-if="item.type === 'system_error'" class="material-symbols-outlined text-red-500 bg-red-50 dark:bg-red-500/10 p-2.5 rounded-xl text-sm shadow-sm">error</span>
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
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  user: { type: Object, required: true },
  notifications: { type: Object, default: () => ({ total: 0, groups: {}, items: [] }) },
  pageTitle: { type: String, default: '' }
})

const localNotifications = ref(JSON.parse(JSON.stringify(props.notifications)))
const notifOpen = ref(false)

watch(() => props.notifications, (newVal) => {
  localNotifications.value = JSON.parse(JSON.stringify(newVal))
}, { deep: true })

function toggleNotifDropdown() {
  notifOpen.value = !notifOpen.value
}

function handleClickOutside(event) {
  if (!event.target.closest('.notif-dropdown')) {
    notifOpen.value = false
  }
}

const userInitials = computed(() => {
  return props.user.name ? props.user.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) : 'DV'
})

const logout = () => {
  router.post(route('devAdmin.logout'))
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)

  if (window.Echo) {
    window.Echo.private(`App.Models.Admin.${props.user.id}`)
      .notification((notification) => {
        const newNotif = {
            id: notification.id,
            type: notification.type || 'info',
            message: notification.message,
            url: notification.url || '#'
        };
        
        if (!localNotifications.value.items) localNotifications.value.items = [];
        localNotifications.value.items.unshift(newNotif);
        localNotifications.value.total++;
      });
  }
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.font-icon {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
}
</style>
