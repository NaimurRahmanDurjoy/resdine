<template>
        <div class="max-w-5xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Header -->
                <div
                    class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Dining Table</h1>
                            <p class="text-gray-600 dark:text-gray-400">Update table capacity or status</p>
                        </div>
                        <Link :href="route('admin.settings.restaurant-setup.tables.index')"
                            class="flex items-center text-sm text-indigo-600 hover:text-indigo-900 font-medium transition-colors">
                            <span class="material-symbols-outlined mr-1 text-base">arrow_back</span>
                            Back to List
                        </Link>
                    </div>
                </div>

                <!-- Form Body -->
                <div class="p-8">
                    <Form :table="table" :branches="branches" :isEdit="true" />
                </div>
            </div>
        </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Form from './Form.vue'
defineOptions({ layout: AdminLayout })

const props = defineProps({
    table: Object,
    branches: Object,
    pageTitle: String
})
</script>
