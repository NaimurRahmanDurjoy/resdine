<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    stocks: Object,
    filters: Object,
    pageTitle: String
})
const headers = [
    { label: 'Type', key: 'inventoryItem.item_type', sortable: true },
    { label: 'Item', key: 'name', sortable: true },
    { label: 'Unit', key: 'unit.short_name' },
    { label: 'Current Stock', key: 'stock_summary.current_stock', sortable: true },
    { label: 'Expiry Status', key: 'has_expiry' },
    { label: 'Last Updated', key: 'stock_summary.updated_at' },
    { label: 'Action', key: 'id' }
]
const search = ref(props.filters.search)
const activeFilter = ref(props.filters.filter || '')
const loading = ref(false)

function debounce(fn, delay) {
    let timeoutId;
    return (...args) => {
        if (timeoutId) clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
}

const performSearch = (newFilter = activeFilter.value) => {
    activeFilter.value = newFilter
    loading.value = true
    router.get(route('admin.stock.index'), {
        search: search.value,
        filter: activeFilter.value,
        sort: props.filters.sort,
        direction: props.filters.direction
    }, {
        preserveState: true,
        replace: true,
        onFinish: () => loading.value = false
    })
}

const getDaysLeft = (dateString) => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const expiryDate = new Date(dateString);
    const diffTime = expiryDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

watch(search, debounce(() => performSearch(), 500))

function sortColumn(column) {
    let direction = 'asc'
    if (props.filters.sort === column) {
        direction = props.filters.direction === 'asc' ? 'desc' : 'asc'
    }
    loading.value = true
    router.get(route('admin.stock.index'), {
        search: search.value,
        filter: activeFilter.value,
        sort: column,
        direction: direction
    }, {
        preserveState: true,
        replace: true,
        onFinish: () => loading.value = false
    })
}
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Page Header -->
        <div class="bg-white dark:bg-gray-800">
            <div
                class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">View your real-time inventory levels</p>
                    </div>
                </div>
            </div>

            <!-- Filters & Actions -->
            <div class="m-4 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <!-- Quick Filters -->
                    <div class="flex p-1 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <button @click="performSearch('')"
                            class="px-4 py-1.5 text-sm font-medium rounded-md transition-all"
                            :class="!activeFilter ? 'bg-white dark:bg-gray-600 text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                            All Stock
                        </button>
                        <button @click="performSearch('low_stock')"
                            class="px-4 py-1.5 text-sm font-medium rounded-md transition-all flex items-center gap-2"
                            :class="activeFilter === 'low_stock' ? 'bg-white dark:bg-gray-600 text-amber-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                            <span class="material-symbols-outlined text-sm">warning</span>
                            Low Stock
                        </button>
                        <button @click="performSearch('expiring')"
                            class="px-4 py-1.5 text-sm font-medium rounded-md transition-all flex items-center gap-2"
                            :class="activeFilter === 'expiring' ? 'bg-white dark:bg-gray-600 text-red-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                            <span class="material-symbols-outlined text-sm">event_busy</span>
                            Expiring Soon
                        </button>
                    </div>

                    <!-- Search Bar -->
                    <div class="flex space-x-2">
                        <div class="relative max-w-sm w-full">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-gray-400">search</span>
                            </span>
                            <input v-model="search" type="text" placeholder="Search items..."
                                class="block w-full pl-10 pr-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <Link :href="route('admin.stock.adjust')"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 flex items-center gap-2 transition-all shadow-sm">
                            <span class="material-symbols-outlined text-sm">tune</span>
                            Adjust Stock
                        </Link>

                        <!-- Buy more -->
                        <Link :href="route('admin.purchase.create')"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center gap-2 transition-all shadow-md shadow-indigo-100 dark:shadow-none">
                            <span class="material-symbols-outlined text-sm">add_shopping_cart</span>
                            Buy More
                        </Link>
                    </div>
                </div>
            </div>
        </div>


        <!-- Content -->

        <ListTable :headers="headers" :items="stocks.data" :pagination="stocks" :sort="filters.sort"
            :direction="filters.direction" :loading="loading" @sort="sortColumn">
            <template #rows="{ items }">
                <tr v-for="stock in items" :key="stock.id"
                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ stock.inventory_item?.item_type == 1 ? 'ING' : 'PRD' || 'N/A' }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900 border-l-4 border-transparent"
                        :class="{
                            '!border-amber-500 bg-amber-50/30': stock.inventory_item && parseFloat(stock.current_stock) <= parseFloat(stock.inventory_item.min_stock),
                            '!border-red-600 bg-red-50': !stock.inventory_item && parseFloat(stock.min_stock) > 0
                        }">
                        {{ stock.inventory_item?.name || 'Unknown' }}
                        <span
                            v-if="stock.inventory_item && parseFloat(stock.current_stock) <= parseFloat(stock.inventory_item.min_stock)"
                            class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-800">
                            Low Stock
                        </span>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
                        {{ stock.inventory_item?.unit?.short_name || stock.inventory_item?.unit?.name || 'N/A' }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm font-bold"
                        :class="parseFloat(stock.current_stock) <= (stock.inventory_item?.min_stock || 0) ? 'text-amber-600' : 'text-indigo-600'">
                        {{ parseFloat(stock.current_stock).toFixed(2) }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm">
                        <div v-if="stock.inventory_item?.purchase_details && stock.inventory_item.purchase_details.length > 0"
                            class="flex flex-col gap-1">
                            <div v-for="batch in stock.inventory_item.purchase_details" :key="batch.id"
                                class="text-[10px] px-1.5 py-0.5 rounded border border-red-200 bg-red-50 text-red-700 flex items-center justify-between">
                                <span>Expires: {{ new Date(batch.expiry_date).toLocaleDateString() }}</span>
                                <span class="font-bold ml-1">({{ getDaysLeft(batch.expiry_date) }}d left)</span>
                            </div>
                        </div>
                        <span v-else class="text-xs text-gray-400 italic">No batches expiring</span>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 font-medium font-mono">
                        {{ stock.updated_at ? new Date(stock.updated_at).toLocaleDateString() : 'N/A' }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm">
                        <Link :href="route('admin.stock.show', { type: stock.inventory_item?.item_type, id: stock.inventory_item?.reference_id })"
                            class="p-1 text-indigo-600 hover:bg-indigo-50 rounded transition-colors group"
                            title="View History">
                            <span
                                class="material-symbols-outlined text-sm group-hover:scale-110 transition-transform">manage_search</span>
                        </Link>
                    </td>
                </tr>
            </template>

            <template #pagination>
                <div class="flex items-center justify-between w-full">
                    <div class="text-sm text-gray-700 dark:text-gray-400">
                        Showing <span class="font-medium">{{ stocks.from }}</span> to <span class="font-medium">
                            {{ stocks.to }}</span> of <span class="font-medium">{{ stocks.total }}</span> entries
                    </div>
                    <div class="flex space-x-1">
                        <Link v-for="(link, k) in stocks.links" :key="k" :href="link.url || '#'" v-html="link.label"
                            class="relative inline-flex items-center px-3 py-1 border text-xs font-medium transition-colors rounded shadow-sm"
                            :class="[
                                link.active
                                    ? 'z-10 bg-indigo-600 border-indigo-600 text-white'
                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700',
                                !link.url ? 'cursor-not-allowed opacity-50' : ''
                            ]" />
                    </div>
                </div>
            </template>

        </ListTable>
    </div>
</template>
