<template>
    <AdminLayout>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">{{ pageTitle }}</h1>
                <Link
                    :href="route('admin.orders.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                >
                    Create New Order
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                <Link :href="route('admin.orders.show', order.id)">{{ order.order_number }}</Link>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ order.customer ? order.customer.name : 'Walk-in' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ order.table ? order.table.name : 'Takeaway/Delivery' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span v-if="order.order_type === 1">Dine-in</span>
                                <span v-else-if="order.order_type === 2">Takeaway</span>
                                <span v-else-if="order.order_type === 3">Delivery</span>
                                <span v-else>Party</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                ${{ order.total_amount }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="statusBadge(order.order_status)">
                                    {{ statusText(order.order_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link :href="route('admin.orders.show', order.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">View</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination component would go here if available -->
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    orders: Object,
    pageTitle: String
});

const statusText = (status) => {
    const statuses = {
        0: 'Pending',
        1: 'Preparing',
        2: 'Ready',
        3: 'Out for Delivery',
        4: 'Completed',
        5: 'Cancelled'
    };
    return statuses[status] || 'Unknown';
}

const statusBadge = (status) => {
    const base = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full ';
    if (status === 0) return base + 'bg-yellow-100 text-yellow-800';
    if (status === 1) return base + 'bg-orange-100 text-orange-800';
    if (status === 2) return base + 'bg-blue-100 text-blue-800';
    if (status === 3) return base + 'bg-purple-100 text-purple-800';
    if (status === 4) return base + 'bg-green-100 text-green-800';
    if (status === 5) return base + 'bg-red-100 text-red-800';
    return base + 'bg-gray-100 text-gray-800';
}
</script>
