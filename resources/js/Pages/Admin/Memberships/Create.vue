<template>
    <MembershipForm :pageTitle="pageTitle" />
</template>

<script setup>
import MembershipForm from './Form.vue'

defineProps({
    pageTitle: String
})
</script>
