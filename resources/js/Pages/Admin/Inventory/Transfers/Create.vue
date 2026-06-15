<template>
  <div
    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <!-- Page Header -->
    <div
      class="bg-gradient-to-r from-indigo-50 to-white dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
      <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ pageTitle }}</h1>
        <Link :href="route('admin.inventory.transfers.index')"
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
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">From Branch</label>
          <select v-model="form.from_branch_id"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required>
            <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
          </select>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">To Branch</label>
          <select v-model="form.to_branch_id"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required>
            <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
          </select>
        </div>
        <div class="space-y-2 md:col-span-2">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Transfer Date</label>
          <input v-model="form.transfer_date" type="date"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required>
        </div>

        <!-- Transfer Items Table (Dynamically inserted) -->
        <div class="md:col-span-2 mt-4 space-y-4">
          <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 border-b pb-2">Requested Items</h3>

          <div v-for="(item, index) in form.items" :key="index"
            class="flex items-center space-x-4 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
            <div class="flex-1">
              <select v-model="item.inventory_item_id"
                class="block w-full px-3 py-1.5 border border-gray-200 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                required>
                <option value="">Select Item</option>
                <option v-for="i in items" :key="i.id" :value="i.id">{{ i.name }}</option>
              </select>
            </div>
            <div class="w-32">
              <input v-model="item.quantity" type="number" step="0.01"
                class="block w-full px-3 py-1.5 border border-gray-200 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                placeholder="Qty" required>
            </div>
            <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">
              <span class="material-symbols-outlined text-lg">delete</span>
            </button>
          </div>

          <button type="button" @click="addItem"
            class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold inline-flex items-center">
            <span class="material-symbols-outlined text-sm mr-1">add</span> Add Item
          </button>
        </div>

      </div>

      <div class="mt-6 flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
        <Link :href="route('admin.inventory.transfers.index')"
          class="px-6 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition mr-3">
          Cancel
        </Link>
        <button type="submit" :disabled="form.processing || form.items.length === 0"
          class="px-8 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 disabled:opacity-50 transition shadow-md flex items-center gap-2">
          <span v-if="form.processing" class="animate-spin material-symbols-outlined text-sm">sync</span>
          <span>Initiate Transfer</span>
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

const form = useForm({ from_branch_id: '', to_branch_id: '', transfer_date: '', items: [] });

const addItem = () => {
  form.items.push({ inventory_item_id: '', quantity: '' });
};

const removeItem = (index) => {
  form.items.splice(index, 1);
};

const submit = () => form.post(route('admin.inventory.transfers.store'));
</script>
