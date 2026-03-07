<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Menu Item</h1>
        <p class="text-gray-600 dark:text-gray-400">Update menu item details and combo settings</p>
      </div>
    </div>

    <Form 
      :form="form" 
      :categories="categories"
      :units="units"
      :departments="departments"
      :menuItems="menuItems"
      :existingImage="product.menu_img"
      :is-edit="true"
      @submit="submit" 
    />
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
  menu_img: null,
  combo_items: props.comboItemIds || [],
  combo_discount: props.product.combo_discount || 0,
  combo_final_price: props.product.combo_final_price || ''
})

const submit = () => {
  form.post(route('admin.product.items.update', props.product.id))
}
</script>
