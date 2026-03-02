<template>
  <aside class="w-64 bg-white dark:bg-gray-900 h-full border-r border-gray-200 dark:border-gray-700 flex flex-col">
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-500 to-purple-600">
      <div class="text-2xl font-bold text-white px-4 py-2 rounded">Resdine POS</div>
    </div>

    <!-- Navigation -->
    <nav class="mt-6 px-4 flex-1 overflow-auto">
      <SideBarItem v-for="menu in menus" :key="menu.id" :menu="menu" />
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-gray-200 dark:border-gray-700 text-sm text-gray-500 text-center">
      © {{ new Date().getFullYear() }} Resdine POS
    </div>
  </aside>
</template>

<script setup>
import SideBarItem from './SideBarItem.vue'

const props = defineProps({
  menus: { type: Array, default: () => [] }
})
</script>
