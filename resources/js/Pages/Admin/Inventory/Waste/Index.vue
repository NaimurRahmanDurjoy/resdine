<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div
        class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }}</h1>
          <Link :href="route('admin.inventory.waste.create')"
            class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700 flex items-center space-x-2 transition">
            <span class="material-symbols-outlined text-sm">add</span>
            <span>Log Waste</span>
          </Link>
        </div>
      </div>
    </div>

    <ListTable :headers="headers" :items="wasteLogs.data" :pagination="wasteLogs" :loading="false">
      <template #rows="{ items }">
        <tr v-for="log in items" :key="log.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">{{ log.date }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-600">{{ log.inventory_item?.name }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-600">{{ log.branch?.name }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-red-600 font-bold">-{{ log.quantity }} {{ log.unit?.name }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-900">{{ log.cost }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-600">{{ log.reason }}</div>
          </td>
        </tr>
      </template>
      <template #pagination v-if="wasteLogs.links">
        <div class="flex items-center justify-between w-full">
          <div class="text-sm text-gray-700 dark:text-gray-400">
            Showing <span class="font-medium">{{ wasteLogs.from }}</span> to <span class="font-medium">{{ wasteLogs.to
              }}</span> of <span class="font-medium">{{ wasteLogs.total }}</span> entries
          </div>
          <div class="flex space-x-1">
            <Link v-for="(link, k) in wasteLogs.links" :key="k" :href="link.url || '#'" v-html="link.label"
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

defineProps({ wasteLogs: Object, pageTitle: String });

const headers = [
  { label: 'Date', key: 'date', sortable: false },
  { label: 'Item', key: 'item', sortable: false },
  { label: 'Branch', key: 'branch', sortable: false },
  { label: 'Qty', key: 'qty', sortable: false },
  { label: 'Cost', key: 'cost', sortable: false },
  { label: 'Reason', key: 'reason', sortable: false }
];
</script>
