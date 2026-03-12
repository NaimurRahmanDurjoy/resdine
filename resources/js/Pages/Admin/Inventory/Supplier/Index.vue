<template>
    <AdminLayout :pageTitle="pageTitle">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Page Header -->
            <div class="bg-white dark:bg-gray-800">
                <div
                    class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Suppliers</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage your product suppliers</p>
                        </div>
                        <Link :href="route('admin.suppliers.create')"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center gap-2 transition-all shadow-md shadow-indigo-100 dark:shadow-none">
                            <span class="material-symbols-outlined text-sm">add</span>
                            Add Supplier
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <ListTable :headers="['Name', 'Company', 'Phone', 'Email', 'Actions']" :items="suppliers">
                    <template #rows="{ items }">
                        <tr v-for="supplier in items" :key="supplier.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ supplier.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ supplier.company_name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ supplier.phone || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ supplier.email || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">
                                    <Link :href="route('admin.suppliers.edit', supplier.id)"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                        <span class="material-symbols-outlined">edit</span>
                                    </Link>
                                    <button @click="deleteSupplier(supplier.id)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="items.length === 0">
                            <td colspan="5"
                                class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                No suppliers found. Click "Add Supplier" to create your first supplier.
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
    suppliers: Array,
    pageTitle: String
})

const deleteSupplier = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "Deleting a supplier may affect related purchase orders!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.suppliers.destroy', id), {
                onSuccess: () => {
                    Swal.fire('Deleted!', 'Supplier has been deleted.', 'success')
                }
            })
        }
    })
}
</script>
