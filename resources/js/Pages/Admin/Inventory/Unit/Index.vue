<template>
    <AdminLayout :pageTitle="pageTitle">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Page Header -->
            <div class="bg-white dark:bg-gray-800">
                <div
                    class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Units</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage your inventory units</p>
                        </div>
                        <Link :href="route('admin.unit.create')"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center gap-2 transition-all shadow-md shadow-indigo-100 dark:shadow-none">
                            <span class="material-symbols-outlined text-sm">add</span>
                            Add Unit
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <ListTable :headers="['Name', 'Short Name', 'Status', 'Actions']" :items="units">
                    <template #rows="{ items }">
                        <tr v-for="unit in items" :key="unit.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ unit.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ unit.short_name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="unit.status ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'">
                                    {{ unit.status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">
                                    <Link :href="route('admin.unit.edit', unit.id)"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                        <span class="material-symbols-outlined">edit</span>
                                    </Link>
                                    <button @click="deleteUnit(unit.id)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="items.length === 0">
                            <td colspan="4"
                                class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                No units found. Click "Add Unit" to create your first unit.
                            </td>
                        </tr>
                    </template>
                </ListTable>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ListTable from '@/Components/ListTable.vue'
import Swal from 'sweetalert2'

const props = defineProps({
    units: Array,
    pageTitle: String
})

const deleteUnit = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "Deleting a unit cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.unit.destroy', id), {
                onSuccess: () => {
                    Swal.fire('Deleted!', 'Unit has been deleted.', 'success')
                }
            })
        }
    })
}
</script>
