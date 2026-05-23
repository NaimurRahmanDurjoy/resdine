<template>
  <AdminLayout :title="pageTitle">
    <div class="p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ pageTitle }} - {{ date }}</h1>
      <div class="bg-white rounded-lg shadow p-6 max-w-4xl mx-auto">
        <p class="text-gray-600 mb-4">Attendance records for today. (Use the POS or Biometric integration to mark check-in/out dynamically).</p>
        
        <table class="w-full whitespace-nowrap">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check In</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check Out</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="att in attendances" :key="att.id">
              <td class="px-6 py-4">{{ att.employee?.user?.name }}</td>
              <td class="px-6 py-4">{{ att.check_in || '--:--' }}</td>
              <td class="px-6 py-4">{{ att.check_out || '--:--' }}</td>
              <td class="px-6 py-4">{{ att.status }}</td>
            </tr>
            <tr v-if="attendances.length === 0">
              <td colspan="4" class="px-6 py-4 text-center text-gray-500">No attendance records for this date.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    attendances: Array,
    date: String,
    pageTitle: String
});
</script>
