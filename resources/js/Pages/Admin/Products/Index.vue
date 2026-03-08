<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div
        class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
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

        <!-- Add new item -->
        <Link :href="route('admin.product.items.create')"
          class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-green-700 flex items-center space-x-2 transition">
          <span class="material-symbols-outlined text-sm">add</span>
          <span>Add Item</span>
        </Link>
      </div>
    </div>

    <!-- Products Table -->
    <ListTable :headers="['Category', 'Name', 'Type', 'Image', 'Price', 'Status', 'Actions']" :items="products.data"
      :pagination="products">
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
            <div v-if="product.menu_img" class="w-12 h-12">
              <img :src="`/storage/${product.menu_img}`" :alt="product.name"
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
          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex justify-end space-x-2">
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

defineOptions({ layout: AdminLayout })

const props = defineProps({
  products: Object,
  filters: Object
})

const search = ref(props.filters.search)

function debounce(fn, delay) {
  let timeoutId;
  return (...args) => {
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
      fn(...args);
    }, delay);
  };
}

const performSearch = () => {
  router.get(route('admin.product.items.index'), { search: search.value, sort: props.filters.sort, direction: props.filters.direction }, {
    preserveState: true,
    replace: true
  })
}

watch(search, debounce((value) => {
  performSearch()
}, 500))

const deleteProduct = (id) => {
  if (confirm('Are you sure you want to delete this menu item?')) {
    router.delete(route('admin.product.items.destroy', id))
  }
}
</script>
