<template>
    <MembershipForm :membership="membership" :pageTitle="pageTitle" />
</template>

<script setup>
import MembershipForm from './Form.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout: AdminLayout })

defineProps({
    membership: Object,
    pageTitle: String
})
</script>
