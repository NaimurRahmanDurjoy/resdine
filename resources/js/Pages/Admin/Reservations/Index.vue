<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-white dark:bg-gray-800">
            <div
                class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Manage table bookings and guest attendance
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
                <Link :href="route('admin.reservations.create')"
                    class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-green-700 flex items-center space-x-2 transition">
                    <span class="material-symbols-outlined text-sm">add</span>
                    <span>New Reservation</span>
                </Link>
            </div>
        </div>

        <ListTable :headers="headers" :items="reservations.data" :pagination="reservations" :sort="filters.sort"
            :direction="filters.direction" :loading="loading" @sort="sortColumn">
            <template #rows="{ items }">
                <tr v-for="reservation in items" :key="reservation.id"
                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ reservation.customer?.name || reservation.customer_name }} <br>
                        <span class="text-xs text-gray-500">{{ reservation.customer?.phone || reservation.customer_phone
                        }}</span>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ reservation.table?.name }} <br>
                        <span class="text-xs">{{ reservation.branch?.name }}</span>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ formatDate(reservation.reservation_time) }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ reservation.guests_count }} Persons
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                            :class="statusClass(reservation.status)">
                            {{ statusLabel(reservation.status) }}
                        </span>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            <button v-if="reservation.status === 1" @click="updateStatus(reservation.id, 2)"
                                class="text-green-600 hover:text-green-900 text-xs flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">check_circle</span> Arrived
                            </button>
                            <button v-if="reservation.status === 1" @click="updateStatus(reservation.id, 0)"
                                class="text-red-600 hover:text-red-900 text-xs flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">cancel</span> Cancel
                            </button>
                        </div>
                    </td>
                </tr>
            </template>
            <template #pagination>
                <div class="flex items-center justify-between w-full">
                    <div class="text-sm text-gray-700 dark:text-gray-400">
                        Showing <span class="font-medium">{{ reservations.from }}</span> to <span class="font-medium">{{
                            reservations.to
                        }}</span> of <span class="font-medium">{{ reservations.total }}</span> entries
                    </div>
                    <div class="flex space-x-1">
                        <Link v-for="(link, k) in reservations.links" :key="k" :href="link.url || '#'"
                            v-html="link.label"
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
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'
import dayjs from 'dayjs'
defineOptions({ layout: AdminLayout })

const props = defineProps({
    reservations: Object,
    filters: Object,
    pageTitle: String
})
const headers = [
    { label: 'Guest', key: 'customer_name', sortable: true },
    { label: 'Table/Branch', key: 'table_id', sortable: false },
    { label: 'Time', key: 'reservation_time', sortable: true },
    { label: 'Guests', key: 'guests_count', sortable: true },
    { label: 'Status', key: 'status', sortable: false },
    { label: 'Actions', key: null, sortable: false }
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
    router.get(route('admin.reservations.index'), { search: search.value, sort: props.filters.sort, direction: props.filters.direction }, {
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
    router.get(route('admin.reservations.index'), { search: search.value, sort: column, direction: direction }, {
        preserveState: true,
        replace: true,
        onFinish: () => loading.value = false
    })
}

const formatDate = (date) => dayjs(date).format('MMM D, YYYY h:mm A')

const statusLabel = (status) => {
    return { 1: 'Confirmed', 0: 'Cancelled', 2: 'Arrived' }[status]
}

const statusClass = (status) => {
    switch (status) {
        case 1: return 'bg-blue-100 text-blue-800'
        case 0: return 'bg-red-100 text-red-800'
        case 2: return 'bg-green-100 text-green-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const updateStatus = (id, status) => {
    router.post(route('admin.reservations.status', id), { status })
}


</script>
