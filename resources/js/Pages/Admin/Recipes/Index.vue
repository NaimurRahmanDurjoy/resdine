<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div
        class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Recipes</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage ingredient recipes for your menu items</p>
          </div>
          <Link :href="route('admin.product.items.index')"
            class="text-gray-500 hover:text-gray-700 flex items-center space-x-1">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>Back to Menu</span>
          </Link>
        </div>
      </div>

      <!-- Filters & Actions -->
      <div class="m-4 flex justify-between items-center">
        <div class="flex space-x-2">
          <div class="relative max-w-sm w-full">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="material-symbols-outlined text-gray-400">search</span>
            </span>
            <input v-model="search" type="text" placeholder="Search recipes..."
              class="block w-full pl-10 pr-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" />
          </div>
          <button @click="performSearch"
            class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 transition">
            Search
          </button>
        </div>
        
        <Link :href="route('admin.recipes.create')"
            class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-green-700 flex items-center space-x-2 transition">
          <span class="material-symbols-outlined text-sm">add</span>
          <span>Add Recipe</span>
        </Link>
      </div>
    </div>

    <!-- Recipes Table -->
    <ListTable :headers="headers" :items="recipes.data" :pagination="recipes" :sort="filters.sort"
      :direction="filters.direction" :loading="loading" @sort="sortColumn">
      <template #rows="{ items }">
        <tr v-for="recipe in items" :key="recipe.id"
          class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ recipe.menu_item?.name }}</div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap">
            <div class="text-sm text-gray-500 dark:text-gray-400">
              <span v-if="recipe.variant"
                class="px-2 py-1 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-md">
                {{ recipe.variant.name }}
              </span>
              <span v-else class="text-gray-400 italic font-normal">All Variants</span>
            </div>
          </td>
          <td class="px-6 py-2">
            <div class="flex flex-wrap gap-1">
              <span v-for="item in recipe.recipe_items" :key="item.id"
                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                {{ item.ingredient?.name || item.sub_product?.name }}: {{ item.quantity }} {{ item.unit?.short_name || item.unit?.name }}
              </span>
            </div>
          </td>
          <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-bold">
            ${{ recipe.food_cost }}
          </td>
          <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
            ${{ recipe.selling_price }}
          </td>
          <td class="px-6 py-2 whitespace-nowrap text-sm font-black">
            <span :class="[
              recipe.gp_percentage >= 75 ? 'text-green-600' :
                recipe.gp_percentage >= 65 ? 'text-amber-500' : 'text-red-500'
            ]">
              {{ recipe.gp_percentage }}%
            </span>
          </td>
          <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex space-x-3">
              <Link :href="route('admin.recipes.edit', recipe.id)"
                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
              <span class="material-symbols-outlined">edit</span>
              </Link>
              <button @click="deleteRecipe(recipe.id)"
                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                <span class="material-symbols-outlined">delete</span>
              </button>
            </div>
          </td>
        </tr>
      </template>

      <template #pagination>
        <div class="flex items-center justify-between w-full">
          <div class="text-sm text-gray-700 dark:text-gray-400">
            Showing <span class="font-medium">{{ recipes.from }}</span> to <span class="font-medium">{{ recipes.to
              }}</span> of <span class="font-medium">{{ recipes.total }}</span> entries
          </div>
          <div class="flex space-x-1">
            <Link v-for="(link, k) in recipes.links" :key="k" :href="link.url || '#'" v-html="link.label"
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
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  recipes: Object,
  filters: Object
})

const headers = [
  { label: 'Menu Item', key: 'menu_item', sortable: true },
  { label: 'Variant', key: 'variant', sortable: false },
  { label: 'Ingredients', key: 'ingredients', sortable: false },
  { label: 'Food Cost', key: 'food_cost', sortable: true },
  { label: 'Sell Price', key: 'selling_price', sortable: true },
  { label: 'GP %', key: 'gp_percentage', sortable: true },
  { label: 'Actions', key: 'action', sortable: false }
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
  router.get(route('admin.recipes.index'), { search: search.value, sort: props.filters.sort, direction: props.filters.direction }, {
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
  router.get(route('admin.recipes.index'), { search: search.value, sort: column, direction: direction }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  })
}

const deleteRecipe = (id) => {
  Swal.fire({
    title: 'Are you sure?',
    text: "This recipe will be deleted permanently!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, delete it!',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.recipes.destroy', id), {
        onSuccess: () => {
          Swal.fire('Deleted!', 'The recipe has been deleted.', 'success')
        },
        onError: () => {
          Swal.fire('Error!', 'Something went wrong.', 'error')
        }
      })
    }
  })
}
</script>
