<template>
  <div v-if="show && product"
    class="fixed inset-0 z-[60] overflow-hidden flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-opacity">
    <div
      class="bg-white rounded-3xl shadow-2xl max-w-sm w-full mx-auto overflow-hidden transform transition-all animate-fade-in-up"
      :class="{ 'dark:bg-gray-800': theme === 'indigo' }">
      <!-- Header -->
      <div class="px-6 py-5 border-b flex justify-between items-center" :class="[
        theme === 'amber' ? 'border-slate-100 bg-slate-50' : 'border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50'
      ]">
        <h3 class="text-lg font-black tracking-tight" :class="[
          theme === 'amber' ? 'text-slate-900' : 'text-gray-800 dark:text-gray-100 uppercase'
        ]">
          Select Variant
        </h3>
        <button @click="$emit('close')" class="rounded-full p-2 transition-colors" :class="[
          theme === 'amber' ? 'hover:bg-slate-200 text-slate-400' : 'hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-400'
        ]">
          <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
      </div>

      <!-- Body -->
      <div class="p-6">
        <div class="space-y-3">
          <button v-for="variant in product.variants" :key="variant.id"
            @click="variant.is_available !== false && $emit('selected', variant)"
            class="w-full relative flex items-center justify-between p-4 border-2 transition-all text-left flex-wrap gap-2"
            :class="[
              variant.is_available === false
                ? 'opacity-60 cursor-not-allowed grayscale border-dashed border-gray-300 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-800'
                : (theme === 'amber'
                  ? 'border-slate-100 hover:border-amber-500 hover:bg-amber-50 cursor-pointer group rounded-2xl'
                  : 'border-transparent bg-indigo-50 dark:bg-indigo-900/20 hover:border-indigo-400/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 rounded-xl cursor-pointer group')
            ]">
            <div class="flex flex-col">
              <span class="font-bold inline-flex items-center gap-2" :class="[
                theme === 'amber' ? 'text-slate-800' : 'text-gray-800 dark:text-gray-200'
              ]">
                {{ variant.name }}
                <span v-if="variant.is_available === false"
                  class="bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 px-2 py-0.5 rounded text-[10px] uppercase font-black tracking-widest border border-red-200 dark:border-red-800">Sold
                  Out</span>
              </span>
            </div>
            <span class="font-black shrink-0" :class="[
              variant.is_available === false
                ? 'text-gray-400 dark:text-gray-500'
                : (theme === 'amber' ? 'text-amber-600' : 'text-indigo-600 dark:text-indigo-400')
            ]">
              {{ currency() }}{{ variant.price || product.price }}
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    required: true
  },
  product: {
    type: Object,
    default: null
  },
  theme: {
    type: String,
    default: 'indigo' // 'indigo' for Admin/POS, 'amber' for Web Menu
  }
})

defineEmits(['close', 'selected'])
</script>

<style scoped>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.4s ease-out forwards;
}
</style>
