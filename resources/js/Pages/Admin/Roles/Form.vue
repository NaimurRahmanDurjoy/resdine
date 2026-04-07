<template>
    <AdminLayout :pageTitle="pageTitle">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <Link :href="route('admin.settings.roles.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                            <span class="material-symbols-outlined font-icon">arrow_back</span>
                        </Link>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white uppercase tracking-wider">{{ pageTitle }}</h1>
                    </div>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Role Name -->
                    <div class="space-y-1.5">
                        <label for="name" class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Role Name</label>
                        <input type="text" v-model="form.name" id="name" placeholder="e.g. Cashier, Manager..."
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all dark:text-white"
                            :class="{ 'border-rose-500 ring-1 ring-rose-500/20': form.errors.name }">
                        <span v-if="form.errors.name" class="text-[10px] text-rose-500 font-bold px-1 uppercase tracking-tighter">{{ form.errors.name }}</span>
                    </div>

                    <!-- Description -->
                    <div class="space-y-1.5">
                        <label for="description" class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Role Description</label>
                        <textarea v-model="form.description" id="description" rows="3" placeholder="Briefly describe the responsibilities of this role..."
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all dark:text-white"
                            :class="{ 'border-rose-500 ring-1 ring-rose-500/20': form.errors.description }"></textarea>
                        <span v-if="form.errors.description" class="text-[10px] text-rose-500 font-bold px-1 uppercase tracking-tighter">{{ form.errors.description }}</span>
                    </div>

                    <!-- Status -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Status</label>
                        <div class="flex items-center space-x-4 p-1 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 w-max">
                             <button type="button" @click="form.status = true"
                                class="px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-widest transition-all"
                                :class="form.status ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'text-gray-400 hover:text-gray-600'">
                                Active
                             </button>
                             <button type="button" @click="form.status = false"
                                class="px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-widest transition-all"
                                :class="!form.status ? 'bg-gray-400 text-white shadow-lg' : 'text-gray-400 hover:text-gray-600'">
                                Inactive
                             </button>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-50 dark:border-gray-800 flex justify-end">
                        <button type="submit" :disabled="form.processing"
                            class="bg-blue-600 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-blue-700 active:scale-95 transition-all shadow-xl shadow-blue-500/20 disabled:opacity-50 flex items-center gap-2">
                             <span v-if="form.processing" class="material-symbols-outlined animate-spin text-sm">sync</span>
                             <span>{{ isEdit ? 'Update' : 'Create' }} Role</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    role: Object,
    isEdit: Boolean,
    pageTitle: String
})

const form = useForm({
    name: props.role?.name ?? '',
    description: props.role?.description ?? '',
    status: props.role?.status ?? true
})

const submit = () => {
    if (props.isEdit) {
        form.put(route('admin.settings.roles.update', props.role.id))
    } else {
        form.post(route('admin.settings.roles.store'))
    }
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
