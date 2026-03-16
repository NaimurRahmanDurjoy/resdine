<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800">
      <div class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Recipes</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage ingredient recipes for your menu items</p>
          </div>
          <Link :href="route('admin.recipes.create')"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center space-x-2 transition shadow-sm">
            <span class="material-symbols-outlined text-lg">add</span>
            <span>Add Recipe</span>
          </Link>
        </div>
      </div>

      <!-- Filters & Actions -->
      <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
        <div class="flex items-center space-x-4">
          <div class="relative max-w-sm">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="material-symbols-outlined text-gray-400 text-sm">search</span>
            </span>
            <input v-model="search" type="text" placeholder="Search menu items or variants..."
              class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" />
          </div>
        </div>
      </div>
    </div>

    <!-- Recipes Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 dark:bg-gray-800/50">
          <tr>
            <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Menu Item</th>
            <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Variant</th>
            <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ingredients</th>
            <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
          <tr v-for="recipe in recipes.data" :key="recipe.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
              {{ recipe.menu_item?.name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400 font-medium">
              <span v-if="recipe.variant" class="px-2 py-1 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-md">
                {{ recipe.variant.name }}
              </span>
              <span v-else class="text-gray-400 italic font-normal">All Variants</span>
            </td>
            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-1">
                <span v-for="item in recipe.recipe_items" :key="item.id" 
                  class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                  {{ item.ingredient?.name }}: {{ item.quantity }} {{ item.unit?.short_name || item.unit?.name }}
                </span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex justify-end space-x-3">
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
          <tr v-if="recipes.data.length === 0">
            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
              <div class="flex flex-col items-center">
                <span class="material-symbols-outlined text-4xl mb-2">menu_book</span>
                <p>No recipes found</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
      <div class="text-sm text-gray-700 dark:text-gray-400">
        Showing <span class="font-medium">{{ recipes.from }}</span> to <span class="font-medium">{{ recipes.to }}</span> of <span class="font-medium">{{ recipes.total }}</span> results
      </div>
      <div class="flex space-x-1">
        <Link v-for="(link, k) in recipes.links" :key="k" :href="link.url || '#'" v-html="link.label"
          class="px-3 py-1 border text-xs font-medium rounded transition-colors"
          :class="[
            link.active 
              ? 'bg-indigo-600 border-indigo-600 text-white' 
              : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700',
            !link.url ? 'opacity-50 cursor-not-allowed' : ''
          ]" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  recipes: Object,
  filters: Object,
  pageTitle: String
})

const search = ref(props.filters.search || '')

function debounce(fn, delay) {
  let timeoutId;
  return (...args) => {
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn(...args), delay);
  };
}

const performSearch = () => {
  router.get(route('admin.recipes.index'), { search: search.value }, {
    preserveState: true,
    replace: true
  })
}

watch(search, debounce(() => performSearch(), 500))

const deleteRecipe = (id) => {
  Swal.fire({
    title: 'Are you sure?',
    text: "This recipe will be deleted permanentely!",
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
