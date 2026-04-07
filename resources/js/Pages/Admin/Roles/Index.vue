<template>
    <AdminLayout :pageTitle="pageTitle">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ pageTitle }}</h1>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage system roles and their
                            permissions</p>
                    </div>
                </div>
            </div>

            <!-- Search and Add Button -->
            <div
                class="m-4 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                <form @submit.prevent="handleSearch" class="flex space-x-2">
                    <div class="relative">
                        <input type="text" v-model="searchQuery" placeholder="Search by name..."
                            class="border dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-lg w-64 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white transition-all shadow-sm" />
                        <span v-if="searchQuery" @click="clearSearch"
                            class="material-symbols-outlined absolute right-2 top-1.5 text-gray-400 text-lg cursor-pointer hover:text-gray-600">close</span>
                    </div>

                    <button type="submit"
                        class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition shadow-sm hover:shadow active:scale-95">
                        Search
                    </button>
                </form>

                <Link :href="route('admin.settings.roles.create')"
                    class="px-4 py-1.5 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 flex items-center space-x-2 shadow-sm hover:shadow active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-lg">add_circle</span>
                    <span>Add New Role</span>
                </Link>
            </div>

            <!-- Table -->
            <DataTable :headers="['Role Details', 'Description', 'Users', 'Status', 'Actions']" :items="roles.data"
                :pagination="roles" :sortableHeaders="{ 0: 'name', 4: 'created_at' }" :currentSort="filters.sort"
                :currentDirection="filters.direction">
                <template #rows>
                    <tr v-for="role in roles.data" :key="role.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-800/50 group transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div
                                    class="h-8 w-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center border border-blue-200 dark:border-blue-800">
                                    <span class="text-blue-700 dark:text-blue-400 font-bold uppercase text-xs">{{
                                        role.name.charAt(0) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div
                                        class="text-sm font-bold text-gray-900 dark:text-white tracking-wide uppercase">
                                        {{ role.name }}</div>
                                    <div
                                        class="text-[9px] text-gray-500 dark:text-gray-400 uppercase tracking-widest mt-0.5">
                                        ID: #RL_{{ role.id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ role.description
                                || 'No description provided' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-mono text-gray-500 dark:text-gray-500">
                                {{ role.users_count || '0' }} Active Users
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full"
                                    :class="role.status ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400'"></span>
                                <span class="text-[10px] font-black uppercase tracking-tighter"
                                    :class="role.status ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-400'">
                                    {{ role.status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <Link :href="route('admin.settings.roles.permissions', role.id)"
                                    class="p-1.5 text-slate-600 hover:bg-slate-50 dark:hover:bg-slate-900/20 rounded-md transition-colors tooltip"
                                    title="Manage Permissions">
                                    <span class="material-symbols-outlined text-xl font-icon">shield</span>
                                </Link>
                                <Link :href="route('admin.settings.roles.edit', role.id)"
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors tooltip"
                                    title="Edit Role">
                                    <span class="material-symbols-outlined text-xl font-icon">edit_square</span>
                                </Link>
                                <button @click="deleteRole(role.id)"
                                    class="p-1.5 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-md transition-colors tooltip"
                                    title="Delete Role">
                                    <span class="material-symbols-outlined text-xl font-icon">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </DataTable>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from '@/Components/Admin/DataTable.vue'

const props = defineProps({
    roles: Object,
    filters: Object,
    pageTitle: String
})

const searchQuery = ref(props.filters.search || '')

const handleSearch = () => {
    router.get(route('admin.settings.roles.index'), {
        search: searchQuery.value,
        sort: props.filters.sort,
        direction: props.filters.direction
    }, {
        preserveState: true,
        replace: true
    })
}

const clearSearch = () => {
    searchQuery.value = ''
    handleSearch()
}

const deleteRole = (id) => {
    if (confirm('CAUTION: Are you sure you want to delete this role? This cannot be undone if users are still assigned.')) {
        router.delete(route('admin.settings.roles.destroy', id))
    }
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
