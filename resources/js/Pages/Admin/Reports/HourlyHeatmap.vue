<template>
    <div class="space-y-6">
      <!-- Page Header -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Hourly Sales Heatmap</h1>
              <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Order frequency by day &amp; hour — Last <span class="font-bold">{{ weeks }}</span> weeks</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Heatmap Grid -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <div class="overflow-x-auto">
          <table class="w-full border-collapse">
            <thead>
              <tr>
                <th class="px-2 py-2 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left w-12"></th>
                <th v-for="h in 24" :key="'h-'+h"
                    class="px-0 py-2 text-[10px] font-black text-gray-400 text-center min-w-[32px]">
                  {{ h - 1 }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="day in dayNames" :key="day">
                <td class="px-2 py-1 text-xs font-bold text-gray-600 dark:text-gray-300">{{ day }}</td>
                <td v-for="hour in 24" :key="day + '-' + hour"
                    class="px-0 py-1 text-center">
                  <div class="w-7 h-7 mx-auto rounded-sm flex items-center justify-center text-[9px] font-bold transition-all cursor-default"
                       :style="{ backgroundColor: getColor(matrix[day]?.[hour - 1]?.orders || 0) }"
                       :class="(matrix[day]?.[hour - 1]?.orders || 0) > 0 ? 'text-white' : 'text-gray-300'"
                       :title="`${day} ${hour - 1}:00 — ${matrix[day]?.[hour - 1]?.orders || 0} orders, ${currency()}${(matrix[day]?.[hour - 1]?.revenue || 0).toFixed(2)}`">
                    {{ (matrix[day]?.[hour - 1]?.orders || 0) > 0 ? matrix[day][hour - 1].orders : '' }}
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Legend -->
        <div class="mt-6 flex items-center justify-center gap-2 text-xs text-gray-500">
          <span>Low</span>
          <div class="flex gap-0.5">
            <div class="w-5 h-5 rounded-sm" style="background-color: rgba(99, 102, 241, 0.15)"></div>
            <div class="w-5 h-5 rounded-sm" style="background-color: rgba(99, 102, 241, 0.35)"></div>
            <div class="w-5 h-5 rounded-sm" style="background-color: rgba(99, 102, 241, 0.55)"></div>
            <div class="w-5 h-5 rounded-sm" style="background-color: rgba(99, 102, 241, 0.75)"></div>
            <div class="w-5 h-5 rounded-sm" style="background-color: rgba(99, 102, 241, 1)"></div>
          </div>
          <span>High</span>
        </div>
      </div>
    </div>

</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout: AdminLayout })

const props = defineProps({ matrix: Object, dayNames: Array, weeks: Number, pageTitle: String })

// Get the max order count for scaling
const allOrders = Object.values(props.matrix || {}).flatMap(hours => Object.values(hours).map(h => h.orders || 0))
const maxOrders = Math.max(...allOrders, 1)

const getColor = (count) => {
  if (count === 0) return '#f9fafb' // gray-50
  const intensity = Math.min(count / maxOrders, 1)
  return `rgba(99, 102, 241, ${0.15 + intensity * 0.85})` // indigo-500 with variable opacity
}
</script>
