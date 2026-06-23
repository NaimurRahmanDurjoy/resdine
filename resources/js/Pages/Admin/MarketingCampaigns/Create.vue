<template>
    <div class="max-w-5xl mx-auto py-8">
        <Form :form="form" :pageTitle="pageTitle" @submit="submit" />
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Form from './Form.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    pageTitle: String
})

const form = useForm({
    title: '',
    image: null,
    link_url: '',
    type: 'popup',
    priority: 0,
    starts_at: null,
    ends_at: null,
    is_active: true
})

const submit = () => {
    form.post(route('admin.marketing-campaigns.store'), {
        forceFormData: true
    })
}
</script>
