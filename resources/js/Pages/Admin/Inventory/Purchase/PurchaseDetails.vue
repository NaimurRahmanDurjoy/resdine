<template>
    <div class="space-y-6">
        <!-- Master Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-gray-100 dark:border-gray-600">
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1">Supplier
                </p>
                <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ purchase.supplier?.name ||
                    purchase.supplier?.company_name || 'N/A' }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-gray-100 dark:border-gray-600">
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1">Purchase
                    Date</p>
                <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ new
                    Date(purchase.purchase_date).toLocaleDateString() }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-gray-100 dark:border-gray-600">
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1">Invoice
                    Number</p>
                <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ purchase.invoice_number || '--' }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-gray-100 dark:border-gray-600">
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1">Status
                </p>
                <span
                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                    {{ purchase.status_label }}
                </span>
            </div>
        </div>

        <div v-if="purchase.notes"
            class="p-4 bg-indigo-50/50 dark:bg-indigo-900/10 rounded-xl border border-indigo-100 dark:border-indigo-900/30">
            <p class="text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-2">Notes</p>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ purchase.notes }}</p>
        </div>

        <!-- Items Table -->
        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Item</th>
                        <th
                            class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Quantity</th>
                        <th
                            class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Unit Price</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider pl-8">
                            Expiry</th>
                        <th
                            class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Line Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                    <tr v-for="detail in purchase.details" :key="detail.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ detail.inventory_item?.name || 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700 dark:text-gray-300">
                            {{ detail.quantity }}
                            <span class="text-gray-400 text-xs ml-1">{{ detail.inventory_item?.unit?.short_name ||
                                detail.inventory_item?.unit?.name || '' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700 dark:text-gray-300">
                            ${{ parseFloat(detail.unit_price).toFixed(2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 pl-8">
                            {{ detail.expiry_date ? new Date(detail.expiry_date).toLocaleDateString() : '--' }}
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-gray-900 dark:text-gray-100 bg-gray-50/30 dark:bg-gray-800/30">
                            ${{ parseFloat(detail.total_price).toFixed(2) }}
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-indigo-50/30 dark:bg-indigo-900/10">
                    <tr>
                        <td colspan="4"
                            class="px-6 py-4 text-right text-sm font-bold text-gray-700 dark:text-gray-400 uppercase tracking-wider">
                            Grand Total
                        </td>
                        <td
                            class="px-6 py-4 text-right text-lg font-black text-indigo-700 dark:text-indigo-400 border-t-2 border-indigo-200 dark:border-indigo-900/50">
                            ${{ parseFloat(purchase.total_amount).toFixed(2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    purchase: {
        type: Object,
        required: true
    }
})
</script>
