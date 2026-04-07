<template>
    <DevAdminLayout>

        <Head :title="isEdit ? 'Edit Menu Action' : 'Add Menu Action'" />

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Header Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div
                    class="bg-gradient-to-r from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 dark:text-white">
                                {{ isEdit ? 'Edit Menu Action' : 'Add Menu Action' }}</h1>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                                {{ isEdit ? 'Update the granular action for this menu' 
                                : 'Create a new internal linkaction' }}
                            </p>
                        </div>
                        <Link :href="route('devAdmin.systemConfig.software.internal-link.index')"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                            <span class="material-symbols-outlined font-icon">close</span>
                        </Link>
                    </div>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Menu Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                            Software Menu <span class="text-red-500">*</span>
                        </label>
                        <select v-model="form.software_menu_id" required @change="onMenuChange"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm"
                            :class="{ 'border-red-500 focus:ring-red-500': form.errors.software_menu_id }">
                            <option value="">-- Select a menu --</option>
                            <option v-for="menu in menus" :key="menu.id" :value="menu.id">
                                {{ menu.name }}
                            </option>
                        </select>
                        <div v-if="form.errors.software_menu_id" class="mt-1 text-xs text-red-500">
                            {{ form.errors.software_menu_id }}
                        </div>
                    </div>

                    <!-- Resource Mode Toggle -->
                    <div
                        class="flex items-center justify-between gap-4 p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Route generation mode
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enable resource mode to
                                auto-generate routes from the selected menu and action(s).</p>
                        </div>
                        <label class="inline-flex items-center cursor-pointer">
                            <span class="mr-3 text-sm font-medium text-gray-700 dark:text-gray-300">Resource</span>
                            <input type="checkbox" v-model="isResource" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 relative">
                            </div>
                        </label>
                    </div>

                    <template v-if="isResource">
                        <!-- Resource Mode: Actions Multi-select -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">
                                Select Actions <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                                Select one or more actions. Each will create a separate internal link.
                            </p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label v-for="action in validActions" :key="action"
                                    class="relative flex items-center cursor-pointer">
                                    <input type="checkbox" :value="action" v-model="selectedActions"
                                        class="hidden peer">
                                    <div
                                        class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 text-center font-semibold text-gray-700 dark:text-gray-300 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-all hover:border-blue-300 dark:hover:border-blue-500">
                                        {{ formatActionName(action) }}
                                    </div>
                                </label>
                            </div>
                            <div v-if="selectedActions.length === 0" class="mt-2 text-xs text-amber-500">
                                Please select at least one action
                            </div>
                        </div>

                        <!-- Generated Routes Preview -->
                        <div v-if="selectedActions.length > 0"
                            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                <span
                                    class="material-symbols-outlined inline-block text-base align-middle mr-2">visibility</span>
                                Generated Routes
                            </h3>
                            <div class="space-y-2">
                                <div v-for="(route, action) in generatedRoutes" :key="action"
                                    class="flex items-center justify-between bg-white dark:bg-gray-800 rounded px-3 py-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">
                                        <span class="font-mono font-semibold text-blue-600 dark:text-blue-400">{{ action
                                            }}</span>
                                        →
                                    </span>
                                    <code
                                        class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-gray-800 dark:text-gray-200 font-mono">
                                        {{ route }}
                                    </code>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <!-- Manual Mode: Action Name Input -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Action Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" v-model="form.action" required
                                placeholder="e.g., customAction, export, approve"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm"
                                :class="{ 'border-red-500 focus:ring-red-500': form.errors.action }">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Use lowercase with no spaces (e.g., customAction)
                            </p>
                            <div v-if="form.errors.action" class="mt-1 text-xs text-red-500">
                                {{ form.errors.action }}
                            </div>
                        </div>
                    </template>

                    <!-- Route Name Input -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                            Route Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" v-model="form.route" required
                            :readonly="isResource && selectedActions.length > 0" :placeholder="routePlaceholder"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm font-mono"
                            :class="[{ 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed': isResource && selectedActions.length > 0 }, form.errors.route ? 'border-red-500 focus:ring-red-500' : '']">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ routeHint }}
                        </p>
                        <div v-if="form.errors.route" class="mt-1 text-xs text-red-500">
                            {{ form.errors.route }}
                        </div>
                    </div>

                    <!-- Active Status Toggle -->
                    <div
                        class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Status
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="form.is_active" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 transition-all">
                            </div>
                            <span class="ml-3 text-sm font-bold text-gray-700 dark:text-gray-300">
                                {{ form.is_active ? 'Active' : 'Inactive' }}</span>
                        </label>
                    </div>

                    <!-- Form Actions -->
                    <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                        <Link :href="route('devAdmin.systemConfig.software.internal-link.index')"
                            class="px-6 py-2 text-sm font-bold text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing || !canSubmit"
                            class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-700 active:scale-95 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ isEdit ? 'Update Action' : 'Save Action' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'
