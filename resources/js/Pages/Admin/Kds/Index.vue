<template>
  <div class="h-screen flex flex-col bg-gray-100 dark:bg-gray-900 overflow-hidden">
    <!-- Header -->
    <header class="bg-indigo-700 text-white shadow-md px-6 py-3 flex justify-between items-center shrink-0">
      <div class="flex items-center space-x-4">
        <span class="material-symbols-outlined text-3xl">restaurant</span>
        <h1 class="text-2xl font-bold font-heading tracking-tight">KDS <span class="text-indigo-300 font-light">Dashboard</span></h1>
        
        <!-- Department Filter -->
        <div class="ml-8 flex bg-indigo-800/50 p-1 rounded-xl border border-indigo-400/20">
          <button @click="changeDepartment(null)" 
                  :class="!currentDepartment ? 'bg-white text-indigo-700 shadow-lg' : 'text-indigo-200 hover:text-white'"
                  class="px-4 py-1.5 rounded-lg text-sm font-black transition-all">ALL</button>
          <button v-for="dept in departments" :key="dept.id"
                  @click="changeDepartment(dept.id)"
                  :class="currentDepartment == dept.id ? 'bg-white text-indigo-700 shadow-lg' : 'text-indigo-200 hover:text-white'"
                  class="px-4 py-1.5 rounded-lg text-sm font-black transition-all uppercase">{{ dept.name }}</button>
        </div>
      </div>
      <div class="flex items-center space-x-6">
        <div class="flex flex-col items-end">
          <span class="text-xs uppercase tracking-widest opacity-60">Pending</span>
          <span class="text-xl font-black leading-none">{{ activeOrders.length }}</span>
        </div>
        <div class="h-8 w-px bg-indigo-500/50"></div>
        <div class="flex flex-col items-end">
          <span class="text-sm font-mono">{{ currentTime }}</span>
          <span class="text-[10px] uppercase tracking-widest opacity-60">Live Sync Active</span>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 p-6 overflow-x-auto overflow-y-hidden flex space-x-6 custom-scrollbar bg-slate-50 dark:bg-slate-900/50">
      <div v-if="activeOrders.length === 0" class="flex-1 flex flex-col items-center justify-center text-gray-400 opacity-50">
        <span class="material-symbols-outlined text-8xl mb-4">check_circle</span>
        <p class="text-2xl font-medium">All orders cleared!</p>
      </div>

      <!-- Order Cards -->
      <div v-for="order in activeOrders" :key="order.id" 
           class="flex-shrink-0 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 flex flex-col overflow-hidden transition-all duration-300 transform"
           :class="[
             order.order_status == 1 ? 'border-orange-400 ring-2 ring-orange-100 dark:ring-orange-900/20' : 'border-gray-200 dark:border-gray-700'
           ]">
        <!-- Card Header -->
        <div class="p-4 border-b dark:border-gray-700" 
             :class="[
               order.order_status == 1 ? 'bg-orange-50 dark:bg-orange-950/20' : 'bg-gray-50 dark:bg-gray-800/10'
             ]">
          <div class="flex justify-between items-start mb-2">
            <span class="text-lg font-bold text-indigo-700 dark:text-indigo-400">#{{ order.order_number }}</span>
            <span class="text-xs font-mono px-2 py-0.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-full shadow-sm">
              {{ formatTime(order.created_at) }}
            </span>
          </div>
          <div class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
            <span class="material-symbols-outlined text-base mr-1">table_restaurant</span>
            <span>{{ order.table ? order.table.name : 'Takeaway' }}</span>
            <span v-if="order.customer" class="ml-2 px-2 py-0.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 text-xs rounded-lg truncate max-w-[120px]">
              {{ order.customer.name }}
            </span>
          </div>
        </div>

        <!-- Items List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
          <div v-for="item in order.items" :key="item.id" 
               class="p-3 rounded-lg border flex flex-col transition-all cursor-pointer"
               :class="statusClasses[item.preparation_status || 'pending']"
               @click="toggleItemStatus(item)">
            <div class="flex justify-between items-start">
              <span class="font-bold text-base leading-tight">{{ item.product ? item.product.name : 'Unknown Item' }}</span>
              <span class="text-lg font-black ml-2">x{{ item.quantity }}</span>
            </div>
            
            <!-- Modifiers/Notes -->
            <div v-if="item.modifiers && item.modifiers.length > 0" class="mt-2 text-xs opacity-80 flex flex-wrap gap-1">
              <span v-for="(mod, index) in item.modifiers" :key="index" class="bg-black/5 dark:bg-white/5 px-2 py-0.5 rounded italic">
                - {{ mod }}
              </span>
            </div>

            <div class="mt-2 flex items-center text-[10px] uppercase font-bold tracking-wider opacity-60">
              <span class="material-symbols-outlined text-xs mr-1">
                {{ item.preparation_status === 'ready' ? 'check_circle' : (item.preparation_status === 'preparing' ? 'pending' : 'schedule') }}
              </span>
              {{ item.preparation_status || 'pending' }}
            </div>
          </div>
        </div>

        <!-- Card Footer -->
        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 border-t dark:border-gray-700">
          <button @click="markOrderReady(order.id)" 
                  class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2 active:scale-95 disabled:opacity-50">
            <span class="material-symbols-outlined">done_all</span>
            <span>MARK AS READY</span>
          </button>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  orders: Array,
  departments: Array,
  currentDepartment: [String, Number]
})

