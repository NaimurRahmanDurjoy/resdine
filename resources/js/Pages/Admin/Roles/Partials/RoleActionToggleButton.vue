<template>
    <button v-if="action" type="button" @click="$emit('update', { action_id: action.id, is_allowed: !isAllowed })"
        class="group relative flex items-center justify-center px-3 py-1.5 min-w-[70px] rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-200"
        :class="isAllowed ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' : 'bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 border border-gray-100 dark:border-gray-700'"
        :title="isAllowed ? 'Allowed' : 'Not Allowed'">
        <!-- Icon/Indicator -->
        <span class="mr-1.5 w-1.5 h-1.5 rounded-full"
            :class="isAllowed ? 'bg-white' : (roleAllowed ? 'bg-emerald-400' : 'bg-rose-400')"></span>

        <!-- Label -->
        <span :class="isAllowed ? 'text-white' : 'text-gray-500 dark:text-gray-400'">{{ label }}</span>
    </button>
    <div v-else
        class="w-[70px] h-8 bg-gray-50/50 dark:bg-gray-800/10 rounded-lg border border-dashed border-gray-100 dark:border-gray-800 flex items-center justify-center">
        <span class="text-[8px] text-gray-300 dark:text-gray-700 uppercase font-black tracking-tighter">N/A</span>
    </div>
</template>

<script setup>
const props = defineProps({
    action: Object,
    label: String,
    isAllowed: Boolean
})

defineEmits(['update'])
</script>
