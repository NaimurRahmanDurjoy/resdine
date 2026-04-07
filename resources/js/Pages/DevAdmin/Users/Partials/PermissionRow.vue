<template>
    <tr class="group hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors border-l-2" 
        :class="statusClasses.border">
        <td class="py-2.5 px-4">
            <div class="flex items-center gap-3" :class="{ 'pl-8': indent }">
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">{{ action.action }}</span>
                    <code class="text-[10px] text-gray-400 dark:text-gray-500 font-mono tracking-tighter">{{ action.route }}</code>
                </div>
                <!-- Status Badge -->
                <div v-if="currentOverride !== null" 
                    class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-tighter border animate-in fade-in slide-in-from-left-1"
                    :class="statusClasses.badge">
                    {{ currentOverride ? 'Explicit Allow' : 'Explicit Denied' }}
                </div>
            </div>
        </td>
        <td class="py-2.5 px-4 text-right">
            <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner">
                <!-- Inherit (Null) -->
                <button @click="toggle(null)" type="button"
                    class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all flex items-center gap-1.5"
                    :class="currentOverride === null ? 'bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm ring-1 ring-gray-200 dark:ring-gray-600' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'">
                    <span class="w-1.5 h-1.5 rounded-full" :class="isRoleAllowed ? 'bg-emerald-400' : 'bg-rose-400'"></span>
                    Inherit
                </button>
                
                <!-- Allow (True) -->
                <button @click="toggle(true)" type="button"
                    class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all"
                    :class="currentOverride === true ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20 ring-1 ring-emerald-600' : 'text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400'">
                    Allow
                </button>
                
                <!-- Deny (False) -->
                <button @click="toggle(false)" type="button"
                    class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all"
                    :class="currentOverride === false ? 'bg-rose-500 text-white shadow-md shadow-rose-500/20 ring-1 ring-rose-600' : 'text-gray-400 hover:text-rose-600 dark:hover:text-rose-400'">
                    Deny
                </button>
            </div>
        </td>
    </tr>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    action: Object,
    form: Object,
    rolePermissions: Array,
    indent: { type: Boolean, default: false }
})

// Check if this action is allowed for the user's role
const isRoleAllowed = computed(() => props.rolePermissions.includes(props.action.id))

// Find current override in form
const currentOverrideIndex = computed(() => props.form.overrides.findIndex(o => o.id === props.action.id))
const currentOverride = computed(() => {
    return currentOverrideIndex.value !== -1 ? props.form.overrides[currentOverrideIndex.value].is_allowed : null
})

const toggle = (val) => {
    if (currentOverrideIndex.value !== -1) {
        props.form.overrides[currentOverrideIndex.value].is_allowed = val
    } else {
        props.form.overrides.push({ id: props.action.id, is_allowed: val })
    }
}

// Styling based on state
const statusClasses = computed(() => {
    if (currentOverride.value === true) {
        return {
            border: 'border-emerald-500',
            badge: 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800'
        }
    }
    if (currentOverride.value === false) {
        return {
            border: 'border-rose-500',
            badge: 'bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-800'
        }
    }
    return {
        border: 'border-transparent',
        badge: ''
    }
})
</script>
