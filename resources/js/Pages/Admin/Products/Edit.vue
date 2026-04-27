<template>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Menu Item</h1>
            <p class="text-gray-600 dark:text-gray-400">Update menu item details and combo settings</p>
          </div>
          <Link :href="route('admin.product.items.index')" class="flex items-center text-sm text-indigo-600 hover:text-indigo-900 transition-colors">
            <span class="material-symbols-outlined mr-1 text-base">arrow_back</span>
            Back to Menu Items
          </Link>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <Form 
      :form="form" 
      :categories="categories"
      :units="units"
      :departments="departments"
      :menuItems="menuItems"
      :existingImage="product.image_url"
      :is-edit="true"
      @submit="submit" 
    />
  </div>
</div>  
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Form from './Form.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  product: Object,
  categories: Array,
  units: Array,
  departments: Array,
  menuItems: Array,
  comboItemIds: Array
})

const form = useForm({
  _method: 'PUT',
  category_id: props.product.category_id,
  type: props.product.type,
  name: props.product.name,
  description: props.product.description || '',
  price: props.product.price,
  cost_price: props.product.cost_price || '',
  unit_id: props.product.unit_id,
  department_id: props.product.department_id,
  status: props.product.status,
  is_featured: props.product.is_featured,
  is_prep_item: props.product.is_prep_item,
  menu_img: null,
  combo_items: props.comboItemIds || [],
  combo_discount: props.product.combo_discount || 0,
  combo_final_price: props.product.combo_final_price || ''
})

const submit = () => {
  form.post(route('admin.product.items.update', props.product.id))
}
</script>
