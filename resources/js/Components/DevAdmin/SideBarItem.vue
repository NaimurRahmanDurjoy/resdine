<template>
  <div>
    <div v-if="menu.hasChildren">
      <button @click="open = !open"
              :class="[
                'flex items-center justify-between w-full px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors',
                isActive ? 'bg-blue-50 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : ''
              ]">
        <div class="flex items-center">
          <span v-if="menu.model.icon" class="material-symbols-outlined w-5 mr-3">{{ menu.model.icon }}</span>
          <span>{{ menu.model.name }}</span>
        </div>
        <span class="material-symbols-outlined text-xs transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
      </button>

      <div v-if="open" class="ml-4 mt-2 space-y-1">
        <SideBarItem v-for="child in menu.children" :key="child.id" :menu="child" />
      </div>
    </div>

    <a v-else :href="menu.url"
       :class="[
         'flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors',
         isActive ? 'bg-blue-50 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : ''
       ]">
      <span v-if="menu.model.icon" class="material-symbols-outlined w-5 mr-3">{{ menu.model.icon }}</span>
      <span>{{ menu.model.name }}</span>
    </a>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  menu: { type: Object, required: true }
})

const open = ref(props.menu.isActive)
const isActive = props.menu.isActive
</script>
