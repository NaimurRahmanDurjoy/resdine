<template>
  <WebLayout>
    <div class="min-h-screen bg-slate-50 py-12">
      <div class="max-w-3xl mx-auto px-4">
        
        <!-- Header / Back button -->
        <div class="flex items-center justify-between mb-8">
          <a href="/menu" class="inline-flex items-center text-slate-600 hover:text-slate-900 font-bold transition gap-2">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            <span>Back to Menu</span>
          </a>
          <span class="bg-amber-100 text-amber-800 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider">
            Live Updates Enabled
          </span>
        </div>

        <!-- Tracking Main Card -->
        <div class="bg-white rounded-3xl shadow-xl border border-slate-100/50 overflow-hidden mb-8">
          <!-- Card Hero Banner -->
          <div class="bg-slate-900 px-8 py-10 text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-amber-400 via-orange-600 to-slate-900"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
              <div>
                <p class="text-slate-400 text-sm font-semibold tracking-wider uppercase mb-1">Order Tracking</p>
                <h2 class="text-3xl font-black tracking-tight flex items-center gap-3">
                  #{{ currentOrder.order_number }}
                </h2>
              </div>
              <div class="text-left md:text-right">
                <p class="text-slate-400 text-sm font-semibold tracking-wider uppercase mb-1">Total Paid</p>
                <p class="text-3xl font-black text-amber-400">{{ currency() }}{{ currentOrder.total_amount }}</p>
              </div>
            </div>
          </div>

          <!-- Timeline Container -->
          <div class="p-8">
            <!-- Timeline Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-6 mb-8">
              <div>
                <h3 class="font-black text-xl text-slate-800">Delivery Status</h3>
                <p class="text-slate-500 text-sm mt-1">Status: <span class="font-bold text-amber-500">{{ statusLabel }}</span></p>
              </div>
              <div v-if="currentOrder.order_status === 5" class="bg-red-100 text-red-700 px-4 py-2 rounded-xl font-bold flex items-center gap-1.5 animate-pulse">
                <span class="material-symbols-outlined text-lg">cancel</span>
                <span>Cancelled</span>
              </div>
              <div v-else-if="currentOrder.order_status === 4" class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-xl font-bold flex items-center gap-1.5">
                <span class="material-symbols-outlined text-lg">check_circle</span>
                <span>Delivered!</span>
              </div>
              <div v-else class="flex items-center gap-2 text-slate-400 text-sm">
                <span class="material-symbols-outlined animate-spin text-amber-500 text-lg">sync</span>
                <span>Updating in real-time...</span>
              </div>
            </div>

            <!-- Steps Progress Line -->
            <div v-if="currentOrder.order_status !== 5" class="relative pl-8 md:pl-0 md:grid md:grid-cols-5 gap-4">
              
              <!-- Timeline vertical line (mobile) & horizontal line (desktop) -->
              <div class="hidden md:block absolute top-[28px] left-[10%] right-[10%] h-1 bg-slate-100 -z-0">
                <div class="h-full bg-amber-500 transition-all duration-1000" :style="{ width: progressLineWidth }"></div>
              </div>
              <div class="md:hidden absolute top-8 bottom-8 left-4 w-1 bg-slate-100 -z-0">
                <div class="w-full bg-amber-500 transition-all duration-1000" :style="{ height: progressLineWidth }"></div>
              </div>

              <!-- Step Item: Pending -->
              <div class="relative z-10 flex md:flex-col items-start md:items-center text-left md:text-center pb-8 md:pb-0 group">
                <div :class="stepClass(0)" class="w-10 h-10 rounded-full flex items-center justify-center border-4 font-bold transition-all duration-500 shrink-0">
                  <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                </div>
                <div class="ml-4 md:ml-0 md:mt-3">
                  <h4 class="font-bold text-slate-800 text-sm md:text-base">Order Received</h4>
                  <p class="text-slate-400 text-xs mt-0.5">Kitchen is reviewing</p>
                </div>
              </div>

              <!-- Step Item: Preparing -->
              <div class="relative z-10 flex md:flex-col items-start md:items-center text-left md:text-center pb-8 md:pb-0 group">
                <div :class="stepClass(1)" class="w-10 h-10 rounded-full flex items-center justify-center border-4 font-bold transition-all duration-500 shrink-0">
                  <span class="material-symbols-outlined text-[18px]">skillet</span>
                </div>
                <div class="ml-4 md:ml-0 md:mt-3">
                  <h4 class="font-bold text-slate-800 text-sm md:text-base">Preparing</h4>
                  <p class="text-slate-400 text-xs mt-0.5">Chef is cooking</p>
                </div>
              </div>

              <!-- Step Item: Ready -->
              <div class="relative z-10 flex md:flex-col items-start md:items-center text-left md:text-center pb-8 md:pb-0 group">
                <div :class="stepClass(2)" class="w-10 h-10 rounded-full flex items-center justify-center border-4 font-bold transition-all duration-500 shrink-0">
                  <span class="material-symbols-outlined text-[18px]">restaurant</span>
                </div>
                <div class="ml-4 md:ml-0 md:mt-3">
                  <h4 class="font-bold text-slate-800 text-sm md:text-base">Ready</h4>
                  <p class="text-slate-400 text-xs mt-0.5">Packed & ready</p>
                </div>
              </div>

              <!-- Step Item: Out for Delivery / Dispatch -->
              <div class="relative z-10 flex md:flex-col items-start md:items-center text-left md:text-center pb-8 md:pb-0 group">
                <div :class="stepClass(3)" class="w-10 h-10 rounded-full flex items-center justify-center border-4 font-bold transition-all duration-500 shrink-0">
                  <span class="material-symbols-outlined text-[18px]">delivery_dining</span>
                </div>
                <div class="ml-4 md:ml-0 md:mt-3">
                  <h4 class="font-bold text-slate-800 text-sm md:text-base">Out for Delivery</h4>
                  <p class="text-slate-400 text-xs mt-0.5">Rider is on the way</p>
                </div>
              </div>

              <!-- Step Item: Completed -->
              <div class="relative z-10 flex md:flex-col items-start md:items-center text-left md:text-center group">
                <div :class="stepClass(4)" class="w-10 h-10 rounded-full flex items-center justify-center border-4 font-bold transition-all duration-500 shrink-0">
                  <span class="material-symbols-outlined text-[18px]">home_pin</span>
                </div>
                <div class="ml-4 md:ml-0 md:mt-3">
                  <h4 class="font-bold text-slate-800 text-sm md:text-base">Delivered</h4>
                  <p class="text-slate-400 text-xs mt-0.5">Enjoy your food!</p>
                </div>
              </div>

            </div>

            <!-- Cancelled State Detail -->
            <div v-else class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
              <span class="material-symbols-outlined text-slate-300 text-7xl mb-3 block">cancel</span>
              <h3 class="text-lg font-black text-slate-800">This order has been cancelled</h3>
              <p class="text-slate-500 text-sm mt-1 max-w-sm mx-auto">
                Please contact the branch or guest support if you have any questions. We apologize for the inconvenience.
              </p>
            </div>

          </div>
        </div>

        <!-- Delivery Details / Driver Card -->
        <div v-if="currentOrder.order_type === 3 && currentOrder.delivery" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <!-- Address & Instructions -->
          <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-lg shadow-slate-100/50 flex flex-col justify-between">
            <div>
              <h4 class="font-black text-slate-800 text-lg mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500">pin_drop</span>
                Delivery Details
              </h4>
              <p class="text-xs text-slate-400 font-bold uppercase tracking-wide">Destination Address</p>
              <p class="text-slate-700 text-sm font-semibold mt-1 mb-4 leading-relaxed">{{ currentOrder.delivery.delivery_address }}</p>
            </div>
            
            <div v-if="currentOrder.delivery.special_instructions" class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
              <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Rider Instructions</p>
              <p class="text-slate-600 text-xs italic">"{{ currentOrder.delivery.special_instructions }}"</p>
            </div>
          </div>

          <!-- Driver Card -->
          <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-lg shadow-slate-100/50 flex flex-col justify-between">
            <h4 class="font-black text-slate-800 text-lg mb-4 flex items-center gap-2">
              <span class="material-symbols-outlined text-amber-500">badge</span>
              Assigned Courier
            </h4>

            <div v-if="currentOrder.delivery.driver_name" class="flex items-center gap-4 py-2">
              <div class="w-14 h-14 rounded-full bg-amber-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/20 font-black text-xl shrink-0">
                {{ currentOrder.delivery.driver_name.charAt(0).toUpperCase() }}
              </div>
              <div>
                <h5 class="font-black text-slate-800 text-base">{{ currentOrder.delivery.driver_name }}</h5>
                <p class="text-slate-400 text-xs">Delivery Partner</p>
                <div class="flex items-center gap-1.5 mt-1 text-slate-600 font-bold text-sm">
                  <span class="material-symbols-outlined text-sm">call</span>
                  <span>{{ currentOrder.delivery.driver_phone || 'No phone provided' }}</span>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
              <span class="material-symbols-outlined text-slate-400 text-3xl animate-bounce mb-1 block">sports_motorsports</span>
              <p class="font-bold text-slate-600 text-sm">Assigning Rider...</p>
              <p class="text-slate-400 text-xs mt-0.5">We will assign a driver shortly.</p>
            </div>

            <div class="text-xs text-slate-400 text-center mt-3 pt-3 border-t border-slate-100">
              Need assistance? Call support at +880 1234 5678
            </div>
          </div>
        </div>

      </div>
    </div>
  </WebLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import WebLayout from '@/Layouts/WebLayout.vue'
