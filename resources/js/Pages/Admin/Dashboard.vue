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
          <div class="flex justify-between items-center w-full group cursor-pointer" @click="router.get(route('admin.stock.index'), { filter: 'low_stock' })">
            <div>
              <div class="font-medium group-hover:text-indigo-600 transition-colors">{{ item.ingredient_name }}</div>
              <div class="text-xs text-gray-500">Only {{ item.quantity }} {{ item.unit }} left</div>
            </div>
            <span class="material-symbols-outlined text-amber-500 text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
          </div>
        </template>
        <template #footer>
          <Link
            :href="route('admin.stock.index', { filter: 'low_stock' })"
            class="w-full mt-4 py-2 bg-amber-50 text-amber-700 border border-amber-100 rounded-lg text-sm font-medium hover:bg-amber-100 transition block text-center"
          >
            Manage Low Stock
          </Link>
        </template>
      </DataList>

      <!-- Expiring Items -->
      <DataList title="Upcoming Expiries" :items="expiringItems">
        <template #empty>
          <div class="py-4 text-center text-gray-400 text-sm italic">
            No items expiring soon
          </div>
        </template>
        <template #item="{ item }">
          <div class="flex justify-between items-center w-full group cursor-pointer" @click="router.get(route('admin.stock.index'), { filter: 'expiring' })">
            <div>
              <div class="font-medium group-hover:text-red-600 transition-colors">{{ item.ingredient_name }}</div>
              <div class="text-xs text-gray-500">Expiring on {{ new Date(item.expiry_date).toLocaleDateString() }}</div>
            </div>
            <span class="material-symbols-outlined text-red-500 text-sm group-hover:translate-x-1 transition-transform">event_busy</span>
          </div>
        </template>
        <template #footer>
          <Link
            :href="route('admin.stock.index', { filter: 'expiring' })"
            class="w-full mt-4 py-2 bg-red-50 text-red-700 border border-red-100 rounded-lg text-sm font-medium hover:bg-red-100 transition block text-center"
          >
            View All Expiries
          </Link>
        </template>
      </DataList>

      <!-- Recent Orders -->
      <DataList title="Recent Orders" :items="recentOrders">
        <template #item="{ item }">
          <Link :href="route('admin.orders.show', item.id)" class="flex justify-between items-center w-full group">
            <div>
              <div class="font-medium group-hover:text-green-600 transition-colors">{{ item.order_number }}</div>
              <div class="text-xs text-gray-500">
                {{ item.customer?.name || 'Walk-in Customer' }} • {{ item.table?.name || 'Takeaway' }}
              </div>
            </div>
            <div class="text-right">
              <div class="font-bold text-gray-900">${{ parseFloat(item.total_amount).toFixed(2) }}</div>
              <div class="text-[10px] uppercase font-bold text-green-600">{{ item.order_status }}</div>
            </div>
          </Link>
        </template>
        <template #footer>
          <Link
            :href="route('admin.orders.index')"
            class="w-full mt-4 py-2 bg-gray-50 text-gray-700 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-100 transition block text-center"
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
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  totalSales: [Number, String],
  topItemName: String,
  lowStock: Array,
  expiringItems: Array,
  labels: Array,
  salesData: Array,
  recentOrders: Array
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

</script>
