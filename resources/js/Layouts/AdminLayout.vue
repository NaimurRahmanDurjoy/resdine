<template>

  <Head :title="pageTitle" />
  <div class="flex h-screen bg-gray-50 dark:bg-gray-900" :class="{ dark: darkMode }">
    <!-- Sidebar Backdrop (for mobile) -->
    <div v-show="sidebarOpen" @click="sidebarOpen = false"
      class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden transition-opacity duration-300"></div>

    <!-- Sidebar -->
    <SideBar :menus="menus"
      class="fixed inset-y-0 left-0 z-30 w-64 transform lg:translate-x-0 transition-transform duration-300 ease-in-out"
      :class="{ '-translate-x-full': !sidebarOpen }" />

    <div class="flex-1 flex flex-col lg:ml-64 transition-all min-w-0">
      <!-- Header -->
      <Header :user="user" :notifications="notifications" :pageTitle="pageTitle"
        @toggleSidebar="sidebarOpen = !sidebarOpen" />

      <!-- Page Content -->
      <main class="flex-1 p-6 overflow-x-hidden overflow-y-auto">
        <slot />
      </main>
    </div>

    <!-- Toasts and Modals -->
    <Toast />
    <GlobalModal />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Header from '@/Components/Admin/Header.vue'
import SideBar from '@/Components/Admin/SideBar.vue'
import Toast from '@/Components/Toast.vue'
import GlobalModal from '@/Components/GlobalModal.vue'

const props = defineProps({
  notifications: { type: Array, default: () => [] },
})

const page = usePage()
const menus = computed(() => page.props.menus || [])
const user = computed(() => page.props.auth?.user || { name: 'Guest' })
const pageTitle = computed(() => page.props.pageTitle || 'Dashboard')

const sidebarOpen = ref(false)
const darkMode = ref(false)
</script>
