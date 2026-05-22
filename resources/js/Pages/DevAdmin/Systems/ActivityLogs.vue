<template>
    <DevAdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center bg-gray-900 p-6 rounded-xl border border-gray-800 shadow-xl">
                <div>
                    <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                        <span class="material-symbols-outlined text-purple-400">policy</span>
                        System Activity Logs
                    </h2>
                    <p class="text-gray-400 text-sm mt-1">Audit trail of critical administrative actions</p>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-gray-900 rounded-xl border border-gray-800 shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-400">
                        <thead class="bg-gray-800/50 text-gray-300 border-b border-gray-800">
                            <tr>
                                <th class="px-6 py-4 font-medium">Timestamp</th>
                                <th class="px-6 py-4 font-medium">Admin</th>
                                <th class="px-6 py-4 font-medium">Action</th>
                                <th class="px-6 py-4 font-medium">Module</th>
                                <th class="px-6 py-4 font-medium">IP Address</th>
                                <th class="px-6 py-4 text-right font-medium">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="logs.data.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="material-symbols-outlined text-4xl opacity-50">search_off</span>
                                        <p>No activity logs found.</p>
                                    </div>
                                </td>
                            </tr>
                            <template v-for="log in logs.data" :key="log.id">
                                <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-gray-300 font-medium">{{ formatDate(log.created_at) }}</span>
                                            <span class="text-xs text-gray-500">{{ formatTime(log.created_at) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs">
                                                {{ log.user?.name?.charAt(0) || '?' }}
                                            </div>
                                            <span class="text-gray-300">{{ log.user?.name || 'Unknown User' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getActionColor(log.action)" class="px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wider uppercase border">
                                            {{ log.action }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-gray-300 font-mono text-xs bg-gray-800 px-2 py-1 rounded">
                                            {{ log.module }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-gray-500">
                                        {{ log.ip_address }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="toggleRow(log.id)" class="text-purple-400 hover:text-purple-300 transition-colors bg-purple-500/10 hover:bg-purple-500/20 px-3 py-1.5 rounded-lg flex items-center gap-1 ml-auto">
                                            <span class="material-symbols-outlined text-sm font-icon">
                                                {{ expandedRows.includes(log.id) ? 'expand_less' : 'expand_more' }}
                                            </span>
                                            <span class="text-xs font-medium">Payload</span>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Expandable Payload Row -->
                                <tr v-if="expandedRows.includes(log.id)" class="bg-[#0d1117] border-b border-gray-800">
                                    <td colspan="6" class="p-6">
                                        <div class="flex items-start gap-4">
                                            <div class="mt-1">
                                                <span class="material-symbols-outlined text-gray-600">data_object</span>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Request Payload</h4>
                                                <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 font-mono text-xs overflow-x-auto text-emerald-400 custom-scrollbar">
                                                    <pre v-if="Object.keys(log.payload || {}).length > 0">{{ JSON.stringify(log.payload, null, 2) }}</pre>
                                                    <p v-else class="text-gray-500 italic">No payload data submitted.</p>
                                                </div>
                                                
                                                <div class="mt-4 flex gap-6 text-xs text-gray-500">
                                                    <div>
                                                        <span class="font-medium text-gray-400">User Agent:</span>
                                                        <span class="ml-2 font-mono">{{ log.user_agent }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="logs.links && logs.data.length > 0" class="p-4 border-t border-gray-800 bg-gray-900/50 flex justify-center">
                    <div class="flex gap-1">
                        <template v-for="(link, index) in logs.links" :key="index">
                            <Link v-if="link.url" :href="link.url"
                                class="px-3 py-1 text-sm rounded-md transition-colors"
                                :class="link.active ? 'bg-purple-500 text-white font-medium shadow-lg shadow-purple-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white'"
                                v-html="link.label">
                            </Link>
                            <span v-else class="px-3 py-1 text-sm text-gray-600 cursor-not-allowed" v-html="link.label"></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'

const props = defineProps({
    logs: Object
})

const expandedRows = ref([])

const toggleRow = (id) => {
    const index = expandedRows.value.indexOf(id)
    if (index > -1) {
        expandedRows.value.splice(index, 1)
    } else {
        expandedRows.value.push(id)
    }
}

const getActionColor = (action) => {
    switch (action.toUpperCase()) {
        case 'POST': return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
        case 'PUT':
        case 'PATCH': return 'bg-amber-500/10 text-amber-400 border-amber-500/20'
        case 'DELETE': return 'bg-rose-500/10 text-rose-400 border-rose-500/20'
        default: return 'bg-gray-500/10 text-gray-400 border-gray-500/20'
    }
}

const formatDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

const formatTime = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

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
