<template>
  <div class="bg-white p-6 rounded-xl shadow-sm">
    <div class="flex justify-between items-center mb-4">
      <div class="text-lg font-semibold text-gray-800">{{ title }}</div>
      <slot name="icon"></slot>
    </div>

    <ul class="space-y-3">
      <li v-for="item in items" :key="item.id" class="flex justify-between items-center p-2 hover:bg-gray-50 rounded-lg">
        <slot name="item" :item="item">
          <!-- default template if slot not provided -->
          <div>
            <div class="font-medium">{{ item.name }}</div>
            <div class="text-xs text-gray-500">{{ item.details }}</div>
          </div>
          <span :class="item.statusClass">{{ item.status }}</span>
        </slot>
      </li>
      <li v-if="items.length === 0" class="text-center text-gray-500 py-4">{{ emptyText }}</li>
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