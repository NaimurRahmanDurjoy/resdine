<template>
    <MembershipForm :pageTitle="pageTitle" />
</template>

<script setup>
import MembershipForm from './Form.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout: AdminLayout })

defineProps({
    pageTitle: String
})
</script>
