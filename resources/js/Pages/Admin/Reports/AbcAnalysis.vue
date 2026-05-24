<template>
    <div class="space-y-6">
      <!-- Page Header -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">ABC Inventory Analysis</h1>
              <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                Classification by consumption value — Last <span class="font-bold">{{ months }}</span> months
                · Total: <span class="font-bold text-gray-800">{{ currency() }}{{ totalValue.toLocaleString(undefined, {minimumFractionDigits: 2}) }}</span>
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Category Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="flex flex-col bg-white dark:bg-slate-800 shadow-sm rounded-xl border border-slate-200 dark:border-slate-700 p-5">
          <div class="text-slate-500 text-sm font-semibold mb-2">Category A — Critical</div>
          <div class="flex items-center">
            <div class="text-3xl font-bold text-red-600 mr-2">{{ countCategory('A') }}</div>
            <div class="text-sm font-medium text-red-500 bg-red-100 dark:bg-red-500/20 px-2 py-0.5 rounded-full">Top 80%</div>
          </div>
        </div>
        <div class="flex flex-col bg-white dark:bg-slate-800 shadow-sm rounded-xl border border-slate-200 dark:border-slate-700 p-5">
          <div class="text-slate-500 text-sm font-semibold mb-2">Category B — Moderate</div>
          <div class="flex items-center">
            <div class="text-3xl font-bold text-amber-600 mr-2">{{ countCategory('B') }}</div>
            <div class="text-sm font-medium text-amber-500 bg-amber-100 dark:bg-amber-500/20 px-2 py-0.5 rounded-full">Next 15%</div>
          </div>
        </div>
        <div class="flex flex-col bg-white dark:bg-slate-800 shadow-sm rounded-xl border border-slate-200 dark:border-slate-700 p-5">
          <div class="text-slate-500 text-sm font-semibold mb-2">Category C — Low Value</div>
          <div class="flex items-center">
            <div class="text-3xl font-bold text-emerald-600 mr-2">{{ countCategory('C') }}</div>
            <div class="text-sm font-medium text-emerald-500 bg-emerald-100 dark:bg-emerald-500/20 px-2 py-0.5 rounded-full">Last 5%</div>
          </div>
        </div>
      </div>

      <!-- Items Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <ListTable :headers="headers" :items="items" :loading="loading">
          <template #rows="{ items: rows }">
            <tr v-for="item in rows" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
              <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ item.name }}
              </td>
              <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">
                {{ item.total_consumed }}
              </td>
              <td class="px-6 py-2 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                {{ currency() }}{{ item.consumption_value.toLocaleString(undefined, {minimumFractionDigits: 2}) }}
              </td>
              <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">
                {{ item.percent_of_total }}%
              </td>
              <td class="px-6 py-2 whitespace-nowrap text-center">
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full border"
                      :class="categoryBadge(item.category)">
                  {{ item.category }}
                </span>
              </td>
            </tr>
          </template>
        </ListTable>
      </div>
    </div>

</template>

<script setup>
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'
defineOptions({ layout: AdminLayout })

const props = defineProps({ items: Array, totalValue: Number, months: Number, pageTitle: String })

const loading = ref(false)

const headers = [
  { label: 'Item Name', key: 'name', sortable: false },
  { label: 'Qty Consumed', key: 'total_consumed', sortable: false },
  { label: 'Value', key: 'consumption_value', sortable: false },
  { label: '% of Total', key: 'percent_of_total', sortable: false },
  { label: 'Category', key: 'category', sortable: false }
]

const countCategory = (cat) => props.items.filter(i => i.category === cat).length

const categoryBadge = (cat) => ({
  'A': 'bg-red-50 text-red-600 border-red-100',
  'B': 'bg-amber-50 text-amber-600 border-amber-100',
  'C': 'bg-emerald-50 text-emerald-600 border-emerald-100',
}[cat] || '')
</script>
