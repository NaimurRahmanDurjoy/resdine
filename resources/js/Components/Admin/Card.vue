<template>
  <div :class="['bg-white p-6 rounded-xl shadow-sm border-l-4', borderClass]">
    <div class="flex justify-between items-start">
      <div>
        <div class="text-gray-500 text-sm font-medium">{{ title }}</div>
        <div class="text-2xl font-bold mt-2">{{ formattedValue }}</div>
        <div v-if="subtitle" class="text-sm text-gray-500 mt-1">{{ subtitle }}</div>
      </div>
      <div :class="iconBgClass">
        <span :class="iconClass">{{ icon }}</span>
      </div>
    </div>

    <div v-if="trend" :class="['mt-4 text-xs flex items-center', trend.up ? 'text-green-500' : 'text-red-500']">
      <span class="material-symbols-outlined text-sm mr-1">
        {{ trend.up ? 'trending_up' : 'trending_down' }}
      </span>
      <span>{{ trend.value }}% {{ trend.up ? 'increase' : 'decrease' }} from yesterday</span>
    </div>

    <slot></slot> <!-- extra content -->
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: String,
  value: [String, Number],
  icon: String,
  color: { type: String, default: 'indigo' },
  trend: Object, // { up: Boolean, value: Number }
  subtitle: [String, Number]
})

const formattedValue = computed(() => {
  return typeof props.value === 'number' ? `$${props.value.toFixed(2)}` : props.value
})

const borderClass = computed(() => `border-${props.color}-500`)
const iconBgClass = computed(() => `bg-${props.color}-100 p-3 rounded-full`)
const iconClass = computed(() => `material-symbols-outlined text-${props.color}-600`)
</script>