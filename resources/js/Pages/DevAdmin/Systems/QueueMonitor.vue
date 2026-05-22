<template>
    <DevAdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center bg-gray-900 p-6 rounded-xl border border-gray-800 shadow-xl">
                <div>
                    <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                        <span class="material-symbols-outlined text-emerald-400">speed</span>
                        Queue Monitor
                    </h2>
                    <p class="text-gray-400 text-sm mt-1">Real-time overview of background jobs and workers</p>
                </div>
                <div class="flex items-center gap-4">
                    <button @click="refreshData"
                        class="flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-gray-300 px-4 py-2 rounded-lg transition-colors border border-gray-700">
                        <span class="material-symbols-outlined text-sm" :class="{ 'animate-spin': isRefreshing }">refresh</span>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Pending Jobs -->
                <div class="bg-gray-900 rounded-xl p-6 border border-gray-800 shadow-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl -mr-10 -mt-10 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-blue-500/10 rounded-lg border border-blue-500/20">
                            <span class="material-symbols-outlined text-blue-400">pending_actions</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-gray-400 text-sm font-medium">Pending Jobs</h3>
                        <p class="text-3xl font-bold text-white mt-1">{{ pendingJobs }}</p>
                    </div>
                </div>

                <!-- Failed Jobs -->
                <div class="bg-gray-900 rounded-xl p-6 border border-gray-800 shadow-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-rose-500/10 rounded-full blur-3xl -mr-10 -mt-10 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-rose-500/10 rounded-lg border border-rose-500/20">
                            <span class="material-symbols-outlined text-rose-400">error</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-gray-400 text-sm font-medium">Failed Jobs</h3>
                        <p class="text-3xl font-bold text-white mt-1">{{ failedJobsCount }}</p>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-gray-900 rounded-xl p-6 border border-gray-800 shadow-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl -mr-10 -mt-10 transition-transform group-hover:scale-110"></div>
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-emerald-500/10 rounded-lg border border-emerald-500/20">
                            <span class="material-symbols-outlined text-emerald-400">monitor_heart</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-gray-400 text-sm font-medium">System Status</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                            <p class="text-xl font-bold text-emerald-400">Active</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Pending Jobs -->
                <div class="bg-gray-900 rounded-xl border border-gray-800 shadow-xl overflow-hidden flex flex-col h-[500px]">
                    <div class="p-4 border-b border-gray-800 bg-gray-800/50">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-400 text-xl">list_alt</span>
                            Queue Pipeline (Top 20)
                        </h3>
                    </div>
                    <div class="flex-1 overflow-auto custom-scrollbar p-2">
                        <div v-if="recentJobs.length === 0" class="flex flex-col items-center justify-center h-full text-gray-500 space-y-3">
                            <span class="material-symbols-outlined text-4xl opacity-50">check_circle</span>
                            <p>No pending jobs in the queue.</p>
                        </div>
                        <ul v-else class="space-y-2">
                            <li v-for="job in recentJobs" :key="job.id" class="p-4 rounded-lg bg-gray-800/50 border border-gray-700/50 hover:border-blue-500/30 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-blue-400 font-mono">{{ job.queue }}</p>
                                        <p class="text-xs text-gray-400 mt-1 truncate max-w-[300px]" :title="parsePayload(job.payload)">
                                            {{ parsePayload(job.payload) }}
                                        </p>
                                    </div>
                                    <span class="text-xs text-gray-500 bg-gray-800 px-2 py-1 rounded">Attempts: {{ job.attempts }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Recent Failed Jobs -->
                <div class="bg-gray-900 rounded-xl border border-gray-800 shadow-xl overflow-hidden flex flex-col h-[500px]">
                    <div class="p-4 border-b border-gray-800 bg-gray-800/50">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-rose-400 text-xl">warning</span>
                            Failed Jobs (Top 20)
                        </h3>
                    </div>
                    <div class="flex-1 overflow-auto custom-scrollbar p-2">
                        <div v-if="failedJobs.length === 0" class="flex flex-col items-center justify-center h-full text-gray-500 space-y-3">
                            <span class="material-symbols-outlined text-4xl opacity-50">task_alt</span>
                            <p>No failed jobs.</p>
                        </div>
                        <ul v-else class="space-y-2">
                            <li v-for="job in failedJobs" :key="job.id" class="p-4 rounded-lg bg-rose-900/10 border border-rose-900/30 hover:border-rose-500/30 transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <p class="text-sm font-medium text-rose-400 font-mono">{{ job.queue }}</p>
                                    <span class="text-xs text-gray-500 bg-gray-800 px-2 py-1 rounded" :title="job.failed_at">
                                        {{ new Date(job.failed_at).toLocaleString() }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 truncate max-w-[300px] mb-2" :title="parsePayload(job.payload)">
                                    {{ parsePayload(job.payload) }}
                                </p>
                                <div class="bg-[#0d1117] p-3 rounded text-[10px] text-rose-300 font-mono overflow-x-auto border border-gray-800 custom-scrollbar">
                                    <p class="whitespace-pre-wrap">{{ job.exception.split('\n')[0] }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'

const props = defineProps({
    pendingJobs: Number,
    failedJobsCount: Number,
    recentJobs: Array,
    failedJobs: Array
})

const isRefreshing = ref(false)

const refreshData = () => {
    isRefreshing.value = true
    router.reload({
        only: ['pendingJobs', 'failedJobsCount', 'recentJobs', 'failedJobs'],
        onFinish: () => {
            isRefreshing.value = false
        }
    })
}

const parsePayload = (payloadString) => {
    try {
        const payload = JSON.parse(payloadString)
        return payload.displayName || payload.job || 'Unknown Job'
    } catch (e) {
        return 'Invalid Payload'
    }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #30363d;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #484f58;
}
</style>