import { useRouteGeneration } from '@/composables/useRouteGeneration'

const props = defineProps({
    action: { type: Object, default: () => ({}) },
    menus: { type: Array, default: () => [] },
    isEdit: { type: Boolean, default: false }
})

const { generateRouteForAction, generateRoutesForActions, extractBaseRoute } = useRouteGeneration()

const validActions = ['view', 'create', 'edit', 'delete']
const isResource = ref(true)
const selectedActions = ref([])

const form = useForm({
    software_menu_id: props.action?.software_menu_id ?? '',
    action: props.action?.action ?? '',
    route: props.action?.route ?? '',
    is_active: props.action?.is_active !== undefined ? Boolean(props.action.is_active) : true,
})

const selectedMenu = computed(() => {
    return props.menus.find(menu => String(menu.id) === String(form.software_menu_id)) || null
})

const baseRoute = computed(() => {
    const menuRoute = selectedMenu.value?.route || ''
    // Extract just the base route, removing any trailing action
    return extractBaseRoute(menuRoute)
})

const routePlaceholder = computed(() => {
    if (isResource.value) {
        return selectedActions.value.length > 0
            ? `Auto-generated from selected actions`
            : 'Select a menu and one or more actions to generate routes'
    }
    return 'e.g., orders.index, orders.create, custom.route'
})

const routeHint = computed(() => {
    if (isResource.value) {
        return selectedActions.value.length > 0
            ? `Routes will be auto-generated based on selected actions`
            : 'Select a menu and one or more actions first.'
    }
    return 'Enter the full route name when resource mode is disabled.'
})

const generatedRoutes = computed(() => {
    if (!isResource.value || !baseRoute.value) return {}
    return generateRoutesForActions(baseRoute.value, selectedActions.value)
})

/**
 * Check if form can be submitted
 * In resource mode: need menu + at least one action
 * In manual mode: need menu + action + route
 */
const canSubmit = computed(() => {
    if (!form.software_menu_id) return false

    if (isResource.value) {
        return selectedActions.value.length > 0
    }

    return form.action && form.route
})

/**
 * Format action name for display (index -> Index, create -> Create, etc.)
 */
const formatActionName = (action) => {
    const names = {
        view: 'View / Index',
        create: 'Create / Store',
        edit: 'Edit / Update',
        delete: 'Delete / Destroy'
    }
    return names[action] || (action.charAt(0).toUpperCase() + action.slice(1))
}

/**
 * Handle menu change - reset actions and route
 */
const onMenuChange = () => {
    selectedActions.value = []
    if (isResource.value) {
        form.route = ''
    }
}

/**
 * Watch for changes in selected actions to update route
 */
watch(selectedActions, (newActions) => {
    if (!isResource.value) return

    if (newActions.length === 0) {
        form.route = ''
        return
    }

    // For single action, set the specific route
    if (newActions.length === 1) {
        form.route = generateRouteForAction(baseRoute.value, newActions[0])
    } else {
        // For multiple actions, set to base route (backend will handle creation of multiple records)
        form.route = baseRoute.value
    }
}, { deep: true })

/**
 * Handle form submission
 */
const submit = () => {
    // In resource mode, actions contain multiple selections
    // Backend will create separate records for each action
    if (isResource.value && selectedActions.value.length > 0) {
        form.action = selectedActions.value
    }

    if (props.isEdit) {
        form.put(route('devAdmin.systemConfig.software.internal-link.update', props.action.id))
    } else {
        form.post(route('devAdmin.systemConfig.software.internal-link.store'))
    }
}

// Initialize selectedActions if in edit mode
if (props.isEdit && props.action?.action) {
    selectedActions.value = Array.isArray(props.action.action)
        ? props.action.action
        : [props.action.action]
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
