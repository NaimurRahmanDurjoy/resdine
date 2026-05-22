<template>
    <DevAdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center bg-gray-900 p-6 rounded-xl border border-gray-800 shadow-xl">
                <div>
                    <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                        <span class="material-symbols-outlined text-amber-400">memory</span>
                        Cache Management
                    </h2>
                    <p class="text-gray-400 text-sm mt-1">Manage and clear application caches to reflect latest changes.</p>
                </div>
            </div>

            <!-- Global Action -->
            <div class="bg-gradient-to-r from-amber-500/10 to-rose-500/10 p-6 rounded-xl border border-amber-500/20 shadow-xl flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-rose-400">warning</span>
                        Clear All Caches
                    </h3>
                    <p class="text-sm text-gray-400 mt-1">
                        This will clear application, config, view, and route caches simultaneously.
                        Useful after deployment or major configuration changes.
                    </p>
                </div>
                <button @click="clearCache('all')" :disabled="processing === 'all'"
                    class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-lg font-medium transition-all shadow-lg shadow-rose-500/20 flex items-center gap-2 disabled:opacity-50">
                    <span class="material-symbols-outlined text-sm" :class="{ 'animate-spin': processing === 'all' }">
                        {{ processing === 'all' ? 'refresh' : 'delete_sweep' }}
                    </span>
                    {{ processing === 'all' ? 'Clearing...' : 'Clear All' }}
                </button>
            </div>

            <!-- Individual Caches Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Application Cache -->
                <div class="bg-gray-900 rounded-xl p-6 border border-gray-800 shadow-xl flex flex-col justify-between group hover:border-emerald-500/30 transition-colors">
                    <div class="mb-6">
                        <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center border border-emerald-500/20 mb-4 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-emerald-400">apps</span>
                        </div>
                        <h3 class="text-lg font-bold text-white">Application Cache</h3>
                        <p class="text-sm text-gray-400 mt-2">
                            Clears the main application cache including database queries, transient data, and standard cached items.
                        </p>
                    </div>
                    <button @click="clearCache('application')" :disabled="processing === 'application'"
                        class="w-full bg-gray-800 hover:bg-gray-700 text-emerald-400 px-4 py-2 rounded-lg border border-gray-700 hover:border-emerald-500/50 transition-all flex justify-center items-center gap-2 disabled:opacity-50">
                        <span class="material-symbols-outlined text-sm" :class="{ 'animate-spin': processing === 'application' }">
                            {{ processing === 'application' ? 'refresh' : 'cleaning_services' }}
                        </span>
                        Clear Application Cache
                    </button>
                </div>

                <!-- Config Cache -->
                <div class="bg-gray-900 rounded-xl p-6 border border-gray-800 shadow-xl flex flex-col justify-between group hover:border-blue-500/30 transition-colors">
                    <div class="mb-6">
                        <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center border border-blue-500/20 mb-4 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-blue-400">settings</span>
                        </div>
                        <h3 class="text-lg font-bold text-white">Configuration Cache</h3>
                        <p class="text-sm text-gray-400 mt-2">
                            Clears cached configuration files. Essential when you change environment variables (.env) or config files.
                        </p>
                    </div>
                    <button @click="clearCache('config')" :disabled="processing === 'config'"
                        class="w-full bg-gray-800 hover:bg-gray-700 text-blue-400 px-4 py-2 rounded-lg border border-gray-700 hover:border-blue-500/50 transition-all flex justify-center items-center gap-2 disabled:opacity-50">
                        <span class="material-symbols-outlined text-sm" :class="{ 'animate-spin': processing === 'config' }">
                            {{ processing === 'config' ? 'refresh' : 'cleaning_services' }}
                        </span>
                        Clear Config Cache
                    </button>
                </div>

                <!-- Route Cache -->
                <div class="bg-gray-900 rounded-xl p-6 border border-gray-800 shadow-xl flex flex-col justify-between group hover:border-purple-500/30 transition-colors">
                    <div class="mb-6">
                        <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center border border-purple-500/20 mb-4 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-purple-400">route</span>
                        </div>
                        <h3 class="text-lg font-bold text-white">Route Cache</h3>
                        <p class="text-sm text-gray-400 mt-2">
                            Clears cached routes. Required when you add, modify, or remove routes in your route files.
                        </p>
                    </div>
                    <button @click="clearCache('route')" :disabled="processing === 'route'"
                        class="w-full bg-gray-800 hover:bg-gray-700 text-purple-400 px-4 py-2 rounded-lg border border-gray-700 hover:border-purple-500/50 transition-all flex justify-center items-center gap-2 disabled:opacity-50">
                        <span class="material-symbols-outlined text-sm" :class="{ 'animate-spin': processing === 'route' }">
                            {{ processing === 'route' ? 'refresh' : 'cleaning_services' }}
                        </span>
                        Clear Route Cache
                    </button>
                </div>

                <!-- View Cache -->
                <div class="bg-gray-900 rounded-xl p-6 border border-gray-800 shadow-xl flex flex-col justify-between group hover:border-pink-500/30 transition-colors">
                    <div class="mb-6">
                        <div class="w-12 h-12 bg-pink-500/10 rounded-xl flex items-center justify-center border border-pink-500/20 mb-4 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-pink-400">web</span>
                        </div>
                        <h3 class="text-lg font-bold text-white">View Cache</h3>
                        <p class="text-sm text-gray-400 mt-2">
                            Clears compiled Blade view files. Useful if UI changes are not reflecting in the browser.
                        </p>
                    </div>
                    <button @click="clearCache('view')" :disabled="processing === 'view'"
                        class="w-full bg-gray-800 hover:bg-gray-700 text-pink-400 px-4 py-2 rounded-lg border border-gray-700 hover:border-pink-500/50 transition-all flex justify-center items-center gap-2 disabled:opacity-50">
                        <span class="material-symbols-outlined text-sm" :class="{ 'animate-spin': processing === 'view' }">
                            {{ processing === 'view' ? 'refresh' : 'cleaning_services' }}
                        </span>
                        Clear View Cache
                    </button>
                </div>
            </div>
        </div>
    </DevAdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import DevAdminLayout from '@/Layouts/DevAdminLayout.vue'
import Swal from 'sweetalert2'

const processing = ref(null)

const clearCache = (type) => {
    processing.value = type
    router.post(route('devAdmin.cache.clear'), { type }, {
        preserveScroll: true,
        onSuccess: (page) => {
            processing.value = null
            if (page.props.flash && page.props.flash.success) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    icon: 'success',
                    title: page.props.flash.success
                })
            }
        },
        onError: () => {
            processing.value = null
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                icon: 'error',
                title: 'An error occurred while clearing the cache.'
            })
        }
    })
}
</script>
