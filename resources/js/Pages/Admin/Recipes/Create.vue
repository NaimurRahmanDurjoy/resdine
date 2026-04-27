<template>
  <div class="max-w-6xl mx-auto py-2">
    <div
      class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
      <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Create New Recipe</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Define ingredients and proportions for a menu item</p>
      </div>

      <div class="p-4">
        <RecipeForm :form="form" :menu-items="menuItems" :ingredients="ingredients" :prep-items="prepItems"
          :units="units" :branches="branches" :initial-product-id="initialProductId" @submit="submit" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import RecipeForm from './Form.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  menuItems: Array,
  ingredients: Array,
  prepItems: Array,
  units: Array,
  branches: Array,
  initialProductId: [String, Number]
})

const form = useForm({
  menu_item_id: props.initialProductId || '',
  variant_id: '',
  branch_id: '',
  items: [
    { ingredient_id: '', quantity: 1, unit_id: '' }
  ]
})

const submit = () => {
  form.post(route('admin.recipes.store'))
}
</script>
