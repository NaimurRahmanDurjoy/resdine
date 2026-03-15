<template>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Page Header -->
            <div class="bg-white dark:bg-gray-800">
                <div
                    class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Purchase Orders</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage vendor purchases and stock intake
                            </p>
                        </div>
                    </div>
                </div>
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

                    <!-- Add new item -->
                    <Link :href="route('admin.purchase.create')"
                        class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-green-700 flex items-center space-x-2 transition">
                        <span class="material-symbols-outlined text-sm">add</span>
                        <span>Add Purchase Order</span>
                    </Link>
                </div>
            </div>
            <!-- Content -->
            <ListTable :headers="headers" :items="purchases.data" :pagination="purchases" :sort="filters.sort"
                :direction="filters.direction" :loading="loading" @sort="sortColumn">
                <template #rows="{ items }">
                    <tr v-for="purchase in items" :key="purchase.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ new Date(purchase.purchase_date).toLocaleDateString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ purchase.invoice_no || 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ purchase.supplier?.name || purchase.supplier?.company_name || 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100">
                            ${{ parseFloat(purchase.total_amount).toFixed(2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ purchase.status.charAt(0).toUpperCase() + purchase.status.slice(1) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-3">
                                <Link :href="route('admin.purchase.show', purchase.id)"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                    <span class="material-symbols-outlined">visibility</span>
                                </Link>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="items.length === 0">
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                            No purchase orders found. Click "New Purchase" to record intake.
                        </td>
                    </tr>
                </template>
            </ListTable>

        </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    purchases: Object,
    filters: Object,
    pageTitle: String
})
const headers = [
    { label: 'Purchase Date', key: 'purchase_date', sortable: true },
    { label: 'Invoice No.', key: 'invoice_no', sortable: true },
    { label: 'Supplier', key: 'supplier_name', sortable: true },
    { label: 'Total Amount', key: 'total_amount', sortable: true },
    { label: 'Status', key: 'status', sortable: true },
    { label: 'Actions', key: 'actions', sortable: false }
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
    router.get(route('admin.purchases.index'), { search: search.value, sort: props.filters.sort, direction: props.filters.direction }, {
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
    router.get(route('admin.purchases.index'), { search: search.value, sort: column, direction: direction }, {
        preserveState: true,
        replace: true,
        onFinish: () => loading.value = false
    })
}

const deletePurchase = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "Deleting a purchase order may affect related inventory records!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.purchases.destroy', id), {
                onSuccess: () => {
                    Swal.fire('Deleted!', 'Purchase order has been deleted.', 'success')
                }
            })
        }
    })
}
</script>
