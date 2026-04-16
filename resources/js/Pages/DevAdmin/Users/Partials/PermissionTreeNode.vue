<template>
    <!-- Current Menu Row -->
        <tr :class="[
            level === 0 ? 'bg-gray-50/50 dark:bg-gray-900/20' : 'bg-gray-50/20 dark:bg-gray-900/10',
            level > 0 ? 'border-l-4 border-gray-100 dark:border-gray-800' : ''
        ]" class="group">
            <td class="py-3 px-2" :style="{ paddingLeft: `${level * 1.5 + 0.5}rem` }">
                <div class="flex items-center space-x-3">
                    <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-xl font-icon">
                        {{ menu.icon || (level === 0 ? 'folder' : 'subdirectory_arrow_right') }}
                    </span>
                    <span class="text-sm font-black text-gray-800 dark:text-gray-200 uppercase tracking-wider" :class="{'text-xs font-bold': level > 0}">{{ menu.name }}</span>
                </div>
            </td>
            <td class="py-1 px-4 text-right">
                <!-- Menu Level Bulk Toggle -->
                <div class="inline-flex gap-1 items-center bg-white dark:bg-gray-800 p-1 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm opacity-0 group-hover:opacity-100 focus-within:opacity-100 transition-opacity">
                    <span class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter mr-1 pl-1">Menu Bulk:</span>
                    <button @click="bulkApply(null)" type="button" title="Inherit Menu" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-gray-400 material-symbols-outlined text-sm">settings_backup_restore</button>
                    <button @click="bulkApply(true)" type="button" title="Allow Menu" class="p-1 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded text-emerald-500 material-symbols-outlined text-sm">check_circle</button>
                    <button @click="bulkApply(false)" type="button" title="Deny Menu" class="p-1 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded text-rose-500 material-symbols-outlined text-sm">cancel</button>
                </div>
            </td>
        </tr>

        <!-- Actions for this Menu -->
        <template v-for="action in menu.actions" :key="action.id">
            <PermissionRow :action="action" :form="form" :role-permissions="rolePermissions" :indent="level > 0" />
        </template>

        <!-- Recursive Children -->
        <template v-for="child in menu.children_recursive" :key="child.id">
            <PermissionTreeNode 
                :menu="child" 
                :form="form" 
                :role-permissions="rolePermissions" 
                :level="level + 1"
                @bulk-apply="emitBulkApply"
            />
        </template>
</template>

<script setup>
import PermissionRow from './PermissionRow.vue'
import PermissionTreeNode from './PermissionTreeNode.vue'

const props = defineProps({
    menu: Object,
    form: Object,
    rolePermissions: Array,
    level: { type: Number, default: 0 }
})

const emit = defineEmits(['bulk-apply'])

const bulkApply = (val) => {
    emit('bulk-apply', { menu: props.menu, val })
}

const emitBulkApply = (payload) => {
    emit('bulk-apply', payload)
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
