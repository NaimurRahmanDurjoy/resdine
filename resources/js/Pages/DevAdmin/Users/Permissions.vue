<template>
    <DevAdminLayout>

        <Head :title="`Permissions for ${user.name}`" />

        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Header Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div
                    class="bg-gradient-to-r from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <Link :href="route('devAdmin.users.index')"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                                <span class="material-symbols-outlined font-icon">arrow_back</span>
                            </Link>
                            <div>
                                <h1 class="text-xl font-bold text-gray-800 dark:text-white">User Permissions</h1>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Managing access for</span>
                                    <span
                                        class="text-xs font-bold text-blue-600 dark:text-blue-400 underline decoration-blue-500/30">{{
                                        user.name }}</span>
                                    <span
                                        class="px-2 py-0.25 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 text-[9px] font-bold rounded border border-indigo-100 dark:border-indigo-800 uppercase tracking-tighter">
                                        {{ user.role?.name || 'No Role' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <button @click="resetToDefault" type="button"
                                class="text-xs font-semibold text-gray-500 hover:text-rose-600 transition-colors uppercase tracking-widest px-3 py-2">
                                Reset All Overrides
                            </button>
                            <button @click="savePermissions" :disabled="form.processing || !form.isDirty"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 active:scale-95 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <span v-if="form.processing"
                                    class="material-symbols-outlined animate-spin text-sm">sync</span>
                                <span v-else class="material-symbols-outlined text-sm">save</span>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Legend, Search & Bulk Actions -->
                <div
                    class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 space-y-4">
                    <div class="flex flex-wrap gap-6 justify-between items-center">
                        <div class="flex items-center gap-8">
                            <div class="flex items-center gap-2 group cursor-help"
                                title="Permission matches that of the user's role">
                                <div
                                    class="w-3.5 h-3.5 rounded-md bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600">
                                </div>
                                <span
                                    class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Inherited</span>
                            </div>
                            <div class="flex items-center gap-2 group cursor-help"
                                title="Access explicitly granted to this user">
                                <div
                                    class="w-3.5 h-3.5 rounded-md bg-emerald-500 border border-emerald-600 shadow-sm shadow-emerald-500/20">
                                </div>
                                <span
                                    class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Explicit
                                    Allow</span>
                            </div>
                            <div class="flex items-center gap-2 group cursor-help"
                                title="Access explicitly denied to this user">
                                <div
                                    class="w-3.5 h-3.5 rounded-md bg-rose-500 border border-rose-600 shadow-sm shadow-rose-500/20">
                                </div>
                                <span
                                    class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-widest">Explicit
                                    Deny</span>
                            </div>
                        </div>

                        <!-- Filter Input -->
                        <div class="relative w-full md:w-80">
                            <input type="text" v-model="searchQuery" placeholder="Filter menus or actions..."
                                class="w-full pl-10 pr-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all dark:text-white shadow-sm" />
                            <span
                                class="material-symbols-outlined absolute left-3 top-2 text-gray-400 text-xl font-icon">search</span>
                            <span v-if="searchQuery" @click="searchQuery = ''"
                                class="material-symbols-outlined absolute right-3 top-2 text-gray-400 text-xl font-icon cursor-pointer hover:text-gray-600">close</span>
                        </div>
                    </div>

                    <!-- Global Bulk Actions -->
                    <div class="flex items-center gap-3 pt-2">
                        <span class="text-[10px] font-black uppercase tracking-tighter text-gray-400">Global Bulk
                            Action:</span>
                        <div
                            class="flex bg-white dark:bg-gray-800 p-0.5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                            <button @click="bulkApply(null)" type="button"
                                class="px-3 py-1 text-[9px] font-bold uppercase tracking-widest text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md transition-colors">Inherit
                                All</button>
                            <div class="w-px h-3 bg-gray-200 dark:bg-gray-700 my-auto mx-1"></div>
                            <button @click="bulkApply(true)" type="button"
                                class="px-3 py-1 text-[9px] font-bold uppercase tracking-widest text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-md transition-colors">Allow
                                All</button>
                            <div class="w-px h-3 bg-gray-200 dark:bg-gray-700 my-auto mx-1"></div>
                            <button @click="bulkApply(false)" type="button"
                                class="px-3 py-1 text-[9px] font-bold uppercase tracking-widest text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-md transition-colors">Deny
                                All</button>
                        </div>
                    </div>
                </div>

                <!-- Tree View -->
                <div class="p-6 overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="text-left border-b border-gray-100 dark:border-gray-700">
                                <th
                                    class="pb-3 text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">
                                    Menu / Action Breakdown</th>
                                <th
                                    class="pb-3 text-right text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">
                                    Permission Override Controls</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            <template v-for="menu in filteredMenus" :key="menu.id">
                                <PermissionTreeNode :menu="menu" :form="form" :role-permissions="rolePermissions"
                                    @bulk-apply="handleTreeNodeBulkApply" />
                            </template>
                        </tbody>
                    </table>

                    <div v-if="filteredMenus.length === 0"
                        class="py-32 flex flex-col items-center justify-center text-center">
                        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-full mb-4">
                            <span
                                class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600">search_off</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Boundary not found</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-sm">No menus or actions matched your
                            current search filters.
                            Try using more general terms.</p>
                    </div>
                </div>
            </div>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'
import PermissionRow from './Partials/PermissionRow.vue'
import PermissionTreeNode from './Partials/PermissionTreeNode.vue'

const props = defineProps({
    user: Object,
    softwareMenus: Array,
    rolePermissions: Array, // IDs of allowed actions
    userPermissions: Object, // action_id -> is_allowed (true/false)
})

const searchQuery = ref('')

// Initialize form with current overrides
const form = useForm({
    overrides: []
})

onMounted(() => {
    // Collect action overrides from props
    const collectedOverrides = []
    const collectFromMenus = (menus) => {
        menus.forEach(menu => {
            menu.actions?.forEach(action => {
                const override = props.userPermissions[action.id]
                collectedOverrides.push({
                    id: action.id,
                    is_allowed: override !== undefined ? Boolean(override) : null
                })
            })
            if (menu.children_recursive) collectFromMenus(menu.children_recursive)
        })
    }
    collectFromMenus(props.softwareMenus)
    form.overrides = collectedOverrides
})

const handleTreeNodeBulkApply = ({ menu, val }) => {
    bulkApplyToMenu(menu, val)
}

const savePermissions = () => {
    form.post(route('devAdmin.users.permissions.update', props.user.id), {
        onSuccess: () => {
            // Optional success logic
        }
    })
}

const resetToDefault = () => {
    if (confirm('Are you sure you want to revert all overrides? This user will revert to standard Role-based permissions.')) {
        form.overrides.forEach(o => o.is_allowed = null)
        savePermissions()
    }
}

const bulkApply = (val) => {
    form.overrides.forEach(o => o.is_allowed = val)
}

const bulkApplyToMenu = (menu, val) => {
    // Apply to direct actions of the menu
    menu.actions?.forEach(action => {
        const override = form.overrides.find(o => o.id === action.id)
        if (override) override.is_allowed = val
    })

    // Recursively apply to children menus
    menu.children_recursive?.forEach(child => {
        bulkApplyToMenu(child, val)
    })
}

// Filtering
const filteredMenus = computed(() => {
    if (!searchQuery.value) return props.softwareMenus
    const query = searchQuery.value.toLowerCase()

    return props.softwareMenus.filter(menu => {
        const menuMatch = menu.name.toLowerCase().includes(query)
        const actionMatch = menu.actions?.some(a => a.action.toLowerCase().includes(query))
        const childMatch = menu.children_recursive?.some(c =>
            c.name.toLowerCase().includes(query) ||
            c.actions?.some(a => a.action.toLowerCase().includes(query))
        )
        return menuMatch || actionMatch || childMatch
    })
})
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
