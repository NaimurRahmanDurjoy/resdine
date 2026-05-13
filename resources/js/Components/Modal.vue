<template>
  <Transition name="fade">
    <div v-if="show" class="fixed inset-0 z-[60] overflow-hidden flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-opacity">
      <div 
        class="bg-white rounded-xl shadow-2xl w-full mx-auto overflow-hidden transform transition-all animate-fade-in-up"
        :class="maxWidthClass"
      >
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">
            {{ title }}
          </h3>
          <button 
            @click="$emit('close')" 
            class="rounded-full p-2 hover:bg-gray-200 text-gray-400 transition-colors"
          >
            <span class="material-symbols-outlined text-[20px]">close</span>
          </button>
        </div>
        
        <!-- Body -->
        <div class="overflow-y-auto max-h-[80vh] custom-scrollbar">
          <slot />
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  title: {
    type: String,
    default: ''
  },
  maxWidth: {
    type: String,
    default: '2xl'
  }
});

defineEmits(['close']);

const maxWidthClass = computed(() => {
  return {
    'sm': 'max-w-sm',
    'md': 'max-w-md',
    'lg': 'max-w-lg',
    'xl': 'max-w-xl',
    '2xl': 'max-w-2xl',
    '3xl': 'max-w-3xl',
    '4xl': 'max-w-4xl',
  }[props.maxWidth] || 'max-w-2xl';
});
</script>

<style scoped>
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
  animation: fadeInUp 0.4s ease-out forwards;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
</style>
