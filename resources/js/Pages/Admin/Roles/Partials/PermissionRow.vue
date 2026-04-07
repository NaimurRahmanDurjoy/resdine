<template>
    <tr class="group hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors border-l-2" 
        :class="isAllowed ? 'border-emerald-500 bg-emerald-50/5' : 'border-transparent'">
        <td class="py-2.5 px-4">
            <div class="flex items-center gap-3" :class="{ 'pl-8': indent }">
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">{{ action.action }}</span>
                    <code class="text-[10px] text-gray-400 dark:text-gray-500 font-mono tracking-tighter">{{ action.route }}</code>
                </div>
                <!-- Status Badge -->
                <div v-if="isAllowed" 
                    class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-tighter border bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800 animate-in fade-in slide-in-from-left-1">
                    Allowed
                </div>
            </div>
        </td>
        <td class="py-2.5 px-4 text-right">
             <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner">
                <!-- Allow -->
                <button @click="toggle(true)" type="button"
                    class="px-4 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all flex items-center gap-1.5"
                    :class="isAllowed ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20 ring-1 ring-emerald-600' : 'text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400'">
                    Allow
                </button>
                
                <!-- Deny (Remove from array) -->
                <button @click="toggle(false)" type="button"
                    class="px-4 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all"
                    :class="!isAllowed ? 'bg-gray-400 text-white shadow-md shadow-gray-500/20 ring-1 ring-gray-500' : 'text-gray-400 hover:text-rose-600 dark:hover:text-rose-400'">
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
    indent: { type: Boolean, default: false }
})

const isAllowed = computed(() => props.form.allowed_action_ids.includes(props.action.id))

const toggle = (shouldAllow) => {
    if (shouldAllow) {
        if (!isAllowed.value) {
            props.form.allowed_action_ids.push(props.action.id)
        }
    } else {
        const index = props.form.allowed_action_ids.indexOf(props.action.id)
        if (index > -1) {
            props.form.allowed_action_ids.splice(index, 1)
        }
    }
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
