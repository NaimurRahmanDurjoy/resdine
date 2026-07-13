<template>
    <div
        class="theme-panel m-0 overflow-hidden border">
        <div class="overflow-x-auto">
            <table
                class="min-w-full divide-y divide-[color:var(--border-subtle)] text-sm text-[color:var(--text-secondary)]">
                <thead
                    class="bg-[color:var(--bg-elevated)] border-b border-[color:var(--border-subtle)] text-[color:var(--text-primary)] text-xs font-semibold uppercase tracking-wider">
                    <tr>
                        <th v-for="(header, index) in headers" :key="index" scope="col"
                            class="px-6 py-4 text-left whitespace-nowrap">
                            <div v-if="sortableHeaders[index]"
                                class="flex items-center space-x-1 hover:text-[color:var(--accent-primary)] transition-colors cursor-pointer"
                                @click="handleSort(sortableHeaders[index])">
                                <span>{{ header }}</span>
                                <span
                                    class="material-symbols-outlined text-sm transition-transform duration-200 ease-in-out"
                                    :class="[
                                        currentSort === sortableHeaders[index] ? 'text-[color:var(--accent-primary)]' : 'text-[color:var(--text-muted)]',
                                        currentSort === sortableHeaders[index] && currentDirection === 'asc' ? 'rotate-180' : ''
                                    ]">
                                    arrow_drop_down
                                </span>
                            </div>
                            <span v-else>{{ header }}</span>
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[color:var(--border-subtle)] bg-[color:var(--bg-surface)]">
                    <slot v-if="hasItems" name="rows"></slot>
                    <tr v-else>
                        <td :colspan="headers.length" class="px-6 py-12 text-center text-[color:var(--text-muted)] italic text-xs">
                            <slot name="empty">No records found.</slot>
                        </td>
                    </tr>
                </tbody>

                <tfoot v-if="$slots.footer" class="bg-[color:var(--bg-elevated)]">
                    <slot name="footer"></slot>
                </tfoot>
            </table>
        </div>

        <div v-if="pagination && pagination.links.length > 3"
            class="px-5 py-3 border-t border-[color:var(--border-subtle)] bg-[color:var(--bg-surface)]">
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
