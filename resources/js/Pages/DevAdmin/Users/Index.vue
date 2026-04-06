<template>
    <DevAdminLayout>
        <!-- Modern Container -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Account Provisioning</h1>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage system administrators and staff
                            access</p>
                    </div>
                </div>
            </div>

            <!-- Search and Add Button -->
            <div
                class="m-4 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                <form @submit.prevent="handleSearch" class="flex space-x-2">
                    <div class="relative">
                        <input type="text" v-model="searchQuery" placeholder="Search by name or email..."
                            class="border dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-lg w-72 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white transition-all shadow-sm" />
                        <span v-if="searchQuery" @click="clearSearch"
                            class="material-symbols-outlined absolute right-2 top-1.5 text-gray-400 text-lg cursor-pointer hover:text-gray-600">close</span>
                    </div>

                    <button type="submit"
                        class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition shadow-sm hover:shadow active:scale-95">
                        Search
                    </button>
                </form>

                <Link :href="route('devAdmin.users.create')"
                    class="px-4 py-1.5 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 flex items-center space-x-2 shadow-sm hover:shadow active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-lg">person_add</span>
                    <span>Provision User</span>
                </Link>
            </div>

            <!-- List Table -->
            <DataTable :headers="['User Details', 'Email', 'Role', 'Status', 'Joined Date', 'Actions']"
                :items="users.data" :pagination="users" :sortableHeaders="{ 0: 'name', 1: 'email', 4: 'created_at' }"
                :currentSort="sort" :currentDirection="direction">
                <template #rows>
                    <tr v-for="user in users.data" :key="user.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-800/50 group transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <div
                                        class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center border border-blue-200 dark:border-blue-800">
                                        <span class="text-blue-700 dark:text-blue-400 font-bold uppercase">{{
                                            user.name.charAt(0) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ user.name }}</div>
                                    <div
                                        class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-widest mt-0.5">
                                        ID: #USR_{{ user.id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ user.email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="px-2 py-0.5 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 text-[10px] font-bold rounded border border-indigo-100 dark:border-indigo-800 uppercase">
                                {{ user.role?.name || 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">ACTIVE</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-500">
                            {{ new Date(user.created_at).toLocaleDateString() }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <Link :href="route('devAdmin.users.edit', user.id)"
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors tooltip"
                                    title="Edit User">
                                    <span class="material-symbols-outlined text-xl font-icon">edit_square</span>
                                </Link>
                                <button @click="deleteUser(user.id)"
                                    class="p-1.5 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-md transition-colors tooltip"
                                    title="Delete User">
                                    <span class="material-symbols-outlined text-xl font-icon">delete</span>
                                </button>
                                <Link :href="route('devAdmin.users.permissions', user.id)"
                                    class="p-1.5 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-md transition-colors tooltip"
                                    title="Manage Permissions">
                                    <span class="material-symbols-outlined text-xl font-icon">shield</span>
                                </Link>
                            </div>
                        </td>
                    </tr>
                </template>

                <template #empty>
                    <div class="flex flex-col items-center space-y-3 py-10">
                        <div
                            class="p-4 bg-gray-50 dark:bg-gray-800 rounded-full border border-gray-100 dark:border-gray-700">
                            <span class="material-symbols-outlined text-gray-400 text-6xl">person_search</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">No agents found</h3>
                        <p class="text-gray-500 text-sm max-w-xs text-center">Your query didn't return any user results.
                            Try searching for more general terms.</p>
                        <button @click="clearSearch" class="text-blue-600 font-bold text-sm hover:underline">Clear
                            search
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
    users: Object,
    search: String,
    sort: String,
    direction: String
})

const searchQuery = ref(props.search || '')

const handleSearch = () => {
    router.get(route('devAdmin.users.index'), {
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

const deleteUser = (id) => {
    if (confirm('CAUTION: Are you sure you want to revoke access for this user? This action cannot be undone.')) {
        router.delete(route('devAdmin.users.destroy', id))
    }
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
