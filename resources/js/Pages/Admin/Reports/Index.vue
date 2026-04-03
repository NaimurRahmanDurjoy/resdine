<template>
  <AdminLayout :title="pageTitle">
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto space-y-8">
      
      <!-- Page header -->
      <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
          <h1 class="text-2xl md:text-3xl text-slate-800 dark:text-slate-100 font-bold mb-2">Reports & Analytics ✨</h1>
          <p class="text-slate-500 text-sm">Overview of your restaurant's performance.</p>
        </div>
      </div>

      <!-- Stats Cards Row -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Sales Today -->
        <div class="flex flex-col bg-white dark:bg-slate-800 shadow-sm rounded-xl border border-slate-200 dark:border-slate-700 p-5">
          <div class="text-slate-500 text-sm font-semibold mb-2">Sales Today</div>
          <div class="flex items-center">
            <div class="text-3xl font-bold text-slate-800 dark:text-slate-100 mr-2">${{ stats.salesToday.toFixed(2) }}</div>
            <div class="text-sm font-medium text-emerald-500 bg-emerald-100 dark:bg-emerald-500/20 px-2 py-0.5 rounded-full">+12%</div>
          </div>
        </div>

        <!-- Sales Month -->
        <div class="flex flex-col bg-white dark:bg-slate-800 shadow-sm rounded-xl border border-slate-200 dark:border-slate-700 p-5">
          <div class="text-slate-500 text-sm font-semibold mb-2">Monthly Revenue</div>
          <div class="flex items-center">
            <div class="text-3xl font-bold text-slate-800 dark:text-slate-100 mr-2">${{ stats.salesMonth.toFixed(2) }}</div>
          </div>
        </div>

        <!-- Orders Today -->
        <div class="flex flex-col bg-white dark:bg-slate-800 shadow-sm rounded-xl border border-slate-200 dark:border-slate-700 p-5">
          <div class="text-slate-500 text-sm font-semibold mb-2">Orders Today</div>
          <div class="flex items-center">
            <div class="text-3xl font-bold text-slate-800 dark:text-slate-100 mr-2">{{ stats.ordersToday }}</div>
          </div>
        </div>

        <!-- Pending Orders -->
        <div class="flex flex-col bg-white dark:bg-slate-800 shadow-sm rounded-xl border border-slate-200 dark:border-slate-700 p-5">
          <div class="text-slate-500 text-sm font-semibold mb-2">Pending Orders</div>
          <div class="flex items-center">
            <div class="text-3xl font-bold text-amber-500 mr-2">{{ stats.pendingOrders }}</div>
            <div v-if="stats.pendingOrders > 0" class="text-xs font-semibold text-white bg-amber-500 px-2 py-1 rounded-full animate-pulse">Action Req</div>
          </div>
        </div>

      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- 7 Day Trend Chart (CSS based for performance and native feel) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 shadow-sm rounded-xl border border-slate-200 dark:border-slate-700 p-6 flex flex-col">
          <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-6">7-Day Sales Trend</h2>
          <div class="flex-1 flex items-end justify-between space-x-2 pt-10 relative">
            
            <div v-for="(day, index) in reversedTrendData" :key="index" class="w-full flex flex-col items-center group relative">
              
              <!-- Tooltip on hover -->
              <div class="opacity-0 group-hover:opacity-100 absolute -top-10 transition-opacity bg-slate-800 text-white text-xs py-1 px-2 rounded font-semibold whitespace-nowrap z-10 pointer-events-none shadow-lg">
                ${{ day.total.toFixed(2) }}
              </div>
              
              <!-- Bar -->
              <div class="w-full bg-indigo-100 dark:bg-indigo-900/30 rounded-t-sm relative flex justify-center group-hover:bg-indigo-200 dark:group-hover:bg-indigo-800/50 transition-colors"
                   :style="{ height: '180px' }">
                 <div class="absolute bottom-0 w-8/12 bg-indigo-500 rounded-t-sm transition-all duration-700 ease-out group-hover:bg-indigo-400 shadow-sm"
                      :style="{ height: (day.total / maxTrendSales * 100) + '%' }"></div>
              </div>
              
              <!-- Label -->
              <span class="text-xs font-medium text-slate-500 mt-3">{{ day.day }}</span>
            </div>
            
          </div>
        </div>

        <!-- Top Selling Items -->
        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl border border-slate-200 dark:border-slate-700 p-6 flex flex-col">
           <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-6">Top Menu Items</h2>
           <div class="flex-1 space-y-4">
              <div v-for="item in topItems" :key="item.item_id" class="flex items-center p-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg transition-colors border border-transparent hover:border-slate-100 dark:hover:border-slate-700">
                <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-lg mr-4 overflow-hidden shadow-inner flex items-center justify-center shrink-0">
                  <img v-if="item.product && item.product.image_url" :src="item.product.image_url" class="w-full h-full object-cover">
                  <span v-else class="material-symbols-outlined text-slate-400">restaurant_menu</span>
                </div>
                <div class="flex-1 min-w-0">
                  <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">{{ item.product ? item.product.name : 'Unknown' }}</h4>
                  <div class="text-xs text-slate-500">{{ item.total_qty }} sold</div>
                </div>
                <div class="text-sm font-bold text-emerald-500">
                  ${{ Number(item.revenue).toFixed(2) }}
                </div>
              </div>

              <div v-if="topItems.length === 0" class="h-full flex items-center justify-center text-slate-400">
                 No sales data yet.
              </div>
           </div>
        </div>
      </div>

      <!-- Recent Transactions Table -->
      <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl border border-slate-200 dark:border-slate-700">
        <header class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
          <h2 class="font-semibold text-slate-800 dark:text-slate-100">Recent Transactions</h2>
        </header>
        <div class="p-3">
          <div class="overflow-x-auto">
            <table class="table-auto w-full">
              <thead class="text-xs font-semibold uppercase text-slate-400 bg-slate-50 dark:bg-slate-700/20">
                <tr>
                  <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Order #</div></th>
                  <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Customer</div></th>
                  <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Amount</div></th>
                  <th class="p-2 whitespace-nowrap"><div class="font-semibold text-center">Status</div></th>
                  <th class="p-2 whitespace-nowrap"><div class="font-semibold text-right">Date</div></th>
                </tr>
              </thead>
              <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-700">
                <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                  <td class="p-2 whitespace-nowrap">
                    <div class="font-medium text-indigo-500">#{{ order.order_number }}</div>
                  </td>
                  <td class="p-2 whitespace-nowrap">
                    <div class="font-medium text-slate-800 dark:text-slate-100">{{ order.customer ? order.customer.name : 'Walk-in / Guest' }}</div>
                  </td>
                  <td class="p-2 whitespace-nowrap">
                    <div class="text-left font-medium text-emerald-500">${{ Number(order.total_amount).toFixed(2) }}</div>
                  </td>
                  <td class="p-2 whitespace-nowrap">
                    <div class="text-center">
                      <span class="text-xs px-2.5 py-1 font-semibold rounded-full"
                            :class="[
                              order.order_status == 0 ? 'bg-amber-100 text-amber-600' :
                              order.order_status == 1 ? 'bg-sky-100 text-sky-600' :
                              order.order_status == 2 ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-500'
                            ]">
                        {{ statusNames[order.order_status] }}
                      </span>
                    </div>
                  </td>
                  <td class="p-2 whitespace-nowrap">
                    <div class="text-right text-slate-500">{{ formatDate(order.created_at) }}</div>
                  </td>
                </tr>
              </tbody>
            </table>
            
            <div v-if="recentOrders.length === 0" class="py-8 text-center text-slate-500">
               No recent transactions found.
            </div>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  stats: Object,
  recentOrders: Array,
  topItems: Array,
  trendData: Array,
  maxTrendSales: Number,
  pageTitle: String
})

// Reverse to show oldest (6 days ago) to newest (today) left-to-right
const reversedTrendData = computed(() => {
  return [...props.trendData].reverse()
})

const statusNames = {
  0: 'Pending',
  1: 'Preparing',
  2: 'Ready',
  3: 'Out for Delivery',
  4: 'Completed',
  5: 'Cancelled'
}

const formatDate = (dateStr) => {
  const date = new Date(dateStr)
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}
</script>
