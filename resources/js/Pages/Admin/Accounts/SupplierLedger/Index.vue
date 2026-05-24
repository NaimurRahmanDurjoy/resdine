<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Page Header -->
      <div class="bg-white dark:bg-gray-800">
        <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Supplier Balances</h1>
              <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Accounts Payable — outstanding balances per supplier</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Content -->
      <ListTable :headers="headers" :items="suppliers.data" :pagination="suppliers" :loading="loading">
        <template #rows="{ items }">
          <tr v-for="s in items" :key="s.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
            <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
              {{ s.name }}
            </td>
            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
              {{ s.company_name || 'N/A' }}
            </td>
            <td class="px-6 py-2 whitespace-nowrap text-sm font-bold text-right"
                :class="parseFloat(s.current_balance || 0) > 0 ? 'text-red-600' : 'text-emerald-600'">
              {{ currency() }}{{ parseFloat(s.current_balance || 0).toLocaleString(undefined, {minimumFractionDigits: 2}) }}
            </td>
            <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
              <Link :href="route('admin.accounts.supplier-ledger.show', s.id)"
                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                <span class="material-symbols-outlined">visibility</span>
              </Link>
            </td>
          </tr>
        </template>

        <template #pagination>
          <div class="flex items-center justify-between w-full p-4 border-t border-gray-50">
            <div class="text-xs text-gray-500">
              Showing {{ suppliers.from }} to {{ suppliers.to }} of {{ suppliers.total }} entries
            </div>
            <div class="flex gap-1">
              <Link v-for="(link, k) in suppliers.links" :key="k" :href="link.url || '#'" v-html="link.label"
                class="px-2 py-1 border text-[10px] font-bold rounded transition-all"
                :class="[link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-400 border-gray-200 hover:bg-gray-50']" />
            </div>
          </div>
        </template>
      </ListTable>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'
defineOptions({ layout: AdminLayout })

defineProps({ suppliers: Object, pageTitle: String })

const loading = ref(false)

const headers = [
  { label: 'Supplier', key: 'name', sortable: false },
  { label: 'Company', key: 'company_name', sortable: false },
  { label: 'Balance', key: 'balance', sortable: false },
  { label: 'Actions', key: 'actions', sortable: false }
]
</script>
