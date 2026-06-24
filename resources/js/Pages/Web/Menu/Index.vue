<template>
  <WebLayout>
    <template #nav-actions>
      <button @click="isCartOpen = true"
        class="relative bg-slate-900 text-white px-3 md:px-5 py-2.5 rounded-full font-bold shadow-lg shadow-slate-900/20 hover:bg-slate-800 transition-all flex items-center gap-2">
        <span class="material-symbols-outlined text-[20px]">shopping_basket</span>
        <span class="tracking-wide hidden md:inline">Cart</span>

        <span v-if="cartTotalItems > 0"
          class="absolute -top-1.5 -right-1.5 bg-amber-500 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full font-black shadow-sm ring-2 ring-white">
          {{ cartTotalItems }}
        </span>
      </button>
    </template>

    <!-- Hero Section -->
    <div class="relative bg-slate-900 overflow-hidden min-h-[32vh] md:min-h-[40vh] flex items-center">
      <div class="absolute inset-0 z-0 opacity-20">
        <div
          class="absolute -top-24 -right-24 w-96 h-96 bg-amber-500 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob">
        </div>
        <div
          class="absolute top-48 -left-24 w-96 h-96 bg-orange-600 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000">
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 relative z-10 w-full py-10 md:py-16 text-center">
        <span class="text-amber-500 font-bold tracking-widest uppercase text-xs md:text-sm mb-3 md:mb-4 block">Welcome to ResDine</span>
        <h1 class="text-3xl sm:text-4xl md:text-7xl font-black text-white leading-tight mb-4 md:mb-6 tracking-tighter">
          Taste the <span
            class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">Extraordinary</span>
        </h1>
        <p class="text-slate-300 max-w-2xl mx-auto text-sm md:text-xl font-light mb-2">
          Order for dine-in or takeaway right from your phone.
        </p>
      </div>
    </div>

    <!-- Category Navigation -->
    <div
      class="sticky top-14 md:top-16 z-40 bg-white/90 backdrop-blur-xl border-b border-slate-100 shadow-sm transition-all py-2.5 md:py-3">
      <div class="max-w-7xl mx-auto px-2 md:px-4 relative">
        <!-- Arrows: desktop only, mobile relies on natural swipe -->
        <div class="hidden md:flex absolute inset-y-0 left-0 items-center pl-1">
          <button type="button" @click="scrollCategory(-280)"
            class="p-2 rounded-full bg-white shadow-sm text-slate-600 hover:text-slate-900 hover:shadow-md transition focus:outline-none">
            <span class="material-symbols-outlined">chevron_left</span>
          </button>
        </div>

        <div ref="categoryNav" class="overflow-x-auto no-scrollbar scroll-smooth px-4 md:px-6" @wheel.prevent="onCategoryWheel"
          tabindex="0" role="group" aria-label="Category navigation">
          <div class="flex space-x-2 w-max py-1">
            <button @click="activeCategory = null"
              :class="[activeCategory === null ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 active:bg-slate-200']"
              class="px-5 md:px-6 py-2 md:py-2.5 rounded-full font-bold text-xs md:text-sm transition-all focus:outline-none whitespace-nowrap">
              All Items
            </button>
            <button v-for="cat in categories" :key="cat.id" @click="activeCategory = cat.id"
              :class="[activeCategory === cat.id ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 active:bg-slate-200']"
              class="px-5 md:px-6 py-2 md:py-2.5 rounded-full font-bold text-xs md:text-sm transition-all focus:outline-none whitespace-nowrap">
              {{ cat.name }}
            </button>
          </div>
        </div>

        <div class="hidden md:flex absolute inset-y-0 right-0 items-center pr-1">
          <button type="button" @click="scrollCategory(280)"
            class="p-2 rounded-full bg-white shadow-sm text-slate-600 hover:text-slate-900 hover:shadow-md transition focus:outline-none">
            <span class="material-symbols-outlined">chevron_right</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Menu Grid -->
    <div class="max-w-7xl mx-auto px-3 md:px-4 py-6 md:py-12">
      <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-8">

        <div v-for="item in filteredItems" :key="item.id"
          class="group bg-white rounded-2xl md:rounded-[2rem] shadow-sm active:shadow-md md:hover:shadow-2xl border border-slate-100/50 overflow-hidden transition-all duration-300 md:transform md:hover:-translate-y-2 flex flex-col">
          <!-- Image Container: shorter on mobile to reduce scroll -->
          <div class="relative h-28 md:h-56 bg-slate-100 overflow-hidden rounded-t-2xl md:rounded-t-[2rem]">
            <img v-if="item.image_url" :src="item.image_url"
              class="w-full h-full object-cover transition-transform duration-700 md:group-hover:scale-110" />
            <div v-else
              class="w-full h-full flex items-center justify-center text-slate-300 bg-gradient-to-br from-slate-100 to-slate-200">
              <span class="material-symbols-outlined text-4xl md:text-7xl opacity-50">fastfood</span>
            </div>

            <div
              class="absolute top-2 right-2 md:top-4 md:right-4 bg-white/90 backdrop-blur-md px-2.5 md:px-4 py-1 md:py-1.5 rounded-full shadow-lg font-black text-slate-900 text-xs md:text-base">
              {{ currency() }}{{ item.price }}
            </div>
          </div>

          <!-- Content: tighter padding on mobile -->
          <div class="p-3 md:p-6 flex-1 flex flex-col justify-between">
            <div>
              <h3
                class="font-black text-sm md:text-xl text-slate-900 mb-1 md:mb-2 leading-tight line-clamp-1 md:group-hover:text-amber-600 transition-colors">
                {{ item.name }}</h3>
              <p class="hidden md:block text-slate-500 text-sm line-clamp-2 leading-relaxed mb-4">
                {{ item.description || 'Deliciously prepared using the freshest ingredients.' }}</p>
            </div>

            <button @click="addToCart(item)"
              class="w-full bg-slate-50 active:bg-amber-500 md:hover:bg-amber-500 text-slate-900 active:text-white md:hover:text-white border border-slate-200 active:border-amber-500 md:hover:border-amber-500 py-2 md:py-3 rounded-xl md:rounded-2xl font-bold text-xs md:text-base transition-all duration-300 flex items-center justify-center gap-1.5">
              <span class="material-symbols-outlined text-base md:text-xl">add_shopping_cart</span>
              <span class="hidden sm:inline">Add to Order</span>
              <span class="sm:hidden">Add</span>
            </button>
          </div>
        </div>

      </div>

      <div v-if="filteredItems.length === 0" class="py-16 md:py-20 text-center">
        <span class="material-symbols-outlined text-6xl md:text-8xl text-slate-200 mb-4 inline-block">search_off</span>
        <h2 class="text-xl md:text-2xl font-bold text-slate-700">No items found</h2>
        <p class="text-slate-500 mt-2 text-sm md:text-base">Looks like this category is empty right now.</p>
      </div>
    </div>

    <!-- Sticky bottom "View Cart" bar — mobile only, hidden once drawer is open -->
    <Transition name="slide-up">
      <button v-if="cartTotalItems > 0 && !isCartOpen" @click="isCartOpen = true"
        class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-slate-900 text-white px-4 py-4 flex items-center justify-between shadow-[0_-8px_30px_-5px_rgba(0,0,0,0.3)]"
        style="padding-bottom: max(1rem, env(safe-area-inset-bottom));">
        <span class="flex items-center gap-2 font-bold text-sm">
          <span class="bg-amber-500 text-white text-xs w-6 h-6 rounded-full flex items-center justify-center font-black">{{ cartTotalItems }}</span>
          View Cart
        </span>
        <span class="font-black">{{ currency() }}{{ cartTotal.toFixed(2) }}</span>
      </button>
    </Transition>

    <!-- Sliding Cart Overlay -->
    <div v-if="isCartOpen" class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog"
      aria-modal="true">
      <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeCart"></div>

      <div class="fixed inset-y-0 right-0 w-full md:max-w-md flex">
        <div class="w-full transform transition-transform duration-500 ease-in-out shadow-2xl">
          <div class="h-full flex flex-col bg-white shadow-xl md:rounded-l-3xl overflow-hidden">

            <!-- Header -->
            <div
              class="px-4 md:px-6 py-4 md:py-5 bg-white border-b border-slate-100 flex items-center justify-between z-10 sticky top-0">
              <div class="flex items-center gap-2">
                <button v-if="checkoutStep === 2" @click="checkoutStep = 1"
                  class="text-slate-400 hover:text-slate-800 p-1 -ml-1 rounded-full transition">
                  <span class="material-symbols-outlined">arrow_back</span>
                </button>
                <h2 class="text-xl md:text-2xl font-black text-slate-900 flex items-center gap-2">
                  <span class="material-symbols-outlined text-amber-500">{{ checkoutStep === 1 ? 'shopping_bag' : 'receipt_long' }}</span>
                  {{ checkoutStep === 1 ? 'Your Order' : 'Checkout' }}
                </h2>
              </div>
              <button @click="closeCart"
                class="text-slate-400 hover:text-slate-800 bg-slate-50 hover:bg-slate-100 p-2 rounded-full transition-all focus:outline-none">
                <span class="material-symbols-outlined">close</span>
              </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 md:p-6 bg-slate-50 relative no-scrollbar">
              <div v-if="cart.length === 0"
                class="h-full flex flex-col items-center justify-center text-center opacity-50 space-y-4">
                <span class="material-symbols-outlined text-7xl block">lunch_dining</span>
                <p class="text-lg font-bold">Your cart is empty and hungry.</p>
              </div>

              <!-- STEP 1: Review items -->
              <div v-else-if="checkoutStep === 1" class="space-y-3">
                <div v-for="(item, index) in cart" :key="index"
                  class="bg-white p-3 md:p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3 md:gap-4 animate-fade-in-up">
                  <img v-if="item.product.image_url" :src="item.product.image_url"
                    class="w-14 h-14 md:w-16 md:h-16 rounded-xl object-cover shadow-inner flex-shrink-0" />
                  <div v-else class="w-14 h-14 md:w-16 md:h-16 rounded-xl bg-slate-100 flex flex-shrink-0 items-center justify-center">
                    <span class="material-symbols-outlined text-slate-300">restaurant</span>
                  </div>

                  <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-sm md:text-base text-slate-900 truncate">
                      {{ item.product.name }}
                      <span v-if="item.variant_name"
                        class="text-xs text-slate-500 font-normal ml-1 border-l pl-1 border-slate-300">{{
                          item.variant_name
                        }}</span>
                    </h4>
                    <div class="text-amber-600 font-black text-sm md:text-base">{{ currency() }}{{ (item.price *
                      item.quantity).toFixed(2)
                    }}
                    </div>
                  </div>

                  <div
                    class="flex flex-col items-center bg-slate-50 rounded-xl p-1 border border-slate-100 shadow-inner shrink-0">
                    <button @click="updateQuantity(index, 1)"
                      class="w-7 h-7 md:w-6 md:h-6 flex items-center justify-center text-slate-500 active:text-slate-900 transition"><span
                        class="material-symbols-outlined text-[16px]">add</span></button>
                    <span class="font-bold text-sm my-1">{{ item.quantity }}</span>
                    <button @click="updateQuantity(index, -1)"
                      class="w-7 h-7 md:w-6 md:h-6 flex items-center justify-center text-slate-500 active:text-slate-900 transition"><span
                        class="material-symbols-outlined text-[16px]">remove</span></button>
                  </div>
                </div>
              </div>

              <!-- STEP 2: Checkout form (separate screen on mobile, less scroll-and-lose-cart-context) -->
              <div v-else class="space-y-4">
                <div class="flex p-1 bg-slate-200/50 rounded-xl">
                  <button @click="form.order_type = 1; form.payment_method = null; form.table_number = ''"
                    :class="form.order_type === 1 ? 'bg-white shadow-sm text-amber-600' : 'text-slate-500 active:text-slate-800'"
                    class="flex-1 py-2.5 text-xs md:text-sm font-bold rounded-lg transition-all">Dine In</button>
                  <button @click="form.order_type = 2"
                    :class="form.order_type === 2 ? 'bg-white shadow-sm text-amber-600' : 'text-slate-500 active:text-slate-800'"
                    class="flex-1 py-2.5 text-xs md:text-sm font-bold rounded-lg transition-all">Takeaway</button>
                  <button @click="form.order_type = 3"
                    :class="form.order_type === 3 ? 'bg-white shadow-sm text-amber-600' : 'text-slate-500 active:text-slate-800'"
                    class="flex-1 py-2.5 text-xs md:text-sm font-bold rounded-lg transition-all">Delivery</button>
                </div>

                <div v-if="form.order_type === 1">
                  <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Table
                    Number</label>
                  <input v-model="form.table_number" type="text" placeholder="e.g. 12" inputmode="numeric"
                    class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-amber-500 transition shadow-sm">
                </div>

                <div v-if="form.order_type === 3" class="space-y-3">
                  <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Delivery
                      Address</label>
                    <textarea v-model="form.delivery_address"
                      placeholder="Enter your full street address, apartment, flat no., etc." rows="2"
                      class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-amber-500 transition shadow-sm resize-none"></textarea>
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Delivery
                      Instructions (Optional)</label>
                    <input v-model="form.delivery_instructions" type="text"
                      placeholder="e.g. Leave at door, call before arriving"
                      class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-amber-500 transition shadow-sm">
                  </div>
                </div>

                <div v-if="form.order_type === 2 || form.order_type === 3">
                  <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Payment
                    Method
                    (Instant Pay)</label>
                  <div class="grid grid-cols-2 gap-2">
                    <button @click="form.payment_method = 1" type="button"
                      :class="form.payment_method === 1 ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-slate-600 border-slate-200'"
                      class="flex items-center justify-center gap-1.5 py-3.5 rounded-xl border font-bold text-xs transition-all shadow-sm">
                      <span class="material-symbols-outlined text-base">payments</span> Cash
                    </button>
                    <button @click="form.payment_method = 3" type="button"
                      :class="form.payment_method === 3 ? 'bg-pink-600 text-white border-pink-600' : 'bg-white text-slate-600 border-slate-200'"
                      class="flex items-center justify-center gap-1.5 py-3.5 rounded-xl border font-bold text-xs transition-all shadow-sm">
                      <span class="material-symbols-outlined text-base">smartphone</span> bKash
                    </button>
                    <button @click="form.payment_method = 2" type="button"
                      :class="form.payment_method === 2 ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-200'"
                      class="flex items-center justify-center gap-1.5 py-3.5 rounded-xl border font-bold text-xs transition-all shadow-sm">
                      <span class="material-symbols-outlined text-base">credit_card</span> Stripe
                    </button>
                    <button @click="form.payment_method = 4" type="button"
                      :class="form.payment_method === 4 ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200'"
                      class="flex items-center justify-center gap-1.5 py-3.5 rounded-xl border font-bold text-xs transition-all shadow-sm">
                      <span class="material-symbols-outlined text-base">account_balance_wallet</span> SSLCommerz
                    </button>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Your
                    Name</label>
                  <input v-model="form.customer_name" type="text" placeholder="John Doe" autocomplete="name"
                    class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-amber-500 transition shadow-sm">
                </div>

                <div>
                  <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Phone
                    Number</label>
                  <input v-model="form.customer_phone" type="tel" placeholder="Phone for updates" autocomplete="tel"
                    inputmode="tel"
                    class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3.5 text-base focus:outline-none focus:ring-2 focus:ring-amber-500 transition shadow-sm">
                </div>
              </div>
            </div>

            <!-- Footer: summary + primary action, changes by step -->
            <div v-if="cart.length > 0"
              class="bg-white p-4 md:p-6 border-t border-slate-100 z-10 shadow-[0_-10px_30px_-10px_rgba(0,0,0,0.1)] space-y-2.5 md:space-y-3"
              style="padding-bottom: max(1rem, env(safe-area-inset-bottom));">
              <div class="flex justify-between items-center text-sm">
                <span class="text-slate-500 font-medium">Subtotal</span>
                <span class="font-bold text-slate-800">{{ currency() }}{{ cartSubtotal.toFixed(2) }}</span>
              </div>
              <div v-if="cartVatAmount > 0" class="flex justify-between items-center text-sm">
                <span class="text-slate-500 font-medium">VAT ({{ branchSetting.vat_percentage }}%{{
                  branchSetting.is_vat_inclusive ? ' Incl.' : ' Excl.' }})</span>
                <span class="font-bold text-slate-800">{{ currency() }}{{ cartVatAmount.toFixed(2) }}</span>
              </div>
              <div v-if="cartServiceChargeAmount > 0" class="flex justify-between items-center text-sm">
                <span class="text-slate-500 font-medium">Service Charge ({{
                  branchSetting.service_charge_percentage
                }}%)</span>
                <span class="font-bold text-slate-800">{{ currency() }}{{ cartServiceChargeAmount.toFixed(2)
                }}</span>
              </div>
              <div class="flex justify-between items-center pt-2.5 md:pt-3 border-t border-slate-100 mb-1 md:mb-2">
                <span class="text-slate-900 font-black text-base md:text-lg">Total Amount</span>
                <span class="text-xl md:text-2xl font-black text-slate-900">{{ currency() }}{{ cartTotal.toFixed(2) }}</span>
              </div>

              <button v-if="checkoutStep === 1" @click="checkoutStep = 2"
                class="w-full bg-slate-900 text-white font-bold text-base md:text-lg py-3.5 md:py-4 rounded-full shadow-xl shadow-slate-900/20 active:bg-slate-800 active:scale-95 transition-all flex items-center justify-center gap-2">
                <span>Proceed to Checkout</span>
                <span class="material-symbols-outlined">arrow_right_alt</span>
              </button>

              <button v-else @click="submitOrder" :disabled="isSubmitting"
                class="w-full bg-slate-900 text-white font-bold text-base md:text-lg py-3.5 md:py-4 rounded-full shadow-xl shadow-slate-900/20 active:bg-slate-800 active:scale-95 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="material-symbols-outlined animate-spin" v-if="isSubmitting">autorenew</span>
                <span>{{ isSubmitting ? 'Placing Order...' : 'Place Order' }}</span>
                <span class="material-symbols-outlined" v-if="!isSubmitting">arrow_right_alt</span>
              </button>
            </div>

          </div>
        </div>
      </div>
    </div>

    <VariantSelectionModal :show="!!selectedProductForVariant" :product="selectedProductForVariant" theme="amber"
      @close="closeVariantModal" @selected="selectVariant" />
    <CampaignModal :activeCampaigns="activeCampaigns" />
  </WebLayout>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import WebLayout from '@/Layouts/WebLayout.vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import VariantSelectionModal from '@/Components/VariantSelectionModal.vue'
