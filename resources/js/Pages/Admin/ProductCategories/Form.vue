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
            placeholder="Enter category name"
            class="w-full h-10 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3"
            :class="{ 'border-red-500 focus:ring-red-500': form.errors.name }"
            required
          />
          <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
        </div>
      </div>

      <!-- Image -->
      <div class="flex items-start gap-6">
        <label class="w-48 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Category Image
        </label>
        <div class="flex-1">
          <ImageUploadPreview 
            name="category_img" 
            :existingImage="existingImage"
            @change="form.category_img = $event.target.files[0]"
          />
          <p v-if="form.errors.category_img" class="mt-1 text-sm text-red-500">{{ form.errors.category_img }}</p>
        </div>
      </div>

      <!-- Status -->
      <div class="flex items-start gap-6">
        <label class="w-48 pt-2 text-sm font-medium text-gray-700 dark:text-gray-300">
          Status
        </label>
        <div class="flex-1 flex items-center h-10">
          <input 
            v-model="form.status"
            type="checkbox" 
            id="status"
            :true-value="1"
            :false-value="0"
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer"
          >
          <label for="status" class="ml-2 block text-sm text-gray-700 dark:text-gray-400 cursor-pointer">
            Active Category
          </label>
        </div>
      </div>

    </div>

    <!-- Form Buttons -->
    <div class="flex justify-start gap-3">
      <Link :href="route('admin.product.categories.index')"
        class="px-4 py-2 rounded text-sm border bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition"
      >
        Cancel
      </Link>
      <button
        type="submit"
        :disabled="form.processing"
        class="px-4 py-2 rounded text-sm bg-indigo-600 text-white font-medium shadow hover:bg-indigo-700 transition disabled:opacity-50 flex items-center"
      >
        <span class="material-symbols-outlined mr-2 text-sm">save</span>
        {{ isEdit ? 'Update Category' : 'Save Category' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import ImageUploadPreview from '@/Components/ImageUploadPreview.vue'

const props = defineProps({
  form: Object,
  existingImage: { type: String, default: null },
  isEdit: { type: Boolean, default: false }
})

defineEmits(['submit'])
</script>
