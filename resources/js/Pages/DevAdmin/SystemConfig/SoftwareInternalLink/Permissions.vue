<template>
    <DevAdminLayout>
        <Head :title="`Permissions: ${action.action}`" />

        <div class="max-w-6xl mx-auto space-y-8">
            <!-- Premium Header -->
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-br from-blue-50 to-white dark:from-blue-900/10 dark:to-gray-800 px-8 py-8 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div class="flex items-center gap-6">
                            <Link :href="route('devAdmin.systemConfig.software.internal-link.index')" class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-400 hover:text-blue-600 transition-all border border-gray-100 dark:border-gray-800">
                                <span class="material-symbols-outlined font-icon">arrow_back</span>
                            </Link>
                            <div>
                                <h1 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-wider mb-1">{{ action.action }} Permission Mapping</h1>
                                <div class="flex items-center gap-3">
                                    <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-[10px] font-black rounded-lg uppercase tracking-widest">{{ action.software_menu?.name }}</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 font-mono tracking-tighter">{{ action.route }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <button @click="save" :disabled="form.processing || !form.isDirty"
                            class="px-8 py-4 bg-blue-600 text-white rounded-[1.25rem] text-sm font-black uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-1 active:scale-95 transition-all disabled:opacity-50 flex items-center gap-3">
                            <span v-if="form.processing" class="material-symbols-outlined animate-spin text-sm">sync</span>
                            <span v-else class="material-symbols-outlined text-sm">auto_verify</span>
                            Sync Global Mapping
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-100 dark:divide-gray-700">
                    <!-- Roles Section -->
                    <div class="p-8 space-y-8">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-500">
                                    <span class="material-symbols-outlined font-icon">vitals</span>
                                </div>
                                <h2 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Role-Based Access</h2>
                            </div>
                            <!-- Bulk Toggle -->
                            <div class="flex bg-gray-50 dark:bg-gray-900 p-1 rounded-xl border border-gray-100 dark:border-gray-800 shadow-inner">
                                <button @click="toggleRoles(true)" type="button" class="px-3 py-1.5 text-[9px] font-black uppercase text-emerald-600 hover:bg-white dark:hover:bg-gray-800 rounded-lg transition-all">Grant All</button>
                                <button @click="toggleRoles(false)" type="button" class="px-3 py-1.5 text-[9px] font-black uppercase text-rose-600 hover:bg-white dark:hover:bg-gray-800 rounded-lg transition-all ml-1">Revoke All</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div v-for="role in roles" :key="role.id" 
                                class="flex items-center justify-between p-4 rounded-2xl border transition-all cursor-pointer group"
                                :class="form.roles[role.id] ? 'bg-emerald-50/30 border-emerald-100 dark:bg-emerald-900/10 dark:border-emerald-800/50' : 'bg-gray-50/50 border-gray-100 dark:bg-gray-900/30 dark:border-gray-800'"
                                @click="form.roles[role.id] = !form.roles[role.id]">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-2 h-2 rounded-full shrink-0" :class="form.roles[role.id] ? 'bg-emerald-500 shadow-sm shadow-emerald-500/50' : 'bg-gray-300 dark:bg-gray-600'"></span>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest truncate">{{ role.name }}</span>
                                </div>
                                <div class="flex-shrink-0 ml-2">
                                    <div class="w-10 h-6 rounded-full transition-colors relative" :class="form.roles[role.id] ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-700'">
                                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform duration-200 shadow-sm" :class="{ 'translate-x-4': form.roles[role.id] }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Overrides Section -->
                    <div class="p-8 space-y-8 bg-gray-50/20 dark:bg-gray-900/5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-500">
                                <span class="material-symbols-outlined font-icon">person_pin_circle</span>
                            </div>
                            <h2 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Active Individual Overrides</h2>
                        </div>

                        <div v-if="usersWithOverrides.length > 0" class="space-y-4">
                            <div v-for="user in usersWithOverrides" :key="user.id" 
                                class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 flex items-center justify-between shadow-sm group">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-lg shadow-blue-500/20">
                                        {{ user.name.charAt(0) }}
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider truncate">{{ user.name }}</span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 font-mono tracking-tighter truncate">{{ user.email }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col items-end">
                                        <span class="px-2 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-widest"
                                            :class="user.is_allowed ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">
                                            {{ user.is_allowed ? 'Explicit Allow' : 'Explicit Deny' }}
                                        </span>
                                    </div>
                                    <button @click="removeUserOverride(user.id)" type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-300 hover:bg-rose-50 hover:text-rose-500 transition-all">
                                        <span class="material-symbols-outlined text-sm font-icon">close</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-16 flex flex-col items-center justify-center text-center">
                            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-[2rem] flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-700">ads_click</span>
                            </div>
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">No Overrides Detected</h3>
                            <p class="text-[10px] text-gray-500 mt-2 max-w-[240px] leading-relaxed">Overrides can be managed individually from the User Account settings panel.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hint Card -->
            <div class="p-6 bg-blue-600 rounded-[2rem] shadow-2xl shadow-blue-500/20 border border-blue-500 flex items-center justify-between group overflow-hidden relative">
                <div class="relative z-10 flex items-center gap-6">
                    <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white border border-white/10">
                        <span class="material-symbols-outlined text-3xl font-icon">info</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-white uppercase tracking-widest mb-1">Architectural Note</h4>
                        <p class="text-blue-100 text-xs font-medium max-w-xl">Role permissions define the global security baseline. User overrides take precedence over role settings to provide granular control when needed.</p>
                    </div>
                </div>
                <!-- Abstract Design Decor -->
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/5 rounded-full blur-3xl transition-transform group-hover:scale-150 duration-700"></div>
            </div>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import { Link, Head, useForm } from '@inertiajs/vue3'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'

const props = defineProps({
    action: Object,
    roles: Array,
    usersWithOverrides: Array,
})

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
    if (confirm('Revert override for this user? This will restore standard Role-based access.')) {
        form.usersToRemove.push(userId)
        save()
    }
}

const save = () => {
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
