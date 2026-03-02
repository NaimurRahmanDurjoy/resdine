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

    <!-- Toasts and Modals -->
    <Toast />
    <ValidationToast />
    <Modal />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Header from '@/Components/Admin/Header.vue'
import SideBar from '@/Components/Admin/SideBar.vue'
import Toast from '@/Components/Toast.vue'
import ValidationToast from '@/Components/ValidationToast.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  user: { type: Object, required: true },
  menus: { type: Array, default: () => [] },
  notifications: { type: Array, default: () => [] },
})

const sidebarOpen = ref(false)
const darkMode = ref(false)
</script>
