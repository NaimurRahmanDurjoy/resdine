<template>
  <nav
    class="h-full flex flex-col border-r border-[color:var(--border-subtle)] bg-[color:var(--bg-surface)] text-[color:var(--text-primary)]">
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-[color:var(--border-subtle)]">
      <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-cyan-600 rounded-lg flex items-center justify-center">
          <span class="material-symbols-outlined text-white text-lg font-icon">code</span>
        </div>
        <span class="text-xl font-bold text-[color:var(--text-primary)] tracking-tight">DevAdmin</span>
      </div>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto py-4 px-3 custom-scrollbar">
      <div class="space-y-1">
        <div v-for="menu in menus" :key="menu.id">
          <!-- Multi-level menu -->
          <div v-if="menu.children && menu.children.length > 0">
            <button @click="toggleMenu(menu.id)"
              class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-150 group border-l-2 focus:outline-none"
              :class="isMenuOpen(menu.id)
                ? 'bg-[color:var(--accent-soft)] text-[color:var(--accent-primary)] border-[color:var(--accent-primary)] font-semibold'
                : 'text-[color:var(--text-secondary)] hover:text-[color:var(--text-primary)] hover:bg-[color:var(--bg-elevated)] border-transparent'">
              <div class="flex items-center">
                <span class="material-symbols-outlined mr-3 text-xl font-icon">{{ menu.icon || 'folder' }}</span>
                <span>{{ menu.name }}</span>
              </div>
              <span class="material-symbols-outlined text-lg transition-transform duration-200 font-icon"
                :class="{ 'rotate-180': isMenuOpen(menu.id) }">
                expand_more
              </span>
            </button>

            <div v-show="isMenuOpen(menu.id)"
              class="mt-1 space-y-1 pl-4 border-l border-[color:var(--border-subtle)] ml-5">
              <SideBarItem v-for="child in menu.children" :key="child.id" :menu="child" />
            </div>
          </div>

          <!-- Single Level Link -->
          <Link v-else :href="menu.url"
            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-150 group border-l-2"
            :class="menu.isActive
              ? 'bg-[color:var(--accent-soft)] text-[color:var(--accent-primary)] border-[color:var(--accent-primary)] font-semibold'
              : 'text-[color:var(--text-secondary)] hover:text-[color:var(--text-primary)] hover:bg-[color:var(--bg-elevated)] border-transparent'">
            <span class="material-symbols-outlined mr-3 text-xl font-icon">{{ menu.icon || 'dashboard' }}</span>
            <span>{{ menu.name }}</span>
          </Link>
        </div>
      </div>
    </div>

    <!-- Bottom Info -->
    <div class="border-t border-[color:var(--border-subtle)] p-4">
      <div class="rounded-lg border border-[color:var(--border-subtle)] bg-[color:var(--bg-elevated)] p-3">
        <div class="text-xs text-[color:var(--text-secondary)] font-medium mb-1">SYSTEM STATUS</div>
        <div class="flex items-center justify-between text-[10px] text-[color:var(--text-muted)]">
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
import SideBarItem from '@/Components/DevAdmin/SideBarItem.vue'
import { ref } from 'vue'

const props = defineProps({
  menus: { type: Array, required: true }
})

const openMenus = ref(props.menus.filter(m => m.isActive).map(m => m.id))

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
