<template>
  <AdminLayout :title="pageTitle">
    <div class="p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ pageTitle }}</h1>
      <p class="text-gray-500 mb-6">Last {{ weeks }} weeks of order data</p>

      <!-- Heatmap Grid -->
      <div class="bg-white rounded-lg shadow p-6 overflow-x-auto">
        <div class="grid" style="grid-template-columns: 60px repeat(24, 1fr); gap: 2px;">
          <!-- Header Row (Hours) -->
          <div class="text-xs text-gray-400"></div>
          <div v-for="h in 24" :key="'h-'+h" class="text-xs text-gray-500 text-center">{{ h - 1 }}</div>

          <!-- Data Rows -->
          <template v-for="day in dayNames" :key="day">
            <div class="text-xs font-medium text-gray-600 flex items-center">{{ day }}</div>
            <div
              v-for="hour in 24"
              :key="day + '-' + hour"
              class="w-full aspect-square rounded-sm flex items-center justify-center text-[10px] text-white font-bold"
              :style="{ backgroundColor: getColor(matrix[day]?.[hour - 1]?.orders || 0) }"
              :title="`${day} ${hour - 1}:00 — ${matrix[day]?.[hour - 1]?.orders || 0} orders`"
            >
              {{ matrix[day]?.[hour - 1]?.orders || '' }}
            </div>
          </template>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ matrix: Object, dayNames: Array, weeks: Number, pageTitle: String });

// Get the max order count for scaling
const allOrders = Object.values(props.matrix || {}).flatMap(hours => Object.values(hours).map(h => h.orders || 0));
const maxOrders = Math.max(...allOrders, 1);

const getColor = (count) => {
  if (count === 0) return '#f3f4f6';
  const intensity = Math.min(count / maxOrders, 1);
  const r = Math.round(99 - intensity * 99);
  const g = Math.round(102 + intensity * 50);
  const b = Math.round(241);
  return `rgba(99, 102, 241, ${0.15 + intensity * 0.85})`;
};
</script>
