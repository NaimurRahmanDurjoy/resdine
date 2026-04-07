<template>
    <DevAdminLayout>
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Menu Actions (Internal Links)</h1>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage granular actions tied to
                            software menus</p>
                    </div>
                </div>
            </div>

            <!-- Search and Add Button -->
            <div
                class="m-4 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                <form @submit.prevent="handleSearch" class="flex space-x-2">
                    <div class="relative">
                        <input type="text" v-model="searchQuery" placeholder="Search actions or routes..."
                            class="border dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-lg w-64 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white transition-all shadow-sm" />
                        <span v-if="searchQuery" @click="clearSearch"
                            class="material-symbols-outlined absolute right-2 top-1.5 text-gray-400 text-lg cursor-pointer hover:text-gray-600">close</span>
                    </div>

                    <button type="submit"
                        class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition shadow-sm hover:shadow active:scale-95">
                        Search
                    </button>
                </form>

                <Link :href="route('devAdmin.systemConfig.software.internal-link.create')"
                    class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 flex items-center space-x-2 shadow-sm hover:shadow active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-lg">add_circle</span>
                    <span>Add Action</span>
                </Link>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-6 py-3 text-left">
                                <div class="flex items-center space-x-2 cursor-pointer hover:text-blue-600"
                                    @click="updateSort('id')">
                                    <span
                                        class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">ID</span>
                                    <span v-if="sort === 'id'" class="material-symbols-outlined text-sm">{{
                                        direction === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <div class="flex items-center space-x-2 cursor-pointer hover:text-blue-600"
                                    @click="updateSort('action')">
                                    <span
                                        class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Action</span>
                                    <span v-if="sort === 'action'" class="material-symbols-outlined text-sm">{{
                                        direction === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <div class="flex items-center space-x-2 cursor-pointer hover:text-blue-600"
                                    @click="updateSort('route')">
                                    <span
                                        class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Route</span>
                                    <span v-if="sort === 'route'" class="material-symbols-outlined text-sm">{{
                                        direction === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <span
                                    class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Menu</span>
                            </th>
                            <th class="px-6 py-3 text-center">
                                <span
                                    class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Active</span>
                            </th>
                            <th class="px-6 py-3 text-center">
                                <span
                                    class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="action in items.data" :key="action.id"
                            class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">{{ action.id }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-bold"
                                    :class="getActionBadgeClass(action.action)">
                                    {{ action.action.toUpperCase() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-gray-900 dark:text-gray-100">
                                {{ action.route }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ action.software_menu?.name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="toggleActive(action)" :class="[
                                    'px-3 py-1 rounded-full text-xs font-bold transition-all shadow-sm',
                                    action.is_active
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 hover:bg-green-200'
                                        : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200'
                                ]">
                                    {{ action.is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <Link :href="route('devAdmin.systemConfig.software.internal-link.permissions', action.id)"
                                        class="p-1.5 rounded-lg bg-slate-100 text-slate-600 dark:bg-slate-900/30 dark:text-slate-400 hover:bg-slate-200 transition inline-flex items-center group/shield"
                                        title="Manage Permissions Across Roles/Users">
                                        <span class="material-symbols-outlined text-lg group-hover/shield:scale-110 transition-transform">shield</span>
                                    </Link>
                                    <Link :href="route('devAdmin.systemConfig.software.internal-link.edit', action.id)"
                                        class="p-1.5 rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 hover:bg-blue-200 transition inline-flex items-center"
                                        title="Edit">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </Link>
                                    <button @click="deleteAction(action.id)"
                                        class="p-1.5 rounded-lg bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 hover:bg-red-200 transition inline-flex items-center"
                                        title="Delete">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-if="items.data.length === 0" class="flex flex-col items-center space-y-3 py-10">
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-full border border-gray-100 dark:border-gray-700">
                    <span class="material-symbols-outlined text-gray-400 text-6xl">list_alt</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">No Actions Found</h3>
                <p class="text-gray-500 text-sm max-w-xs text-center">No menu actions configured yet. Create your first
                    action to get started.</p>
                <button @click="clearSearch" class="text-blue-600 font-bold text-sm hover:underline">
                    Clear all filters
                </button>
            </div>

            <!-- Pagination -->
            <div v-if="items.data.length > 0"
                class="border-t border-gray-100 dark:border-gray-700 px-6 py-4 flex justify-between items-center">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ items.from }} to {{ items.to }} of {{ items.total }} results
                </div>
                <div class="flex space-x-2">
                    <Link v-for="link in items.links" :key="link.label" :href="link.url || '#'" :class="[
                        'px-3 py-1.5 rounded-lg text-sm font-semibold transition-all',
                        link.active
                            ? 'bg-blue-600 text-white shadow-lg'
                            : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                    ]" v-html="link.label">
                    </Link>
                </div>
            </div>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'

const props = defineProps({
    items: Object,
    search: String,
    sort: String,
    direction: String
})

const searchQuery = ref(props.search || '')
const sort = ref(props.sort || 'id')
const direction = ref(props.direction || 'desc')

const handleSearch = () => {
    router.get(route('devAdmin.systemConfig.software.internal-link.index'), {
        search: searchQuery.value,
        sort: sort.value,
        direction: direction.value
    }, {
        preserveState: true,
        replace: true
    })
}

const clearSearch = () => {
    searchQuery.value = ''
    sort.value = 'id'
    direction.value = 'desc'
    handleSearch()
}

const updateSort = (field) => {
    if (sort.value === field) {
        direction.value = direction.value === 'asc' ? 'desc' : 'asc'
    } else {
        sort.value = field
        direction.value = 'asc'
    }
    handleSearch()
}

const deleteAction = (id) => {
    if (confirm('Are you sure you want to delete this menu action?')) {
        router.delete(route('devAdmin.systemConfig.software.internal-link.destroy', id))
    }
}

const toggleActive = (action) => {
    router.patch(route('devAdmin.systemConfig.software.internal-link.update', action.id), {
        software_menu_id: action.software_menu_id,
        action: action.action,
        route: action.route,
        is_active: !action.is_active
    }, {
        preserveScroll: true
    })
}

const getActionBadgeClass = (action) => {
    const baseClass = 'text-xs font-bold'
    switch (action.toLowerCase()) {
        case 'view':
            return `${baseClass} bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400`
        case 'create':
            return `${baseClass} bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400`
        case 'edit':
            return `${baseClass} bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400`
        case 'delete':
            return `${baseClass} bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400`
        default:
            return `${baseClass} bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300`
    }
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
