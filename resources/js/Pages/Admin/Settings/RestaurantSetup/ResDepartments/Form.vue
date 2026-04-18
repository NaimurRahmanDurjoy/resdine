<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 gap-6">
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
                <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                <div class="flex-1">
                    <input v-model="form.name" type="text" placeholder="E.g. Kitchen, Juice Bar, Bakery"
                        class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                        required />
                    <p v-if="form.errors.name" class="text-sm text-red-500 mt-1">{{ form.errors.name }}</p>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <button type="submit"
                    class="px-8 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">save</span>
                    {{ isEdit ? 'Update Department' : 'Create Department' }}
                </button>
                <Link :href="route('admin.settings.restaurant-setup.res-departments.index')"
                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    Cancel
                </Link>
            </div>
        </div>
    </form>
</template>

<script setup>
import { Link , useForm} from '@inertiajs/vue3'

const props = defineProps({
    department: Object,
    branches: Object,
    isEdit: Boolean
})

const form = useForm({
    name: props.department?.name || '',
    branch_id: props.department?.branch_id || ''
})

const submit = () => {
    if(props.isEdit){
        form.put(route('admin.settings.restaurant-setup.res-departments.update', props.department.id))
    } else {
        form.post(route('admin.settings.restaurant-setup.res-departments.store'))
    }
}

</script>
