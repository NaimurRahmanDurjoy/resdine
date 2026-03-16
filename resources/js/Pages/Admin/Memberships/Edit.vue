<template>
    <MembershipForm :membership="membership" :pageTitle="pageTitle" />
</template>

<script setup>
import MembershipForm from './Form.vue'

defineProps({
    membership: Object,
    pageTitle: String
})
</script>
