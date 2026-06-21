<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div
        class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }}</h1>
      </div>
      <!-- Actions -->
      <div class="m-4 flex justify-end items-center">
        <Link :href="route('admin.hr.employees.create')"
          class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-green-700 transition flex items-center space-x-2">
          <span class="material-symbols-outlined text-sm">add</span>
          <span>Add Employee</span>
        </Link>
      </div>
    </div>

    <ListTable :headers="headers" :items="employees.data" :pagination="employees" :loading="false">
      <template #rows="{ items }">
        <tr v-for="emp in items" :key="emp.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">{{ emp.employee_code }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-600">{{ emp.user?.name }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-600">{{ emp.designation }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
              v-if="emp.status">Active</span>
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"
              v-else>Inactive</span>
          </td>
        </tr>
      </template>
      <template #pagination v-if="employees.links">
        <div class="flex items-center justify-between w-full">
          <div class="text-sm text-gray-700 dark:text-gray-400">
            Showing <span class="font-medium">{{ employees.from }}</span> to <span class="font-medium">{{ employees.to
              }}</span> of <span class="font-medium">{{ employees.total }}</span> entries
          </div>
          <div class="flex space-x-1">
            <Link v-for="(link, k) in employees.links" :key="k" :href="link.url || '#'" v-html="link.label"
              class="relative inline-flex items-center px-3 py-1 border text-xs font-medium transition-colors rounded shadow-sm"
              :class="[link.active ? 'z-10 bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700', !link.url ? 'cursor-not-allowed opacity-50' : '']" />
          </div>
        </div>
      </template>
    </ListTable>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ListTable from '@/Components/ListTable.vue';
defineOptions({ layout: AdminLayout })

defineProps({
  employees: Object,
  pageTitle: String
});

const headers = [
  { label: 'Employee Code', key: 'employee_code', sortable: false },
  { label: 'Name', key: 'name', sortable: false },
  { label: 'Designation', key: 'designation', sortable: false },
  { label: 'Status', key: 'status', sortable: false }
];
</script>
