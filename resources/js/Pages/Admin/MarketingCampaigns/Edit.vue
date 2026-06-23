<template>
    <div class="max-w-5xl mx-auto py-8">
        <Form :form="form" :pageTitle="pageTitle" :existingImage="campaign.full_image_url" @submit="submit" />
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Form from './Form.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    campaign: Object,
    pageTitle: String
})

const form = useForm({
    _method: 'PUT', // For multipart/form-data update in Laravel
    title: props.campaign.title,
    image: null,
    link_url: props.campaign.link_url,
    type: props.campaign.type,
    priority: props.campaign.priority,
    starts_at: props.campaign.starts_at ? new Date(props.campaign.starts_at).toISOString().slice(0, 16) : null,
    ends_at: props.campaign.ends_at ? new Date(props.campaign.ends_at).toISOString().slice(0, 16) : null,
    is_active: props.campaign.is_active == 1
})

const submit = () => {
    form.post(route('admin.marketing-campaigns.update', props.campaign.id), {
        forceFormData: true
    })
}
</script>
