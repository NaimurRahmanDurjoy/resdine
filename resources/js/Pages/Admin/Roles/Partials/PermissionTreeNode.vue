<template>
    <div class="role-permission-node-wrapper" :class="{ 'mt-4': level === 0 }">
        <!-- Node Row -->
        <div 
            class="group flex items-center justify-between p-3 rounded-2xl transition-all duration-200 border border-transparent shadow-sm"
            :class="[
                level === 0 ? 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 shadow-indigo-500/5' : 'hover:bg-gray-50/80 dark:hover:bg-gray-900/30',
                isExpanded ? 'mb-2' : ''
            ]"
            @click="toggle"
        >
            <div class="flex items-center gap-4 flex-1 min-w-0">
                <!-- Indent -->
                <div v-for="i in level" :key="i" class="w-6 h-px bg-gray-200 dark:bg-gray-800 ml-2 first:ml-0"></div>

                <!-- Icons & Label -->
                <div class="flex items-center gap-3 min-w-0">
                    <span 
                        class="material-symbols-outlined text-gray-400 font-icon transition-transform duration-300"
                        :class="{ 'rotate-90 text-indigo-500': isExpanded }"
                    >chevron_right</span>
                    
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div 
                            class="w-9 h-9 rounded-xl flex items-center justify-center border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 group-hover:scale-110 transition-transform"
                            :class="level === 0 ? 'bg-indigo-50/50 dark:bg-indigo-900/10 text-indigo-600 dark:text-indigo-400' : ''"
                        >
                            <span class="material-symbols-outlined text-xl font-icon">{{ menu.icon || (level === 0 ? 'shield_person' : 'folder') }}</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span 
                                class="text-sm font-black truncate tracking-wider"
                                :class="level === 0 ? 'text-gray-800 dark:text-gray-100 uppercase' : 'text-gray-600 dark:text-gray-300'"
                            >
                                {{ menu.name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Area -->
            <div class="flex items-center gap-6" @click.stop>
                <!-- Grouped Actions -->
                <RoleActionGroup 
                    v-if="menu.grouped_actions && (menu.grouped_actions.view || menu.grouped_actions.create || menu.grouped_actions.edit || menu.grouped_actions.delete || menu.grouped_actions.others.length)"
                    :actions="menu.grouped_actions"
                    :allowed-action-ids="allowedActionIds"
                    @update="$emit('update', $event)"
                />

                <!-- Bulk Controls -->
                <div class="flex items-center bg-gray-100/50 dark:bg-gray-900/50 p-1 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="$emit('bulk-apply', { menu, val: true })" title="Allow All in this branch" class="p-1.5 hover:text-emerald-500 material-symbols-outlined text-sm font-icon">check_circle</button>
                    <button @click="$emit('bulk-apply', { menu, val: false })" title="Deny All in this branch" class="p-1.5 hover:text-rose-500 material-symbols-outlined text-sm font-icon">cancel</button>
                </div>
            </div>
        </div>

        <!-- Expansion Area -->
        <div v-show="isExpanded" class="animate-in slide-in-from-top-2 fade-in duration-300">
            <PermissionTreeNode 
                v-for="child in menu.children_recursive" 
                :key="child.id"
                :menu="child" 
                :allowed-action-ids="allowedActionIds"
                :expanded-menus="expandedMenus" 
                :search-query="searchQuery" 
                :level="level + 1" 
                @toggle="$emit('toggle', $event)"
                @bulk-apply="$emit('bulk-apply', $event)" 
                @update="$emit('update', $event)" 
            />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import RoleActionGroup from './RoleActionGroup.vue'
import PermissionTreeNode from './PermissionTreeNode.vue'

const props = defineProps({
    menu: Object,
    allowedActionIds: Array,
    expandedMenus: Object, // Set
    searchQuery: String,
    level: { type: Number, default: 0 }
})

const emit = defineEmits(['toggle', 'bulk-apply', 'update'])

const isExpanded = computed(() => props.expandedMenus.has(props.menu.id) || !!props.searchQuery)

const toggle = () => emit('toggle', props.menu.id)
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
