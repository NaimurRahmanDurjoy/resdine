<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Page Header -->
        <div class="bg-white dark:bg-gray-800">
            <div
                class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }}</h1>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage promotional banners and popup
                            campaigns</p>
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
                        <input v-model="search" type="text" placeholder="Search campaigns..."
                            class="block w-full pl-10 pr-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" />
                    </div>
                </div>

                <!-- Add new campaign -->
                <Link :href="route('admin.marketing-campaigns.create')"
                    class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 flex items-center space-x-2 transition">
                    <span class="material-symbols-outlined text-sm">add</span>
                    <span>Add Campaign</span>
                </Link>
            </div>
        </div>

        <!-- Table -->
        <ListTable :headers="headers" :items="campaigns.data" :pagination="campaigns" :sort="filters.sort"
            :direction="filters.direction" :loading="loading" @sort="sortColumn">
            <template #rows="{ items }">
                <tr v-for="campaign in items" :key="campaign.id"
                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-10 rounded overflow-hidden bg-gray-100 border border-gray-200">
                                <img :src="campaign.full_image_url" class="w-full h-full object-cover" />
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ campaign.title }}
                                </div>
                                <div class="text-xs text-gray-500 uppercase tracking-tighter">{{ campaign.type }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ campaign.starts_at ? formatDate(campaign.starts_at) : 'Immediate' }}
                            <span v-if="campaign.ends_at">to {{ formatDate(campaign.ends_at) }}</span>
                            <span v-else> (Permanent)</span>
                        </div>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-center">
                        <span class="text-sm font-black px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600">
                            #{{ campaign.priority }}
                        </span>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                            :class="campaign.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'">
                            {{ campaign.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex space-x-2">
                            <Link :href="route('admin.marketing-campaigns.edit', campaign.id)"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                <span class="material-symbols-outlined">edit</span>
                            </Link>
                            <button @click="deleteCampaign(campaign.id)"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            </template>

            <template #pagination>
                <div class="flex items-center justify-between w-full p-4">
                    <div class="text-sm text-gray-700 dark:text-gray-400">
                        Showing <span class="font-medium">{{ campaigns.from }}</span> to <span class="font-medium">{{
                            campaigns.to
                            }}</span> of <span class="font-medium">{{ campaigns.total }}</span> entries
                    </div>
                    <div class="flex space-x-1">
                        <Link v-for="(link, k) in campaigns.links" :key="k" :href="link.url || '#'" v-html="link.label"
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
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'
import Swal from 'sweetalert2'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    campaigns: Object,
    filters: Object,
    pageTitle: String
})

const headers = [
    { label: 'Campaign / Banner', key: 'title', sortable: true },
    { label: 'Schedule', key: 'starts_at', sortable: true },
    { label: 'Priority', key: 'priority', sortable: true },
    { label: 'Status', key: 'is_active', sortable: false },
    { label: 'Actions', key: 'actions', sortable: false }
]

const search = ref(props.filters.search || '')
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
    router.get(route('admin.marketing-campaigns.index'), { search: search.value, sort: props.filters.sort, direction: props.filters.direction }, {
        preserveState: true,
        replace: true,
        onFinish: () => loading.value = false
    })
}

watch(search, debounce(() => performSearch(), 500))

const formatDate = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

function sortColumn(column) {
    let direction = 'asc'
    if (props.filters.sort === column) {
        direction = props.filters.direction === 'asc' ? 'desc' : 'asc'
    }
    loading.value = true
    router.get(route('admin.marketing-campaigns.index'), { search: search.value, sort: column, direction: direction }, {
        preserveState: true,
        replace: true,
        onFinish: () => loading.value = false
    })
}

const deleteCampaign = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this! The image will also be deleted.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.marketing-campaigns.destroy', id), {
                onSuccess: () => {
                    Swal.fire('Deleted!', 'Campaign has been deleted.', 'success')
                }
            })
        }
    })
}
</script>
