<script setup>
import SideBarItem from './SideBarItem.vue'
import Logo from './Logo.vue'

const props = defineProps({
  menus: { type: Array, default: () => [] }
})
</script>

<template>
  <aside
    class="w-64 h-full border-r border-[color:var(--border-subtle)] bg-[color:var(--bg-surface)] text-[color:var(--text-primary)] flex flex-col overflow-hidden">
    <!-- Logo -->
    <div
      class="flex-shrink-0 flex items-center h-16 border-b border-[color:var(--border-subtle)] bg-gradient-to-r from-indigo-600 to-indigo-700 dark:from-transparent dark:to-transparent">
      <Logo is-sidebar />
    </div>

    <!-- Navigation -->
    <nav class="mt-4 px-3 flex-1 overflow-y-auto custom-scrollbar">
      <SideBarItem v-for="menu in menus" :key="menu.id" :menu="menu" />
    </nav>

    <!-- Footer -->
    <div
      class="flex-shrink-0 border-t border-[color:var(--border-subtle)] bg-[color:var(--bg-elevated)] p-4 text-center text-xs text-[color:var(--text-muted)]">
      © {{ new Date().getFullYear() }} Resdine POS
    </div>
  </aside>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #334155;
}
</style>
