<template>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white/50 dark:bg-gray-800/50 p-4 rounded-xl backdrop-blur-sm border border-white/20 dark:border-gray-700/30">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Batch Production</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Track and manage your kitchen production batches.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <Link :href="route('admin.production.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 flex items-center space-x-2 transition-all shadow-lg shadow-indigo-500/20 active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>New Production Batch</span>
                </Link>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
            <ListTable :headers="headers" :items="history.data" :pagination="history" :loading="loading">
                <template #rows="{ items }">
                    <tr v-for="batch in items" :key="batch.id"
                        class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors border-b border-gray-50 dark:border-gray-700/50 last:border-0">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ new Date(batch.transaction_date).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs">
                                    {{ (batch.product_item?.name || '??').charAt(0).toUpperCase() }}
                                </div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ batch.product_item?.name || 'Unknown Product' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ batch.qty_in }} {{ batch.unit?.name || '' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ formatCurrency(batch.unit_cost) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                            {{ formatCurrency(batch.qty_in * batch.unit_cost) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                COMPLETED
                            </span>
                        </td>
                    </tr>
                </template>
                
                <template #empty>
                    <div class="py-12 flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                        <span class="material-symbols-outlined text-5xl mb-4 opacity-20">inventory_2</span>
                        <p class="text-lg font-medium">No production history found</p>
                        <p class="text-sm">Start recording production batches to track them here.</p>
                    </div>
                </template>
            </ListTable>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    history: Object,
    pageTitle: String
})

const loading = ref(false)

const headers = [
    { label: 'Date', key: 'transaction_date' },
    { label: 'Product / Prep Item', key: 'product' },
    { label: 'Quantity', key: 'qty' },
    { label: 'Unit Cost', key: 'unit_cost' },
    { label: 'Total Batch Value', key: 'total_value' },
    { label: 'Status', key: 'status' }
]

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value)
}
</script>
