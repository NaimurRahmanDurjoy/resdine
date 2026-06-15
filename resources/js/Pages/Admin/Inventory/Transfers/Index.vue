<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div
        class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }}</h1>
          <Link :href="route('admin.inventory.transfers.create')"
            class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 flex items-center space-x-2 transition">
            <span class="material-symbols-outlined text-sm">add</span>
            <span>New Transfer</span>
          </Link>
        </div>
      </div>
    </div>

    <ListTable :headers="headers" :items="transfers.data" :pagination="transfers" :loading="false">
      <template #rows="{ items }">
        <tr v-for="t in items" :key="t.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm font-bold text-gray-900">{{ t.reference_no }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-600">{{ t.transfer_date }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-600">{{ t.from_branch?.name }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-600">{{ t.to_branch?.name }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
              v-if="t.status === 'Pending'">Pending</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800"
              v-else-if="t.status === 'Dispatched'">Dispatched</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"
              v-else>Received</span>
          </td>
          <td class="px-6 py-2 whitespace-nowrap text-sm">
            <Link v-if="t.status === 'Pending'" :href="route('admin.inventory.transfers.dispatch', t.id)" method="post"
              as="button" class="text-indigo-600 hover:underline font-semibold">Dispatch</Link>
            <Link v-if="t.status === 'Dispatched'" :href="route('admin.inventory.transfers.receive', t.id)"
              method="post" as="button" class="text-green-600 hover:underline font-semibold">Receive</Link>
          </td>
        </tr>
      </template>
      <template #pagination v-if="transfers.links">
        <div class="flex items-center justify-between w-full">
          <div class="text-sm text-gray-700 dark:text-gray-400">
            Showing <span class="font-medium">{{ transfers.from }}</span> to <span class="font-medium">{{ transfers.to
              }}</span> of <span class="font-medium">{{ transfers.total }}</span> entries
          </div>
          <div class="flex space-x-1">
            <Link v-for="(link, k) in transfers.links" :key="k" :href="link.url || '#'" v-html="link.label"
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

defineProps({ transfers: Object, pageTitle: String });

const headers = [
  { label: 'Ref No', key: 'ref_no', sortable: false },
  { label: 'Date', key: 'date', sortable: false },
  { label: 'From', key: 'from', sortable: false },
  { label: 'To', key: 'to', sortable: false },
  { label: 'Status', key: 'status', sortable: false },
  { label: 'Action', key: 'action', sortable: false }
];
</script>
