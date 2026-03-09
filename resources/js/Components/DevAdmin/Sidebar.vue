<template>
  <nav class="bg-gray-900 h-full flex flex-col border-r border-gray-800">
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-800">
      <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-cyan-600 rounded-lg flex items-center justify-center">
          <span class="material-symbols-outlined text-white text-lg font-icon">code</span>
        </div>
        <span class="text-xl font-bold text-white tracking-tight">DevAdmin</span>
      </div>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto py-4 px-3 custom-scrollbar">
      <div class="space-y-1">
        <div v-for="menu in menus" :key="menu.id">
          <!-- Multi-level menu -->
          <div v-if="menu.children && menu.children.length > 0">
            <button @click="toggleMenu(menu.id)"
              class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-150 group"
              :class="isMenuOpen(menu.id) ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800'">
              <div class="flex items-center">
                <span class="material-symbols-outlined mr-3 text-xl font-icon">{{ menu.icon || 'folder' }}</span>
                <span>{{ menu.name }}</span>
              </div>
              <span class="material-symbols-outlined text-lg transition-transform duration-200 font-icon"
                :class="{ 'rotate-180': isMenuOpen(menu.id) }">
                expand_more
              </span>
            </button>

            <div v-show="isMenuOpen(menu.id)" class="mt-1 space-y-1 pl-10 border-l border-gray-800 ml-5">
              <Link v-for="child in menu.children" :key="child.id" :href="child.url"
                class="block px-3 py-1.5 text-sm font-medium rounded-md transition-colors duration-150"
                :class="child.isActive ? 'text-cyan-400' : 'text-gray-500 hover:text-white'">
                {{ child.name }}
              </Link>
            </div>
          </div>

          <!-- Single Level Link -->
          <Link v-else :href="menu.url"
            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-150 group"
            :class="menu.isActive ? 'bg-cyan-900/30 text-cyan-400' : 'text-gray-400 hover:text-white hover:bg-gray-800'">
            <span class="material-symbols-outlined mr-3 text-xl font-icon">{{ menu.icon || 'dashboard' }}</span>
            <span>{{ menu.name }}</span>
          </Link>
        </div>
      </div>
    </div>

    <!-- Bottom Info -->
    <div class="p-4 border-t border-gray-800">
      <div class="bg-gray-800/50 rounded-lg p-3">
        <div class="text-xs text-gray-500 font-medium mb-1">SYSTEM STATUS</div>
        <div class="flex items-center justify-between text-[10px] text-gray-400">
          <span>v1.0.0</span>
          <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>PRODUCTION</span>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  menus: { type: Array, required: true }
})

const openMenus = ref([])

const toggleMenu = (id) => {
  const index = openMenus.value.indexOf(id)
  if (index === -1) {
    openMenus.value.push(id)
  } else {
    openMenus.value.splice(index, 1)
  }
}

const isMenuOpen = (id) => openMenus.value.includes(id)
</script>

<style scoped>
.font-icon {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #374151;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #4b5563;
}
</style>
