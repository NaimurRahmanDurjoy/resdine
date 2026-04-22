<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Menu Items</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage your restaurant menu items</p>
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
            <input v-model="search" type="text" placeholder="Search items..."
              class="block w-full pl-10 pr-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" />
          </div>
          <button @click="performSearch"
            class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 transition">
            Search
          </button>
        </div>
        <div class="flex space-x-2">
          <!-- Recipe List Btn -->
          <Link :href="route('admin.recipes.index')"
            class="px-3 py-1 bg-gray-600 text-white rounded text-sm hover:bg-gray-700 flex items-center space-x-2 transition">
            <span class="material-symbols-outlined text-sm">restaurant_menu</span>
            <span>Recipe List</span>
          </Link>
          <!-- Add new item -->
          <Link :href="route('admin.product.items.create')"
            class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-green-700 flex items-center space-x-2 transition">
            <span class="material-symbols-outlined text-sm">add</span>
            <span>Add Item</span>
          </Link>
        </div>
      </div>
    </div>

    <!-- Products Table -->
    <ListTable :headers="headers" :items="products.data" :pagination="products" :sort="filters.sort" :direction="filters.direction" :loading="loading" @sort="sortColumn">
      <template #rows="{ items }">
        <tr v-for="product in items" :key="product.id"
          class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
            {{ product.category?.name || 'N/A' }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
            {{ product.name }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
            <span v-if="product.type == 1">Regular</span>
            <span v-else-if="product.type == 2">Combo</span>
            <span v-else-if="product.type == 3">Comp.</span>
            <span v-else>Unknown</span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div v-if="product.image_url" class="w-12 h-12">
              <img :src="product.image_url" :alt="product.name"
                class="w-full h-full rounded-md object-cover border border-gray-200" />
            </div>
            <span v-else class="text-gray-400 italic text-sm">No image</span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-medium">
            ${{ parseFloat(product.price).toFixed(2) }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
              :class="product.status ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'">
              {{ product.status ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <Link :href="route('admin.recipes.create', { product_id: product.id })"
              class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-semibold
              bg-orange-200 text-orange-700 hover:bg-orange-300 dark:bg-orange-900/30 dark:text-orange-300 transition-all shadow-sm">
              <span class="material-symbols-outlined text-sm">restaurant_menu</span> 
              <span>Recipe</span>
            </Link>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex space-x-2">
              <Link :href="route('admin.product.items.edit', product.id)"
                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                <span class="material-symbols-outlined">edit</span>
              </Link>
              <button @click="deleteProduct(product.id)"
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
            Showing <span class="font-medium">{{ products.from }}</span> to <span class="font-medium">{{ products.to
              }}</span> of <span class="font-medium">{{ products.total }}</span> entries
          </div>
          <div class="flex space-x-1">
            <Link v-for="(link, k) in products.links" :key="k" :href="link.url || '#'" v-html="link.label"
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
  products: Object,
  filters: Object
})

const headers = [
 { label:'Category', key:'category', sortable:false },
 { label:'Name', key:'name', sortable:true },
 { label:'Type', key:'type', sortable:true },
 { label:'Image', key:'image', sortable:false },
 { label:'Price', key:'price', sortable:true },
 { label:'Status', key:'status', sortable:true },
 { label:'Recipe', key:'recipe', sortable:false },
 { label:'Actions', key:'actions', sortable:false }
]

const search = ref(props.filters.search)
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
  router.get(route('admin.product.items.index'), { search: search.value, sort: props.filters.sort, direction: props.filters.direction }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  })
}

watch(search, debounce(() => performSearch(), 500))

function sortColumn(column){
  let direction = 'asc'
  if(props.filters.sort === column){
    direction = props.filters.direction === 'asc' ? 'desc' : 'asc'
  }
  loading.value = true
  router.get(route('admin.product.items.index'), { search: search.value, sort: column, direction: direction }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  })
}

const deleteProduct = (id) => {
  Swal.fire({
    title: 'Are you sure?',
    text: "This item will be deleted!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626', // red
    cancelButtonColor: '#6b7280',  // gray
    confirmButtonText: 'Yes, delete it!',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.product.items.destroy', id), {
        onSuccess: () => {
          Swal.fire('Deleted!', 'The menu item has been deleted.', 'success')
        },
        onError: () => {
          Swal.fire('Error!', 'Something went wrong.', 'error')
        }
      })
    }
  })
}
</script>
