<template>
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Header -->
                <div
                    class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Record New Purchase</h1>
                            <p class="text-gray-600 dark:text-gray-400">Intake stock and raw materials</p>
                        </div>
                        <Link :href="route('admin.purchase.index')"
                            class="flex items-center text-sm text-indigo-600 hover:text-indigo-900 font-medium transition-colors">
                            <span class="material-symbols-outlined mr-1 text-base">arrow_back</span>
                            Back to List
                        </Link>
                    </div>
                </div>

                <!-- Form Body -->
                <div class="p-8">
                    <form @submit.prevent="submit" class="space-y-8">
                        
                        <!-- General Info -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier *</label>
                                <select v-model="form.supplier_id"
                                    class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                                    required>
                                    <option value="">Select Supplier...</option>
                                    <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                        {{ supplier.name }} - {{ supplier.company_name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.supplier_id" class="text-red-500 text-xs mt-1">{{ form.errors.supplier_id }}</div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Purchase Date *</label>
                                <input v-model="form.purchase_date" type="date"
                                    class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm"
                                    required />
                                <div v-if="form.errors.purchase_date" class="text-red-500 text-xs mt-1">{{ form.errors.purchase_date }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Invoice Number</label>
                                <input v-model="form.invoice_number" type="text" placeholder="Optional reference"
                                    class="w-full h-10 border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 text-sm" />
                                <div v-if="form.errors.invoice_number" class="text-red-500 text-xs mt-1">{{ form.errors.invoice_number }}</div>
                            </div>
                        </div>

                        <!-- Ingredients Matrix -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                                <h3 class="text-lg font-medium text-gray-800">Purchased Items</h3>
                                <button type="button" @click="addItem"
                                    class="text-sm bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-md hover:bg-gray-50 transition-colors flex items-center gap-1 shadow-sm">
                                    <span class="material-symbols-outlined text-sm">add</span> Add Row
                                </button>
                            </div>
                            
                            <div class="p-4 space-y-4 bg-white">
                                <div v-for="(item, index) in form.items" :key="index" class="flex flex-wrap md:flex-nowrap gap-4 items-end bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    
                                    <div class="w-full md:w-1/3">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Ingredient *</label>
                                        <select v-model="item.ingredient_id" @change="handleIngredientChange(index)"
                                            class="w-full h-9 border rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-2"
                                            required>
                                            <option value="">Select...</option>
                                            <option v-for="ing in ingredients" :key="ing.id" :value="ing.id">
                                                {{ ing.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="w-full md:w-1/6">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Unit *</label>
                                        <select v-model="item.unit_id"
                                            class="w-full h-9 border rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-2"
                                            :disabled="!item.ingredient_id" required>
                                            <option v-for="unit in getAvailableUnits(item.ingredient_id)" :key="unit.id" :value="unit.id">
                                                {{ unit.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="w-full md:w-1/6">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Qty *</label>
                                        <div class="flex items-center gap-1">
                                            <input v-model.number="item.quantity" type="number" step="0.01" min="0.01"
                                                class="w-full h-9 border rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-2"
                                                required />
                                            <span v-if="item.unit_id && item.ingredient_id"
                                                class="text-[10px] text-indigo-600 whitespace-nowrap">
                                                ≈ {{ calculateNormalizedQty(item) }} {{ getUnitShortName(item.ingredient_id) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="w-full md:w-1/6">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Unit Price ($) *</label>
                                        <input v-model.number="item.unit_price" type="number" step="0.01" min="0" placeholder="0.00"
                                            class="w-full h-9 border rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-2"
                                            required />
                                    </div>

                                    <div class="w-full md:w-1/5">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Expiry Date</label>
                                        <input v-model="item.expiry_date" type="date"
                                            class="w-full h-9 border rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-2" />
                                    </div>

                                    <div class="w-full md:w-1/6">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Total</label>
                                        <div class="h-9 flex items-center font-bold text-gray-800 px-2 bg-gray-100 rounded-md border border-gray-200">
                                            $ {{ calculateLineTotal(item) }}
                                        </div>
                                    </div>

                                    <div class="shrink-0 flex items-center pb-[2px]">
                                        <button type="button" @click="removeItem(index)" :disabled="form.items.length === 1"
                                            class="h-9 w-9 flex items-center justify-center text-red-500 hover:bg-red-50 rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </div>

                                </div>
                                <div v-if="form.errors.items" class="text-red-500 text-sm mt-4 p-3 bg-red-50 rounded-lg border border-red-100">
                                    {{ form.errors.items }}
                                </div>
                            </div>
                            
                            <!-- Totals Footer -->
                            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
                                <div class="text-right">
                                    <span class="text-sm text-gray-500 mr-4">Grand Total:</span>
                                    <span class="text-2xl font-bold text-indigo-700">${{ grandTotal }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Purchase Notes</label>
                            <textarea v-model="form.notes" rows="3" placeholder="Optional details regarding this shipment..."
                                class="w-full border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 py-2 text-sm"></textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <button type="submit"
                                    class="px-8 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                    Record Purchase
                                </button>
                                <Link :href="route('admin.purchase.index')"
                                    class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all">
                                    Cancel
                                </Link>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
</template>

<script setup>
import {  computed } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout : AdminLayout })

const props = defineProps({
    suppliers: Array,
    ingredients: Array,
    units: Array,
    pageTitle: String
})

const form = useForm({
    supplier_id: '',
    purchase_date: new Date().toISOString().split('T')[0],
    invoice_number: '',
    notes: '',
    items: [
        { ingredient_id: '', unit_id: '', quantity: 1, unit_price: 0, expiry_date: '' }
    ]
})

const addItem = () => {
    form.items.push({ ingredient_id: '', unit_id: '', quantity: 1, unit_price: 0, expiry_date: '' })
}

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1)
    }
}

// Helpers
const getIngredient = (id) => props.ingredients.find(i => i.id === id)

const getUnitShortName = (id) => {
    if (!id) return ''
    const ing = getIngredient(id)
    return ing?.unit?.short_name || ing?.unit?.name || ''
}

const handleIngredientChange = (index) => {
    const item = form.items[index];
    const ingredient = getIngredient(item.ingredient_id)

    if (ingredient) {
        item.unit_id = ingredient.unit_id
        if (!ingredient.expiry_tracking) {
            item.expiry_date = ''
        }
    }
}

const getAvailableUnits = (ingredientId) => {
    if (!ingredientId) return []
    const ingredient = getIngredient(ingredientId)
    if (!ingredient) return []
    
    const baseUnitId = ingredient.unit?.base_unit_id || ingredient.unit_id
    return props.units.filter(u => u.id == baseUnitId || u.base_unit_id == baseUnitId)
}

const getAbsoluteBaseValue = (unit, quantity) => {
    let currentValue = parseFloat(quantity)
    let currentUnit = unit
    const visited = [unit.id]

    while (currentUnit.base_unit_id) {
        currentValue *= parseFloat(currentUnit.conversion_factor)
        const nextUnit = props.units.find(u => u.id == currentUnit.base_unit_id)
        
        if (!nextUnit || visited.includes(nextUnit.id)) {
            break
        }
        
        currentUnit = nextUnit
        visited.push(currentUnit.id)
    }

    return {
        value: currentValue,
        root_unit_id: currentUnit.id,
        root_unit_name: currentUnit.name
    }
}

const calculateNormalizedQty = (item) => {
    if (!item.ingredient_id || !item.unit_id || !item.quantity) return 0
    const ingredient = getIngredient(item.ingredient_id)
    const fromUnit = props.units.find(u => u.id == item.unit_id)
    const toUnit = props.units.find(u => u.id == ingredient.unit_id)

    if (!fromUnit || !toUnit) return item.quantity

    // Conversion Logic (Matching Recursive RecipeService)
    try {
        const fromBase = getAbsoluteBaseValue(fromUnit, item.quantity)
        const toBase = getAbsoluteBaseValue(toUnit, 1)

        if (fromBase.root_unit_id !== toBase.root_unit_id) {
            return 0 // Incompatible
        }

        return fromBase.value / toBase.value
    } catch (e) {
        return 0
    }
}

const calculateLineTotal = (item) => {
    return (parseFloat(item.quantity) * parseFloat(item.unit_price) || 0).toFixed(2)
}

const grandTotal = computed(() => {
    return form.items.reduce((total, item) => {
        return total + (parseFloat(item.quantity) * parseFloat(item.unit_price) || 0)
    }, 0).toFixed(2)
})

const submit = () => {
    // Normalize empty dates to null before submission
    form.items.forEach(item => {
        if (item.expiry_date === '') {
            item.expiry_date = null;
        }
    });

    form.post(route('admin.purchase.store'))
}
</script>
