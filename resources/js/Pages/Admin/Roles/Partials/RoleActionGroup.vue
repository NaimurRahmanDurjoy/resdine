<template>
    <div class="flex items-center gap-4 py-1">
        <!-- Standard Actions Group -->
        <div class="flex bg-gray-50 dark:bg-gray-900/50 p-1 rounded-xl border border-gray-100 dark:border-gray-800 shadow-inner overflow-hidden">
            <div v-for="(type, index) in standardTypes" :key="type" class="flex items-center">
                <div v-if="index > 0" class="w-px h-4 bg-gray-200 dark:bg-gray-700 mx-1"></div>
                <RoleActionToggleButton 
                    :action="actions[type]" 
                    :label="type"
                    :is-allowed="isAllowed(actions[type]?.id)"
                    @update="$emit('update', $event)"
                />
            </div>
        </div>

        <!-- Others / Special Actions -->
        <div v-if="actions.others?.length" class="flex flex-wrap gap-2">
            <RoleActionToggleButton 
                v-for="action in actions.others" 
                :key="action.id"
                :action="action" 
                :label="action.action"
                :is-allowed="isAllowed(action.id)"
                @update="$emit('update', $event)"
            />
        </div>
    </div>
</template>

<script setup>
import RoleActionToggleButton from './RoleActionToggleButton.vue'

const props = defineProps({
    actions: Object,
    allowedActionIds: Array
})

defineEmits(['update'])

const standardTypes = ['view', 'create', 'edit', 'delete']

const isAllowed = (actionId) => {
    if (!actionId) return false
    return props.allowedActionIds.map(id => Number(id)).includes(Number(actionId))
}
</script>
