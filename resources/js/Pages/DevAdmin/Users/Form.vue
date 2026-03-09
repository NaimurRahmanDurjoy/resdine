<template>
    <DevAdminLayout>

        <Head :title="isEdit ? 'Modify Access' : 'Create Access'" />

        <div class="max-w-4xl mx-auto pb-10">
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div
                    class="bg-gradient-to-br from-blue-600 to-indigo-700 px-8 py-10 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="material-symbols-outlined text-blue-200">shield_person</span>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-200">Security /
                                Identity Access</span>
                        </div>
                        <h1 class="text-3xl font-black">{{ isEdit ? 'Modify Access' : 'Create Access' }}</h1>
                        <p class="text-blue-100 mt-2 text-sm opacity-90 max-w-sm">Define user credentials and system
                            level permissions with precision auditing.</p>
                    </div>

                    <!-- Decorative circles -->
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute right-20 bottom-0 w-20 h-20 bg-blue-400/20 rounded-full blur-2xl"></div>
                </div>

                <form @submit.prevent="submit" class="p-8 space-y-8">
                    <!-- Basic Information Section -->
                    <div class="space-y-6">
                        <div
                            class="flex items-center gap-2 text-gray-400 border-b border-gray-50 dark:border-gray-700 pb-2">
                            <span class="material-symbols-outlined text-sm">badge</span>
                            <h2 class="text-[10px] font-black uppercase tracking-widest">Basic Information</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-1">
                                <label
                                    class="block text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Full
                                    Legal Name</label>
                                <input type="text" v-model="form.name" required
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-gray-400"
                                    placeholder="e.g. Johnathan Doe">
                                <div v-if="form.errors.name" class="mt-1 text-[10px] font-bold text-rose-500">{{
                                    form.errors.name }}</div>
                            </div>

                            <div class="space-y-1">
                                <label
                                    class="block text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email
                                    Endpoint</label>
                                <input type="email" v-model="form.email" required
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-gray-400"
                                    placeholder="j.doe@system.com">
                                <div v-if="form.errors.email" class="mt-1 text-[10px] font-bold text-rose-500">{{
                                    form.errors.email }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Configuration -->
                    <div class="space-y-6">
                        <div
                            class="flex items-center gap-2 text-gray-400 border-b border-gray-50 dark:border-gray-700 pb-2">
                            <span class="material-symbols-outlined text-sm">key</span>
                            <h2 class="text-[10px] font-black uppercase tracking-widest">Security Configuration</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-1">
                                <label
                                    class="block text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ isEdit ? 'New Password' : 'Access Password' }}
                                </label>
                                <input type="password" v-model="form.password" :required="!isEdit"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                                    placeholder="••••••••">
                                <p v-if="isEdit" class="text-[10px] text-gray-400 mt-1 italic">Leave blank to retain
                                    current password hash</p>
                                <div v-if="form.errors.password" class="mt-1 text-[10px] font-bold text-rose-500">{{
                                    form.errors.password }}</div>
                            </div>

                            <div class="space-y-1">
                                <label
                                    class="block text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Confirm
                                    Credentials</label>
                                <input type="password" v-model="form.password_confirmation" :required="!isEdit"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <!-- Access Boundary -->
                    <div class="space-y-6">
                        <div
                            class="flex items-center gap-2 text-gray-400 border-b border-gray-50 dark:border-gray-700 pb-2">
                            <span class="material-symbols-outlined text-sm">hub</span>
                            <h2 class="text-[10px] font-black uppercase tracking-widest">Access Boundary</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-1">
                                <label
                                    class="block text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Operational
                                    Branch</label>
                                <select v-model="form.branch_id" required
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all appearance-none cursor-pointer">
                                    <option :value="null">Select deployment branch...</option>
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{
                                        branch.name }}</option>
                                </select>
                                <div v-if="form.errors.branch_id" class="mt-1 text-[10px] font-bold text-rose-500">{{
                                    form.errors.branch_id }}</div>
                            </div>

                            <div class="space-y-1">
                                <label
                                    class="block text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">Access
                                    Privilege</label>
                                <select v-model="form.role_id" required
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all appearance-none cursor-pointer">
                                    <option :value="null">Select permission level...</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.role_id" class="mt-1 text-[10px] font-bold text-rose-500">{{
                                    form.errors.role_id }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-8 flex justify-end items-center gap-6">
                        <Link :href="route('devAdmin.users.index')"
                            class="text-sm font-black text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 uppercase tracking-widest transition-colors">
                            Discard
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-[0.15em] text-xs hover:bg-indigo-700 active:scale-95 transition-all shadow-xl shadow-indigo-500/25 disabled:opacity-50 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">{{ isEdit ? 'save_as' : 'verified_user'
                                }}</span>
                            {{ isEdit ? 'Commit Changes' : 'Initialize Account' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'

const props = defineProps({
    user: { type: Object, default: () => ({}) },
    branches: { type: Array, default: () => [] },
    roles: { type: Array, default: () => [] },
    isEdit: { type: Boolean, default: false }
})

const form = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    password: '',
    password_confirmation: '',
    branch_id: props.user.branch_id || null,
    role_id: props.user.role_id || null,
})

const submit = () => {
    if (props.isEdit) {
        form.put(route('devAdmin.users.update', props.user.id))
    } else {
        form.post(route('devAdmin.users.store'))
    }
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
