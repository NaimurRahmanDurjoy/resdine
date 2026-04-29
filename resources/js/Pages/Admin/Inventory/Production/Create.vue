<template>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Record New Production</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Deduct raw ingredients and increase stock of prepped items.</p>
            </div>
            <Link :href="route('admin.production.index')"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 flex items-center space-x-1 transition-colors">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                <span>Back to History</span>
            </Link>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Side: Form Details -->
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Product Selection -->
                        <div class="space-y-1">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Select Item to Produce</label>
                            <select v-model="form.product_item_id"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-gray-900 dark:text-white"
                                :class="{ 'ring-2 ring-red-500': form.errors.product_item_id }">
                                <option value="" disabled>Choose a prep item...</option>
                                <option v-for="item in prepItems" :key="item.id" :value="item.id">
                                    {{ item.name }} (Base Unit: {{ item.unit?.name || 'N/A' }})
                                </option>
                            </select>
                            <p v-if="form.errors.product_item_id" class="text-xs text-red-500 mt-1">{{ form.errors.product_item_id }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Quantity -->
                            <div class="space-y-1">
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Quantity to Produce</label>
                                <div class="relative">
                                    <input v-model="form.quantity" type="number" step="0.01" min="0.01"
                                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-gray-900 dark:text-white pl-4"
                                        :class="{ 'ring-2 ring-red-500': form.errors.quantity }"
                                        placeholder="0.00" />
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-indigo-500 uppercase">
                                        {{ selectedProductUnit }}
                                    </div>
                                </div>
                                <p v-if="form.errors.quantity" class="text-xs text-red-500 mt-1">{{ form.errors.quantity }}</p>
                            </div>

                            <!-- Date -->
                            <div class="space-y-1">
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Production Date</label>
                                <input v-model="form.production_date" type="date"
                                    class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-gray-900 dark:text-white"
                                    :class="{ 'ring-2 ring-red-500': form.errors.production_date }" />
                                <p v-if="form.errors.production_date" class="text-xs text-red-500 mt-1">{{ form.errors.production_date }}</p>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="space-y-1">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Notes (Optional)</label>
                            <textarea v-model="form.notes" rows="3"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-gray-900 dark:text-white"
                                placeholder="Any internal notes for this batch..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Summary / Action -->
            <div class="space-y-6">
                <div class="bg-indigo-600 rounded-2xl shadow-xl shadow-indigo-500/20 p-6 text-white space-y-4">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="material-symbols-outlined p-2 bg-white/20 rounded-lg">info</span>
                        <h3 class="font-bold">Summary</h3>
                    </div>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center py-2 border-b border-white/10">
                            <span class="opacity-80">Product</span>
                            <span class="font-semibold text-right">{{ selectedProductName }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-white/10">
                            <span class="opacity-80">Quantity</span>
                            <span class="font-semibold">{{ form.quantity || 0 }} {{ selectedProductUnit }}</span>
                        </div>
                    </div>

                    <p class="text-xs opacity-70 leading-relaxed italic">
                        * Ingredients will be automatically deducted from stock based on the recipe defined for this item.
                    </p>

                    <button type="submit" 
                        :disabled="form.processing"
                        class="w-full py-4 bg-white text-indigo-600 rounded-xl font-bold hover:bg-gray-100 transition-all active:scale-95 disabled:opacity-50 disabled:active:scale-100 flex items-center justify-center space-x-2">
                        <span v-if="form.processing" class="material-symbols-outlined animate-spin">progress_activity</span>
                        <span v-else class="material-symbols-outlined">check_circle</span>
                        <span>Confirm Production</span>
                    </button>
                </div>

                <!-- Recipe Warning -->
                <div v-if="selectedProduct && !selectedProduct.recipe" class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800/50 flex items-start space-x-3">
                    <span class="material-symbols-outlined text-amber-500 mt-0.5">warning</span>
                    <div>
                        <h4 class="text-sm font-bold text-amber-800 dark:text-amber-400">No Recipe Found</h4>
                        <p class="text-xs text-amber-700/80 dark:text-amber-500/80 mt-1">
                            This product has no recipe defined. You must create a recipe before you can record production.
                        </p>
                        <Link :href="route('admin.recipes.create')" class="text-xs font-bold underline text-amber-800 dark:text-amber-400 mt-2 block">
                            Create Recipe Now
                        </Link>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    prepItems: Array,
    pageTitle: String
})

const form = useForm({
    product_item_id: '',
    quantity: '',
    production_date: new Date().toISOString().substr(0, 10),
    notes: ''
})

const selectedProduct = computed(() => {
    return props.prepItems.find(p => p.id === form.product_item_id)
})

const selectedProductName = computed(() => {
    return selectedProduct.value?.name || 'None selected'
})

const selectedProductUnit = computed(() => {
    return selectedProduct.value?.unit?.name || ''
})

const submit = () => {
    if (!selectedProduct.value) {
        Swal.fire('Error', 'Please select a product first.', 'error')
        return
    }

    Swal.fire({
        title: 'Confirm Production',
        text: "This will deduct ingredients from stock. Proceed?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, record batch'
    }).then((result) => {
        if (result.isConfirmed) {
            form.post(route('admin.production.store'), {
                onSuccess: () => {
                    Swal.fire('Success', 'Production batch recorded successfully.', 'success')
                    form.reset()
                }
            })
        }
    })
}
</script>
