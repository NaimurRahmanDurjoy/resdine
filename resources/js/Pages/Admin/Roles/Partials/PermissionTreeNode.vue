<template>
    <!-- Parent Menu Row -->
    <tr :class="[
            level === 0 ? 'bg-gray-50/50 dark:bg-gray-900/20' : 'bg-gray-50/20 dark:bg-gray-900/10',
            level > 0 ? 'border-l-4 border-gray-100 dark:border-gray-800' : ''
        ]" class="group cursor-pointer" @click="emitToggle(menu.id)">
        <td class="py-3 px-2" :style="{ paddingLeft: `${level * 1.5 + 0.5}rem` }">
            <div class="flex items-center space-x-3">
                <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-lg font-icon transition-transform" 
                    :class="{ 'rotate-90 text-blue-500': isExpanded(menu.id) }">chevron_right</span>
                <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-xl font-icon">{{ menu.icon || (level === 0 ? 'folder' : 'subdirectory_arrow_right') }}</span>
                <span class="text-sm font-black text-gray-800 dark:text-gray-200 uppercase tracking-wider" :class="{'text-xs font-bold': level > 0}">{{ menu.name }}</span>
            </div>
        </td>
        <td class="py-1 px-4 text-right">
            <div class="inline-flex gap-1 items-center bg-white dark:bg-gray-800 p-1 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm opacity-0 group-hover:opacity-100 focus-within:opacity-100 transition-opacity" @click.stop>
                <span class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter mr-1 pl-1 whitespace-nowrap">Batch Actions:</span>
                <button @click="emitBulkApply(menu, true)" type="button" title="Grant All under this Menu" class="p-1 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded text-emerald-500 material-symbols-outlined text-sm">check_circle</button>
                <button @click="emitBulkApply(menu, false)" type="button" title="Revoke All under this Menu" class="p-1 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded text-rose-500 material-symbols-outlined text-sm">cancel</button>
            </div>
        </td>
    </tr>

    <!-- Actions & Children -->
    <template v-if="isExpanded(menu.id)">
        <template v-for="action in menu.actions" :key="action.id">
            <PermissionRow 
                :action="action" 
                :form="form" 
                :indent="level > 0"
            />
        </template>
        <template v-for="child in menu.children_recursive" :key="child.id">
            <PermissionTreeNode 
                :menu="child" 
                :form="form" 
                :expandedMenus="expandedMenus"
                :searchQuery="searchQuery"
                :level="level + 1"
                @toggle="emitToggle"
                @bulk-apply="emitBulkApply"
            />
        </template>
    </template>
</template>

<script setup>
import PermissionRow from './PermissionRow.vue'
import PermissionTreeNode from './PermissionTreeNode.vue'

const props = defineProps({
    menu: Object,
    form: Object,
    expandedMenus: Object, // Set
    searchQuery: String,
    level: { type: Number, default: 0 }
})

const emit = defineEmits(['toggle', 'bulk-apply'])

const isExpanded = (id) => props.expandedMenus.has(id) || !!props.searchQuery

const emitToggle = (id) => emit('toggle', id)
const emitBulkApply = (menu, val) => emit('bulk-apply', menu, val)
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
