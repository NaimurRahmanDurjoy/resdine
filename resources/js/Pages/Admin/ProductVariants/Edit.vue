<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div
        class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Menu Variant</h1>
            <p class="text-gray-600 dark:text-gray-400">Update variant details</p>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 p-6">
      <Form :form="form" :menuItems="menuItems" :is-edit="true" @submit="submit" />
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Form from './Form.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  variant: Object,
  menuItems: Array
})

const form = useForm({
  _method: 'PUT',
  name: props.variant.name,
  item_id: props.variant.item_id,
  price: props.variant.price
})

const submit = () => {
  form.post(route('admin.product.variants.update', props.variant.id))
}
</script>
