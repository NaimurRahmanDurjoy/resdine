<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Menu Category</h1>
        <p class="text-gray-600 dark:text-gray-400">Update category details</p>
      </div>
    </div>

    <Form :form="form" :existingImage="category.image_url" :is-edit="true" @submit="submit" />
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Form from './Form.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  category: Object
})

const form = useForm({
  _method: 'PUT',
  name: props.category.name,
  category_img: null,
  status: props.category.status
})

const submit = () => {
  // Use POST with _method=PUT for multipart/form-data support in Laravel
  form.post(route('admin.product.categories.update', props.category.id))
}
</script>
