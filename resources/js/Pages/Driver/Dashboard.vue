<template>
  <div class="min-h-screen bg-slate-900 text-slate-100 font-sans pb-10">
    
    <!-- Top Header -->
    <header class="bg-slate-950 border-b border-slate-800 sticky top-0 z-50 px-6 py-4 shadow-xl">
      <div class="max-w-4xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center font-black shadow-lg shadow-amber-500/20">
            <span class="material-symbols-outlined">delivery_dining</span>
          </div>
          <div>
            <h1 class="font-black text-lg tracking-tight">Courier Panel</h1>
            <p class="text-slate-400 text-xs font-semibold">{{ $page.props.auth?.user?.name || 'Rider' }}</p>
          </div>
        </div>
        
        <Link href="/logout" method="post" as="button" 
          class="flex items-center justify-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-red-900/40 hover:text-red-400 border border-slate-700 hover:border-red-900/60 rounded-xl transition font-bold text-xs">
          <span class="material-symbols-outlined text-sm">logout</span>
          <span>Logout</span>
        </Link>
      </div>
    </header>

    <!-- Stats Summary Section -->
    <main class="max-w-4xl mx-auto px-4 mt-6">
      
      <!-- Metrics Card -->
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-slate-950/50 p-5 rounded-3xl border border-slate-800 shadow-md">
          <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block mb-1">Active Shipments</span>
          <span class="text-3xl font-black text-amber-500">{{ activeOrders.length }}</span>
        </div>
        <div class="bg-slate-950/50 p-5 rounded-3xl border border-slate-800 shadow-md">
          <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block mb-1">Completed</span>
          <span class="text-3xl font-black text-emerald-500">{{ completedOrders.length }}</span>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex p-1.5 bg-slate-950 rounded-2xl border border-slate-800 mb-6">
        <button @click="activeTab = 'active'"
          :class="activeTab === 'active' ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/10' : 'text-slate-400 hover:text-slate-200'"
          class="flex-1 py-3 text-xs font-black uppercase tracking-wider rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
          <span class="material-symbols-outlined text-sm">local_shipping</span>
          Active ({{ activeOrders.length }})
        </button>
        <button @click="activeTab = 'completed'"
          :class="activeTab === 'completed' ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/10' : 'text-slate-400 hover:text-slate-200'"
          class="flex-1 py-3 text-xs font-black uppercase tracking-wider rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
          <span class="material-symbols-outlined text-sm">history</span>
          History ({{ completedOrders.length }})
        </button>
      </div>

      <!-- Active Tab Content -->
      <div v-if="activeTab === 'active'" class="space-y-4">
        <div v-if="activeOrders.length === 0" class="text-center py-16 bg-slate-950/20 border border-slate-800 border-dashed rounded-3xl">
          <span class="material-symbols-outlined text-slate-700 text-6xl mb-3 block">sports_motorsports</span>
          <h3 class="font-bold text-slate-400">All caught up!</h3>
          <p class="text-slate-600 text-xs mt-1">No pending deliveries assigned to you right now.</p>
        </div>

        <div v-for="order in activeOrders" :key="order.id" 
          class="bg-slate-950 border border-slate-800 rounded-3xl p-6 shadow-xl relative overflow-hidden group">
          <!-- Glass effect top bar -->
          <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
            <div>
              <span class="bg-amber-500/10 text-amber-400 text-xs px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">
                Order #{{ order.order_number }}
              </span>
            </div>
            <div>
              <span :class="statusBadgeClass(order.order_status)" class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full">
                {{ statusLabel(order.order_status) }}
              </span>
            </div>
          </div>

          <!-- Customer details -->
          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-slate-500 mt-0.5 text-lg">person</span>
              <div>
                <h4 class="font-bold text-sm text-slate-200">{{ order.customer_name }}</h4>
                <a :href="'tel:' + order.customer_phone" class="text-xs text-amber-500 font-bold hover:underline flex items-center gap-1 mt-1">
                  <span class="material-symbols-outlined text-xs">call</span>
                  {{ order.customer_phone }}
                </a>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-slate-500 mt-0.5 text-lg">pin_drop</span>
              <div class="flex-1">
                <p class="text-xs text-slate-400 leading-relaxed">{{ order.delivery_address }}</p>
                <a :href="'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(order.delivery_address)" 
                  target="_blank" 
                  class="inline-flex items-center gap-1 text-[11px] text-blue-400 font-bold hover:underline mt-2 bg-blue-500/5 px-2.5 py-1 rounded-lg border border-blue-500/10">
                  <span class="material-symbols-outlined text-[12px]">map</span>
                  Open in Google Maps
                </a>
              </div>
            </div>

            <div v-if="order.special_instructions" class="bg-slate-900/60 p-3 rounded-2xl border border-slate-800">
              <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Rider Instructions</p>
              <p class="text-xs italic text-slate-400">"{{ order.special_instructions }}"</p>
            </div>

            <!-- Action buttons -->
            <div class="pt-4 border-t border-slate-800/80 flex flex-col sm:flex-row items-center gap-3">
              <div class="text-left w-full sm:w-auto shrink-0 mb-3 sm:mb-0">
                <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Total Bill</span>
                <span class="text-xl font-black text-amber-500">${{ order.total_amount }}</span>
              </div>
              
              <button v-if="order.order_status < 3" @click="updateStatus(order.id, 3)"
                class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-black uppercase text-xs tracking-wider py-3.5 px-6 rounded-2xl transition duration-300 flex items-center justify-center gap-2 shadow-lg shadow-amber-500/10">
                <span class="material-symbols-outlined text-sm">local_shipping</span>
                Mark as Out for Delivery
              </button>
              
              <button v-else-if="order.order_status === 3" @click="updateStatus(order.id, 4)"
                class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black uppercase text-xs tracking-wider py-3.5 px-6 rounded-2xl transition duration-300 flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/10">
                <span class="material-symbols-outlined text-sm">home_pin</span>
                Mark as Delivered
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Completed Tab Content -->
      <div v-if="activeTab === 'completed'" class="space-y-4">
        <div v-if="completedOrders.length === 0" class="text-center py-16 bg-slate-950/20 border border-slate-800 border-dashed rounded-3xl">
          <span class="material-symbols-outlined text-slate-700 text-6xl mb-3 block">history</span>
          <h3 class="font-bold text-slate-400">No history yet</h3>
          <p class="text-slate-600 text-xs mt-1">Orders you deliver will appear here.</p>
        </div>

        <div v-for="order in completedOrders" :key="order.id" 
          class="bg-slate-950/50 border border-slate-900 rounded-3xl p-5 shadow-md flex items-center justify-between gap-4">
          <div class="min-w-0">
            <span class="text-xs font-bold text-slate-400">Order #{{ order.order_number }}</span>
            <h4 class="font-black text-sm text-slate-200 truncate mt-0.5">{{ order.customer_name }}</h4>
            <p class="text-xs text-slate-500 truncate mt-1">{{ order.delivery_address }}</p>
          </div>
          <div class="text-right shrink-0">
            <span class="text-sm font-black text-emerald-500 block">${{ order.total_amount }}</span>
            <span class="text-[9px] font-black uppercase tracking-wider text-emerald-500/80 bg-emerald-500/10 px-2 py-0.5 rounded mt-1 inline-block">
              Delivered
            </span>
          </div>
        </div>
      </div>

    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
  orders: Array
})

