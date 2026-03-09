<template>
    <DevAdminLayout>

        <Head :title="isEdit ? 'Update Software Menu' : 'Add Software Menu'" />

        <div class="max-w-4xl mx-auto">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div
                    class="bg-gradient-to-r from-emerald-50 to-white dark:from-emerald-900/20 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ isEdit ? 'Update Software
                                Menu' : 'Add Software Menu' }}</h1>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ isEdit ? 'Modify global
                                navigation item' : 'Create new public facing navigation' }}</p>
                        </div>
                        <Link :href="route('devAdmin.systemConfig.software.menu.index')"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                            <span class="material-symbols-outlined font-icon">close</span>
                        </Link>
                    </div>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Menu Name & Route -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                                Menu Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" v-model="form.name" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all shadow-sm"
                                :class="{ 'border-red-500 focus:ring-red-500': form.errors.name }">
                            <div v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                                Route
                            </label>
                            <input type="text" v-model="form.route"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all shadow-sm"
                                :class="{ 'border-red-500 focus:ring-red-500': form.errors.route }">
                            <div v-if="form.errors.route" class="mt-1 text-xs text-red-500">{{ form.errors.route }}
                            </div>
                        </div>
                    </div>

                    <!-- Icon & Parent -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                                Icon (Material Symbol)
                            </label>
                            <div class="relative">
                                <input type="text" v-model="form.icon" placeholder="e.g. store, shopping_cart"
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all shadow-sm">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-2 text-gray-400 text-xl font-icon">{{
                                    form.icon || 'star' }}</span>
                            </div>
                            <div v-if="form.errors.icon" class="mt-1 text-xs text-red-500">{{ form.errors.icon }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                                Parent Menu
                            </label>
                            <select v-model="form.parent_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all shadow-sm">
                                <option :value="null">None (Root Level)</option>
                                <option v-for="parent in parents" :key="parent.id" :value="parent.id">
                                    {{ parent.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.parent_id" class="mt-1 text-xs text-red-500">{{ form.errors.parent_id
                                }}</div>
                        </div>
                    </div>

                    <!-- Order & Active -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                                Menu Order
                            </label>
                            <input type="number" v-model="form.order"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all shadow-sm">
                            <div v-if="form.errors.order" class="mt-1 text-xs text-red-500">{{ form.errors.order }}
                            </div>
                        </div>

                        <div class="flex items-center mt-6">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="form.is_active" class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 dark:peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600 transition-all">
                                </div>
                                <span class="ml-3 text-sm font-bold text-gray-700 dark:text-gray-300">{{ form.is_active
                                    ? 'Active' : 'Inactive' }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                        <Link :href="route('devAdmin.systemConfig.software.menu.index')"
                            class="px-5 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="bg-emerald-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-emerald-700 active:scale-95 transition-all shadow-lg shadow-emerald-500/20 disabled:opacity-50">
                            {{ isEdit ? 'Update Software' : 'Save Software' }}
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
    menu: { type: Object, default: () => ({}) },
    parents: { type: Array, default: () => [] },
    isEdit: { type: Boolean, default: false }
})

const form = useForm({
    name: props.menu?.name ?? '',
    route: props.menu?.route ?? '',
    icon: props.menu?.icon ?? '',
    parent_id: props.menu?.parent_id ?? null,
    order: props.menu?.order ?? 0,
    is_active: props.menu?.is_active !== undefined ? Boolean(props.menu.is_active) : true,
})

const submit = () => {
    if (props.isEdit) {
        form.put(route('devAdmin.systemConfig.software.menu.update', props.menu.id))
    } else {
        form.post(route('devAdmin.systemConfig.software.menu.store'))
    }
}
</script>

<style scoped>
.font-icon {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
