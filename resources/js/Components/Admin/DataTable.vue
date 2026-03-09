<template>
    <div
        class="m-0 bg-white dark:bg-gray-900 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table
                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-300">
                <thead
                    class="bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 uppercase text-sm font-semibold tracking-wide">
                    <tr>
                        <th v-for="(header, index) in headers" :key="index" scope="col"
                            class="px-6 py-3 text-left whitespace-nowrap">
                            <div v-if="sortableHeaders[index]"
                                class="flex items-center space-x-1 hover:text-indigo-900 dark:hover:text-indigo-400 transition-colors cursor-pointer"
                                @click="handleSort(sortableHeaders[index])">
                                <span>{{ header }}</span>
                                <span
                                    class="material-symbols-outlined text-base transition-transform duration-200 ease-in-out"
                                    :class="[
                                        currentSort === sortableHeaders[index] ? 'text-indigo-700 dark:text-indigo-300' : 'text-gray-400 dark:text-gray-500',
                                        currentSort === sortableHeaders[index] && currentDirection === 'asc' ? 'rotate-180' : ''
                                    ]">
                                    arrow_drop_down
                                </span>
                            </div>
                            <span v-else>{{ header }}</span>
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
                    <slot v-if="hasItems" name="rows"></slot>
                    <tr v-else>
                        <td :colspan="headers.length" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <slot name="empty">No data available.</slot>
                        </td>
                    </tr>
                </tbody>

                <tfoot v-if="$slots.footer" class="bg-gray-50 dark:bg-gray-800">
                    <slot name="footer"></slot>
                </tfoot>
            </table>
        </div>

        <div v-if="pagination && pagination.links.length > 3"
            class="px-5 py-2 bg-indigo-50 dark:bg-indigo-900/30 border-t border-indigo-100 dark:border-indigo-800 rounded-b-lg">
            <Pagination :links="pagination.links" />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import Pagination from './Pagination.vue'

const props = defineProps({
    headers: { type: Array, required: true },
    items: { type: Array, default: () => [] },
    pagination: { type: Object, default: null },
    sortableHeaders: { type: Object, default: () => ({}) }, // index -> fieldName
    currentSort: String,
    currentDirection: String
})

const hasItems = computed(() => props.items.length > 0)

const handleSort = (field) => {
    const direction = props.currentSort === field && props.currentDirection === 'asc' ? 'desc' : 'asc'
    router.get(window.location.pathname, {
        ...Object.fromEntries(new URLSearchParams(window.location.search)),
        sort: field,
        direction: direction,
    }, {
        preserveState: true,
        replace: true,
    })
}
</script>