import CampaignModal from '@/Components/CampaignModal.vue'

const page = usePage()

const props = defineProps({
  categories: Array,
  items: Array,
  activeCampaigns: Array,
  branchSetting: Object
})

// State
const activeCategory = ref(null)
const isCartOpen = ref(false)
const checkoutStep = ref(1) // 1 = review items, 2 = checkout form
const cart = ref([])
const isSubmitting = ref(false)
const selectedProductForVariant = ref(null)
const categoryNav = ref(null)

const form = reactive({
  customer_name: '',
  customer_phone: '',
  order_type: 1,
  table_number: '',
  payment_method: null,
  delivery_address: '',
  delivery_instructions: ''
})

// Computed
const filteredItems = computed(() => {
  if (activeCategory.value) {
    return props.items.filter(i => i.category_id === activeCategory.value)
  }
  return props.items
})

const cartTotalItems = computed(() => cart.value.reduce((acc, curr) => acc + curr.quantity, 0))
const cartSubtotal = computed(() => cart.value.reduce((acc, curr) => acc + (curr.price * curr.quantity), 0))

const cartVatAmount = computed(() => {
  const vatPercent = parseFloat(props.branchSetting?.vat_percentage || 0)
  if (vatPercent <= 0) return 0

  if (props.branchSetting?.is_vat_inclusive) {
    return cartSubtotal.value - (cartSubtotal.value / (1 + vatPercent / 100))
  } else {
    return cartSubtotal.value * (vatPercent / 100)
  }
})

