<template>
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="bg-gradient-to-r from-gray-900 to-indigo-900 rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-500/20 p-4 rounded-2xl backdrop-blur-md border border-white/10">
                        <span class="material-symbols-outlined text-4xl text-indigo-300">receipt_long</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-3xl font-black tracking-tight">Order {{ order.order_number }}</h1>
                            <span :class="statusBadge(order.order_status)"
                                class="text-xs px-3 py-1 uppercase tracking-widest font-black rounded-full">
                                {{ statusText(order.order_status) }}
                            </span>
                        </div>
                        <p class="text-indigo-200/70 mt-1 flex items-center gap-2 text-sm">
                            <span class="material-symbols-outlined text-xs">calendar_today</span>
                            Placed on {{ formatDate(order.created_at) }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <Link :href="route('admin.orders.invoice', order.id)" target="_blank"
                        class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl transition-all backdrop-blur-md border border-white/10 text-sm font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">print</span>
                        Invoice
                    </Link>
                    <button v-if="order.due_amount > 0" @click="showPaymentModal = true"
                        class="px-5 py-2.5 bg-indigo-500 hover:bg-indigo-400 text-white rounded-xl transition-all shadow-lg shadow-indigo-500/20 text-sm font-black flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">payments</span>
                        Process Payment
                    </button>
                    <Link :href="route('admin.orders.index')"
                        class="px-5 py-2.5 bg-white/5 hover:bg-white/10 text-white/70 rounded-xl transition-all text-sm font-bold border border-white/5">
                        Back to List
                    </Link>
                </div>
            </div>
            <!-- Decorative elements -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Status Control -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">cycle</span>
                        Workflow Management
                    </h2>
                    <div class="flex flex-wrap gap-3">
                        <button v-for="(label, status) in statuses" :key="status" @click="updateStatus(status)"
                            :disabled="order.order_status == status"
                            class="px-6 py-3 rounded-2xl text-sm font-black transition-all duration-300 flex items-center gap-2 shadow-sm border"
                            :class="statusClass(status, order.order_status)">
                            <span class="material-symbols-outlined text-lg">{{ statusIcon(status) }}</span>
                            {{ label }}
                        </button>
                    </div>
                </div>

                <!-- Items Table -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div
                        class="px-8 py-4 border-b border-gray-50 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/30">
                        <h2 class="text-sm font-black uppercase tracking-widest text-gray-400 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-indigo-500">flatware</span>
                            Manifest
                        </h2>
                        <span
                            class="text-xs font-bold text-gray-500 px-3 py-1 bg-white dark:bg-gray-800 rounded-lg shadow-sm">{{
                                order.items?.length || 0 }} Items</span>
                    </div>

                    <table class="w-full">
                        <thead>
                            <tr
                                class="text-left text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 dark:border-gray-700">
                                <th class="px-8 py-4">Item Description</th>
                                <th class="px-8 py-4 text-center">Qty</th>
                                <th class="px-8 py-4 text-right">Unit Price</th>
                                <th class="px-8 py-4 text-right font-black text-indigo-500">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            <tr v-for="item in order.items" :key="item.id"
                                class="hover:bg-gray-50/50 dark:hover:bg-gray-900/20 transition-colors">
                                <td class="px-8 py-2">
                                    <div class="font-black text-gray-800 dark:text-gray-100 text-lg">{{
                                        item.product?.name }}</div>
                                    <div v-if="item.notes"
                                        class="text-xs text-indigo-400 mt-1 flex items-center gap-1 italic">
                                        <span class="material-symbols-outlined text-xs">info</span>
                                        {{ item.notes }}
                                    </div>
                                </td>
                                <td class="px-8 py-2 text-center">
                                    <span
                                        class="bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-xl font-black text-gray-700 dark:text-gray-300">
                                        {{ item.quantity }}
                                    </span>
                                </td>
                                <td class="px-8 py-2 text-right text-gray-500 font-medium">${{ item.price }}</td>
                                <td class="px-8 py-2 text-right font-black text-gray-900 dark:text-white text-lg">${{
                                    item.total_price }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="bg-gray-50/50 dark:bg-gray-900/50 px-8 py-6">
                        <div class="max-w-xs ml-auto space-y-4">
                            <div class="flex justify-between items-center text-gray-500">
                                <span class="text-sm font-bold uppercase tracking-tighter">Subtotal</span>
                                <span class="font-black">${{ order.subtotal }}</span>
                            </div>
                            <div v-if="order.discount > 0" class="flex justify-between items-center text-red-500">
                                <span class="text-sm font-bold uppercase tracking-tighter">Discount</span>
                                <span class="font-black">-${{ order.discount }}</span>
                            </div>
                            <div
                                class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-end">
                                <span class="text-xs font-black text-indigo-500 uppercase tracking-widest">Grand
                                    Total</span>
                                <span class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter">${{
                                    order.total_amount }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div
                        class="px-8 py-6 border-b border-gray-50 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
                        <h2 class="text-sm font-black uppercase tracking-widest text-gray-400 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-green-500">history_edu</span>
                            Payment Ledger
                        </h2>
                    </div>

                    <div v-if="order.payments.length === 0" class="p-12 text-center text-gray-400 opacity-50">
                        <span class="material-symbols-outlined text-5xl mb-2">money_off</span>
                        <p class="font-medium">No transactions recorded for this order.</p>
                    </div>

                    <table v-else class="w-full">
                        <thead>
                            <tr
                                class="text-left text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 dark:border-gray-700">
                                <th class="px-8 py-5">Processing Date</th>
                                <th class="px-8 py-5">Method</th>
                                <th class="px-8 py-5">Reference</th>
                                <th class="px-8 py-5 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            <tr v-for="payment in order.payments" :key="payment.id"
                                class="hover:bg-gray-50/50 dark:hover:bg-gray-900/20 transition-colors">
                                <td class="px-8 py-5 text-sm font-bold text-gray-600 dark:text-gray-400">{{
                                    formatDate(payment.paid_at) }}</td>
                                <td class="px-8 py-5">
                                    <span
                                        class="flex items-center gap-2 text-sm font-black text-gray-800 dark:text-gray-200">
                                        <span class="material-symbols-outlined text-sm text-indigo-500">{{
                                            paymentMethodIcon(payment.method) }}</span>
                                        {{ paymentMethodName(payment.method) }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-xs text-gray-400 uppercase font-mono tracking-tighter">{{
                                    payment.payment_reference || 'N/A' }}</td>
                                <td class="px-8 py-5 text-right font-black text-green-600 dark:text-green-400">${{
                                    payment.amount }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Info Sidebar -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Customer Profile Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 relative overflow-hidden group">
                    <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-8 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">person_pin</span>
                        Guest Profile
                    </h2>

                    <div v-if="order.customer" class="relative z-10">
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-indigo-500/20">
                                {{ order.customer.name.charAt(0) }}
                            </div>
                            <div>
                                <div class="font-black text-gray-900 dark:text-white text-xl">{{ order.customer.name }}
                                </div>
                                <div class="text-xs font-bold text-indigo-500 uppercase tracking-widest">LOYAL CUSTOMER
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div
                                class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                                <span class="material-symbols-outlined text-lg text-gray-400">call</span>
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ order.customer.phone
                                }}</span>
                            </div>
                            <div v-if="order.customer.email"
                                class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                                <span class="material-symbols-outlined text-lg text-gray-400">mail</span>
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300 truncate">{{
                                    order.customer.email }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-6 opacity-40 grayscale group-hover:grayscale-0 transition-all">
                        <span class="material-symbols-outlined text-6xl mb-4">person_off</span>
                        <p class="font-black text-sm uppercase tracking-tighter">Quick Walk-in Session</p>
                    </div>

                    <!-- Background decors -->
                    <div
                        class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-50 dark:bg-indigo-900/20 rounded-full blur-2xl">
                    </div>
                </div>

                <!-- Financial Pulse Card -->
                <div
                    class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-3xl shadow-xl shadow-indigo-500/20 p-8 text-white relative overflow-hidden">
                    <h2
                        class="text-xs font-black uppercase tracking-widest text-indigo-300 mb-8 border-b border-white/10 pb-4">
                        Financial Pulse</h2>

                    <div class="space-y-6 relative z-10">
                        <div class="flex justify-between items-center group">
                            <span class="text-sm font-bold text-indigo-200 uppercase tracking-tighter">Gross
                                Revenue</span>
                            <span class="font-black text-2xl tracking-tighter">${{ order.total_amount }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-indigo-200 uppercase tracking-tighter">Clearing
                                Fund</span>
                            <div class="flex flex-col items-end">
                                <span class="font-black text-2xl text-green-300 tracking-tighter">${{
                                    order.collect_amount }}</span>
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-green-300 opacity-60">Success</span>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-indigo-500/50 flex justify-between items-end">
                            <div class="flex flex-col">
                                <span
                                    class="text-xs font-black text-white uppercase tracking-widest mb-1">Exposure</span>
                                <span class="font-black text-3xl tracking-tighter"
                                    :class="order.due_amount > 0 ? 'text-orange-400' : 'text-green-300'">
                                    ${{ order.due_amount }}
                                </span>
                            </div>
                            <div v-if="order.due_amount > 0" class="flex flex-col items-end">
                                <div
                                    class="bg-orange-500/20 text-orange-400 text-[10px] font-black px-2 py-1 rounded border border-orange-500/20 uppercase animate-pulse">
                                    Action Required</div>
                            </div>
                            <div v-else>
                                <span class="material-symbols-outlined text-green-300 text-3xl">verified</span>
                            </div>
                        </div>
                    </div>

                    <!-- Abstract background shape -->
                    <div class="absolute right-0 bottom-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mb-16 blur-2xl">
                    </div>
                </div>

                <!-- Session Intelligence -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <h2 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">psychology</span>
                        Session Context
                    </h2>

                    <div class="space-y-4">
                        <div
                            class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Location
                            </div>
                            <div class="font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm text-indigo-500">table_bar</span>
                                {{ order.table ? order.table.name : 'Off-site / Takeaway' }}
                            </div>
                        </div>

                        <div
                            class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Guest
                                Directives</div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium italic">
                                "{{ order.notes || 'No special directives provided by agent.' }}"
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Gateway Modal Mock/Integration -->
        <PaymentModal :show="showPaymentModal" :orderId="order.id" :remainingDue="parseFloat(order.due_amount)"
            @close="showPaymentModal = false" />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PaymentModal from '@/Components/Admin/Orders/PaymentModal.vue';

defineOptions({ layout: AdminLayout });

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

const statusIcon = (status) => {
    const icons = {
        0: 'schedule',
        1: 'skillet',
        2: 'person_check',
        4: 'task_alt',
        5: 'cancel'
    };
    return icons[status] || 'help';
}

const statusClass = (status, current) => {
    if (status == current) {
        if (status == 4) return 'bg-green-600 text-white border-green-600 shadow-green-500/20';
        if (status == 5) return 'bg-red-600 text-white border-red-600 shadow-red-500/20';
        return 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-500/20';
    }
    return 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900';
};

const updateStatus = (status) => {
    router.post(route('admin.orders.status.update', props.order.id), {
        status: status
    }, {
        preserveScroll: true
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const statusText = (status) => {
    const statuses = {
        0: 'Pending Approval',
        1: 'Active Prep',
        2: 'Ready for Service',
        3: 'In Transit',
        4: 'Fulfilled',
        5: 'Voided'
    };
    return statuses[status] || 'Status Undefined';
}

const statusBadge = (status) => {
    if (status == 4) return 'bg-green-500/20 text-green-400 border border-green-500/20';
    if (status == 5) return 'bg-red-500/20 text-red-500 border border-red-500/20';
    if (status == 0) return 'bg-yellow-500/20 text-yellow-500 border border-yellow-500/20';
    return 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/20';
}

const paymentMethodName = (method) => {
    const methods = { 1: 'Cash Reserve', 2: 'Digital Card', 3: 'bKash Merchant', 4: 'Nagad Pay', 5: 'Smart Wallet' };
    return methods[method] || 'Unknown Channel';
}

const paymentMethodIcon = (method) => {
    const icons = { 1: 'payments', 2: 'credit_card', 3: 'account_balance_wallet', 4: 'account_balance_wallet', 5: 'wallet' };
    return icons[method] || 'help';
}
</script>

