<template>
  <div class="flex h-screen bg-gray-50 dark:bg-gray-900" :class="{ dark: darkMode }">
    <!-- Sidebar Backdrop (for mobile) -->
    <div v-show="sidebarOpen" 
         @click="sidebarOpen = false"
         class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden transition-opacity duration-300"></div>

    <!-- Sidebar -->
    <SideBar :menus="menus" 
                  class="fixed inset-y-0 left-0 z-30 w-64 transform lg:translate-x-0 transition-transform duration-300 ease-in-out"
                  :class="{ '-translate-x-full': !sidebarOpen }" />

    <div class="flex-1 flex flex-col lg:ml-64 transition-all">
      <!-- Header -->
      <Header :user="user" 
                 :notifications="notifications" 
                 @toggleSidebar="sidebarOpen = !sidebarOpen" />

      <!-- Page Content -->
      <main class="flex-1 p-6">
        <slot />
      </main>
    </div>

    <!-- Toasts and Modals (Removed temporarily) -->
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Header from '@/Components/Admin/Header.vue'
import SideBar from '@/Components/Admin/SideBar.vue'

const props = defineProps({
  notifications: { type: Array, default: () => [] },
})

const page = usePage().props
const menus = page?.menus ?? []
const user = page?.auth?.user ?? { name: 'Guest' }


const sidebarOpen = ref(false)
const darkMode = ref(false)
</script>
