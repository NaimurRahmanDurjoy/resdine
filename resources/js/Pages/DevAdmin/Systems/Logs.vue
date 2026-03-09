<template>
    <DevAdminLayout>
        <div
            class="bg-gray-900 rounded-xl shadow-2xl border border-gray-800 overflow-hidden flex flex-col h-[calc(100vh-12rem)]">
            <!-- Terminal Header -->
            <div class="bg-gray-800 px-4 py-2 flex items-center justify-between border-b border-gray-700">
                <div class="flex items-center gap-2">
                    <div class="flex gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    </div>
                    <span
                        class="ml-4 text-[10px] font-mono text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm font-icon">terminal</span>
                        System Output: laravel.log
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <div v-if="isRefreshing"
                        class="flex items-center gap-2 text-[10px] text-emerald-400 font-mono italic">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                        Syncing...
                    </div>
                    <button @click="refreshLogs"
                        class="text-gray-400 hover:text-white transition-colors p-1 rounded-md hover:bg-gray-700">
                        <span class="material-symbols-outlined text-lg font-icon"
                            :class="{ 'animate-spin': isRefreshing }">refresh</span>
                    </button>
                </div>
            </div>

            <!-- Terminal Content -->
            <div class="flex-1 overflow-auto p-6 font-mono text-sm custom-scrollbar bg-[#0d1117]" ref="terminalBody">
                <div class="space-y-1">
                    <div v-for="(line, index) in parsedLogs" :key="index" class="group flex gap-4">
                        <span class="text-gray-600 text-right min-w-[3rem] select-none text-xs">{{ index + 1 }}</span>
                        <p class="break-all whitespace-pre-wrap flex-1" :class="getLogColor(line)">
                            {{ line }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer / Status Bar -->
            <div
                class="bg-gray-800 px-4 py-1.5 flex justify-between items-center text-[10px] text-gray-500 font-mono border-t border-gray-700">
                <div class="flex gap-4">
                    <span>ENCODING: UTF-8</span>
                    <span>ROWS: {{ parsedLogs.length }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">visibility</span>
                    READ ONLY
                </div>
            </div>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'

const props = defineProps({
    logs: String
})

const terminalBody = ref(null)
const isRefreshing = ref(false)

const parsedLogs = computed(() => {
    return props.logs.split('\n').filter(line => line.trim())
})

const getLogColor = (line) => {
    const lower = line.toLowerCase()
    if (lower.includes('.error') || lower.includes('critical') || lower.includes('stack trace:')) return 'text-rose-400'
    if (lower.includes('.warning')) return 'text-amber-300'
    if (lower.includes('.info')) return 'text-blue-400'
    if (lower.includes('.debug')) return 'text-gray-500'
    return 'text-gray-300'
}

const refreshLogs = () => {
    isRefreshing.value = true
    router.reload({
        only: ['logs'],
        onFinish: () => {
            isRefreshing.value = false
            scrollToBottom()
        }
    })
}

const scrollToBottom = () => {
    nextTick(() => {
        if (terminalBody.value) {
            terminalBody.value.scrollTop = terminalBody.value.scrollHeight
        }
    })
}

onMounted(() => {
    scrollToBottom()
})
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
