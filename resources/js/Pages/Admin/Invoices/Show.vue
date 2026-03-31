<template>
    <div class="bg-gray-100 min-h-screen p-4 sm:p-8 flex justify-center">
        <div class="max-w-2xl w-full bg-white shadow-2xl rounded-lg overflow-hidden flex flex-col" id="printable-invoice">
            <!-- Header -->
            <div class="bg-indigo-900 p-8 text-white flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-black italic tracking-tighter">RESDINE</h1>
                    <p class="text-xs opacity-70 mt-1 uppercase tracking-widest">Gourmet Dining Experience</p>
                </div>
                <div class="text-right">
                    <h2 class="text-xl font-bold uppercase">Invoice</h2>
                    <p class="text-sm opacity-80">{{ invoice.invoice_number }}</p>
                    <p class="text-xs opacity-60">Date: {{ formatDate(invoice.issued_at) }}</p>
                </div>
            </div>

            <!-- Addresses -->
            <div class="p-8 grid grid-cols-2 gap-8 text-sm border-b border-gray-100">
                <div>
                    <h3 class="text-gray-400 font-bold uppercase text-xs mb-2">Billed From:</h3>
                    <p class="font-bold text-gray-800">ResDine Main Branch</p>
                    <p class="text-gray-600">Road 12, Banani, Dhaka</p>
                    <p class="text-gray-600">Tel: +880 1234 567 890</p>
                </div>
                <div class="text-right">
                    <h3 class="text-gray-400 font-bold uppercase text-xs mb-2">Billed To:</h3>
                    <p class="font-bold text-gray-800">{{ invoice.customer ? invoice.customer.name : 'Walk-in Guest' }}</p>
                    <p v-if="invoice.customer" class="text-gray-600">{{ invoice.customer.phone }}</p>
                    <p v-if="invoice.order?.table" class="text-gray-600">Table: {{ invoice.order.table.name }}</p>
                </div>
            </div>

            <!-- Table -->
            <div class="p-8 flex-grow">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="pb-4">Item Description</th>
                            <th class="pb-4 text-center">Qty</th>
                            <th class="pb-4 text-right">Price</th>
                            <th class="pb-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="item in invoice.order?.items" :key="item.id">
                            <td class="py-4">
                                <div class="font-bold text-gray-800">{{ item.product?.name }}</div>
                                <div v-if="item.notes" class="text-xs text-gray-400 italic">{{ item.notes }}</div>
                            </td>
                            <td class="py-4 text-center text-gray-600">{{ item.quantity }}</td>
                            <td class="py-4 text-right text-gray-600">${{ item.price }}</td>
                            <td class="py-4 text-right font-bold text-gray-800">${{ item.total_price }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer Totals -->
            <div class="p-8 bg-gray-50 border-t border-gray-100">
                <div class="flex justify-end">
                    <div class="w-full max-w-xs space-y-3">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-bold text-gray-800">${{ invoice.sub_total }}</span>
                        </div>
                        <div v-if="invoice.discount > 0" class="flex justify-between text-sm text-red-500">
                            <span>Discount</span>
                            <span class="font-bold">-${{ invoice.discount }}</span>
                        </div>
                        <div class="flex justify-between text-xl font-black pt-3 border-t border-gray-200">
                            <span class="text-indigo-900">Total</span>
                            <span class="text-indigo-900">${{ invoice.grand_total }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-green-600 pt-2">
                            <span>Paid Amount</span>
                            <span class="font-bold">${{ invoice.collect_amount }}</span>
                        </div>
                        <div v-if="invoice.due_amount > 0" class="flex justify-between text-sm text-orange-600">
                            <span>Amount Due</span>
                            <span class="font-bold">${{ invoice.due_amount }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-200 text-center">
                    <p class="text-sm font-bold text-gray-800 uppercase tracking-widest">Thank you for dining with us!</p>
                    <p class="text-xs text-gray-400 mt-1 italic">Please keep this receipt for your records.</p>
                </div>
            </div>
            
            <!-- Hide for Print button -->
            <div class="p-4 bg-indigo-50 flex justify-center print:hidden">
                <button @click="printInvoice" class="px-6 py-2 bg-indigo-600 text-white rounded-full font-bold hover:bg-indigo-700 transition flex items-center gap-2 shadow-lg">
                    <span class="material-symbols-outlined">print</span>
                    Print Receipt
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    invoice: Object,
    pageTitle: String
});

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const printInvoice = () => {
    window.print();
};
</script>

<style>
@media print {
    body {
        margin: 0;
        padding: 0;
        background: white;
    }
    .print\:hidden {
        display: none !important;
    }
    #printable-invoice {
        box-shadow: none !important;
        border: none !important;
        width: 100% !important;
        max-width: 100% !important;
    }
}
</style>
