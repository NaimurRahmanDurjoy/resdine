<template>

    <Head :title="pageTitle" />

    <div class="max-w-[1600px] mx-auto">
        <div class="flex flex-col lg:flex-row gap-8 items-start">

            <!-- Left Column: Summary & Search (Sticky) -->
            <div class="w-full lg:w-80 space-y-6 lg:sticky lg:top-6">
                <!-- User Profile Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-indigo-500/20">
                            {{ user.name.charAt(0) }}
                        </div>
                        <div class="min-w-0">
                            <h2
                                class="text-base font-black text-gray-800 dark:text-white truncate uppercase tracking-wider">
                                {{ user.name }}</h2>
                            <p class="text-xs text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest">{{
                                user.role.name }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <Link :href="route('devAdmin.users.index')"
                            class="flex items-center justify-center gap-2 w-full py-3 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                            Back to Users
                        </Link>

                        <button @click="savePermissions" :disabled="form.processing || !form.isDirty"
                            class="w-full py-4 bg-indigo-600 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-indigo-500/25 hover:bg-indigo-700 hover:-translate-y-0.5 active:scale-95 transition-all disabled:opacity-50 disabled:translate-y-0 flex items-center justify-center gap-2">
                            <span v-if="form.processing"
                                class="material-symbols-outlined animate-spin text-sm">sync</span>
                            <span v-else class="material-symbols-outlined text-sm">enhanced_encryption</span>
                            Update Core Rights
                        </button>
                    </div>
                </div>

                <!-- Search & Filters -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                    <div class="relative">
                        <input type="text" v-model="searchQuery" placeholder="Search modules..."
                            class="w-full pl-10 pr-4 py-3.5 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500/50 transition-all dark:text-white placeholder-gray-400" />
                        <span
                            class="material-symbols-outlined absolute left-3.5 top-3.5 text-gray-400 text-xl font-icon">search</span>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button @click="expandAll"
                            class="flex-1 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors">Expand
                            All</button>
                        <button @click="collapseAll"
                            class="flex-1 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest bg-gray-50 dark:bg-gray-900 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">Collapse</button>
                    </div>
                </div>

                <!-- Summary Panel -->
                <PermissionSummary :stats="permissionStats" />
            </div>

            <!-- Right Column: Permission Tree -->
            <div class="flex-1 min-w-0 space-y-6">
                <!-- Global Actions Bar -->
                <div
                    class="flex items-center justify-between bg-white/50 dark:bg-gray-800/50 backdrop-blur-xl p-2 pl-6 rounded-3xl border border-white dark:border-gray-700 shadow-sm sticky top-6 z-10">
                    <div class="flex items-center gap-6">
                        <h2 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em]">
                            Software Architecture</h2>
                        <div class="h-4 w-px bg-gray-200 dark:bg-gray-700"></div>
                        <div class="flex items-center gap-4">
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Global
                                Batch:</span>
                            <div class="flex gap-1">
                                <button @click="bulkApply(null)"
                                    class="px-3 py-1 rounded-lg text-[9px] font-bold uppercase tracking-widest text-gray-500 hover:bg-white dark:hover:bg-gray-800 transition-all">Inherit</button>
                                <button @click="bulkApply(true)"
                                    class="px-3 py-1 rounded-lg text-[9px] font-bold uppercase tracking-widest text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all">Grant
                                    All</button>
                                <button @click="bulkApply(false)"
                                    class="px-3 py-1 rounded-lg text-[9px] font-bold uppercase tracking-widest text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all">Revoke
                                    All</button>
                            </div>
                        </div>
                    </div>

                    <button @click="resetToDefault"
                        class="px-4 py-2 text-rose-500 hover:text-rose-600 text-[10px] font-black uppercase tracking-widest transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">restart_alt</span>
                        Revert All Overrides
                    </button>
                </div>

                <!-- Tree Rendering -->
                <div class="space-y-4">
                    <PermissionTreeNode v-for="menu in filteredMenus" :key="menu.id" :menu="menu"
                        :overrides="form.overrides" :role-permissions="rolePermissionIds" :expanded-menus="expandedMenus"
                        :search-query="searchQuery" @toggle="toggleMenu" @bulk-apply="bulkApplyToMenu"
                        @update="handleUpdate" />

                    <!-- Empty State -->
                    <div v-if="filteredMenus.length === 0" class="py-20 text-center">
                        <div
                            class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span
                                class="material-symbols-outlined text-gray-300 dark:text-gray-700 text-4xl">search_off</span>
                        </div>
                        <h3 class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest text-sm">No
                            Results Match</h3>
                        <p class="text-xs text-gray-400 mt-1">Try a different search term</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'
