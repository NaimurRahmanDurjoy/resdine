<template>
  <form @submit.prevent="$emit('submit', form)" class="space-y-8">
    <!-- Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 space-y-6">
      
      <!-- Name -->
      <div class="flex items-start gap-6">
        <label class="w-48 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Name *
        </label>
        <div class="flex-1">
          <input
            v-model="form.name"
            type="text"
            placeholder="Enter variant name"
            class="w-full h-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
            :class="{ 'border-red-500 focus:ring-red-500': form.errors.name }"
            required
          />
          <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
        </div>
      </div>

      <!-- Parent Item -->
      <div class="flex items-start gap-6">
        <label class="w-48 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Menu Item *
        </label>
        <div class="flex-1">
          <select
            v-model="form.item_id"
            class="w-full h-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
            :class="{ 'border-red-500 focus:ring-red-500': form.errors.item_id }"
            required
          >
            <option value="">Select Item</option>
            <option v-for="item in menuItems" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select>
          <p v-if="form.errors.item_id" class="mt-1 text-sm text-red-500">{{ form.errors.item_id }}</p>
        </div>
      </div>

      <!-- Price -->
      <div class="flex items-start gap-6">
        <label class="w-48 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Price *
        </label>
        <div class="flex-1 relative">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">$</span>
          <input
            v-model="form.price"
            type="number"
            step="0.01"
            placeholder="0.00"
            class="w-full h-10 pl-8 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
            :class="{ 'border-red-500 focus:ring-red-500': form.errors.price }"
            required
          />
          <p v-if="form.errors.price" class="mt-1 text-sm text-red-500">{{ form.errors.price }}</p>
        </div>
      </div>

    </div>

    <!-- Form Buttons -->
    <div class="flex justify-start gap-3">
      <Link :href="route('admin.product.variants.index')"
        class="px-4 py-2 rounded text-sm border bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition font-medium"
      >
        Cancel
      </Link>
      <button
        type="submit"
        :disabled="form.processing"
        class="px-6 py-2 rounded text-sm bg-indigo-600 text-white font-bold shadow hover:bg-indigo-700 transition disabled:opacity-50 flex items-center"
      >
        <span class="material-symbols-outlined mr-2 text-sm">save</span>
        {{ isEdit ? 'Update Variant' : 'Save Variant' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  form: Object,
  menuItems: Array,
  isEdit: { type: Boolean, default: false }
})

defineEmits(['submit'])
</script>
