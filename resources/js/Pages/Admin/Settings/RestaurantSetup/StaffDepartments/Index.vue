<template>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Page Header -->
            <div class="bg-white dark:bg-gray-800">
                <div
                    class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Staff Departments</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage employee groups and department
                                assignments</p>
                        </div>
                    </div>
                </div>
                <div class="m-4 flex justify-between items-center">
                    <div class="flex space-x-2">
                    <div class="relative max-w-sm w-full">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-gray-400">search</span>
                        </span>
                        <input v-model="search" type="text" placeholder="Search items..."
                        class="block w-full pl-10 pr-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" />
                    </div>
                    <button @click="performSearch"
                        class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 transition">
                        Search
                    </button>
                    </div>

                    <!-- Add new item -->
                    <Link :href="route('admin.settings.restaurant-setup.staff-departments.create')"
                    class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-green-700 flex items-center space-x-2 transition">
                    <span class="material-symbols-outlined text-sm">add</span>
                    <span>Add Departments</span>
                    </Link>
                </div>
            </div>

            <!-- Content -->
             <ListTable :headers="headers" :items="departments.data" :pagination="departments" :sort="filters.sort" :direction="filters.direction" :loading="loading" @sort="sortColumn">
                    <template #rows="{ items }">
                        <tr v-for="dept in items" :key="dept.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td
                                class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ dept.name }}
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ dept.branch?.name || 'N/A' }}
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex gap-3">
                                    <Link
                                        :href="route('admin.settings.restaurant-setup.staff-departments.edit', dept.id)"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                        <span class="material-symbols-outlined">edit</span>
                                    </Link>
                                    <button @click="deleteDept(dept.id)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template #pagination>
                        <div class="flex items-center justify-between w-full">
                        <div class="text-sm text-gray-700 dark:text-gray-400">
                            Showing <span class="font-medium">{{ departments.from }}</span> to <span class="font-medium">{{ departments.to
                            }}</span> of <span class="font-medium">{{ departments.total }}</span> entries
                        </div>
                        <div class="flex space-x-1">
                            <Link v-for="(link, k) in departments.links" :key="k" :href="link.url || '#'" v-html="link.label"
                            class="relative inline-flex items-center px-3 py-1 border text-xs font-medium transition-colors rounded shadow-sm"
                            :class="[
                                link.active
                                ? 'z-10 bg-indigo-600 border-indigo-600 text-white'
                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700',
                                !link.url ? 'cursor-not-allowed opacity-50' : ''
                            ]" />
                        </div>
                        </div>
                    </template>
                </ListTable>
        </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'
import Swal from 'sweetalert2'
defineOptions({ layout: AdminLayout })

const props = defineProps({
    departments: Object,
    filters: Object,
    pageTitle: String
})
const headers = [
    { label: 'Name', key: 'name', sortable: true },
    { label: 'Branch', key: 'branch.name', sortable: true },
    { label: 'Actions', key: 'actions', sortable: false }
]

const search = ref(props.filters.search)
const loading = ref(false)

function debounce(fn, delay) {
  let timeoutId;
  return (...args) => {
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn(...args), delay);
  };
}

const performSearch = () => {
  loading.value = true
  router.get(route('admin.settings.restaurant-setup.staff-departments.index'), { search: search.value, sort: props.filters.sort, direction: props.filters.direction }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  })
}

watch(search, debounce(() => performSearch(), 500))

function sortColumn(column){
  let direction = 'asc'
  if(props.filters.sort === column){
    direction = props.filters.direction === 'asc' ? 'desc' : 'asc'
  }
  loading.value = true
  router.get(route('admin.settings.restaurant-setup.staff-departments.index'), { search: search.value, sort: column, direction: direction }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  })
}
const deleteDept = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.settings.restaurant-setup.staff-departments.destroy', id), {
                onSuccess: () => {
                    Swal.fire('Deleted!', 'Department has been deleted.', 'success')
                }
            })
        }
    })
}
</script>
