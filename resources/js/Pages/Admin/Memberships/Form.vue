<template>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-2xl mx-auto">
            <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-800">{{ pageTitle }}</h1>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tier Name</label>
                    <input v-model="form.name" type="text" 
                        class="pl-2 mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="e.g. Gold" required>
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Discount Percentage (%)</label>
                        <input v-model="form.discount_percentage" type="number" step="0.01"
                            class="pl-2 mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required>
                        <div v-if="form.errors.discount_percentage" class="text-red-500 text-xs mt-1">{{ form.errors.discount_percentage }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Loyalty Multiplier</label>
                        <input v-model="form.loyalty_multiplier" type="number"
                            class="pl-2 mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required>
                        <div v-if="form.errors.loyalty_multiplier" class="text-red-500 text-xs mt-1">{{ form.errors.loyalty_multiplier }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Min Points</label>
                        <input v-model="form.min_points" type="number"
                            class="pl-2 mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required>
                        <div v-if="form.errors.min_points" class="text-red-500 text-xs mt-1">{{ form.errors.min_points }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Max Points (Optional)</label>
                        <input v-model="form.max_points" type="number"
                            class="pl-2 mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <div v-if="form.errors.max_points" class="text-red-500 text-xs mt-1">{{ form.errors.max_points }}</div>
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
                    <Link :href="route('admin.memberships.index')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        {{ membership ? 'Update Tier' : 'Create Tier' }}
                    </button>
                </div>
            </form>
        </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
    membership: Object,
    pageTitle: String
})

const form = useForm({
    name: props.membership?.name || '',
    discount_percentage: props.membership?.discount_percentage || 0,
    loyalty_multiplier: props.membership?.loyalty_multiplier || 1,
    min_points: props.membership?.min_points || 0,
    max_points: props.membership?.max_points || null,
    status: props.membership?.status ?? 1
})

const submit = () => {
    if (props.membership) {
        form.put(route('admin.memberships.update', props.membership.id))
    } else {
        form.post(route('admin.memberships.store'))
    }
}
</script>
