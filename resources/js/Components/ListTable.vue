<template>
  <div class="m-4 bg-white dark:bg-gray-900 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-300">
        <thead class="bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 uppercase text-sm font-semibold tracking-wide">
          <tr>
            <th
              v-for="header in headers"
              :key="header.key"
              @click="handleSort(header)"
              class="px-6 py-3 text-left whitespace-nowrap"
              :class="header.sortable ? 'cursor-pointer select-none' : ''"
            >
              <div class="flex items-center gap-1">

                {{ header.label }}

                <span v-if="header.sortable" class="text-xs">

                  <span v-if="sort === header.key && direction === 'asc'"> ↑ </span>

                  <span v-else-if="sort === header.key && direction === 'desc'">  ↓ </span>

                  <span v-else>  ⇅  </span>

                </span>

              </div>
            </th>
          </tr>
        </thead>

        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
          <!-- Loading state -->
          <tr v-if="loading">
            <td :colspan="headers.length" class="text-center py-4">
              <div class="flex items-center justify-center space-x-2">
                <!-- Tiny spinner -->
                <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span class="text-gray-500 dark:text-gray-300 text-sm">Loading…</span>
              </div>
            </td>
          </tr>
          <!-- Data rows -->          
          <template v-if="items.length">
            <slot name="rows" :items="items"></slot>
          </template>
          <tr v-else>
            <td :colspan="headers.length" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
              {{ empty }}
            </td>
          </tr>
        </tbody>

        <tfoot v-if="$slots.footer" class="bg-gray-50 dark:bg-gray-800">
          <slot name="footer" />
        </tfoot>
      </table>
    </div>

    <div v-if="pagination" class="px-5 py-2 bg-indigo-50 dark:bg-indigo-900/30 border-t border-indigo-100 dark:border-indigo-800 rounded-b-lg">
      <slot name="pagination" />
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  headers: {
    type: Array,
    default: () => []
  },
  items: {
    type: Array,
    default: () => []
  },
  empty: {
    type: String,
    default: 'No data available.'
  },
  pagination: {
    type: Object,
    default: null
  },
    sort: {
    type: String,
    default: null
  },
  direction: {
    type: String,
    default: 'asc'
  },
  loading: {    
    type: Boolean,
    default: false
  }
})
const emit = defineEmits(['sort'])

function handleSort(header){

  if(!header.sortable) return

  emit('sort', header.key)

}
</script>
