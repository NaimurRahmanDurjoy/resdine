<template>
  <div class="h-screen flex flex-col bg-gray-900 text-white font-sans overflow-hidden">
    <!-- Header -->
    <header class="bg-indigo-900 px-8 py-4 flex justify-between items-center shadow-2xl z-10 border-b border-white/10">
      <div class="flex items-center gap-4">
        <div class="bg-white/10 p-2 rounded-xl backdrop-blur-md">
          <span class="material-symbols-outlined text-indigo-300 text-3xl">restaurant</span>
        </div>
        <div>
          <h1 class="text-2xl font-black tracking-tight uppercase">Waiter Expo</h1>
          <p class="text-indigo-300/60 text-xs font-bold tracking-widest uppercase">Ready for service</p>
        </div>
      </div>
      <div class="flex items-center gap-6">
        <div class="text-right">
          <div class="text-2xl font-mono font-black tabular-nums">{{ currentTime }}</div>
          <div class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Real-time Sync</div>
        </div>
        <Link :href="route('admin.dashboard')" class="bg-white/5 hover:bg-white/10 p-3 rounded-2xl transition-all">
          <span class="material-symbols-outlined text-white/70">dashboard</span>
        </Link>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-8 custom-scrollbar">
      <div v-if="readyItems.length === 0" class="h-full flex flex-col items-center justify-center opacity-20">
        <span class="material-symbols-outlined text-9xl mb-4">check_circle</span>
        <h2 class="text-3xl font-black uppercase tracking-tighter">All Clear</h2>
        <p class="font-bold text-indigo-300">No items are currently waiting for service.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <transition-group name="list">
          <div v-for="item in readyItems" :key="item.id"
            class="bg-gray-800 rounded-3xl border border-white/5 shadow-2xl overflow-hidden group flex flex-col">
            <!-- Order Info -->
            <div class="p-6 bg-indigo-500/10 border-b border-white/5 flex justify-between items-start">
              <div>
                <div class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Table / Guest</div>
                <div class="text-xl font-black text-white truncate">
                  {{ item.order.table ? item.order.table.name : 'Takeaway' }}
                </div>
              </div>
              <div class="bg-indigo-500 text-white text-[10px] font-black px-2 py-1 rounded-lg shadow-lg">
                #{{ item.order.order_number }}
              </div>
            </div>

            <!-- Item Details -->
            <div class="p-6 flex-1 flex flex-col justify-center text-center">
              <div class="text-2xl font-black text-white leading-tight mb-2">
                {{ item.product.name }}
              </div>
              <div v-if="item.variant_name" class="text-sm font-bold text-indigo-400 uppercase tracking-widest">
                {{ item.variant_name }}
              </div>
              <div class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 bg-white/5 rounded-2xl border border-white/10">
                <span class="text-2xl font-black text-white">{{ item.quantity }}</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Portions</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="p-4 bg-gray-900/50">
              <button @click="markAsServed(item)"
                class="w-full py-4 bg-green-500 hover:bg-green-400 text-white rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-lg shadow-green-500/20 flex items-center justify-center gap-2 active:scale-95">
                <span class="material-symbols-outlined text-lg">check_circle</span>
                Mark as Served
              </button>
            </div>
          </div>
        </transition-group>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';

const readyItems = ref([]);
const currentTime = ref('');

const updateTime = () => {
  currentTime.value = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
};

const fetchReadyItems = async () => {
  try {
    const response = await axios.get(route('admin.kds.ready-items'));
    readyItems.value = response.data;
  } catch (error) {
    console.error('Failed to fetch ready items:', error);
  }
};

const markAsServed = (item) => {
  router.post(route('admin.kds.item.status', item.id), {
    status: 'served'
  }, {
    preserveScroll: true,
    onSuccess: () => {
      fetchReadyItems();
    }
  });
};

let pollInterval;

onMounted(() => {
  updateTime();
  setInterval(updateTime, 1000);
  fetchReadyItems();
  pollInterval = setInterval(fetchReadyItems, 5000); // Poll every 5 seconds
});

onUnmounted(() => {
  clearInterval(pollInterval);
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.2);
}

.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateY(30px);
}
</style>
