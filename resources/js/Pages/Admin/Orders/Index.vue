<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div
        class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }}</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage and track all restaurant orders</p>
          </div>
        </div>
      </div>

      <!-- Filters & Actions -->
      <div class="m-4 flex justify-between items-center">
        <div class="flex space-x-2">
          <div class="relative max-w-sm w-full">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="material-symbols-outlined text-gray-400">search</span>
            </span>
            <input v-model="search" type="text" placeholder="Search order # or customer..."
              class="block w-full pl-10 pr-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition" />
          </div>
        </div>

        <Link :href="route('admin.orders.create')"
          class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 flex items-center space-x-2 transition shadow-sm">
          <span class="material-symbols-outlined text-sm">add</span>
          <span>Create New Order</span>
        </Link>
      </div>
    </div>

    <!-- Table -->
    <ListTable :headers="headers" :items="orders.data" :pagination="orders" :sort="filters.sort"
      :direction="filters.direction" :loading="loading" @sort="sortColumn">
      <template #rows="{ items }">
        <tr v-for="order in items" :key="order.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
          <td class="px-6 py-3 whitespace-nowrap">
            <Link :href="route('admin.orders.show', order.id)"
              class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
              {{ order.order_number }}
            </Link>
          </td>
          <td class="px-6 py-3 whitespace-nowrap">
            <div class="text-sm text-gray-900 dark:text-gray-100 font-medium">
              {{ order.customer ? order.customer.name : 'Walk-in' }}
            </div>
          </td>
          <td class="px-6 py-3 whitespace-nowrap">
            <div class="text-sm text-gray-600 dark:text-gray-400">
              {{ order.table ? order.table.name : 'N/A' }}
            </div>
          </td>
          <td class="px-6 py-3 whitespace-nowrap">
            <span
              class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
              {{ orderTypeText(order.order_type) }}
            </span>
          </td>
          <td class="px-6 py-3 whitespace-nowrap">
            <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
              ${{ order.total_amount }}
            </div>
          </td>
          <td class="px-6 py-3 whitespace-nowrap">
            <span :class="statusBadge(order.order_status)">
              {{ statusText(order.order_status) }}
            </span>
          </td>
          <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex space-x-2">
              <Link :href="route('admin.orders.show', order.id)"
                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1 rounded-full hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                <span class="material-symbols-outlined">visibility</span>
              </Link>
            </div>
          </td>
        </tr>
      </template>

      <template #pagination>
        <div class="flex items-center justify-between w-full">
          <div class="text-sm text-gray-700 dark:text-gray-400">
            Showing <span class="font-medium">{{ orders.from }}</span> to <span class="font-medium">{{ orders.to
            }}</span> of <span class="font-medium">{{ orders.total }}</span> entries
          </div>
          <div class="flex space-x-1">
            <Link v-for="(link, k) in orders.links" :key="k" :href="link.url || '#'" v-html="link.label"
              class="relative inline-flex items-center px-3 py-1 border text-xs font-medium transition-colors rounded shadow-sm"
              :class="[
                link.active
                  ? 'z-10 bg-indigo-600 border-indigo-600 text-white'
                  : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700',
                !link.url ? 'cursor-not-allowed opacity-50' : ''
              ]" />
          </div>
        </div>
      </template>
    </ListTable>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  orders: Object,
  filters: Object,
  pageTitle: String
})

const headers = [
  { label: 'Order #', key: 'order_number', sortable: true },
  { label: 'Customer', key: 'customer_id', sortable: true },
  { label: 'Table', key: 'table_id', sortable: true },
  { label: 'Type', key: 'order_type', sortable: true },
  { label: 'Total', key: 'total_amount', sortable: true },
  { label: 'Status', key: 'order_status', sortable: true },
  { label: 'Actions', key: 'actions', sortable: false }
]

const search = ref(props.filters.search || '')
const loading = ref(false)

function debounce(fn, delay) {
  let timeoutId;
  return (...args) => {
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn(...args), delay);
  };
}

const performSearch = () => {
  loading.value = true
  router.get(route('admin.orders.index'), { search: search.value, sort: props.filters.sort, direction: props.filters.direction }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  })
}

watch(search, debounce(() => performSearch(), 500))

function sortColumn(column) {
  let direction = 'asc'
  if (props.filters.sort === column) {
    direction = props.filters.direction === 'asc' ? 'desc' : 'asc'
  }
  loading.value = true
  router.get(route('admin.orders.index'), { search: search.value, sort: column, direction: direction }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  })
}

const orderTypeText = (type) => {
  const types = { 1: 'Dine-in', 2: 'Takeaway', 3: 'Delivery', 4: 'Party' };
  return types[type] || 'Other';
}

const statusText = (status) => {
  const statuses = {
    0: 'Pending',
    1: 'Preparing',
    2: 'Ready',
    3: 'Out for Delivery',
    4: 'Completed',
    5: 'Cancelled'
  };
  return statuses[status] || 'Unknown';
}

const statusBadge = (status) => {
  const base = 'px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm ';
  if (status === 0) return base + 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300';
  if (status === 1) return base + 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300';
  if (status === 2) return base + 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
  if (status === 3) return base + 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300';
  if (status === 4) return base + 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
  if (status === 5) return base + 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300';
  return base + 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
}
</script>
