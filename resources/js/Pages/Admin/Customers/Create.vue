<template>
    <CustomerForm :memberships="memberships" :pageTitle="pageTitle" />
</template>

<script setup>
import CustomerForm from './Form.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout: AdminLayout })

defineProps({
    memberships: Array,
    pageTitle: String
})
</script>
