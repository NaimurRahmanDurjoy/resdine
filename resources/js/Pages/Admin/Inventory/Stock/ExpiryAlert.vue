<template>
    <AdminLayout :pageTitle="pageTitle">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Page Header -->
            <div class="bg-white dark:bg-gray-800">
                <div
                    class="bg-gradient-to-r from-orange-50 to-white dark:from-orange-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-orange-100 text-orange-600 rounded-lg">
                                <span class="material-symbols-outlined">event_busy</span>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Expiry Alerts</h1>
                                <p class="text-sm text-orange-600 dark:text-orange-400 font-medium">Items expiring within 30 days</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Using a simple table to list upcoming expiries -->
                <ListTable :headers="['Ingredient', 'Quantity', 'Purchase Reference', 'Expiry Date', 'Days Left']" :items="expiringItems">
                    <template #rows="{ items }">
                        <tr v-for="item in items" :key="item.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-l-4 border-transparent"
                                :class="{'!border-red-500 bg-red-50/30': getDaysLeft(item.expiry_date) <= 7}">
                                {{ item.ingredient_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">
                                {{ parseFloat(item.quantity).toFixed(2) }} 
                                <span class="text-xs text-gray-400 font-normal">{{ item.unit_name }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600">
                                <Link :href="route('admin.purchase.show', item.purchase_master_id)" class="hover:underline">
                                    PO-{{ item.purchase_master_id }}
                                </Link>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                {{ new Date(item.expiry_date).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full"
                                    :class="getWarningClass(getDaysLeft(item.expiry_date))">
                                    {{ getDaysLeft(item.expiry_date) }} days back
                                </span>
                            </td>
                        </tr>
                        <tr v-if="items.length === 0">
                            <td colspan="5"
                                class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                No items are expiring soon. Good job on inventory rotation!
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
    expiringItems: Array,
    pageTitle: String
})

const getDaysLeft = (dateString) => {
    const today = new Date();
    today.setHours(0,0,0,0);
    const expiryDate = new Date(dateString);
    const diffTime = expiryDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
    return diffDays;
}

const getWarningClass = (days) => {
    if (days <= 0) return 'bg-red-200 text-red-900 border border-red-300';
    if (days <= 7) return 'bg-red-100 text-red-800';
    if (days <= 14) return 'bg-orange-100 text-orange-800';
    return 'bg-yellow-100 text-yellow-800';
}
</script>
