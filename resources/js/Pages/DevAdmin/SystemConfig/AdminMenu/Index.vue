<template>
    <DevAdminLayout>
        <!-- Modern Container -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-indigo-50 to-white dark:from-indigo-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Admin Menu List</h1>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage your admin panel navigation
                            structure</p>
                    </div>
                </div>
            </div>

            <!-- Search and Add Button -->
            <div
                class="m-4 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                <form @submit.prevent="handleSearch" class="flex space-x-2">
                    <div class="relative">
                        <input type="text" v-model="searchQuery" placeholder="Search menus..."
                            class="border dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-lg w-64 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-white transition-all shadow-sm" />
                        <span v-if="searchQuery" @click="clearSearch"
                            class="material-symbols-outlined absolute right-2 top-1.5 text-gray-400 text-lg cursor-pointer hover:text-gray-600">close</span>
                    </div>

                    <button type="submit"
                        class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow-sm hover:shadow active:scale-95">
                        Search
                    </button>
                </form>

                <Link :href="route('devAdmin.systemConfig.adminPanel.menu.create')"
                    class="px-4 py-1.5 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 flex items-center space-x-2 shadow-sm hover:shadow active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-lg">add</span>
                    <span>Add Menu</span>
                </Link>
            </div>

            <!-- List Table -->
            <DataTable :headers="['Menu Name', 'Route', 'Icon', 'Parent', 'Order', 'Status', 'Actions']"
                :items="menus.data" :pagination="menus" :sortableHeaders="{ 0: 'name', 1: 'route', 4: 'order' }"
                :currentSort="sort" :currentDirection="direction">
                <template #rows>
                    <template v-for="menu in menus.data" :key="menu.id">
                        <!-- Parent Row -->
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 group transition-colors">
                            <td class="px-6 py-3 font-bold text-indigo-900 dark:text-indigo-400">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                    {{ menu.name }}
                                </div>
                            </td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs">{{ menu.route ||
                                '-' }}</td>
                            <td class="px-6 py-3">
                                <span v-if="menu.icon"
                                    class="material-symbols-outlined text-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 p-1.5 rounded-lg border border-indigo-100 dark:border-indigo-800/50">{{
                                        menu.icon }}</span>
                                <span v-else class="text-gray-300">-</span>
                            </td>
                            <td class="px-6 py-3 text-gray-400 text-xs italic">Parent</td>
                            <td class="px-6 py-3">
                                <span
                                    class="px-2 py-0.5 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded text-[10px] font-bold text-gray-600 dark:text-gray-400">
                                    ORDER: {{ menu.order }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span :class="[
                                    'px-2 py-0.5 text-[10px] font-bold rounded-full uppercase tracking-tighter',
                                    menu.is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400'
                                ]">
                                    {{ menu.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <Link :href="route('devAdmin.systemConfig.adminPanel.menu.edit', menu.id)"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors tooltip"
                                        title="Edit Menu">
                                        <span class="material-symbols-outlined text-xl font-icon">edit</span>
                                    </Link>
                                    <button @click="deleteMenu(menu.id)"
                                        class="p-1.5 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-md transition-colors tooltip"
                                        title="Delete Menu">
                                        <span class="material-symbols-outlined text-xl font-icon">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Child Rows -->
                        <tr v-for="child in menu.children_recursive" :key="child.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800/50 bg-gray-50/50 dark:bg-gray-900/30 group transition-colors">
                            <td
                                class="px-6 py-2 text-gray-700 dark:text-gray-300 pl-12 border-l-2 border-indigo-100 dark:border-indigo-900">
                                <div class="flex items-center gap-2">
                                    <span class="text-indigo-300 leading-none">└─</span>
                                    {{ child.name }}
                                </div>
                            </td>
                            <td class="px-6 py-2 text-gray-500 dark:text-gray-500 font-mono text-xs">{{ child.route ||
                                '-' }}</td>
                            <td class="px-6 py-2">
                                <span v-if="child.icon" class="material-symbols-outlined text-gray-400 text-lg">{{
                                    child.icon }}</span>
                                <span v-else class="text-gray-300">-</span>
                            </td>
                            <td class="px-6 py-2 text-gray-500 text-xs">{{ menu.name }}</td>
                            <td class="px-6 py-2">
                                <span class="text-[10px] text-gray-400">#{{ child.order }}</span>
                            </td>
                            <td class="px-6 py-2">
                                <span :class="[
                                    'w-2 h-2 rounded-full inline-block mr-2',
                                    child.is_active ? 'bg-emerald-500' : 'bg-rose-500'
                                ]"></span>
                                <span class="text-xs">{{ child.is_active ? 'Active' : 'Hidden' }}</span>
                            </td>
                            <td class="px-6 py-2">
                                <div class="flex items-center gap-2">
                                    <Link :href="route('devAdmin.systemConfig.adminPanel.menu.edit', child.id)"
                                        class="text-gray-400 hover:text-blue-600 transition-colors">
                                        <span class="material-symbols-outlined text-lg font-icon">edit</span>
                                    </Link>
                                    <button @click="deleteMenu(child.id)"
                                        class="text-gray-400 hover:text-rose-600 transition-colors">
                                        <span class="material-symbols-outlined text-lg font-icon">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </template>

                <template #empty>
                    <div class="flex flex-col items-center space-y-3 py-10">
                        <div
                            class="p-4 bg-gray-50 dark:bg-gray-800 rounded-full border border-gray-100 dark:border-gray-700">
                            <span class="material-symbols-outlined text-gray-400 text-6xl">menu_book</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">No menus discovered</h3>
                        <p class="text-gray-500 text-sm max-w-xs text-center">Your query didn't return any results. Try
                            adjusting your search filters.</p>
                        <button @click="clearSearch" class="text-indigo-600 font-bold text-sm hover:underline">Clear all
                            filters</button>
                    </div>
                </template>
            </DataTable>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'
import DataTable from '@/Components/Admin/DataTable.vue'

const props = defineProps({
    menus: Object,
    search: String,
    sort: String,
    direction: String
})

const searchQuery = ref(props.search || '')

const handleSearch = () => {
    router.get(route('devAdmin.systemConfig.adminPanel.menu.index'), {
        search: searchQuery.value,
        sort: props.sort,
        direction: props.direction
    }, {
        preserveState: true,
        replace: true
    })
}

const clearSearch = () => {
    searchQuery.value = ''
    handleSearch()
}

const deleteMenu = (id) => {
    if (confirm('Are you sure you want to permanently delete this menu item and all its children?')) {
        router.delete(route('devAdmin.systemConfig.adminPanel.menu.destroy', id), {
            onSuccess: () => {
                // Notification success
            }
        })
    }
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
