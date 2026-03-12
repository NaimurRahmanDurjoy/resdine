<template>
    <AdminLayout :pageTitle="pageTitle">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Header -->
                <div
                    class="bg-gradient-to-r from-blue-50 to-white dark:from-gray-700 dark:to-gray-800 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Adjust Stock</h1>
                            <p class="text-gray-600 dark:text-gray-400">Manually correct inventory counts</p>
                        </div>
                        <Link :href="route('admin.stock.index')"
                            class="flex items-center text-sm text-indigo-600 hover:text-indigo-900 font-medium transition-colors">
                            <span class="material-symbols-outlined mr-1 text-base">arrow_back</span>
                            Back to Stock
                        </Link>
                    </div>
                </div>

                <!-- Form Body -->
                <div class="p-8">
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg flex items-start gap-3">
                        <span class="material-symbols-outlined text-yellow-600 shrink-0">info</span>
                        <p class="text-sm text-yellow-800">
                            <strong>Note:</strong> Standard intakes should be recorded as Purchase Orders. Use this adjustment tool only to correct discrepancies, account for spoilage, or log non-purchased intakes.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div class="space-y-5">
                            <!-- Ingredient -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Ingredient *</label>
                                <select v-model="form.ingredient_id" @change="updateCurrentStockDisplay"
                                    class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                                    required>
                                    <option value="">Choose item...</option>
                                    <option v-for="ingredient in ingredients" :key="ingredient.id" :value="ingredient.id">
                                        {{ ingredient.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Current stock visualization (readonly) -->
                            <div v-if="form.ingredient_id" class="flex gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase">Current System Stock</p>
                                    <p class="text-lg font-bold text-gray-900">{{ currentStockDisplay }} <span class="text-sm text-gray-500 font-normal">{{ unitDisplay }}</span></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Transaction Type *</label>
                                    <select v-model="form.transaction_type"
                                        class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                                        required>
                                        <option value="in">Add Stock (In)</option>
                                        <option value="out">Remove Stock (Out)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantity to Adjust *</label>
                                    <div class="relative">
                                        <input v-model.number="form.quantity" type="number" step="0.01" min="0.01" placeholder="0.00"
                                            class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm pr-12"
                                            required />
                                        <div class="absolute inset-y-0 right-0 py-2 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 text-sm font-medium">{{ unitDisplay }}</span>
                                        </div>
                                    </div>
                                    <p v-if="form.transaction_type === 'out' && (form.quantity > currentStockValue)" class="mt-1 text-xs text-red-600 font-medium">
                                        Cannot remove more than current stock!
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason / Notes *</label>
                                <input v-model="form.notes" type="text" placeholder="e.g., Spoilage, Inventory Count Sync, Damage"
                                    class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                                    required />
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <button type="submit" :disabled="form.transaction_type === 'out' && form.quantity > currentStockValue"
                                    class="px-8 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="material-symbols-outlined text-sm">save</span>
                                    Apply Adjustment
                                </button>
                                <Link :href="route('admin.stock.index')"
                                    class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all">
                                    Cancel
                                </Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    ingredients: Array,
    pageTitle: String
})

const form = reactive({
    ingredient_id: '',
    transaction_type: 'out',
    quantity: '',
    notes: ''
})

const currentStockDisplay = ref('0.00')
const currentStockValue = ref(0)
const unitDisplay = ref('')

const updateCurrentStockDisplay = () => {
    if (!form.ingredient_id) {
        currentStockDisplay.value = '0.00'
        currentStockValue.value = 0
        unitDisplay.value = ''
        return
    }

    const ingredient = props.ingredients.find(i => i.id === form.ingredient_id)
    if (ingredient) {
        unitDisplay.value = ingredient.unit?.short_name || ingredient.unit?.name || ''
        if (ingredient.stock_master) {
            currentStockValue.value = parseFloat(ingredient.stock_master.current_stock)
            currentStockDisplay.value = parseFloat(ingredient.stock_master.current_stock).toFixed(2)
        } else {
            currentStockValue.value = 0
            currentStockDisplay.value = '0.00'
        }
    }
}

const submit = () => {
    // Validate if outward and qty > current stock
    if (form.transaction_type === 'out' && form.quantity > currentStockValue.value) {
        return;
    }
    
    router.post(route('admin.stock.adjust.process'), form)
}
</script>
