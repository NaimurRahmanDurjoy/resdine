<template>
  <div class="theme-panel p-6 border-l-4 transition-all duration-300 hover:shadow-md hover:translate-y-[-1px]" :style="{ borderLeftColor: computedBorderColor }">
    <div class="flex justify-between items-start">
      <div>
        <div class="text-[color:var(--text-secondary)] text-sm font-medium">{{ title }}</div>
        <div class="text-2xl font-extrabold tracking-tight mt-2 text-[color:var(--text-primary)]">{{ formattedValue }}</div>
        <div v-if="subtitle" class="text-xs text-[color:var(--text-muted)] mt-1.5">{{ subtitle }}</div>
      </div>
      <div class="p-2.5 rounded-xl transition-colors duration-200" :style="{ backgroundColor: computedIconBg, color: computedIconColor }">
        <span class="material-symbols-outlined block text-xl">{{ icon }}</span>
      </div>
    </div>

    <div v-if="trend" :class="['mt-4 text-xs flex items-center font-semibold', trend.up ? 'text-emerald-500 dark:text-emerald-400' : 'text-rose-500 dark:text-rose-400']">
      <span class="material-symbols-outlined text-sm mr-1">
        {{ trend.up ? 'trending_up' : 'trending_down' }}
      </span>
      <span>{{ trend.value }}% {{ trend.up ? 'increase' : 'decrease' }} vs yesterday</span>
    </div>

    <slot></slot>
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

const colorMap = {
  indigo: { border: '#6366f1', bg: 'rgba(99, 102, 241, 0.1)', text: '#6366f1' },
  green: { border: '#10b981', bg: 'rgba(16, 185, 129, 0.1)', text: '#10b981' },
  blue: { border: '#3b82f6', bg: 'rgba(59, 130, 246, 0.1)', text: '#3b82f6' },
  purple: { border: '#a855f7', bg: 'rgba(168, 85, 247, 0.1)', text: '#a855f7' },
  amber: { border: '#f59e0b', bg: 'rgba(245, 158, 11, 0.1)', text: '#f59e0b' },
  red: { border: '#ef4444', bg: 'rgba(239, 68, 68, 0.1)', text: '#ef4444' }
}

const colorData = computed(() => colorMap[props.color] || colorMap.indigo)

const computedBorderColor = computed(() => colorData.value.border)
const computedIconBg = computed(() => colorData.value.bg)
const computedIconColor = computed(() => colorData.value.text)
</script>