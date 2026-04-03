<template>
  <PromotionForm :product-items="productItems" :is-edit="false" />
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PromotionForm from './Form.vue'

defineOptions({ layout: AdminLayout })

defineProps({
  productItems: Array
})
</script>
