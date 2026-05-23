<template>
  <AdminLayout :title="pageTitle">
    <div class="p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ pageTitle }}</h1>
      <p class="text-gray-500 mb-6">Total Consumption Value: <span class="font-bold text-gray-800">{{ totalValue.toFixed(2) }}</span> (Last {{ months }} months)</p>

      <!-- Category Summary -->
      <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
          <div class="text-3xl font-bold text-red-600">{{ countCategory('A') }}</div>
          <div class="text-sm text-red-500 font-medium">Category A (Critical)</div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
          <div class="text-3xl font-bold text-yellow-600">{{ countCategory('B') }}</div>
          <div class="text-sm text-yellow-500 font-medium">Category B (Moderate)</div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
          <div class="text-3xl font-bold text-green-600">{{ countCategory('C') }}</div>
          <div class="text-sm text-green-500 font-medium">Category C (Low Value)</div>
        </div>
      </div>

      <!-- Items Table -->
      <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full whitespace-nowrap">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Consumed</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Value</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">% of Total</th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Category</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="item in items" :key="item.id">
              <td class="px-6 py-4 font-medium">{{ item.name }}</td>
              <td class="px-6 py-4 text-right">{{ item.total_consumed }}</td>
              <td class="px-6 py-4 text-right">{{ item.consumption_value }}</td>
              <td class="px-6 py-4 text-right">{{ item.percent_of_total }}%</td>
              <td class="px-6 py-4 text-center">
                <span :class="categoryClass(item.category)" class="px-2 py-1 rounded text-xs font-bold">{{ item.category }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ items: Array, totalValue: Number, months: Number, pageTitle: String });

const countCategory = (cat) => props.items.filter(i => i.category === cat).length;

const categoryClass = (cat) => ({
  'A': 'bg-red-100 text-red-700',
  'B': 'bg-yellow-100 text-yellow-700',
  'C': 'bg-green-100 text-green-700',
}[cat] || '');
</script>
