<template>

    <Head :title="pageTitle" />
    <div class="flex h-screen bg-[color:var(--bg-app)] text-[color:var(--text-primary)] transition-colors duration-200"
        :class="{ dark: darkMode }">
        <!-- Sidebar Backdrop (for mobile) -->
        <div v-show="sidebarOpen" @click="sidebarOpen = false"
            class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden transition-opacity duration-300"></div>

        <!-- Sidebar -->
        <Sidebar :menus="menus"
            class="fixed inset-y-0 left-0 z-30 w-64 transform lg:translate-x-0 transition-transform duration-300 ease-in-out"
            :class="{ '-translate-x-full': !sidebarOpen }" />

        <div class="flex-1 flex flex-col lg:ml-64 transition-all min-w-0">
            <!-- Header -->
            <Header :user="user" :notifications="notifications" :pageTitle="pageTitle"
                @toggleSidebar="sidebarOpen = !sidebarOpen" @toggleTheme="toggleTheme" />

            <!-- Page Content -->
            <main class="flex-1 p-6 overflow-x-hidden overflow-y-auto">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage, Head } from '@inertiajs/vue3'
import Header from '@/Components/DevAdmin/Header.vue'
import Sidebar from '@/Components/DevAdmin/Sidebar.vue'

const props = defineProps({
    notifications: { type: Array, default: () => [] },
})

const page = usePage()
const menus = computed(() => page.props.menus || [])
const user = computed(() => page.props.auth?.user || { name: 'Guest' })
const pageTitle = computed(() => page.props.pageTitle || 'DevAdmin Dashboard')

const sidebarOpen = ref(false)
const darkMode = ref(false)

const applyTheme = () => {
    document.documentElement.classList.toggle('dark', darkMode.value)
    document.documentElement.setAttribute('data-theme', darkMode.value ? 'dark' : 'light')
    localStorage.setItem('resdine-theme', darkMode.value ? 'dark' : 'light')
}

const toggleTheme = () => {
    darkMode.value = !darkMode.value
    applyTheme()
}

onMounted(() => {
    const storedTheme = localStorage.getItem('resdine-theme')
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    darkMode.value = storedTheme ? storedTheme === 'dark' : prefersDark
    applyTheme()
})
</script>
