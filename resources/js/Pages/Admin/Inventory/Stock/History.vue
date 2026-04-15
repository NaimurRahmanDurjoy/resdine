<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    ingredient: Object,
    ledger: Object,
    pageTitle: String
})

const headers = [
    { label: 'Date', key: 'transaction_date' },
    { label: 'Type', key: 'transaction_type' },
    { label: 'Reference', key: 'reference_id' },
    { label: 'Qty In', key: 'qty_in' },
    { label: 'Qty Out', key: 'qty_out' },
    { label: 'Unit Cost', key: 'unit_cost' },
    { label: 'Batch No', key: 'batch_no' }
]

const getStatusClass = (type) => {
    const t = String(type || '').toLowerCase()
    if (!t) return 'bg-gray-100 text-gray-700 border-gray-200'
    switch (t) {
        case 'purchase': return 'bg-green-100 text-green-700 border-green-200'
        case 'adjustment': return 'bg-amber-100 text-amber-700 border-amber-200'
        case 'consumption': return 'bg-red-100 text-red-700 border-red-200'
        case 'return': return 'bg-gray-100 text-gray-700 border-gray-200'
        default: return 'bg-blue-100 text-blue-700 border-blue-200'
    }
}
</script>

<template>
    <div class="space-y-6">
        <!-- Back Link & Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.stock.index')" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-gray-600">arrow_back</span>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ pageTitle }}</h1>
                    <p class="text-sm text-gray-500">Transaction history and audit trail</p>
                </div>
            </div>
            
            <div class="flex gap-3">
                <Link :href="route('admin.stock.adjust')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 flex items-center gap-2 transition-all shadow-sm">
                    <span class="material-symbols-outlined text-sm">tune</span>
                    Adjust Stock
                </Link>
                <Link :href="route('admin.purchase.create')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center gap-2 transition-all shadow-md shadow-indigo-100 dark:shadow-none">
                    <span class="material-symbols-outlined text-sm">add_shopping_cart</span>
                    Buy More
                </Link>
            </div>
        </div>

        <!-- Ingredient Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="text-sm font-medium text-gray-500 mb-1">Current Stock</div>
                <div class="text-3xl font-bold text-indigo-600">
                    {{ ingredient.stock_summary ? parseFloat(ingredient.stock_summary.current_stock).toFixed(2) : '0.00' }}
                    <span class="text-sm font-normal text-gray-400">{{ ingredient.unit?.short_name }}</span>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="text-sm font-medium text-gray-500 mb-1">Minimum Threshold</div>
                <div class="text-3xl font-bold text-gray-900">
                    {{ parseFloat(ingredient.min_stock).toFixed(2) }}
                    <span class="text-sm font-normal text-gray-400">{{ ingredient.unit?.short_name }}</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="text-sm font-medium text-gray-500 mb-1">Status</div>
                <div class="flex items-center gap-2 mt-2">
                    <span v-if="ingredient.stock_summary && parseFloat(ingredient.stock_summary.current_stock) <= parseFloat(ingredient.min_stock)" 
                        class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                        Low Stock Alert
                    </span>
                    <span v-else-if="!ingredient.stock_summary && parseFloat(ingredient.min_stock) > 0"
                        class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                        Never Stocked
                    </span>
                    <span v-else class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                        Healthy Level
                    </span>
                </div>
            </div>
        </div>

        <!-- Audit Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800">Transaction Ledger</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th v-for="h in headers" :key="h.label" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                {{ h.label }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="entry in ledger.data" :key="entry.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ entry.transaction_date ? new Date(entry.transaction_date).toLocaleDateString() : new Date(entry.created_at).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border" :class="getStatusClass(entry.transaction_type)">
                                    {{ entry.transaction_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                <template v-if="entry.transaction_type === 'purchase' && entry.reference_id">
                                    <Link :href="route('admin.purchase.show', entry.reference_id)" class="hover:underline">PO-{{ entry.reference_id }}</Link>
                                </template>
                                <template v-else>
                                    #{{ entry.reference_id || 'N/A' }}
                                </template>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                {{ parseFloat(entry.qty_in || 0) > 0 ? '+' + parseFloat(entry.qty_in).toFixed(2) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">
                                {{ parseFloat(entry.qty_out || 0) > 0 ? '-' + parseFloat(entry.qty_out).toFixed(2) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ parseFloat(entry.unit_cost || 0) > 0 ? '$' + parseFloat(entry.unit_cost).toFixed(2) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                {{ entry.batch_no || 'Default' }}
                            </td>
                        </tr>
                        <tr v-if="ledger.data.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic text-sm">
                                No transaction history found for this ingredient.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Showing <span class="font-medium">{{ ledger.from }}</span> to <span class="font-medium">{{ ledger.to }}</span> of <span class="font-medium">{{ ledger.total }}</span> movements
                </div>
                <div class="flex gap-1">
                    <Link v-for="link in ledger.links" :key="link.label" :href="link.url || '#'" 
                        class="px-3 py-1 text-xs border rounded transition-colors"
                        :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 hover:bg-gray-50'"
                        v-html="link.label" />
                </div>
            </div>
        </div>
    </div>
</template>
