<template>
  <div class="h-screen flex flex-col bg-gray-50 flex-1 overflow-hidden font-sans">
    
    <!-- Top Bar -->
    <header class="bg-indigo-700 text-white shadow-md flex justify-between items-center px-4 py-2 shrink-0 z-10 w-full relative">
      <div class="flex items-center space-x-3">
        <Link :href="route('admin.dashboard')" class="hover:bg-indigo-600 p-2 rounded-full transition cursor-pointer" title="Back to Dashboard">
          <span class="material-symbols-outlined text-xl">arrow_back</span>
        </Link>
        <span class="font-bold text-lg tracking-wide hidden sm:block">RESDINE POS</span>
      </div>
      
      <div class="flex items-center space-x-4 flex-1 justify-center">
        <!-- Optional Search Bar in Header -->
        <div class="relative w-full max-w-md hidden md:block">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-indigo-300">search</span>
          </span>
          <input v-model="searchQuery" type="text" placeholder="Search menu..." class="block w-full pl-10 pr-3 py-1.5 border-transparent rounded-full bg-indigo-800/50 text-white placeholder-indigo-300 focus:outline-none focus:ring-2 focus:ring-white focus:bg-indigo-600 transition shadow-inner">
        </div>
      </div>

      <div class="flex items-center space-x-3">
        <span class="text-sm opacity-90 mr-2 hidden lg:block">{{ currentTime }}</span>
        <div class="bg-indigo-600 px-3 py-1 rounded-full text-sm font-semibold flex items-center shadow-inner cursor-pointer hover:bg-indigo-500 transition">
          <span class="material-symbols-outlined text-sm mr-1">person</span> User
        </div>
      </div>
    </header>

    <!-- Main Content Area -->
    <div class="flex flex-1 overflow-hidden">
      
      <!-- Left Catalog Pane -->
      <div class="flex-1 flex flex-col bg-gray-100 overflow-hidden relative shadow-inner z-0">
        
        <!-- Category Tabs -->
        <div class="bg-white shadow-sm z-10">
          <div class="overflow-x-auto custom-scrollbar flex scroll-smooth snap-x">
            <button @click="selectedCategoryId = null" :class="[selectedCategoryId === null ? 'border-b-4 border-indigo-600 text-indigo-700 bg-indigo-50 font-bold' : 'text-gray-600 hover:bg-gray-50']" class="shrink-0 px-6 py-4 font-medium transition whitespace-nowrap snap-start focus:outline-none focus-visible:bg-indigo-50">
              All Items
            </button>
            <button v-for="cat in categories" :key="cat.id" @click="selectedCategoryId = cat.id" :class="[selectedCategoryId === cat.id ? 'border-b-4 border-indigo-600 text-indigo-700 bg-indigo-50 font-bold' : 'text-gray-600 hover:bg-gray-50']" class="shrink-0 px-6 py-4 font-medium transition whitespace-nowrap snap-start focus:outline-none focus-visible:bg-indigo-50">
              {{ cat.name }}
            </button>
          </div>
        </div>

        <!-- Items Grid -->
        <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            <div v-for="item in filteredItems" :key="item.id" @click="addToCart(item)" class="bg-white rounded-xl shadow-sm hover:shadow-xl border border-gray-100 hover:border-indigo-300 transition-all cursor-pointer overflow-hidden transform hover:-translate-y-1 group flex flex-col active:scale-95 duration-200">
              <div class="aspect-square bg-gray-100 relative overflow-hidden flex items-center justify-center">
                <img v-if="item.image_url" :src="item.image_url" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-500">
                <span v-else class="material-symbols-outlined text-4xl text-gray-300">restaurant_menu</span>
                <!-- Price Badge overlay -->
                <div class="absolute bottom-2 right-2 bg-black/70 backdrop-blur-sm text-white px-2 py-0.5 rounded text-sm font-bold shadow-lg">
                  ${{ item.price }}
                </div>
              </div>
              <div class="p-3 text-center flex-1 flex flex-col justify-center bg-gradient-to-t from-gray-50 to-white">
                <h3 class="text-sm font-bold text-gray-800 line-clamp-2 leading-tight group-hover:text-indigo-700 transition-colors">{{ item.name }}</h3>
              </div>
            </div>
            
            <div v-if="filteredItems.length === 0" class="col-span-full py-12 flex flex-col items-center justify-center text-gray-400">
              <span class="material-symbols-outlined text-6xl mb-3 opacity-50">search_off</span>
              <p class="text-lg">No items found.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Cart Pane -->
      <div class="w-96 bg-white shadow-2xl z-20 flex flex-col border-l border-gray-200 shrink-0">
        <!-- Order Config -->
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 space-y-3">
          <div class="flex gap-2 p-1 bg-gray-200/50 rounded-lg">
            <button @click="orderType = 1" :class="[orderType === 1 ? 'bg-white shadow text-indigo-700 font-bold' : 'text-gray-500 hover:bg-white/50']" class="flex-1 py-1.5 rounded transition text-sm">Dine-in</button>
            <button @click="orderType = 2" :class="[orderType === 2 ? 'bg-white shadow text-indigo-700 font-bold' : 'text-gray-500 hover:bg-white/50']" class="flex-1 py-1.5 rounded transition text-sm">Takeaway</button>
          </div>
          
          <div class="flex space-x-2">
            <div class="flex-1 relative">
              <select v-model="selectedTableId" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 pl-3 pr-8 appearance-none bg-white">
                <option :value="null">Select Table...</option>
                <option v-for="table in tables" :key="table.id" :value="table.id">{{ table.table_name }}</option>
              </select>
              <span class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-400 material-symbols-outlined text-sm">expand_more</span>
            </div>
            <div class="flex-1 relative">
              <select v-model="selectedCustomerId" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 pl-3 pr-8 appearance-none bg-white">
                <option :value="null">Guest Customer</option>
                <option v-for="customer in customers" :key="customer.id" :value="customer.id">{{ customer.name }} ({{ customer.phone }})</option>
              </select>
              <span class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-400 material-symbols-outlined text-sm">expand_more</span>
            </div>
          </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-3 space-y-2 custom-scrollbar bg-gray-50/30">
          <div v-for="(cartItem, index) in cart" :key="index" class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition group overflow-hidden relative">
            <div class="flex justify-between items-start">
              <div class="font-semibold text-gray-800 pr-2 leading-tight">
                {{ cartItem.product.name }}
              </div>
              <div class="font-bold text-gray-900 shrink-0">
                ${{ (cartItem.price * cartItem.quantity).toFixed(2) }}
              </div>
            </div>
            
            <div class="flex justify-between items-center mt-3">
              <div class="flex items-center space-x-1 bg-gray-100 rounded-lg p-0.5 border border-gray-200">
                <button @click="updateQuantity(index, -1)" class="w-7 h-7 flex items-center justify-center rounded-md bg-white text-gray-600 hover:text-red-500 hover:bg-red-50 shadow-sm transition active:scale-95 focus:outline-none">
                   <span class="material-symbols-outlined text-sm">remove</span>
                </button>
                <span class="w-8 text-center font-bold text-gray-800 text-sm select-none">{{ cartItem.quantity }}</span>
                <button @click="updateQuantity(index, 1)" class="w-7 h-7 flex items-center justify-center rounded-md bg-white text-gray-600 hover:text-green-500 hover:bg-green-50 shadow-sm transition active:scale-95 focus:outline-none">
                  <span class="material-symbols-outlined text-sm">add</span>
                </button>
              </div>
              <button @click="removeFromCart(index)" class="text-xs text-red-500 hover:text-red-700 hover:bg-red-50 px-2 py-1 rounded transition opacity-0 group-hover:opacity-100 focus:opacity-100 font-medium">Remove</button>
            </div>
          </div>
          
          <div v-if="cart.length === 0" class="h-full flex flex-col items-center justify-center text-gray-400 opacity-60">
            <span class="material-symbols-outlined text-6xl mb-2">shopping_bag</span>
            <p>Cart is empty</p>
          </div>
        </div>

        <!-- Totals & Payment -->
        <div class="p-5 bg-white border-t-2 border-gray-200 z-10 shadow-[0_-10px_20px_-10px_rgba(0,0,0,0.1)]">
          <!-- Totals Breakdown -->
          <div class="space-y-1.5 mb-4 text-sm px-1">
            <div class="flex justify-between text-gray-600">
              <span>Subtotal</span>
              <span class="font-semibold">${{ cartSubtotal.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>Discount</span>
              <span class="font-semibold text-green-600">-${{ cartDiscount.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between items-end mt-2 pt-2 border-t border-gray-200 border-dashed">
              <span class="text-lg font-bold text-gray-800">Total</span>
              <span class="text-2xl font-black text-indigo-700">${{ cartTotal.toFixed(2) }}</span>
            </div>
          </div>
          
          <!-- Actions -->
          <div class="grid grid-cols-2 gap-3">
             <button @click="cart = []" :disabled="cart.length===0" class="py-3 px-4 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed border border-red-200">
              Clear Cart
            </button>
            <button @click="processPaymentClick" :disabled="cart.length===0 || isSubmitting" class="py-3 px-4 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition active:scale-95 shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2">
              <span class="material-symbols-outlined text-sm" v-if="isSubmitting">autorenew</span>
              <span v-else class="material-symbols-outlined text-sm">payments</span>
              <span>PAY & SEND</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'
import Swal from 'sweetalert2'

const props = defineProps({
  categories: Array,
  items: Array,
  customers: Array,
  tables: Array
})

// UI State
const currentTime = ref('')
const timer = ref(null)

const updateTime = () => {
  currentTime.value = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

onMounted(() => {
  updateTime()
  timer.value = setInterval(updateTime, 1000)
})

onUnmounted(() => clearInterval(timer.value))

// Data State
const searchQuery = ref('')
const selectedCategoryId = ref(null)
const orderType = ref(1) // 1 = Dine-in, 2 = Takeaway
const selectedTableId = ref(null)
const selectedCustomerId = ref(null)
const isSubmitting = ref(false)

const cart = ref([])

// Computed
const filteredItems = computed(() => {
  let result = props.items

  if (selectedCategoryId.value) {
    result = result.filter(item => item.category_id === selectedCategoryId.value)
  }

  if (searchQuery.value) {
    const term = searchQuery.value.toLowerCase()
    result = result.filter(item => item.name.toLowerCase().includes(term))
  }

  return result
})

const cartSubtotal = computed(() => {
  return cart.value.reduce((acc, item) => acc + (item.price * item.quantity), 0)
})

const cartDiscount = computed(() => {
  return 0 // Future promotion logic here
})

const cartTotal = computed(() => {
  return cartSubtotal.value - cartDiscount.value
})

// Methods
const addToCart = (product) => {
  // Check if item exists (simple version without variants)
  const existingIndex = cart.value.findIndex(i => i.product.id === product.id)
  
  if (existingIndex !== -1) {
    cart.value[existingIndex].quantity++
  } else {
    cart.value.unshift({
      product: product,
      quantity: 1,
      price: product.price,
      item_id: product.id,
      variant_id: null
    })
  }

  // Very slight haptic/visual cue can be added via JS animation if needed
}

const updateQuantity = (index, delta) => {
  const newQuantity = cart.value[index].quantity + delta
  if (newQuantity > 0) {
    cart.value[index].quantity = newQuantity
  } else if (newQuantity === 0) {
    removeFromCart(index)
  }
}

const removeFromCart = (index) => {
  cart.value.splice(index, 1)
}

const processPaymentClick = async () => {
  if (cart.value.length === 0) return

  // Basic Validation
  if (orderType.value === 1 && !selectedTableId.value) {
    Swal.fire('Warning', 'Please select a table for Dine-in orders', 'warning')
    return
  }

  // Construct Payload
  const payload = {
    customer_id: selectedCustomerId.value,
    table_id: selectedTableId.value,
    order_type: orderType.value,
    subtotal: cartSubtotal.value,
    discount: cartDiscount.value,
    total_amount: cartTotal.value,
    payment_type: 'cash', // Hardcoded for simplicity in this flow
    items: cart.value.map(c => ({
      item_id: c.item_id,
      variant_id: c.variant_id,
      quantity: c.quantity,
      price: c.price
    }))
  }

  isSubmitting.value = true
  try {
    const res = await axios.post(route('admin.pos.submit'), payload)
    if (res.data.success) {
      Swal.fire({
        title: 'Success!',
        text: 'Order placed and sent to kitchen!',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
      })
      // Reset POS state
      cart.value = []
      selectedTableId.value = null
      selectedCustomerId.value = null
    }
  } catch (error) {
    Swal.fire('Error', error.response?.data?.message || 'Failed to submit order', 'error')
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
.font-heading { font-family: 'Outfit', sans-serif; }
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(99, 102, 241, 0.2);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(99, 102, 241, 0.4);
}
</style>
