<template>
    <form @submit.prevent="$emit('submit', form)" class="space-y-6">
        <div class="grid grid-cols-1 gap-6">
            <!-- Name -->
            <div class="flex items-start gap-6">
                <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                <div class="flex-1">
                    <input v-model="form.name" type="text" placeholder="E.g. Grams"
                        class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                        required />
                </div>
            </div>

            <!-- Base Unit -->
            <div class="flex items-start gap-6">
                <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Parent Unit</label>
                <div class="flex-1">
                    <select v-model="form.base_unit_id"
                        class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm">
                        <option :value="null">None (This is a Base Unit)</option>
                        <option v-for="unit in baseUnits" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                    </select>
                    <p class="mt-1 text-[11px] text-gray-500">Select if this is a sub-unit (e.g. Grams is a sub-unit of Kilogram)</p>
                </div>
            </div>

            <!-- Conversion Factor -->
            <div v-if="form.base_unit_id" class="flex items-start gap-6">
                <label class="w-32 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Conversion Factor *</label>
                <div class="flex-1">
                    <input v-model="form.conversion_factor" type="number" step="0.000001" placeholder="0.001"
                        class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                        required />
                    
                    <!-- Dynamic Helper Message -->
                    <div v-if="selectedBaseUnit && form.name" class="mt-3 p-3 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-lg flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-500 text-sm">info</span>
                        <span class="text-xs font-medium text-indigo-700 dark:text-indigo-300">
                             1 {{ form.name }} = {{ form.conversion_factor }} {{ selectedBaseUnit.name }}
                        </span>
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
                    {{ isEdit ? 'Update Unit' : 'Create Unit' }}
                </button>
                <Link :href="route('admin.unit.index')"
                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    Cancel
                </Link>
            </div>
        </div>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    unit: Object,
    baseUnits: Array,
    isEdit: Boolean
})

const form = useForm({
    name: props.unit?.name ?? '',
    base_unit_id: props.unit?.base_unit_id ?? null,
    conversion_factor: props.unit?.conversion_factor ?? 1,
    status: props.unit?.status ?? 1
})

const selectedBaseUnit = computed(() => {
    return props.baseUnits?.find(u => u.id == form.base_unit_id)
})
</script>
