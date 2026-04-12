<template>
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Purchase Order Details</h1>
                        <p class="text-gray-600 dark:text-gray-400">Reference: #{{ purchase.id }}</p>
                    </div>
                    <Link :href="route('admin.purchase.index')"
                        class="flex items-center text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 font-medium transition-colors">
                        <span class="material-symbols-outlined mr-1 text-base">arrow_back</span>
                        Back to List
                    </Link>
                </div>
            </div>

            <div class="p-8">
                <PurchaseDetails :purchase="purchase" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PurchaseDetails from '@/Components/Admin/Inventory/Purchase/PurchaseDetails.vue'
defineOptions({ layout: AdminLayout })

const props = defineProps({
    purchase: Object,
    pageTitle: String
})
</script>