import PermissionTreeNode from './Partials/PermissionTreeNode.vue'
import PermissionSummary from './Partials/PermissionSummary.vue'

defineOptions({ layout: DevAdminLayout })

const props = defineProps({
    user: Object,
    softwareMenus: Array,
    rolePermissions: Array,
    overrides: Array,
    pageTitle: { type: String, default: 'DevAdmin: Permissions' }
})

const searchQuery = ref('')

const allMenuIds = []
const allActionIds = []
const collectData = (menus) => {
    menus.forEach(menu => {
        allMenuIds.push(menu.id)
        if (menu.grouped_actions) {
            ['view', 'create', 'edit', 'delete'].forEach(type => {
                if (menu.grouped_actions[type]) allActionIds.push(menu.grouped_actions[type].id)
            })
            menu.grouped_actions.others?.forEach(a => allActionIds.push(a.id))
        }
        if (menu.children_recursive) collectData(menu.children_recursive)
    })
}
collectData(props.softwareMenus)

// Auto-expand all menus so saved permissions are visible on first load
const expandedMenus = ref(new Set(allMenuIds))

const form = useForm({
    overrides: props.overrides.map(override => ({
        ...override,
        action_id: Number(override.action_id)
    }))
})

const rolePermissionIds = computed(() => props.rolePermissions.map(id => Number(id)))

const toggleMenu = (id) => expandedMenus.value.has(id) ? expandedMenus.value.delete(id) : expandedMenus.value.add(id)
const expandAll = () => allMenuIds.forEach(id => expandedMenus.value.add(id))
const collapseAll = () => expandedMenus.value.clear()

const handleUpdate = (update) => {
    const normalizedUpdate = {
        ...update,
        action_id: Number(update.action_id)
    }
    const index = form.overrides.findIndex(o => o.action_id === normalizedUpdate.action_id)
    if (update.is_allowed === null) {
        if (index > -1) form.overrides.splice(index, 1)
    } else {
        if (index > -1) {
            form.overrides[index].is_allowed = normalizedUpdate.is_allowed
        } else {
            form.overrides.push(normalizedUpdate)
        }
    }
}

const savePermissions = () => {
    form.post(route('devAdmin.users.permissions.update', props.user.id), {
        preserveScroll: true
    })
}

const resetToDefault = () => {
    if (confirm('Revert all custom overrides for this user?')) {
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

const bulkApplyToMenu = ({ menu, val }) => {
    const getMenuActionIds = (node) => {
        let ids = []
        if (node.grouped_actions) {
            ['view', 'create', 'edit', 'delete'].forEach(type => {
                if (node.grouped_actions[type]) ids.push(node.grouped_actions[type].id)
            })
            node.grouped_actions.others?.forEach(a => ids.push(a.id))
        }
        if (node.children_recursive) {
            node.children_recursive.forEach(child => {
                ids = [...ids, ...getMenuActionIds(child)]
            })
        }
        return ids
    }

    const menuActionIds = getMenuActionIds(menu)
    form.overrides = form.overrides.filter(o => !menuActionIds.includes(o.action_id))

    if (val !== null) {
        menuActionIds.forEach(id => {
            form.overrides.push({ action_id: id, is_allowed: val })
        })
    }
}

const permissionStats = computed(() => {
    let explicitAllow = 0
    let explicitDeny = 0

    form.overrides.forEach(o => {
        if (o.is_allowed === true) explicitAllow++
        else if (o.is_allowed === false) explicitDeny++
    })

    const allowedSet = new Set(rolePermissionIds.value)
    form.overrides.forEach(o => {
        if (o.is_allowed === true) allowedSet.add(o.action_id)
        else if (o.is_allowed === false) allowedSet.delete(o.action_id)
    })

    return {
        allowed: allowedSet.size,
        restricted: allActionIds.length - allowedSet.size,
        overrides: form.overrides.length,
        explicitAllow,
        explicitDeny
    }
})

const filteredMenus = computed(() => {
    if (!searchQuery.value) return props.softwareMenus
    const q = searchQuery.value.toLowerCase()

    const checkNode = (node) => {
        const matchesName = node.name.toLowerCase().includes(q)
        const matchesAction = node.grouped_actions && (
            ['view', 'create', 'edit', 'delete'].some(type => node.grouped_actions[type]?.action.toLowerCase().includes(q)) ||
            node.grouped_actions.others?.some(a => a.action.toLowerCase().includes(q))
        )
        const hasMatchingChild = node.children_recursive?.some(child => checkNode(child))
        return matchesName || matchesAction || hasMatchingChild
    }

    return props.softwareMenus.filter(m => checkNode(m))
})
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
