<template>
  <AdminLayout :title="pageTitle">
    <div class="p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ pageTitle }}</h1>
      <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full whitespace-nowrap">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month/Year</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Basic</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Net Salary</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="payroll in payrolls.data" :key="payroll.id">
              <td class="px-6 py-4">{{ payroll.employee?.user?.name }}</td>
              <td class="px-6 py-4">{{ payroll.month }} {{ payroll.year }}</td>
              <td class="px-6 py-4">{{ payroll.basic_salary }}</td>
              <td class="px-6 py-4 font-bold">{{ payroll.net_salary }}</td>
              <td class="px-6 py-4">{{ payroll.payment_status }}</td>
            </tr>
            <tr v-if="payrolls.data.length === 0">
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">No payroll records found.</td>
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
    payrolls: Object,
    pageTitle: String
});
</script>
