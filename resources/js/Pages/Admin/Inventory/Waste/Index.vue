<template>
  <AdminLayout :title="pageTitle">
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ pageTitle }}</h1>
        <Link :href="route('admin.inventory.waste.create')" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Log Waste</Link>
      </div>
      <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full whitespace-nowrap">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="log in wasteLogs.data" :key="log.id">
              <td class="px-6 py-4">{{ log.date }}</td>
              <td class="px-6 py-4">{{ log.inventory_item?.name }}</td>
              <td class="px-6 py-4">{{ log.branch?.name }}</td>
              <td class="px-6 py-4 text-red-600 font-bold">-{{ log.quantity }} {{ log.unit?.name }}</td>
              <td class="px-6 py-4">{{ log.cost }}</td>
              <td class="px-6 py-4">{{ log.reason }}</td>
            </tr>
            <tr v-if="wasteLogs.data.length === 0">
              <td colspan="6" class="px-6 py-4 text-center text-gray-500">No waste logs found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ wasteLogs: Object, pageTitle: String });
</script>
