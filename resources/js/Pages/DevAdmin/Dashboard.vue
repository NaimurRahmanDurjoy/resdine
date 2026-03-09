<template>
    <DevAdminLayout>
        <div class="space-y-6">
            <!-- Header Info -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">System Dashboard</h1>
                    <p class="text-sm text-gray-500">Overview of system health and deployment metrics</p>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="refreshData"
                        class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                        <span class="material-symbols-outlined text-lg font-icon">refresh</span>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Users -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400">
                            <span class="material-symbols-outlined text-2xl font-icon">group</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white leading-none mt-1">{{
                            formatNumber(totalUsers) }}</p>
                    </div>
                </div>

                <!-- Database Size -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400">
                            <span class="material-symbols-outlined text-2xl font-icon">database</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Database Size</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white leading-none mt-1">{{ databaseSize }}
                            <span class="text-sm font-medium">MB</span>
                        </p>
                    </div>
                </div>

                <!-- Errors -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400">
                            <span class="material-symbols-outlined text-2xl font-icon">error</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Errors Today</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white leading-none mt-1">{{ errorsToday }}
                        </p>
                    </div>
                </div>

                <!-- Server Load -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400">
                            <span class="material-symbols-outlined text-2xl font-icon">speed</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Server Load</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white leading-none mt-1 text-base">{{
                            serverLoad }}</p>
                    </div>
                </div>
            </div>

            <!-- System Health & Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Health Monitor -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-gray-900 dark:text-white">System Monitor</h3>
                        <span
                            class="px-2 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase rounded-md">Real-time</span>
                    </div>
                    <div class="space-y-4">
                        <div v-for="(status, key) in systemHealth" :key="key"
                            class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-transparent hover:border-gray-100 dark:hover:border-gray-700 transition-all">
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full mr-3" :class="getHealthColor(status)"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{
                                    key.replace('_', ' ') }}</span>
                            </div>
                            <span class="text-xs font-bold" :class="getHealthTextColor(status)">{{ status }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-6">Operations Hub</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <Link :href="route('devAdmin.cache.clear')"
                            class="group flex flex-col p-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-2xl hover:bg-cyan-50 dark:hover:bg-cyan-900/10 hover:border-cyan-200 dark:hover:border-cyan-800 transition-all">
                            <span class="material-symbols-outlined text-cyan-600 mb-3 font-icon">bolt</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Clear Cache</span>
                            <span class="text-[10px] text-gray-500">Flush all buffers</span>
                        </Link>

                        <Link :href="route('devAdmin.logs.view')"
                            class="group flex flex-col p-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-2xl hover:bg-purple-50 dark:hover:bg-purple-900/10 hover:border-purple-200 dark:hover:border-purple-800 transition-all">
                            <span class="material-symbols-outlined text-purple-600 mb-3 font-icon">summarize</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Review Logs</span>
                            <span class="text-[10px] text-gray-500">Check system events</span>
                        </Link>

                        <Link :href="route('devAdmin.database.info')"
                            class="group flex flex-col p-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-2xl hover:bg-emerald-50 dark:hover:bg-emerald-900/10 hover:border-emerald-200 dark:hover:border-emerald-800 transition-all">
                            <span class="material-symbols-outlined text-emerald-600 mb-3 font-icon">storage</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">DB Explorer</span>
                            <span class="text-[10px] text-gray-500">Table health info</span>
                        </Link>

                        <Link :href="route('devAdmin.users.create')"
                            class="group flex flex-col p-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-2xl hover:bg-blue-50 dark:hover:bg-blue-900/10 hover:border-blue-200 dark:hover:border-blue-800 transition-all">
                            <span class="material-symbols-outlined text-blue-600 mb-3 font-icon">person_add</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Provision User</span>
                            <span class="text-[10px] text-gray-500">Create new account</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'

const props = defineProps({
    totalUsers: Number,
    ordersToday: Number,
    revenueToday: Number,
    databaseSize: [Number, String],
    errorsToday: Number,
    systemHealth: Object,
    serverLoad: String
})

const formatNumber = (num) => {
    return new Intl.NumberFormat().format(num)
}

const getHealthColor = (status) => {
    if (status === 'Healthy' || status === 'Connected' || status === 'Online') return 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]'
    if (status === 'Processing' || status === 'Idle') return 'bg-blue-500'
    return 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]'
}

const getHealthTextColor = (status) => {
    if (status === 'Healthy' || status === 'Connected' || status === 'Online') return 'text-emerald-600 dark:text-emerald-400'
    if (status === 'Processing' || status === 'Idle') return 'text-blue-600 dark:text-blue-400'
    return 'text-red-600 dark:text-red-400'
}

const refreshData = () => {
    router.reload({ only: ['systemHealth', 'errorsToday', 'serverLoad'] })
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
