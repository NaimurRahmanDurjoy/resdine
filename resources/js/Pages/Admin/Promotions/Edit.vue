<template>
  <PromotionForm 
    :promotion="promotion" 
    :product-items="productItems" 
    :selected-items="selectedItems" 
    :is-edit="true" 
  />
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PromotionForm from './Form.vue'

defineOptions({ layout: AdminLayout })

defineProps({
  promotion: Object,
  productItems: Array,
  selectedItems: Array
})
</script>
