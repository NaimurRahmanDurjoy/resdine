<template>
    <tr class="group hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors border-l-2" 
        :class="statusClass">
        <td class="py-2.5 px-4">
            <div class="flex items-center gap-3" :class="{ 'pl-8': indent }">
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">{{ action.action }}</span>
                    <code class="text-[10px] text-gray-400 dark:text-gray-500 font-mono tracking-tighter">{{ action.route }}</code>
                </div>
                <!-- Status Badge -->
                <div class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-tighter border animate-in fade-in slide-in-from-left-1"
                    :class="badgeClass">
                    {{ badgeText }}
                </div>
            </div>
        </td>
        <td class="py-2.5 px-4 text-right">
             <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner">
                <!-- Inherit (Null) -->
                <button @click="update(null)" type="button"
                    class="px-3 py-1 rounded-lg text-[9px] font-bold uppercase tracking-widest transition-all"
                    :class="isInherited ? 'bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm ring-1 ring-gray-200' : 'text-gray-400 hover:text-gray-600'">
                    Inherit
                </button>

                <!-- Allow (True) -->
                <button @click="update(true)" type="button"
                    class="px-3 py-1 rounded-lg text-[9px] font-bold uppercase tracking-widest transition-all"
                    :class="state === true ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' : 'text-gray-400 hover:text-emerald-600'">
                    Allow
                </button>
                
                <!-- Deny (False) -->
                <button @click="update(false)" type="button"
                    class="px-3 py-1 rounded-lg text-[9px] font-bold uppercase tracking-widest transition-all"
                    :class="state === false ? 'bg-rose-500 text-white shadow-md shadow-rose-500/20' : 'text-gray-400 hover:text-rose-600'">
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
    overrides: Array,
    rolePermissions: Array,
    indent: { type: Boolean, default: false }
})

const emit = defineEmits(['update'])

const override = computed(() => props.overrides.find(o => o.action_id === props.action.id))
const state = computed(() => override.value ? override.value.is_allowed : null)
const isInherited = computed(() => state.value === null)
const roleAllows = computed(() => props.rolePermissions.includes(props.action.id))

const statusClass = computed(() => {
    if (state.value === true) return 'border-emerald-500 bg-emerald-50/5'
    if (state.value === false) return 'border-rose-500 bg-rose-50/5'
    return 'border-transparent'
})

const badgeText = computed(() => {
    if (state.value === true) return 'Explicitly Allowed'
    if (state.value === false) return 'Explicitly Denied'
    return roleAllows.value ? 'Inherited: Allow' : 'Inherited: Deny'
})

const badgeClass = computed(() => {
    if (state.value === true) return 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400'
    if (state.value === false) return 'bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-900/20 dark:text-rose-400'
    return roleAllows.value 
        ? 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/10 dark:text-blue-400'
        : 'bg-gray-50 text-gray-700 border-gray-100 dark:bg-gray-800 dark:text-gray-500'
})

const update = (val) => {
    emit('update', { action_id: props.action.id, is_allowed: val })
}
</script>
