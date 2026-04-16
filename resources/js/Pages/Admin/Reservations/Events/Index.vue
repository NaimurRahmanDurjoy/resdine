<template>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-white dark:bg-gray-800">
            <div
                class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Schedule branch-wide events and manage
                            approvals</p>
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
                <Link :href="route('admin.events.create')"
                    class="px-3 py-1 bg-indigo-600 text-white rounded text-sm font-semibold hover:bg-indigo-700 flex items-center gap-2 transition-all shadow-md shadow-indigo-100 dark:shadow-none">
                    <span class="material-symbols-outlined text-sm">event</span>
                    Schedule Event
                </Link>
            </div>
        </div>

        <ListTable :headers="headers" :items="events.data" :pagination="events" :sort="filters.sort"
            :direction="filters.direction" :loading="loading" @sort="sortColumn">

            <template #rows="{ items }">
                <tr v-for="event in items" :key="event.id"
                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ event.title }} <br>
                        <span class="text-xs text-gray-500">{{ event.estimated_guests }} Est. Guests</span>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ event.branch?.name }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ formatRange(event.start_time, event.end_time) }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                            :class="approvalClass(event.admin_approval)">
                            {{ approvalLabel(event.admin_approval) }}
                        </span>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex gap-2">
                            <button v-if="event.admin_approval === 0" @click="updateApproval(event.id, 1)"
                                class="text-indigo-600 hover:text-indigo-900 border border-indigo-200 px-2 py-1 rounded text-xs">
                                Approve
                            </button>
                            <button v-if="event.admin_approval === 0" @click="updateApproval(event.id, 2)"
                                class="text-red-600 hover:text-red-900 border border-red-200 px-2 py-1 rounded text-xs">
                                Reject
                            </button>
                        </div>
                    </td>
                </tr>
            </template>
            <template #pagination>
                <div class="flex items-center justify-between w-full">
                    <div class="text-sm text-gray-700 dark:text-gray-400">
                        Showing <span class="font-medium">{{ events.from }}</span> to <span class="font-medium">{{
                            events.to
                        }}</span> of <span class="font-medium">{{ events.total }}</span> entries
                    </div>
                    <div class="flex space-x-1">
                        <Link v-for="(link, k) in events.links" :key="k" :href="link.url || '#'" v-html="link.label"
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
import Swal from 'sweetalert2'
defineOptions({ layout: AdminLayout })

const props = defineProps({
    events: Object,
    filters: Object,
    pageTitle: String
})
const headers = [
    { label: 'Event', key: 'title', sortable: true },
    { label: 'Branch', key: 'branch.name', sortable: false },
    { label: 'Duration', key: null, sortable: false },
    { label: 'Approval Status', key: 'admin_approval', sortable: false },
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
    router.get(route('admin.events.index'), { search: search.value, sort: props.filters.sort, direction: props.filters.direction }, {
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
    router.get(route('admin.events.index'), { search: search.value, sort: column, direction: direction }, {
        preserveState: true,
        replace: true,
        onFinish: () => loading.value = false
    })
}
const formatRange = (start, end) => `${dayjs(start).format('MMM D, h:mm A')} - ${dayjs(end).format('h:mm A')}`

const approvalLabel = (status) => {
    return { 0: 'Pending', 1: 'Approved', 2: 'Rejected' }[status]
}

const approvalClass = (status) => {
    switch (status) {
        case 0: return 'bg-yellow-100 text-yellow-800'
        case 1: return 'bg-green-100 text-green-800'
        case 2: return 'bg-red-100 text-red-800'
        default: return 'bg-gray-100 text-gray-800'
    }
}

const updateApproval = (id, approval) => {
    const action = approval === 1 ? 'approve' : 'reject'
    Swal.fire({
        title: `Are you sure to ${action}?`,
        text: approval === 1 ? "This will fully book the branch for this period!" : "",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.events.approve', id), { approval })
        }
    })
}
</script>
