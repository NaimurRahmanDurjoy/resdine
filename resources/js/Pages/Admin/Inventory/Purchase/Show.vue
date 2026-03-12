<template>
    <AdminLayout :pageTitle="pageTitle">
        <div class="max-w-5xl mx-auto space-y-6">
            
            <!-- Header Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Purchase Order Details</h1>
                            <p class="text-gray-600 dark:text-gray-400">Reference: #{{ purchase.id }}</p>
                        </div>
                        <Link :href="route('admin.purchase.index')"
                            class="flex items-center text-sm text-indigo-600 hover:text-indigo-900 font-medium transition-colors">
                            <span class="material-symbols-outlined mr-1 text-base">arrow_back</span>
                            Back to List
                        </Link>
                    </div>
                </div>

                <div class="p-8">
                    <!-- Master Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Supplier</p>
                            <p class="text-sm font-medium text-gray-900">{{ purchase.supplier?.name || purchase.supplier?.company_name || 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Purchase Date</p>
                            <p class="text-sm font-medium text-gray-900">{{ new Date(purchase.purchase_date).toLocaleDateString() }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Invoice Number</p>
                            <p class="text-sm font-medium text-gray-900">{{ purchase.invoice_no || '--' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ purchase.status.charAt(0).toUpperCase() + purchase.status.slice(1) }}
                            </span>
                        </div>
                    </div>

                    <div v-if="purchase.notes" class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Notes</p>
                        <p class="text-sm text-gray-700">{{ purchase.notes }}</p>
                    </div>

                    <!-- Items Table -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ml-4">Expiry</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                                <tr v-for="detail in purchase.details" :key="detail.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-l-4 border-transparent hover:border-indigo-500">
                                        {{ detail.ingredient?.name || 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700">
                                        {{ detail.quantity }}
                                        <span class="text-gray-400 text-xs ml-1">{{ detail.ingredient?.unit?.short_name || detail.ingredient?.unit?.name || '' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700">
                                        ${{ parseFloat(detail.unit_price).toFixed(2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 pl-8">
                                        {{ detail.expiry_date ? new Date(detail.expiry_date).toLocaleDateString() : '--' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right text-gray-900 bg-gray-50/50">
                                        ${{ parseFloat(detail.total_price).toFixed(2) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-indigo-50/50">
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">
                                        Grand Total
                                    </td>
                                    <td class="px-6 py-4 text-right text-lg font-bold text-indigo-700 border-t-2 border-indigo-200">
                                        ${{ parseFloat(purchase.total_amount).toFixed(2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    purchase: Object,
    pageTitle: String
})
</script>
