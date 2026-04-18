<template>
  <div class="fixed top-6 right-6 z-[9999] flex flex-col space-y-4 w-full max-w-sm pointer-events-none">
    <TransitionGroup 
      name="toast-list" 
      tag="div" 
      class="flex flex-col space-y-3 w-full"
    >
      <div 
        v-for="toast in toasts" 
        :key="toast.id"
        class="pointer-events-auto relative group overflow-hidden"
        @mouseenter="pauseToast(toast)"
        @mouseleave="resumeToast(toast)"
      >
        <div 
          class="flex items-start p-4 rounded-xl border shadow-2xl backdrop-blur-md transition-all duration-300"
          :class="themeClasses(toast.type)"
        >
          <!-- Icon -->
          <div class="flex-shrink-0 mr-3 mt-0.5">
            <span class="material-symbols-outlined text-2xl" :class="iconColor(toast.type)">
              {{ iconName(toast.type) }}
            </span>
          </div>

          <!-- Content -->
          <div class="flex-1 mr-2">
            <h4 class="text-sm font-semibold mb-1 capitalize">{{ toast.type }}</h4>
            <p class="text-xs opacity-90 leading-relaxed font-medium">
              {{ toast.message }}
            </p>
          </div>

          <!-- Close Button -->
          <button 
            @click="removeToast(toast.id)"
            class="flex-shrink-0 text-current opacity-50 hover:opacity-100 transition-opacity focus:outline-none"
          >
            <span class="material-symbols-outlined text-lg">close</span>
          </button>

          <!-- Progress Bar -->
          <div class="absolute bottom-0 left-0 h-1 w-full bg-black/5 overflow-hidden rounded-b-xl">
            <div 
              class="h-full transition-all duration-[50ms] ease-linear"
              :class="progressClasses(toast.type)"
              :style="{ width: `${(toast.remaining / toast.duration) * 100}%` }"
            ></div>
          </div>
        </div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const toasts = ref([])
const INTERVAL = 50 // ms for progress bar update

function addToast(type, message) {
  if (!message) return
  
  const id = Date.now() + Math.random()
  const duration = type === 'error' ? 8000 : 5000 // Error toasts stay longer
  
  const toast = {
    id,
    type,
    message,
    duration,
    remaining: duration,
    paused: false,
    timer: null
  }
  
  toasts.value.push(toast)
  startTimer(toast)
}

function startTimer(toast) {
  toast.timer = setInterval(() => {
    if (!toast.paused) {
      toast.remaining -= INTERVAL
      if (toast.remaining <= 0) {
        removeToast(toast.id)
      }
    }
  }, INTERVAL)
}

function pauseToast(toast) {
  toast.paused = true
}

function resumeToast(toast) {
  toast.paused = false
}

function removeToast(id) {
  const index = toasts.value.findIndex(t => t.id === id)
  if (index !== -1) {
    clearInterval(toasts.value[index].timer)
    toasts.value.splice(index, 1)
  }
}

function themeClasses(type) {
  switch (type) {
    case 'success':
      return 'bg-emerald-50/90 border-emerald-200 text-emerald-900 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-100'
    case 'error':
      return 'bg-rose-50/90 border-rose-200 text-rose-900 dark:bg-rose-900/20 dark:border-rose-800 dark:text-rose-100'
    case 'warning':
      return 'bg-amber-50/90 border-amber-200 text-amber-900 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-100'
    default:
      return 'bg-blue-50/90 border-blue-200 text-blue-900 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-100'
  }
}

function iconColor(type) {
  switch (type) {
    case 'success': return 'text-emerald-500'
    case 'error': return 'text-rose-500'
    case 'warning': return 'text-amber-500'
    default: return 'text-blue-500'
  }
}

function progressClasses(type) {
  switch (type) {
    case 'success': return 'bg-emerald-500'
    case 'error': return 'bg-rose-500'
    case 'warning': return 'bg-amber-500'
    default: return 'bg-blue-500'
  }
}

function iconName(type) {
  switch (type) {
    case 'success': return 'check_circle'
    case 'error': return 'error'
    case 'warning': return 'warning'
    default: return 'info'
  }
}

// Watch for Inertia flash messages
watch(() => page.props.flash, (flash) => {
  if (flash) {
    Object.keys(flash).forEach(key => {
      if (flash[key]) {
        addToast(key, flash[key])
        // Optional: clear flash message from session if needed, 
        // but Inertia handle this usually by clearing flash object on next request.
      }
    })
  }
}, { immediate: true, deep: true })

onUnmounted(() => {
  toasts.value.forEach(t => clearInterval(t.timer))
})
</script>

<style scoped>
.toast-list-enter-active,
.toast-list-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.toast-list-enter-from {
  opacity: 0;
  transform: translateX(30px) scale(0.9);
}

.toast-list-leave-to {
  opacity: 0;
  transform: translateX(30px) scale(0.9);
}

/* Ensure smooth moving when others leave */
.toast-list-move {
  transition: transform 0.4s ease;
}
</style>
