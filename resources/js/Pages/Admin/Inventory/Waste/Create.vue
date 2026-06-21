<template>
  <div
    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <!-- Page Header -->
    <div
      class="bg-gradient-to-r from-red-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
      <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }}</h1>
        <Link :href="route('admin.inventory.waste.index')"
          class="text-gray-500 hover:text-gray-700 flex items-center space-x-1">
          <span class="material-symbols-outlined text-sm">arrow_back</span>
          <span>Back</span>
        </Link>
      </div>
    </div>

    <!-- Form Content -->
    <form @submit.prevent="submit" class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6">
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Inventory Item</label>
          <select v-model="form.inventory_item_id"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"
            required>
            <option v-for="item in items" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Branch</label>
          <select v-model="form.branch_id"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"
            required>
            <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
          </select>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Quantity</label>
          <input v-model="form.quantity" type="number" step="0.01"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"
            required>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Date</label>
          <input v-model="form.date" type="date"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"
            required>
        </div>
        <div class="space-y-2 md:col-span-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Reason</label>
          <input v-model="form.reason" type="text"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"
            required>
        </div>
        <div class="space-y-2 md:col-span-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Notes (Optional)</label>
          <textarea v-model="form.notes" rows="2"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"></textarea>
        </div>
      </div>

      <div class="mt-6 flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
        <Link :href="route('admin.inventory.waste.index')"
          class="px-6 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition mr-3">
          Cancel
        </Link>
        <button type="submit" :disabled="form.processing"
          class="px-8 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 disabled:opacity-50 transition shadow-md flex items-center gap-2">
          <span v-if="form.processing" class="animate-spin material-symbols-outlined text-sm">sync</span>
          <span>Save Log</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
defineOptions({ layout: AdminLayout })

defineProps({ items: Array, branches: Array, pageTitle: String });

const form = useForm({ inventory_item_id: '', branch_id: '', quantity: '', reason: '', date: '', notes: '' });
const submit = () => form.post(route('admin.inventory.waste.store'));
</script>
