<template>

    <Head :title="pageTitle" />

    <div class="max-w-[1600px] mx-auto">
        <div class="flex flex-col lg:flex-row gap-8 items-start">

            <!-- Left Column: Context & Stats -->
            <div class="w-full lg:w-80 space-y-6 lg:sticky lg:top-6">
                <!-- Role Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-indigo-500/20">
                            <span class="material-symbols-outlined">shield_person</span>
                        </div>
                        <div class="min-w-0">
                            <h2
                                class="text-base font-black text-gray-800 dark:text-white truncate uppercase tracking-wider">
                                {{ role.name }}</h2>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest">
                                Global Security Role</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <Link :href="route('admin.settings.roles.index')"
                            class="flex items-center justify-center gap-2 w-full py-3 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                            Back to Roles
                        </Link>

                        <button @click="savePermissions" :disabled="form.processing || !form.isDirty"
                            class="w-full py-4 bg-indigo-600 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-indigo-500/25 hover:bg-indigo-700 hover:-translate-y-0.5 active:scale-95 transition-all disabled:opacity-50 disabled:translate-y-0 flex items-center justify-center gap-2">
                            <span v-if="form.processing"
                                class="material-symbols-outlined animate-spin text-sm">sync</span>
                            <span v-else class="material-symbols-outlined text-sm">security</span>
                            Update Role Access
                        </button>
                    </div>
                </div>

                <!-- Search -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                    <div class="relative">
                        <input type="text" v-model="searchQuery" placeholder="Find menu or action..."
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

                <!-- Summary -->
                <RolePermissionSummary :allowed-count="form.allowed_action_ids.length"
                    :restricted-count="allActionIds.length - form.allowed_action_ids.length" />
            </div>

            <!-- Right Column: Tree -->
            <div class="flex-1 min-w-0 space-y-6">
                <!-- Global Actions -->
                <div
                    class="flex items-center justify-between bg-white/50 dark:bg-gray-800/50 backdrop-blur-xl p-2 pl-6 rounded-3xl border border-white dark:border-gray-700 shadow-sm sticky top-6 z-10">
                    <h2 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em]">
                        Permissions Explorer</h2>
                    <div class="flex items-center gap-2">
                        <button @click="bulkApply(true)"
                            class="px-4 py-2 bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-emerald-100 transition-all">Grant
                            All</button>
                        <button @click="bulkApply(false)"
                            class="px-4 py-2 bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-rose-100 transition-all">Revoke
                            All</button>
                    </div>
                </div>

                <div class="space-y-4">
                    <PermissionTreeNode v-for="menu in filteredMenus" :key="menu.id" :menu="menu"
                        :allowed-action-ids="form.allowed_action_ids" :expanded-menus="expandedMenus"
                        :search-query="searchQuery" @toggle="toggleMenu" @bulk-apply="bulkApplyToMenu"
                        @update="handleUpdate" />

                    <!-- Empty State -->
                    <div v-if="filteredMenus.length === 0" class="py-20 text-center">
                        <div
                            class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <span
                                class="material-symbols-outlined text-gray-300 dark:text-gray-700 text-3xl">filter_list_off</span>
                        </div>
                        <h3 class="text-gray-400 font-bold uppercase tracking-widest text-xs">No Results</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PermissionTreeNode from './Partials/PermissionTreeNode.vue'
import RolePermissionSummary from './Partials/RolePermissionSummary.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    role: Object,
    softwareMenus: Array,
    rolePermissions: Array,
    pageTitle: String
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

// Auto-expand all menus so previously saved permissions are visible on first load
const expandedMenus = ref(new Set(allMenuIds))

const form = useForm({
    allowed_action_ids: props.rolePermissions.map(id => Number(id))
})

watch(
    () => props.rolePermissions,
    rolePermissions => {
        form.allowed_action_ids = rolePermissions.map(id => Number(id))
    },
    { immediate: true }
)

const toggleMenu = (id) => expandedMenus.value.has(id) ? expandedMenus.value.delete(id) : expandedMenus.value.add(id)
const expandAll = () => allMenuIds.forEach(id => expandedMenus.value.add(id))
const collapseAll = () => expandedMenus.value.clear()

const handleUpdate = (update) => {
    const actionId = Number(update.action_id)
    const index = form.allowed_action_ids.indexOf(actionId)
    if (update.is_allowed) {
        if (index === -1) form.allowed_action_ids.push(actionId)
    } else {
        if (index > -1) form.allowed_action_ids.splice(index, 1)
    }
}

const savePermissions = () => {
    form.post(route('admin.settings.roles.permissions.update', props.role.id), {
        preserveScroll: true
    })
}

const bulkApply = (shouldAllow) => {
    form.allowed_action_ids = shouldAllow ? [...allActionIds] : []
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
    form.allowed_action_ids = form.allowed_action_ids.filter(id => !menuActionIds.includes(id))

    if (val) {
        menuActionIds.forEach(id => form.allowed_action_ids.push(id))
    }
}

const filteredMenus = computed(() => {
    if (!searchQuery.value) return props.softwareMenus
    const q = searchQuery.value.toLowerCase()

    const checkNode = (node) => {
        const matchesName = node.name.toLowerCase().includes(q)
        const hasMatchingChild = node.children_recursive?.some(child => checkNode(child))
        return matchesName || hasMatchingChild
    }

    return props.softwareMenus.filter(m => checkNode(m))
})
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
