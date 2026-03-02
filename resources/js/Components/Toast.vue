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

<script setup>
import { ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

const { props } = usePage()

const flashMessages = props.value.flash || {}

const show = ref({
  success: !!flashMessages.success,
  error: !!flashMessages.error,
  warning: !!flashMessages.warning,
  info: !!flashMessages.info
})

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
  return type === 'success' ? 'bg-green-400 text-white'
       : type === 'error' ? 'bg-red-400 text-white'
       : type === 'warning' ? 'bg-yellow-400 text-white'
       : 'bg-blue-400 text-white'
}

onMounted(() => {
  Object.keys(show.value).forEach(type => {
    if(show.value[type]) setTimeout(() => show.value[type] = false, 3000)
  })
})
</script>
