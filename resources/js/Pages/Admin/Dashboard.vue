<template>
  <div class="space-y-8">

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <Card
        title="Total Sales Today"
        :value="totalSales"
        icon="payments"
        color="indigo"
        :trend="{ up: true, value: 12 }"
      />

      <Card
        title="Orders Today"
        value="42"
        icon="receipt"
        color="green"
        :trend="{ up: true, value: 5 }"
      />

      <Card
        title="Avg. Order Value"
        :value="24.50"
        icon="attach_money"
        color="blue"
        :trend="{ up: false, value: 3 }"
      />

      <Card
        title="Top-Selling Item"
        :value="topItemName"
        icon="local_fire_department"
        color="purple"
      >
        <div class="text-sm text-gray-500 mt-1">{{ topItemSold }} sold today</div>
      </Card>
    </div>

    <!-- Charts & Additional Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- Sales Chart -->
      <LineChart
        title="Sales Trend (Last 7 Days)"
        :labels="labels"
        :datasets="chartDatasets"
      >
        <template #controls>
          <div class="flex space-x-2">
            <button class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-sm font-medium">Week</button>
            <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">Month</button>
            <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">Year</button>
          </div>
        </template>
      </LineChart>

      <!-- Low Stock Alerts -->
      <DataList title="Low Stock Alerts" :items="lowStockItems">
        <template #item="{ item }">
          <div class="flex justify-between items-center w-full">
            <div>
              <div class="font-medium">{{ item.ingredient_name }}</div>
              <div class="text-xs text-gray-500">Only {{ item.quantity }} left</div>
            </div>
            <span class="material-symbols-outlined text-amber-500 text-sm">arrow_forward</span>
          </div>
        </template>
        <template #footer>
          <router-link
            to="/admin/stock"
            class="w-full mt-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition block text-center"
          >
            View All Stock
          </router-link>
        </template>
      </DataList>

      <!-- Recent Orders -->
      <DataList title="Recent Orders" :items="recentOrders">
        <template #item="{ item }">
          <div class="flex justify-between items-center w-full">
            <div>
              <div class="font-medium">{{ item.order_no }}</div>
              <div class="text-xs text-gray-500">{{ item.table }} • {{ item.time }}</div>
            </div>
            <span
              :class="{
                'px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full': item.status === 'Completed',
                'px-2 py-1 bg-amber-100 text-amber-800 text-xs rounded-full': item.status === 'Preparing',
                'px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full': item.status === 'Served'
              }"
            >
              {{ item.status }}
            </span>
          </div>
        </template>
        <template #footer>
          <router-link
            to="/admin/orders"
            class="w-full mt-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition block text-center"
          >
            View All Orders
          </router-link>
        </template>
      </DataList>

    </div>

  </div>
</template>

<script setup>
import Card from '@/Components/Admin/Card.vue'
import DataList from '@/Components/Admin/DataList.vue'
import LineChart from '@/Components/Admin/LineChart.vue'

import { ref } from 'vue'

// Props or fetch data from API
const totalSales = ref(1250.50)
const topItemName = ref('Cheeseburger')
const topItemSold = ref(28)

const labels = ref(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'])
const chartDatasets = ref([
  {
    label: 'Sales ($)',
    data: [120, 250, 180, 300, 200, 450, 400],
    backgroundColor: 'rgba(99, 102, 241, 0.1)',
    borderColor: 'rgba(99, 102, 241, 1)',
    borderWidth: 3,
    tension: 0.3,
    fill: true,
    pointBackgroundColor: 'rgba(99, 102, 241, 1)',
    pointBorderColor: '#fff',
    pointBorderWidth: 2,
    pointRadius: 5,
    pointHoverRadius: 7
  }
])

const lowStockItems = ref([
  { id: 1, ingredient_name: 'Tomatoes', quantity: 5 },
  { id: 2, ingredient_name: 'Cheese', quantity: 2 },
  { id: 3, ingredient_name: 'Lettuce', quantity: 1 }
])

const recentOrders = ref([
  { id: 1, order_no: 'Order #1245', table: 'Table 4', time: '12:30 PM', status: 'Completed' },
  { id: 2, order_no: 'Order #1244', table: 'Takeaway', time: '12:15 PM', status: 'Preparing' },
  { id: 3, order_no: 'Order #1243', table: 'Table 2', time: '11:45 AM', status: 'Served' }
])
</script>
