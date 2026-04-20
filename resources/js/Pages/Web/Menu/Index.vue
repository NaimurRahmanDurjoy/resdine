<template>
  <WebLayout>
    <template #nav-actions>
      <button @click="isCartOpen = true" class="relative bg-slate-900 text-white px-5 py-2.5 rounded-full font-bold shadow-lg shadow-slate-900/20 hover:bg-slate-800 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-2 group">
        <span class="material-symbols-outlined text-[20px] group-hover:animate-bounce">shopping_basket</span>
        <span class="tracking-wide">Cart</span>
        
        <span v-if="cartTotalItems > 0" class="absolute -top-1.5 -right-1.5 bg-amber-500 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full font-black shadow-sm ring-2 ring-white">
          {{ cartTotalItems }}
        </span>
      </button>
    </template>

    <!-- Hero Section -->
    <div class="relative bg-slate-900 overflow-hidden min-h-[40vh] flex items-center">
      <!-- Decorative background elements -->
      <div class="absolute inset-0 z-0 opacity-20">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-amber-500 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
        <div class="absolute top-48 -left-24 w-96 h-96 bg-orange-600 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 relative z-10 w-full py-16 text-center">
        <span class="text-amber-500 font-bold tracking-widest uppercase text-sm mb-4 block">Welcome to ResDine</span>
        <h1 class="text-5xl md:text-7xl font-black text-white leading-tight mb-6 tracking-tighter">
          Taste the <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">Extraordinary</span>
        </h1>
        <p class="text-slate-300 max-w-2xl mx-auto text-lg md:text-xl font-light mb-8">
          Order for dine-in or takeaway right from your phone. Freshly prepared, delivered to your table.
        </p>
      </div>
    </div>

    <!-- Category Navigation Stacky -->
    <div class="sticky top-16 z-40 bg-white/80 backdrop-blur-xl border-b border-slate-100 shadow-sm transition-all py-3">
      <div class="max-w-7xl mx-auto px-4 overflow-x-auto no-scrollbar">
        <div class="flex space-x-2 w-max">
           <button 
             @click="activeCategory = null"
             :class="[activeCategory === null ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200']"
             class="px-6 py-2.5 rounded-full font-bold text-sm transition-all focus:outline-none whitespace-nowrap">
             All Items
           </button>
           <button 
             v-for="cat in categories" :key="cat.id"
             @click="activeCategory = cat.id"
             :class="[activeCategory === cat.id ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200']"
             class="px-6 py-2.5 rounded-full font-bold text-sm transition-all focus:outline-none whitespace-nowrap">
             {{ cat.name }}
           </button>
        </div>
      </div>
    </div>

    <!-- Menu Grid -->
    <div class="max-w-7xl mx-auto px-4 py-12">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        
        <div v-for="item in filteredItems" :key="item.id" class="group bg-white rounded-[2rem] shadow-sm hover:shadow-2xl border border-slate-100/50 overflow-hidden transition-all duration-300 transform hover:-translate-y-2 flex flex-col">
          <!-- Image Container -->
          <div class="relative h-56 bg-slate-100 overflow-hidden rounded-t-[2rem]">
            <img v-if="item.image_url" :src="item.image_url" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
            <div v-else class="w-full h-full flex items-center justify-center text-slate-300 bg-gradient-to-br from-slate-100 to-slate-200">
              <span class="material-symbols-outlined text-7xl opacity-50">fastfood</span>
            </div>
            
            <!-- Price floating badge -->
            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-md px-4 py-1.5 rounded-full shadow-lg font-black text-slate-900">
              ${{ item.price }}
            </div>
          </div>
          
          <!-- Content -->
          <div class="p-6 flex-1 flex flex-col justify-between">
            <div>
               <h3 class="font-black text-xl text-slate-900 mb-2 leading-tight group-hover:text-amber-600 transition-colors">{{ item.name }}</h3>
               <p class="text-slate-500 text-sm line-clamp-2 leading-relaxed mb-4">{{ item.description || 'Deliciously prepared using the freshest ingredients.' }}</p>
            </div>
            
            <button @click="addToCart(item)" class="w-full bg-slate-50 hover:bg-amber-500 text-slate-900 hover:text-white border border-slate-200 hover:border-amber-500 py-3 rounded-2xl font-bold transition-all duration-300 flex items-center justify-center gap-2 group/btn">
              <span class="material-symbols-outlined text-xl transition-transform group-hover/btn:scale-125">add_shopping_cart</span>
              Add to Order
            </button>
          </div>
        </div>

      </div>
      
      <!-- Empty State -->
      <div v-if="filteredItems.length === 0" class="py-20 text-center">
         <span class="material-symbols-outlined text-8xl text-slate-200 mb-4 inline-block">search_off</span>
         <h2 class="text-2xl font-bold text-slate-700">No items found</h2>
         <p class="text-slate-500 mt-2">Looks like this category is empty right now.</p>
      </div>
    </div>

    <!-- Sliding Cart Overlay -->
    <div v-if="isCartOpen" class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
      <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="isCartOpen = false"></div>

      <div class="fixed inset-y-0 right-0 max-w-full flex">
        <div class="w-screen max-w-md transform transition-transform duration-500 ease-in-out shadow-2xl">
          <div class="h-full flex flex-col bg-white shadow-xl rounded-l-3xl overflow-hidden">
            
            <!-- Cart Header -->
            <div class="px-6 py-5 bg-white border-b border-slate-100 flex items-center justify-between z-10 sticky top-0">
              <h2 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500">shopping_bag</span>
                Your Order
              </h2>
              <button @click="isCartOpen = false" class="text-slate-400 hover:text-slate-800 bg-slate-50 hover:bg-slate-100 p-2 rounded-full transition-all focus:outline-none">
                <span class="material-symbols-outlined">close</span>
              </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 bg-slate-50 relative no-scrollbar">
               <div v-if="cart.length === 0" class="h-full flex flex-col items-center justify-center text-center opacity-50 space-y-4">
                 <span class="material-symbols-outlined text-7xl block">lunch_dining</span>
                 <p class="text-lg font-bold">Your cart is empty and hungry.</p>
               </div>

               <div v-else class="space-y-4">
                  <!-- Order items -->
                  <div v-for="(item, index) in cart" :key="index" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4 animate-fade-in-up">
                    <img v-if="item.product.image_url" :src="item.product.image_url" class="w-16 h-16 rounded-xl object-cover shadow-inner" />
                    <div v-else class="w-16 h-16 rounded-xl bg-slate-100 flex flex-shrink-0 items-center justify-center">
                       <span class="material-symbols-outlined text-slate-300">restaurant</span>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                      <h4 class="font-bold text-slate-900 truncate">
                        {{ item.product.name }}
                        <span v-if="item.variant_name" class="text-xs text-slate-500 font-normal ml-1 border-l pl-1 border-slate-300">{{ item.variant_name }}</span>
                      </h4>
                      <div class="text-amber-600 font-black">${{ (item.price * item.quantity).toFixed(2) }}</div>
                    </div>

                    <div class="flex flex-col items-center bg-slate-50 rounded-xl p-1 border border-slate-100 shadow-inner">
                      <button @click="updateQuantity(index, 1)" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-900 transition"><span class="material-symbols-outlined text-[16px]">add</span></button>
                      <span class="font-bold text-sm my-1">{{ item.quantity }}</span>
                      <button @click="updateQuantity(index, -1)" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-900 transition"><span class="material-symbols-outlined text-[16px]">remove</span></button>
                    </div>
                  </div>

                  <!-- Checkout Form Inline -->
                  <div class="mt-8 pt-6 border-t border-slate-200">
                     <h3 class="font-black text-lg mb-4 text-slate-800">Checkout Details</h3>
                     <div class="space-y-4">
                       
                       <div class="flex p-1 bg-slate-200/50 rounded-xl">
                          <button @click="form.order_type = 1" :class="form.order_type === 1 ? 'bg-white shadow-sm text-amber-600' : 'text-slate-500 hover:text-slate-800'" class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">Dine In</button>
                          <button @click="form.order_type = 2" :class="form.order_type === 2 ? 'bg-white shadow-sm text-amber-600' : 'text-slate-500 hover:text-slate-800'" class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">Takeaway</button>
                       </div>

                       <div v-if="form.order_type === 1">
                          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Table Number</label>
                          <input v-model="form.table_number" type="text" placeholder="e.g. 12" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500 transition shadow-sm">
                       </div>

                       <div>
                          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Your Name</label>
                          <input v-model="form.customer_name" type="text" placeholder="John Doe" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500 transition shadow-sm">
                       </div>
                       
                       <div>
                          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Phone Number</label>
                          <input v-model="form.customer_phone" type="text" placeholder="Phone for updates" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500 transition shadow-sm">
                       </div>

                     </div>
                  </div>
               </div>
            </div>

            <!-- Cart Footer Summary -->
            <div class="bg-white p-6 border-t border-slate-100 z-10 shadow-[0_-10px_30px_-10px_rgba(0,0,0,0.1)]">
              <div class="flex justify-between items-center mb-6">
                 <span class="text-slate-500 font-medium">Subtotal</span>
                 <span class="text-2xl font-black text-slate-900">${{ cartTotal.toFixed(2) }}</span>
              </div>
              <button @click="submitOrder" :disabled="cart.length === 0 || isSubmitting" class="w-full bg-slate-900 text-white font-bold text-lg py-4 rounded-full shadow-xl shadow-slate-900/20 hover:bg-slate-800 hover:-translate-y-1 active:translate-y-0 active:scale-95 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                 <span class="material-symbols-outlined animate-spin" v-if="isSubmitting">autorenew</span>
                 <span>{{ isSubmitting ? 'Placing Order...' : 'Place Order' }}</span>
                 <span class="material-symbols-outlined" v-if="!isSubmitting">arrow_right_alt</span>
              </button>
            </div>

          </div>
        </div>
      </div>
    </div>
    <!-- Variant Modal (Web) Component -->
    <VariantSelectionModal 
        :show="!!selectedProductForVariant" 
        :product="selectedProductForVariant" 
        theme="amber" 
        @close="closeVariantModal" 
        @selected="selectVariant" 
    />
  </WebLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import WebLayout from '@/Layouts/WebLayout.vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import VariantSelectionModal from '@/Components/VariantSelectionModal.vue'

