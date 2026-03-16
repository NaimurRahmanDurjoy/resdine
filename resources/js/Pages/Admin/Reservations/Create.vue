<template>
    <AdminLayout :pageTitle="pageTitle">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-2xl mx-auto">
            <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-800">{{ pageTitle }}</h1>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Branch</label>
                        <select v-model="form.branch_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Table</label>
                        <select v-model="form.table_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            <option v-for="table in filteredTables" :key="table.id" :value="table.id">
                                {{ table.name }} (Cap: {{ table.capacity }})
                            </option>
                        </select>
                        <div v-if="form.errors.table_id" class="text-red-500 text-xs mt-1">{{ form.errors.table_id }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reservation Time</label>
                        <input v-model="form.reservation_time" type="datetime-local" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Gests Count</label>
                        <input v-model="form.guests_count" type="number" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Customer Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Existing Customer</label>
                            <select v-model="form.customer_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option :value="null">Walk-in / New Customer</option>
                                <option v-for="customer in customers" :key="customer.id" :value="customer.id">{{ customer.name }} ({{ customer.phone }})</option>
                            </select>
                        </div>
                        
                        <div v-if="!form.customer_id" class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <input v-model="form.customer_name" type="text" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <input v-model="form.customer_phone" type="text" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Special Requests</label>
                    <textarea v-model="form.special_requests" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <Link :href="route('admin.reservations.index')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        Book Table
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    tables: Array,
    customers: Array,
    branches: Array,
    pageTitle: String
})

const form = useForm({
    branch_id: props.branches[0]?.id || '',
    table_id: '',
    reservation_time: '',
    guests_count: 2,
    customer_id: null,
    customer_name: '',
    customer_phone: '',
    special_requests: ''
})

const filteredTables = computed(() => {
    return props.tables.filter(t => t.branch_id === form.branch_id)
})

const submit = () => {
    form.post(route('admin.reservations.store'))
}
</script>
