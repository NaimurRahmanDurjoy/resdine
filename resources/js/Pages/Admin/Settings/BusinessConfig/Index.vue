<template>
    <AdminLayout :pageTitle="pageTitle">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-4xl mx-auto">
            <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-800">{{ pageTitle }}</h1>
                <p class="text-sm text-gray-500">Manage global restaurant variables like VAT, Currency, and more.</p>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-8">
                <div v-for="(groupConfigs, groupName) in configs" :key="groupName" class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-2">
                        {{ groupName }} Settings
                    </h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div v-for="config in groupConfigs" :key="config.key">
                            <label class="block text-sm font-medium text-gray-700 capitalize">
                                {{ config.key.replace(/_/g, ' ') }}
                            </label>
                            <input v-model="formConfigs[config.key]" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Default fields if table is empty -->
                <div v-if="Object.keys(configs).length === 0" class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-2">General Settings</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Currency Symbol</label>
                            <input v-model="formConfigs.currency" type="text" placeholder="$"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">VAT (%)</label>
                            <input v-model="formConfigs.vat_percentage" type="text" placeholder="15"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" :disabled="form.processing"
                        class="px-6 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">save</span>
                        Save All Configurations
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    configs: Object,
    pageTitle: String
})

const formConfigs = reactive({})

onMounted(() => {
    // Flatten and populate form
    Object.values(props.configs).flat().forEach(c => {
        formConfigs[c.key] = c.value
    })
    
    // Ensure defaults if empty
    if (!formConfigs.currency) formConfigs.currency = '$'
    if (!formConfigs.vat_percentage) formConfigs.vat_percentage = '0'
})

const form = useForm({
    configs: []
})

const submit = () => {
    form.configs = Object.entries(formConfigs).map(([key, value]) => ({
        key,
        value,
        group: findGroup(key)
    }))
    
    form.post(route('admin.business-config.update'), {
        preserveScroll: true
    })
}

const findGroup = (key) => {
    for (const [group, items] of Object.entries(props.configs)) {
        if (items.find(i => i.key === key)) return group
    }
    return 'general'
}
</script>
