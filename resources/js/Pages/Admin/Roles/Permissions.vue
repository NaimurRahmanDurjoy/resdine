<template>
    <AdminLayout :pageTitle="pageTitle">

        <Head :title="pageTitle" />

        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Header Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div
                    class="bg-gradient-to-r from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <Link :href="route('admin.settings.roles.index')"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                                <span class="material-symbols-outlined font-icon">arrow_back</span>
                            </Link>
                            <div>
                                <h1 class="text-xl font-bold text-gray-800 dark:text-white uppercase tracking-wider">{{
                                    pageTitle }}</h1>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Configuring permissions
                                        for</span>
                                    <span
                                        class="text-xs font-bold text-blue-600 dark:text-blue-400 underline decoration-blue-500/30 whitespace-nowrap">{{
                                        role.name }}</span>
                                    <span
                                        class="px-2 py-0.25 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 text-[9px] font-bold rounded border border-emerald-100 dark:border-emerald-800 uppercase tracking-tighter">
                                        Role Mapping
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <button @click="resetToDefault" type="button"
                                class="text-xs font-semibold text-gray-500 hover:text-rose-600 transition-colors uppercase tracking-widest px-3 py-2">
                                Revoke All
                            </button>
                            <button @click="savePermissions" :disabled="form.processing || !form.isDirty"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 active:scale-95 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <span v-if="form.processing"
                                    class="material-symbols-outlined animate-spin text-sm">sync</span>
                                <span v-else class="material-symbols-outlined text-sm">save</span>
                                Save Permissions
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
                                title="Role currently has access to this action">
                                <div
                                    class="w-3.5 h-3.5 rounded-md bg-emerald-500 border border-emerald-600 shadow-sm shadow-emerald-500/20">
                                </div>
                                <span
                                    class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Allowed
                                    Access</span>
                            </div>
                            <div class="flex items-center gap-2 group cursor-help"
                                title="Role currently has NO access to this action">
                                <div
                                    class="w-3.5 h-3.5 rounded-md bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600">
                                </div>
                                <span
                                    class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">No
                                    Access</span>
                            </div>
                        </div>

                        <!-- Filter -->
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
                    <div class="flex items-center gap-6 pt-2">
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-black uppercase tracking-tighter text-gray-400 whitespace-nowrap">Bulk Permissions:</span>
                            <div class="flex bg-white dark:bg-gray-800 p-0.5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                <button @click="bulkApply(true)" type="button" class="px-3 py-1 text-[9px] font-bold uppercase tracking-widest text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-md transition-colors">Grant All</button>
                                <div class="w-px h-3 bg-gray-200 dark:bg-gray-700 my-auto mx-1"></div>
                                <button @click="bulkApply(false)" type="button" class="px-3 py-1 text-[9px] font-bold uppercase tracking-widest text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-md transition-colors">Revoke All</button>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 ml-auto">
                            <span class="text-[10px] font-black uppercase tracking-tighter text-gray-400 whitespace-nowrap">View Control:</span>
                            <div class="flex bg-white dark:bg-gray-800 p-0.5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                <button @click="expandAll" type="button" title="Expand All" class="px-2 py-1 text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-all flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm font-icon">unfold_more</span>
                                    <span class="text-[8px] font-bold uppercase tracking-widest leading-none">Expand All</span>
                                </button>
                                <div class="w-px h-3 bg-gray-200 dark:bg-gray-700 my-auto mx-1"></div>
                                <button @click="collapseAll" type="button" title="Collapse All" class="px-2 py-1 text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded transition-all flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm font-icon">unfold_less</span>
                                    <span class="text-[8px] font-bold uppercase tracking-widest leading-none">Collapse All</span>
                                </button>
                            </div>
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
                                    Menu / Action Hierarchy</th>
                                <th
                                    class="pb-3 text-right text-xs font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">
                                    Enable / Disable Access</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            <template v-for="menu in filteredMenus" :key="menu.id">
                                <PermissionTreeNode 
                                    :menu="menu" 
                                    :form="form" 
                                    :expandedMenus="expandedMenus"
                                    :searchQuery="searchQuery"
                                    @toggle="toggleMenu"
                                    @bulk-apply="bulkApplyToMenu"
                                />
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PermissionRow from './Partials/PermissionRow.vue'
import PermissionTreeNode from './Partials/PermissionTreeNode.vue'

const props = defineProps({
    role: Object,
    softwareMenus: Array,
    rolePermissions: Array, // IDs of currently allowed actionIds
    pageTitle: String
})

const searchQuery = ref('')
const expandedMenus = ref(new Set())

// Collect all software action IDs for batch operations
const allActionIds = []
const allMenuIds = []
const collectActionIds = (menus) => {
    menus.forEach(menu => {
        allMenuIds.push(menu.id)
        menu.actions?.forEach(action => allActionIds.push(action.id))
        if (menu.children_recursive) collectActionIds(menu.children_recursive)
    })
}
collectActionIds(props.softwareMenus)

onMounted(() => {
    // Optionally expand some by default or based on state
    // For now, let's keep them collapsed for a clean look
})

const isExpanded = (id) => expandedMenus.value.has(id) || !!searchQuery.value

const toggleMenu = (id) => {
    if (expandedMenus.value.has(id)) {
        expandedMenus.value.delete(id)
    } else {
        expandedMenus.value.add(id)
    }
}

const expandAll = () => {
    allMenuIds.forEach(id => expandedMenus.value.add(id))
}

const collapseAll = () => {
    expandedMenus.value.clear()
}

const form = useForm({
    allowed_action_ids: [...props.rolePermissions]
})

const savePermissions = () => {
    form.post(route('admin.settings.roles.permissions.update', props.role.id), {
        preserveScroll: true
    })
}

const resetToDefault = () => {
    if (confirm('Revoke all permissions for this role? All users with this role will lose access.')) {
        form.allowed_action_ids = []
        savePermissions()
    }
}

const bulkApply = (shouldAllow) => {
    if (shouldAllow) {
        form.allowed_action_ids = [...allActionIds]
    } else {
        form.allowed_action_ids = []
    }
}

const bulkApplyToMenu = (menu, shouldAllow) => {
    const getMenuActionIds = (node) => {
        let ids = node.actions?.map(a => a.id) || []
        if (node.children_recursive) {
            node.children_recursive.forEach(child => {
                ids = [...ids, ...getMenuActionIds(child)]
            })
        }
        return ids
    }

    const menuIds = getMenuActionIds(menu)

    if (shouldAllow) {
        // Add unique menuIds to form
        const currentIds = new Set(form.allowed_action_ids)
        menuIds.forEach(id => currentIds.add(id))
        form.allowed_action_ids = Array.from(currentIds)
    } else {
        // Remove menuIds from form
        form.allowed_action_ids = form.allowed_action_ids.filter(id => !menuIds.includes(id))
    }
}

// Filtering Logic
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
