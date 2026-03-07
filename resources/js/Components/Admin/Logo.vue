<template>
  <Link 
    href="/admin/dashboard" 
    class="flex items-center gap-2 group transition-all"
    :class="[isSidebar ? 'px-4' : '']"
  >
    <div 
      class="flex items-center justify-center rounded-lg transition-colors overflow-hidden flex-shrink-0"
      :class="[
        isSidebar 
          ? 'w-10 h-10 bg-white/20 text-white' 
          : 'w-8 h-8 bg-indigo-600 text-white shadow-sm'
      ]"
    >
      <span class="material-symbols-outlined shrink-0" :class="[isSidebar ? 'text-2xl' : 'text-xl']">
        restaurant
      </span>
    </div>
    <div 
      class="font-bold tracking-tight transition-colors whitespace-nowrap"
      :class="[
        isSidebar 
          ? 'text-xl text-white' 
          : 'text-lg text-gray-800'
      ]"
    >
      Resdine <span :class="[isSidebar ? 'text-indigo-100' : 'text-indigo-600']">POS</span>
    </div>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  isSidebar: {
    type: Boolean,
    default: false
  }
})
</script>
