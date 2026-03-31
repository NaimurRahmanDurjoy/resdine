<template>
    <AdminLayout>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Order {{ order.order_number }}</h1>
                    <p class="text-sm text-gray-500">Placed on {{ formatDate(order.created_at) }}</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('admin.orders.invoice', order.id)" target="_blank" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">print</span>
                        Invoice
                    </Link>
                    <button 
                        v-if="order.due_amount > 0"
                        @click="showPaymentModal = true"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition flex items-center gap-2"
                    >
                        <span class="material-symbols-outlined text-sm">payments</span>
                        Process Payment
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Items & Status -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Status Control -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-medium mb-4">Chef & Kitchen Status</h2>
                        <div class="flex flex-wrap gap-2">
                            <button 
                                v-for="(label, status) in statuses" 
                                :key="status"
                                @click="updateStatus(status)"
                                :disabled="order.order_status == status"
                                class="px-4 py-2 rounded-md text-sm font-medium transition disabled:opacity-50"
                                :class="statusClass(status, order.order_status)"
                            >
                                {{ label }}
                            </button>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="bg-white p-6 rounded-lg shadow overflow-hidden">
                        <h2 class="text-lg font-medium mb-4">Ordered Items</h2>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-gray-500 uppercase">
                                    <th class="pb-3">Item</th>
                                    <th class="pb-3">Quantity</th>
                                    <th class="pb-3 text-right">Price</th>
                                    <th class="pb-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="item in order.items" :key="item.id">
                                    <td class="py-4">
                                        <div class="font-medium text-gray-900">{{ item.product?.name }}</div>
                                        <div v-if="item.notes" class="text-xs text-gray-500 italic">{{ item.notes }}</div>
                                    </td>
                                    <td class="py-4 text-sm text-gray-500">{{ item.quantity }}</td>
                                    <td class="py-4 text-sm text-gray-500 text-right">${{ item.price }}</td>
                                    <td class="py-4 text-sm font-semibold text-gray-900 text-right">${{ item.total_price }}</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="py-3 px-4 text-right text-sm font-medium text-gray-500">Subtotal</td>
                                    <td class="py-3 px-4 text-right text-sm font-bold text-gray-900">${{ order.subtotal }}</td>
                                </tr>
                                <tr v-if="order.discount > 0">
                                    <td colspan="3" class="py-3 px-4 text-right text-sm font-medium text-gray-500">Discount</td>
                                    <td class="py-3 px-4 text-right text-sm font-bold text-red-600">-${{ order.discount }}</td>
                                </tr>
                                <tr class="border-t-2 border-gray-200">
                                    <td colspan="3" class="py-3 px-4 text-right text-base font-bold text-gray-900">Grand Total</td>
                                    <td class="py-3 px-4 text-right text-base font-bold text-indigo-600">${{ order.total_amount }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Payment History -->
                    <div class="bg-white p-6 rounded-lg shadow overflow-hidden">
                        <h2 class="text-lg font-medium mb-4">Payment History</h2>
                        <div v-if="order.payments.length === 0" class="text-gray-500 text-sm italic">
                            No payments recorded yet.
                        </div>
                        <table v-else class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-gray-500 uppercase">
                                    <th class="pb-3 text-left">Date</th>
                                    <th class="pb-3">Method</th>
                                    <th class="pb-3">Reference</th>
                                    <th class="pb-3 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="payment in order.payments" :key="payment.id">
                                    <td class="py-4 text-sm text-gray-500 text-left">{{ formatDate(payment.paid_at) }}</td>
                                    <td class="py-4 text-sm text-gray-500">
                                        <span v-if="payment.method == 1">Cash</span>
                                        <span v-else-if="payment.method == 2">Card</span>
                                        <span v-else-if="payment.method == 3">bKash</span>
                                        <span v-else-if="payment.method == 4">Nagad</span>
                                        <span v-else>Wallet</span>
                                    </td>
                                    <td class="py-4 text-sm text-gray-500">{{ payment.transaction_reference || '-' }}</td>
                                    <td class="py-4 text-sm font-semibold text-gray-900 text-right">${{ payment.amount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="space-y-6">
                    <!-- Customer Card -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-medium mb-4">Customer Info</h2>
                        <div v-if="order.customer">
                            <div class="font-bold text-gray-900 text-lg">{{ order.customer.name }}</div>
                            <div class="text-sm text-gray-500">{{ order.customer.phone }}</div>
                            <div class="text-sm text-gray-500">{{ order.customer.email }}</div>
                        </div>
                        <div v-else class="text-gray-500 italic">
                            Walk-in Customer
                        </div>
                    </div>

                    <!-- Order Summary Card -->
                    <div class="bg-indigo-700 p-6 rounded-lg shadow text-white">
                        <h2 class="text-lg font-medium mb-4 opacity-80">Financial Summary</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="opacity-70">Total Amount</span>
                                <span class="font-bold text-xl">${{ order.total_amount }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="opacity-70">Paid So Far</span>
                                <span class="font-bold text-xl text-green-300">${{ order.collect_amount }}</span>
                            </div>
                            <div class="flex justify-between pt-4 border-t border-indigo-500">
                                <span class="opacity-90 font-bold uppercase tracking-wider">Due Amount</span>
                                <span class="font-black text-2xl" :class="order.due_amount > 0 ? 'text-orange-300' : 'text-green-300'">
                                    ${{ order.due_amount }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Card -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-medium mb-4">General Notes</h2>
                        <p class="text-sm text-gray-600 italic">
                            {{ order.notes || 'No general notes provided.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <PaymentModal 
            :show="showPaymentModal" 
            :orderId="order.id" 
            :remainingDue="parseFloat(order.due_amount)"
            @close="showPaymentModal = false"
        />
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaymentModal from '@/Components/Admin/Orders/PaymentModal.vue';

const props = defineProps({
    order: Object,
    pageTitle: String
});

const showPaymentModal = ref(false);

const statuses = {
    0: 'Pending',
    1: 'Preparing',
    2: 'Served',
    4: 'Completed',
    5: 'Cancelled'
};

const statusClass = (status, current) => {
    if (status == current) {
        return 'bg-indigo-600 text-white shadow-md';
    }
    return 'bg-gray-100 text-gray-700 hover:bg-gray-200';
};

const updateStatus = (status) => {
    router.post(route('admin.orders.status.update', props.order.id), {
        status: status
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleString();
};
</script>
