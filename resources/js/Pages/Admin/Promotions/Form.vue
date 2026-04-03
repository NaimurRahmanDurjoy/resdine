<template>
  <div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Header -->
      <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <div>
          <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ isEdit ? 'Edit Promotion' : 'Add New Promotion' }}</h1>
          <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Define discount rules and validity periods</p>
        </div>
        <Link :href="route('admin.promotions.index')" class="text-gray-500 hover:text-gray-700 flex items-center space-x-1">
          <span class="material-symbols-outlined text-sm">arrow_back</span>
          <span>Back to List</span>
        </Link>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Name -->
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Promotion Name</label>
            <input v-model="form.name" type="text" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white" placeholder="e.g. Summer Sale 2024" required>
            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
          </div>

          <!-- Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Discount Type</label>
            <select v-model="form.type" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
              <option value="percentage">Percentage (%)</option>
              <option value="fixed">Fixed Amount ($)</option>
            </select>
          </div>

          <!-- Value -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Discount Value</label>
            <input v-model="form.value" type="number" step="0.01" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white" required>
            <div v-if="form.errors.value" class="text-red-500 text-xs mt-1">{{ form.errors.value }}</div>
          </div>

          <!-- Start Date -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
            <input v-model="form.start_date" type="date" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white" required>
            <div v-if="form.errors.start_date" class="text-red-500 text-xs mt-1">{{ form.errors.start_date }}</div>
          </div>

          <!-- End Date -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date (Optional)</label>
            <input v-model="form.end_date" type="date" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
            <div v-if="form.errors.end_date" class="text-red-500 text-xs mt-1">{{ form.errors.end_date }}</div>
          </div>

          <!-- Status -->
          <div class="col-span-2">
            <label class="flex items-center space-x-3 cursor-pointer">
              <input v-model="form.status" type="checkbox" :value="1" :true-value="1" :false-value="0" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
            </label>
          </div>

          <!-- Applicable Items -->
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Applicable Menu Items (Select multiple)</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 max-h-60 overflow-y-auto p-4 border border-gray-200 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800/50">
              <label v-for="item in productItems" :key="item.id" class="flex items-center space-x-2 text-sm p-1 hover:bg-white dark:hover:bg-gray-700 rounded transition cursor-pointer">
                <input type="checkbox" :value="item.id" v-model="form.items" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <span class="text-gray-700 dark:text-gray-300 truncate">{{ item.name }}</span>
              </label>
            </div>
            <p class="text-xs text-gray-500 mt-2 italic">If no items are selected, the promotion may apply to all (depending on your business logic implementation).</p>
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
          <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition disabled:opacity-50">
            {{ isEdit ? 'Update Promotion' : 'Create Promotion' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  promotion: { type: Object, default: () => ({}) },
  productItems: Array,
  selectedItems: { type: Array, default: () => [] },
  isEdit: Boolean
})

const form = useForm({
  name: props.promotion.name || '',
  type: props.promotion.type || 'percentage',
  value: props.promotion.value || 0,
  start_date: props.promotion.start_date || '',
  end_date: props.promotion.end_date || '',
  status: props.promotion.status !== undefined ? props.promotion.status : 1,
  items: props.selectedItems || []
})

const submit = () => {
    if (props.isEdit) {
        form.put(route('admin.promotions.update', props.promotion.id))
    } else {
        form.post(route('admin.promotions.store'))
    }
}
</script>