const activeOrders = ref([...props.orders])

watch(() => props.orders, (newVal) => {
    activeOrders.value = [...newVal]
})

const currentTime = ref('')
const timer = ref(null)

const statusClasses = {
  'pending': 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200 shadow-sm',
  'preparing': 'bg-orange-50 dark:bg-orange-900/10 border-orange-200 dark:border-orange-800 text-orange-800 dark:text-orange-300 shadow-md ring-1 ring-orange-200/50',
  'ready': 'bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 opacity-60 line-through grayscale-[0.5]'
}

const formatTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const changeDepartment = (id) => {
  router.get(route('admin.kds.index'), { department_id: id }, {
    preserveState: true,
    preserveScroll: true
  })
}

const toggleItemStatus = (item) => {
  let nextStatus = 'preparing'
  if (item.preparation_status === 'preparing') nextStatus = 'ready'
  else if (item.preparation_status === 'ready') nextStatus = 'pending'

  router.post(route('admin.kds.item.status', item.id), { status: nextStatus }, {
    preserveScroll: true,
    preserveState: true
  })
}

const markOrderReady = (orderId) => {
  router.post(route('admin.kds.order.ready', orderId), {}, {
    onFinish: () => {
        // Optional success notification
    }
  })
}

const updateTime = () => {
  currentTime.value = new Date().toLocaleTimeString()
}

onMounted(() => {
  updateTime()
  timer.value = setInterval(updateTime, 1000)
  
  if (window.Echo) {
    window.Echo.channel('kds.orders')
      .listen('.order.created', (e) => {
        const belongsToDept = !props.currentDepartment || e.items.some(item => item.product?.department_id == props.currentDepartment);
        if (belongsToDept) {
            activeOrders.value.push(e);
        }
      })
      .listen('.order.status.updated', (e) => {
        const order = activeOrders.value.find(o => o.id === e.order_id);
        if (order) {
            order.order_status = e.order_status;
            if (e.item_id) {
                const item = order.items.find(i => i.id === e.item_id);
                if (item) {
                    item.preparation_status = e.item_status;
                }
            } else {
                order.items.forEach(i => i.preparation_status = e.item_status);
            }
            if (order.order_status === 2) {
                activeOrders.value = activeOrders.value.filter(o => o.id !== e.order_id);
            }
        }
      });
  }
})

onUnmounted(() => {
  clearInterval(timer.value)
  if (window.Echo) {
      window.Echo.leaveChannel('kds.orders')
  }
})
</script>

<style scoped>
.font-heading { font-family: 'Outfit', sans-serif; }
.custom-scrollbar::-webkit-scrollbar {
  height: 8px;
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(0,0,0,0.1);
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.05);
}
</style>