const props = defineProps({
  categories: Array,
  items: Array
})

// State
const activeCategory = ref(null)
const isCartOpen = ref(false)
const cart = ref([])
const isSubmitting = ref(false)
const selectedProductForVariant = ref(null)

const form = reactive({
  customer_name: '',
  customer_phone: '',
  order_type: 1,
  table_number: ''
})

// Computed
const filteredItems = computed(() => {
  if (activeCategory.value) {
    return props.items.filter(i => i.category_id === activeCategory.value)
  }
  return props.items
})

const cartTotalItems = computed(() => cart.value.reduce((acc, curr) => acc + curr.quantity, 0))
const cartTotal = computed(() => cart.value.reduce((acc, curr) => acc + (curr.price * curr.quantity), 0))

// Methods
const addToCart = (product) => {
  if (product.variants && product.variants.length > 0) {
    selectedProductForVariant.value = product
    return
  }

  doAddToCart(product, null, null, product.price)
}

const selectVariant = (variant) => {
  doAddToCart(
    selectedProductForVariant.value,
    variant.id,
    variant.name,
    variant.price || selectedProductForVariant.value.price
  )
  closeVariantModal()
}

const closeVariantModal = () => {
  selectedProductForVariant.value = null
}

const doAddToCart = (product, variantId, variantName, price) => {
  const existing = cart.value.find(i => i.item_id === product.id && i.variant_id === variantId)
  if (existing) {
    existing.quantity++
  } else {
    cart.value.push({
      item_id: product.id,
      product: product,
      price: parseFloat(price),
      quantity: 1,
      variant_id: variantId,
      variant_name: variantName
    })
  }

  // Animation trigger or toast logic could go here
  // Opening cart for demo
  isCartOpen.value = true
}

