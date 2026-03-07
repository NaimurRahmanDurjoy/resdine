<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Add Menu Variant</h1>
        <p class="text-gray-600 dark:text-gray-400">Create a new variant for a menu item</p>
      </div>
    </div>

    <Form :form="form" :menuItems="menuItems" @submit="submit" />
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Form from './Form.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  menuItems: Array
})

const form = useForm({
  name: '',
  item_id: '',
  price: ''
})

const submit = () => {
  form.post(route('admin.product.variants.store'))
}
</script>
