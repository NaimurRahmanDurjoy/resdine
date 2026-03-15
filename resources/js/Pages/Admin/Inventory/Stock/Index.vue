<template>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Page Header -->
            <div class="bg-white dark:bg-gray-800">
                <div
                    class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Current Stock</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">View your real-time inventory levels</p>
                        </div>
                    </div>
                </div>

                <!-- Filters & Actions -->
                <div class="m-4 flex justify-between items-center">
                    <div class="flex space-x-2">
                        <div class="relative max-w-sm w-full">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-gray-400">search</span>
                            </span>
                            <input v-model="search" type="text" placeholder="Search items..."
                                class="block w-full pl-10 pr-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>
                        <button @click="performSearch"
                            class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 transition">
                            Search
                        </button>
                    </div>
                    <div class="flex gap-2">
                        <Link :href="route('admin.stock.adjust')"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 flex items-center gap-2 transition-all shadow-sm">
                            <span class="material-symbols-outlined text-sm">tune</span>
                            Adjust Stock
                        </Link>

                        <!-- Add new item -->
                        <Link :href="route('admin.purchase.create')"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center gap-2 transition-all shadow-md shadow-indigo-100 dark:shadow-none">
                            <span class="material-symbols-outlined text-sm">add_shopping_cart</span>
                            Buy More
                        </Link>
                    </div>
                </div>
            </div>


            <!-- Content -->

            <ListTable :headers="headers" :items="stocks.data" :pagination="stocks" :sort="filters.sort"
                :direction="filters.direction" :loading="loading" @sort="sortColumn">
                <template #rows="{ items }">
                    <tr v-for="stock in items" :key="stock.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-l-4 border-transparent"
                            :class="{ '!border-red-500 bg-red-50/30': parseFloat(stock.current_stock) <= parseFloat(stock.ingredient?.min_stock) }">
                            {{ stock.ingredient?.name || 'Unknown' }}
                            <span v-if="parseFloat(stock.current_stock) <= parseFloat(stock.ingredient?.min_stock)"
                                class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-800">
                                Low Stock
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ stock.ingredient?.unit?.short_name || stock.ingredient?.unit?.name || 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold"
                            :class="parseFloat(stock.current_stock) <= parseFloat(stock.ingredient?.min_stock) ? 'text-red-600' : 'text-indigo-600'">
                            {{ parseFloat(stock.current_stock).toFixed(2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium">
                            {{ parseFloat(stock.total_in).toFixed(2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-600 font-medium">
                            {{ parseFloat(stock.total_out).toFixed(2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ new Date(stock.last_updated).toLocaleString() }}
                        </td>
                    </tr>
                </template>

            </ListTable>
        </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'

defineOptions({layout: AdminLayout})

const props = defineProps({
    stocks: Object,
    filters: Object,
    pageTitle: String
})
const headers = [
    { label: 'Ingredient', key: 'ingredient.name', sortable: true },
    { label: 'Unit', key: 'ingredient.unit.short_name' },
    { label: 'Current Stock', key: 'current_stock', sortable: true },
    { label: 'Total In', key: 'total_in' },
    { label: 'Total Out', key: 'total_out' },
    { label: 'Last Updated', key: 'last_updated' }
]
const search = ref(props.filters.search)
const loading = ref(false)

function debounce(fn, delay) {
    let timeoutId;
    return (...args) => {
        if (timeoutId) clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
}

const performSearch = () => {
    loading.value = true
    router.get(route('admin.stocks.index'), { search: search.value, sort: props.filters.sort, direction: props.filters.direction }, {
        preserveState: true,
        replace: true,
        onFinish: () => loading.value = false
    })
}

watch(search, debounce(() => performSearch(), 500))

function sortColumn(column) {
    let direction = 'asc'
    if (props.filters.sort === column) {
        direction = props.filters.direction === 'asc' ? 'desc' : 'asc'
    }
    loading.value = true
    router.get(route('admin.stocks.index'), { search: search.value, sort: column, direction: direction }, {
        preserveState: true,
        replace: true,
        onFinish: () => loading.value = false
    })
}
</script>
