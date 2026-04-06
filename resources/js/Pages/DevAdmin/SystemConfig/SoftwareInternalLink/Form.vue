<template>
    <DevAdminLayout>

        <Head :title="isEdit ? 'Edit Menu Action' : 'Add Menu Action'" />

        <div class="max-w-4xl mx-auto">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div
                    class="bg-gradient-to-r from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 dark:text-white">
                                {{ isEdit ? 'Edit Menu Action' : 'Add Menu Action' }}</h1>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                                {{ isEdit ? 'Update the granular action for this menu' : 'Create a new internal link action' }}
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
                        <select v-model="form.software_menu_id" required
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

                    <!-- Resource Mode -->
                    <div
                        class="flex items-center justify-between gap-4 p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Route generation mode</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enable resource mode to auto-generate routes from the selected menu and action.</p>
                        </div>
                        <label class="inline-flex items-center cursor-pointer">
                            <span class="mr-3 text-sm font-medium text-gray-700 dark:text-gray-300">Resource</span>
                            <input type="checkbox" v-model="isResource" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 relative">
                            </div>
                        </label>
                    </div>

                    <!-- Action Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                            Action Name <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label v-for="actionType in actionTypes" :key="actionType"
                                class="relative flex items-center cursor-pointer">
                                <input type="radio" :value="actionType" v-model="form.action" required
                                    class="hidden peer">
                                <div
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 text-center font-semibold text-gray-700 dark:text-gray-300 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-all">
                                    {{ actionType.charAt(0).toUpperCase() + actionType.slice(1) }}
                                </div>
                            </label>
                        </div>
                        <div v-if="form.errors.action" class="mt-2 text-xs text-red-500">
                            {{ form.errors.action }}
                        </div>
                    </div>


                    <!-- Route -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                            Route Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" v-model="form.route" required :readonly="isResource"
                            :placeholder="routePlaceholder"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm font-mono"
                            :class="[{ 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed': isResource }, form.errors.route ? 'border-red-500 focus:ring-red-500' : '']">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ routeHint }}
                        </p>
                        <div v-if="form.errors.route" class="mt-1 text-xs text-red-500">
                            {{ form.errors.route }}
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="form.is_active" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 transition-all">
                            </div>
                            <span class="ml-3 text-sm font-bold text-gray-700 dark:text-gray-300">
                                {{ form.is_active ? 'Active' : 'Inactive' }}</span>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                        <Link :href="route('devAdmin.systemConfig.software.internal-link.index')"
                            class="px-5 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-700 active:scale-95 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50">
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

const props = defineProps({
    action: { type: Object, default: () => ({}) },
    menus: { type: Array, default: () => [] },
    isEdit: { type: Boolean, default: false }
})

const actionTypes = ['view', 'create', 'edit', 'delete']
const isResource = ref(true)

const form = useForm({
    software_menu_id: props.action?.software_menu_id ?? '',
    action: props.action?.action ?? '',
    route: props.action?.route ?? '',
    is_active: props.action?.is_active !== undefined ? Boolean(props.action.is_active) : true,
})

const selectedMenu = computed(() => {
    return props.menus.find(menu => String(menu.id) === String(form.software_menu_id)) || null
})

const routePlaceholder = computed(() => {
    if (isResource.value) {
        return selectedMenu.value
            ? `Automatically builds from ${selectedMenu.value.route} + .action`
            : 'Select a menu and action to generate the route'
    }
    return 'e.g. orders.index, orders.create, custom.route'
})

const routeHint = computed(() => {
    if (isResource.value) {
        return selectedMenu.value
            ? `Route will be generated as ${selectedMenu.value.route}.{action}`
            : 'Select a menu and action first to generate the route automatically.'
    }
    return 'Enter the full route name when resource mode is disabled.'
})

const updateRouteFromSelection = () => {
    if (!isResource.value || !selectedMenu.value || !form.action) {
        return
    }

    const mainRoute = selectedMenu.value.route || ''
    if (!mainRoute) {
        return
    }

    form.route = `${mainRoute}.${form.action}`
}

watch([() => form.software_menu_id, () => form.action, isResource], updateRouteFromSelection)

const submit = () => {
    if (props.isEdit) {
        form.put(route('devAdmin.systemConfig.software.internal-link.update', props.action.id))
    } else {
        form.post(route('devAdmin.systemConfig.software.internal-link.store'))
    }
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