const cartServiceChargeAmount = computed(() => {
  const serviceChargePercent = parseFloat(props.branchSetting?.service_charge_percentage || 0)
  if (serviceChargePercent <= 0) return 0
  return cartSubtotal.value * (serviceChargePercent / 100)
})

const cartTotal = computed(() => {
  if (props.branchSetting?.is_vat_inclusive) {
    return cartSubtotal.value + cartServiceChargeAmount.value
  } else {
    return cartSubtotal.value + cartVatAmount.value + cartServiceChargeAmount.value
  }
})

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
  // Note: cart no longer auto-opens on add — keeps the user browsing the menu,
  // the sticky bottom bar gives them a persistent, low-friction way to check out
  // when they're ready. Re-enable the line below if you prefer auto-open:
  // isCartOpen.value = true
}

const updateQuantity = (index, delta) => {
  const item = cart.value[index]
  const newQ = item.quantity + delta
  if (newQ > 0) {
    item.quantity = newQ
  } else {
    cart.value.splice(index, 1)
    if (cart.value.length === 0) {
      isCartOpen.value = false
      checkoutStep.value = 1
    }
  }
}

const closeCart = () => {
  isCartOpen.value = false
  checkoutStep.value = 1
}

const scrollCategory = (distance) => {
  const nav = categoryNav.value
  if (!nav) return
  nav.scrollBy({ left: distance, behavior: 'smooth' })
}