const activeTab = ref('active')

// Grouping orders
const activeOrders = computed(() => {
  return props.orders.filter(o => o.order_status < 4)
})

const completedOrders = computed(() => {
  return props.orders.filter(o => o.order_status === 4)
})

// Labels & badge styles helpers
const statusLabel = (status) => {
  const labels = {
    0: 'Received',
    1: 'Preparing',
    2: 'Ready',
    3: 'Out for Delivery'
  }
  return labels[status] || 'Unknown'
}

const statusBadgeClass = (status) => {
  if (status === 3) return 'bg-sky-500/10 text-sky-400 border border-sky-500/20'
  if (status === 2) return 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
  return 'bg-slate-800 text-slate-400'
}

// Update status handler
const updateStatus = (orderId, newStatus) => {
  const confirmText = newStatus === 3 
    ? 'Are you ready to head out for this delivery?' 
    : 'Have you arrived and delivered the order to the guest?'

  Swal.fire({
    title: 'Update Delivery Status?',
    text: confirmText,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: newStatus === 3 ? '#f59e0b' : '#10b981',
    cancelButtonColor: '#334155',
    confirmButtonText: 'Yes, update status',
    background: '#0f172a',
    color: '#f8fafc'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('driver.order.status', { id: orderId }), {
        status: newStatus
      }, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            title: 'Updated!',
            text: newStatus === 3 ? 'You are now out for delivery.' : 'Order marked as completed.',
            icon: 'success',
            background: '#0f172a',
            color: '#f8fafc',
            confirmButtonColor: '#f59e0b'
          })
        }
      })
    }
  })
}
</script>

<style scoped>
/* Mobile adjustments */
</style>
