<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Menu Category</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage your restaurant menu categories</p>
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
            <input
              v-model="search"
              type="text"
              placeholder="Search categories..."
              class="block w-full pl-10 pr-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>
          <button 
            @click="performSearch"
            class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 transition"
          >
            Search
          </button>
        </div>

        <!-- Add new category -->
        <Link :href="route('admin.product.categories.create')"
          class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-green-700 flex items-center space-x-2 transition"
        >
          <span class="material-symbols-outlined text-sm">add</span>
          <span>Add Category</span>
        </Link>
      </div>
    </div>

    <!-- Categories Table -->
    <ListTable
      :headers="['Name', 'Image', 'Status', 'Actions']"
      :items="categories.data"
      :pagination="categories"
    >
      <template #rows="{ items }">
        <tr v-for="category in items" :key="category.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ category.name }}</div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div v-if="category.image" class="w-12 h-12">
              <img :src="category.image" :alt="category.name" class="w-full h-full rounded-md object-cover border border-gray-200" />
            </div>
            <span v-else class="text-gray-400 italic text-sm">No image</span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <span
              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
              :class="category.status ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'"
            >
              {{ category.status ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex justify-end space-x-2">
              <Link :href="route('admin.product.categories.edit', category.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                <span class="material-symbols-outlined">edit</span>
              </Link>
              <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                <span class="material-symbols-outlined">delete</span>
              </button>
            </div>
          </td>
        </tr>
      </template>

      <template #pagination>
        <div class="flex items-center justify-between w-full">
          <div class="text-sm text-gray-700 dark:text-gray-400">
            Showing <span class="font-medium">{{ categories.from }}</span> to <span class="font-medium">{{ categories.to }}</span> of <span class="font-medium">{{ categories.total }}</span> entries
          </div>
          <div class="flex space-x-1">
            <Link
              v-for="(link, k) in categories.links"
              :key="k"
              :href="link.url || '#'"
              v-html="link.label"
              class="relative inline-flex items-center px-3 py-1 border text-xs font-medium transition-colors rounded shadow-sm"
              :class="[
                link.active 
                  ? 'z-10 bg-indigo-600 border-indigo-600 text-white' 
                  : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700',
                !link.url ? 'cursor-not-allowed opacity-50' : ''
              ]"
            />
          </div>
        </div>
      </template>
    </ListTable>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  categories: Object,
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
  router.get(route('admin.product.categories.index'), { search: search.value, sort: props.filters.sort, direction: props.filters.direction }, {
    preserveState: true,
    replace: true
  })
}

watch(search, debounce((value) => {
  performSearch()
}, 500))

const deleteCategory = (id) => {
  if (confirm('Are you sure you want to delete this category?')) {
    router.delete(route('admin.product.categories.destroy', id))
  }
}
</script>