const onCategoryWheel = (event) => {
  const nav = categoryNav.value
  if (!nav) return
  nav.scrollBy({ left: event.deltaY, behavior: 'smooth' })
}

const submitOrder = async () => {
  if (cart.value.length === 0) return

  if (!form.customer_name || !form.customer_phone) {
    Swal.fire('Required Fields', 'Please enter your name and phone number.', 'error')
    return
  }

  if (form.order_type === 1 && !form.table_number) {
    Swal.fire('Required Fields', 'Please enter your table number for Dine In.', 'error')
    return
  }

  if ((form.order_type === 2 || form.order_type === 3) && !form.payment_method) {
    Swal.fire('Required Fields', `Please select a payment method for ${form.order_type === 2 ? 'Takeaway' : 'Delivery'}.`, 'error')
    return
  }

  if (form.order_type === 3 && !form.delivery_address) {
    Swal.fire('Required Fields', 'Please enter a delivery address.', 'error')
    return
  }

  const payload = {
    ...form,
    subtotal: cartSubtotal.value,
    vat_amount: cartVatAmount.value,
    service_charge_amount: cartServiceChargeAmount.value,
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

      if (res.data.redirect_url) {
        cart.value = []
        window.location.href = res.data.redirect_url
        return
      }

      Swal.fire({
        title: 'Order Confirmed!',
        text: `Your order #${res.data.order_number} has been placed successfully.`,
        icon: 'success',
        confirmButtonColor: '#f59e0b',
        confirmButtonText: 'Track Order'
      }).then(() => {
        const orderNum = res.data.order_number
        cart.value = []
        checkoutStep.value = 1
        form.customer_name = ''
        form.customer_phone = ''
        form.table_number = ''
        form.delivery_address = ''
        form.delivery_instructions = ''

        window.location.href = route('web.order.track', { orderNumber: orderNum })
      })
    }
  } catch (error) {
    Swal.fire('Oops...', error.response?.data?.message || 'Something went wrong!', 'error')
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  const flash = page.props.flash || {}
  if (flash.success) {
    Swal.fire({
      title: 'Success!',
      text: flash.success,
      icon: 'success',
      confirmButtonColor: '#f59e0b',
    })
  } else if (flash.error) {
    Swal.fire({
      title: 'Error!',
      text: flash.error,
      icon: 'error',
      confirmButtonColor: '#ef4444',
    })
  }
})
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}

.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
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

/* Sticky bottom cart bar transition */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.3s ease, opacity 0.3s ease;
}
.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
  opacity: 0;
}
</style>