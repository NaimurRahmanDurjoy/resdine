<template>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden sticky top-6">
        <div class="px-6 py-5 bg-gradient-to-br from-indigo-50/50 to-white dark:from-indigo-900/10 dark:to-gray-800 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Role Access Overview</h3>
        </div>
        
        <div class="p-6 space-y-6">
            <!-- Stats -->
            <div class="space-y-3">
                <div class="p-5 bg-emerald-50/50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100 dark:border-emerald-900/20">
                    <div class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1 text-center">Total Allowed Actions</div>
                    <div class="text-4xl font-black text-emerald-700 dark:text-emerald-300 text-center">{{ allowedCount }}</div>
                </div>
                
                <div class="p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 text-center">Restricted Actions</div>
                    <div class="text-xl font-black text-gray-500 dark:text-gray-400 text-center">{{ restrictedCount }}</div>
                </div>
            </div>

            <!-- Role Context -->
            <div class="px-2">
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Role Capabilities</div>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-blue-500 text-lg font-icon">clinical_notes</span>
                        </div>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed italic">Changes to this role will instantly apply to all assigned users.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    allowedCount: Number,
    restrictedCount: Number
})
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
