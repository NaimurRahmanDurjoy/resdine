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
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ new Date(purchase.purchase_date).toLocaleDateString() }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ purchase.invoice_number || 'N/A' }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ purchase.supplier?.name || purchase.supplier?.company_name || 'N/A' }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100">
                        ${{ parseFloat(purchase.total_amount).toFixed(2) }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap">
                        <span
                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            {{ purchase.status_label }}
                        </span>
                    </td>
                        <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex gap-3">
                                <button @click="showDetails(purchase)"
                                    :disabled="fetchingId === purchase.id"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors disabled:opacity-50">
                                    <span v-if="fetchingId === purchase.id" class="material-symbols-outlined animate-spin">progress_activity</span>
                                    <span v-else class="material-symbols-outlined">visibility</span>
                                </button>
                            </div>
                        </td>
                </tr>
            </template>
            <template #pagination>
                <div class="flex items-center justify-between w-full">
                    <div class="text-sm text-gray-700 dark:text-gray-400">
                        Showing <span class="font-medium">{{ purchases.from }}</span> to <span class="font-medium">
                            {{ purchases.to }}</span> of <span class="font-medium">{{ purchases.total }}</span> entries
                    </div>
                    <div class="flex space-x-1">
                        <Link v-for="(link, k) in purchases.links" :key="k" :href="link.url || '#'" v-html="link.label"
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

<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import axios from 'axios'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'
import { openModal } from '@/Stores/modalStore'
import PurchaseDetails from './PurchaseDetails.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    purchases: Object,
    filters: Object,
    pageTitle: String
})
const headers = [
    { label: 'Purchase Date', key: 'purchase_date', sortable: true },
    { label: 'Invoice No.', key: 'invoice_number', sortable: true },
    { label: 'Supplier', key: 'supplier_name', sortable: true },
    { label: 'Total Amount', key: 'total_amount', sortable: true },
    { label: 'Status', key: 'status', sortable: true },
    { label: 'Actions', key: 'actions', sortable: false }
]
const search = ref(props.filters.search)
const loading = ref(false)
const fetchingId = ref(null)

const showDetails = async (purchase) => {
    try {
        fetchingId.value = purchase.id
        const response = await axios.get(route('admin.purchase.show', purchase.id), {
            headers: { 'Accept': 'application/json' }
        })
        
        openModal(PurchaseDetails, { purchase: response.data }, {
            title: `Purchase Order: ${response.data.invoice_number || response.data.id}`,
            maxWidth: '5xl'
        })
    } catch (error) {
        console.error('Failed to fetch purchase details:', error)
        Swal.fire('Error', 'Could not load purchase details.', 'error')
    } finally {
        fetchingId.value = null
    }
}

function debounce(fn, delay) {
    let timeoutId;
    return (...args) => {
        if (timeoutId) clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
}

const performSearch = () => {
    loading.value = true
    router.get(route('admin.purchase.index'), { search: search.value, sort: props.filters.sort, direction: props.filters.direction }, {
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
    router.get(route('admin.purchase.index'), { search: search.value, sort: column, direction: direction }, {
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
            router.delete(route('admin.purchase.destroy', id), {
                onSuccess: () => {
                    Swal.fire('Deleted!', 'Purchase order has been deleted.', 'success')
                }
            })
        }
    })
}
</script>
