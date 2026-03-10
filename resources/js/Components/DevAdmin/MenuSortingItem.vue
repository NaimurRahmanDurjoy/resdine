<template>
    <div class="menu-item">
        <!-- Menu Item Display -->
        <div class="bg-white dark:bg-gray-800 p-2 rounded-lg border shadow-sm flex justify-between items-center hover:shadow-md transition-shadow"
            :style="{ paddingLeft: `${level * 20}px` }"
            :class="color === 'indigo' ? 'border-indigo-200 dark:border-indigo-800' : 'border-emerald-200 dark:border-emerald-800'">
            <div class="flex items-center space-x-3 flex-1">
                <button
                    @click.prevent="isDragging = !isDragging"
                    class="drag-handle cursor-grab active:cursor-grabbing p-1 transition"
                    :class="color === 'indigo' ? 'text-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-300' : 'text-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-300'">
                    <!-- <span class="material-symbols-outlined text-lg">drag_handle</span> -->
                    <span v-if="menu.icon" class="material-symbols-outlined" :class="color === 'indigo' ? 'text-indigo-500' : 'text-emerald-500'">{{ menu.icon }}</span>
                </button>
                <span class="font-medium text-gray-800 dark:text-gray-200">{{ menu.name }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <span :class="['px-2 py-1 text-xs rounded', color === 'indigo' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300']">
                    Order: {{ menu.order }}
                </span>
                <span :class="['px-2 py-1 text-xs rounded', menu.is_active ? (color === 'indigo' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300') : 'bg-rose-100 dark:bg-rose-900/30 text-rose-800 dark:text-rose-300']">
                    {{ menu.is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <!-- Nested Children Draggable -->
        <draggable
            v-if="localMenu.children && localMenu.children.length > 0"
            v-model="localMenu.children"
            :options="dragOptions"
            class="space-y-2 mt-2"
            @change="emitUpdate"
            tag="div">
            <template #item="{ element }">
                <MenuSortingItem
                    :menu="element"
                    :level="level + 1"
                    :color="color"
                    :route-prefix="routePrefix"
                    @update:menu="emitUpdate"
                />
            </template>
        </draggable>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import draggable from 'vuedraggable'

const props = defineProps({
    menu: { type: Object, required: true },
    level: { type: Number, default: 0 },
    color: { type: String, default: 'indigo' },
    routePrefix: { type: String, default: 'devAdmin.systemConfig.adminPanel.menu' }
})

const emit = defineEmits(['update:menu'])

const localMenu = ref({ ...props.menu, children: props.menu.children || props.menu.children_recursive || [] })
const isDragging = ref(false)

const dragOptions = {
    animation: 200,
    group: 'menus',
    ghostClass: 'bg-indigo-50 dark:bg-indigo-900/50',
    handle: '.drag-handle',
    fallbackOnBody: true,
    swapThreshold: 0.65,
    invertSwap: true
}

const emitUpdate = () => {
    emit('update:menu', localMenu.value)
}
</script>

<style scoped>
.menu-item {
    transition: all 0.2s ease;
}

.menu-item:hover {
    transform: translateX(4px);
}

.drag-handle {
    cursor: grab;
}

.drag-handle:active {
    cursor: grabbing;
}
</style>
