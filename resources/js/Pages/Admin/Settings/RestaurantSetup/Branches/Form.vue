<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 gap-6">
            <!-- Name -->
            <div class="flex items-start gap-6">
                <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                <div class="flex-1">
                    <input v-model="form.name" type="text" placeholder="E.g. Main Branch, Downtown Location"
                        class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                        required />
                    <p v-if="form.errors.name" class="text-sm text-red-500 mt-1">{{ form.errors.name }}</p>
                </div>
            </div>

            <!-- Location -->
            <div class="flex items-start gap-6">
                <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                <div class="flex-1">
                    <textarea v-model="form.location" rows="2" placeholder="Enter branch address or location details"
                        class="w-full border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 py-2 text-sm"></textarea>
                    <p v-if="form.errors.location" class="text-sm text-red-500 mt-1">{{ form.errors.location }}</p>
                </div>
            </div>

            <!-- Status -->
            <div class="flex items-center gap-6">
                <label class="w-32 text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <div class="flex-1">
                    <div class="flex items-center gap-6">
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" v-model="form.status" :value="1"
                                class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 transition-colors">Active</span>
                        </label>
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" v-model="form.status" :value="0"
                                class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 transition-colors">Inactive</span>
                        </label>
                        <p v-if="form.errors.status" class="text-sm text-red-500 mt-1">{{ form.errors.status }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <button type="submit"
                    class="px-8 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">save</span>
                    {{ isEdit ? 'Update Branch' : 'Create Branch' }}
                </button>
                <Link :href="route('admin.settings.restaurant-setup.branches.index')"
                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    Cancel
                </Link>
            </div>
        </div>
    </form>
</template>

<script setup>

import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    branch: Object,
    isEdit: Boolean
})

const form = useForm({
    name: props.branch?.name || '',
    location: props.branch?.location || '',
    status: props.branch?.status ?? 1
})

const submit = () => {
    if (props.isEdit) {
        form.put(route('admin.settings.restaurant-setup.branches.update', props.branch.id))
    } else {
        form.post(route('admin.settings.restaurant-setup.branches.store'))
    }
}

</script>