const updateQuantity = (index, delta) => {
  const item = cart.value[index]
  const newQ = item.quantity + delta
  if (newQ > 0) {
    item.quantity = newQ
  } else {
    cart.value.splice(index, 1)
    if(cart.value.length === 0) {
      isCartOpen.value = false;
    }
  }
}

const submitOrder = async () => {
  if (cart.value.length === 0) return

  // Basic Validation
  if (!form.customer_name || !form.customer_phone) {
    Swal.fire('Required Fields', 'Please enter your name and phone number.', 'error')
    return
  }

  if (form.order_type === 1 && !form.table_number) {
    Swal.fire('Required Fields', 'Please enter your table number for Dine In.', 'error')
    return
  }

  const payload = {
    ...form,
    subtotal: cartTotal.value,
    total_amount: cartTotal.value,
    items: cart.value.map(i => ({
      item_id: i.item_id,
      variant_id: i.variant_id,
      quantity: i.quantity,
      price: i.price
    }))
  }

  isSubmitting.value = true
  try {
    const res = await axios.post(route('web.order.submit'), payload)
    if (res.data.success) {
      isCartOpen.value = false
      Swal.fire({
        title: 'Order Confirmed!',
        text: `Your order #${res.data.order_number} has been sent to the kitchen.`,
        icon: 'success',
        confirmButtonColor: '#f59e0b',
        confirmButtonText: 'Awesome!'
      }).then(() => {
        // Reset state
        cart.value = []
        form.customer_name = ''
        form.customer_phone = ''
        form.table_number = ''
      })
    }
  } catch (error) {
    Swal.fire('Oops...', error.response?.data?.message || 'Something went wrong!', 'error')
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
/* High-End Aesthetics Utilities */
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}

@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
  animation: blob 7s infinite;
}
.animation-delay-2000 {
  animation-delay: 2s;
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
  animation: fadeInUp 0.4s ease-out forwards;
}
</style>
