<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div
                class="space-y-6 bg-gray-50 dark:bg-gray-900/50 p-6 rounded-xl border border-gray-100 dark:border-gray-800">
                <!-- Branch Selection -->
                <div class="flex items-start gap-6">
                    <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Branch *</label>
                    <div class="flex-1">
                        <select v-model="form.branch_id"
                            class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                            required>
                            <option value="">Select Branch</option>
                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.branch_id" class="text-sm text-red-500 mt-1">{{ form.errors.branch_id }}</p>
                    </div>
                </div>

                <!-- Name -->
                <div class="flex items-start gap-6">
                    <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Table Name *</label>
                    <div class="flex-1">
                        <input v-model="form.name" type="text" placeholder="E.g. Table 01, VIP-A"
                            class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                            required />
                        <p v-if="form.errors.name" class="text-sm text-red-500 mt-1">{{ form.errors.name }}</p>
                    </div>
                </div>

                <!-- Capacity -->
                <div class="flex items-start gap-6">
                    <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Capacity *</label>
                    <div class="flex-1">
                        <input v-model="form.capacity" type="number" min="1"
                            class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                            required />
                        <p v-if="form.errors.capacity" class="text-sm text-red-500 mt-1">{{ form.errors.capacity }}</p>
                    </div>
                </div>
            </div>

            <div
                class="space-y-6 bg-gray-50 dark:bg-gray-900/50 p-6 rounded-xl border border-gray-100 dark:border-gray-800">
                <!-- Section -->
                <div class="flex items-start gap-6">
                    <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Section</label>
                    <div class="flex-1">
                        <input v-model="form.section" type="text" placeholder="E.g. Ground Floor, VIP Hall"
                            class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm" />
                        <p v-if="form.errors.section" class="text-sm text-red-500 mt-1">{{ form.errors.section }}</p>
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
                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 transition-colors">Available</span>
                            </label>
                            <label class="flex items-center cursor-pointer group">
                                <input type="radio" v-model="form.status" :value="0"
                                    class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <span
                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 transition-colors">Unavailable</span>
                            </label>
                            <p v-if="form.errors.status" class="text-sm text-red-500 mt-1">{{ form.errors.status }}</p>
                        </div>
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
                    {{ isEdit ? 'Update Table' : 'Create Table' }}
                </button>
                <Link :href="route('admin.settings.restaurant-setup.tables.index')"
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
    table: Object,
    branches: Array,
    isEdit: Boolean
})

const form = useForm({
    name: props.table?.name || '',
    branch_id: props.table?.branch_id || '',
    capacity: props.table?.capacity || 4,
    section: props.table?.section || '',
    status: props.table?.status ?? 1
})
const submit = () => {
    if (props.isEdit) {
        form.put(route('admin.settings.restaurant-setup.tables.update', props.table.id), {
            preserveScroll: true
        })
    } else {
        form.post(route('admin.settings.restaurant-setup.tables.store'), {
            preserveScroll: true
        })
    }
}
</script>
