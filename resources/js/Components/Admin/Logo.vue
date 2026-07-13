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
          ? 'w-10 h-10 bg-white/20 text-white dark:bg-orange-500/10 dark:text-orange-500' 
          : 'w-8 h-8 bg-indigo-600 text-white shadow-sm dark:bg-orange-600 dark:text-white'
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
          ? 'text-xl text-white dark:text-slate-100' 
          : 'text-lg text-gray-800 dark:text-slate-100'
      ]"
    >
      Resdine <span :class="[isSidebar ? 'text-indigo-100 dark:text-orange-500' : 'text-indigo-600 dark:text-orange-500']">POS</span>
    </div>
  </Link>
</template>

<script setup>

defineProps({
  isSidebar: {
    type: Boolean,
    default: false
  }
})
</script>
