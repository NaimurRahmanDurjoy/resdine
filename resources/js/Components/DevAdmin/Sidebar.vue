<template>
  <aside class="w-64 bg-white dark:bg-gray-900 h-full border-r border-gray-200 dark:border-gray-700 flex flex-col">
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
          <span class="material-symbols-outlined text-white text-lg">code</span>
        </div>
        <span class="text-xl font-bold text-gray-800 dark:text-white">DevAdmin</span>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-6 px-4 flex-1 overflow-auto">
      <SideBarItem v-for="menu in menus" :key="menu.id" :menu="menu" />
    </nav>

    <!-- Bottom Info -->
    <div class="p-4 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400">
      <div>v{{ appVersion }}</div>
      <div>Env: {{ appEnv }}</div>
    </div>
  </aside>
</template>

<script setup>
import SideBarItem from './SideBarItem.vue'

const props = defineProps({
  menus: { type: Array, default: () => [] },
  appVersion: { type: String, default: '1.0.0' },
  appEnv: { type: String, default: 'production' }
})
</script>
