<template>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-2xl mx-auto">
            <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-800">{{ pageTitle }}</h1>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input v-model="form.name" type="text" 
                            class="pl-2 mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="John Doe" required>
                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input v-model="form.email" type="email" 
                            class="pl-2 mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="john@example.com">
                        <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input v-model="form.phone" type="text" 
                            class="pl-2 mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="+1234567890">
                        <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Membership Tier</label>
                            <select v-model="form.membership_id"
                                class="pl-2 mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option :value="null">None</option>
                                <option v-for="membership in memberships" :key="membership.id" :value="membership.id">
                                    {{ membership.name }} ({{ membership.discount_percentage }}% discount)
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Card Number</label>
                            <input v-model="form.card_no" type="text" 
                                class="pl-2 mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="CARD-12345" :disabled="!form.membership_id">
                            <div v-if="form.errors.card_no" class="text-red-500 text-xs mt-1">{{ form.errors.card_no }}</div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select v-model="form.status"
                        class="pl-2 mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option :value="1">Active</option>
                        <option :value="0">Inactive</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <Link :href="route('admin.customers.index')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        {{ customer ? 'Update Customer' : 'Create Customer' }}
                    </button>
                </div>
            </form>
        </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
    customer: Object,
    memberships: Array,
    pageTitle: String
})

const form = useForm({
    name: props.customer?.name || '',
    email: props.customer?.email || '',
    phone: props.customer?.phone || '',
    status: props.customer?.status ?? 1,
    membership_id: props.customer?.memberships?.[0]?.id || null,
    card_no: props.customer?.memberships?.[0]?.pivot?.card_no || ''
})

const submit = () => {
    if (props.customer) {
        form.put(route('admin.customers.update', props.customer.id))
    } else {
        form.post(route('admin.customers.store'))
    }
}
</script>
