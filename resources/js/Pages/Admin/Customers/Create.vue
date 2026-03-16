<template>
    <CustomerForm :memberships="memberships" :pageTitle="pageTitle" />
</template>

<script setup>
import CustomerForm from './Form.vue'

defineProps({
    memberships: Array,
    pageTitle: String
})
</script>
