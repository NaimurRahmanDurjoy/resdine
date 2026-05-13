<template>
  <AdminLayout :title="pageTitle">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Page Header -->
      <div class="bg-white dark:bg-gray-800">
        <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Account Vouchers</h1>
              <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Transaction history and ledger records</p>
            </div>
            <Link :href="route('admin.accounts.vouchers.create')" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 flex items-center space-x-2 transition">
              <span class="material-symbols-outlined text-sm">add</span>
              <span>New Voucher</span>
            </Link>
          </div>
        </div>
      </div>

      <!-- Vouchers List -->
      <ListTable :headers="headers" :items="vouchers.data" :pagination="vouchers" :loading="loading">
        <template #rows="{ items }">
          <tr v-for="voucher in items" :key="voucher.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
            <td class="px-6 py-2 whitespace-nowrap text-sm font-bold text-gray-900">
              {{ voucher.voucher_no }}
            </td>
            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
              {{ voucher.voucher_date }}
            </td>
            <td class="px-6 py-2 whitespace-nowrap">
              <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-bold rounded border border-gray-200">
                {{ voucher.voucher_type?.name }}
              </span>
            </td>
            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-400 truncate max-w-[200px]">
              {{ voucher.reference_no || 'N/A' }}
            </td>
            <td class="px-6 py-2 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
              ${{ parseFloat(voucher.total_debit).toLocaleString() }}
            </td>
            <td class="px-6 py-2 whitespace-nowrap text-center">
              <span v-if="voucher.status === 1" class="px-2 py-0.5 bg-yellow-50 text-yellow-600 text-[10px] font-bold rounded-full border border-yellow-100">Draft</span>
              <span v-else-if="voucher.status === 2" class="px-2 py-0.5 bg-green-50 text-green-600 text-[10px] font-bold rounded-full border border-green-100">Approved</span>
              <span v-else class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-bold rounded-full border border-red-100">Cancelled</span>
            </td>
            <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
              <Link :href="route('admin.accounts.vouchers.show', voucher.id)" class="text-indigo-600 hover:text-indigo-900">
                <span class="material-symbols-outlined">visibility</span>
              </Link>
            </td>
          </tr>
        </template>

        <template #pagination>
          <div class="flex items-center justify-between w-full p-4 border-t border-gray-50">
            <div class="text-xs text-gray-500">
              Showing {{ vouchers.from }} to {{ vouchers.to }} of {{ vouchers.total }} entries
            </div>
            <div class="flex gap-1">
               <Link v-for="(link, k) in vouchers.links" :key="k" :href="link.url || '#'" v-html="link.label"
                  class="px-2 py-1 border text-[10px] font-bold rounded transition-all"
                  :class="[link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-400 border-gray-200 hover:bg-gray-50']" />
            </div>
          </div>
        </template>
      </ListTable>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'

defineProps({
  vouchers: Object,
  pageTitle: String
})

const loading = ref(false)

const headers = [
  { label: 'Voucher No', key: 'voucher_no', sortable: false },
  { label: 'Date', key: 'date', sortable: false },
  { label: 'Type', key: 'type', sortable: false },
  { label: 'Reference', key: 'ref', sortable: false },
  { label: 'Amount', key: 'amount', sortable: false },
  { label: 'Status', key: 'status', sortable: false },
  { label: 'Actions', key: 'actions', sortable: false }
]
</script>
