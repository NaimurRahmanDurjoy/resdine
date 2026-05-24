<template>
    <div class="p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ pageTitle }}</h1>
      <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full whitespace-nowrap">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Range</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="leave in leaves.data" :key="leave.id">
              <td class="px-6 py-4">{{ leave.employee?.user?.name }}</td>
              <td class="px-6 py-4">{{ leave.start_date }} to {{ leave.end_date }}</td>
              <td class="px-6 py-4">{{ leave.type }}</td>
              <td class="px-6 py-4">{{ leave.status }}</td>
              <td class="px-6 py-4">
                  <!-- Action buttons to approve/reject would go here -->
              </td>
            </tr>
            <tr v-if="leaves.data.length === 0">
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">No leave requests found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
defineOptions({ layout: AdminLayout })

defineProps({
    leaves: Object,
    pageTitle: String
});
</script>
