<template>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-4xl mx-auto mb-10">
            <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-800">{{ pageTitle }}</h1>
                <p class="text-sm text-gray-500">Manage critical settings, tax rules, and branding for this branch.</p>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-8">
                <!-- Financial Settings -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-2">Financial & Tax Settings</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Currency</label>
                            <select v-model="form.currency_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option :value="null">-- Select Currency --</option>
                                <option v-for="c in currencies" :key="c.id" :value="c.id">{{ c.name }} ({{ c.symbol }})</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">VAT Registration No.</label>
                            <input v-model="form.vat_registration_no" type="text" placeholder="e.g. 123456789" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">VAT Percentage (%)</label>
                            <input v-model="form.vat_percentage" type="number" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Service Charge (%)</label>
                            <input v-model="form.service_charge_percentage" type="number" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div class="col-span-2 flex items-center">
                            <input v-model="form.is_vat_inclusive" id="is_vat_inclusive" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_vat_inclusive" class="ml-2 block text-sm text-gray-900">Prices Include VAT (Inclusive Tax)</label>
                        </div>
                    </div>
                </div>

                <!-- Operational Settings -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-2">Operational Settings</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Timezone</label>
                            <input v-model="form.timezone" type="text" placeholder="e.g. UTC, America/New_York" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Language Code</label>
                            <input v-model="form.language_code" type="text" placeholder="en" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Opening Time</label>
                            <input v-model="form.opening_time" type="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Closing Time</label>
                            <input v-model="form.closing_time" type="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Branding Settings -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-2">Branding & Receipts</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Receipt Header Title</label>
                            <input v-model="form.receipt_header_title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Invoice Prefix</label>
                            <input v-model="form.invoice_prefix" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Receipt Footer Text</label>
                            <textarea v-model="form.receipt_footer_text" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        </div>
                        <div class="col-span-2 flex items-center">
                            <input v-model="form.show_logo_on_receipt" id="show_logo_on_receipt" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="show_logo_on_receipt" class="ml-2 block text-sm text-gray-900">Show Logo on Receipts</label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button type="submit" :disabled="form.processing"
                        class="px-6 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">save</span>
                        Save Configuration
                    </button>
                </div>
            </form>
        </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout: AdminLayout })

const props = defineProps({
    settings: Object,
    currencies: Array,
    pageTitle: String
})

const form = useForm({
    currency_id: props.settings?.currency_id || null,
    vat_registration_no: props.settings?.vat_registration_no || '',
    vat_percentage: props.settings?.vat_percentage || 0,
    service_charge_percentage: props.settings?.service_charge_percentage || 0,
    is_vat_inclusive: props.settings?.is_vat_inclusive || false,
    timezone: props.settings?.timezone || 'UTC',
    language_code: props.settings?.language_code || 'en',
    opening_time: props.settings?.opening_time || '',
    closing_time: props.settings?.closing_time || '',
    receipt_header_title: props.settings?.receipt_header_title || '',
    receipt_footer_text: props.settings?.receipt_footer_text || '',
    show_logo_on_receipt: props.settings?.show_logo_on_receipt ?? true,
    invoice_prefix: props.settings?.invoice_prefix || 'INV',
})

const submit = () => {
    form.post(route('admin.settings.business-config.update'), {
        preserveScroll: true
    })
}
</script>
