<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div
        class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Promotions</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage discounts and seasonal offers</p>
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
            <input v-model="search" type="text" placeholder="Search promotions..."
              class="block w-full pl-10 pr-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" />
          </div>
        </div>

        <!-- Add new promotion -->
        <Link :href="route('admin.promotions.create')"
          class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 flex items-center space-x-2 transition">
          <span class="material-symbols-outlined text-sm">add</span>
          <span>Add Promotion</span>
        </Link>
      </div>
    </div>

    <!-- Table -->
    <ListTable :headers="headers" :items="promotions.data" :pagination="promotions" :sort="filters.sort"
      :direction="filters.direction" :loading="loading" @sort="sortColumn">
      <template #rows="{ items }">
        <tr v-for="promotion in items" :key="promotion.id"
          class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ promotion.name }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <span class="text-sm text-gray-600 dark:text-gray-400">
              {{ promotion.type === 'percentage' ? promotion.value + '%' : '$' + promotion.value }}
            </span>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-xs text-gray-500 dark:text-gray-400">
              {{ formatDate(promotion.start_date) }} to {{ promotion.end_date ? formatDate(promotion.end_date) :
                'Ongoing' }}
            </div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
              :class="promotion.status ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'">
              {{ promotion.status ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex space-x-2">
              <Link :href="route('admin.promotions.edit', promotion.id)"
                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                <span class="material-symbols-outlined">edit</span>
              </Link>
              <button @click="deletePromotion(promotion.id)"
                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                <span class="material-symbols-outlined">delete</span>
              </button>
            </div>
          </td>
        </tr>
      </template>

      <template #pagination>
        <div class="flex items-center justify-between w-full">
          <div class="text-sm text-gray-700 dark:text-gray-400">
            Showing <span class="font-medium">{{ promotions.from }}</span> to <span class="font-medium">{{ promotions.to
              }}</span> of <span class="font-medium">{{ promotions.total }}</span> entries
          </div>
          <div class="flex space-x-1">
            <Link v-for="(link, k) in promotions.links" :key="k" :href="link.url || '#'" v-html="link.label"
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
import Swal from 'sweetalert2'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  promotions: Object,
  filters: Object,
  pageTitle: String
})

const headers = [
  { label: 'Name', key: 'name', sortable: true },
  { label: 'Value', key: 'value', sortable: true },
  { label: 'Validity', key: 'start_date', sortable: true },
  { label: 'Status', key: 'status', sortable: false },
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
  router.get(route('admin.promotions.index'), { search: search.value, sort: props.filters.sort, direction: props.filters.direction }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  })
}

watch(search, debounce(() => performSearch(), 500))

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

function sortColumn(column) {
  let direction = 'asc'
  if (props.filters.sort === column) {
    direction = props.filters.direction === 'asc' ? 'desc' : 'asc'
  }
  loading.value = true
  router.get(route('admin.promotions.index'), { search: search.value, sort: column, direction: direction }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  })
}

const deletePromotion = (id) => {
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#4f46e5',
    cancelButtonColor: '#ef4444',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.promotions.destroy', id), {
        onSuccess: () => {
          Swal.fire('Deleted!', 'Promotion has been deleted.', 'success')
        }
      })
    }
  })
}
</script>
