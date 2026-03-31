<template>
    <AdminLayout>
        <div class="p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6">{{ pageTitle }}</h1>

            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Order Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Product Selection -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-medium mb-4">Select Items</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 overflow-y-auto max-h-[500px]">
                            <div 
                                v-for="product in products" 
                                :key="product.id"
                                @click="addItem(product)"
                                class="border rounded-lg p-3 cursor-pointer hover:border-indigo-500 hover:shadow-md transition bg-gray-50 flex flex-col items-center text-center"
                            >
                                <img v-if="product.image_url" :src="product.image_url" class="w-20 h-20 object-cover rounded-md mb-2">
                                <span class="font-medium text-sm">{{ product.name }}</span>
                                <span class="text-xs text-gray-500">${{ product.price }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Items -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-medium mb-4">Current Order</h2>
                        <div v-if="form.items.length === 0" class="text-center py-8 text-gray-500">
                            No items selected yet.
                        </div>
                        <table v-else class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="text-left text-xs text-gray-500 uppercase">
                                    <th class="pb-2">Item</th>
                                    <th class="pb-2">Qty</th>
                                    <th class="pb-2">Price</th>
                                    <th class="pb-2">Total</th>
                                    <th class="pb-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="(item, index) in form.items" :key="index">
                                    <td class="py-3">
                                        <div class="font-medium text-sm">{{ getItemName(item.item_id) }}</div>
                                        <input 
                                            v-model="item.notes" 
                                            placeholder="Notes/Modifiers" 
                                            class="mt-1 block w-full px-2 py-1 text-xs border rounded focus:ring-indigo-500 focus:border-indigo-500"
                                        >
                                    </td>
                                    <td class="py-3">
                                        <div class="flex items-center">
                                            <button @click.prevent="adjustQty(index, -1)" class="p-1 text-gray-500 hover:text-indigo-600">-</button>
                                            <span class="mx-2 text-sm">{{ item.quantity }}</span>
                                            <button @click.prevent="adjustQty(index, 1)" class="p-1 text-gray-500 hover:text-indigo-600">+</button>
                                        </div>
                                    </td>
                                    <td class="py-3 text-sm">${{ item.price }}</td>
                                    <td class="py-3 text-sm">${{ (item.price * item.quantity).toFixed(2) }}</td>
                                    <td class="py-3 text-right">
                                        <button @click.prevent="removeItem(index)" class="text-red-500 hover:text-red-700">✕</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Order Settings & Totals -->
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-medium mb-4">Order Settings</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Branch</label>
                                <select v-model="form.branch_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Order Type</label>
                                <select v-model="form.order_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1">Dine-in</option>
                                    <option value="2">Takeaway</option>
                                    <option value="3">Delivery</option>
                                    <option value="4">Party</option>
                                </select>
                            </div>

                            <div v-if="form.order_type == 1">
                                <label class="block text-sm font-medium text-gray-700">Table</label>
                                <select v-model="form.table_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Table</option>
                                    <option v-for="table in tables" :key="table.id" :value="table.id">{{ table.name }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Customer (Optional)</label>
                                <select v-model="form.customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Walk-in Customer</option>
                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">{{ customer.name }} ({{ customer.phone }})</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">General Notes</label>
                                <textarea v-model="form.notes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="2"></textarea>
                            </div>
                        </div>

                        <hr class="my-6">

                        <!-- Totals -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span>Subtotal</span>
                                <span>${{ subtotal.toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm items-center">
                                <span>Discount</span>
                                <input v-model.number="form.discount" type="number" class="w-20 px-2 py-1 text-right text-sm border rounded">
                            </div>
                            <div class="flex justify-between font-bold text-lg pt-2 border-t mt-2">
                                <span>Total</span>
                                <span class="text-indigo-600">${{ total.toFixed(2) }}</span>
                            </div>
                        </div>

                        <button 
                            @click="submit"
                            :disabled="form.processing || form.items.length === 0"
                            class="mt-6 w-full py-3 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 disabled:bg-gray-400 transition shadow-lg"
                        >
                            <span v-if="form.processing">Processing...</span>
                            <span v-else>PLACE ORDER</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    customers: Array,
    tables: Array,
    branches: Array,
    products: Array,
    pageTitle: String
});

const form = useForm({
    branch_id: props.branches[0]?.id || '',
    customer_id: '',
    table_id: '',
    order_type: '1',
    items: [],
    discount: 0,
    notes: '',
});

const subtotal = computed(() => {
    return form.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
});

const total = computed(() => {
    return subtotal.value - (form.discount || 0);
});

const addItem = (product) => {
    const existingIndex = form.items.findIndex(i => i.item_id === product.id);
    if (existingIndex > -1) {
        form.items[existingIndex].quantity++;
    } else {
        form.items.push({
            item_id: product.id,
            quantity: 1,
            price: parseFloat(product.price),
            notes: '',
            modifiers: {}
        });
    }
};

const adjustQty = (index, delta) => {
    form.items[index].quantity += delta;
    if (form.items[index].quantity < 1) {
        removeItem(index);
    }
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const getItemName = (itemId) => {
    return props.products.find(p => p.id === itemId)?.name || 'Unknown Item';
};

const submit = () => {
    form.post(route('admin.orders.store'));
};
</script>
