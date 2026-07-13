<template>
  <div class="theme-panel p-6">
    <div class="flex justify-between items-center mb-5">
      <div class="text-base font-bold text-[color:var(--text-primary)] tracking-tight">{{ title }}</div>
      <slot name="icon"></slot>
    </div>

    <ul class="divide-y divide-[color:var(--border-subtle)]">
      <li v-for="item in items" :key="item.id" class="flex justify-between items-center py-3.5 first:pt-0 last:pb-0 transition-colors duration-150 rounded-lg hover:bg-[color:var(--bg-elevated)] px-2 -mx-2">
        <slot name="item" :item="item">
          <!-- default template if slot not provided -->
          <div>
            <div class="font-medium text-sm text-[color:var(--text-primary)]">{{ item.name }}</div>
            <div class="text-xs text-[color:var(--text-secondary)] mt-0.5">{{ item.details }}</div>
          </div>
          <span :class="['text-xs px-2 py-1 rounded-full font-medium', item.statusClass || 'bg-[color:var(--bg-muted)] text-[color:var(--text-secondary)]']">{{ item.status }}</span>
        </slot>
      </li>
      <li v-if="items.length === 0" class="text-center text-xs text-[color:var(--text-muted)] py-6 italic">{{ emptyText }}</li>
    </ul>

    <slot name="footer"></slot>
  </div>
</template>

<script setup>
const props = defineProps({
  title: String,
  items: { type: Array, default: () => [] },
  emptyText: { type: String, default: 'No data available' }
})
</script>