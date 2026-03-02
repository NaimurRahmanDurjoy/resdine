<template>
  <div class="m-4 bg-white dark:bg-gray-900 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-300">
        <thead class="bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 uppercase text-sm font-semibold tracking-wide">
          <tr>
            <th
              v-for="(header, index) in headers"
              :key="index"
              class="px-6 py-3 text-left whitespace-nowrap"
            >
              {{ header }}
            </th>
          </tr>
        </thead>

        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
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
  headers: { type: Array, default: () => [] },
  items: { type: Array, default: () => [] },
  empty: { type: String, default: 'No data available.' },
  pagination: { type: Object, default: null }
})
</script>
