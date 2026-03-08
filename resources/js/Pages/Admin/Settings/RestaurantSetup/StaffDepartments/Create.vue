<template>
    <AdminLayout :pageTitle="pageTitle">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Header -->
                <div
                    class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Add Staff Department</h1>
                            <p class="text-gray-600 dark:text-gray-400">Organize your personnel into functional groups
                            </p>
                        </div>
                        <Link :href="route('admin.settings.restaurant-setup.staff-departments.index')"
                            class="flex items-center text-sm text-indigo-600 hover:text-indigo-900 font-medium transition-colors">
                            <span class="material-symbols-outlined mr-1 text-base">arrow_back</span>
                            Back to List
                        </Link>
                    </div>
                </div>

                <!-- Form Body -->
                <div class="p-8">
                    <Form :department="null" :branches="branches" :isEdit="false" @submit="submit" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Form from './Form.vue'

const props = defineProps({
    branches: Array,
    pageTitle: String
})

const submit = (form) => {
    router.post(route('admin.settings.restaurant-setup.staff-departments.store'), form)
}
</script>
