<template>
  <div v-for="(message, type) in flashMessages" :key="type" v-show="show[type]" 
       class="fixed top-6 right-6 z-50 max-w-sm w-full shadow-lg rounded-lg px-5 py-4 text-md flex items-center justify-between space-x-4"
       :class="bgColor(type)">
    <div class="flex items-center space-x-2">
      <span class="material-symbols-outlined text-white text-base">{{ icon(type) }}</span>
      <span>{{ message }}</span>
    </div>
    <button @click="hide(type)" class="text-white text-xl leading-none focus:outline-none">×</button>
  </div>
</template>

import { ref, watch, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const show = ref({
  success: false,
  error: false,
  warning: false,
  info: false
})

const messages = ref({
  success: '',
  error: '',
  warning: '',
  info: ''
})

watch(() => page.props.flash, (flash) => {
  if (flash) {
    Object.keys(flash).forEach(key => {
      if (flash[key]) {
        messages.value[key] = flash[key]
        show.value[key] = true
        setTimeout(() => show.value[key] = false, 4000)
      }
    })
  }
}, { immediate: true, deep: true })

function hide(type) {
  show.value[type] = false
}

function icon(type) {
  return type === 'success' ? 'check_circle'
       : type === 'error' ? 'error'
       : type === 'warning' ? 'warning'
       : 'info'
}

function bgColor(type) {
  return type === 'success' ? 'bg-green-500 text-white'
       : type === 'error' ? 'bg-red-500 text-white'
       : type === 'warning' ? 'bg-yellow-500 text-white'
       : 'bg-blue-500 text-white'
}
