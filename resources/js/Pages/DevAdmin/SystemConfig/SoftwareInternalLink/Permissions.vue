<template>
    <DevAdminLayout>
        <Head :title="`Permissions: ${action.action}`" />

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <Link :href="route('devAdmin.systemConfig.software.internal-link.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                                <span class="material-symbols-outlined font-icon">arrow_back</span>
                            </Link>
                            <div>
                                <h1 class="text-xl font-bold text-gray-800 dark:text-white uppercase tracking-wider">{{ action.action }} Permission Mapping</h1>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-400 dark:text-gray-500 font-mono tracking-tighter">{{ action.route }}</span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                    <span class="text-xs text-gray-500 font-bold uppercase tracking-widest">{{ action.software_menu?.name }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <button @click="save" :disabled="form.processing || !form.isDirty"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 active:scale-95 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 flex items-center gap-2">
                            <span v-if="form.processing" class="material-symbols-outlined animate-spin text-sm">sync</span>
                            <span v-else class="material-symbols-outlined text-sm">save</span>
                            Save All Changes
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700">
                    <!-- Roles Section -->
                    <div class="p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-indigo-500 font-icon text-xl">vitals</span>
                                <h2 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Global Role Access</h2>
                            </div>
                            <!-- Bulk Toggle for Roles -->
                            <div class="flex bg-gray-100 dark:bg-gray-900 p-0.5 rounded-lg border border-gray-200 dark:border-gray-700">
                                <button @click="toggleRoles(true)" type="button" class="px-2 py-0.5 text-[8px] font-black uppercase text-emerald-600 hover:bg-white dark:hover:bg-gray-800 rounded shadow-sm transition-all">Allow All</button>
                                <button @click="toggleRoles(false)" type="button" class="px-2 py-0.5 text-[8px] font-black uppercase text-rose-600 hover:bg-white dark:hover:bg-gray-800 rounded shadow-sm transition-all">Deny All</button>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div v-for="role in roles" :key="role.id" 
                                class="flex items-center justify-between p-3 rounded-xl border transition-all"
                                :class="form.roles[role.id] ? 'bg-emerald-50/50 border-emerald-100 dark:bg-emerald-900/10 dark:border-emerald-800/50' : 'bg-gray-50 border-gray-100 dark:bg-gray-900/50 dark:border-gray-800'">
                                <div class="flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full" :class="form.roles[role.id] ? 'bg-emerald-500 shadow-sm shadow-emerald-500/50' : 'bg-gray-300 dark:bg-gray-600'"></span>
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ role.name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <label :for="`role-${role.id}`" class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" v-model="form.roles[role.id]" :id="`role-${role.id}`" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Overrides Section -->
                    <div class="p-6 space-y-6 bg-gray-50/30 dark:bg-gray-900/10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-500 font-icon text-xl">person_pin_circle</span>
                                <h2 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">User Overrides</h2>
                            </div>
                        </div>

                        <div v-if="usersWithOverrides.length > 0" class="space-y-4">
                            <div v-for="user in usersWithOverrides" :key="user.id" 
                                class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 flex items-center justify-between shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center border border-blue-100 dark:border-blue-800">
                                        <span class="text-xs font-bold text-blue-600 dark:text-blue-400 capitalize">{{ user.name.charAt(0) }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-900 dark:text-white">{{ user.name }}</span>
                                        <span class="text-[9px] text-gray-500 dark:text-gray-500 font-mono tracking-tighter">{{ user.email }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-tighter"
                                        :class="user.is_allowed ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100'">
                                        {{ user.is_allowed ? 'Explicit Allow' : 'Explicit Deny' }}
                                    </span>
                                    <button @click="removeUserOverride(user.id)" type="button" class="p-1 text-gray-400 hover:text-rose-600 transition-colors">
                                        <span class="material-symbols-outlined text-sm">close</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-12 flex flex-col items-center justify-center text-center">
                            <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-full mb-3">
                                <span class="material-symbols-outlined text-3xl text-gray-400">group_off</span>
                            </div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">No individual overrides</h3>
                            <p class="text-[10px] text-gray-500 mt-1 max-w-[200px]">Use the Manage Permissions button in the account list to add user-specific overrides.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import { Link, Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
    action: Object,
    roles: Array,
    usersWithOverrides: Array,
})

// Build initial values for roles
const initialRoles = {}
props.roles.forEach(role => {
    initialRoles[role.id] = role.is_allowed
})

const form = useForm({
    roles: initialRoles,
    usersToRemove: [],
})

const toggleRoles = (val) => {
    Object.keys(form.roles).forEach(id => {
        form.roles[id] = val
    })
}

const removeUserOverride = (userId) => {
    if (confirm('Revert override for this user? They will revert to standard Role-based access.')) {
        form.usersToRemove.push(userId)
        // Locally hide the user (optional, form will sync on success)
        // We'll just submit the form for now as the server logic removes overrides
        save()
    }
}

const save = () => {
    // Collect roles that were explicitly changed
    // In this view, we'll just send all role states
    form.post(route('devAdmin.systemConfig.software.internal-link.permissions.update', props.action.id), {
        preserveScroll: true
    })
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
