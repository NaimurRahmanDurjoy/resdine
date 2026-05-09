<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Record New Production</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Deduct raw ingredients and increase stock of prepped
                    items.</p>
            </div>
            <Link :href="route('admin.production.index')"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 flex items-center space-x-1 transition-colors">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                <span>Back to History</span>
            </Link>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left Side: Form Details (5/12) -->
            <div class="lg:col-span-5 space-y-6">
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 space-y-6">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-indigo-500">edit_note</span>
                        Batch Details
                    </h3>
                    <div class="grid grid-cols-1 gap-5">
                        <!-- Product Selection -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Item to
                                Produce</label>
                            <select v-model="form.product_item_id"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-gray-900 dark:text-white"
                                :class="{ 'ring-2 ring-red-500': form.errors.product_item_id }">
                                <option value="" disabled>Choose a prep item...</option>
                                <option v-for="item in prepItems" :key="item.id" :value="item.id">
                                    {{ item.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Quantity</label>
                            <div class="relative">
                                <input v-model="form.quantity" type="number" step="0.01" min="0.01"
                                    class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-gray-900 dark:text-white pl-4"
                                    :class="{ 'ring-2 ring-red-500': form.errors.quantity }" placeholder="0.00" />
                                <div
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-indigo-500 uppercase">
                                    {{ selectedProductUnit }}
                                </div>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Date</label>
                            <input v-model="form.production_date" type="date"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-gray-900 dark:text-white"
                                :class="{ 'ring-2 ring-red-500': form.errors.production_date }" />
                        </div>

                        <!-- Notes -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Notes</label>
                            <textarea v-model="form.notes" rows="2"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-gray-900 dark:text-white"
                                placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle: Integrated Ingredients & Stock Check (4/12) -->
            <div class="lg:col-span-4">
                <div v-if="selectedProduct && selectedProduct.recipe"
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden h-full">
                    <div
                        class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-indigo-500">receipt_long</span>
                            Stock Status
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr
                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50 dark:bg-gray-900/30">
                                    <th class="px-4 py-3">Item</th>
                                    <th class="px-4 py-3">Need</th>
                                    <th class="px-4 py-3">Stock</th>
                                    <th class="px-4 py-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="item in ingredientCheck" :key="item.id" class="text-xs transition-colors"
                                    :class="item.hasStock ? 'hover:bg-emerald-50/30' : 'bg-red-50/30 hover:bg-red-50/50'">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        {{ item.name }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                        {{ item.required.toFixed(2) }}
                                    </td>
                                    <td class="px-4 py-3"
                                        :class="item.hasStock ? 'text-emerald-600' : 'text-red-600 font-bold'">
                                        {{ item.current.toFixed(2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="material-symbols-outlined text-sm"
                                            :class="item.hasStock ? 'text-emerald-500' : 'text-red-500'">
                                            {{ item.hasStock ? 'check_circle' : 'error' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Empty State for Recipe Check -->
                <div v-else
                    class="h-full border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl flex flex-col items-center justify-center p-8 text-gray-400 text-center">
                    <span class="material-symbols-outlined text-4xl mb-2">inventory_2</span>
                    <p class="text-sm">Select an item to view recipe requirements</p>
                </div>
            </div>

            <!-- Right Side: Summary / Action (3/12) -->
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-indigo-600 rounded-2xl shadow-xl shadow-indigo-500/20 p-6 text-white space-y-4">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="material-symbols-outlined p-2 bg-white/20 rounded-lg">info</span>
                        <h3 class="font-bold">Summary</h3>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center py-2 border-b border-white/10">
                            <span class="opacity-80">Batch</span>
                            <span class="font-semibold text-right truncate ml-2">{{ selectedProductName }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-white/10">
                            <span class="opacity-80">Result</span>
                            <span class="font-semibold">+{{ form.quantity || 0 }} {{ selectedProductUnit }}</span>
                        </div>
                    </div>

                    <div v-if="!canProduce && form.quantity > 0"
                        class="p-3 bg-red-500/30 rounded-xl border border-red-400/50 text-center">
                        <p class="text-[10px] font-bold text-red-100">INSUFFICIENT STOCK</p>
                    </div>

                    <button type="submit" :disabled="form.processing || !canProduce || !form.quantity"
                        class="w-full py-4 bg-white text-indigo-600 rounded-xl font-bold hover:bg-gray-100 transition-all active:scale-95 disabled:opacity-50 disabled:active:scale-100 flex items-center justify-center space-x-2">
                        <span v-if="form.processing"
                            class="material-symbols-outlined animate-spin">progress_activity</span>
                        <span v-else class="material-symbols-outlined">check_circle</span>
                        <span>Confirm</span>
                    </button>
                </div>

                <!-- Recipe Warning -->
                <div v-if="selectedProduct && !selectedProduct.recipe"
                    class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800/50 flex items-start space-x-3">
                    <span class="material-symbols-outlined text-amber-500 mt-0.5">warning</span>
                    <div>
                        <h4 class="text-sm font-bold text-amber-800 dark:text-amber-400">No Recipe Found</h4>
                        <p class="text-xs text-amber-700/80 dark:text-amber-500/80 mt-1">
                            This product has no recipe defined. You must create a recipe before you can record
                            production.
                        </p>
                        <Link :href="route('admin.recipes.create')"
                            class="text-xs font-bold underline text-amber-800 dark:text-amber-400 mt-2 block">
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

const ingredientCheck = computed(() => {
    if (!selectedProduct.value || !selectedProduct.value.recipe) return []

    const multiplier = parseFloat(form.quantity) || 0
    return selectedProduct.value.recipe.recipe_items.map(item => {
        const required = item.quantity * multiplier
        const currentStock = item.inventory_item?.stock_summary?.current_stock || 0
        return {
            id: item.id,
            name: item.inventory_item?.name,
            required: required,
            current: parseFloat(currentStock),
            hasStock: parseFloat(currentStock) >= required
        }
    })
})

const canProduce = computed(() => {
    if (!form.quantity || form.quantity <= 0) return false
    if (!selectedProduct.value?.recipe) return false
    return ingredientCheck.value.every(item => item.hasStock)
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
                    form.reset()
                }
            })
        }
    })
}
</script>
