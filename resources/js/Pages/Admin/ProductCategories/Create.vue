<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Add Menu Category</h1>
        <p class="text-gray-600 dark:text-gray-400">Create a new menu category for your restaurant</p>
      </div>
    </div>

    <Form :form="form" @submit="submit" />
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Form from './Form.vue'

defineOptions({ layout: AdminLayout })

const form = useForm({
  name: '',
  category_img: null,
  status: 1
})

const submit = () => {
  form.post(route('admin.product.categories.store'))
}
</script>
