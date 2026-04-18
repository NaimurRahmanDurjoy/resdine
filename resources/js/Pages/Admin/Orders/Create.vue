<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-black tracking-tight">{{ pageTitle }}</h1>
                <p class="text-indigo-100 mt-2 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">restaurant</span>
                    Setup and submit a new guest order
                </p>
            </div>
            <Link :href="route('admin.orders.index')" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition-all backdrop-blur-md border border-white/20 text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Back to List
            </Link>
        </div>
        <!-- Decorative blob -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-3xl transition-transform duration-1000"></div>
    </div>

    <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Product Selection & Current Order -->
      <div class="lg:col-span-8 space-y-6">
        <!-- Item Selector -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 overflow-hidden">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600">menu_book</span>
                Select Menu Items
            </h2>
            <div class="relative">
                 <input type="text" placeholder="Search menu..." class="pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all w-64">
                 <span class="absolute left-3 top-2.5 material-symbols-outlined text-gray-400 text-sm">search</span>
            </div>
          </div>
          
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 overflow-y-auto max-h-[450px] pr-2 custom-scrollbar">
            <div 
              v-for="product in products" 
              :key="product.id"
              @click="addItem(product)"
              class="group relative bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-3 cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-900/20 border-2 border-transparent hover:border-indigo-400/50 transition-all duration-300 flex flex-col items-center text-center shadow-sm"
            >
              <div class="relative w-full aspect-square mb-3 overflow-hidden rounded-xl">
                 <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                 <div v-else class="w-full h-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-indigo-300 text-4xl">restaurant</span>
                 </div>
                 <div class="absolute top-2 right-2 bg-white/90 dark:bg-gray-800/90 backdrop-blur px-2 py-1 rounded-lg text-xs font-bold text-indigo-600 shadow-sm border border-indigo-100 dark:border-indigo-900/50">
                    ${{ product.price }}
                 </div>
              </div>
              <span class="font-bold text-sm text-gray-800 dark:text-gray-200 line-clamp-1 truncate w-full">{{ product.name }}</span>
              
              <!-- Add overlay on hover -->
              <div class="absolute inset-x-0 bottom-3 px-3 opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0">
                  <div class="bg-indigo-600 text-white text-[10px] font-bold py-1 rounded-lg shadow-lg">ADD TO ORDER</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Selected Items Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
                <h2 class="text-sm font-black uppercase tracking-widest text-gray-400 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">shopping_cart</span>
                    Cart Summary
                </h2>
            </div>
            
            <div v-if="form.items.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400">
                <span class="material-symbols-outlined text-6xl mb-4 opacity-20">order_approve</span>
                <p>Your cart is empty. Pick items from the menu.</p>
            </div>
            
            <div v-else class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                            <th class="px-6 py-4">Item Details</th>
                            <th class="px-6 py-4 text-center">Quantity</th>
                            <th class="px-6 py-4">Subtotal</th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="(item, index) in form.items" :key="index" class="group hover:bg-gray-50 dark:hover:bg-gray-900/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 dark:text-gray-100">{{ getItemName(item.item_id) }}</div>
                                <input 
                                    v-model="item.notes" 
                                    placeholder="Add special instructions..." 
                                    class="mt-1 block w-full bg-transparent border-none p-0 text-xs text-indigo-500 focus:ring-0 placeholder-gray-400"
                                >
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center bg-gray-100 dark:bg-gray-700/50 rounded-xl p-1 w-24 mx-auto">
                                    <button @click.prevent="adjustQty(index, -1)" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-white dark:hover:bg-gray-600 rounded-lg transition-all shadow-none hover:shadow-sm">-</button>
                                    <span class="w-8 text-center text-sm font-bold">{{ item.quantity }}</span>
                                    <button @click.prevent="adjustQty(index, 1)" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-white dark:hover:bg-gray-600 rounded-lg transition-all shadow-none hover:shadow-sm">+</button>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-black text-gray-900 dark:text-white">
                                    ${{ (item.price * item.quantity).toFixed(2) }}
                                </div>
                                <div class="text-[10px] text-gray-400">${{ item.price }} / unit</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button @click.prevent="removeItem(index)" class="p-2 text-gray-300 hover:text-red-500 transition-colors">
                                    <span class="material-symbols-outlined">delete_sweep</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
      </div>

      <!-- Settings & Checkout Sidebar -->
      <div class="lg:col-span-4 space-y-6">
        <!-- Order Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
          <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center gap-2">
              <span class="material-symbols-outlined text-indigo-600">settings</span>
              Configuration
          </h2>
          
          <div class="space-y-4">
              <div>
                  <label class="text-[11px] font-black uppercase text-gray-400 tracking-wider mb-1 block">Selected Branch</label>
                  <select v-model="form.branch_id" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-sm">
                      <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                  </select>
              </div>

              <div class="grid grid-cols-2 gap-4">
                  <div>
                      <label class="text-[11px] font-black uppercase text-gray-400 tracking-wider mb-1 block">Order Type</label>
                      <select v-model="form.order_type" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-sm">
                          <option value="1">Dine-in</option>
                          <option value="2">Takeaway</option>
                          <option value="3">Delivery</option>
                          <option value="4">Party</option>
                      </select>
                  </div>
                  <div v-if="form.order_type == 1">
                      <label class="text-[11px] font-black uppercase text-gray-400 tracking-wider mb-1 block">Table</label>
                      <select v-model="form.table_id" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-sm">
                          <option value="">Select Table</option>
                          <option v-for="table in tables" :key="table.id" :value="table.id">{{ table.name }}</option>
                      </select>
                  </div>
              </div>

              <div>
                  <label class="text-[11px] font-black uppercase text-gray-400 tracking-wider mb-1 block">Customer Selection</label>
                  <select v-model="form.customer_id" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-sm">
                      <option value="">Walk-in Guest</option>
                      <option v-for="customer in customers" :key="customer.id" :value="customer.id">{{ customer.name }} ({{ customer.phone }})</option>
                  </select>
              </div>

              <div>
                  <label class="text-[11px] font-black uppercase text-gray-400 tracking-wider mb-1 block">Special Notes</label>
                  <textarea v-model="form.notes" rows="2" placeholder="e.g. Any food allergies or special requests..." class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm"></textarea>
              </div>
          </div>
        </div>

        <!-- Checkout Card -->
        <div class="bg-indigo-900 rounded-3xl shadow-xl p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-indigo-300 font-black text-xs uppercase tracking-widest mb-6 border-b border-indigo-800 pb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">payments</span>
                    Pricing Summary
                </h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center opacity-80">
                        <span class="text-sm">Subtotal</span>
                        <span class="font-bold text-lg">${{ subtotal.toFixed(2) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center group">
                        <div class="flex flex-col">
                            <span class="text-sm opacity-80">Discount Applied</span>
                            <span class="text-[10px] text-indigo-400">Manual adjustment</span>
                        </div>
                        <div class="relative w-24">
                            <span class="absolute left-2 top-2 text-indigo-400 text-xs">$</span>
                            <input v-model.number="form.discount" type="number" step="0.01" class="w-full bg-white/10 hover:bg-white/20 border-none rounded-lg text-right text-sm font-bold pl-5 focus:ring-2 focus:ring-indigo-400 transition-all py-2">
                        </div>
                    </div>

                    <div class="pt-6 mt-6 border-t border-indigo-800 border-dashed">
                        <div class="flex justify-between items-end">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-indigo-400 uppercase tracking-tighter">Amount to Collect</span>
                                <span class="text-4xl font-black text-white">${{ total.toFixed(2) }}</span>
                            </div>
                            <div class="text-[10px] text-indigo-500 font-bold bg-white/10 px-2 py-1 rounded mb-1">VAT INCL.</div>
                        </div>
                    </div>

                    <button 
                        @click="submit"
                        :disabled="form.processing || form.items.length === 0"
                        class="mt-8 w-full group relative flex items-center justify-center gap-3 py-4 bg-white text-indigo-900 rounded-2xl font-black text-lg hover:bg-indigo-50 disabled:bg-indigo-800 disabled:text-indigo-400 transition-all duration-300 shadow-2xl overflow-hidden"
                    >
                        <span v-if="form.processing" class="flex items-center gap-2">
                             <span class="animate-spin rounded-full h-4 w-4 border-2 border-indigo-900 border-t-transparent"></span>
                             Processing...
                        </span>
                        <template v-else>
                            CONFIRM & PLACE ORDER
                            <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">bolt</span>
                        </template>
                    </button>
                    
                    <p class="text-[10px] text-center mt-4 text-indigo-400 uppercase font-black tracking-widest opacity-50">Secure POS transaction system</p>
                </div>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -left-10 -top-10 w-32 h-32 bg-indigo-500/20 rounded-full blur-2xl"></div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

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
    return Math.max(0, subtotal.value - (form.discount || 0));
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

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #334155;
}
</style>
