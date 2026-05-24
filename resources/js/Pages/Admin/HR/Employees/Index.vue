<template>
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ pageTitle }}</h1>
        <Link :href="route('admin.hr.employees.create')" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
          Add Employee
        </Link>
      </div>

      <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full whitespace-nowrap">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Code</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Designation</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="emp in employees.data" :key="emp.id">
              <td class="px-6 py-4">{{ emp.employee_code }}</td>
              <td class="px-6 py-4">{{ emp.user.name }}</td>
              <td class="px-6 py-4">{{ emp.designation }}</td>
              <td class="px-6 py-4">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" v-if="emp.status">Active</span>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800" v-else>Inactive</span>
              </td>
            </tr>
            <tr v-if="employees.data.length === 0">
              <td colspan="4" class="px-6 py-4 text-center text-gray-500">No employees found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
defineOptions({ layout: AdminLayout })

defineProps({
    employees: Object,
    pageTitle: String
});
</script>
