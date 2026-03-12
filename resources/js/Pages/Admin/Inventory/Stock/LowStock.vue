<template>
    <AdminLayout :pageTitle="pageTitle">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Page Header -->
            <div class="bg-white dark:bg-gray-800">
                <div
                    class="bg-gradient-to-r from-red-50 to-white dark:from-red-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-red-100 text-red-600 rounded-lg">
                                <span class="material-symbols-outlined">warning</span>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Low Stock Alerts</h1>
                                <p class="text-sm text-red-600 dark:text-red-400 font-medium">Items that have fallen below their minimum threshold</p>
                            </div>
                        </div>
                        <Link :href="route('admin.purchase.create')"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center gap-2 transition-all shadow-md shadow-indigo-100 dark:shadow-none">
                            <span class="material-symbols-outlined text-sm">add_shopping_cart</span>
                            Buy Replacements
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                
                <h3 v-if="missingStocks.length > 0" class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Never Stocked (Critical)</h3>
                <div v-if="missingStocks.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div v-for="item in missingStocks" :key="'m_'+item.ingredient.id" class="bg-red-50 border border-red-100 rounded-lg p-4 flex justify-between items-center shadow-sm">
                        <div>
                            <p class="font-bold text-gray-900">{{ item.ingredient.name }}</p>
                            <p class="text-xs text-red-600 mt-1">Min Required: {{ item.ingredient.min_stock }} {{ item.ingredient.unit?.short_name }}</p>
                        </div>
                        <div class="text-right">
                            <span class="block text-2xl font-black text-red-600">0</span>
                        </div>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Running Low</h3>
                <ListTable :headers="['Ingredient', 'Unit', 'Current Stock', 'Min Threshold', 'Deficit']" :items="stocks">
                    <template #rows="{ items }">
                        <tr v-for="stock in items" :key="stock.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ stock.ingredient?.name || 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ stock.ingredient?.unit?.short_name || stock.ingredient?.unit?.name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">
                                {{ parseFloat(stock.current_stock).toFixed(2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                                {{ parseFloat(stock.ingredient?.min_stock || 0).toFixed(2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500 font-semibold bg-red-50/50">
                                -{{ (parseFloat(stock.ingredient?.min_stock || 0) - parseFloat(stock.current_stock)).toFixed(2) }}
                            </td>
                        </tr>
                        <tr v-if="items.length === 0">
                            <td colspan="5"
                                class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                No items are running low. Excellent!
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
    stocks: Array,
    missingStocks: Array,
    pageTitle: String
})
</script>
