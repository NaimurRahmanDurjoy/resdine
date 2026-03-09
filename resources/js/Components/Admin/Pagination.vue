<template>
    <div class="flex flex-wrap -mb-1">
        <template v-for="(link, key) in links" :key="key">
            <div v-if="link.url === null" class="mr-1 mb-1 px-3 py-1 text-sm leading-4 text-gray-400 border rounded"
                v-html="link.label" />
            <Link v-else
                class="mr-1 mb-1 px-3 py-1 text-sm leading-4 border rounded hover:bg-indigo-600 hover:text-white focus:border-indigo-500 focus:text-indigo-500 transition"
                :class="{ 'bg-indigo-600 text-white': link.active }" :href="link.url" v-html="link.label" />
        </template>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: Array,
})
</script>
