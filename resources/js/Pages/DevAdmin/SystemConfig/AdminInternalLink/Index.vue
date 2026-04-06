<template>
    <DevAdminLayout>
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-purple-50 to-white dark:from-purple-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Admin Panel Menu Configuration
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage navigation structure for admin
                            panel</p>
                    </div>
                </div>
            </div>

            <!-- Search and Add Button -->
            <div
                class="m-4 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                <form @submit.prevent="handleSearch" class="flex space-x-2">
                    <div class="relative">
                        <input type="text" v-model="searchQuery" placeholder="Search admin menus..."
                            class="border dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-lg w-64 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:text-white transition-all shadow-sm" />
                        <span v-if="searchQuery" @click="clearSearch"
                            class="material-symbols-outlined absolute right-2 top-1.5 text-gray-400 text-lg cursor-pointer hover:text-gray-600">close</span>
                    </div>

                    <button type="submit"
                        class="px-4 py-1.5 bg-purple-600 text-white rounded-lg text-sm font-semibold hover:bg-purple-700 transition shadow-sm hover:shadow active:scale-95">
                        Search
                    </button>
                </form>

                <Link :href="route('devAdmin.systemConfig.adminPanel.menu.create')"
                    class="px-4 py-1.5 bg-purple-600 text-white rounded-lg text-sm font-semibold hover:bg-purple-700 flex items-center space-x-2 shadow-sm hover:shadow active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-lg">add_circle</span>
                    <span>Add Admin Menu</span>
                </Link>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-6 py-3 text-left">
                                <div class="flex items-center space-x-2 cursor-pointer hover:text-purple-600"
                                    @click="updateSort('name')">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Menu
                                        Name</span>
                                    <span v-if="sort === 'name'" class="material-symbols-outlined text-sm">{{
                                        direction === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <div class="flex items-center space-x-2 cursor-pointer hover:text-purple-600"
                                    @click="updateSort('route')">
                                    <span
                                        class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Route</span>
                                    <span v-if="sort === 'route'" class="material-symbols-outlined text-sm">{{
                                        direction === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <span
                                    class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Icon</span>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <span
                                    class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Parent</span>
                            </th>
                            <th class="px-6 py-3 text-center">
                                <div class="flex items-center space-x-2 cursor-pointer hover:text-purple-600 justify-center"
                                    @click="updateSort('order')">
                                    <span
                                        class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Order</span>
                                    <span v-if="sort === 'order'" class="material-symbols-outlined text-sm">{{
                                        direction === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-center">
                                <span
                                    class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Status</span>
                            </th>
                            <th class="px-6 py-3 text-center">
                                <span
                                    class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="menu in items.data" :key="menu.id"
                            class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ menu.name }}
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">
                                {{ menu.route || '--' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <span v-if="menu.icon" class="material-symbols-outlined inline-block text-gray-500">{{
                                    menu.icon }}</span>
                                <span v-else class="text-gray-400">--</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ getParentName(menu) || 'Root' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center font-mono text-gray-600 dark:text-gray-400">
                                {{ menu.order || 0 }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span :class="[
                                    'px-3 py-1 rounded-full text-xs font-bold',
                                    menu.is_active
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400'
                                ]">
                                    {{ menu.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <Link :href="route('devAdmin.systemConfig.adminPanel.menu.edit', menu.id)"
                                        class="p-1.5 rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400 hover:bg-purple-200 transition inline-flex items-center"
                                        title="Edit">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </Link>
                                    <button @click="deleteMenu(menu.id)"
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
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">No Admin Menus Found</h3>
                <p class="text-gray-500 text-sm max-w-xs text-center">No admin menus configured yet. Start by creating a
                    root menu item.</p>
                <button @click="clearSearch" class="text-purple-600 font-bold text-sm hover:underline">
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
                            ? 'bg-purple-600 text-white shadow-lg'
                            : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                    ]" v-html="link.label">
                    </Link>
                </div>
            </div>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'

const props = defineProps({
    items: Object,
    search: String,
    sort: String,
    direction: String
})

const searchQuery = ref(props.search || '')
const sort = ref(props.sort || 'order')
const direction = ref(props.direction || 'asc')

const handleSearch = () => {
    router.get(route('devAdmin.systemConfig.adminPanel.internalLink.index'), {
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
    sort.value = 'order'
    direction.value = 'asc'
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

const deleteMenu = (id) => {
    if (confirm('Are you sure you want to permanently delete this admin menu?')) {
        router.delete(route('devAdmin.systemConfig.adminPanel.menu.destroy', id))
    }
}

const getParentName = (menu) => {
    if (!menu.parent_id) return null
    // Find parent in items - this assumes parent is also in the current page
    // In production, you might want to store parent name with the menu
    return 'Parent Menu'
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
