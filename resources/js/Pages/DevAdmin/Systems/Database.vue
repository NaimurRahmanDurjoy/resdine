<template>
    <DevAdminLayout>
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-emerald-50 to-white dark:from-emerald-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Database Infrastructure</h1>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Resource allocation and table weight
                            analysis</p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Selected Schema</div>
                        <div class="text-sm font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ dbName }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="p-6 bg-gray-50/50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <div class="flex justify-between mb-1">
                            <span class="text-xs font-bold text-gray-500 uppercase">Total Footprint</span>
                            <span class="text-xs font-bold text-emerald-600">{{ totalSize }} MB</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-1000"
                                :style="{ width: '100%' }"></div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <div
                            class="px-4 py-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm">
                            <div class="text-[10px] text-gray-400 uppercase font-black">Tables</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ tables.length }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tables Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="table in tables" :key="table.name"
                        class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-emerald-200 dark:hover:border-emerald-800 transition-all group">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-2">
                                <span
                                    class="material-symbols-outlined text-gray-400 text-lg group-hover:text-emerald-500 transition-colors">table</span>
                                <span
                                    class="text-sm font-bold text-gray-700 dark:text-gray-200 truncate max-w-[150px]">{{
                                    table.name }}</span>
                            </div>
                            <span
                                class="text-[10px] font-mono bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-500 dark:text-gray-400">
                                {{ table.size_mb }}MB
                            </span>
                        </div>

                        <!-- Mini indicator -->
                        <div class="w-full bg-gray-100 dark:bg-gray-700 h-1 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full"
                                :style="{ width: Math.min((table.size_mb / totalSize) * 100 * 5, 100) + '%' }"></div>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="text-[10px] text-gray-400 uppercase tracking-tighter">Weight
                                Distribution</span>
                            <span class="text-[10px] font-bold text-gray-500">{{ ((table.size_mb / totalSize) *
                                100).toFixed(1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'

const props = defineProps({
    tables: Array,
    totalSize: [Number, String],
    dbName: String
})
</script>
