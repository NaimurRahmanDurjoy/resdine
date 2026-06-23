<template>
    <div class="flex items-center gap-4 py-1">
        <!-- Standard Actions Group -->
        <div class="flex bg-gray-50 dark:bg-gray-900/50 p-1 rounded-xl border border-gray-100 dark:border-gray-800 shadow-inner overflow-hidden">
            <div v-for="(type, index) in standardTypes" :key="type" class="flex items-center">
                <div v-if="index > 0" class="w-px h-4 bg-gray-200 dark:bg-gray-700 mx-1"></div>
                <ActionToggleButton 
                    :action="actions[type]" 
                    :label="type"
                    :override="getOverride(actions[type]?.id)"
                    :role-allowed="isRoleAllowed(actions[type]?.id)"
                    @update="$emit('update', $event)"
                />
            </div>
        </div>

        <!-- Others / Special Actions -->
        <div v-if="actions.others?.length" class="flex flex-wrap gap-2">
            <ActionToggleButton 
                v-for="action in actions.others" 
                :key="action.id"
                :action="action" 
                :label="action.action"
                :override="getOverride(action.id)"
                :role-allowed="isRoleAllowed(action.id)"
                @update="$emit('update', $event)"
            />
        </div>
    </div>
</template>

<script setup>
import ActionToggleButton from './ActionToggleButton.vue'

const props = defineProps({
    actions: Object, // {view, create, edit, delete, others: []}
    overrides: Array,
    rolePermissions: Array
})

defineEmits(['update'])

const standardTypes = ['view', 'create', 'edit', 'delete']

const getOverride = (actionId) => {
    if (!actionId) return null
    const o = props.overrides.find(o => o.action_id === actionId)
    return o ? o.is_allowed : null
}

const isRoleAllowed = (actionId) => {
    if (!actionId) return false
    return props.rolePermissions.includes(actionId)
}
</script>
