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
      <DataList title="Low Stock Alerts" :items="lowStock">
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
          <Link
            to="/admin/stock"
            class="w-full mt-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition block text-center"
          >
            View All Stock
          </Link>
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
          <Link
            to="/admin/orders"
            class="w-full mt-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition block text-center"
          >
            View All Orders
          </Link>
        </template>
      </DataList>

    </div>

  </div>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue'

export default {
  layout: AdminLayout,
}
</script>

<script setup>
import Card from '@/Components/Admin/Card.vue'
import DataList from '@/Components/Admin/DataList.vue'
import LineChart from '@/Components/Admin/LineChart.vue'
import { computed } from 'vue'

const props = defineProps({
  totalSales: [Number, String],
  topItemName: String,
  lowStock: Array,
  labels: Array,
  salesData: Array
});

// Assuming no specific property 'topItemSold' returned, leaving out or you could add to controller if needed.
const topItemSold = 0; // Or passed from backend

const chartDatasets = computed(() => [
  {
    label: 'Sales ($)',
    data: props.salesData,
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
]);

const recentOrders = []; // Add feature to Backend controller later if needed.
</script>
