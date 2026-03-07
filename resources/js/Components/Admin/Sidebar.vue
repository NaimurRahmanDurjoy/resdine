<script setup>
import SideBarItem from './SideBarItem.vue'
import Logo from './Logo.vue'

const props = defineProps({
  menus: { type: Array, default: () => [] }
})
</script>

<template>
  <aside class="w-64 bg-white dark:bg-gray-900 h-full border-r border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden">
    <!-- Logo -->
    <div class="flex-shrink-0 flex items-center h-16 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-600 to-indigo-700 dark:from-indigo-900 dark:to-slate-900">
      <Logo is-sidebar />
    </div>

    <!-- Navigation -->
    <nav class="mt-4 px-3 flex-1 overflow-y-auto custom-scrollbar">
      <SideBarItem v-for="menu in menus" :key="menu.id" :menu="menu" />
    </nav>

    <!-- Footer -->
    <div class="flex-shrink-0 p-4 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-500 text-center bg-gray-50 dark:bg-gray-800/50">
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
