<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 gap-6">
            <!-- Name -->
            <div class="flex items-start gap-6">
                <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                <div class="flex-1">
                    <input v-model="form.name" type="text" placeholder="Contact Person Name"
                        class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                        required />
                </div>
            </div>

            <!-- Company Name -->
            <div class="flex items-start gap-6">
                <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Company</label>
                <div class="flex-1">
                    <input v-model="form.company_name" type="text" placeholder="Company Name"
                        class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm" />
                </div>
            </div>

            <!-- Phone -->
            <div class="flex items-start gap-6">
                <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                <div class="flex-1">
                    <input v-model="form.phone" type="text" placeholder="Phone Number"
                        class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm" />
                </div>
            </div>

            <!-- Email -->
            <div class="flex items-start gap-6">
                <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <div class="flex-1">
                    <input v-model="form.email" type="email" placeholder="Email Address"
                        class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm" />
                </div>
            </div>

            <!-- Address -->
            <div class="flex items-start gap-6">
                <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                <div class="flex-1">
                    <textarea v-model="form.address" rows="3" placeholder="Full Address"
                        class="w-full border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 py-2 text-sm"></textarea>
                </div>
            </div>

        </div>

        <!-- Form Actions -->
        <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <button type="submit"
                    class="px-8 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">save</span>
                    {{ isEdit ? 'Update Supplier' : 'Create Supplier' }}
                </button>
                <Link :href="route('admin.suppliers.index')"
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
    supplier: Object,
    isEdit: Boolean
})

const form = useForm({
    name: props.supplier?.name || '',
    phone: props.supplier?.phone || '',
    email: props.supplier?.email || '',
    address: props.supplier?.address || '',
    company_name: props.supplier?.company_name || ''
})
const submit = () => {
    if (props.isEdit) {
        form.put(route('admin.suppliers.update', props.supplier.id))
    } else {
        form.post(route('admin.suppliers.store'))
    }
}

</script>
