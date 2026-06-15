<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div
        class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }} - {{ date }}</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Attendance records for today. (Use the POS or
              Biometric integration to mark check-in/out dynamically).</p>
          </div>
        </div>
      </div>
    </div>

    <ListTable :headers="headers" :items="attendances.data" :pagination="attendances" :loading="false">
      <template #rows="{ items }">
        <tr v-for="att in items" :key="att.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">{{ att.employee?.user?.name }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-600">{{ att.check_in || '--:--' }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-600">{{ att.check_out || '--:--' }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-900 font-bold">{{ att.status }}</div>
          </td>
        </tr>
      </template>
      <template #pagination v-if="attendances.links">
        <div class="flex items-center justify-between w-full">
          <div class="text-sm text-gray-700 dark:text-gray-400">
            Showing <span class="font-medium">{{ attendances.from }}</span> to <span class="font-medium">{{
              attendances.to }}</span> of <span class="font-medium">{{ attendances.total }}</span> entries
          </div>
          <div class="flex space-x-1">
            <Link v-for="(link, k) in attendances.links" :key="k" :href="link.url || '#'" v-html="link.label"
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
  attendances: Object,
  date: String,
  pageTitle: String
});

const headers = [
  { label: 'Employee', key: 'employee', sortable: false },
  { label: 'Check In', key: 'check_in', sortable: false },
  { label: 'Check Out', key: 'check_out', sortable: false },
  { label: 'Status', key: 'status', sortable: false }
];
</script>
