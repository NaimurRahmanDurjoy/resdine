<template>
    <CustomerForm :customer="customer" :memberships="memberships" :pageTitle="pageTitle" />
</template>

<script setup>
import CustomerForm from './Form.vue'

defineProps({
    customer: Object,
    memberships: Array,
    pageTitle: String
})
</script>
