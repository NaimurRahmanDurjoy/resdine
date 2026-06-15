<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div
        class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }}</h1>
      </div>
    </div>

    <ListTable :headers="headers" :items="leaves.data" :pagination="leaves" :loading="false">
      <template #rows="{ items }">
        <tr v-for="leave in items" :key="leave.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">{{ leave.employee?.user?.name }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-600">{{ leave.start_date }} to {{ leave.end_date }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-600">{{ leave.type }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-900">{{ leave.status }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <!-- Action buttons to approve/reject would go here -->
          </td>
        </tr>
      </template>
      <template #pagination v-if="leaves.links">
        <div class="flex items-center justify-between w-full">
          <div class="text-sm text-gray-700 dark:text-gray-400">
            Showing <span class="font-medium">{{ leaves.from }}</span> to <span class="font-medium">{{ leaves.to
              }}</span> of <span class="font-medium">{{ leaves.total }}</span> entries
          </div>
          <div class="flex space-x-1">
            <Link v-for="(link, k) in leaves.links" :key="k" :href="link.url || '#'" v-html="link.label"
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
  leaves: Object,
  pageTitle: String
});

const headers = [
  { label: 'Employee', key: 'employee', sortable: false },
  { label: 'Date Range', key: 'date_range', sortable: false },
  { label: 'Type', key: 'type', sortable: false },
  { label: 'Status', key: 'status', sortable: false },
  { label: 'Action', key: 'action', sortable: false }
];
</script>
