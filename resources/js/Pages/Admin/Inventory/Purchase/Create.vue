<template>
    <div class="mx-auto">
        <div
            class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ pageTitle }}</h1>
                        <p class="text-gray-600 dark:text-gray-400">Record a new incoming shipment of ingredients or
                            retail products.</p>
                    </div>
                    <Link :href="route('admin.purchase.index')"
                        class="flex items-center text-sm text-indigo-600 hover:text-indigo-900 font-medium transition-colors">
                        <span class="material-symbols-outlined mr-1 text-base">arrow_back</span>
                        Back to List
                    </Link>
                </div>
            </div>



            <div class="p-6 sm:p-8">
                <form @submit.prevent="submit" class="space-y-8">
                    <!-- Master Info -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Supplier
                                <span class="text-red-500">*</span></label>
                            <select v-model="form.supplier_id"
                                class="w-full border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 py-2 text-sm"
                                :class="{ 'border-red-500 ring-red-500': form.errors.supplier_id }" required>
                                <option value="" disabled>Select a Supplier</option>
                                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                    {{ supplier.name }} {{ supplier.company_name ? `(${supplier.company_name})` : ''
                                    }}
                                </option>
                            </select>
                            <p v-if="form.errors.supplier_id" class="text-red-500 text-xs mt-1">{{
                                form.errors.supplier_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Purchase
                                Date</label>
                            <input v-model="form.purchase_date" type="date"
                                class="w-full border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 py-2 text-sm"
                                :class="{ 'border-red-500 ring-red-500': form.errors.purchase_date }" required />
                            <p v-if="form.errors.purchase_date" class="text-red-500 text-xs mt-1">{{
                                form.errors.purchase_date }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Invoice
                                / Ref #</label>
                            <input v-model="form.invoice_number" type="text" placeholder="PO-12345"
                                class="w-full border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="border rounded-xl border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div
                            class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Purchase Items</h3>
                            <button type="button" @click="addItem"
                                class="flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-tighter transition-colors">
                                <span class="material-symbols-outlined text-sm">add_circle</span>
                                Add Line Item
                            </button>
                        </div>

                        <div class="p-0">
                            <div
                                class="w-full max-h-[400px] overflow-y-auto overflow-x-auto custom-scrollbar border-b border-gray-200 dark:border-gray-700">
                                <table class="w-full text-left text-sm whitespace-nowrap relative">
                                    <thead
                                        class="bg-gray-50 dark:bg-gray-800/95 backdrop-blur text-gray-500 dark:text-gray-400 uppercase text-[10px] font-bold tracking-wider sticky top-0 z-10 shadow-sm border-b border-gray-200 dark:border-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 min-w-[200px]">Inventory Item</th>
                                            <th class="px-4 py-3 w-32">Unit</th>
                                            <th class="px-4 py-3 w-32">Qty</th>
                                            <th class="px-4 py-3 w-32">Unit Price</th>
                                            <th class="px-4 py-3 w-40">Expiry Date</th>
                                            <th class="px-4 py-3 w-32">Line Total</th>
                                            <th class="px-4 py-3 w-16 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="divide-y divide-gray-100 dark:divide-gray-700/50 bg-white dark:bg-gray-800/30">
                                        <tr v-for="(item, index) in form.items" :key="index"
                                            class="group hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                                            <td class="px-4 py-1 relative">
                                                <select v-model="item.inventory_item_id"
                                                    @change="handleItemChange(index)"
                                                    class="block w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium"
                                                    required>
                                                    <option value="" disabled>Select Item</option>
                                                    <option v-for="i in inventoryItems" :key="i.id" :value="i.id">
                                                        {{ i.name }} ({{ i.item_type === 1 ? 'Ing' : (i.item_type
                                                            === 3 ? 'Prep' : 'Product') }})
                                                    </option>
                                                </select>
                                            </td>
                                            <td class="px-4 py-1">
                                                <select v-model="item.unit_id"
                                                    class="block w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium"
                                                    required>
                                                    <option v-for="unit in getAvailableUnits(index)" :key="unit.id"
                                                        :value="unit.id">
                                                        {{ unit.name }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td class="px-4 py-1">
                                                <input v-model="item.quantity" type="number" step="0.01" min="0.01"
                                                    class="block w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium"
                                                    required />
                                            </td>
                                            <td class="px-4 py-1">
                                                <input v-model="item.unit_price" type="number" step="0.01" min="0"
                                                    class="block w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium"
                                                    required />
                                            </td>
                                            <td class="px-4 py-1">
                                                <input v-model="item.expiry_date" type="date"
                                                    class="block w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium" />
                                            </td>
                                            <td class="px-4 py-1">
                                                <div
                                                    class="px-3 py-1.5 bg-gray-50 dark:bg-gray-900/40 rounded-lg text-sm font-bold text-gray-700 dark:text-gray-300 border border-transparent whitespace-nowrap text-right">
                                                    {{ currency() }}{{ calculateLineTotal(item) }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-1 text-center">
                                                <button type="button" @click="removeItem(index)"
                                                    :disabled="form.items.length === 1"
                                                    class="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors inline-flex justify-center items-center disabled:opacity-30 disabled:cursor-not-allowed">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7"
                                                class="px-4 py-3 bg-gray-50/50 dark:bg-gray-800/30 border-t border-gray-100 dark:border-gray-700/50 text-center">
                                                <button type="button" @click="addItem"
                                                    class="text-indigo-600 dark:text-indigo-400 text-sm font-semibold hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors inline-flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-sm">add_circle</span>
                                                    Add More Items
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-if="form.errors.items"
                                class="text-red-500 text-sm mt-4 mb-4 mx-4 p-3 bg-red-50 rounded-lg border border-red-100">
                                {{ form.errors.items }}
                            </div>
                        </div>

                        <!-- Totals Footer -->
                        <div
                            class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                            <div class="text-right">
                                <span class="text-sm text-gray-500 mr-4">Grand Total:</span>
                                <span class="text-2xl font-bold text-indigo-700 dark:text-indigo-400">{{ currency()
                                }}{{ grandTotal }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Purchase Notes
                            <span class="text-red-500">*</span></label>
                        <textarea v-model="form.notes" required rows="3"
                            placeholder="Optional details regarding this shipment..."
                            class="w-full border rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition px-3 py-2 text-sm"></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <button type="submit" :disabled="form.processing"
                                class="px-8 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all flex items-center gap-2 disabled:opacity-50">
                                <span v-if="form.processing"
                                    class="material-symbols-outlined text-sm animate-spin">progress_activity</span>
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
</template>

<script setup>
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
defineOptions({ layout: AdminLayout })

const props = defineProps({
    suppliers: Array,
    inventoryItems: Array,
    units: Array,
    pageTitle: String
})

const form = useForm({
    supplier_id: '',
    purchase_date: new Date().toISOString().split('T')[0],
    invoice_number: '',
    notes: '',
    items: [
        { inventory_item_id: '', unit_id: '', quantity: 1, unit_price: 0, expiry_date: '' }
    ]
})

const addItem = () => {
    form.items.push({ inventory_item_id: '', unit_id: '', quantity: 1, unit_price: 0, expiry_date: '' })
}

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1)
    }
}

// Helpers
const handleItemChange = (index) => {
    const item = form.items[index];
    const inventoryItem = props.inventoryItems.find(i => i.id === item.inventory_item_id)

    if (inventoryItem) {
        item.unit_id = inventoryItem.unit_id
    }
}

const getAvailableUnits = (index) => {
    const item = form.items[index]
    if (!item.inventory_item_id) return []

    const inventoryItem = props.inventoryItems.find(i => i.id === item.inventory_item_id)
    if (!inventoryItem) return []

    const baseUnitId = inventoryItem.unit?.base_unit_id || inventoryItem.unit_id
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
