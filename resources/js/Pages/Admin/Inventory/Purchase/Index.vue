<template>
    <AdminLayout :pageTitle="pageTitle">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Page Header -->
            <div class="bg-white dark:bg-gray-800">
                <div
                    class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Purchase Orders</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage vendor purchases and stock intake</p>
                        </div>
                        <Link :href="route('admin.purchase.create')"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center gap-2 transition-all shadow-md shadow-indigo-100 dark:shadow-none">
                            <span class="material-symbols-outlined text-sm">add</span>
                            New Purchase
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <ListTable :headers="['Date', 'Invoice No', 'Supplier', 'Amount', 'Status', 'Actions']" :items="purchases">
                    <template #rows="{ items }">
                        <tr v-for="purchase in items" :key="purchase.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ new Date(purchase.purchase_date).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ purchase.invoice_no || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ purchase.supplier?.name || purchase.supplier?.company_name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100">
                                ${{ parseFloat(purchase.total_amount).toFixed(2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ purchase.status.charAt(0).toUpperCase() + purchase.status.slice(1) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">
                                    <Link :href="route('admin.purchase.show', purchase.id)"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </Link>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="items.length === 0">
                            <td colspan="6"
                                class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                No purchase orders found. Click "New Purchase" to record intake.
                            </td>
                        </tr>
                    </template>
                </ListTable>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'

const props = defineProps({
    purchases: Array,
    pageTitle: String
})
</script>
