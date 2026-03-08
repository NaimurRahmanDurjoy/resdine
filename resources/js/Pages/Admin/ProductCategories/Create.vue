<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div
        class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Add Menu Category</h1>
            <p class="text-gray-600 dark:text-gray-400">Create a new menu category for your restaurant</p>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 p-6">
      <Form :form="form" @submit="submit" />
    </div>
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
