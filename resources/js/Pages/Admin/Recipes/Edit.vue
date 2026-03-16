<template>
  <div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
      <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Recipe</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Update ingredients for {{ recipe.menu_item?.name }}</p>
      </div>
      
      <div class="p-6">
        <RecipeForm 
          :form="form" 
          :menu-items="menuItems" 
          :ingredients="ingredients" 
          :units="units" 
          :branches="branches" 
          is-edit
          @submit="submit" />
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
  recipe: Object,
  menuItems: Array,
  ingredients: Array,
  units: Array,
  branches: Array
})

const form = useForm({
  _method: 'PUT',
  menu_item_id: props.recipe.menu_item_id,
  variant_id: props.recipe.variant_id || '',
  branch_id: props.recipe.branch_id || '',
  items: props.recipe.recipe_items.map(item => ({
    ingredient_id: item.ingredient_id,
    quantity: item.quantity,
    unit_id: item.unit_id
  }))
})

const submit = () => {
  form.post(route('admin.recipes.update', props.recipe.id))
}
</script>
