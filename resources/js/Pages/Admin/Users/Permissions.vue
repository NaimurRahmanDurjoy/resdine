<template>
        <Head :title="pageTitle" />

        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Header Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div
                    class="bg-gradient-to-r from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <Link :href="route('admin.users.index')"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                                <span class="material-symbols-outlined font-icon">arrow_back</span>
                            </Link>
                            <div>
                                <h1 class="text-xl font-bold text-gray-800 dark:text-white uppercase tracking-wider">{{
                                    pageTitle }}</h1>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Overriding permissions
                                        for</span>
                                    <span
                                        class="text-xs font-bold text-blue-600 dark:text-blue-400 underline decoration-blue-500/30 whitespace-nowrap">{{
                                        user.name }}</span>
                                    <span
                                        class="px-2 py-0.25 bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 text-[9px] font-bold rounded border border-blue-100 dark:border-blue-800 uppercase tracking-tighter">
                                        Role: {{ user.role.name }}
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
                                Save Overrides
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Legend, Search & View Controls -->
                <div
                    class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 space-y-4">
                    <div class="flex flex-wrap gap-6 justify-between items-center">
                        <div class="flex items-center gap-8">
                            <div class="flex items-center gap-2" title="Permission matches Role default">
                                <div class="w-3.5 h-3.5 rounded-md bg-white border border-gray-300 dark:bg-gray-700">
                                </div>
                                <span
                                    class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Inherit</span>
                            </div>
                            <div class="flex items-center gap-2" title="User is explicitly granted access">
                                <div
                                    class="w-3.5 h-3.5 rounded-md bg-emerald-500 border border-emerald-600 shadow-sm shadow-emerald-500/20">
                                </div>
                                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Explicit
                                    Allow</span>
                            </div>
                            <div class="flex items-center gap-2" title="User is explicitly denied access">
                                <div
                                    class="w-3.5 h-3.5 rounded-md bg-rose-500 border border-rose-600 shadow-sm shadow-rose-500/20">
                                </div>
                                <span class="text-[10px] font-bold text-rose-600 uppercase tracking-widest">Explicit
                                    Deny</span>
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

                    <!-- Global Tools -->
                    <div class="flex items-center gap-6 pt-2">
                        <div class="flex items-center gap-3">
                            <span
                                class="text-[10px] font-black uppercase tracking-tighter text-gray-400 whitespace-nowrap">Global
                                Actions:</span>
                            <div
                                class="flex bg-white dark:bg-gray-800 p-0.5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                <button @click="bulkApply(null)" type="button"
                                    class="px-3 py-1 text-[9px] font-bold uppercase tracking-widest text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-900/20 rounded-md transition-colors">Inherit
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

                        <div class="flex items-center gap-3 ml-auto">
                            <span
                                class="text-[10px] font-black uppercase tracking-tighter text-gray-400 whitespace-nowrap">View
                                Control:</span>
                            <div
                                class="flex bg-white dark:bg-gray-800 p-0.5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                <button @click="expandAll" type="button" title="Expand All"
                                    class="px-2 py-1 text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-all flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm font-icon">unfold_more</span>
                                    <span class="text-[8px] font-bold uppercase tracking-widest leading-none">Expand
                                        All</span>
                                </button>
                                <div class="w-px h-3 bg-gray-200 dark:bg-gray-700 my-auto mx-1"></div>
                                <button @click="collapseAll" type="button" title="Collapse All"
                                    class="px-2 py-1 text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded transition-all flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm font-icon">unfold_less</span>
                                    <span class="text-[8px] font-bold uppercase tracking-widest leading-none">Collapse
                                        All</span>
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
                                    Override State (Inherit/Allow/Deny)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            <template v-for="menu in filteredMenus" :key="menu.id">
                                <PermissionTreeNode :menu="menu" :overrides="form.overrides"
                                    :rolePermissions="rolePermissions" :expandedMenus="expandedMenus"
                                    :searchQuery="searchQuery" @toggle="toggleMenu" @bulk-apply="bulkApplyToMenu"
                                    @update="handleUpdate" />
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PermissionRow from './Partials/PermissionRow.vue'
import PermissionTreeNode from './Partials/PermissionTreeNode.vue'
defineOptions({ layout: AdminLayout })

const props = defineProps({
    user: Object,
    softwareMenus: Array,
    rolePermissions: Array,
    overrides: Array, // Array of {action_id, is_allowed}
    pageTitle: String
})

const searchQuery = ref('')
const expandedMenus = ref(new Set())

const allMenuIds = []
const allActionIds = []
const collectData = (menus) => {
    menus.forEach(menu => {
        allMenuIds.push(menu.id)
        menu.actions?.forEach(a => allActionIds.push(a.id))
        if (menu.children_recursive) collectData(menu.children_recursive)
    })
}
collectData(props.softwareMenus)

const form = useForm({
    overrides: [...props.overrides]
})

const isExpanded = (id) => expandedMenus.value.has(id) || !!searchQuery.value
const toggleMenu = (id) => expandedMenus.value.has(id) ? expandedMenus.value.delete(id) : expandedMenus.value.add(id)
const expandAll = () => allMenuIds.forEach(id => expandedMenus.value.add(id))
const collapseAll = () => expandedMenus.value.clear()

const handleUpdate = (update) => {
    const index = form.overrides.findIndex(o => o.action_id === update.action_id)
    if (update.is_allowed === null) {
        if (index > -1) form.overrides.splice(index, 1)
    } else {
        if (index > -1) {
            form.overrides[index].is_allowed = update.is_allowed
        } else {
            form.overrides.push(update)
        }
    }
}

const savePermissions = () => {
    form.post(route('admin.users.permissions.update', props.user.id), {
        preserveScroll: true
    })
}

const resetToDefault = () => {
    if (confirm('Are you sure you want to revert all overrides? This user will revert to standard Role-based permissions.')) {
        form.overrides = []
        savePermissions()
    }
}

const bulkApply = (shouldAllow) => {
    form.overrides = []
    if (shouldAllow !== null) {
        allActionIds.forEach(id => {
            form.overrides.push({ action_id: id, is_allowed: shouldAllow })
        })
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

    const menuActionIds = getMenuActionIds(menu)

    // Remove existing overrides for these actions
    form.overrides = form.overrides.filter(o => !menuActionIds.includes(o.action_id))

    if (shouldAllow !== null) {
        menuActionIds.forEach(id => {
            form.overrides.push({ action_id: id, is_allowed: shouldAllow })
        })
    }
}

const filteredMenus = computed(() => {
    if (!searchQuery.value) return props.softwareMenus
    const q = searchQuery.value.toLowerCase()
    return props.softwareMenus.filter(m => {
        const match = m.name.toLowerCase().includes(q)
        const actionMatch = m.actions?.some(a => a.action.toLowerCase().includes(q))
        const childMatch = m.children_recursive?.some(c => c.name.toLowerCase().includes(q) || c.actions?.some(a => a.action.toLowerCase().includes(q)))
        return match || actionMatch || childMatch
    })
})
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