import axios from 'axios'

const props = defineProps({
  order: Object
})

const currentOrder = ref(props.order)
let pollInterval = null

// Status Labels helper
const statusLabel = computed(() => {
  const statuses = {
    0: 'Order Received',
    1: 'Preparing in Kitchen',
    2: 'Order Ready',
    3: 'Rider Out for Delivery',
    4: 'Delivered',
    5: 'Cancelled'
  }
  return statuses[currentOrder.value.order_status] || 'Unknown'
})

// Calculate percentage for progress line
const progressLineWidth = computed(() => {
  const status = currentOrder.value.order_status
  if (status === 5) return '0%'
  if (status === 4) return '100%'
  // Map 0 -> 0%, 1 -> 25%, 2 -> 50%, 3 -> 75%, 4 -> 100%
  const percentage = status * 25
  return `${percentage}%`
})

// Set active styles for steps
const stepClass = (stepNum) => {
  const currentStatus = currentOrder.value.order_status
  
  if (currentStatus === 5) {
    return 'bg-slate-100 text-slate-400 border-slate-200'
  }

  if (currentStatus === stepNum) {
    return 'bg-amber-500 text-white border-amber-500 shadow-lg shadow-amber-500/30 scale-110 ring-4 ring-amber-500/20'
  } else if (currentStatus > stepNum) {
    return 'bg-emerald-500 text-white border-emerald-500 shadow-md'
  } else {
    return 'bg-white text-slate-400 border-slate-200'
  }
}

// Currency Symbol
const currency = () => {
  return '$' // Replace with system currency configuration helper if available
}

// Live polling for status changes
const pollOrderStatus = async () => {
  try {
    const res = await axios.get(route('web.order.track', { orderNumber: currentOrder.value.order_number }), {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    
    // Check if the response actually contains the Inertia component properties or json
    if (res.data && res.data.props && res.data.props.order) {
      currentOrder.value = res.data.props.order
    } else if (res.data && res.data.order) {
      currentOrder.value = res.data.order
    }
  } catch (error) {
    console.error('Error polling order status:', error)
  }
}

onMounted(() => {
  // Poll every 8 seconds for fast updates in tracking
  pollInterval = setInterval(pollOrderStatus, 8000)
})

onUnmounted(() => {
  if (pollInterval) {
    clearInterval(pollInterval)
  }
})
</script>

<style scoped>
/* Additional animations or transitions */
</style>
