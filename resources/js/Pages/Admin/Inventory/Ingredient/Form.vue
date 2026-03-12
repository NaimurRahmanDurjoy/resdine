<template>
    <form @submit.prevent="$emit('submit', form)" class="space-y-6">
        <div class="grid grid-cols-1 gap-6">
            <!-- Name -->
            <div class="flex items-start gap-6">
                <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                <div class="flex-1">
                    <input v-model="form.name" type="text" placeholder="Ingredient Name (e.g. Flour, Sugar)"
                        class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                        required />
                </div>
            </div>

            <!-- Unit -->
            <div class="flex items-start gap-6">
                <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Unit *</label>
                <div class="flex-1">
                    <select v-model="form.unit_id"
                        class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                        required>
                        <option value="">Select a unit...</option>
                        <option v-for="unit in units" :key="unit.id" :value="unit.id">
                            {{ unit.name }} {{ unit.short_name ? `(${unit.short_name})` : '' }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Minimum Stock -->
            <div class="flex items-start gap-6">
                <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Min Stock *</label>
                <div class="flex-1">
                    <input v-model="form.min_stock" type="number" step="0.01" min="0" placeholder="Minimum stock alert level"
                        class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                        required />
                    <p class="mt-1 text-xs text-gray-500">Alert triggers when stock falls below this level</p>    
                </div>
            </div>

            <!-- Expiry Tracking -->
            <div class="flex items-center gap-6">
                <label class="w-32 text-sm font-medium text-gray-700 dark:text-gray-300">Track Expiry?</label>
                <div class="flex-1">
                    <div class="flex items-center gap-6">
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" v-model="form.expiry_tracking" :value="1"
                                class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 transition-colors">Yes</span>
                        </label>
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" v-model="form.expiry_tracking" :value="0"
                                class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 transition-colors">No</span>
                        </label>
                    </div>
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
                    {{ isEdit ? 'Update Ingredient' : 'Create Ingredient' }}
                </button>
                <Link :href="route('admin.ingredients.index')"
                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    Cancel
                </Link>
            </div>
        </div>
    </form>
</template>

<script setup>
import { reactive } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    ingredient: Object,
    units: Array,
    isEdit: Boolean
})

const form = reactive({
    name: props.ingredient?.name || '',
    unit_id: props.ingredient?.unit_id || '',
    min_stock: props.ingredient?.min_stock || 0,
    expiry_tracking: props.ingredient?.expiry_tracking ?? 0,
    status: props.ingredient?.status ?? 1
})
</script>
