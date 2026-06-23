<template>
    <button 
        v-if="action"
        type="button"
        @click="handleClick"
        class="group relative flex items-center justify-center px-3 py-1.5 min-w-[70px] rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-200"
        :class="bgClass"
        :title="titleText"
    >
        <!-- Icon/Indicator -->
        <span class="mr-1.5 w-1.5 h-1.5 rounded-full" :class="indicatorClass"></span>
        
        <!-- Label -->
        <span :class="textClass">{{ label }}</span>

        <!-- Override Indicator (Dot) -->
        <span v-if="override !== null" class="absolute -top-1 -right-1 w-2 h-2 bg-blue-500 border-2 border-white dark:border-gray-800 rounded-full shadow-sm animate-bounce"></span>
    </button>
    <div v-else class="w-[70px] h-8 bg-gray-50/50 dark:bg-gray-800/10 rounded-lg border border-dashed border-gray-100 dark:border-gray-800 flex items-center justify-center">
        <span class="text-[8px] text-gray-300 dark:text-gray-700 uppercase font-black tracking-tighter">N/A</span>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    action: Object,
    label: String,
    override: [Boolean, null], // true, false, null (inherit)
    roleAllowed: Boolean
})

const emit = defineEmits(['update'])

const isEffectiveAllowed = computed(() => {
    if (props.override !== null) return props.override
    return props.roleAllowed
})

const bgClass = computed(() => {
    if (props.override === true) return 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20'
    if (props.override === false) return 'bg-rose-500 text-white shadow-md shadow-rose-500/20'
    
    // Inherit State
    return 'bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 border border-gray-100 dark:border-gray-700'
})

const textClass = computed(() => {
    if (props.override !== null) return 'text-white'
    return 'text-gray-500 dark:text-gray-400'
})

const indicatorClass = computed(() => {
    if (props.override !== null) {
        return props.override ? 'bg-white' : 'bg-white'
    }
    return props.roleAllowed ? 'bg-emerald-400' : 'bg-rose-400'
})

const titleText = computed(() => {
    if (props.override === true) return 'Explicitly Allowed'
    if (props.override === false) return 'Explicitly Denied'
    return props.roleAllowed ? 'Inherited from Role: Allow' : 'Inherited from Role: Deny'
})

const handleClick = () => {
    // Cycle: Inherit -> Allow -> Deny -> Inherit
    let nextState;
    if (props.override === null) nextState = true
    else if (props.override === true) nextState = false
    else nextState = null

    emit('update', { action_id: props.action.id, is_allowed: nextState })
}
</script>
