<template>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden sticky top-6">
        <div class="px-6 py-5 bg-gradient-to-br from-blue-50/50 to-white dark:from-blue-900/10 dark:to-gray-800 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Permission Summary</h3>
        </div>
        
        <div class="p-6 space-y-6">
            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-emerald-50/50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100 dark:border-emerald-900/20">
                    <div class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1">Allowed</div>
                    <div class="text-2xl font-black text-emerald-700 dark:text-emerald-300">{{ stats.allowed }}</div>
                </div>
                <div class="p-4 bg-rose-50/50 dark:bg-rose-900/10 rounded-2xl border border-rose-100 dark:border-rose-900/20">
                    <div class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-widest mb-1">Restricted</div>
                    <div class="text-2xl font-black text-rose-700 dark:text-rose-300">{{ stats.restricted }}</div>
                </div>
            </div>

            <!-- Overrides Info -->
            <div class="p-5 bg-blue-50/30 dark:bg-blue-900/5 rounded-2xl border border-blue-100/50 dark:border-blue-900/10">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest">Active Overrides</span>
                    <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-[10px] font-black rounded-lg">
                        {{ stats.overrides }}
                    </span>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/20"></div>
                        <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">Explicitly Granted: {{ stats.explicitAllow }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-rose-500 shadow-sm shadow-rose-500/20"></div>
                        <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">Explicitly Denied: {{ stats.explicitDeny }}</span>
                    </div>
                </div>
            </div>

            <!-- Sync Info -->
            <div class="flex items-center gap-3 px-2 py-1">
                <div class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-gray-400 text-xl font-icon">sync</span>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">Live Syncing</div>
                    <div class="text-[10px] text-gray-400">Permissions are updated in real-time</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            allowed: 0,
            restricted: 0,
            overrides: 0,
            explicitAllow: 0,
            explicitDeny: 0
        })
    }
})
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
