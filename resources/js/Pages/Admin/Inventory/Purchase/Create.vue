<template>
    <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ pageTitle }}
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Record a new incoming shipment of ingredients or retail products.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="p-6 sm:p-8">
                    <form @submit.prevent="submit" class="space-y-8">
                        <!-- Master Info -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Supplier</label>
                                <select v-model="form.supplier_id"
                                    class="w-full border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 py-2 text-sm"
                                    :class="{ 'border-red-500 ring-red-500': form.errors.supplier_id }" required>
                                    <option value="" disabled>Select a Supplier</option>
                                    <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                        {{ supplier.name }} {{ supplier.company_name ? `(${supplier.company_name})` : '' }}
                                    </option>
                                </select>
                                <p v-if="form.errors.supplier_id" class="text-red-500 text-xs mt-1">{{ form.errors.supplier_id }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Purchase Date</label>
                                <input v-model="form.purchase_date" type="date"
                                    class="w-full border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 py-2 text-sm"
                                    :class="{ 'border-red-500 ring-red-500': form.errors.purchase_date }" required />
                                <p v-if="form.errors.purchase_date" class="text-red-500 text-xs mt-1">{{ form.errors.purchase_date }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Invoice / Ref #</label>
                                <input v-model="form.invoice_number" type="text" placeholder="PO-12345"
                                    class="w-full border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="border rounded-xl border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Purchase Items</h3>
                                <button type="button" @click="addItem"
                                    class="flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-tighter transition-colors">
                                    <span class="material-symbols-outlined text-sm">add_circle</span>
                                    Add Line Item
                                </button>
                            </div>

                            <div class="p-4 sm:p-6 space-y-4">
                                <div v-for="(item, index) in form.items" :key="index"
                                    class="flex flex-wrap md:flex-nowrap gap-4 items-end bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm relative group transition-all hover:shadow-md">
                                    
                                    <!-- Item Type Selector -->
                                    <div class="w-full md:w-32">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                                        <select v-model="item.item_type" @change="handleTypeChange(index)"
                                            class="w-full h-9 border rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-2">
                                            <option :value="1">Ingredient</option>
                                            <option :value="2">Product</option>
                                        </select>
                                    </div>

                                    <div class="grow min-w-[200px]">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">
                                            Select {{ item.item_type === 1 ? 'Ingredient' : 'Retail Product' }}
                                        </label>
                                        <select v-model="item.item_id" @change="handleItemChange(index)"
                                            class="w-full h-9 border rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-2"
                                            required>
                                            <option value="" disabled>Choose item...</option>
                                            <template v-if="item.item_type === 1">
                                                <option v-for="ing in ingredients" :key="ing.id" :value="ing.id">
                                                    {{ ing.name }}
                                                </option>
                                            </template>
                                            <template v-else>
                                                <option v-for="prod in productItems" :key="prod.id" :value="prod.id">
                                                    {{ prod.name }}
                                                </option>
                                            </template>
                                        </select>
                                    </div>

                                    <div class="w-full md:w-32">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Unit</label>
                                        <select v-model="item.unit_id"
                                            class="w-full h-9 border rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-2"
                                            required>
                                            <option v-for="unit in getAvailableUnits(index)" :key="unit.id" :value="unit.id">
                                                {{ unit.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="w-full md:w-24">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Qty</label>
                                        <input v-model="item.quantity" type="number" step="0.01" min="0.01"
                                            class="w-full h-9 border rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-2"
                                            required />
                                    </div>

                                    <div class="w-full md:w-28">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Unit Price</label>
                                        <input v-model="item.unit_price" type="number" step="0.01" min="0"
                                            class="w-full h-9 border rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-2"
                                            required />
                                    </div>

                                    <div class="w-full md:w-32">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Expiry Date</label>
                                        <input v-model="item.expiry_date" type="date"
                                            class="w-full h-9 border rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm px-2" />
                                    </div>

                                    <div class="w-full md:w-32 text-right">
                                        <label class="block text-xs font-medium text-gray-700 mb-1 font-bold">Line Total</label>
                                        <div class="h-9 flex items-center justify-end font-bold text-gray-800 px-2 bg-gray-100 rounded-md border border-gray-200 text-sm">
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
                            <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                                <div class="text-right">
                                    <span class="text-sm text-gray-500 mr-4">Grand Total:</span>
                                    <span class="text-2xl font-bold text-indigo-700 dark:text-indigo-400">${{ grandTotal }}</span>
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
                                <button type="submit" :disabled="form.processing"
                                    class="px-8 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all flex items-center gap-2 disabled:opacity-50">
                                    <span v-if="form.processing" class="material-symbols-outlined text-sm animate-spin">progress_activity</span>
                                    <span v-else class="material-symbols-outlined text-sm">check_circle</span>
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
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout : AdminLayout })

const props = defineProps({
    suppliers: Array,
    ingredients: Array,
    productItems: Array,
    units: Array,
    pageTitle: String
})

const form = useForm({
    supplier_id: '',
    purchase_date: new Date().toISOString().split('T')[0],
    invoice_number: '',
    notes: '',
    items: [
        { item_type: 1, item_id: '', unit_id: '', quantity: 1, unit_price: 0, expiry_date: '' }
    ]
})

const addItem = () => {
    form.items.push({ item_type: 1, item_id: '', unit_id: '', quantity: 1, unit_price: 0, expiry_date: '' })
}

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1)
    }
}

// Helpers
const getModel = (type, id) => {
    if (type === 1) return props.ingredients.find(i => i.id === id)
    return props.productItems.find(p => p.id === id)
}

const handleTypeChange = (index) => {
    const item = form.items[index];
    item.item_id = ''
    item.unit_id = ''
}

const handleItemChange = (index) => {
    const item = form.items[index];
    const model = getModel(item.item_type, item.item_id)

    if (model) {
        item.unit_id = model.unit_id
    }
}

const getAvailableUnits = (index) => {
    const item = form.items[index]
    if (!item.item_id) return []
    
    const model = getModel(item.item_type, item.item_id)
    if (!model) return []
    
    const baseUnitId = model.unit?.base_unit_id || model.unit_id
    return props.units.filter(u => u.id == baseUnitId || u.base_unit_id == baseUnitId)
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
