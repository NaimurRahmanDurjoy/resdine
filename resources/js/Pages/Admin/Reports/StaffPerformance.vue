<template>

    <div class="space-y-6">
      <!-- Page Header -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Staff Performance</h1>
              <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Revenue &amp; order metrics per team member — Last <span class="font-bold">{{ months }}</span> month(s)</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Staff Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <ListTable :headers="headers" :items="staff" :loading="loading">
          <template #rows="{ items }">
            <tr v-for="(s, idx) in items" :key="s.user_id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
              <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-400">
                <span class="text-xs px-2 py-0.5 font-bold rounded-full border"
                      :class="idx === 0 ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-gray-50 text-gray-400 border-gray-200'">
                  #{{ idx + 1 }}
                </span>
              </td>
              <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ s.name }}
              </td>
              <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">
                {{ s.total_orders }}
              </td>
              <td class="px-6 py-2 whitespace-nowrap text-sm font-bold text-emerald-600 text-right">
                {{ currency() }}{{ s.total_revenue.toLocaleString(undefined, {minimumFractionDigits: 2}) }}
              </td>
              <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">
                {{ currency() }}{{ s.avg_ticket.toLocaleString(undefined, {minimumFractionDigits: 2}) }}
              </td>
            </tr>
          </template>
        </ListTable>

        <div v-if="staff.length === 0" class="p-20 text-center">
          <span class="material-symbols-outlined text-gray-200 text-6xl mb-4">group</span>
          <h3 class="text-gray-400 font-bold">No staff performance data for this period</h3>
        </div>
      </div>
    </div>

</template>

<script setup>
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'
defineOptions({ layout: AdminLayout })

defineProps({ staff: Array, months: Number, pageTitle: String })

const loading = ref(false)

const headers = [
  { label: 'Rank', key: 'rank', sortable: false },
  { label: 'Staff Member', key: 'name', sortable: false },
  { label: 'Orders', key: 'total_orders', sortable: false },
  { label: 'Revenue', key: 'total_revenue', sortable: false },
  { label: 'Avg Ticket', key: 'avg_ticket', sortable: false }
]
</script>
