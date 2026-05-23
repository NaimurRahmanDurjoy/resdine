<template>
  <AdminLayout :title="pageTitle">
    <div class="p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ pageTitle }}</h1>
      <p class="text-gray-500 mb-6">Last {{ months }} month(s)</p>

      <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full whitespace-nowrap">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff Member</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Orders</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Avg Ticket</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="(s, idx) in staff" :key="s.user_id">
              <td class="px-6 py-4">{{ idx + 1 }}</td>
              <td class="px-6 py-4 font-medium">{{ s.name }}</td>
              <td class="px-6 py-4 text-right">{{ s.total_orders }}</td>
              <td class="px-6 py-4 text-right font-bold text-indigo-600">{{ s.total_revenue }}</td>
              <td class="px-6 py-4 text-right">{{ s.avg_ticket }}</td>
            </tr>
            <tr v-if="staff.length === 0">
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">No data available for this period.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ staff: Array, months: Number, pageTitle: String });
</script>
