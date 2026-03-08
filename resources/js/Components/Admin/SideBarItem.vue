<template>
  <div>
    <!-- Menu with children -->
    <div v-if="menu.hasChildren">
      <button @click="open = !open"
              :class="[
                'w-full flex items-center px-4 py-3 rounded transition-colors justify-between',
                isActive ? 'border-l-4 border-blue-500 dark:border-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-200'
              ]">
        <div class="flex items-center ">
          <span v-if="menu.model.icon" class="material-symbols-outlined w-5 mr-3">{{ menu.model.icon }}</span>
          <span>{{ menu.model.name }}</span>
        </div>
        <span class="material-symbols-outlined text-xs transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
      </button>
      <div v-if="open" class="ml-4 mt-2 space-y-1">
        <SideBarItem v-for="child in menu.children" :key="child.id" :menu="child" />
      </div>
    </div>

    <!-- Single menu item -->
    <Link v-else :href="menu.url"
       :class="[
         'flex items-center px-4 py-3 rounded hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors',
         isActive ? 'border-l-4 border-blue-500 dark:border-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-200'
       ]">
      <span v-if="menu.model.icon" class="material-symbols-outlined w-5 mr-3">{{ menu.model.icon }}</span>
      <span>{{ menu.model.name }}</span>
    </Link>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  menu: { type: Object, required: true }
})

const open = ref(props.menu.isActive)
const isActive = computed(() => props.menu.isActive)

// Keep submenus open if they become active
watch(() => props.menu.isActive, (val) => {
  if (val) open.value = true
})
</script>
