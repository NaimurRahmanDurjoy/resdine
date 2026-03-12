<template>
    <AdminLayout :pageTitle="pageTitle">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Page Header -->
            <div class="bg-white dark:bg-gray-800">
                <div
                    class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Ingredients</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage your raw materials</p>
                        </div>
                        <Link :href="route('admin.ingredients.create')"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 flex items-center gap-2 transition-all shadow-md shadow-indigo-100 dark:shadow-none">
                            <span class="material-symbols-outlined text-sm">add</span>
                            Add Ingredient
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <ListTable :headers="['Name', 'Unit', 'Min Stock', 'Expiry Tracking', 'Status', 'Actions']" :items="ingredients">
                    <template #rows="{ items }">
                        <tr v-for="ingredient in items" :key="ingredient.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ ingredient.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs font-semibold">
                                    {{ ingredient.unit?.short_name || ingredient.unit?.name || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ ingredient.min_stock }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="ingredient.expiry_tracking ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800'">
                                    {{ ingredient.expiry_tracking ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="ingredient.status ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'">
                                    {{ ingredient.status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">
                                    <Link :href="route('admin.ingredients.edit', ingredient.id)"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                        <span class="material-symbols-outlined">edit</span>
                                    </Link>
                                    <button @click="deleteIngredient(ingredient.id)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="items.length === 0">
                            <td colspan="6"
                                class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                No ingredients found. Click "Add Ingredient" to create your first item.
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
    ingredients: Array,
    pageTitle: String
})

const deleteIngredient = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "Deleting an ingredient may affect stock and recipe records!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.ingredients.destroy', id), {
                onSuccess: () => {
                    Swal.fire('Deleted!', 'Ingredient has been deleted.', 'success')
                }
            })
        }
    })
}
</script>
