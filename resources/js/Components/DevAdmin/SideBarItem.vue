<template>
  <div>
    <div v-if="menu.hasChildren">
      <button @click="open = !open" :class="[
        'flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg transition-colors',
        isActive ? 'bg-gray-700 text-cyan-400' : 'text-gray-400 hover:text-white hover:bg-gray-800'
      ]">
        <div class="flex items-center">
          <span v-if="menu.icon" class="material-symbols-outlined mr-3 text-base font-icon">{{ menu.icon }}</span>
          <span>{{ menu.name }}</span>
        </div>
        <span class="material-symbols-outlined text-base transition-transform font-icon"
          :class="{ 'rotate-180': open }">expand_more</span>
      </button>

      <div v-if="open" class="mt-1 space-y-1 pl-4 border-l border-gray-800 ml-5">
        <SideBarItem v-for="child in menu.children" :key="child.id" :menu="child" />
      </div>
    </div>

    <a v-else :href="menu.url" :class="[
      'block px-3 py-1.5 text-sm font-medium rounded-md transition-colors duration-150',
      isActive ? 'text-cyan-400' : 'text-gray-500 hover:text-white'
    ]">
      <span v-if="menu.icon" class="material-symbols-outlined mr-3 text-base font-icon">{{ menu.icon }}</span>
      <span>{{ menu.name }}</span>
    </a>
  </div>
</template>

<script setup>
import { ref, defineProps } from 'vue'
import SideBarItem from '@/Components/DevAdmin/SideBarItem.vue'

const props = defineProps({
  menu: { type: Object, required: true }
})

const open = ref(props.menu.isActive)
const isActive = props.menu.isActive
</script>

<style scoped>
.font-icon {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
