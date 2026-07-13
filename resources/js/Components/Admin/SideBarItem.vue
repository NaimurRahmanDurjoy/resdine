<template>
  <div class="space-y-0.5">
    <!-- Menu with children -->
    <div v-if="menu.hasChildren">
      <button @click="open = !open"
              :class="[
                'w-full flex items-center px-3.5 py-2.5 rounded-lg text-sm transition-all duration-150 justify-between font-medium border-l-2 focus:outline-none',
                isActive 
                  ? 'bg-[color:var(--accent-soft)] text-[color:var(--accent-primary)] border-[color:var(--accent-primary)]' 
                  : 'text-[color:var(--text-secondary)] hover:text-[color:var(--text-primary)] hover:bg-[color:var(--bg-elevated)] border-transparent'
              ]">
        <div class="flex items-center">
          <span v-if="menu.icon" class="material-symbols-outlined text-[20px] mr-3">{{ menu.icon }}</span>
          <span>{{ menu.name }}</span>
        </div>
        <span class="material-symbols-outlined text-[16px] transition-transform duration-200" :class="{ 'rotate-180': open }">expand_more</span>
      </button>
      <div v-if="open" class="pl-4 pr-1 py-1 space-y-0.5 border-l border-[color:var(--border-subtle)] ml-4.5 mt-0.5">
        <SideBarItem v-for="child in menu.children" :key="child.id" :menu="child" />
      </div>
    </div>

    <!-- Single menu item -->
    <Link v-else :href="menu.url"
       :class="[
         'flex items-center px-3.5 py-2.5 rounded-lg text-sm transition-all duration-150 font-medium border-l-2',
         isActive 
           ? 'bg-[color:var(--accent-soft)] text-[color:var(--accent-primary)] font-semibold border-[color:var(--accent-primary)]' 
           : 'text-[color:var(--text-secondary)] hover:text-[color:var(--text-primary)] hover:bg-[color:var(--bg-elevated)] border-transparent'
       ]">
      <span v-if="menu.icon" class="material-symbols-outlined text-[20px] mr-3">{{ menu.icon }}</span>
      <span>{{ menu.name }}</span>
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
